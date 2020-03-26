export function initialize (entity, fieldName = 'address') {
    $(document).ready(function () {
        // When the country code changes the form will be submitted so we get a validated form for that country
        // Only the address part here is important to change in the current view
        let onCountryCodeChange = function () {
            var $form = $(this).closest('form');
            var $address = $form.find('#' + entity + '_' + fieldName);

            var data = {};
            var $addressElements = $form.find('.form-control');
            $addressElements.each(function (index, element) {
                data[$(element).attr('name')] = $(element).val();
            });

            $.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: data,
                success: function (html) {
                    $address.replaceWith(
                        $(html).find('#' + entity + '_' + fieldName)
                    );
                    $form.find('#' + entity + '_' + fieldName + '_countryCode').change(onCountryCodeChange);
                }
            });
        };

        var $countryCode = $('#' + entity + '_' + fieldName + '_countryCode');
        $countryCode.change(onCountryCodeChange);
    });
}
