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
					'date' => __('Date',WCDR_PREFIX),
					'date-range' => __('Date Range',WCDR_PREFIX),
					'include' => __('Include',WCDR_PREFIX),
					'exclude' => __('Exclude',WCDR_PREFIX),
					'count' => __('Number of item(s) in cart',WCDR_PREFIX),
					'amount' => __('Total Amount',WCDR_PREFIX)
				],
				'pro_version' => [
					'include_category' => __('Include Category',WCDR_PREFIX),
					'exclude_category' => __('Exclude Category',WCDR_PREFIX),
					'country' => __('Country',WCDR_PREFIX),
					'first_order' => __('First Order',WCDR_PREFIX),
					'nth_order' => __('User\'s (n)th order',WCDR_PREFIX),
					'previous_orders' => __('User has number of previous order(s)',WCDR_PREFIX),
					'metas' => __('User has meta(s)',WCDR_PREFIX),
					'role' => __('User has role(s)',WCDR_PREFIX),
					'url_param' => __('URL has parameter(s)',WCDR_PREFIX)
				]
			]
		);
	}

	public static function translatable_text()
	{
		return apply_filters(
			'wcdr-labels',[
				'date' => __('Date',WCDR_PREFIX),
				'date_range' => __('Date Range',WCDR_PREFIX),
				'from' => __('From',WCDR_PREFIX),
				'to' => __('Include',WCDR_PREFIX),
				'include' => __('Include',WCDR_PREFIX),
				'exclude' => __('Exclude',WCDR_PREFIX),
				'product' => __('Product',WCDR_PREFIX),
				'select_product' => __('Select Product',WCDR_PREFIX),
				'items_in_cart' => __('Number of item(s) in cart',WCDR_PREFIX),
				'total_amount' => __('Total amount in the cart',WCDR_PREFIX),
				'or' => __('Or',WCDR_PREFIX),
				'and' => __('And',WCDR_PREFIX),
				'condition' => __('Condition',WCDR_PREFIX),
				'confirm_rule' => __('Are you sure you want to remove this rule?',WCDR_PREFIX),
				'confirm_product' => __('Are you sure you want to remove this product?',WCDR_PREFIX),
				'remove' => __('Remove',WCDR_PREFIX),
				'less_than_equal' => __('Less than and equal',WCDR_PREFIX),
				'greater_than_equal' => __('Greater than and equal',WCDR_PREFIX),
				'equal' => __('Equal',WCDR_PREFIX),
				'less_than' => __('Less than',WCDR_PREFIX)
			]
		);
	}

}
?>