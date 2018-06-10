<?php
/**
 * BP Profile Search Helper
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.3
 */

namespace BPXProfileCFTR\Filters;


// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Compatibility helper for BP Profile Search.
 */
class BP_Profile_Search_Helper {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	public function setup() {
		add_action( 'bps_custom_field', array( $this, 'register_field_types' ) );
	}

	/**
	 * Register field types to Bp profile Search.
	 *
	 * @param \stdClass $field object.
	 */
	public function register_field_types( $field ) {

		$field_types = bpxcftr_get_field_types();

		if ( ! isset( $field_types[ $field->type ] ) ) {
			return;
		}

		switch ( $field->type ) {

			case 'birthdate':
			case 'datepicker':
				$field->format = 'date';
				break;

			case 'color':
			case 'email':
			case 'web':
				$field->format = 'text';
				break;

			case 'number_minmax':
			case 'slider':
				$field->format = 'integer';
				break;

			case 'decimal_number':
				$field->format = 'decimal';
				break;
		}
	}
}
