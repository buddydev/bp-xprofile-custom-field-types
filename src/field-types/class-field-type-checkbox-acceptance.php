<?php
/**
 * Checkbox for terms fields
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Tos field.
 */
class Field_Type_Checkbox_Acceptance extends \BP_XProfile_Field_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Checkbox Acceptance', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->supports_multiple_defaults = false;
		$this->accepts_null_value         = false;
		$this->supports_options           = false;

		$this->set_format( '/^.+$/', 'replace' );
		do_action( 'bp_xprofile_field_type_checkbox_acceptance', $this );
	}

	/**
	 * Edit field html.
	 *
	 * @param array $raw_properties properties.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		$user_id = bp_displayed_user_id();

		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		}

		// HTML5 required attribute.
		if ( bp_get_the_profile_field_is_required() ) {
			$raw_properties['required'] = 'required';
			$required                   = true;
		} else {
			$required = false;
		}
		?>
		<legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>

		<?php bp_the_profile_field_options( "user_id={$user_id}&required={$required}" ); ?>

		<?php if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description"
               id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
	 * Field html for Admin-> User->Profile Fields screen.
	 *
	 * @param array $raw_properties properties.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		global $field;

		$text = wp_kses_data( self::get_content( $field ) );

		$html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'checkbox' ),
			$raw_properties
		) );
		?>
        <label for="<?php bp_the_profile_field_input_name(); ?>">
            <input <?php echo $html; ?>>
			<?php echo $text; ?>
        </label>
		<?php
	}

	/**
	 * Admin new field screen.
	 *
	 * @param \BP_XProfile_Field $current_field profile field object.
	 * @param string             $control_type type.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';
		$text  = self::get_content( $current_field );

		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box"
             style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
            <h3><?php esc_html_e( 'Use this field to write a text that should be displayed beside the checkbox:', 'bp-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
                <p>
                    <textarea name="bpxcftr_tos_content" id="bpxcftr_tos_content" rows="5" cols="60"><?php echo $text; ?></textarea>
                </p>
            </div>
        </div>
		<?php
	}

	/**
	 * Profile edit/register options html.
	 *
	 * @param array $args args.
	 */
	public function edit_field_options_html( array $args = array() ) {
		global $field;

		$checkbox_acceptance = maybe_unserialize( \BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

		if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
			$new_checkbox_acceptance = $_POST[ 'field_' . $this->field_obj->id ];
			$checkbox_acceptance     = ( $checkbox_acceptance != $new_checkbox_acceptance ) ? $new_checkbox_acceptance : $checkbox_acceptance;
		}

		$checkbox_acceptance = absint( $checkbox_acceptance );

		$atts = array(
			'type'  => 'checkbox',
			'name'  => bp_get_the_profile_field_input_name(),
			'id'    => bp_get_the_profile_field_input_name(),
			'value' => 1,
			'class' => 'bpxcftr-tos-checkbox',
		);

		if ( $checkbox_acceptance == 1 ) {
			$atts['checked'] = "checked";
		}

		$html = '<input ' . $this->get_edit_field_html_elements( $atts ) . ' />';
		// we should most probably avoid kses  on output.
		$html .= wp_kses_post( self::get_content( $field ) );
		echo apply_filters( 'bp_get_the_profile_field_checkbox_acceptance', $html, $args['type'], $this->field_obj->id, $checkbox_acceptance );
		?>
    	<?php
	}

	/**
	 * Check if field is valid?
	 *
	 * @param string|int $values value.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {

		if ( empty( $values ) || 1 == $values ) {
			return true;
		}

		return false;
	}

	/**
	 * Modify the appearance of value.
	 *
	 * @param string $field_value Original value of field.
	 * @param int    $field_id field id.
	 *
	 * @return string   Value formatted
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {
		return empty( $field_value ) ? __( 'No', 'bp-xprofile-custom-field-types' ) : __( 'Yes', 'bp-xprofile-custom-field-types' );
	}

	/**
	 * Get the terms content.
	 *
	 * @param \BP_XProfile_Field $field field object.
	 *
	 * @return string
	 */
	private static function get_content( $field = null ) {

		if ( ! $field ) {
			$field_id = bp_get_the_profile_field_id();
		} else {
			$field_id = $field->id;
		}

		if ( ! $field_id ) {
			return '';
		}

		return bp_xprofile_get_meta( $field_id, 'field', 'tos_content', true );
	}
}
