<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.codefish.com.eg
 * @since      1.0.0
 *
 * @package    code_moodle_learndash
 * @subpackage code_moodle_learndash/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    code_moodle_learndash
 * @subpackage code_moodle_learndash/includes
 * @author     codefish Team <info@codefish.com.eg>
 */

class Subscribe{
     /**
	 * Get Coupun Data
	 *
	 * @since    1.0.0
	 */
    public function get_coupon_data($coupon){
		$coupon_data = new WC_Coupon($coupon);
		return $coupon_data;
    }

	 /**
	 * Applay Coupuon
	 *
	 * @since    1.0.0
	 */
	public function applay_coupon($coupon){
		$coupon_all_data = $this->get_coupon_data($coupon);
		$coupon_data = $coupon_all_data->get_id();
		echo $coupon;
		$coupun_expiry_date = $coupon_all_data->get_date_expires();
		echo $coupun_expiry_date;
		$user = wp_get_current_user();
		
		$sub = $this->give_user_subscription($user, $coupon_data, $coupon, $coupun_expiry_date);

		

		return $sub;
	}

	/**
	 * Give User Subscribtion
	 *
	 * @since    1.0.0
	 */
	public function give_user_subscription($user, $coupon_data, $coupon, $coupun_expiry_date ){
		
		
		if( ! function_exists( 'wc_create_order' ) || ! function_exists( 'wcs_create_subscription' ) || ! class_exists( 'WC_Subscriptions_Product' )  ){
			return false;
		}


		
		
		$cart = WC()->cart;
		$cart->empty_cart();
		$cart->remove_coupons();
		$applied = $cart->apply_coupon($coupon);
		if ($cart->get_cart_contents_count() == 0){
			$cart->remove_coupons();
			$cart->empty_cart();
			$users_subscriptions = wcs_get_users_subscriptions($user->ID);
			
			$url = '/courses/?type=my-courses';
			echo "<script type=\"text/javascript\">window.location.replace('$url');</script>";
		}else{
		
			$checkout = WC()->checkout();

			$user_id = $user->ID;
			$checkout = WC()->checkout();
			$order_id = $checkout->create_order( array( 'customer_id' => $user_id ) );
			$order = wc_get_order($order_id);

			$end_date_method = get_post_meta($coupon_data, '_adjust_subscriptions_enddate', true);
			$custom_end_date = get_post_meta($coupon_data, '_custom_subscriptions_date', true);

			if( is_wp_error( $order ) ){
				return false;
			}

			$order->calculate_totals();
			$user = get_user_by( 'ID', $user_id );

			$fname     = $user->first_name;
			$lname     = $user->last_name;
			$email     = $user->user_email;
			$address_1 = get_user_meta( $user_id, 'billing_address_1', true );
			$address_2 = get_user_meta( $user_id, 'billing_address_2', true );
			$city      = get_user_meta( $user_id, 'billing_city', true );
			$postcode  = get_user_meta( $user_id, 'billing_postcode', true );
			$country   = get_user_meta( $user_id, 'billing_country', true );
			$state     = get_user_meta( $user_id, 'billing_state', true );

			$address         = array(
				'first_name' => $fname,
				'last_name'  => $lname,
				'email'      => $email,
				'address_1'  => $address_1,
				'address_2'  => $address_2,
				'city'       => $city,
				'state'      => $state,
				'postcode'   => $postcode,
				'country'    => $country,
				'billing_email' => $email,
			);

			$order->set_address( $address, 'billing' );
			$order->set_address( $address, 'shipping' );

			$order_items = $order->get_items();
			if (!empty($order_items)){
				foreach( $order->get_items() as  $item_id => $item  ) {
					$product = $item->get_product();

					$sub = wcs_create_subscription(array(
						'order_id' => $order->id,
						'status' => 'pending', // Status should be initially set to pending to match how normal checkout process goes
						'billing_period' => 'year',//WC_Subscriptions_Product::get_period( $product ),
						'billing_interval' => '1',//WC_Subscriptions_Product::get_interval( $product ),
						'customer_user' => $user_id
					));
					if( is_wp_error( $sub ) ){
						return false;
					}

					// Modeled after WC_Subscriptions_Cart::calculate_subscription_totals()
					$start_date = gmdate( 'Y-m-d H:i:s' );
					//$start_date = date("Y-m-d H:i:s", strtotime($custom_start_date)); 
					// Add product to subscription
					$sub->add_product( $product, 1 );
					if ( $end_date_method == "subscriptions_end_of_current_month") {
						$end_date = date("Y-m-d H:i:s", strtotime($coupun_expiry_date));
						//$end_date = date('Y-m-t H:i:s');
					} elseif ( $end_date_method == "subscriptions_use_product_end_date") {
						$end_date = WC_Subscriptions_Product::get_expiration_date( $product, $start_date );
					} elseif ( $end_date_method == "subscriptions_use_custom_date"){
						$end_date = date("Y-m-d H:i:s", strtotime($custom_end_date)); 
					}
					echo $end_date;
					$dates = array(
						'trial_end'    => WC_Subscriptions_Product::get_trial_expiration_date( $product, $start_date ),
						//'next_payment' => WC_Subscriptions_Product::get_first_renewal_payment_date( $product, $start_date ),
						'end'          => $end_date,
					);

					$sub->update_dates( $dates );
					$sub->calculate_totals();
					$note = ! empty( $note ) ? $note : __( $coupon);
					$sub->update_status( 'active');
				}
			}
			$order->update_status( 'completed', $note, true );
			$cart->empty_cart();
			$url = '/courses/?type=my-courses';
			echo "<script type=\"text/javascript\">window.location.replace('$url');</script>";

		}
	}

	function get_user_enrolled_courses( $user_id = null ) {
		global $wpdb;
		$user_id = ! is_numeric( $user_id ) ? get_current_user_id() : (int) $user_id;

		$result = $wpdb->get_results( $wpdb->prepare( "SELECT course_id FROM {$wpdb->prefix}moodle_enrollment WHERE user_id=%d;", $user_id ) ); // @codingStandardsIgnoreLine
		$courses = array();
		foreach ( $result as $key => $course ) {
			$courses[] = $course->course_id;
		}

		return $courses;
	}
}