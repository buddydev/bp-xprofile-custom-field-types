<?php
/**
 * File field type
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
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
 * File Type
 */
class Field_Type_File extends \BP_XProfile_Field_Type {

	public function __construct() {
		parent::__construct();

		$this->name     = _x( 'File', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->set_format( '/^.+$/', 'replace' );
		do_action( 'bp_xprofile_field_type_file', $this );
	}

	public function edit_field_html( array $raw_properties = array() ) {
		global $field;

		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'file' ),
			$raw_properties
		) );

		$value = is_user_logged_in() ? bp_get_the_profile_field_value() : '';
		$name  = bp_get_the_profile_field_input_name();

		$has_file = false;
		// for backward compatibility, check against '-'.
		if ( $value && $value != '-' ) {
			$has_file = true;
		}

		$edit_value = isset( $field->data ) && ! empty( $field->data->value ) ? $field->data->value : '-';

		?>

        <legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
        </legend>

		<?php
		do_action( bp_get_the_profile_field_errors_action() );
		?>

        <input <?php echo $html; ?> />

		<?php if ( $has_file ) : ?>
            <p>
				<?php echo $value; ?>
            </p>

            <label>
                <input type="checkbox" name="<?php echo $name; ?>_delete" value="1"/> <?php _e( 'Check this to delete this file', 'bp-xprofile-custom-field-types' ); ?>
            </label>
		<?php endif; ?>

        <input type="hidden" value="<?php echo esc_attr( $edit_value ); ?>" name="<?php echo esc_attr( $name ); ?>"/>

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

	    $html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'file' ),
			$raw_properties
		) );
		?>

        <input <?php echo $html; ?> />

		<?php
	}

	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {
	}

	/**
	 * Modify the appearance of value.
	 *
	 * @param  string $field_value Original value of field.
	 * @param  int $field_id Id of field.
	 *
	 * @return string   Value formatted
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {

	    if ( empty( $field_value ) ) {
			return '';
		}

		$field_value = trim( $field_value, '/\\' );// no absolute path or dir please.
		// the BP Xprofile Custom Fields type stored '/path' which was a bad decision
		// we are using the above line for back compatibility with them.

		$uploads = wp_upload_dir();

		$new_field_value = trailingslashit( $uploads['baseurl'] ) . $field_value;

		$new_field_value = sprintf( '<a href="%s" rel="nofollow" class="bpxcftr-file-link">%s</a>', esc_url( $new_field_value ), __( 'Download file', 'bp-xprofile-custom-field-types' ) );

		return apply_filters( 'bpxcftr_file_display_data', $new_field_value, $field_id );
	}
}
