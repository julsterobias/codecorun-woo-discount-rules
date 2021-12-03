<?php
/**
 * 
 * wcdr-admin-class
 * @version 1.0
 * 
 */
namespace wcdr\admin;
use wcdr\common\wcdr_common_class;

defined( 'ABSPATH' ) or die( 'No access area' );

class wcdr_admin_class extends wcdr_common_class
{

    public function __construct()
    {
        add_filter( 'woocommerce_coupon_data_tabs', [$this,'rules_tab'], 10 , 1 );
        add_filter('woocommerce_coupon_data_panels', [$this,'rules_html'], 10, 1);
        add_action('admin_enqueue_scripts',[$this, 'assets']);
        add_action('wp_ajax_wcdr_product_list_options',[$this,'get_woo_products']);
        //update the post
        add_action('save_post_shop_coupon', [$this,'save_coupon']);
    }

    public function assets()
    {   
        //load css
        wp_enqueue_style(WCDR_PREFIX.'-admin-assets', WCDR_API_URL.'admin/assets/admin.css');
        //load js
        wp_register_script( WCDR_PREFIX.'-admin-assets-js', WCDR_API_URL.'admin/assets/admin.js', array('jquery') );
        wp_enqueue_script( WCDR_PREFIX.'-admin-assets-js' );
        wp_localize_script( WCDR_PREFIX.'-admin-assets-js', 'wcdrAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
        //render translatable labels
        wp_localize_script( WCDR_PREFIX.'-admin-assets-js', 'wcdr_label_factory', $this->translatable_text());   
    }


    public function rules_tab( $product_data_tabs ) {
        $product_data_tabs['wcdr-discount-rules'] = [
            'label' => 'Discount Rules',
            'target' => 'wcdr_discount_rules',
            'class' => 'wcdr_discount_rules'
        ];
        return $product_data_tabs;
    }

    public function rules_html()
    {
        global $post;
        $get_rules = apply_filters('wcdr_saved_rules',get_post_meta($post->ID,'wcdr-coupon-rules',true));
        $rules = $this->rules();
        $this->set_template('panel-html',['other' => 'admin', 'rules' => $rules, 'save_rules' => json_encode($get_rules)]);
    }

    public function get_woo_products()
    {
       
        if ( ! wp_verify_nonce( $_GET['nonce'], 'wcdr-nonce-admin' ) ) {
            //do not echo anything will scare the cat
            exit();
        }
        $search = sanitize_text_field($_GET['search']);
        $args = [
            'posts_per_page' => -1,
            'post_type' => 'product',
            's' => $search,
            'post_status' => 'publish'
        ];

        $args = apply_filters('wcdr_search_args', $args);

        $results = get_posts($args);
        $res = [];
        if($results){
            foreach($results as $result){
                $res[] = [
                    'id' => $result->ID,
                    'text' => $result->post_title
                ];
            }
        }
        
        echo json_encode($res);
        exit;
        
    }

    public function save_coupon($post_id){
        // I will resume here.
        // Save the data in serialize array format
        // Save the data via post_meta
        //wcdr_field

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;


        $coupon_rules = $_POST['wcdr_field'];

        //let's sanitize
        foreach($coupon_rules as $index => $rules){
            if(!is_array($rules)){
                $coupon_rules[$index] = sanitize_text_field($rules);
            }else{
                //reloop
                foreach($rules as $i => $rule){
                    $coupon_rules[$index][$i] = sanitize_text_field($rule);
                }
            }
        }
        //save to post meta
        if(!empty($coupon_rules)){
            update_post_meta($post_id,'wcdr-coupon-rules',$coupon_rules);
        }else{
            delete_post_meta($post_id,'wcdr-coupon-rules');
        }

    }

}
?>