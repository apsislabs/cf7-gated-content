(function($) {
  var SUCCESS_STATUS = "mail_sent";
  var AJAX_ACTION = "getDownloadButton";

  document.addEventListener(
    "wpcf7submit",
    function(event) {
      var detail = event.detail;
      var apiResponse = detail.apiResponse;

      // bail early if we didn't succeed
      if (apiResponse.status !== SUCCESS_STATUS) {
        return;
      }

      var $target = $(apiResponse.into);
      var downloadId = "wpcf7-download-" + detail.unitTag;

      // Set a cookie
      document.cookie = wpcf7gc.cookieKey + detail.contactFormId + "=1";

      $.ajax({
        type: "post",
        dataType: "json",
        url: wpcf7gc.ajaxurl,
        data: {
          action: AJAX_ACTION,
          contactFormId: detail.contactFormId
        },
        error: function(res) {
          console.error("Error Fetching Gated Content: ", res);
          alert(
            "There was an error fetching your download. Please refresh the page."
          );
        },
        success: function(res) {
          if (res.data != null) {
            if ($target && $("#" + downloadId).length < 1) {
              var $downloadContainer = $("<div></div>", { id: downloadId });
              $downloadContainer.append(res.data).appendTo($target);
            }
          }
        }
      });
    },
    false
  );
})(jQuery);
