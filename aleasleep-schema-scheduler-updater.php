<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://aleasleep.com
 * @since             1.0.0
 * @package           AleaSleep_Schema_Scheduler_Updater
 *
 * @wordpress-plugin
 * Plugin Name:       Schema Scheduler and Updater
 * Plugin URI:        https://aleasleep.com
 * Description:       This plugin gets the product reviews from Yotpo ever week and updates the static storage file. This plugin also adds/replaces the product's AggregateRating schema.
 * Version:           1.0.0
 * Author:            Jason Behik
 * Author URI:        https://aleasleep.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aleasleep-schema-scheduler-updater
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aleasleep-schema-scheduler-updater-activator.php
 */
function activate_AleaSleep_Schema_Scheduler_Updater() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aleasleep-schema-scheduler-updater-activator.php';
	AleaSleep_Schema_Scheduler_Updater_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aleasleep-schema-scheduler-updater-deactivator.php
 */
function deactivate_AleaSleep_Schema_Scheduler_Updater() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aleasleep-schema-scheduler-updater-deactivator.php';
	AleaSleep_Schema_Scheduler_Updater_Deactivator::deactivate();
}

add_action( 'aleasleep_update_product_reviews', 'AleaSleep_Schema_Scheduler_get_trustspot_schema' );

/**
 * Get TrustSpot schema through their Product Review API
 */
function AleaSleep_Schema_Scheduler_get_trustspot_schema() {
	$yotpo_default = [
		'app_key' => '',
        'secret' => '',
        'widget_location' => 'footer',
        'language_code' => 'en',
        'widget_tab_name' => 'Reviews',
        'bottom_line_enabled_product' => true,
        'bottom_line_enabled_category' => false,
        'yotpo_language_as_site' => true,
        'show_submit_past_orders' => true,
        'yotpo_order_status' => 'wc-completed',
        'disable_native_review_system' => true,
        'native_star_ratings_enabled' => 'no'
	];
	$yotpo_settings = get_option('yotpo_settings', $yotpo_default);
	if ( !empty($yotpo_settings['app_key']) ) {
		$url = 'https://api.yotpo.com/v1/widget/' . $yotpo_settings['app_key'] . '/products/{product_id}/reviews.json';
		global $trustspot_options;
		$temp_product = get_page_by_path( $product, OBJECT, 'product' );
		if ( $trustspot_options ) {
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => -1
			);
			$products = wc_get_products( $args );
			$merchantId = $trustspot_options[ 'trustspot_trustspot_client_id' ];
			if ( $products ) {
				foreach ( $products as $key => $product ) {
					$review_url = str_replace('{product_id}', $product->get_id(), $url);
					$response = json_decode( AleaSleep_Schema_Scheduler_execute_trustspot_call( $review_url ) );
					if ( $response ) {
						AleaSleep_Schema_Scheduler_save_product_reviews( $product_id, $response );
					}
				}
			}
		}
	}
}

/**
 * Get the TrustSpot API response
 */
function AleaSleep_Schema_Scheduler_execute_trustspot_call( $url ) {
	$curl = curl_init( $url );
	curl_setopt($curl, CURLOPT_HTTPGET, true);
	curl_setopt($curl, CURLOPT_URL, $url);
    # Set some default CURL options
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Yotpo-Php');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
	$result = curl_exec( $curl );
	curl_close( $curl );
	if ( $result ) {
		return $result;
	} else {
		return 0;
	}
}

/**
 * Update the static file where our product reviews are saved.
 */ 
function AleaSleep_Schema_Scheduler_save_product_reviews( $product_id, $product_review_details ) {
	$path = ABSPATH . 'wp-content/product-reviews/aleasleep_product_reviews.txt';
	$contents = @file_get_contents( $path );
	// if exists
	if ( $contents !== false ) {
		$contents = json_decode( $contents, 1 );
	} else {
		$parts = explode ( '/', $path );
		array_pop( $parts );
		foreach( $parts as $part ) {
			if( !is_dir( $dir .= "/$part" ) ) {
				mkdir( $dir );
			}
		}
	}
	if ( !empty( $contents ) ) {
		$contents[$product_id] = [
			'product_id' => $product_id,
			'rating' => $product_review_details->response->bottomline->average_score,
			'review_count' => $product_review_details->response->bottomline->total_review
		];
	} else {
		$contents = [
			$product_id => [
				'sku' => $product_id,
				'rating' => $product_review_details->response->bottomline->average_score,
				'review_count' => $product_review_details->response->bottomline->total_review
			]
		];
	}
	$contents = json_encode( $contents );
	file_put_contents($path, $contents);
}

register_activation_hook( __FILE__, 'activate_AleaSleep_Schema_Scheduler_Updater' );
register_deactivation_hook( __FILE__, 'deactivate_AleaSleep_Schema_Scheduler_Updater' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aleasleep-schema-scheduler-updater.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_AleaSleep_Schema_Scheduler_Updater() {

	$plugin = new AleaSleep_Schema_Scheduler_Updater();
	$plugin->run();

}
run_AleaSleep_Schema_Scheduler_Updater();
