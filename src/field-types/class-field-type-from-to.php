<?php
/**
 * From/To value field
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * 'fromto' field type.
 */
class Field_Type_From_To extends \BP_XProfile_Field_Type {

	public function __construct() {

		parent::__construct();

		$this->name     = __( 'From/To values', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->accepts_null_value = true;
		$this->supports_options   = false;

		do_action( 'bp_xprofile_field_type_from_to_number', $this );
	}


	public function edit_field_html( array $raw_properties = array() ) {
		global $field;

		$data = empty( $field->data ) ? array(
			'from' => self::get_from_value( $field->id ),
			'to'   => self::get_to_value( $field->id )
		) : maybe_unserialize( $field->data->value );

		// make sure data is always array. In case some one changed the field type, do not throw error.
		if ( ! is_array( $data ) ) {
			$data = array(
				'to'   => '',
				'from' => '',
			);
		}

		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$field_name = bp_get_the_profile_field_input_name();
		$value      = bp_get_the_profile_field_edit_value();

		$is_array = is_array( $value );
		$from = $is_array && isset( $value['from'] ) ? $value['from'] : $data['from'];
		$to   = $is_array && isset( $value['to'] ) ? $value['to'] : $data['to'];

		$type = self::get_value_type( $field->id);

		$from_atts = $this->get_edit_field_html_elements( array_merge(
			array(
				'type'  => 'text',
				'name'  => $field_name . '[from]',
				'id'    => $field_name . '[from]',
				'value' => $from,
			),
			$raw_properties
		) );

		$to_atts =  $this->get_edit_field_html_elements( array_merge(
			array(
				'type' => 'text',
				'name' => $field_name .'[to]',
				'id' => $field_name .'[to]',
				'value' => $to,
			),
			$raw_properties
		) );

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
		<div class="bpxcftr-from-to-edit-field bpxcftr-from-to-edit-field-<?php echo esc_attr( $type );?>">
			<input <?php echo $from_atts; ?> /> <span class="bpxcftr-fromto-edit-separator">-</span>
			<input <?php echo $to_atts; ?> />
		</div>

		<?php if ( bp_get_the_profile_field_description() ) : ?>
			<p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}


	public function admin_field_html( array $raw_properties = array() ) {
		global $field;

		$field_name = bp_get_the_profile_field_input_name();

		$from_atts = $this->get_edit_field_html_elements( array_merge(
			array(
				'type' => 'text',
				'name' => $field_name . '[from]',
				'id'   => $field_name . '[from]',
			),
			$raw_properties
		) );

		$to_atts =  $this->get_edit_field_html_elements( array_merge(
			array(
				'type' => 'text',
				'name' => $field_name . '[to]',
				'id'   => $field_name . '[to]',
			),
			$raw_properties
		) );

		?>
		<div class="bpxcftr-from-to-edit-field">
			<input <?php echo $from_atts; ?> /> <span class="bpxcftr-fromto-edit-separator">-</span>
			<input <?php echo $to_atts; ?> />
		</div>

		<?php
	}

	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class      = $current_field->type != $type ? 'display: none;' : '';
		$from_value = self::get_from_value( $current_field->id );
		$to_value   = self::get_to_value( $current_field->id );
		$value_type = self::get_value_type( $current_field->id );

		?>
		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<h3><?php esc_html_e( 'Options', 'bp-xprofile-custom-field-types' ); ?></h3>
			<div class="inside">
				<p>
					<label for="bpxcftr_fromto_value_type"> <?php _e( 'Value Type', 'bp-xprofile-custom-field-types' );?></label>
					<select name="bpxcftr_fromto_value_type">
						<option value="" <?php selected( $value_type, '', true);?>><?php _e( 'No restrictions', 'bp-xprofile-custom-field-types');?></option>
						<option value="integer" <?php selected( $value_type, 'integer', true );?>><?php _e( 'Integers', 'bp-xprofile-custom-field-types');?></option>
						<option value="numeric" <?php selected( $value_type, 'numeric', true );?>><?php _e( 'Numeric', 'bp-xprofile-custom-field-types');?></option>
						<option value="string" <?php selected( $value_type, 'string', true );?>><?php _e( 'String', 'bp-xprofile-custom-field-types');?></option>
					</select>
				</p>

				<div class="int-numeric-constraints">
					<label for="bpxcftr_numeric_type_constraints"><?php _e('Integer/Numeric Type Constraints', 'bp-xprofile-custom-field-types');?></label>

					<h4><?php _e( 'Default Values', 'bp-xprofile-custom-field-types');?></h4>
					<p>
						<label>
							<?php _e( 'From', 'bp-xprofile-custom-field-types');?>
							<input type="text" name="bpxcftr_fromto_from_value" value="<?php echo esc_attr( $from_value );?>" />
						</label>

						<label>
							<?php _e( 'To', 'bp-xprofile-custom-field-types');?>
							<input type="text" name="bpxcftr_fromto_to_value" value="<?php echo esc_attr( $to_value );?>" />
						</label>

					</p>

				</div><!-- constraints -->

			</div>
		</div>
		<?php
	}

	/**
	 * Modify display for the field.
	 *
	 * @param mixed $field_value field value.
	 * @param int $field_id field id
	 *
	 * @return string
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {

		if ( empty( $field_value ) ) {
			return $field_value;
		}
		$field_value = explode( ',', $field_value );
		$field_value = array_map( 'trim', $field_value );

		if( count( $field_value) !== 2 ) {
		    // always return string.
			return join('', $field_value );
		}

		return sprintf( '<span class="bpxcftr-fromto-from-value">%s</span>-<span class="bpxcftr-fromto-to-value">%s</span>', $field_value[0], $field_value[1] );
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
		bpxcftr_set_current_field( null );
		// we can't guess.
		if ( empty( $field ) ) {
			return true;
		}

		// if it is not empty, make sure, it has 2 values.
		if ( ! is_array( $values ) || 2 !== count( $values ) ) {
			return false;
		}

		$type = self::get_value_type( $field->id );

		if ( empty( $type ) || 'string' === $type ) {
			return true;// allow numbers?
		}

		if ( 'integer' === $type && ( filter_var( $values['from'], FILTER_VALIDATE_INT ) === false || filter_var( $values['to'], FILTER_VALIDATE_INT ) === false ) ) {
			return false;
		} elseif ( 'numeric' === $type && (! is_numeric( $values['from'] ) || ! is_numeric( $values['to'] ) ) ) {
			return false;
		}


		// if we are here, it validates.
		return true;
	}

	/**
	 * Get the allowed value type(can be integer|numeric|string).
	 *
	 * @param int $field_id field id.
	 *
	 * @return float|int
	 */
	private static function get_value_type( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'value_type',  true );
	}

	/**
	 * Get the From Value.
	 *
	 * @param int $field_id field id.
	 *
	 * @return float|int
	 */
	private static function get_from_value( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'from_value',  true );
	}

	/**
	 * Get the To value
	 *
	 * @param int $field_id field id.
	 *
	 * @return int
	 */
	private static function get_to_value( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'to_value',  true );
	}
}
