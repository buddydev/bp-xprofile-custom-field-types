<?php
/**
 * Field type to manage a list of predefined tokens.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Token field.
 */
class Field_Type_Token extends \BP_XProfile_Field_Type_Textbox {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Token', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->supports_multiple_defaults = false;
		$this->accepts_null_value         = false;
		$this->supports_options           = false;

		$this->set_format( '/^.+$/', 'replace' );
		do_action( 'bp_xprofile_field_type_token', $this );
	}

	/**
	 * Admin new field screen.
	 *
	 * @param \BP_XProfile_Field $current_field profile field object.
	 *
	 * @param string $control_type type.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';
		$text  = join( ',', self::get_tokens( $current_field->id ) );

		?>
		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box"
		     style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<h3><?php esc_html_e( 'Add a list of allowde tokens(separated by comma(,) eg. ONE,two etc):', 'bp-xprofile-custom-field-types' ); ?></h3>
			<div class="inside">
				<p>
					<textarea name="bpxcftr_token_tokens" id="bpxcftr_token_tokens" rows="5" cols="60"><?php echo $text; ?></textarea>
				</p>
			</div>
		</div>
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

		$field = bpxcftr_get_current_field();

		if ( ! $field ) {
			return false;
		}

		$tokens = self::get_tokens( $field->id );

		if ( empty( $values ) || ! in_array( $values, $tokens ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the tokens.
	 *
	 * @param int $field_id field id.
	 *
	 * @return array
	 */
	private static function get_tokens( $field_id = null ) {

		if ( ! $field_id ) {
			$field_id = bp_get_the_profile_field_id();
		}

		if ( ! $field_id ) {
			return array();
		}

		return array_map( 'trim', explode( ',', bp_xprofile_get_meta( $field_id, 'field', 'token_tokens', true ) ) );
	}
}

