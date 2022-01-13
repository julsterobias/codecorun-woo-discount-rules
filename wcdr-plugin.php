<?php
/** 
 * 
 * Plugin Name: Codecorun - Coupon Discount Rules
 * Plugin URI: https://codecorun.com/plugins/woocommerce-coupon-discount-rules/
 * Description: A coupon extension plugin that will allow you to set single or multiple rules with "AND" conditional operation.
 * Author:      Codecorun
 * Plugin Type: Extension
 * Author URI: https://codecorun.com
 * Version: 1.0.2
 * Text Domain: codecorun-coupon-discount-rules
 * 
 * 
*/

defined( 'ABSPATH' ) or die( 'No access area' );
define('CODECORUN_CDR_PATH', plugin_dir_path( __FILE__ ));
define('CODECORUN_CDR_URL', plugin_dir_url( __FILE__ ));
define('CODECORUN_CDR_PREFIX','codecorun_cdr');
define('CODECORUN_CDR_VERSION','1.0.2');

add_action( 'init', 'codecorun_cdr_load_textdomain' );
function codecorun_cdr_load_textdomain() {
	load_plugin_textdomain( 'codecorun-coupon-discount-rules', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function codecorun_cdr_install(){
	if(class_exists('WooCommerce'))
		return;		

	echo '<h3>'.__('Plugin failed to install', 'codecorun-coupon-discount-rules').'</h3>';
    @trigger_error(__('This plugin requires WooCommerce installation', 'codecorun-coupon-discount-rules'), E_USER_ERROR);
}
register_activation_hook( __FILE__, 'codecorun_cdr_install' );

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