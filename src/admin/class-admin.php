<?php
/**
 * Admin handler.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Admin
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Admin;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

class Admin {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Bind hooks
	 */
	private function setup() {
		// called after pp_loaded.
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_notices', array( $this, 'notices' ) );
	}

	/**
	 * Initialize.
	 */
	public function init() {

		if ( ! bp_is_active( 'xprofile' ) ) {
			$notices = get_option( 'bpxcftr_notices' , array());

			$notices[] = __( 'BuddyPress Xprofile Custom Field Types plugin needs Buddypress Xprofile Component. Please enable Xprofile first.', 'buddypress-xprofile-custom-fields-types' );
			update_option( 'bpxcftr_notices', $notices );
		}
	}

	/**
	 * Show notices.
	 */
	public function notices() {
		$notices = get_option( 'bpxcftr_notices' );

		if ( $notices ) {
			foreach ( $notices as $notice ) {
				$notice = wp_kses_data( $notice );
				$notice = wpautop( $notice );

				echo "<div class='error'>{$notice}</div>";
			}
			delete_option( 'bpxcftr_notices' );
		}
	}

}