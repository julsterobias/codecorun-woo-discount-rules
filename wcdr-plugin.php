<?php
/** 
 * 
 * Plugin Name: Codecorun - Coupon Discount Rules
 * Plugin URI: https://codecorun.com/documentation/woocommerce-coupon-discount-rules/
 * Description: A coupon extension plugin that will allow you to set single or multiple rules with "AND" conditional operation.
 * Author:      Codecorun
 * Plugin Type: Extension
 * Author URI: https://codecorun.com
 * Version: 1.0.2
 * Text Domain: wcdr
 * 
 * 
*/

defined( 'ABSPATH' ) or die( 'No access area' );
define('WCDR_API_PATH', plugin_dir_path( __FILE__ ));
define('WCDR_API_URL', plugin_dir_url( __FILE__ ));
define('WCDR_FOLDER_NAME','codecorun-woo-discount-rules');
define('WCDR_PREFIX','wcdr');
define('WCDR_VERSION','1.0.2');

if(!function_exists('codecorun_wcdr_load_textdomain')){
	add_action( 'init', 'codecorun_wcdr_load_textdomain' );
	function codecorun_wcdr_load_textdomain() {
		load_plugin_textdomain( 'wcdr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}
}

function codecorun_wcdr_install(){
	if(class_exists('WooCommerce'))
		return;		

	echo '<h3>'.__('Plugin failed to install', WCDR_PREFIX).'</h3>';
    @trigger_error(__('This plugin requires WooCommerce installation', WCDR_PREFIX), E_USER_ERROR);
}
register_activation_hook( __FILE__, 'codecorun_wcdr_install' );

//autoload classes
spl_autoload_register(function ($class) {

	if(strpos($class,WCDR_PREFIX) !== false){
		$class = preg_replace('/\\\\/', '{'.WCDR_PREFIX.'}', $class);
        $fullclass = $class;
		$class = explode('{'.WCDR_PREFIX.'}', $class);
		if(!empty(end($class))){
			$filename = str_replace("_", "-", end($class));
            $admin = (strpos($fullclass,'admin') !== false)? 'admin/' : null;
			include $admin.'includes/'.$filename.'.php';
		}
	}

});


add_action('plugins_loaded','thsa_wcdr_init');
function thsa_wcdr_init(){
	
	if(current_user_can('administrator')){
		//load admin class
		new wcdr\admin\wcdr_admin_class();
	}
	
	//load global class
	new \wcdr\main\wcdr_main_class();
	
}
?>