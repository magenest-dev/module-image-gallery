require([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    //Validate Image FileSize

    $.validator.addMethod(
        'validate-filesize', function (v, elm) {
            var maxSize = 2 * 1024000;
            if (navigator.appName == "Microsoft Internet Explorer") {
                if (elm.value) {
                    var oas = new ActiveXObject("Scripting.FileSystemObject");
                    var e = oas.getFile(elm.value);
                    var size = e.size;
                }
            } else {
                if (elm.files[0] != undefined) {
                    size = elm.files[0].size;
                }
            }
            if (size != undefined && size > maxSize) {
                return false;
            }
            return true;
        }, $.mage.__('The file size should not exceed 2MB'));

});