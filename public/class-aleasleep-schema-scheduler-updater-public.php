<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://aleasleep.com
 * @since      1.0.0
 *
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/public
 * @author     Jason Behik <jason.e.behik@gmail.com>
 */
class AleaSleep_Schema_Scheduler_Updater_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
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
		 * defined in AleaSleep_Schema_Scheduler_Updater_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The AleaSleep_Schema_Scheduler_Updater_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aleasleep-schema-scheduler-updater-public.css', array(), $this->version, 'all' );

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
		 * defined in AleaSleep_Schema_Scheduler_Updater_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The AleaSleep_Schema_Scheduler_Updater_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aleasleep-schema-scheduler-updater-public.js', array( 'jquery' ), $this->version, false );

	}
	
	/**
	 * Initiates/creates the static file for our product reviews if it doesn't exist yet
	 */
	public function initiate_product_reviews_static_data() {
		$path = ABSPATH . 'wp-content/product-reviews/aleasleep_product_reviews.txt';
		$contents = @file_get_contents( $path );
		// if file doesn't exist
		if ( $contents === false ) {
			do_action( 'aleasleep_update_product_reviews' );
		}
	}


	/**
	 * Change/Add the AggregateRating schema
	 */
	public function add_aggregate_rating($markup_product) {
		global $product;
		$path = ABSPATH . 'wp-content/product-reviews/aleasleep_product_reviews.txt';
		$contents = @file_get_contents( $path );
		if ( $contents ) {
			$contents = json_decode( $contents, 1 );
			if ($contents) {
				$id = $product->get_id();
				// custom, 3922 and 44 are the same product but are US and CA version respectively.
				if ($id == 3922) {
					$id = 44;
				}
				$product_review = $contents[strval($id)];
				$markup_product[ 'aggregateRating' ] = array(
					'@type'       => 'AggregateRating',
					'ratingValue' => $product_review['rating'],
					'reviewCount' => $product_review['review_count'],
				);
			}
		}
		return $markup_product;
	}

}
