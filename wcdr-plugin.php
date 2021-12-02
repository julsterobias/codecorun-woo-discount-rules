<?php
/** 
 * 
 * Plugin Name: Woo Coupon Discount Rules
 * Description: Woocommerce coupon extension to set single or multiple rules for coupon discount
 * Author:      wolfplug
 * Plugin Type: Functionality
 * Version: 1.0
 * 
 * 
*/

defined( 'ABSPATH' ) or die( 'No access area' );
define('WCDR_API_PATH', plugin_dir_path( __FILE__ ));
define('WCDR_API_URL', plugin_dir_url( __FILE__ ));
define('WCDR_FOLDER_NAME','woo-coupon-discount-rules');
define('WCDR_PREFIX','wcdr');
define('WCDR_VERSION','1.0');

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