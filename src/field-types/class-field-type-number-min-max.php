<?php
/**
 * Min Max Field
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * NumberMinMax Type
 */
class Field_Type_Number_Min_Max extends \BP_XProfile_Field_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = __( 'Number within min/max values (HTML5 field)', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->accepts_null_value = true;
		$this->supports_options   = false;

		do_action( 'bp_xprofile_field_type_number_minmax', $this );
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

		$args = array(
			'type'  => 'number',
			'value' => bp_get_the_profile_field_edit_value(),
			'min'   => self::get_min_val( $field->id ),
			'max'   => self::get_max_val( $field->id ),
		);

		$html = $this->get_edit_field_html_elements( array_merge( $args, $raw_properties ) );

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
	 * Dashboard->Users->Profile Fields entry.
	 *
	 * @param array $raw_properties properties.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		global $field;

		$args = array(
			'type' => 'number',
			'min'  => self::get_min_val( $field->id ),
			'max'  => self::get_max_val( $field->id ),
		);

		$html = $this->get_edit_field_html_elements( array_merge( $args, $raw_properties ) );
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

		$class = $current_field->type != $type ? 'display: none;' : '';

		$min = self::get_min_val( $current_field->id );
		$max = self::get_max_val( $current_field->id );

		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">

            <h3><?php esc_html_e( 'Write min and max values. You can leave any field blank if you want.', 'bp-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
                <p>

                    <label for="bpxcftr_minmax_min">
						<?php esc_html_e( 'Minimum:', 'bp-xprofile-custom-field-types' ); ?>
                    </label>

                    <input type="text" name="bpxcftr_minmax_min" id="bpxcftr_minmax_min" value="<?php echo esc_attr( $min ); ?>"/>

                    <label for="bpxcftr_minmax_max">
						<?php esc_html_e( 'Maximum:', 'bp-xprofile-custom-field-types' ); ?>
                    </label>
                    <input type="text" name="bpxcftr_minmax_max" id="bpxcftr_minmax_max" value="<?php echo esc_attr( $max ); ?>"/>
                </p>
            </div>
        </div>

		<?php
	}

	/**
	 * Check the validity of the value
	 *
	 * @param string $values value.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {

		if ( empty( $values ) ) {
			return true;
		}

		$field = bpxcftr_get_current_field();
		// we don't know the field, have no idea how to validate.
		if ( empty( $field ) ) {
			return is_numeric( $values );
		}

		$min = self::get_min_val( $field->id );
		$max = self::get_max_val( $field->id );

		// unset the current field from our saved field id.
		bpxcftr_set_current_field( null );

		return ( $values >= $min ) && ( $values <= $max );
	}

	/**
	 * Get the minimum allowed value for the field id.
	 *
	 * @param int $field_id field id.
	 *
	 * @return float
	 */
	public static function get_min_val( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'min_val', true );
	}

	/**
	 * Get the max allowed value for the field id.
	 *
	 * @param int $field_id field id.
	 *
	 * @return float
	 */
	public static function get_max_val( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'max_val', true );
	}
}
