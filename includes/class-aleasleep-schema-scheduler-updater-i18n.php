<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://aleasleep.com
 * @since      1.0.0
 *
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    AleaSleep_Schema_Scheduler_Updater
 * @subpackage AleaSleep_Schema_Scheduler_Updater/includes
 * @author     Jason Behik <jason.e.behik@gmail.com>
 */
class AleaSleep_Schema_Scheduler_Updater_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'aleasleep-schema-scheduler-updater',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
