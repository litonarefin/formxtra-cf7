(function ($) {

    // Add style to all Formxtra CF7 tags
    jQuery('.thickbox.button').each(function () {
        var str = jQuery(this).attr('href');

        if (str.indexOf("formxtra_cf7") >= 0) {
            jQuery(this).css({ "backgroundColor": "#2271b1", "color": "white", "border-color": "#2271b1" });
        }
        if (str.indexOf("uarepeater") >= 0) {
            jQuery(this).css({ "backgroundColor": "#2271b1", "color": "white", "border-color": "#2271b1" });
        }
        if (str.indexOf("conditional") >= 0) {
            jQuery(this).css({ "backgroundColor": "#2271b1", "color": "white", "border-color": "#2271b1" });
        }
    });
})(jQuery);
