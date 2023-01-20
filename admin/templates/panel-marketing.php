<div class="codecorun_layout_corner">
    <div class="codecorun_layout_canvas">
        <table border="0" width="100%">
            <thead>
                <tr>
                    <td width="50%">
                        <img src="<?php echo CODECORUN_CDR_URL; ?>admin/assets/logo.svg" width="130"> &nbsp;<?php echo CODECORUN_CDR_VERSION;  ?>
                    </td>
                    <td width="50%" align="right">
                        <a href="<?php echo esc_url('https://codecorun.com/documentation/woocommerce-coupon-discount-rules/'); ?>"><?php esc_html_e('Documentation', 'codecorun-coupon-discount-rules'); ?></a>&nbsp;&nbsp;
                        <a href="<?php echo esc_url('https://codecorun.com/my-tickets/'); ?>"><?php esc_html_e('Support', 'codecorun-coupon-discount-rules'); ?></a>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td valign="top" colspan="2">
                        <div class="codecorun_content_wcdr" >
                            <label>
                                <input type="checkbox" id="wcdr_noti_status" <?php echo ($params['settings']['enabled'])? 'checked' : null; ?>> <?php esc_html_e('Enable discount notification when applied', 'codecorun-coupon-discount-rules') ?>
                            </label>
                            <div class="wcdr_noti_field">
                                <label><?php esc_html_e('Message','codecorun-coupon-discount-rules'); ?></label>
                                <?php 
                                    $message = ($params['settings']['message'])? $params['settings']['message'] : ''; 
                                    echo wp_editor($message, 'wcdr_noti_message',['editor_height' => 200]);
                                    
                                    $pos = [
                                        'center' => '',
                                        'top' => '',
                                        'bottom' => ''
                                    ];

                                    if(!isset($params['settings']['pos'])){
                                        $pos['center'] = 'active';
                                    }else{
                                        $pos[$params['settings']['pos']] = 'active';
                                    }

                                ?>
                                <p>
                                <span class="dashicons dashicons-info"></span> <?php esc_html_e('To display all applied coupon description add the [codecorun_wcdr_applied_codes] in your notification message.', 'codecorun-coupon-discount-rules'); ?></p>
                            </div>
                            <div class="wcdr_noti_field">
                                <table width="100%" class="wcdr_noti_pos_table">
                                    <tr>
                                        <td width="33.3%">
                                            <span class="wcdr_noti_pos center <?php esc_html_e($pos['center'],'codecorun-coupon-discount-rules'); ?>" data-pos="center">
                                                <span></span>
                                            </span>
                                            <?php esc_html_e('Center', 'codecorun-coupon-discount-rules') ?>
                                        </td>
                                        <td width="33.3%">
                                            <span class="wcdr_noti_pos top <?php esc_html_e($pos['top'],'codecorun-coupon-discount-rules') ?>" data-pos="top">
                                                <span></span>
                                            </span>
                                            <?php esc_html_e('Top', 'codecorun-coupon-discount-rules'); ?>
                                        </td>
                                        <td width="33.3%">
                                            <span class="wcdr_noti_pos bottom <?php echo $pos['bottom'] ?>" data-pos="bottom">
                                                <span></span>
                                            </span>
                                            <?php esc_html_e('Bottom', 'codecorun-coupon-discount-rules'); ?>
                                        </td>
                                        
                                    </tr>
                                </table>
                                
                                <div class="wcdr_noti_field">
                                    <label><?php esc_html_e('Custom CSS','codecorun-coupon-discount-rules'); ?>
                                    <textarea id="wcdr_custom_css" name="wcdr_custom_css" class="widefat"><?php echo $params['settings']['css']; ?></textarea></label>
                                </div>
                                <div class="wcdr_noti_field">
                                    <button class="button button-primary" id="codecorun_save_settings" data-to-save="wcdr" data-transkey="<?php echo wp_create_nonce('wcdr-nonce-admin'); ?>" ><?php esc_html_e('Save changes', 'codecorun-coupon-discount-rules'); ?></button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div align="center">
            <p><?php esc_html_e('Codecorun - Woocommerce Discount Rules &copy; '.date('Y').' All Rights Reserved', 'codecorun-coupon-discount-rules'); ?></p>
            
        </div>
    </div>
</div>