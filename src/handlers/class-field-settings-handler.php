<?php
/**
 * Select 2 enabler..
 *
 * @package    BuddyPress Xprofile Custom Field Types Reloaded
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Handlers;

use BPXProfileCFTR\Field_Types\Field_Type_Multi_Select_Taxonomy;
use BPXProfileCFTR\Field_Types\Field_Type_Tags;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Field settings helper.
 */
class Field_Settings_Handler {

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
		add_action( 'bp_custom_profile_edit_fields_pre_visibility', array( $this, 'enable_select2' ) );
	}

	/**
	 * Enable_select2.
	 */
	public function enable_select2() {
		global $field;

		if ( ! $this->is_select2_enabled( $field ) ) {
			return;
		}

		$field_name_id = bp_get_the_profile_field_input_name();
		// for multi valued field.
		if ( bpxcftr_is_multi_valued_field( $field ) ) {
			$field_name_id .= '[]';
		}

		$allow_new_tags = false;

		if ( Field_Type_Multi_Select_Taxonomy::allow_new_terms( $field->id ) || Field_Type_Tags::allow_new_tags( $field->id ) ) {
			$allow_new_tags = true;
		}


		if ( $allow_new_tags ) {
			?>
            <script>
                jQuery(function ($) {
                    $('select[name="<?php echo $field_name_id; ?>"]').select2({
                        tags: true,
                        tokenSeparators: [',']
                    });
                });
            </script>
			<?php
		} else {
			?>
            <script>
                jQuery(function ($) {
                    $('select[name="<?php echo $field_name_id; ?>"]').select2();
                });
            </script>
			<?php
		}
	}


	/**
	 * Check if select 2 is enabled for the field.
	 *
	 * @param \BP_XProfile_Field $field field object.
	 *
	 * @return bool
	 */
	private function is_select2_enabled( $field ) {
		if ( ! $field ) {
			return false;
		}

		global $field;
		if ( ! bpxcftr_is_selectable_field( $field ) ) {
			return false;
		}


		$do_select2 = bp_xprofile_get_meta( $field->id, 'field', 'do_select2' );

		return 'on' === $do_select2;
	}
}
