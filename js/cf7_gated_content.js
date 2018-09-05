(function($) {
    document.addEventListener(
        "wpcf7submit",
        function(event) {
            document.cookie =
                wpcf7gc.cookieKey + event.detail.contactFormId + "=1";

            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: wpcf7gc.ajaxurl,
                data: {
                    action: "getDownloadButton",
                    contactFormId: event.detail.contactFormId
                },
                success: function(res) {
                    jQuery(event.target).append(res.data);
                }
            });
        },
        false
    );
})(jQuery);
