<?php
/**
 * Handles the terms creation/validation for multi taxonomy field.
 *
 * @package    BuddyPress Xprofile Custom Field Types Reloaded
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Handlers;

use BPXProfileCFTR\Field_Types\Field_Type_Multi_Select_Taxonomy;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}
/**
 * Field settings helper.
 */
class Taxonomy_Terms_Creator {

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
		// Pre validate multiselect custom taxonomy.
		add_filter( 'bp_xprofile_set_field_data_pre_validate', array(
			$this,
			'sanitize'
		), 10, 3 );
	}

	public function sanitize( $value, $field, $field_type_obj ) {
		// store Field's reference to allow us fetch it when validating.
		bpxcftr_set_current_field( $field );

		if ( $field->type !== 'multiselect_custom_taxonomy' ) {
			return $value;
		}
		$allow_new_tags    = Field_Type_Multi_Select_Taxonomy::allow_new_terms( $field->id );
		$taxonomy_selected = Field_Type_Multi_Select_Taxonomy::get_selected_taxonomy( $field->id );

		if ( empty( $taxonomy_selected )|| empty( $value ) ) {
			return '';
		}

		// Add new tags if needed.
		$sanitized = array();

		foreach ( $value as $tag ) {

			if ( ! term_exists( (int) $tag, $taxonomy_selected ) && ! term_exists( $tag, $taxonomy_selected ) ) {

				if ( ! $allow_new_tags ) {
					continue;
				}

				$res = wp_insert_term( $tag, $taxonomy_selected );

				if ( ! is_wp_error( $res ) && is_array( $res ) ) {
					$sanitized[] = "{$res['term_id']}";
				}

			} else {
				$sanitized[] = $tag;
			}
		}

		return $sanitized;
	}

}
