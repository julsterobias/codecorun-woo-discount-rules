<?php
/** 
 * 
 * 
 * Plugin Name: Codecorun - Coupon Discount Rules
 * Plugin URI: https://codecorun.com/plugins/woocommerce-coupon-discount-rules/
 * Description: A WooCommerce coupon extension plugin that will allow you to set single or multiple rules with "AND" conditional operation.
 * Author:      Codecorun
 * Author URI: https://codecorun.com
 * Plugin Type: Extension
 * Version: 1.3.1
 * Text Domain: codecorun-coupon-discount-rules
 * 
 * 
*/

defined( 'ABSPATH' ) or die( 'No access area' );
define('CODECORUN_CDR_PATH', plugin_dir_path( __FILE__ ));
define('CODECORUN_CDR_URL', plugin_dir_url( __FILE__ ));
define('CODECORUN_CDR_PREFIX','codecorun_cdr');
define('CODECORUN_CDR_VERSION','1.3.0');
define('CODECORUN_CDR_PRO_ID','codecorun-coupon-discount-rules-pro/codecorun-cdr-plugin-pro.php');

add_action( 'init', 'codecorun_wcdr_load_textdomain' );
function codecorun_wcdr_load_textdomain() {
	load_plugin_textdomain( 'codecorun-coupon-discount-rules', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function codecorun_wcdr_install(){
	if(class_exists('WooCommerce'))
		return;

	echo '<strong>'.__('This plugin requires woocommerce installation', 'codecorun-coupon-discount-rules').'</strong>';
    @trigger_error(__('Woocommerce plugin is missing', 'codecorun-coupon-discount-rules'), E_USER_ERROR);
}
register_activation_hook( __FILE__, 'codecorun_wcdr_install' );

//autoload classes
spl_autoload_register(function ($class) {

	if(strpos($class,CODECORUN_CDR_PREFIX) !== false){
		$class = preg_replace('/\\\\/', '{'.CODECORUN_CDR_PREFIX.'}', $class);
        $fullclass = $class;
		$class = explode('{'.CODECORUN_CDR_PREFIX.'}', $class);
		if(!empty(end($class))){
			$filename = str_replace("_", "-", end($class));
            $admin = (strpos($fullclass,'admin') !== false)? 'admin/' : null;
			include $admin.'includes/'.$filename.'.php';
		}
	}

});

add_action('plugins_loaded','codecorun_cdr_init');
function codecorun_cdr_init(){
		
	if(current_user_can('administrator')){
		//load admin class
		new codecorun\cdr\admin\codecorun_cdr_admin_class();
	}
		
	//load global class
	new codecorun\cdr\main\codecorun_cdr_main_class();
		
}
?>