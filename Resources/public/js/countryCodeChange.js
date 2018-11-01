module.exports = {
    // The address form fields are initialized here, these form fields are common for some entity forms and are therefore put in this function
    initialize: function (entity) {
        $(document).ready(function () {
            // When the country code changes the form will be submitted so we get a validated form for that country
            // Only the address part here is important to change in the current view
            var onCountryCodeChange = function () {
                var $form = $(this).closest('form');
                var $address = $form.find('#' + entity + '_address');

                var data = {};
                $addressElements = $address.find('.form-control');
                $addressElements.each(function (index, element) {
                    data[$(element).attr('name')] = $(element).val();
                });

                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method'),
                    data: data,
                    success: function (html) {
                        $address.replaceWith(
                            $(html).find('#' + entity + '_address')
                        );
                        $form.find('#' + entity + '_address_countryCode').change(onCountryCodeChange);
                    }
                });
            };

            $countryCode = $('#' + entity + '_address_countryCode');
            $countryCode.change(onCountryCodeChange);
        });
    }
};