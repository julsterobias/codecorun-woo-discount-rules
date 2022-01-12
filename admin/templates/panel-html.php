<div id="wcdr_discount_rules" class="panel woocommerce_options_panel wcdr_discount_rules_panel" data-trans-key="<?php echo wp_create_nonce('wcdr-nonce-admin'); ?>">
    <div class="wcdr_discount_rules_panel_container">
        <table border="0" class="widefat">
            <tr>
                <td>
                    <select class="widefat" id="wcdr_select_rules__">
                        <option value=""><?php esc_html_e('Select Rule', WCDR_PREFIX); ?></option>
                        <?php 
                            if($params['rules']['lite_version']): 
                                foreach($params['rules']['lite_version'] as $index => $lite):    
                        ?>
                                <option value="<?php esc_html_e($index, WCDR_PREFIX); ?>"><?php esc_html_e($lite, WCDR_PREFIX); ?></option>
                        <?php
                                endforeach;
                            endif;
                        ?>
                        </optgroup>
                    </select>
                </td>
            </tr>
        </table>

        <div class="wcdr_rules_canvas__">
            <div class="wcdr_no_rules"><center><?php esc_html_e('No rules available', WCDR_PREFIX); ?></center></div>
        </div>

        <div id="wcdr_saved_rules_container">
            <?php esc_html_e($params['save_rules'], WCDR_PREFIX); ?>
        </div>

        <div align="center">
            <ul>
                <li>
                    <?php esc_html_e('Codecorun - WooCommerce Discount Rules &copy; '.date('Y').' all rights reserved', WCDR_PREFIX); ?>
                </li>
                <li>
                    <!-- subject to change the support URL -->
                    <a href="mail:codecorun@gmail.com" target="_blank"><?php esc_html_e('Support', WCDR_PREFIX); ?></a>&nbsp;
                    <a href="<?php echo esc_url('https://codecorun.com/plugins/woocommerce-coupon-discount-rules/'); ?>" target="_blank"><b><?php esc_html_e('Full Version', WCDR_PREFIX); ?></b></a>
                </li>
            </ul>
        </div>
    </div>
</div>