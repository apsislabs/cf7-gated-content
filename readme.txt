=== Contact Form 7 Gated Content ===
Contributors: odisant
Tags: form, contact form, contact form 7, cf7, gated content, gated file
Requires at least: 4.5
Tested up to: 5.4.1
Requires PHP: 5.6
Stable tag: 1.4.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Gate files behind your Contact Form 7 forms.

== Description ==

Contact Form 7 Gated Content is a lead-capture extension for Contact Form 7. This plugin adds options to your CF7 forms to gate access to a file so users don't see a download button until after the form has been successfully submitted. Repeat visitors will be presented with the download button only, so repeat form submissions are not required.

**Image Credit:** Uehara, Konen, Artist. Secchū Mimeguri. Japan, None. [Between 1900 and 1920] Photograph. https://www.loc.gov/item/2008660511/.

== Supported Versions ==

We support and test against CF7 versions 4.9, 5.0, and 5.1. Earlier or later versions may not behave as expected.

== Issues? ==

If you're experiencing an issue with the plugin, please file an issue on our [GitHub](https://github.com/apsislabs/cf7-gated-content/issues).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the contents of `/contact-form-7-gated-content/` to the `/wp-content/plugins/` directory
2. Activate the *Contact Form 7 Gated Content* Plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What kinds of files can I gate? =

Anything that can be uploaded to the WordPress media library.

= Can the download button be customized? =

Yes! You can customize the output of the download box with CSS, or you can add this snippet to your theme\'s `functions.php` file:

<code>
function my_content_button($url, $button_text, $button_classes, $content)
{
  return "$button_text";
}

add_filter('wpcf7_gated_content_button', 'my_content_button', 10, 3);
</code>

= Can I disable the default CSS? =

Yes! The default CSS can be disabled on a per-form basis.

= Does this secure my files? =

**No!** Gating content does not secure it from outside access. While it does restrict visibility of a download link to those who have completed your form, it does nothing to make the file private. Anyone with the attachment link or a properly formatted cookie will be able to access your file.

== Screenshots ==
1. Options Panel
2. Before Submission
3. After Submission
4. Repeat Visit

== Changelog ==

* 1.0.8 Add art assets
* 1.0.1 Fix deployment issues
* 1.0.0 Initial release
* 1.2.0 Fix issue with multiple download buttons
* 1.3.0 Fix issue with breaking other shortcodes
* 1.4.0 Added option for setting download target; fix bug where we always show download button; add option to disable download button
* 1.4.1 Fix bug with default option for enabling forms set to false

== Upgrade Notice ==
