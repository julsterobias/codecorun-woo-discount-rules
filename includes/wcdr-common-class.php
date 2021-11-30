<?php 
/**
 * 
 * wcdr-common-class
 * @version 1.0
 * 
 */
namespace wcdr\common;

defined( 'ABSPATH' ) or die( 'No access area' );

class wcdr_common_class
{
    public function set_template($file, $params = array())
    {
        if(!$file)
			return;

		if(strpos($file,'.php') === false)
			$file = $file.'.php';

		$other = null;
		if(isset($params['other'])){
			$other = $params['other'].'/';
		}

		$path = get_template_directory().'/'.WCDR_FOLDER_NAME.'/'.$other.'templates';
		$child = get_template_directory().'-child/'.WCDR_FOLDER_NAME.'/'.$other.'templates';

		if(is_dir($path.'/'.$file)){
			include $path.'/'.$file;
		}elseif(is_dir($child.'/'.$file)){
			include $child.'/'.$file;
		}else{
			if(isset($params['other'])){
				$other = $params['other'];
			}
			include WCDR_API_PATH.$other.'/templates/'.$file;
		}
    }

	public static function rules()
	{
		return apply_filters(
			'wcdr-rules',
			[
				'lite_version' => [
					'date' => __('Date'),
					'date-range' => __('Date Range'),
					'include' => __('Include'),
					'exclude' => __('Exclude'),
					'count' => __('Number of item(s) in cart'),
					'amount' => __('Total Amount')
				],
				'pro_version' => [
					'include_category' => __('Include Category'),
					'exclude_category' => __('Exclude Category'),
					'country' => __('Country'),
					'first_order' => __('First Order'),
					'nth_order' => __('User\'s (n)th order'),
					'previous_orders' => __('User has number of previous order(s)'),
					'metas' => __('User has meta(s)'),
					'role' => __('User has role(s)'),
					'url_param' => __('URL has parameter(s)')
				]
			]
		);
	}

	public static function translatable_text()
	{
		return apply_filters(
			'wcdr-labels',[
				'date' => __('Date'),
				'date_range' => __('Date Range'),
				'from' => __('From'),
				'to' => __('Include'),
				'include' => __('Include'),
				'exclude' => __('Exclude'),
				'product' => __('Product'),
				'select_product' => __('Select Product'),
				'items_in_cart' => __('Number of item(s) in cart'),
				'total_amount' => __('Total amount in the cart'),
				'or' => __('Or'),
				'and' => __('And'),
				'condition' => __('Condition'),
				'confirm_rule' => __('Are you sure you want to remove this rule?'),
				'confirm_product' => __('Are you sure you want to remove this product?'),
				'remove' => 'Remove'
			]
		);
	}
}
?>