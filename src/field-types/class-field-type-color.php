<?php
/**
 * Color field
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
 * Color type.
 */
class Field_Type_Color extends \BP_XProfile_Field_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Color (HTML5 field)', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->set_format( '/^.+$/', 'replace' );
		do_action( 'bp_xprofile_field_type_color', $this );
	}

	/**
	 * Edit profile field/register page.
	 *
	 * @param array $raw_properties props.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		// reset user_id.
		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$html = $this->get_edit_field_html_elements(
			array_merge(
				array(
					'type'  => 'color',
					'value' => bp_get_the_profile_field_edit_value(),
					'class' => 'bpxcftr-color',
				),
				$raw_properties
			)
		);
		?>

		<legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>

        <input <?php echo $html; ?>>

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

		$html = $this->get_edit_field_html_elements(
			array_merge(
				array( 'type' => 'color' ),
				$raw_properties
			)
		);
		?>

        <input <?php echo $html; ?>>
		<?php
	}

	/**
	 * Dashboard->Users->Profile Fields->New|Edit entry.
	 *
	 * @param \BP_XProfile_Field $current_field object.
	 * @param string             $control_type type.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {
	}
}
