/**
 * Interaction for the faq categories
 */
jsBackend.addresses =
    {
        // init, something like a constructor
        init: function()
        {
            var categoryTitles = $('.category-title');

            categoryTitles.on('blur', function(){

               var newTitle = $(this).val();

                categoryTitles.each(function(index){
                  if($(categoryTitles[index]).val() === '') {
                      $(categoryTitles[index]).val(newTitle);
                  }
               });
            });

            var itemTitles = $('.item-title');

            itemTitles.on('blur', function(){

               var newTitle = $(this).val();

                itemTitles.each(function(index){
                  if($(itemTitles[index]).val() === '') {
                      $(itemTitles[index]).val(newTitle);
                  }
               });
            });

            var categoryHiddens = $('.category-hidden');

            categoryHiddens.on('change', function(){
                if(this.checked) {
                    categoryHiddens.each(function(){
                        this.checked = true;
                    })
                }
                if(!this.checked) {
                    categoryHiddens.each(function(){
                        this.checked = false;
                    })
                }
            });

            var itemSpotlights = $('.item-spotlight');

            itemSpotlights.on('change', function(){
                if(this.checked) {
                    itemSpotlights.each(function(){
                        this.checked = true;
                    })
                }
                if(!this.checked) {
                    itemSpotlights.each(function(){
                        this.checked = false;
                    })
                }
            });

            var itemHiddens = $('.item-hidden');
            itemHiddens.on('change', function(){
               if(this.checked) {
                   itemHiddens.each(function(){
                       this.checked = true;
                   })
               }
               if(!this.checked) {
                   itemHiddens.each(function(){
                       this.checked = false;
                   })
               }
            });
        }
    };

$(jsBackend.addresses.init);
