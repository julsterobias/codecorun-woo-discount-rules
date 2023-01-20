<?php
/**
 * 
 * wcdr_main_class
 * @version 1.2.0
 * @author codecorun
 * 
 */
namespace codecorun\cdr\main;
use codecorun\cdr\common\codecorun_cdr_common_class;

defined( 'ABSPATH' ) or die( 'No access area' );

class codecorun_cdr_main_class extends codecorun_cdr_common_class{

    /**
     * 
     * Instance variable for class initialization
     * 
     */
    private static $instance = null;

    /**
     * 
     * to get notification settings data set from admin
     * 
     */
    private $notification = null;

    /**
     * 
     * store all applied coupon for later use in notification
     * 
     */
    private $applied_codes = [];

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
        add_action('template_redirect', [$this, 'load_notification']);
       
        $this->notification = get_option('codecorun_wcdr_noti_settings');

        //shortcodes
        add_shortcode('codecorun_wcdr_applied_codes', [$this, 'applied_codes']);
        
        //apply custom css in the footer
        add_action('wp_footer',[$this,'custom_css']);
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
                        
                        //remove added coupons for revalidation
                        $coupon_name_ = strtolower( $coupon->post_title );
                        if(in_array( $coupon_name_, WC()->cart->get_applied_coupons())){
                            WC()->cart->remove_coupon( $coupon_name_ );
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

                                    //pro features below
                                    case 'include_category':
                                    case 'exclude_category':
                                        $cond_value[] = $this->check_include_exclude_category(
                                            [
                                                'type' => $type[0],
                                                'rule' => $rule 
                                            ]
                                        );
                                        break;
                                    case 'had_purchased_product':
                                        $cond_value[] = $this->check_had_purchased($rule);
                                        break;
                                    case 'previous_orders':
                                        $cond_value[] = $this->check_prev_orders($rule);
                                        break;
                                    case 'metas':
                                        $cond_value[] = $this->check_meta_values($rule);
                                        break;
                                    case 'role':
                                        $cond_value[] = $this->check_role($rule);
                                        break;
                                    case 'url_param':
                                        $cond_value[] = $this->check_param($rule);
                                        break;
                                    default:
                                        break;
                                }
                                
                            }
                        }
 
                    }

                    $cond_collections[$coupon->ID] = [
                        'coupon_code' => strtolower( $coupon->post_title ),
                        'rule' => $cond_value
                    ];

                }


                foreach($cond_collections as $collection){

       
                    if( is_plugin_active( CODECORUN_CDR_PRO_ID ) ){
                        $prospace = 'codecorun\cdr\pro\main\codecorun_cdr_pro_main_class';
                        $extend = new $prospace;
                        $apply_coupon_ = $extend::extend_operand( $collection['rule'] );
                    }else{
                        //evaluate the 'and' operation
                        $apply_coupon_ = true;
                        foreach($collection['rule'] as $result_and){
                            if($result_and == 0){
                                $apply_coupon_ = false;
                            }
                        }
                    }
                    

                    $apply_coupon_ = apply_filters('codecorun_on_apply_coupon', $apply_coupon_);
                   

                    if($apply_coupon_){
                        $coupon_ = new \WC_Coupon($collection['coupon_code']);                
                        $WC_Discounts = new \WC_Discounts();
                        $isvalid = $WC_Discounts->is_coupon_valid( $coupon_ );
                        if( $isvalid ){
                            //implement discounts
                            $this->applied_codes[] = $collection['coupon_code'];
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
            $today = strtotime($locatime[0]);
            $date = strtotime(date($args['date']));
            $diff = $date - $today;
            return ($diff == 0)? 1 : 0;
        }else{
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
     * check_include_exclude_category
     * @since 1.2.0
     * @param array
     * @return int
     * 
     */
    public function check_include_exclude_category($rule = [])
    {   
        if(empty($rule))
            return;

        //get products inside cart
        $items = $this->get_cart_items('items');
        
        if(empty($items))
            return;

        $cat_inside = [];
        foreach($items as $item) { 
            $post_categories = get_the_terms( $item['data']->get_id(), 'product_cat' ); 
            foreach($post_categories as $cat){
                $cat_inside[] = $cat->term_id;
            }     
        } 

        $in_count = 0;
        foreach($rule['rule'] as $rul){
            //extract ID
            $term_data = explode('-',$rul);
            
            if(!is_numeric($term_data[0]))
                continue;

            if($rule['type'] == 'include_category'){
                if(in_array($term_data[0],$cat_inside)){
                    $in_count++;
                }
            }else{
                if(!in_array($term_data[0],$cat_inside)){
                    $in_count++;
                }
            }
            
        }
        if(count($rule['rule']) == $in_count){
            return 1;
        }else{
            return 0;
        }
        
    }


    /**
     * 
     * check_had_purchased
     * @since 1.2.0
     * @param array
     * @return int
     *  
     */
     public function check_had_purchased($rule = [])
     {
        if(empty($rule))
            return;

        $purchased = $this->get_purchased();
        $is_purchased = 0;
        foreach($rule as $rul){
            //get id
            $cat_id = explode('-', $rul);
            if(in_array($cat_id[0],$purchased)){
                $is_purchased++;
            }
        }

        if(count($rule) == $is_purchased){
            return 1;
        }else{
            return 0;
        }
        
     }

    /**
     * 
     * get_purchased
     * @since 1.2.0
     * @param 
     * @return array
     * 
     */

    public function get_purchased($type = null)
    {
        $customer_orders = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => 'shop_order',
            'post_status' => 'wc-completed'
        ) );
        $data = [];
        foreach ( $customer_orders as $customer_order ) {
            $order = wc_get_order( $customer_order );
            if($type){
                $data[] = $order;
            }else{
                foreach ($order->get_items() as $item) {
                    if ( version_compare( WC_VERSION, '3.0', '<' ) ){
                        $data[] = $item['product_id'];
                    }else{
                        $data[] = $item->get_product_id();
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 
     * check_nth_orders
     * @since 1.2.0
     * @param array
     * @return int
     * 
     */
    public function check_prev_orders($rule = [])
    {
        if(empty($rule))
            return;
        
        $orders = $this->get_purchased('orders');
        switch($rule['condition']){
            case 'less_than_equal':
                return (count($orders) <= $rule['value'])? 1 : 0;
                break;
            case 'greater_than_equal':
                return (count($orders) >= $rule['value'])? 1 : 0;             
                break;
            case 'equal':
                return (count($orders) == $rule['value'])? 1 : 0;  
                break;
        }
        
    }

    /**
     * 
     * check_meta_value
     * @since 1.2.0
     * @param array
     * @return int
     * 
     */

    public function check_meta_values($rules = [])
    {
        if(empty($rules))
            return;

        $student_id = get_current_user_id();

        $num_has = 0;
        foreach($rules as $rule){
            if(get_user_meta($student_id, $rule['key'], true) == $rule['value']){
                $num_has++;
            }
        }

        if($num_has == count($rules)){
            return 1;
        }

        return 0;
    }

    /**
     * 
     * check_role
     * @since 1.2.0
     * @param array
     * @return int
     * 
     */

    public function check_role($rules)
    {
        if(empty($rules))
            return;
        
        $user_meta = get_userdata(get_current_user_id());
        $user_roles = $user_meta->roles;

        $has_role = 0;
        foreach($user_roles as $role){
            if(in_array($role, $rules))
                $has_role++;
        }

        if($has_role == count($rules))
            return 1;
        else
            return 0;
          
    }  

    /**
     * 
     * check_param
     * @since 1.2.0
     * @param array
     * @return int
     * 
     */
    public function check_param($rules)
    {
        if(empty($rules))
            return;

        $get_params = array_map( 'sanitize_text_field', $_GET );
        
        $has_param = 0;
        foreach($get_params as $key => $param){
            foreach($rules as $rule){
                if($rule['key'] == $key && $param == $rule['value']){
                    $has_param++;
                }
            }
        }

        if($has_param == count($rules))
            return 1;
        else
            return 0;
    }

    /**
     * 
     * load_notification
     * @since 1.2.0
     * @param
     * @return
     * 
     */
    public function load_notification()
    {
        if(!is_cart())
            return;

        if($this->notification){

            if(!$this->notification['enabled'])
                return;

            if(empty($this->applied_codes))
                return;

            //load css
            add_action('wp_enqueue_scripts',function(){
                wp_enqueue_style(CODECORUN_CDR_PREFIX.'-public-assets', CODECORUN_CDR_URL.'assets/codecorun.css');
                wp_enqueue_script(CODECORUN_CDR_PREFIX.'-public-assets-js', CODECORUN_CDR_URL.'assets/codecorun.js');
            });

            //load template
            add_action('wp_footer', function(){
                $this->set_template('notification', ['settings' => $this->notification]);
            });
        }

    }

    /**
     * 
     * applied_codes
     * @since 1.2.0
     * @param
     * @return
     * 
     */

    public function applied_codes()
    {
        if(empty($this->applied_codes))
            return;
        
        $applied_codes = apply_filters('codecorun_wcdr_shortcode_applied_codes',$this->applied_codes);
        $codes = '<ul>';
        ob_start();
        foreach($applied_codes as $code){
            $c = new \WC_Coupon($code);
            $details = get_post($c->id);
            $codes .= '<li>'.$details->post_excerpt.'</li>';
        }
        $codes .= '</ul>';
        $codes = apply_filters('codecorun_wcdr_shortcode_applied_codes_html',$codes);
        echo $codes;
        return ob_get_clean();
    }

    /**
     * 
     * custom_css
     * @since 1.2.0
     * @param
     * @return
     * 
     */

    public function custom_css()
    {
        if(empty($this->notification))
            return;

        if($this->notification['css']){
            wp_register_style( CODECORUN_CDR_PREFIX.'-noti-custom', false );
            wp_enqueue_style( CODECORUN_CDR_PREFIX.'-noti-custom' );
            wp_add_inline_style(CODECORUN_CDR_PREFIX.'-noti-custom', $this->notification['css']);
        }
        
    }

}

?>