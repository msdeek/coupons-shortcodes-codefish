<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.codefish.com.eg
 * @since      1.0.0
 *
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/public
 * @author     codefish <info@codefish.com.eg>
 */
class coupons_shortcodes_codefish_Public {

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
	 * @param      string    $coupons_shortcodes_codefish       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $coupons_shortcodes_codefish, $version ) {

		$this->coupons_shortcodes_codefish = $coupons_shortcodes_codefish;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->coupons_shortcodes_codefish, plugin_dir_url( __FILE__ ) . 'css/coupons-shortcodes-codefish-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->coupons_shortcodes_codefish, plugin_dir_url( __FILE__ ) . 'js/coupons-shortcodes-codefish-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'qr-js', 'https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js', array( 'jquery' ), $this->version, false );


	}

	public function display_copoun_shortcode(){
		
		$placeholder = __('Insert&nbsp;Coupon&nbsp;Code', 'coupons_shortcodes_codefish');
		$usecode = __('Use&nbsp;Code', 'coupons_shortcodes_codefish');

		if( isset($_GET['coupon']) && isset($_GET['redeem-coupon']) ){
			$data = new Subscribe;
			if( $coupon = esc_attr($_GET['coupon']) ) {
				$data = $data->applay_coupon($coupon);
			}
			$success = sprintf( __('Please Wait, Your Order is processing', 'coupons_shortcodes_codefish'), $coupon );
			$error   = sprintf( __('Please Wait, Your account ractivating', 'coupons_shortcodes_codefish'), $coupon );
			$message = isset($applied) && $applied ? $success : $error;
		}
		


		$content = '';
		$content .= '<div style="display: flex;flex-wrap: wrap;flex-direction: row;align-content: center;align-items: baseline;">';
		$content .= '<div class="coupon">';
		$content .= '<form id="coupon-redeem">';
		$content .= '<p>';
		$content .= '<input type="text" name="coupon" id="coupon" placeholder='.$placeholder.' />';
		$content .= '<input class="button" type="submit" name="redeem-coupon" value="'.("$usecode").'" style="margin: 10px;"/>';
		$content .= '</p>';
		$output  = '


		
		<div class="coupon">
		
		<p>
		
		
		
		</p>';

			$output .= isset($coupon) ? '<p class="result">'.$message.'</p>' : '';

			return $content . '</form></div></div>';
	}


	
	/**
	public function display_mycourses_shortcode(){
		
		$template_loader = new \app\wisdmlabs\edwiserBridge\EbTemplateLoader(
			'Edwiser Bridge - WordPress Moodle LMS Integration','2.1.6'
		);
		
		
		$courses = new Subscribe;
		$user_id = get_current_user_id();
		$my_courses = $courses->get_user_enrolled_courses($user_id);
		foreach ($my_courses as $course){
		echo $course;
		}
		$args = array(
			'post_type'           => 'eb_course',
			'post_status'         => 'publish',
			'post__in'            => $my_courses,
			'ignore_sticky_posts' => true,
			'posts_per_page'      => -1,
		);

		$courses = new \WP_Query( $args );

		echo "<div class='eb-my-course eb_course_cards_wrap'>";
		if ( $courses->have_posts() ) {
			$course_progress_manager = new \app\wisdmlabs\edwiserBridge\Eb_Course_Progress();
			$progress_data           = $course_progress_manager->get_course_progress();
			$user_id                 = get_current_user_id();
			$mdl_uid                 = get_user_meta( $user_id, 'moodle_user_id', true );
			$atts['show_progress'] = true;
			while ( $courses->have_posts() ) :
				$courses->the_post();

				if ( $mdl_uid && isset( $atts['my_courses_progress'] ) && $atts['my_courses_progress'] ) {
					$course_prog_data         = $this->get_course_progress( get_the_ID(), $progress_data, $user_id, $atts, $mdl_uid );
					$atts['progress_btn_div'] = $course_prog_data['html'];
					$atts['completed']        = $course_prog_data['completed'];
				} else {
					$atts['progress_btn_div'] = '';
					$atts['completed']        = 0;
				}

				$template_loader->wp_get_template(
					'content-eb_course.php',
					array(
						'is_eb_my_courses' => true,
						'attr'             => $atts,
					)
				);
			endwhile;

		}
		echo "</div>";
	}*/

}