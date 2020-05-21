<div class="warning postbox">
    <div class="inside">
        <?= __('<strong>Note:</strong> Gating content does not secure it from outside access. While it does restrict visibility of a download link to those who have completed your form, it does nothing to make the file private. Anyone with the attachment link or a properly formatted cookie will be able to access your file.', 'apsis_wp'); ?>
    </div>
</div>

<table class="form-table">
    <tr>
        <th scope="row">
            <?= __('Enable Gated Content', 'apsis_wp'); ?>
        </th>
        <td>
            <label for="enable_gated_content">
                <input name="enable_gated_content" type="checkbox" id="enable_gated_content" value="1" <?= checked($enable_gated_content); ?>>
                <?= __('Enable Gated Content for this Form.', 'apsis_wp'); ?>
            </label>

            <p class="description">
                <?= __('Check this box to enable Gated Content download button when submitting this form.', 'apsis_wp'); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?= __('Gated Content', 'apsis_wp'); ?>
        </th>

        <td>
            <?php $hidden = $image_attachment_id ? null : 'hidden'; ?>

            <div class="attachment_box postbox <?= $hidden; ?>" id="attachment_box">
                <div class="inside">
                    <div class="left">
                        <span class="attachment-icon dashicons dashicons-media-default"></span>
                    </div>

                    <div class="right">
                        <span class="attachment_filename"><?= $attachment_meta["filename"] ?></span>
                        <span class="code attachment_url"><?= $attachment_meta["url"] ?></span>
                    </div>
                </div>
            </div>

            <input type='hidden' name='image_attachment_url' id='image_attachment_url' value='<?= $image_attachment_url; ?>'>
            <input type='hidden' name='image_attachment_id' id='image_attachment_id' value='<?= $image_attachment_id; ?>'>
            <input id="upload_image_button" type="button" class="button-primary" value="<?php _e( 'Set Gated Content', 'apsis_wp' ); ?>" />
            <input id="remove_image_button" type="button" class="button" value="<?php _e( 'Clear', 'apsis_wp' ); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <?= __('Download Box Content', 'apsis_wp'); ?>
        </th>
        <td>
            <?= wp_editor($download_content, 'download_content', array(
                'media_buttons' => false,
                'teeny' => true,
                'textarea_rows' => 6 )
            ); ?>

            <p class="description">
                <?= __('Optionally include content to display above the download button.'); ?>
            </p>
        </td>
    </tr>

    <tr>
        <th scope="row">
            <?= __('Download Button Text', 'apsis_wp'); ?>
        </th>
        <td>
            <input type="text" class="regular-text code" name="download_button_text" value='<?= $download_button_text; ?>'>
        </td>
    </tr>

    <tr>
        <th scope="row">
            <?= __('Button CSS Classes', 'apsis_wp'); ?>
        </th>
        <td>
            <input type="text" class="regular-text code" name="download_button_classes" value='<?= $download_button_classes; ?>'>
        </td>
    </tr>

    <tr>
        <th scope="row">
            <?= __('Always Require Form', 'apsis_wp'); ?>
        </th>
        <td>
            <label for="always_require_form">
                <input name="always_require_form" type="checkbox" id="always_require_form" value="1" <?= checked($always_require_form); ?>>
                <?= __('Require form for repeat visitors.', 'apsis_wp'); ?>
            </label>

            <p class="description">
                <?= __('Check this box to require visitors to complete the form on repeat visits. If left unchecked, visitors who complete the form will not see the form on repeat visits.', 'apsis_wp'); ?>
            </p>
        </td>
    </tr>

    <tr>
        <th scope="row">
            <?= __('Show Download for Admin', 'apsis_wp'); ?>
        </th>
        <td>
            <label for="show_download_for_admin">
                <input name="show_download_for_admin" type="checkbox" id="show_download_for_admin" value="1" <?= checked($show_download_for_admin); ?>>
                <?= __('Always show the download button to admins.', 'apsis_wp'); ?>
            </label>

            <p class="description">
                <?= __('Check this box to always show the download button for admin users.', 'apsis_wp'); ?>
            </p>
        </td>
    </tr>

    <tr>
        <th scope="row">
            <?= __('Open Download in New Tab', 'apsis_wp'); ?>
        </th>
        <td>
            <label for="open_in_new_tab">
                <input name="open_in_new_tab" type="checkbox" id="open_in_new_tab" value="1" <?= checked($open_in_new_tab); ?>>
                <?= __('Open download link in new tab.', 'apsis_wp'); ?>
            </label>

            <p class="description">
                <?= __('Set the <code>target</code> attribute of your download link to <code>_blank</code>.', 'apsis_wp'); ?>
            </p>
        </td>
    </tr>

    <tr>
        <th scope="row">
            <?= __('Include Default CSS', 'apsis_wp'); ?>
        </th>
        <td>
            <label for="include_default_css">
                <input name="include_default_css" type="checkbox" id="include_default_css" value="1" <?= checked($include_default_css); ?>>
                <?= __('Load default download box styles.', 'apsis_wp'); ?>
            </label>
        </td>
    </tr>
</table>

<hr>

<small class="thankyou">
    <?= __('Built with care by Apsis Labs', 'apsis_wp'); ?>.

    <a href="https://apsis.io" target="_blank">
        <?= __('Like our work? Hire us.', 'apsis_wp'); ?>
    </a>
</small>
