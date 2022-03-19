module.exports = {
    // The address form fields are initialized here, these form fields are common for some entity forms and are therefore put in this function
    initialize: function () {
        $(document).ready(function () {
            $('.address-embeddable').once('initiate-country-code-change').each(function () {
                var id = $(this).attr('id');

                // When the country code changes the form will be submitted so we get a validated form for that country
                // Only the address part here is important to change in the current view
                var onCountryCodeChange = function () {
                    var $form = $(this).closest('form');
                    var $countryCode = $(this);
                    var $address = $countryCode.closest('.address-embeddable');

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
                                $(html).find('#' + id)
                            );
                            var $countryCode = $form.find('#' + id + '_countryCode');
                            $countryCode.change(onCountryCodeChange);
                            $countryCode.closest('.address-embeddable').trigger('countryCodeChanged');
                        }
                    });
                };

                $countryCode = $('#' + id + '_countryCode');
                $countryCode.change(onCountryCodeChange);
            });
        });
    }
};
