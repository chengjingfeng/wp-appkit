<?php

/*
  Plugin Name: WP App Kit
  Description: Build Phonegap Mobile apps based on your WordPress contents
  Version: 0.2
 */

if ( !class_exists( 'WpAppKit' ) ) {

	class WpAppKit {

		const resources_version = '0.4';
		const i18n_domain = 'wp-app-kit';

		public static function hooks() {
			add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );

			register_activation_hook( __FILE__, array( __CLASS__, 'on_activation' ) );
			register_deactivation_hook( __FILE__, array( __CLASS__, 'on_deactivation' ) );

			add_action( 'init', array( __CLASS__, 'init' ) );
			add_action( 'template_redirect', array( __CLASS__, 'template_redirect' ), 5 );
		}

		protected static function lib_require() {
			require_once(dirname( __FILE__ ) . '/lib/addons/addons.php');
			require_once(dirname( __FILE__ ) . '/lib/web-services/web-services.php');
			require_once(dirname( __FILE__ ) . '/lib/apps/apps.php');
			require_once(dirname( __FILE__ ) . '/lib/apps/build.php');
			require_once(dirname( __FILE__ ) . '/lib/themes/themes.php');
			require_once(dirname( __FILE__ ) . '/lib/themes/upload-themes.php');
			require_once(dirname( __FILE__ ) . '/lib/user-permissions/user-permissions.php');
			require_once(dirname( __FILE__ ) . '/lib/settings/settings.php');
			require_once(dirname( __FILE__ ) . '/lib/components/components.php');
			require_once(dirname( __FILE__ ) . '/lib/navigation/navigation.php');
			require_once(dirname( __FILE__ ) . '/lib/options/options.php');
			require_once(dirname( __FILE__ ) . '/lib/simulator/simulator.php');
		}

		public static function plugins_loaded() {
			load_plugin_textdomain( self::i18n_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
			self::lib_require();
		}

		public static function on_activation() {
			self::lib_require();
			WpakWebServices::add_rewrite_tags_and_rules();
			WpakConfigFile::rewrite_rules();
			WpakThemes::rewrite_rules();
			flush_rewrite_rules();
			
			WpakThemes::create_theme_directory();
		}

		public static function on_deactivation() {
			flush_rewrite_rules();
		}

		public static function init() {
			WpakWebServices::add_rewrite_tags_and_rules();
			
			//Handle specific mobile images sizes :
			$mobile_images_sizes_default = array(
				//Example : array( 'name' => 'mobile-featured-thumb', 'width' => 327, 'height' => 218 )
			);

			/**
			 * Use this 'wpak_mobile_images_sizes' filter to add custom mobile images sizes
			 */
			$mobile_images_sizes = apply_filters( 'wpak_mobile_images_sizes', $mobile_images_sizes_default );
			if ( !empty( $mobile_images_sizes ) ) {
				foreach ( $mobile_images_sizes as $image_size ) {
					add_image_size( $image_size['name'], $image_size['width'], $image_size['height'] );
				}
			}
		}

		public static function template_redirect() {
			WpakWebServices::template_redirect();
		}

	}

	WpAppKit::hooks();
}