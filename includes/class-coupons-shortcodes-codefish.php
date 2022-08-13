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
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/includes
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
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/includes
 * @author     Your Name <email@example.com>
 */
class coupons_shortcodes_codefish {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      coupons_shortcodes_codefish_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $coupons_shortcodes_codefish    The string used to uniquely identify this plugin.
	 */
	protected $coupons_shortcodes_codefish;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'coupons_shortcodes_codefish_VERSION' ) ) {
			$this->version = coupons_shortcodes_codefish_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->coupons_shortcodes_codefish = 'coupons_shortcodes_codefish';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - coupons_shortcodes_codefish_Loader. Orchestrates the hooks of the plugin.
	 * - coupons_shortcodes_codefish_i18n. Defines internationalization functionality.
	 * - coupons_shortcodes_codefish_Admin. Defines all hooks for the admin area.
	 * - coupons_shortcodes_codefish_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coupons-shortcodes-codefish-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coupons-shortcodes-codefish-i18n.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coupons-shortcodes-codefish-subsribe.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-coupons-shortcodes-codefish-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-coupons-shortcodes-codefish-public.php';


		

		$this->loader = new coupons_shortcodes_codefish_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the coupons_shortcodes_codefish_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new coupons_shortcodes_codefish_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new coupons_shortcodes_codefish_Admin( $this->get_coupons_shortcodes_codefish(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_coupon_options', $plugin_admin, 'add_coupon_text_field' );
		$this->loader->add_action( 'woocommerce_coupon_options_save', $plugin_admin, 'save_coupon_text_field' );
		// Smart Coupons export headers.
		$this->loader->add_filter( 'wc_smart_coupons_export_headers', $plugin_admin,'smart_coupon_export_headers' );

		// Smart Coupons import meta fields.
		$this->loader->add_filter( 'smart_coupons_parser_postmeta_defaults', $plugin_admin, 'postmeta_defaults' ) ;	

		// Include FGC meta in Smart Coupons bulk generation.
		$this->loader->add_filter( 'sc_generate_coupon_meta',$plugin_admin, 'coupon_meta' , 10, 2 );

		// Include FGC meta in Smart Coupons auto-generation.
		$this->loader->add_action( 'wc_sc_new_coupon_generated', $plugin_admin, 'new_coupon_meta'  );

		// Include FGC meta in Smart Coupons auto-generation.
		$this->loader->add_filter( 'wc_sc_is_auto_generate',$plugin_admin, 'is_auto_generate' , 10, 2 );

		apply_filters( 'woocommerce_coupon_code_generator_characters', '23456789' );
		


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new coupons_shortcodes_codefish_Public( $this->get_coupons_shortcodes_codefish(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_shortcode('copoun_shortcode', $plugin_public, 'display_copoun_shortcode');
		
		/**$this->loader->add_shortcode('mycours_shortcode', $plugin_public, 'display_mycourses_shortcode');*/


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_coupons_shortcodes_codefish() {
		return $this->coupons_shortcodes_codefish;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    coupons_shortcodes_codefish_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
