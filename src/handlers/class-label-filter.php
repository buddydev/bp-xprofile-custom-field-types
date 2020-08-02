<?php
/**
 * Filters label for Birthdate/Age.
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
 * Filters Label.
 */
class Label_Filter {

	/**
	 * Flag to keep a tab on whether we remove the filter or not?
	 *
	 * @var bool
	 */
	protected $removed = false;

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
		add_filter( 'bp_get_the_profile_field_name', array( $this, 'filter_label' ) );
	}

	/**
	 * Filter field label.
	 *
	 * @param string $label label.
	 *
	 * @return string
	 */
	public function filter_label( $label ) {
		global $field;
		if ( ! $field || 'birthdate' !== $field->type ) {
			return $label;
		}

		$age_label = Field_Type_Birthdate::get_age_label( $field->id );
		if ( ! $age_label ) {
			return $label;
		}

		// if we are here, should should change it in display context only. For edit context, the normal label should work.
		$is_edit = is_admin() || bp_is_register_page() || bp_is_user_profile_edit();
		if ( apply_filters( 'bpxcftr_is_edit_profile_context', $is_edit ) ) {
			return $label;
		}

		return $age_label;
	}
}
