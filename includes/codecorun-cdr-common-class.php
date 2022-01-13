<?php 
/**
 * 
 * wcdr-common-class
 * @version 1.0
 * 
 */
namespace codecorun\cdr\common;

defined( 'ABSPATH' ) or die( 'No access area' );

class codecorun_cdr_common_class
{
	/**
	 * 
	 * set_template
	 * @since 1.0
	 * @param string, array
	 * @return none
	 * 
	 */
    public function set_template($file, $params = [])
    {
        if(!$file)
			return;

		if(strpos($file,'.php') === false)
			$file = $file.'.php';

		$other = null;
		if(isset($params['other'])){
			$other = $params['other'].'/';
		}

		//get plugin folder name without manually assigning the folder name
		$plugin_folder = explode('/',CODECORUN_CDR_URL);
		$plugin_folder = array_filter($plugin_folder);
		
		$path = get_template_directory().'/'.end($plugin_folder).'/'.$other.'templates';
		$child = get_template_directory().'-child/'.end($plugin_folder).'/'.$other.'templates';

		if(is_dir($path.'/'.$file)){
			include $path.'/'.$file;
		}elseif(is_dir($child.'/'.$file)){
			include $child.'/'.$file;
		}else{
			if(isset($params['other'])){
				$other = $params['other'];
			}
			include CODECORUN_CDR_PATH.$other.'/templates/'.$file;
		}
    }

	/**
	 * 
	 * rules
	 * @since 1.0
	 * @param
	 * @return array
	 * 
	 */

	public static function rules()
	{
		return apply_filters(
			'wcdr-rules',
			[
				'lite_version' => [
					'date' => __('Date','codecorun-coupon-discount-rules'),
					'date-range' => __('Date Range','codecorun-coupon-discount-rules'),
					'include' => __('Include Product(s)','codecorun-coupon-discount-rules'),
					'exclude' => __('Exclude Product(s)','codecorun-coupon-discount-rules'),
					'count' => __('Number of item(s) in cart','codecorun-coupon-discount-rules'),
					'amount' => __('Total Amount','codecorun-coupon-discount-rules')
				]
			]
		);
	}


	/**
	 * 
	 * translatable_text
	 * @since 1.0
	 * @param
	 * @return array
	 * 
	 */
	public static function translatable_text()
	{
		return apply_filters(
			'wcdr-labels',[
				'date' => __('Date','codecorun-coupon-discount-rules'),
				'date_range' => __('Date Range','codecorun-coupon-discount-rules'),
				'from' => __('From','codecorun-coupon-discount-rules'),
				'to' => __('To','codecorun-coupon-discount-rules'),
				'include' => __('Include','codecorun-coupon-discount-rules'),
				'exclude' => __('Exclude','codecorun-coupon-discount-rules'),
				'product' => __('Product(s)','codecorun-coupon-discount-rules'),
				'select_product' => __('Select Product','codecorun-coupon-discount-rules'),
				'items_in_cart' => __('Number of item(s) in cart','codecorun-coupon-discount-rules'),
				'total_amount' => __('Total amount in the cart','codecorun-coupon-discount-rules'),
				'or' => __('Or','codecorun-coupon-discount-rules'),
				'and' => __('And','codecorun-coupon-discount-rules'),
				'condition' => __('Condition','codecorun-coupon-discount-rules'),
				'confirm_rule' => __('Are you sure you want to remove this rule?','codecorun-coupon-discount-rules'),
				'confirm_product' => __('Are you sure you want to remove this product?','codecorun-coupon-discount-rules'),
				'remove' => __('Remove','codecorun-coupon-discount-rules'),
				'less_than_equal' => __('Less than or equal','codecorun-coupon-discount-rules'),
				'greater_than_equal' => __('Greater than or equal','codecorun-coupon-discount-rules'),
				'equal' => __('Equal','codecorun-coupon-discount-rules'),
				'less_than' => __('Less than','codecorun-coupon-discount-rules'),
				'tooltip_date' => __('Date when the coupon can be applied', 'codecorun-coupon-discount-rules'),
				'tooltip_date_range' => __('Duration of the date when the coupon can be applied', 'codecorun-coupon-discount-rules'),
				'tooltip_include_products' => __('Coupon will apply when product(s) IN the cart', 'codecorun-coupon-discount-rules'),
				'tooltip_exclude_products' => __('Coupon will apply when product(s) NOT IN the cart', 'codecorun-coupon-discount-rules'),
				'tooltip_number_items' => __('Coupon will apply when X is equal to number of product(s) in cart', 'codecorun-coupon-discount-rules'),
				'tooltip_total_amount' => __('Coupon will apply when X is equal to the total amount in cart', 'codecorun-coupon-discount-rules'),
			]
		);
	}

}
?>