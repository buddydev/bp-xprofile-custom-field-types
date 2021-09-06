<?php
/**
 * Handles the tags creation/validation for tags field.
 *
 * @package    BuddyPress Xprofile Custom Field Types Reloaded
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Handlers;

use BPXProfileCFTR\Field_Types\Field_Type_Tags;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Tags creator.
 */
class Tags_Creator {

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
		// Pre validate tags.
		//add_filter( 'bp_xprofile_set_field_data_pre_validate', array( $this, 'sanitize' ), 10, 2 );

		add_filter( 'bp_get_the_profile_field_value', array( $this, 'filter_value' ), 10, 3 );

		add_filter( 'bpxcftr_load_front_assets', array( $this, 'should_load_assets' ) );

		add_action( 'wp_ajax_bpxcftr_remove_user_tag', array( $this, 'remove_user_tag' ) );
	}

	/**
	 * Sanitize Value.
	 *
	 * @param mixed              $value value.
	 * @param \BP_XProfile_Field $field field object.
	 *
	 * @return array|string
	 */
	public function sanitize( $value, $field ) {
		// store Field's reference to allow us fetch it when validating.
		bpxcftr_set_current_field( $field );

		if ( 'tags' !== $field->type || empty( $value ) ) {
			return $value;
		}

		$allow_new_tags = Field_Type_Tags::allow_new_tags( $field->id );

		// Add new tags if needed.
		$sanitized = array();

		$user_id = bp_displayed_user_id();

		$default_tags = Field_Type_Tags::get_default_tags( $field->id );
		$user_tags    = xprofile_get_field_data( $field->id, $user_id );
		$user_tags    = $user_tags ? $user_tags : array();

		foreach ( $value as $tag ) {

			if ( in_array( $tag, $default_tags ) ) {
				$sanitized[] = $tag;
			} elseif ( $allow_new_tags ) {
				$field_data = array_merge( $user_tags, array( $tag ) );
				xprofile_set_field_data( $field->id, $user_id, $field_data );

				$sanitized[] = $tag;
			}
		}

		return $sanitized;
	}

	/**
	 * Filter profile field value
	 *
	 * @param string $value      Value for the profile field.
	 * @param string $field_type Type for the profile field.
	 * @param int    $field_id   ID for the profile field.
	 *
	 * @return string
	 */
	public function filter_value( $value, $field_type, $field_id ) {
		if ( 'tags' !== $field_type || ! bp_is_my_profile() ) {
			return $value;
		}

		$tags    = array_filter( explode( ',', $value ) );
		$user_id = get_current_user_id();

		$appended_tags = array_map(
			function ( $tag ) use ( $user_id, $field_id ) {
				$tag   = wp_strip_all_tags( $tag );
				$nonce = wp_create_nonce( 'bpxcftr-remove-user-' . $user_id . '-tag-' . $tag );

				return '<span class="bpxcftr-remove-tag" data-field-id="' . esc_attr( $field_id ) . '" data-tag="' . esc_html( $tag ) . '" data-nonce="' . $nonce . '" style="cursor: pointer;">' . $tag . '[x]</span>';
			},
			$tags
		);

		return join( ', ', $appended_tags );
	}

	/**
	 * Should load assets.
	 *
	 * @return bool
	 */
	public function should_load_assets() {

		if ( ! bp_is_profile_component() ) {
			return false;
		}

		return is_super_admin() || bp_is_my_profile();
	}

	/**
	 * Remove user tag
	 */
	public function remove_user_tag() {
		$posted_data = wp_unslash( $_POST );

		$tag      = isset( $posted_data['tag'] ) ? sanitize_text_field( wp_unslash( $posted_data['tag'] ) ) : '';
		$field_id = isset( $posted_data['field_id'] ) ? wp_unslash( $posted_data['field_id'] ) : '';
		$user_id  = get_current_user_id();

		if ( ! $tag || ! $field_id || ! $user_id || ! bp_is_my_profile() ) {
			wp_send_json_error( __( 'Invalid Request.', 'bp-xpofile-custom-field-types' ) );
		}

		check_ajax_referer( 'bpxcftr-remove-user-' . $user_id . '-tag-' . $tag, 'nonce' );

		$field_data = xprofile_get_field_data( $field_id, $user_id );

		$position = array_search( $tag, $field_data, true );

		if ( false === $position ) {
			wp_send_json_error( __( 'Tag not found.', 'bp-xpofile-custom-field-types' ) );
		}

		unset( $field_data[ $position ] );

		xprofile_set_field_data( $field_id, $user_id, $field_data );

		wp_send_json_success( __( 'Tag removed.', 'bp-xpofile-custom-field-types' ) );
	}
}
