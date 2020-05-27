<div class='wpcf7-gated-content'>
    <?php if ( !empty($content) ) : ?>
        <div class='wpcf7-content'><?= $content; ?></div>
    <?php endif; ?>

    <div class='wpcf7-download'>
        <a href='<?= $url; ?>' class='<?= $button_classes; ?> wpcf7-btn' download target="<?= $target ?>">
            <?= $button_text; ?>
        </a>
    </div>
</div>
