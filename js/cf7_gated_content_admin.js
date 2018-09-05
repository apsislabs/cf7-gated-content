(function($) {
  $(document).ready(function() {
    // Set all variables to be used in scope
    var frame;
    var $uploadButton = $("#upload_image_button");
    var $removeButton = $("#remove_image_button");
    var $urlInput = $("#image_attachment_url");
    var $idInput = $("#image_attachment_id");
    var $attachmentBox = $("#attachment_box");

    function renderAttachmentBox(attachment) {
      $attachmentBox.find(".attachment_filename").text(attachment.filename);
      $attachmentBox.find(".attachment_url").text(attachment.url);
      $attachmentBox.show();
    }

    function handleFrameOpen(frame) {
      var selection = frame.state().get("selection");
      var selected = $idInput.val();

      if (selected) {
        selection.add(wp.media.attachment(selected));
      }
    }

    function handleFrameSelect(frame) {
      var attachment = frame
        .state()
        .get("selection")
        .first()
        .toJSON();

      $urlInput.val(attachment.url);
      $idInput.val(attachment.id);
      renderAttachmentBox(attachment);
    }

    $uploadButton.on("click", function(event) {
      event.preventDefault();

      // If the media frame already exists, reopen it.
      if (frame) {
        frame.open();
        return;
      }

      // Create the media frame.
      frame = wp.media.frames.frame = wp.media({
        title: wpcf7gc.mediaTitle,
        multiple: false
      });

      frame.on("open", function() {
        handleFrameOpen(frame);
      });

      frame.on("select", function() {
        handleFrameSelect(frame);
      });

      frame.open();
    });

    $removeButton.on("click", function(event) {
      event.preventDefault();
      $idInput.val("");
      $urlInput.val("");
      $attachmentBox.hide();
    });
  });
})(jQuery);
