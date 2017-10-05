var s, t, AddressMap = {
    settings: {
        map: null,
        search: document.getElementById('searchBox'),
    },
    temp: {
        request: null,
        markers: [],
        markerLimit: 0,
        startPoint: new google.maps.LatLng(51.072453, 2.598119),
        bounds: new google.maps.LatLngBounds(),
        infoWindow: new google.maps.InfoWindow
    },
    init: function () {
        s = this.settings;
        t = this.temp;

        //-- Init map
        this.initMap();
        //-- Init nearby search
        this.initNearbySearch(t.startPoint);
        //-- Init search box
        this.initSearchBox();
    },
    initMap: function() {
        s.map = new google.maps.Map(document.getElementById('map'), {
            center: t.startPoint,
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
    },
    initNearbySearch: function(startPoint) {
      //-- Set startPoint
      t.startPoint = startPoint;

      //-- Create search request
      t.request = {
        location: startPoint,
        radius: '1000000',
        // types: ['store'],
        name: "Real tobacco"
      };

      service = new google.maps.places.PlacesService(s.map);
      service.nearbySearch(t.request, AddressMap.requestCallBack);
    },
    requestCallBack: function(results, status) {
        console.log(jsFrontend.data.get('Addresses.addresses'));
        console.log(results);

        var availableAddresses = jsFrontend.data.get('Addresses.addresses');

        //-- Check if place id matches any
        for (var k = 0; k < results.length; k++) {
            var found = false;
            for (var j = 0; j < availableAddresses.length; j++) {
                if (results[k].place_id === availableAddresses[j].placeId) {
                    found = true;
                }
            }
            if (!found) {
                results.splice(k, 1);
            }
        }


      if(status == google.maps.places.PlacesServiceStatus.OK) {
        if(!results.length) {
          t.markerLimit = 0
          AddressMap.initNearbySearch(t.startPoint);
          return;
        }
        var sortedResults = AddressMap.sortResults(results);
        for (var i = 0; i < sortedResults.length; i++) {
          AddressMap.createMarker(sortedResults[i]);
          // if(t.markerLimit > 0) {
          //   break;
          // }
        }
        //-- Create cluster
        var options = {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'};

        //-- Create cluster
        var markerCluster = new MarkerClusterer(s.map, t.markers, options);
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
      var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
      var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
      var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
      var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      var d = R * c;
      //-- Return distance
      return d;
    },
    createMarker: function(place) {
        var image = {
          url: place.icon,
          size: new google.maps.Size(71, 71),
          origin: new google.maps.Point(0, 0),
          anchor: new google.maps.Point(17,34),
          scaledSize: new google.maps.Size(25, 25)
        };

        var marker = new google.maps.Marker({
          map: s.map,
          icon: image,
          title: place.name,
          position: place.geometry.location
        });

        t.markers.push(marker);

        if(t.markerLimit == 1) {
          //-- If the place has a geometry, present it on a map
          if (place.geometry.viewport) {
            s.map.fitBounds(place.geometry.viewport);
          } else {
            s.map.setCenter(place.geometry.location);
            s.map.setZoom(17);
          }
        } else {
          //-- Set bounds
          t.bounds.extend(place.geometry.location);
          //-- Fit bounds
          s.map.fitBounds(t.bounds);
        }

        //-- Fetch information for place
        place = AddressMap.getInformationForPlace(place, marker);
    },
    getInformationForPlace: function(place, marker) {
      //-- Empty search results first
      $('#searchResults').empty();
      service.getDetails({
        placeId: place.place_id
      }, function(place, status) {
        if(place) {
          $.ajax({
            data: {
              fork: { module: 'Addresses', action: 'AddressInformation'},
              street: place.address_components[1].long_name,
              number: place.address_components[0].long_name
            },
            success: function(data, status) {
              if(data.data) {
                place.template = data.data.template;

                //-- Retreived data, now start adding to search list on left
                $('#searchResults').append($('<li></li>', { html: data.data.template_small, 'id': 'place-' + place.place_id, class: 'place' }));

                AddressMap.listenOnSearchClick(place);

                AddressMap.addInfoWindow(marker, place);
              }
            }
          })
        }
      });
    },
    listenOnSearchClick: function(place) {
      $('#place-' + place.place_id).on('click', function(e) {
        e.preventDefault();
        t.bounds = new google.maps.LatLngBounds();
        t.bounds.extend(place.geometry.location);
        s.map.fitBounds(t.bounds);

      google.maps.event.addListenerOnce(s.map, 'bounds_changed', function(event) {
          if (this.getZoom() > 17) {
              this.setZoom(17);
          }
      });
      });
    },
    addInfoWindow: function(marker, place) {
      google.maps.event.addListener(marker, 'click', function() {
        var content = place.template;
        t.infoWindow = new google.maps.InfoWindow({
          content: content
        });
        t.infoWindow.open(s.map, marker);
      });
      google.maps.event.addListener(s.map, 'click', function() {
        if (t.infoWindow) {
          t.infoWindow.close(s.map, marker);
        }
      });
    },
    initSearchBox: function() {
        //-- Create search box
        var autoComplete = AddressMap.createSearchBox();
        //-- Listen on search box
        AddressMap.listenOnSearchBox(autoComplete);
    },
    createSearchBox: function() {
      // s.map.controls[google.maps.ControlPosition.TOP_LEFT].push(s.search);
      var autoComplete = new google.maps.places.Autocomplete(s.search);
      autoComplete.bindTo('bounds', s.map);

      return autoComplete;
    },
    clearMarkers: function() {
      for (var i = 0; i < t.markers.length; i++) {
        t.markers[i].setMap(null);
      }
      t.markers = [];
    },
    listenOnSearchBox: function(autoComplete) {
      autoComplete.addListener('place_changed', function() {
        //-- Clear all markers previously added
        AddressMap.clearMarkers();

        if(!autoComplete.getPlace().geometry) {
          t.markerLimit = 0
          AddressMap.initNearbySearch(t.startPoint);
          return;
        }
        //-- Set marker limit
        t.markerLimit = 1;
        //-- Do the search with given place_changed
        AddressMap.initNearbySearch(autoComplete.getPlace().geometry.location);
      });
    }
};

AddressMap.init();
