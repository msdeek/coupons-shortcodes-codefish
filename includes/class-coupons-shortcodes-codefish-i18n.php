<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.codefish.com.eg
 * @since      1.0.0
 *
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    coupons_shortcodes_codefish
 * @subpackage coupons_shortcodes_codefish/includes
 * @author     Your Name <email@example.com>
 */
class coupons_shortcodes_codefish_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'coupons_shortcodes_codefish',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
