module.exports = {
    // The address form fields are initialized here, these form fields are common for some entity forms and are therefore put in this function
    initialize: function () {
        $(document).ready(function () {
            $('.address-autocomplete-input', context).once('initiate-autocomplete').each(function() {
                // The element to work with.
                var autocompleteField = $(this);
                var apiKey = $(this).attr('data-api-key');

                // Load Google Maps Javascript API library and ensure it's only loaded once
                if(!drupalSettings.addressAutocomplete.apiLoaded) {
                    $.getScript('https://maps.googleapis.com/maps/api/js?key='+apiKey+'&libraries=places', function(){
                        apiLoaded = true;
                        // initiateAutocomplete();
                        alert('test');
                    });
                } else {
                    // initiateAutocomplete();
                }
            }
        });
    }
};
