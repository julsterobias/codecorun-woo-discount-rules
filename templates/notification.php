<div class="wcdr_notification_overlay">
    <div class="wcdr_discount_container wcdr_mode_<?php echo $params['settings']['pos']; ?> wcdr_mode_<?php echo $params['settings']['pos']; ?>_def" data-status="<?php echo $params['settings']['pos']; ?>">
        <div class="wcdr_discount_content">
            <?php echo do_shortcode($params['settings']['message']); ?>
        </div>
        <div class="wcdr_discount_btn">
            <a href="javascript:void(0);" class="wcdr_noti_close_bnt"><?php _e('Close',CODECORUN_CDR_PREFIX); ?></a>
        </div>
    </div>
</div>