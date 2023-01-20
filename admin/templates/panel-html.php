<div id="wcdr_discount_rules" class="panel woocommerce_options_panel wcdr_discount_rules_panel" data-trans-key="<?php echo wp_create_nonce('wcdr-nonce-admin'); ?>">
    <div class="wcdr_discount_rules_panel_container">
        <table border="0" class="widefat">
            <tr>
                <td>
                        <select class="widefat" id="wcdr_select_rules__">
                            <option value=""><?php esc_html_e('Select Rule', 'codecorun-coupon-discount-rules'); ?></option>
                            <?php 
                                if($params['rules']['lite_version']): 
                                    foreach($params['rules']['lite_version'] as $index => $lite):    
                            ?>
                                        <option value="<?php esc_html_e($index, 'codecorun-coupon-discount-rules'); ?>"><?php esc_html_e($lite, 'codecorun-coupon-discount-rules'); ?></option>
                            <?php
                                    endforeach; 
                                endif; 
                            ?>
                            <?php 
                                if($params['rules']['pro_version']): 
                                    foreach($params['rules']['pro_version'] as $index => $pro):    
                            ?>
                                        <option value="<?php esc_html_e($index, 'codecorun-coupon-discount-rules'); ?>"><?php esc_html_e($pro, 'codecorun-coupon-discount-rules'); ?></option>
                            <?php
                                    endforeach; 
                                endif; 
                            ?>
                        </select>
                    
                </td>
            </tr>
        </table>

        <div class="wcdr_rules_canvas__">
            <div class="wcdr_no_rules"><center><?php esc_html_e('No rules available', 'codecorun-coupon-discount-rules'); ?></center></div>
        </div>

        <div id="wcdr_saved_rules_container">
            <?php esc_html_e($params['save_rules'], 'codecorun-coupon-discount-rules'); ?>
        </div>


        <div align="center">
            <ul>
                <li>
                    <?php esc_html_e('Codecorun - Coupon Discount Rules &copy; '.date('Y').' all rights reserved', 'codecorun-coupon-discount-rules'); ?>
                </li>
                <li>
                    <!-- subject to change the support URL -->
                    <a href="<?php echo esc_url('https://codecorun.com/my-tickets/'); ?>" target="_blank"><?php esc_html_e('Support', 'codecorun-coupon-discount-rules'); ?></a>&nbsp;
                    <a href="<?php echo esc_url('https://codecorun.com/documentation/woocommerce-coupon-discount-rules/'); ?>" target="_blank"><b><?php esc_html_e('Documentation', 'codecorun-coupon-discount-rules'); ?></b></a>
                </li>
            </ul>
        </div>
    </div>
</div>