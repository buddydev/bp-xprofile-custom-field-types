<?php
/**
 * Manages admin field preferences.
 *
 * @package    BuddyPress Xprofile Custom Field Types Reloaded
 * @subpackage Bootstrap
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
/**
 * Note:- To be fully backward compatible with BuddyPress Xprofile Custom Fields Type plugin,
 * we are using the same settings name.
 */

/**
 * Field Edit Settings helper
 */
class Field_Settings_Helper {

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
	    //save meta.
		add_action( 'xprofile_fields_saved_field', array( $this, 'save_meta' ) );

		add_action( 'xprofile_field_after_submitbox', array( $this, 'show_select2_box_settings' ) );
		add_action( 'xprofile_fields_saved_field', array( $this, 'update_select2_settings' ) );
	}

	/**
	 * Save the text when the field is saved
	 *
	 * @param \BP_XProfile_Field $field field object.
	 */
	public function save_meta( $field ) {

		switch ( $field->type ) {

			case 'checkbox_acceptance':

				$data = isset( $_POST['bpxcftr_tos_content'] ) ? $_POST['bpxcftr_tos_content'] : '';
				$data = sanitize_textarea_field( $data );
				bp_xprofile_update_field_meta( $field->id, 'tos_content', $data );

				break;

			case 'select_custom_post_type':

			    $data = isset( $_POST['bpxcftr_selected_post_typebpxcftr_selected_post_type'] ) ? $_POST['bpxcftr_selected_post_type'] : '';
				$data = sanitize_text_field( $data ); // seriously, we should validate against the post tye.
				bp_xprofile_update_field_meta( $field->id, 'selected_post_type', $data );

				break;

			case 'multiselect_custom_post_type':

				$data = isset( $_POST['bpxcftr_multi_selected_post_type'] ) ? $_POST['bpxcftr_multi_selected_post_type'] : '';
				bp_xprofile_update_field_meta( $field->id, 'selected_post_type', $data );

				break;

			case 'select_custom_taxonomy':

			    $data = isset( $_POST['bpxcftr_selected_taxonomy'] ) ? $_POST['bpxcftr_selected_taxonomy'] : '';
				bp_xprofile_update_field_meta( $field->id, 'selected_taxonomy', $data );

				break;

			case 'multiselect_custom_taxonomy':

				$data = isset( $_POST['bpxcftr_multi_selected_taxonomy'] ) ? $_POST['bpxcftr_multi_selected_taxonomy'] : '';
				bp_xprofile_update_field_meta( $field->id, 'selected_taxonomy', $data );

				$allow_terms = isset( $_POST['bpxcftr_multi_tax_allow_new_terms'] ) ? 1 : 0;

				if ( $allow_terms ) {
					bp_xprofile_update_field_meta( $field->id, 'allow_new_terms', $data );
				} else {
					bp_xprofile_delete_meta( $field->id, 'field', 'allow_new_terms' );
				}

				break;

			case 'number_minmax':

				$min = isset( $_POST['bpxcftr_minmax_min'] ) ? $_POST['bpxcftr_minmax_min'] : 0;
				bp_xprofile_update_field_meta( $field->id, 'min_val', $min );

				$max = isset( $_POST['bpxcftr_minmax_max'] ) ? $_POST['bpxcftr_minmax_max'] : 0;
				bp_xprofile_update_field_meta( $field->id, 'max_val', $max );

				break;

			case 'slider':

				$min = isset( $_POST['bpxcftr_slider_min'] ) ? $_POST['bpxcftr_slider_min'] : 0;
				bp_xprofile_update_field_meta( $field->id, 'min_val', $min );

				$max = isset( $_POST['bpxcftr_slider_max'] ) ? $_POST['bpxcftr_slider_max'] : 0;
				bp_xprofile_update_field_meta( $field->id, 'max_val', $max );

				break;

			case 'decimal_number':

				$precision = isset( $_POST['bpxcftr_decimal_precision'] ) ? $_POST['bpxcftr_decimal_precision'] : 0;
				bp_xprofile_update_field_meta( $field->id, 'precision', $precision );

				$step = isset( $_POST['bpxcftr_decimal_step_size'] ) ? $_POST['bpxcftr_decimal_step_size'] : 1;
				bp_xprofile_update_field_meta( $field->id, 'step_size', $step );

				break;

			case 'birthdate':

				$show_age = isset( $_POST['bpxcftr_birtdate_show_age'] ) ? 1 : 0;
				bp_xprofile_update_field_meta( $field->id, 'show_age', $show_age );

				$min_age = isset( $_POST['bpxcftr_birtdate_min_age'] ) ? $_POST['bpxcftr_birtdate_min_age'] : 0;

				if ( $min_age ) {
					bp_xprofile_update_field_meta( $field->id, 'min_age', $min_age );
				} else {
					bp_xprofile_delete_meta( $field->id, 'field', 'min_age' );

				}

				$hide_months = isset( $_POST['bpxcftr_birtdate_hide_months'] ) ? 1 : 0;
				bp_xprofile_update_field_meta( $field->id, 'hide_months', $hide_months );
				break;

            case 'fromto':

	            $value_type = isset( $_POST['bpxcftr_fromto_value_type'] ) ? $_POST['bpxcftr_fromto_value_type'] : '';
	            bp_xprofile_update_field_meta( $field->id, 'value_type', $value_type );

	            $from_value = isset( $_POST['bpxcftr_fromto_from_value'] ) ? $_POST['bpxcftr_fromto_from_value'] : '';
	            bp_xprofile_update_field_meta( $field->id, 'from_value', $from_value );


	            $from_value = isset( $_POST['bpxcftr_fromto_to_value'] ) ? $_POST['bpxcftr_fromto_to_value'] : '';
	            bp_xprofile_update_field_meta( $field->id, 'to_value', $from_value );

	            break;
		}
	}

	/**
	 * Update preference for select2 use.
	 *
	 * @param \BP_XProfile_Field $field field object.
	 */
	public function update_select2_settings( $field ) {
		$field_id = $field->id;

		if ( ! bpxcftr_is_selectable_field( $field ) ) {
			bp_xprofile_update_field_meta( $field_id, 'do_select2', '' );

			return;
		}

		// Save select2 settings.
		if ( isset( $_POST['do_select2'] ) && 'on' === wp_unslash( $_POST['do_select2'] ) ) {
			bp_xprofile_update_field_meta( $field_id, 'do_select2', 'on' );
		} else {
			bp_xprofile_update_field_meta( $field_id, 'do_select2', 'off' );
		}

	}

	public function show_select2_box_settings( $field ) {

		$do_select2 = bp_xprofile_get_meta( $field->id, 'field', 'do_select2' );
		$hidden     = true;

		if ( bpxcftr_is_selectable_field( $field ) ) {
			$hidden = false;
		}

		?>
        <div id="select2-box" class="postbox<?php if ( $hidden ): ?> hidden<?php endif; ?>">
            <h2><?php esc_html_e( 'Select2', 'buddypress-xprofile-custom-fields-types' ); ?></h2>
            <div class="inside">
                <p class="description"><?php _e( 'Enable select2 javascript code.', 'buddypress-xprofile-custom-fields-types' ); ?></p>

                <p>
                    <label for="do-select2"
                           class="screen-reader-text"><?php _e( 'Select2 status for this field', 'buddypress-xprofile-custom-fields-types' ); ?></php></label>
                    <select name="do_select2" id="do-select2">
                        <option value="on" <?php if ( $do_select2 === 'on' ): ?> selected="selected"<?php endif; ?>>
							<?php _e( 'Enabled', 'buddypress-xprofile-custom-fields-types' ); ?>
                        </option>
                        <option value=""<?php if ( $do_select2 !== 'on' ): ?> selected="selected"<?php endif; ?>>
							<?php _e( 'Disabled', 'buddypress-xprofile-custom-fields-types' ); ?>
                        </option>
                    </select>
                </p>
            </div>
        </div>
		<?php
	}

}
