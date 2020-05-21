<?php

/**
 * Plugin Name: Contact Form 7 Gated Content
 * Description: An add-on for Contact Form 7 that allows you to gate content behind form submission
 * Version: 1.4.4
 * Author: Apsis Labs
 * Author URI: http://apsis.io
 * License: GPLv3
 *
 * @package cf7_gated_content
 */

namespace Apsis;

const METABOX_NONCE_SECRET = 'cf7_gated_content_metaboxes';
const METABOX_NONCE_KEY = 'cf7_gated_content_metaboxes_nonce';
const GATED_CONTENT_COOKIE_KEY = 'cf7_gated_content_';

class ContactFormGatedContent
{
  /**
   * Initialize the plugin, register actions and filters
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function init()
  {
    add_action('wpcf7_editor_panels', array(static::class, 'registerPanels'));
    add_action('wpcf7_after_save', array(static::class, 'saveGatedContentForm'));
    add_action('wpcf7_mail_sent', array(static::class, 'setCookieServerSide'));
    add_action('wpcf7_enqueue_scripts', array(static::class, 'clientSideScript'));
    add_action('wpcf7_enqueue_styles', array(static::class, 'clientSideStyles'));

    add_action('admin_enqueue_scripts', array(static::class, 'adminScript'));
    add_action('wp_ajax_getDownloadButton', array(static::class, 'getDownloadButton'));
    add_action('wp_ajax_nopriv_getDownloadButton', array(static::class, 'getDownloadButton'));

    add_filter('do_shortcode_tag', array(static::class, 'outputShortcode'), 10, 4);
    add_filter('wpcf7_form_response_output', array(static::class, 'addFormResponse'), 10, 4);
  }

  /**
   * Register the gated contact form panel to display in CF7
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function registerPanels($panels)
  {
    $panels['gated_content'] = array(
      'title' => __('Gated Content', 'apsis_wp'),
      'callback' => [static::class, 'drawGatedContentPanel']
    );

    return $panels;
  }

  /**
   * Render the panel content
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function drawGatedContentPanel($post)
  {
    wp_nonce_field(METABOX_NONCE_SECRET, METABOX_NONCE_KEY);

    if ($post->id()) {
      // Fetch settings
      $image_attachment_url = get_post_meta($post->id(), 'image_attachment_url', true);
      $image_attachment_id = get_post_meta($post->id(), 'image_attachment_id', true);
      $download_content = get_post_meta($post->id(), 'download_content', true);
      $download_button_text = get_post_meta($post->id(), 'download_button_text', true);
      $download_button_classes = get_post_meta($post->id(), 'download_button_classes', true);
      $always_require_form = get_post_meta($post->id(), 'always_require_form', true);
      $show_download_for_admin = get_post_meta($post->id(), 'show_download_for_admin', true);
      $open_in_new_tab = get_post_meta($post->id(), 'open_in_new_tab', true);
      $enable_gated_content = get_post_meta($post->id(), 'enable_gated_content', true);
      $include_default_css = get_post_meta($post->id(), 'include_default_css', true);

      // Fetch attachment value
      $attachment_meta = static::getAttachmentMeta($image_attachment_id);

      $user_settings = array_filter(compact(
        "image_attachment_url",
        "image_attachment_id",
        "download_content",
        "download_button_text",
        "download_button_classes",
        "always_require_form",
        "show_download_for_admin",
        "open_in_new_tab",
        "enable_gated_content",
        "attachment_meta",
        "include_default_css"
      ), function ($v) {
        return !is_null($v);
      });

      // Merge with default values
      $values = array_merge(static::getDefaults(), $user_settings);
    } else {
      $values = static::getDefaults();
    }

    // Render metabox
    echo static::renderTemplate(dirname(__FILE__) . '/templates/metabox.php', $values);
  }

  /**
   * Save gated content settings when saving the CF7 form
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function saveGatedContentForm($contact_form)
  {
    global $post;
    $contact_form_id = $contact_form->id();
    $meta = array();

    // Validate user permissions
    if (!current_user_can('edit_post', $contact_form_id)) {
      return $contact_form_id;
    }

    // Validate admin nonce
    if (!isset($_POST[METABOX_NONCE_KEY]) || !wp_verify_nonce($_POST[METABOX_NONCE_KEY], METABOX_NONCE_SECRET)) {
      return $contact_form_id;
    }

    // Don't save during autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $contact_form_id;
    }

    // Don't save on revisions
    if ($post && 'revision' === $post->post_type) {
      return $contact_form_id;
    }

    $meta['image_attachment_id'] = sanitize_text_field($_POST['image_attachment_id']);
    $meta['image_attachment_url'] = sanitize_text_field($_POST['image_attachment_url']);
    $meta['download_button_text'] = sanitize_text_field($_POST['download_button_text']);
    $meta['download_button_classes'] = sanitize_text_field($_POST['download_button_classes']);
    $meta['always_require_form'] = isset($_POST['always_require_form']) ? !!$_POST['always_require_form'] : false;
    $meta['show_download_for_admin'] = isset($_POST['show_download_for_admin']) ? !!$_POST['show_download_for_admin'] : false;
    $meta['open_in_new_tab'] = isset($_POST['open_in_new_tab']) ? !!$_POST['open_in_new_tab'] : false;
    $meta['enable_gated_content'] = isset($_POST['enable_gated_content']) ? !!$_POST['enable_gated_content'] : false;
    $meta['include_default_css'] = isset($_POST['include_default_css']) ? !!$_POST['include_default_css'] : false;
    $meta['download_content'] = wp_kses_post($_POST['download_content']);

    // wp_die(print_r($meta, true));
    foreach ($meta as $key => $value) {
      static::storeMetaValue($contact_form_id, $key, $value);
    }
  }

  /**
   * Set cookie when processing a contact form on the server side
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function setCookieServerSide($contact_form)
  {
    setcookie(GATED_CONTENT_COOKIE_KEY . $contact_form->id(), 1);
  }

  /**
   * Register client side styles
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function clientSideStyles()
  {
    wp_register_style('cf7_gated_content', plugin_dir_url(__FILE__) . 'css/cf7_gated_content.css');
  }

  /**
   * Enqueue all scripts for admin panels
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function adminScript()
  {
    wp_enqueue_media();

    wp_enqueue_script(
      'cf7_gated_content_admin',
      plugin_dir_url(__FILE__) . 'js/cf7_gated_content_admin.js',
      ['jquery']
    );

    wp_localize_script('cf7_gated_content_admin', 'wpcf7gc', array(
      'mediaTitle' => __('Select gated content', 'apsis_wp')
    ));

    wp_enqueue_style('cf7_gated_content_admin', plugin_dir_url(__FILE__) . 'css/cf7_gated_content_admin.css');
  }

  /**
   * Enqueue all styles for client side forms
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function clientSideScript()
  {
    wp_enqueue_script('cf7_gated_content', plugin_dir_url(__FILE__) . 'js/cf7_gated_content.js', ['jquery'], true);

    wp_localize_script('cf7_gated_content', 'wpcf7gc', array(
      'wpDebug' => WP_DEBUG,
      'ajaxurl' => admin_url('admin-ajax.php'),
      'cookieKey' => GATED_CONTENT_COOKIE_KEY
    ));
  }

  /**
   * Modify the output of the contact form shortcode to render
   * the download box if form has been previously submitted.
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function outputShortcode($output, $tag, $atts, $m)
  {
    if ($tag != 'contact-form-7') {
      return $output;
    }

    $id = $atts ? $atts['id'] : null;

    if (!$id) {
      return '';
    }

    $gated_content_url = get_post_meta($id, 'image_attachment_url', true);
    $include_default_css = get_post_meta($id, 'include_default_css', true);
    $always_require_form = get_post_meta($id, 'always_require_form', true);
    $show_download_for_admin = get_post_meta($id, 'show_download_for_admin', true);
    $enable_gated_content = get_post_meta($id, 'enable_gated_content', true);

    if ($include_default_css) {
      wp_enqueue_style('cf7_gated_content');
    }

    if ($gated_content_url && $enable_gated_content) {
      $cookie_key = GATED_CONTENT_COOKIE_KEY . $id;

      if (
        (!$always_require_form && isset($_COOKIE[$cookie_key])) ||
        ($show_download_for_admin && current_user_can('administrator'))
      ) {
        $output = static::renderDownloadButton($id);
      }
    }

    return $output;
  }

  /**
   * Modify the output of the form response if the form was posted
   * without AJAX and includes the sent-ok class.
   *
   * @since 1.4.2
   * @static
   * @access public
   */
  public static function addFormResponse($output, $class, $_content, $form)
  {
    if ($form->is_posted() && strpos($class, 'wpcf7-mail-sent-ok')) {
      $output = sprintf("%s%s", $output, static::renderDownloadButton($form->id()));
    }

    return $output;
  }

  /**
   * Render the download button. The resulting value is passed through
   * a filter, allowing customization of output for your theme or plugin.
   *
   * <code>
   * function wpcf7_gated_content_button($url, $button_text, $button_classes, $content)
   * {
   *   return "<a href='$url'>$button_text</a>";
   * }
   * add_filter('wpcf7_gated_content_button', 'my_content_button', 10, 3)
   * </code>
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function renderDownloadButton($contact_form_id)
  {
    $enabled = get_post_meta($contact_form_id, 'enable_gated_content', true);
    $url = get_post_meta($contact_form_id, 'image_attachment_url', true);

    if ($enabled && $url) {
      $button_text = get_post_meta($contact_form_id, 'download_button_text', true);
      $button_classes = get_post_meta($contact_form_id, 'download_button_classes', true);
      $content = wp_kses_post(get_post_meta($contact_form_id, 'download_content', true));
      $open_in_new_tab = get_post_meta($contact_form_id, 'open_in_new_tab', true);

      $template_path = dirname(__FILE__) . '/templates/download_button.php';
      $target = $open_in_new_tab ? "_blank" : "_self";

      $output = static::renderTemplate($template_path, compact(
        "url",
        "button_text",
        "button_classes",
        "content",
        "target"
      ));

      return apply_filters(
        'wpcf7_gated_content_button',
        $output,
        $url,
        $button_text,
        $button_classes,
        $content
      );
    }
  }

  /**
   * AJAX response to render download button for JS
   * requests.
   *
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function getDownloadButton()
  {
    $contact_form_id = intval($_POST['contactFormId']);
    wp_send_json_success(static::renderDownloadButton($contact_form_id));
  }

  /**
   * Get and format attachment meta for display in
   * attachment box.
   *
   * @return Array    the meta values for the attached file
   * @since 1.0.0
   * @static
   * @access public
   */
  public static function getAttachmentMeta($id)
  {
    $file = get_attached_file($id);
    $url = wp_get_attachment_url($id);

    return array(
      'filename' => basename($file),
      'url' => $url
    );
  }

  /**
   * Utility function for getting rendered file as a string.
   *
   * @since 1.0.0
   * @static
   */
  static function renderTemplate($path, $params = array())
  {
    if (!empty($path)) {
      ob_start();
      extract($params, EXTR_SKIP);
      include $path;
      return ob_get_clean();
    }
  }

  /**
   * Get default values for gated content settings
   *
   * @return Array    the array of default values
   * @since 1.0.0
   * @static
   */
  static function getDefaults()
  {
    return array(
      "image_attachment_url" => null,
      "image_attachment_id" => null,
      "download_content" => null,
      "download_button_text" => __("Download", "apsis_wp"),
      "download_button_classes" => null,
      "always_require_form" => false,
      "show_download_for_admin" => false,
      "open_in_new_tab" => true,
      "enable_gated_content" => true,
      "attachment_meta" => null,
      "include_default_css" => true
    );
  }

  /**
   * Helper to store meta values
   *
   * @since 1.0.0
   * @static
   */
  static function storeMetaValue($post_id, $key, $value)
  {
    if (!isset($value)) {
      // Delete the meta key if there's no value
      delete_post_meta($post_id, $key);
    } else {
      if (get_post_meta($post_id, $key, false)) {
        // If the custom field already has a value, update it.
        update_post_meta($post_id, $key, $value);
      } else {
        // If the custom field doesn't have a value, add it.
        add_post_meta($post_id, $key, $value);
      }
    }
  }
}

ContactFormGatedContent::init();
