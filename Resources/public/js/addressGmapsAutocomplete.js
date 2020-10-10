var countryCodeChange = require('./countryCodeChange');
module.exports = {
    initialize: function () {
        countryCodeChange.initialize();
        $(document).ready(function () {
            $('.address-autocomplete-input').once('initiate-autocomplete').each(function () {
                initializeAutoComplete($(this));
            });
        });

        function initializeAutoComplete($autocompleteField, oldAutocomplete) {
            var apiKey = $autocompleteField.attr('data-api-key');
            var language = $autocompleteField.attr('data-language');
            var allowedCountries = $autocompleteField.attr('data-allowed-countries').split('|');

            // Load Google Maps Javascript API library and ensure it's only loaded once
            if (typeof(apiLoaded) === 'undefined' || !apiLoaded) {
                $.getScript('https://maps.googleapis.com/maps/api/js?key='+apiKey+'&language='+language+'&libraries=places', function () {
                    apiLoaded = true;
                    createAutoCompleteInstance();
                });
            } else {
                createAutoCompleteInstance();
            }
            function createAutoCompleteInstance() {

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
                    };

                autocomplete = new google.maps.places.Autocomplete(
                    /** @type {!HTMLInputElement} */($autocompleteField[0]),
                    {types: ['geocode']});

                // Set restrict to the list of available countries.
                if (allowedCountries.length) {
                    autocomplete.setComponentRestrictions(
                        {'country': allowedCountries});
                }
                autocomplete.addListener('place_changed', function() {
                    fillInAddress(autocomplete.getPlace());
                });

                // Get wrapper
                var wrapper = $autocompleteField.closest('[id$=_address]');
                wrapper.closest('form').on('countryCodeChanged', '.address-embeddable', function () {
                    $(this).find('.address-autocomplete-input').once('initiate-autocomplete').each(function () {
                        initializeAutoComplete($(this), true);
                    });
                });

                if (oldAutocomplete) {
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({address: $autocompleteField.val()}, function (result, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            fillInAddress(result[0]);
                        }
                    });
                }

                function fillInAddress(place)
                {
                    if (typeof place.name === 'undefined') {
                        place.name = getPlaceName(place);
                    }
                    // This event was triggered because the address
                    // changed. Check to see if we need to trigger a
                    // country code change.
                    $countryCodeField = wrapper.find('[id$=_countryCode]');
                    if (place && place.address_components) {
                        // Get each component of the address from the place details
                        // and fill the corresponding field on the form.
                        for (var i = 0; i < place.address_components.length; i++) {
                            var addressType = place.address_components[i].types[0];
                            if (addressType !== 'country') {
                                continue;
                            }
                            if (componentForm[addressType]) {
                                var value = place.address_components[i][componentForm[addressType]];
                                if (value && value.length && $countryCodeField.val() !== value) {
                                    $countryCodeField.val(value).trigger('change');
                                    // No use filling in the reset of the
                                    // fields as they might change because the
                                    // country code change triggers an ajax
                                    // call to update the form.
                                    return;
                                }
                            }
                        }

                        // Get each component of the address from the place details
                        // and fill the corresponding field on the form.
                        for (var i = 0; i < place.address_components.length; i++) {
                            var addressType = place.address_components[i].types[0];
                            if (componentForm[addressType]) {
                                var value = place.address_components[i][componentForm[addressType]];
                                // The place.name == "StreetName StreetNumber".
                                value = addressType == 'route' ? place.name : value;
                                if (value && value.length) {
                                    wrapper.find(address[addressType]).val(value);
                                }
                            }
                        }
                    }
                }

                function getPlaceName(place) {
                    var street = '', streetNumber = '';
                    for (var i = 0; i < place.address_components.length; i++) {
                        switch (place.address_components[i].types[0]) {
                            case 'route':
                                street = place.address_components[i].long_name;
                                break;


                            case 'street_number':
                                streetNumber = place.address_components[i].long_name;
                                break;
                        }
                    }
                    return [street, streetNumber].join(' ');
                }
            }
        }
    }
};
