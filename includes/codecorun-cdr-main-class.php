<?php
/**
 * 
 * wcdr_main_class
 * @version 1.0
 * 
 */
namespace codecorun\cdr\main;

defined( 'ABSPATH' ) or die( 'No access area' );

class codecorun_cdr_main_class{

    private static $instance = null;

    /**
     * 
     * factory instance method
     * @since 1.0
     * static
     * 
     */
    public static function factory()
    {
        if(!self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * 
     * construct
     * @since 1.0
     * 
     */
    public function __construct()
    {
        //render assets
        add_action('template_redirect', [$this, 'apply_coupon']);
    }

    /**
     * 
     * get_cart_items
     * @since 1.0
     * @param string
     * @return mixed
     * 
     */
    public function get_cart_items($args)
    {
        if(empty($args))
            return;
        
        global $woocommerce;

        switch($args){
            case 'items':
                return $woocommerce->cart->get_cart();
                break;
            case 'amount':
                return $woocommerce->cart->total;
                break;
            case 'count':
                $items = $woocommerce->cart->get_cart();
                $chained_products = [];
                foreach($items as $item){
                   $chained_products[] = get_post_meta($item['data']->get_id(),'_chained_product_ids',true);
                }
                //do not include the bundled items
                $items_count = 0;
                foreach($items as $item){
                    $not_chained = false;
                    foreach($chained_products as $chained){
                        if(is_array($chained)){
                            if(in_array($item['data']->get_id(),$chained)){
                                $not_chained = true;
                                break;
                            }
                        }
                    }
                    if(!$not_chained){
                        $items_count++;
                    }
                }
                return $items_count;
                break;
            default:
                return $woocommerce->cart;  
                break;
        }
 
    }

    /**
     * 
     * apply_coupon
     * @since 1.0
     * @param none
     * @return none
     * 
     */

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
                $cond_collections = [];
                foreach($coupons as $coupon){

                    $cond_value = [];
                    $rules = get_post_meta($coupon->ID,'wcdr-coupon-rules',true);
                    
                    if($rules){
                        //remove added coupons
                        if(in_array( $coupon->post_name, WC()->cart->get_applied_coupons())){
                            WC()->cart->remove_coupon( $coupon->post_name );
                            WC()->cart->calculate_totals();
                        }

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

                    $cond_collections[$coupon->ID] = [
                        'coupon_code' => $coupon->post_name,
                        'rule' => $cond_value
                    ];

                }


                foreach($cond_collections as $collection){

                     /**
                     * execute using eval() - deprecated
                     * @since 1.0.2
                     * we deprecated this approach because the wordpress is not approving the idea.
                     * 
                     */
                    //$cond_value = implode(' ',$collection['rule']);
                    //$cond_value = "\$apply_coupon_ = (".$cond_value.");";
                    //eval($cond_value);
                    
                    //evaluate the grouped results together with 'and' operation
                    $apply_coupon_ = true;
                    foreach($collection['rule'] as $result_and){
                        if($result_and == 0){
                            $apply_coupon_ = false;
                        }
                    }

                    if($apply_coupon_){
                        //yes apply coupon
                        //get coupon details
                        $coupon_ = new \WC_Coupon($collection['coupon_code']);
                        //Why this is not working???
                        //$WC_Discounts = new \WC_Discounts();
                        //$WC_Discounts->is_coupon_valid( $coupon_ )
                        //deprecated watch for the day they will remove this
                        if($coupon_->is_valid()){
                            //implement discounts
                            WC()->cart->add_discount( $collection['coupon_code'] );
                        }
                    }
                }
               
            }
           
        }
    }

    /**
     * 
     * check_include_exclude
     * @since 1.0
     * @param array
     * @return int
     * 
     */
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

    /**
     * 
     * check_today
     * @since 1.0
     * @param array
     * @return int
     * 
     */
    public function check_today($args = [])
    {
       
        if(empty($args))
            return;

        $locatime = explode(' ',current_time( 'mysql' ));

        if($args['type'] == 'today'){
            $args['date'] = $this->extra_sanitize($args['date'], 'string');
            $today = strtotime($locatime[0]);
            $date = strtotime(date($args['date']));
            $diff = $date - $today;
            return ($diff == 0)? 1 : 0;
        }else{
            //extra sanitize
            $args['from'] = $this->extra_sanitize($args['from'], 'string');
            $args['to'] = $this->extra_sanitize($args['to'], 'string');
            $today = strtotime($locatime[0]);
            $date1 = strtotime(date($args['from']));
            $date2 = strtotime(date($args['to']));
            $diff1 = $date1 - $today;
            $diff2 = $date2 - $today;
            return ($diff1 <= 0 && $diff2 >= 0)? 1 : 0;
        }
    }

    /**
     * 
     * check_count_amount
     * @since 1.0
     * @param array
     * @return int
     * 
     */
    public function check_count_amount($args = [])
    {
        if(empty($args))
            return 0;
        

        if($args['type'] == 'count'){
            $in_rule_value = $this->get_cart_items('count');
        }else{
            $in_rule_value = $this->get_cart_items('amount');
        }

        //extra sanitize
        $args['rule']['value'] = $this->extra_sanitize($args['rule']['value'], 'number');
    
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

    /**
     * 
     * extra_sanitize
     * @since 1.0.2
     * @param mixed, string
     * @return string
     * 
     */
    public function extra_sanitize($input = null, $type = '')
    {
        if(!$input)
            return;

        switch($type){
            case 'number':
                if(!is_numeric($input))
                    return 0;
                else
                    return $input;
            break;
            case 'string':
                return sanitize_text_field($input);
            break;
        }
        
    }

}

?>