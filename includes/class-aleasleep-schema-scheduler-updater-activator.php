<?php

/**
 * Fired during plugin activation
 *
 * @link       https://aleasleep.com
 * @since      1.0.0
 *
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/includes
 * @author     Jason Behik <jason.e.behik@gmail.com>
 */
class AleaSleep_Schema_Scheduler_Updater_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_filter( 'cron_schedules', function( $schedules ) {
			$schedules[ 'weekly' ] = array( 
			'interval' => 60 * 60 * 24 * 7, # 604,800, seconds in a week
			'display' => __( 'Weekly' ) );
			return $schedules;
		} );
		if( !wp_next_scheduled( 'aleasleep_update_product_reviews' ) ) {  
		   wp_schedule_event( time(), 'weekly', 'aleasleep_update_product_reviews' );  
		}
	}

}
