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

// Do not allow direct access over web.
use BPXProfileCFTR\Field_Types\Field_Type_Country;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility helper for BP Profile Search.
 */
class BP_Profile_Search_Helper {

	/**
	 * Sets up the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Sets up hooks.
	 */
	public function setup() {
		add_action( 'bps_custom_field', array( $this, 'register_field_types' ) );
		add_action( 'bp_ps_custom_field', array( $this, 'register_field_types' ) );
	}

	/**
	 * Registers field types to Bp profile Search.
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

			case 'country':
				$field->format  = 'text';
				$field->options = Field_Type_Country::get_countries();
				break;
		}
	}
}
