<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.codefish.com.eg
 * @since      1.0.0
 *
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/admin
 * @author     Your Name <email@example.com>
 */


 class coupons_shortcodes_codefish_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $coupons_shortcodes_codefish    The ID of this plugin.
	 */
	private $coupons_shortcodes_codefish;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $coupons_shortcodes_codefish       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $coupons_shortcodes_codefish, $version ) {

		$this->coupons_shortcodes_codefish = $coupons_shortcodes_codefish;
		$this->version = $version;



	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in coupons_shortcodes_codefish_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The coupons_shortcodes_codefish_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->coupons_shortcodes_codefish, plugin_dir_url( __FILE__ ) . 'css/coupons_shortcodes_codefish-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in coupons_shortcodes_codefish_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The coupons_shortcodes_codefish_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->coupons_shortcodes_codefish, plugin_dir_url( __FILE__ ) . 'js/coupons_shortcodes_codefish-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function add_coupon_text_field() {
		

		woocommerce_wp_select( array(
			'id'                => '_adjust_subscriptions_enddate',
			'label'             => __( 'Adjust subscriptions End Date', 'woocommerce' ),
			'description'       => __( 'Adjust subscriptions End Date', 'woocommerce' ),
			'desc_tip'    => true,
			'options' => array(
				'subscriptions_end_of_current_month'   => __( 'End Of Current Month', 'woocommerce' ),
				'subscriptions_use_product_end_date'   => __( 'Use Product End Date', 'woocommerce' ),
				'subscriptions_use_custom_date' => __( 'Custom End Date', 'woocommerce' )
				)
	
		) );

		woocommerce_wp_text_input( array(
			'id'                => '_custom_subscriptions_date',
			'label'             => __( 'Custom subscriptions End Date', 'woocommerce' ),
			'placeholder' => '', 
			'description'       => __( 'Custom Subscriptions  End Date', 'woocommerce' ),
			'desc_tip'    => true,
			'type' => 'date',
	
		) );


	}

	public function save_coupon_text_field( $post_id) {
		$adjust_subscriptions_enddate = $_POST['_adjust_subscriptions_enddate'];
		if( !empty( $adjust_subscriptions_enddate ) )
			update_post_meta( $post_id, '_adjust_subscriptions_enddate', esc_attr( $adjust_subscriptions_enddate ) );
		$custom_subscriptions_date = $_POST['_custom_subscriptions_date'];
			if( !empty( $custom_subscriptions_date ) )
				update_post_meta( $post_id, '_custom_subscriptions_date', esc_attr( $custom_subscriptions_date) );
		
	}

	public static function smart_coupon_export_headers( $headers ) {
		$headers['_adjust_subscriptions_enddate']  = __( 'Sub Data', 'wc_sub_data_coupons' );
		$headers['_custom_subscriptions_date'] = __( 'Sub Data', 'wc_sub_data_coupons' );
		return $headers;
	}
	/**
	 * Include FGC data when using Smart Coupons export.
	 *
	 * @param  array     $headers
	 * @return array
	 */
	public static function postmeta_defaults( $headers ) {
		$headers['_adjust_subscriptions_enddate']  = '';
		$headers['_custom_subscriptions_date'] = '';
		return $headers;
	}

	public static function coupon_meta( $data, $post  ) {

		if ( isset( $post['discount_type'] ) && $post['discount_type'] === 'free_gift' ) {
			$data['_adjust_subscriptions_enddate']          = $post['_adjust_subscriptions_enddate'] ;
			$data['_custom_subscriptions_date'] =  $post['_custom_subscriptions_date'] ;
		}

		return $data;
	}

	public static function new_coupon_meta( $args = array() ) {

		if ( ! empty( $args['new_coupon_id'] ) && ! empty( $args['ref_coupon'] ) ) {
			$prev_wc_sub_coupon_data          = ( is_object( $args['ref_coupon'] ) && is_callable( array( $args['ref_coupon'], 'get_meta' ) ) ) ? (array) $args['ref_coupon']->get_meta( '_adjust_subscriptions_enddate' ) : 'subscriptions_end_of_current_month' ;
			$prev_wc_sub_coupon_free_shipping = ( is_object( $args['ref_coupon'] ) && is_callable( array( $args['ref_coupon'], 'get_meta' ) ) ) ? (array) $args['ref_coupon']->get_meta( '_custom_subscriptions_date' ) : '';
			
			update_post_meta( $args['new_coupon_id'], '_adjust_subscriptions_enddate', $prev_wc_sub_coupon_data );
			update_post_meta( $args['new_coupon_id'], '_custom_subscriptions_date', $prev_wc_sub_coupon_free_shipping );
		}

	}

	public static function is_auto_generate( $is_auto_generate = false, $args = array() ) {

		$coupon = ( ! empty( $args['coupon_obj'] ) ) ? $args['coupon_obj'] : null;

		if ( is_a( $coupon, 'WC_Coupon' ) && $coupon->is_type( 'free_gift' ) ) {
			$is_coupon_auto_generate = ( ! empty( $args['auto_generate'] ) ) ? $args['auto_generate'] : 'no';
			if ( 'yes' === $is_coupon_auto_generate ) {
				return true;
			}
		}
		apply_filters( 'woocommerce_coupon_code_generator_characters', '23456789' );
		return $is_auto_generate;
	}
}
