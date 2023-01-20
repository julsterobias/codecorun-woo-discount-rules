<?php
/**
 * 
 * wcdr-admin-class
 * @version 1.2.0
 * @author codecorun
 * 
 */
namespace codecorun\cdr\admin;
use codecorun\cdr\common\codecorun_cdr_common_class;

defined( 'ABSPATH' ) or die( 'No access area' );

class codecorun_cdr_admin_class extends codecorun_cdr_common_class
{

    /**
     * 
     * constructor
     * @since 1.0
     * @param
     * @return
     * 
     */
    public function __construct()
    {
        add_filter( 'woocommerce_coupon_data_tabs', [$this,'rules_tab'], 10 , 1 );
        add_filter('woocommerce_coupon_data_panels', [$this,'rules_html'], 10, 1);
        add_action('admin_enqueue_scripts',[$this, 'assets']);
        add_action('wp_ajax_wcdr_product_list_options',[$this,'get_woo_products']);
        add_action('wp_ajax_wcdr_category_list_options',[$this,'get_woo_category']);
        add_action('wp_ajax_wcdr_role_list_options',[$this,'get_wp_roles']);
        //update the post
        add_action('save_post_shop_coupon', [$this,'save_coupon']);
        
        add_action('admin_menu',[$this, 'add_to_menu']);

        //save notification settings
        add_action('wp_ajax_wcdr_save_settings', [$this, 'wcdr_save_settings']);
    }

    /**
     * 
     * assets
     * @since 1.0
     * @param
     * @return
     * 
     */
    public function assets()
    {   
        //codemirror
        if(isset($_GET['page'])){
            $page = sanitize_text_field( $_GET['page'] );
            if( $page == 'codecorun-wcdr' ){
                wp_enqueue_style(CODECORUN_CDR_PREFIX.'-codemirror-style', CODECORUN_CDR_URL.'admin/assets/codemirror.css');
                wp_register_script( CODECORUN_CDR_PREFIX.'-codemirror-js', CODECORUN_CDR_URL.'admin/assets/codemirror.js', array('jquery') );
                wp_enqueue_script( CODECORUN_CDR_PREFIX.'-codemirror-js' );
                wp_enqueue_script(CODECORUN_CDR_PREFIX.'-wcdr-setting', CODECORUN_CDR_URL.'admin/assets/settings.js');
            }
        }

        //load css
        wp_enqueue_style(CODECORUN_CDR_PREFIX.'-admin-assets', CODECORUN_CDR_URL.'admin/assets/admin.css');
        //load js
        wp_register_script( CODECORUN_CDR_PREFIX.'-admin-assets-js', CODECORUN_CDR_URL.'admin/assets/admin.js', array('jquery') );
        wp_enqueue_script( CODECORUN_CDR_PREFIX.'-admin-assets-js' );
        wp_localize_script( CODECORUN_CDR_PREFIX.'-admin-assets-js', 'wcdrAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ))); 
        //render translatable labels
        wp_localize_script( CODECORUN_CDR_PREFIX.'-admin-assets-js', 'wcdr_label_factory', $this->translatable_text());   

        if( is_plugin_active( CODECORUN_CDR_PRO_ID ) ){
            wp_localize_script( CODECORUN_CDR_PREFIX.'-admin-assets-js', 'codecorun_is_upgraded', true );  
        }else{
            wp_localize_script( CODECORUN_CDR_PREFIX.'-admin-assets-js', 'codecorun_is_upgraded', null );
        }

        
    }

    /**
     * 
     * rules_tab
     * @since 1.0
     * @param array
     * @return array
     * 
     */
    public function rules_tab( $product_data_tabs ) {
        $product_data_tabs['wcdr-discount-rules'] = [
            'label' => __('Discount Rules'),
            'target' => 'wcdr_discount_rules',
            'class' => 'wcdr_discount_rules'
        ];
        return $product_data_tabs;
    }

    /**
     * 
     * rules_html
     * @since 1.0
     * @param
     * @return
     * 
     */
    public function rules_html()
    {
        global $post;
        $get_rules = apply_filters('wcdr_saved_rules',get_post_meta($post->ID,'wcdr-coupon-rules',true));
        $rules = $this->rules();
        $this->set_template('panel-html',['other' => 'admin', 'rules' => $rules, 'save_rules' => json_encode($get_rules)]);
    }

    /**
     * 
     * get_woo_products
     * @since 1.0
     * @param
     * @return json
     * 
     */
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

    /**
     * 
     * get_woo_category
     * @since 1.2.0 Pro
     * @param
     * @return json
     * 
     */
    public function get_woo_category()
    {
       
        if ( ! wp_verify_nonce( $_GET['nonce'], 'wcdr-nonce-admin' ) ) {
            //do not echo anything will scare the cat
            exit();
        }
        $search = sanitize_text_field($_GET['search']);

        $args = [
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'name__like' => $search
        ];

        $args = apply_filters('wcdr_category_search_args', $args);

        $results = get_terms($args);
        $res = [];
        if($results){
            foreach($results as $result){
                $res[] = [
                    'id' => $result->term_id.'-'.$result->name,
                    'text' => $result->term_id.'-'.$result->name
                ];
            }
        }
        
        echo json_encode($res);
        exit;
        
    }

    /**
     * 
     * get_wp_roles
     * @since 1.2.0
     * @param
     * @return json
     */
    public function get_wp_roles()
    {
        if ( ! wp_verify_nonce( $_GET['nonce'], 'wcdr-nonce-admin' ) ) {
            //do not echo anything will scare the cat
            exit();
        }
        
        //not usable
        //$search = sanitize_text_field($_GET['search']);

        $editable_roles = get_editable_roles();
       
        $res = [];
        if($editable_roles){
            foreach($editable_roles as $role => $details){
                $res[] = [
                    'id' => $role,
                    'text' => $details['name']
                ];
            }
        }

        $res = apply_filters('wcdr_roles_search_args_after', $res);
        
        echo json_encode($res);
        exit;
    }

    /**
     * 
     * save_coupon
     * @since 1.0
     * @param int
     * @return
     * 
     */
    public function save_coupon($post_id){
        // I will resume here.
        // Save the data in serialize array format
        // Save the data via post_meta
        //wcdr_field

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;


        $coupon_rules = (isset($_POST['wcdr_field']))? $_POST['wcdr_field'] : null;

        if( !( $coupon_rules ) ){
            delete_post_meta($post_id,'wcdr-coupon-rules');
            return;
        }

        //let's sanitize
        foreach($coupon_rules as $index => $rules){
            if(!is_array($rules)){
                $coupon_rules[$index] = sanitize_text_field($rules);
            }else{
                //reloop
                //check index and do something for meta & params
                foreach($rules as $i => $rule){
                    $coupon_rules[$index][$i] = sanitize_text_field($rule);
                }
            }
        }

        //reloop and check for index to reformat their data structure
        foreach($coupon_rules as $index => $rules){
            $what_index = explode('-', $index);
            if($what_index[0] == 'metas' || $what_index[0] == 'url_param'){
                //do reformatting
                $prev_key = null;
                $new_format_ = [];
                foreach($rules as $i => $rule){
                    if($i % 2 == 0){
                        $prev_key = $rule;
                    }else{
                        $meta_param = [
                            'key' => sanitize_text_field( $prev_key ),
                            'value' => sanitize_text_field( $rule )
                        ];
                        $new_format_[] = $meta_param;
                        $meta_param = null;
                        $prev_key = null;
                    }
                }
                $coupon_rules[$index] =  $new_format_;
            } 
        }

        //save to post meta
        if(!empty($coupon_rules)){
            update_post_meta($post_id,'wcdr-coupon-rules',$coupon_rules);
        }

    }

    /**
     * 
     * add_to_menu
     * @since 1.2.0
     * @param
     * @return
     * 
     */
    public function add_to_menu()
    {
        add_submenu_page(
            'woocommerce-marketing',
            __( 'Discount Rules Notification', 'codecorun-coupon-discount-rules' ),
            __( 'Discount Rules Notification', 'codecorun-coupon-discount-rules' ),
            'manage_options',
            'codecorun-wcdr',
            [$this,'codecorun_marketing']
        );
    }

    /**
     * 
     * codecorun_marketing
     * @since 1.2.0
     * @param
     * @return
     * 
     */
    public function codecorun_marketing()
    {
        
        $noti_settings = get_option('codecorun_wcdr_noti_settings');
        if(!$noti_settings){
            $noti_settings = [
                'enabled' => null,
                'message' => null,
                'pos' => null,
                'css' => null
            ];
        }
        $this->set_template('panel-marketing',['other' => 'admin', 'settings' => $noti_settings]);
    }

    /**
     * 
     * wcdr_save_settings
     * @since 1.2.0
     * @param
     * @return
     * 
     */
    public function wcdr_save_settings()
    {

        if ( ! wp_verify_nonce( $_POST['transkey'], 'wcdr-nonce-admin' ) ) {
            //do not echo anything will scare the cat
            exit();
        }

        $to_save = [
            'enabled' => ($_POST['enabled'] == 'true')? true : false,
            'type' => sanitize_text_field($_POST['type']),
            'message' => wp_kses_post($_POST['message']),
            'pos' => sanitize_text_field($_POST['pos']),
            'css' => sanitize_textarea_field($_POST['css'])
        ];
        
        update_option('codecorun_wcdr_noti_settings', $to_save);
        exit();

    }

}
?>