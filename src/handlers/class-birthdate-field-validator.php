<?php
/**
 * Birthdate Field Validator
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Handlers;

use BPXProfileCFTR\Field_Types\Field_Type_Birthdate;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Manage and sync field data.
 */
class Birthdate_Field_Validator {

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
		add_action( 'xprofile_data_before_save', array( $this, 'on_field_data_save' ) );
	}

	/**
	 * Validate on field save.
	 *
	 * @param \BP_XProfile_ProfileData $data data object.
	 *
	 * @return mixed
	 */
	public function on_field_data_save( $data ) {

		if ( ! is_user_logged_in() ) {
			return $data;
		}

		$field_id = $data->field_id;
		$field    = new \BP_XProfile_Field( $field_id );
		$min_age  = Field_Type_Birthdate::get_min_age( $field_id );

		if ( 'birthdate' !== $field->type || $min_age <= 0 ) {
			return $data;
		}

		$redirect_url = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() . '/edit/group/' . bp_action_variable( 1 ) );

		// Check birthdate.
		$now   = new \DateTime();
		$year  = $_POST[ 'field_' . $field_id . '_year' ];
		$month = $_POST[ 'field_' . $field_id . '_month' ];
		$day   = $_POST[ 'field_' . $field_id . '_day' ];

		if ( ! is_numeric( $year ) || empty( $month ) || ! is_numeric( $day ) ) {
			bp_core_add_message( sprintf( __( 'Incorrect birthdate selection.', 'bp-xprofile-custom-field-types' ), $min_age ), 'error' );
			bp_core_redirect( $redirect_url );
		}

		$birthdate = new \DateTime( sprintf( '%s-%s-%s', str_pad( $day, 2, '0', STR_PAD_LEFT ), $month, $year ) );

		if ( $now <= $birthdate ) {
			bp_core_add_message( sprintf( __( 'Incorrect birthdate selection.', 'bp-xprofile-custom-field-types' ), $min_age ), 'error' );
			bp_core_redirect( $redirect_url );
		}

		$age = $now->diff( $birthdate );

		if ( $age->y < $min_age ) {
			bp_core_add_message( sprintf( __( 'You have to be at least %s years old.', 'bp-xprofile-custom-field-types' ), $min_age ), 'error' );
			bp_core_redirect( $redirect_url );
		}
	}
}
