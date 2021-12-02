<?php
/**
 * 
 * wcdr_main_class
 * @version 1.0
 * 
 */
namespace wcdr\main;

defined( 'ABSPATH' ) or die( 'No access area' );

class wcdr_main_class{

    private static $instance = null;
    private $rules = '';

    public static function factory()
    {
        if(!self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        //render assets
        add_action('template_redirect', [$this, 'apply_coupon']);
    }

    public function apply_coupon()
    {
        if(is_cart() || is_checkout()){
           
            //get coupons with wcdr meta
            $args = [
                'posts_per_page' => -1,
                'post_type' => 'shop_coupon',
                'meta_query' => [
                    [
                        'key' => 'wcdr-coupon-rules',
                        'compare' => 'EXISTS'
                    ]
                ],
                'post_status' => 'publish'
            ];
            $coupons = get_posts($args);
            if($coupons){
                //get meta

                foreach($coupons as $coupon){
                    $rules = get_post_meta($coupon->ID,'wcdr-coupon-rules',true);
                    if($rules){
                        foreach($rules as $rule_index => $rule){
                            $type = explode('-',$rule);
                            if(count($type) > 1){

                                switch($type[0]){
                                    case 'date':
                                        
                                        break;
                                    case 'date-range':
                                        break;
                                    case 'include':
                                        break;
                                    case 'exclude':
                                        break;
                                    case 'count':
                                        break;
                                    case 'amount':
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                    }
                }
                
            }
           
        }
    }

}

?>