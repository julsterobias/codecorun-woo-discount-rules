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

    public function get_cart_items($args)
    {
        if(empty($args))
            return;
        //do not include the bundled items
        global $woocommerce;

        switch($args){
            case 'items':
                return $woocommerce->cart->get_cart();
                break;
            case 'amount':
                return $woocommerce->cart->total;
                break;
            default:
                return $woocommerce->cart;  
                break;
        }
 
    }

    public function apply_coupon()
    {

        if(is_cart() || is_checkout()){

            if(empty($this->get_cart_items('items')))
                return;

            $apply_coupon_ = false;
           
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
                    $cond_value = [];

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
                                        $cond_value[] = $this->check_include_exclude(
                                            [
                                                'type' => 'include',
                                                'rule' => $rule
                                            ]
                                        );
                                        break;
                                    case 'exclude':
                                        $cond_value[] = $this->check_include_exclude(
                                            [
                                                'type' => 'exclude',
                                                'rule' => $rule
                                            ]
                                        );
                                        break;
                                    case 'count':
                                        $cond_value[] = $this->check_count_amount(
                                            [
                                                'type' => 'count',
                                                'rule' => $rule
                                            ]
                                        );
                                        break;
                                    case 'amount':
                                        $cond_value[] = $this->check_count_amount(
                                            [
                                                'type' => 'amount',
                                                'rule' => $rule
                                            ]
                                        );
                                        break;
                                    case 'condition':
                                        $cond_value[] = ($rule == 'and')? '&&' : '||';
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
 
                    }


                    $cond_value = implode(' ',$cond_value);
                    //execute
                    $cond_value = "\$apply_coupon_ = (".$cond_value.");";
                    eval($cond_value);
                    if($apply_coupon_){
                        //yes apply coupon
                        //get coupon details
                    }else{
                        //remove coupon
                        //get coupon details
                    }

                }

               
            }
           
        }
    }

    public function check_include_exclude($args = [])
    {
        if(empty($args['rule']))
            return 0;

        $inc_ids = [];
        foreach($args['rule'] as $prod){
            $id_ = explode('-',$prod);
            if(isset($id_[0])){
                if(is_numeric($id_[0]))
                    $inc_ids[] = $id_[0];
            }
        }
    
        //items count
        $in_rule_count = count($this->get_cart_items('items'));
        $in_cart_count = count($inc_ids);
        $not_in_cart = 0;
        $in_cart = 0;
        
        foreach($this->get_cart_items('items') as $item) { 
            if($args['type'] == 'include'){
                if(in_array($item['data']->get_id(), $inc_ids)){
                    $in_cart++;
                }
            }else{
                if(!in_array($item['data']->get_id(), $inc_ids)){
                    $not_in_cart++;
                }
            }
            
        } 

        if($args['type'] == 'exclude'){
            if($in_rule_count == $not_in_cart){
                return 1;
            }
        }else{
            if($in_cart_count == $in_cart){
                return 1;
            }
        }
    
        return 0;

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

    public function check_count_amount($args = [])
    {
        if(empty($args))
            return 0;


        if($args['type'] == 'count'){
            $in_rule_value = count($this->get_cart_items('items'));
        }else{
            $in_rule_value = $this->get_cart_items('amount');
        }
    
        switch($args['rule']['condition']){
            case 'less_than_equal':
                if($in_rule_value <= $args['rule']['value']){
                    return 1;
                }else{
                    return 0;
                }
                break;
            case 'greater_than_equal':
               
                if($in_rule_value >= $args['rule']['value']){
                    return 1;
                }else{
                    return 0;
                }
                break;
            case 'equal':
                if($in_rule_value == $args['rule']['value']){
                    return 1;
                }else{
                    return 0;
                }
                break;
            default:
                return 0;
                break;
        }
    }

}

?>