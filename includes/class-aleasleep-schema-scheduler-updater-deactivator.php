<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://aleasleep.com
 * @since      1.0.0
 *
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/includes
 * @author     Jason Behik <jason.e.behik@gmail.com>
 */
class AleaSleep_Schema_Scheduler_Updater_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// find out when the last event was scheduled
		$timestamp = wp_next_scheduled ('aleasleep_update_product_reviews');
		// unschedule previous event if any
		wp_unschedule_event ($timestamp, 'aleasleep_update_product_reviews');
	}

}
