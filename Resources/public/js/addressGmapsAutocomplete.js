module.exports = {
    // The address form fields are initialized here, these form fields are common for some entity forms and are therefore put in this function
    initialize: function () {
        $(document).ready(function () {
            $('.address-autocomplete-input').once('initiate-autocomplete').each(function () {
                // The element to work with.
                var autocompleteField = $(this);
                var apiKey = $(this).attr('data-api-key');
                var allowedCountries = $(this).attr('data-allowed-countries').split('|');

                // Load Google Maps Javascript API library and ensure it's only loaded once
                if (typeof(apiLoaded) === 'undefined' || !apiLoaded) {
                    $.getScript('https://maps.googleapis.com/maps/api/js?key='+apiKey+'&libraries=places', function () {
                        apiLoaded = true;
                        initiateAutocomplete();
                    });
                } else {
                    initiateAutocomplete();
                }
                function initiateAutocomplete() {

                    var autocomplete,
                        componentForm = {
                            street_number: 'short_name',
                            route: 'long_name',
                            neighborhood: 'short_name',
                            locality: 'long_name',
                            administrative_area_level_1: 'short_name',
                            country: 'short_name',
                            postal_code: 'short_name'
                        },
                        address = {
                            street_number: '.street-number',
                            route: '[id$=_addressLine1]',
                            route_2: '[id$=_addressLine2]',
                            sublocality_level_1: '[id$=_dependentLocality]',
                            locality: '[id$=_locality]',
                            administrative_area_level_1: '[id$=_administrativeArea]',
                            country: '[id$=_countryCode]',
                            postal_code: '[id$=_postalCode]',
                            // given_name: '.given-name',
                            // family_name: '.family-name',
                            // organization: '.organization'
                        };

                    autocomplete = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */(autocompleteField[0]),
                        {types: ['geocode']});

                    // Set restrict to the list of available countries.
                    if (allowedCountries.length) {
                        autocomplete.setComponentRestrictions(
                            {'country': allowedCountries});
                    }
                    autocomplete.addListener('place_changed', fillInAddress);

                    // TODO: Figure out wtf this is for?
                    if (location.protocol == 'https:' && navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var geolocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };
                            var circle = new google.maps.Circle({
                                center: geolocation,
                                radius: position.coords.accuracy
                            });
                            autocomplete.setBounds(circle.getBounds());
                        });
                    }

                    // Get wrapper
                    var wrapper = autocompleteField.closest('[id$=_address]');
                    // wrapper.find('input.address-autocomplete-input').removeClass('address-autocomplete-component--hidden');
                    // // Hide all other address fields.
                    // for (var component in address) {
                    //     var addressField = wrapper.find(address[component]);
                    //     if (addressField.length) {
                    //         wrapper.find('label[for="'+addressField.attr('id')+'"]').hide();
                    //         addressField.hide();
                    //     }
                    // }

                    function fillInAddress()
                    {
                        // Fill initial address fields
                        var place = autocomplete.getPlace();
                        var country = wrapper.find('select.country').val();
                        if (place && place.address_components) {
                            // Get each component of the address from the place details
                            // and fill the corresponding field on the form.
                            for (var i = 0; i < place.address_components.length; i++) {
                                var addressType = place.address_components[i].types[0];
                                if (componentForm[addressType]) {
                                    var value = place.address_components[i][componentForm[addressType]];
                                    // The place.name == "StreetName StreetNumber".
                                    value = addressType == 'route' ? place.name : value;
                                    if (value.length) {
                                        if (addressType == 'country') {
                                            country = value;
                                        }
                                        else {
                                            wrapper.find(address[addressType]).val(value);
                                        }
                                    }
                                }
                            }
                        }
                        // Initiates the ajax event providing appropriate address
                        // components for a country chosen with Google Places API.
                        // TODO: Check if we can do this too?
                        // wrapper.find('select.country').val(country).change();
                    }

                }
            });
        });
    }
};
