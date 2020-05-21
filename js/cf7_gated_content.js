(function ($) {
  var SUCCESS_STATUS = "mail_sent";
  var AJAX_ACTION = "getDownloadButton";
  var SUPPORTED_VERSIONS = [4, 5];

  function warnVersion(version) {
    console.warn(
      "[CF7 GATED CONTENT]: This version of CF7 is not supported: " + version
    );
  }

  function parseVersions(detail) {
    var version = detail.pluginVersion;
    var versionParts = version.split(".").map(v => parseInt(v, 10));

    // Warn if we are in a non-supported version of CF7
    if (!SUPPORTED_VERSIONS.includes(versionParts[0])) {
      warnVersion(version);
    }

    // Return version object
    return {
      string: version,
      major: versionParts[0],
      minor: versionParts[1],
      patch: versionParts[2],
    };
  }

  function parseStatus(detail) {
    var version = parseVersions(detail);

    switch (version.major) {
      case 5:
        return detail.apiResponse.status;

      case 4:
        return detail.status;

      default:
        return null;
    }
  }

  function parseTarget(detail) {
    var version = parseVersions(detail);

    switch (version.major) {
      case 5:
        return $(detail.apiResponse.into);

      case 4:
        return $("#" + detail.id);

      default:
        return null;
    }
  }

  document.addEventListener(
    "wpcf7submit",
    function (event) {
      var detail = event.detail;
      var status = parseStatus(detail);
      var $target = parseTarget(detail);

      // bail early if we didn't succeed
      if (status !== SUCCESS_STATUS || $target === null) {
        return false;
      }

      var downloadId = "wpcf7-download-" + detail.unitTag;

      // Set a cookie
      document.cookie = wpcf7gc.cookieKey + detail.contactFormId + "=1";

      $.ajax({
        type: "post",
        dataType: "json",
        url: wpcf7gc.ajaxurl,
        data: {
          action: AJAX_ACTION,
          contactFormId: detail.contactFormId,
        },
        error: function (res) {
          console.error("Error Fetching Gated Content: ", res);

          alert(
            "There was an error fetching your download. Please refresh the page."
          );
        },
        success: function (res) {
          if (res.data != null) {
            if ($target && $("#" + downloadId).length < 1) {
              var $downloadContainer = $("<div></div>", { id: downloadId });
              $downloadContainer.append(res.data).appendTo($target);
            }
          }
        },
      });
    },
    false
  );
})(jQuery);
