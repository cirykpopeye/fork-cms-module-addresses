

$(document).on('ready', function() {
    var map;
    const startPoint = new google.maps.LatLng(51.072453, 2.598119);
    var currentPosition = startPoint;
    var directionsService = new google.maps.DirectionsService();
    const addresses = jsFrontend.data.get('Addresses.addresses');
    const searchOptions = $('#searchResults');
    const searchBox = $('#searchBox');
    var directionsDisplay;
    var locationAvailable = false;
    var infoWindow = new google.maps.InfoWindow;

    var AddressMap = {
        init: function() {
            this.renderMap();
            this.askForLocation();
            this.renderMarkers();
            this.renderSearchOptions();
            this.searchAddress();
        },
        askForLocation: function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(AddressMap.showPosition);
            } else {
                console.log("Geolocation is not supported");
            }
        },
        showPosition: function(position) {
            currentPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            map.setCenter(currentPosition);
            locationAvailable = true;
            //-- Add marker to map with this position, to show where the user currently is.
            new google.maps.Marker({
                map: map,
                title: 'Here I am',
                icon: 'http://i.stack.imgur.com/orZ4x.png',
                position: {
                    lng: position.coords.longitude,
                    lat: position.coords.latitude
                }
            });

            //-- Update bounds
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < addresses.length; i++) {
                bounds.extend({ lat: addresses[i].lat, lng: addresses[i].lng });
            }
            bounds.extend({
                lat: position.coords.latitude, lng: position.coords.longitude
            });
            map.fitBounds(bounds);
        },
        renderMap: function() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: startPoint,
                zoom: 13,
                styles: [
                    {
                        "featureType": "administrative",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "color": "#e41b13"
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "labels.text",
                        "stylers": [
                            {
                                "color": "#e41b13"
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "labels.text.fill",
                        "stylers": [
                            {
                                "color": "#e41b13"
                            }
                        ]
                    },
                    {
                        "featureType": "administrative",
                        "elementType": "labels.text.stroke",
                        "stylers": [
                            {
                                "color": "#ffffff"
                            },
                            {
                                "saturation": "-0"
                            },
                            {
                                "lightness": "0"
                            },
                            {
                                "gamma": "1"
                            },
                            {
                                "weight": "0"
                            }
                        ]
                    },
                    {
                        "featureType": "landscape",
                        "elementType": "all",
                        "stylers": [
                            {
                                "color": "#f2f2f2"
                            }
                        ]
                    },
                    {
                        "featureType": "poi",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "road",
                        "elementType": "all",
                        "stylers": [
                            {
                                "saturation": -100
                            },
                            {
                                "lightness": 45
                            }
                        ]
                    },
                    {
                        "featureType": "road.highway",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "simplified"
                            }
                        ]
                    },
                    {
                        "featureType": "road.arterial",
                        "elementType": "labels.icon",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "transit",
                        "elementType": "all",
                        "stylers": [
                            {
                                "visibility": "off"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "all",
                        "stylers": [
                            {
                                "color": "#f6f9fc"
                            },
                            {
                                "visibility": "on"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry.fill",
                        "stylers": [
                            {
                                "saturation": "0"
                            },
                            {
                                "lightness": "0"
                            },
                            {
                                "color": "#7eafea"
                            }
                        ]
                    },
                    {
                        "featureType": "water",
                        "elementType": "geometry.stroke",
                        "stylers": [
                            {
                                "color": "#4a90e2"
                            }
                        ]
                    }
                ]
            });

            //-- Check if any address, if so set the center to that
            if (addresses.length) {
                var address = addresses[0];
                map.setCenter(new google.maps.LatLng(address.lat, address.lng));

                //-- Update bounds
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0; i < addresses.length; i++) {
                    bounds.extend({ lat: addresses[i].lat, lng: addresses[i].lng });
                }
                map.fitBounds(bounds);
            }
        },
        renderMarkers: function() {
            var markers = [];
            for (var i = 0; i < addresses.length; i++) {
                if (addresses[i].lat && addresses[i].lng) {
                    var marker = new google.maps.Marker({
                        map: map,
                        title: addresses[i].title,
                        position: {
                            lat: addresses[i].lat,
                            lng: addresses[i].lng
                        }
                    });

                    addresses[i].marker = marker;
                    markers.push(marker);
                }
            }

            var options = {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'};
            new MarkerClusterer(map, markers, options);
        },
        renderSearchOptions: function() {
            searchOptions.empty();
            for (var i = 0; i < addresses.length; i++) {
                $.ajax({
                    data: {
                        fork: { module: 'Addresses', action: 'AddressInformation'},
                        addressId: addresses[i].id,
                        counter: i
                    },
                    success: function(data) {
                        if(data.data) {
                            var listId = 'place-' + data.data.addressId;

                            //-- Retreived data, now start adding to search list on left
                            // searchOptions.append($('<li></li>', { html: data.data.template_small, 'id': listId, class: 'place' }));
                            // AddressMap.onSearchOptionClick(listId, data.data.counter);

                            addresses[data.data.counter].template = data.data.template;
                            addresses[data.data.counter].listId = listId;


                            AddressMap.addInfoWindow(addresses[data.data.counter]);
                        }
                    }
                });
            }
        },
        onSearchOptionClick: function(listId, counter) {
            $('#' + listId).on('click', function(e) {
                e.preventDefault();

                //-- Set fitting bounds to clicked address
                var bounds = new google.maps.LatLngBounds();
                bounds.extend({ lat: addresses[counter].lat, lng: addresses[counter].lng });
                map.fitBounds(bounds);

                //-- Set decent zoom
                google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
                    if (this.getZoom() > 17) {
                        this.setZoom(17);
                    }
                });
            });
        },
        addInfoWindow: function(address) {
            google.maps.event.addListener(address.marker, 'click', function() {
                var content = address.template;
                infoWindow = new google.maps.InfoWindow({
                    content: content
                });
                infoWindow.open(map, address.marker);
            });
            google.maps.event.addListener(map, 'click', function() {
                if (infoWindow) {
                    infoWindow.close(map, address.marker);
                }
            });
        },
        onSearchType: function() {
            searchBox.on('keyup', function() {
                AddressMap.searchAddress($(this).val());
            });
        },
        searchAddress: function(string) {
            //-- Find address searched for, then find closest address in database
            var input = document.getElementById('searchBox');
            var searchBox = new google.maps.places.SearchBox(input);

            map.addListener('bouds_changed', function() {
               searchBox.setBounds(map.getBounds());
            });

            searchBox.addListener('places_changed', function() {
               var places = searchBox.getPlaces();
               if (places.length) {
                   var firstPlace = places[0];
                   var position = {
                       lat: firstPlace.geometry.location.lat(),
                       lng: firstPlace.geometry.location.lng()
                   };

                   //-- Find closest address now
                   AddressMap.findClosestAddressTo(position);
               }
            });

            // string = string.toLowerCase();
            // for (var i = 0; i < addresses.length; i++) {
            //     var found = false;
            //     //-- First search for the name
            //     if (addresses[i].title.toLowerCase().indexOf(string) !== -1) {
            //         found = true;
            //     }
            //     //-- Check placename
            //     if (addresses[i].city.toLowerCase().indexOf(string) !== -1) {
            //         found = true;
            //     }
            //
            //     //-- Check street
            //     if (addresses[i].street.toLowerCase().indexOf(string) !== -1) {
            //         found = true;
            //     }
            //
            //     //-- Hide / Show if found / not found
            //     if (!found && string.length) {
            //         $('#' + addresses[i].listId).hide();
            //     } else {
            //         $('#' + addresses[i].listId).show();
            //     }
            // }
        },
        findClosestAddressTo: function(startPoint) {
            for (var i = 0; i < addresses.length; i++) {
                var address = addresses[i];
                var distance = AddressMap.calculateDistance({ lat: address.lat, lng: address.lng}, startPoint);
                address.distance = distance;
            }

            addresses.sort(function(a, b) {
                return parseFloat(a.distance) - parseFloat(b.distance);
            });

            //-- Fetch first address as that's the closest one by sorting
            var closestAddress = addresses[0];

            var closestAddressLocation = new google.maps.LatLng(closestAddress.lat, closestAddress.lng);

            map.setCenter(closestAddressLocation);

            //-- If location is set, set bounds so it's visible
            if (locationAvailable) {
                var bounds = new google.maps.LatLngBounds();
                bounds.extend({ lat: closestAddress.lat, lng: closestAddress.lng});
                bounds.extend({ lat: currentPosition.lat(), lng: currentPosition.lng() });
                map.fitBounds(bounds);

                //-- Set route
                var request = {
                    origin: currentPosition,
                    destination: closestAddressLocation,
                    travelMode: google.maps.TravelMode.DRIVING
                };

                directionsDisplay = new google.maps.DirectionsRenderer();

                directionsService.route(request, function(response, status) {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setMap(map);
                        directionsDisplay.setDirections(response);
                    }
                });
            } else {
                google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                   if (map.getZoom()) {
                       this.setZoom(12);
                   }
                });
            }
        },
        sortResults: function(results) {
            //-- Fetch distance compared to searched position
            for (var i = 0; i < results.length; i++) {
                var distance = AddressMap.calculateDistance(results[i].geometry.location, t.startPoint);
                results[i].distance = distance;
            }
            //-- Result sorting by distance
            results.sort(function(a, b) {
                return parseFloat(a.distance) - parseFloat(b.distance);
            });

            return results;
        },
        calculateDistance: function(p1, p2) {
            //Returns Distance between two latlng objects using haversine formula
            if (!p1 || !p2) {
                //-- Positions are equal
                return 0;
            }
            //-- Do some weird calculation
            var R = 6371000; // Radius of the Earth in m
            var dLat = (p2.lat - p1.lat) * Math.PI / 180;
            var dLon = (p2.lng - p1.lng) * Math.PI / 180;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(p1.lat * Math.PI / 180) * Math.cos(p2.lat * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c;
            //-- Return distance
            return d;
        }
    };


    AddressMap.init();
});