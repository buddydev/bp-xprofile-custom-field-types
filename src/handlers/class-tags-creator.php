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
		add_filter( 'bp_xprofile_set_field_data_pre_validate', array( $this, 'sanitize' ), 10, 2 );
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

		$parent_id      = $field->id;
		$field_group_id = $field->group_id;

		$field_options    = $field->get_children( true );
		$options_name     = empty( $field_options ) ? array() : wp_list_pluck( $field_options, 'name' );
		$max_option_order = empty( $field_options ) ? 0 : max( wp_list_pluck( $field_options, 'option_order' ) );

		foreach ( $value as $tag ) {

			if ( in_array( $tag, $options_name ) ) {
				$sanitized[] = $tag;
			} elseif ( $allow_new_tags ) {
				$field_id = xprofile_insert_field(
					array(
						'field_group_id' => $field_group_id,
						'parent_id'      => $parent_id,
						'type'           => 'option',
						'name'           => $tag,
						'option_order'   => ++ $max_option_order,
					)
				);

				if ( $field_id ) {
					$field       = xprofile_get_field( $field_id );
					$sanitized[] = $field->name;
				}
			}
		}

		return $sanitized;
	}

}
