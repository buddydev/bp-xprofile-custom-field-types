<?php
/**
 * Number Field
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Decimal number.
 */
class Field_Type_Decimal_Number extends \BP_XProfile_Field_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = __( 'Decimal number (HTML5 field)', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->accepts_null_value = true;
		$this->supports_options   = false;

		$this->set_format( '/^\d+\.?\d*$/', 'replace' );

		do_action( 'bp_xprofile_field_type_decimal_number', $this );
	}

	/**
	 * Edit field html.
	 *
	 * @param array $raw_properties properties.
	 */
	public function edit_field_html( array $raw_properties = array() ) {
        global $field;

        if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements(
			array_merge(
				array(
					'type'  => 'number',
					'step'  => floatval( self::get_step( $field->id ) ),
					'value' => bp_get_the_profile_field_edit_value(),
				),
				$raw_properties
			)
		);
		?>

		<legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php

		// Errors.
		do_action( bp_get_the_profile_field_errors_action() );
		// Input.
		?>
        <input <?php echo $html; ?> />

		<?php if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
	 * Admin field list html.
	 *
	 * @param array $raw_properties properties.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		global $field;

		$html = $this->get_edit_field_html_elements(
			array_merge(
				array(
					'type' => 'number',
					'step' => self::get_step( $field->id ),
				),
				$raw_properties
			)
		);
		?>
        <input <?php echo $html; ?> />
		<?php
	}

	/**
	 * Dashboard->Users->Profile Fields->New|Edit entry.
	 *
	 * @param \BP_XProfile_Field $current_field object.
	 * @param string             $control_type type.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

        $type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

        if ( false === $type ) {
			return;
		}

		$class             = $current_field->type != $type ? 'display: none;' : '';
		$current_precision = self::get_precision( $current_field->id );
		$current_step      = self::get_step( $current_field->id );
		if ( ! $current_step ) {
			$current_step = 1;
		}
		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
            <h3><?php esc_html_e( 'Options', 'bp-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
                <p>
                    <label for="bpxcftr_decimal_precision"><?php _e('Precision', 'bp-xprofile-custom-field-types');?></label>
                    <select name="bpxcftr_decimal_precision" id="bpxcftr_decimal_precision">
						<?php for ( $j = 1; $j <= 6; $j ++ ): ?>
                            <option value="<?php echo $j; ?>" <?php  selected( $current_precision, $j, true ); ?>><?php echo $j; ?></option>
						<?php endfor; ?>
                    </select>
                </p>

                <p>
                    <label for="bpxcftr_decimal_step_size"><?php _e('Step Size', 'bp-xprofile-custom-field-types');?></label>
                    <input type="text" value="<?php echo esc_attr( $current_step);?>" name="bpxcftr_decimal_step_size" />
                </p>
                <p><?php _e('Use decimal in step e.g .05, 1.3 etc, otherwise the field will behave as number','bp-xprofile-custom-field-types' );?></p>
            </div>
        </div>
		<?php
	}

	/**
	 * It is a valid value?
	 *
	 * @param string $values value to be checked.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {

		if ( empty( $values ) ) {
			return true;
		}

		$field = bpxcftr_get_current_field();

		// we can't guess.
		if ( empty( $field ) ) {
			return true;
		}

		$precision = self::get_precision( $field->id );
		bpxcftr_set_current_field( null );
		$pos = strpos( $values, '.' );

		if ( false === $pos ) {
			return is_numeric( $values );
		}

		$decimal = substr( $values, $pos + 1 );

		return is_numeric( $values ) && ( $precision >= strlen( $decimal ) );
	}

	/**
	 * Get the step size.
	 *
	 * @param int $field_id field id.
	 *
	 * @return float|int
	 */
	private static function get_step( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'step_size', true );
	}

	/**
	 * Get the step size.
	 *
	 * @param int $field_id field id.
	 *
	 * @return int
	 */
	private static function get_precision( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'precision', true );
	}
}
