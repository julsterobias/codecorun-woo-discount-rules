<div id="wcdr_discount_rules" class="panel woocommerce_options_panel wcdr_discount_rules_panel" data-trans-key="<?php echo wp_create_nonce('wcdr-nonce-admin'); ?>">
    <div class="wcdr_discount_rules_panel_container">
        <table border="0" class="widefat">
            <tr>
                <td>
                        <select class="widefat" id="wcdr_select_rules__">
                            <option value=""><?php _e('Select Rule'); ?></option>
                            <optgroup label="<?php _e('Lite Version'); ?>">
                            <?php 
                                if($params['rules']['lite_version']): 
                                    foreach($params['rules']['lite_version'] as $index => $lite):    
                            ?>
                                        <option value="<?php echo $index; ?>"><?php echo $lite; ?></option>
                            <?php
                                    endforeach; 
                                endif; 
                            ?>
                            </optgroup>
                            <optgroup label="<?php _e('Pro Version'); ?>">
                            <?php 
                                if($params['rules']['pro_version']): 
                                    foreach($params['rules']['pro_version'] as $index => $pro):    
                            ?>
                                        <option value="<?php echo $index; ?>"><?php echo $pro; ?></option>
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
            <div class="wcdr_no_rules"><center><?php echo _e('No rules available'); ?></center></div>
        </div>

        <div id="wcdr_saved_rules_container">
            <?php echo $params['save_rules']; ?>
        </div>

        <div align="center">
            <ul>
                <li>
                    <?php echo _e('Woo Coupon Discount Rules &copy; '.date('Y').' all rights reserved'); ?>
                </li>
                <li>
                    <a href=""><?php _e('Documentation'); ?></a>&nbsp;
                    <!-- <a href="">Support</a>&nbsp; -->
                    <a href=""><?php _e('Feature Request'); ?></a>&nbsp;
                    <a href=""><b><?php _e('Pro Version'); ?></b></a>
                </li>
            </ul>
        </div>
    </div>
</div>