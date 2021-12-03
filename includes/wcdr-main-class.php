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

                $cond_value = [];
                foreach($coupons as $coupon){
                    $rules = get_post_meta($coupon->ID,'wcdr-coupon-rules',true);
                    if($rules){
                        foreach($rules as $rule_index => $rule){
                            $type = explode('-',$rule_index);
                            if(count($type) > 1){
                                switch($type[0]){
                                    case 'date':
                                        $cond_value[] = $this->check_today(
                                            [
                                                'type' => 'today',
                                                'date' => $rule
                                            ]
                                        );
                                        break;
                                    case 'date_range':
                                        $cond_value[] = $this->check_today(
                                            [
                                                'from' => $rule['from'],
                                                'to'   => $rule['to'],
                                                'type' => 'date_range'
                                            ]
                                        );
                                        break;
                                    case 'include':

                                        break;
                                    case 'exclude':
                                        break;
                                    case 'count':
                                        break;
                                    case 'amount':
                                        break;
                                    case 'condition':
                                        $cond_value[] = ($rule == 'and')? '&&' : '||';
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
                        print_r($cond_value);
                    }
                }
                
            }
           
        }
    }

    public function check_include($ids = [])
    {
        if(empty($ids))
            return;

            
    }

    public function check_today($args = [])
    {
       
        if(empty($args))
            return;

        if($args['type'] == 'today'){
            $today = strtotime(date('Y-m-d'));
            $date = strtotime(date($args['date']));
            $diff = $date - $today;
            return ($diff == 0)? 1 : 0;
        }else{
            $today = strtotime(date('Y-m-d'));
            $date1 = strtotime(date($args['from']));
            $date2 = strtotime(date($args['to']));
            $diff1 = $date1 - $today;
            $diff2 = $date2 - $today;
            return ($diff1 <= 0 && $diff2 >= 0)? 1 : 0;
        }
    }

}

?>