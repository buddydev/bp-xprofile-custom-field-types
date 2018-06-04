<?php
/**
 * Email Field.
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

class Field_Type_Email extends \BP_XProfile_Field_Type {

	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Email (HTML5 field)', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		do_action( 'bp_xprofile_field_type_email', $this );
	}

	public function edit_field_html( array $raw_properties = array() ) {

		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements( array_merge(
			array(
				'type'  => 'email',
				'value' => bp_get_the_profile_field_edit_value(),
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

        <input <?php echo $html; ?> />

		<?php if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	public function admin_field_html( array $raw_properties = array() ) {

		$html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'email' ),
			$raw_properties
		) );
		?>

        <input <?php echo $html; ?> />

		<?php
	}

	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {
	}

	/**
     * Check if it is a valid email.
     *
	 * @param string $values value.
	 *
	 * @return bool
	 */
	public function is_valid( $values ) {
	    return empty( $values ) || is_email( $values );
	}

	/**
	 * Modify the appearance of value.
	 *
	 * @param  string $field_value Original value of field.
	 * @param  int    $field_id Id of field.
	 *
	 * @return string   Value formatted
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {
	    return empty( $field_value ) ? '' : sprintf( '<a href="mailto:%1$s" rel="nofollow">%1$s</a>', $field_value );
	}
}

