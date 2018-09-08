=== Contact Form 7 Gated Content ===
Contributors: odisant
Tags: form, contact form, contact form 7, cf7, gated content, gated file
Requires at least: 4.5
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gate files behind your Contact Form 7 forms.

== Description ==
Contact Form 7 Gated Content is a lead-capture extension for Contact Form 7. This plugin adds options to your CF7 forms to gate access to a file so users don\'t see a download button until after the form has been successfully submitted. Repeat visitors will be presented with the download button only, so repeat form submissions are not required.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the contents of `/contact-form-7-gated-content/` to the `/wp-content/plugins/` directory
2. Activate the *Contact Form 7 Gated Content* Plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= What kinds of files can I gate? =

Anything that can be uploaded to the WordPress media library.

= Can the download button be customized? =

Yes! You can customize the output of the download box with CSS, or you can add this snippet to your theme\'s `functions.php` file:

```
function my_content_button($url, $button_text, $button_classes, $content)
{
  return "$button_text";
}

add_filter('wpcf7_gated_content_button', 'my_content_button', 10, 3);
```

= Can I disable the default CSS? =

Yes! The default CSS can be disabled on a per-form basis.

= Does this secure my files? =

**No!** Gating content does not secure it from outside access. While it does restrict visibility of a download link to those who have completed your form, it does nothing to make the file private. Anyone with the attachment link or a properly formatted cookie will be able to access your file.

== Screenshots ==
1. Options Panel
2. Before Submission
3. After Submission

== Changelog ==

* 1.0.0 Initial release
== Upgrade Notice ==
