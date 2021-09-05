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

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

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
		// save meta.
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
				$data = isset( $_POST['bpxcftr_tos_content'] ) ? wp_unslash( $_POST['bpxcftr_tos_content'] ) : '';
				$data = wp_kses_post( $data );
				bp_xprofile_update_field_meta( $field->id, 'tos_content', $data );

				break;

			case 'country':
				$country = isset( $_POST['bpxcftr_default_country'] ) ? wp_unslash( $_POST['bpxcftr_default_country'] ) : '';
				$country = sanitize_text_field( $country );
				bp_xprofile_update_field_meta( $field->id, 'default_country', $country );
				break;

			case 'select_custom_post_type':
				$data = isset( $_POST['bpxcftr_selected_post_type'] ) ? wp_unslash( $_POST['bpxcftr_selected_post_type'] ) : '';
				$data = sanitize_text_field( $data ); // seriously, we should validate against the post tye.
				bp_xprofile_update_field_meta( $field->id, 'selected_post_type', $data );

				break;

			case 'multiselect_custom_post_type':
				$data = isset( $_POST['bpxcftr_multi_selected_post_type'] ) ? wp_unslash( $_POST['bpxcftr_multi_selected_post_type'] ) : '';
				bp_xprofile_update_field_meta( $field->id, 'selected_post_type', $data );

				break;

			case 'select_custom_taxonomy':
				$data = isset( $_POST['bpxcftr_selected_taxonomy'] ) ? wp_unslash( $_POST['bpxcftr_selected_taxonomy'] ) : '';
				bp_xprofile_update_field_meta( $field->id, 'selected_taxonomy', $data );

				break;

			case 'multiselect_custom_taxonomy':
				$data = isset( $_POST['bpxcftr_multi_selected_taxonomy'] ) ? wp_unslash( $_POST['bpxcftr_multi_selected_taxonomy'] ) : '';
				bp_xprofile_update_field_meta( $field->id, 'selected_taxonomy', $data );

				$allow_terms = isset( $_POST['bpxcftr_multi_tax_allow_new_terms'] ) ? 1 : 0;

				if ( $allow_terms ) {
					bp_xprofile_update_field_meta( $field->id, 'allow_new_terms', $data );
				} else {
					bp_xprofile_delete_meta( $field->id, 'field', 'allow_new_terms' );
				}

				break;

			case 'tags':
				$default_tags = isset( $_POST['bpxcftr_tags_default_tags'] ) ? wp_unslash( $_POST['bpxcftr_tags_default_tags'] ) : '';
				$default_tags = sanitize_textarea_field( $default_tags );

				$allow_tags   = isset( $_POST['bpxcftr_tags_allow_new_tags'] ) ? 1 : 0;

				bp_xprofile_update_field_meta( $field->id, 'default_tags', $default_tags );

				if ( $allow_tags ) {
					bp_xprofile_update_field_meta( $field->id, 'allow_new_tags', $allow_tags );
				} else {
					bp_xprofile_delete_meta( $field->id, 'field', 'allow_new_tags' );
				}

				break;

			case 'number_minmax':
				$min = isset( $_POST['bpxcftr_minmax_min'] ) ? intval( $_POST['bpxcftr_minmax_min'] ) : 0;
				bp_xprofile_update_field_meta( $field->id, 'min_val', $min );

				$max = isset( $_POST['bpxcftr_minmax_max'] ) ? intval( $_POST['bpxcftr_minmax_max'] ) : 0;
				bp_xprofile_update_field_meta( $field->id, 'max_val', $max );

				break;

			case 'slider':
				$min = isset( $_POST['bpxcftr_slider_min'] ) ? wp_unslash( $_POST['bpxcftr_slider_min'] ) : 0;
				bp_xprofile_update_field_meta( $field->id, 'min_val', $min );

				$max = isset( $_POST['bpxcftr_slider_max'] ) ? wp_unslash( $_POST['bpxcftr_slider_max'] ) : 0;
				bp_xprofile_update_field_meta( $field->id, 'max_val', $max );

				break;

			case 'decimal_number':
				$precision = isset( $_POST['bpxcftr_decimal_precision'] ) ? wp_unslash( $_POST['bpxcftr_decimal_precision'] ) : 0;
				bp_xprofile_update_field_meta( $field->id, 'precision', $precision );

				$step = isset( $_POST['bpxcftr_decimal_step_size'] ) ? wp_unslash( $_POST['bpxcftr_decimal_step_size'] ) : 1;
				bp_xprofile_update_field_meta( $field->id, 'step_size', $step );

				break;

			case 'birthdate':
				$show_age = isset( $_POST['bpxcftr_birtdate_show_age'] ) ? 1 : 0;
				bp_xprofile_update_field_meta( $field->id, 'show_age', $show_age );

				$min_age = isset( $_POST['bpxcftr_birtdate_min_age'] ) ? wp_unslash( $_POST['bpxcftr_birtdate_min_age'] ) : 0;

				if ( $min_age ) {
					bp_xprofile_update_field_meta( $field->id, 'min_age', $min_age );
				} else {
					bp_xprofile_delete_meta( $field->id, 'field', 'min_age' );

				}

				$hide_months = isset( $_POST['bpxcftr_birtdate_hide_months'] ) ? 1 : 0;
				bp_xprofile_update_field_meta( $field->id, 'hide_months', $hide_months );

				$age_label = isset( $_POST['bpxcftr_birthdate_age_label'] ) ? trim( wp_unslash( $_POST['bpxcftr_birthdate_age_label'] ) ) : '';
				bp_xprofile_update_field_meta( $field->id, 'age_label', $age_label );

				break;

			case 'fromto':
				$value_type = isset( $_POST['bpxcftr_fromto_value_type'] ) ? wp_unslash( $_POST['bpxcftr_fromto_value_type'] ) : '';
				bp_xprofile_update_field_meta( $field->id, 'value_type', $value_type );

				$from_value = isset( $_POST['bpxcftr_fromto_from_value'] ) ? wp_unslash( $_POST['bpxcftr_fromto_from_value'] ) : '';
				bp_xprofile_update_field_meta( $field->id, 'from_value', $from_value );


				$from_value = isset( $_POST['bpxcftr_fromto_to_value'] ) ? wp_unslash( $_POST['bpxcftr_fromto_to_value'] ) : '';
				bp_xprofile_update_field_meta( $field->id, 'to_value', $from_value );

				$separator = isset( $_POST['bpxcftr_fromto_separator_token'] )? wp_unslash( $_POST['bpxcftr_fromto_separator_token']): '-';
				bp_xprofile_update_field_meta( $field->id, 'separator_token', $separator );

				break;

			case 'token':
				$data = isset( $_POST['bpxcftr_token_tokens'] ) ? wp_unslash( $_POST['bpxcftr_token_tokens'] ) : '';
				$data = sanitize_textarea_field( $data );
				bp_xprofile_update_field_meta( $field->id, 'token_tokens', $data );
				// Ignore case?
				$is_ignored = isset( $_POST['bpxcftr_token_ignore_case'] ) ? 1 : 0;
				bp_xprofile_update_field_meta( $field->id, 'token_ignore_case', $is_ignored );

				break;
			case 'web':
				$link_target = isset( $_POST['bpxcftr_web_link_target'] ) ? wp_unslash( $_POST['bpxcftr_web_link_target'] ) : '';

				if ( '_blank' !== $link_target ) {
					$link_target = '';
				}

				bp_xprofile_update_field_meta( $field->id, 'link_target', $link_target );

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

	/**
	 * Prints select2 setting.
	 *
	 * @param \BP_XProfile_Field $field field object.
	 */
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
