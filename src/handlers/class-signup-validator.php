<?php
/**
 * Signup validator.
 *
 * @package    BuddyPress Xprofile Custom Field Types Reloaded
 * @subpackage Bootstrap
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
 * Signup data validator.
 */
class Signup_Validator {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup.
	 */
	public function setup() {
		add_action( 'bp_signup_validate', array( $this, 'validate' ) );
	}

	/**
	 * Validate fields.
	 */
	public function validate() {
		if ( ! bp_is_active( 'xprofile' ) ) {
			return;
		}

		$profile_field_ids = isset( $_POST['signup_profile_field_ids'] ) ? explode( ',', wp_unslash( $_POST['signup_profile_field_ids'] ) ) : array();

		foreach ( $profile_field_ids as $field_id ) {
			$this->validate_field( $field_id );
		}
	}

	/**
	 * Validate a field.
	 *
	 * @param int $field_id field id.
	 */
	private function validate_field( $field_id ) {
		$bp    = buddypress();
		$field = new \BP_XProfile_Field( $field_id );

		switch ( $field->type ) {
			case 'image':
			case 'file':
				if ( empty( $_FILES[ 'field_' . $field_id ] ) || empty( $_FILES[ 'field_' . $field_id ]['name'] ) ) {
					if ( $field->is_required ) {
						$bp->signup->errors[ 'field_' . $field_id ] = __( 'This is a required field.', 'bp-xprofile-custom-field-types' );
					}
					break;
				}
				// remove error?
				unset( $bp->signup->errors[ 'field_' . $field_id ] );
				$this->validate_file( $field );

				break;

			case 'checkbox_acceptance':
				if ( $field->is_required && ( empty( $_POST[ 'field_' . $field_id ] ) || 1 != $_POST[ 'field_' . $field_id ] ) ) {
					$bp->signup->errors[ 'field_' . $field_id ] = __( 'This is a required field', 'bp-xprofile-custom-field-types' );
				}

				break;

			case 'fromto':
				if ( $field->is_required && ( empty( $_POST[ 'field_' . $field_id ]['from'] ) || empty( $_POST[ 'field_' . $field_id ]['to'] ) ) ) {
					$bp->signup->errors[ 'field_' . $field_id ] = __( 'This is a required field', 'bp-xprofile-custom-field-types' );
				}

				break;

			case 'birthdate':
				$this->validate_birthdate( $field );
				break;

			case 'token':
				$this->validate_token( $field, isset( $_POST[ 'field_' . $field_id ] ) ? trim( $_POST[ 'field_' . $field_id ] ) : '' );
				break;
		}
	}

	/**
	 * Validate the file type fields.
	 *
	 * @param \BP_XProfile_Field $field field object.
	 */
	private function validate_file( $field ) {
		$bp       = buddypress();
		$field_id = $field->id;

		$filesize = round( $_FILES[ 'field_' . $field_id ]['size'] / ( 1024 * 1024 ), 2 );

		if ( $field->is_required && $filesize <= 0 ) {
			$bp->signup->errors[ 'field_' . $field_id ] = __( 'This is a required field.', 'bp-xprofile-custom-field-types' );

			return;
		}

		// Check extensions.
		$ext = strtolower( substr( $_FILES[ 'field_' . $field_id ]['name'], strrpos( $_FILES[ 'field_' . $field_id ]['name'], '.' ) + 1 ) );

		$allowed_extension = bpxcftr_get_allowed_file_extensions( $field->type );
		$allowed_size      = bpxcftr_get_allowed_file_size( $field->type );

		if ( $allowed_size < $filesize ) {
			$bp->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'File exceed the upload limit. Max upload size %d.', 'bp-xprofile-custom-field-types' ), $allowed_size );
		}

		if ( ! in_array( $ext, $allowed_extension ) ) {
			$bp->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'File type not allowed: (%s).', 'bp-xprofile-custom-field-types' ), implode( ',', $allowed_extension ) );
		}
	}


	/**
	 * Validate the Birthdate.
	 *
	 * @param \BP_XProfile_Field $field field object.
	 */
	private function validate_birthdate( $field ) {
		$bp       = buddypress();
		$field_id = $field->id;
		$min_age  = Field_Type_Birthdate::get_min_age( $field_id );

		if ( $min_age <= 0 ) {
			return;
		}

		if ( empty( $_POST[ 'field_' . $field_id ] ) ) {
			$bp->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'You have to be at least %s years old.', 'bp-xprofile-custom-field-types' ), $min_age );

			return;
		}

		$year  = wp_unslash( $_POST[ 'field_' . $field_id . '_year' ] );
		$month = wp_unslash( $_POST[ 'field_' . $field_id . '_month' ] );
		$day   = wp_unslash( $_POST[ 'field_' . $field_id . '_day' ] );

		if ( ! is_numeric( $year ) || empty( $month ) || ! is_numeric( $day ) ) {
			$bp->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'Invalid date.', 'bp-xprofile-custom-field-types' ) );
			return;
		}

		// Check birthdate.
		$now       = new \DateTime();
		$birthdate = new \DateTime( sprintf( '%s-%s-%s', str_pad( $day, 2, '0', STR_PAD_LEFT ), $month, $year ) );
		$age       = $now->diff( $birthdate );

		if ( $age->y < $min_age ) {
			$bp->signup->errors[ 'field_' . $field_id ] = sprintf( __( 'You have to be at least %s years old.', 'bp-xprofile-custom-field-types' ), $min_age );
		}
	}

	/**
	 * Validate signup token.
	 *
	 * @param \BP_XProfile_Field_Type $field filed type token.
	 * @param string                  $token token to validate.
	 */
	private function validate_token( $field, $token ) {

		if ( ! $field->is_required && empty( $token ) ) {
			return;
		}

		$field_type_obj = bp_xprofile_create_field_type( 'token' );
		bpxcftr_set_current_field( $field );
		if ( ! $field_type_obj->is_valid( $token ) ) {
			buddypress()->signup->errors[ 'field_' . $field->id ] = __( 'Please provide a valid token.', 'bp-xprofile-custom-field-types' );
		}
	}
}
