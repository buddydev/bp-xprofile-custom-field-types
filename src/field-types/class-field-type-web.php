<?php
/**
 * Web Type Field
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
 * Web Type
 */
class Field_Type_Web extends \BP_XProfile_Field_Type_URL {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->name     = _x( 'Website (HTML5 field)', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );
	}

	/**
	 * This method usually outputs HTML for this field type's children options
	 * on the wp-admin Profile Fields "Add Field" and "Edit Field" screens, but
	 * for this field type, we don't want it, so it's stubbed out.
	 *
	 * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string            $control_type  Optional. HTML input type used to render the current
	 *                                         field's child options.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';

		$is_checked = self::get_link_target( $current_field->id ) === '_blank';
		?>


		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">

			<h3><?php esc_html_e( 'Link options:-', 'bp-xprofile-custom-field-types' ); ?></h3>
			<div class="inside">
				<p>
					<label>
						<?php esc_html_e( 'Open link in new window?', 'bp-xprofile-custom-field-types' ); ?>
					</label>
					<label for="bpxcftr_web_link_target">
						<input type="checkbox" name="bpxcftr_web_link_target" id="bpxcftr_web_link_target" value="_blank" <?php checked(true, $is_checked );?> /> <?php __( 'Yes', 'bp-xprofile-custom-field-types' ); ?>
					</label>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Format URL values for display.
	 *
	 * @param string     $field_value The URL value, as saved in the database.
	 * @param string|int $field_id    Optional. ID of the field.
	 * @return string URL converted to a link.
	 */
	public static function display_filter( $field_value, $field_id = '' ) {
		$link   = strip_tags( $field_value );
		$target = self::get_link_target( $field_id ) ? 'target="_blank"' : '';

		return sprintf( '<a href="%1$s" rel="nofollow" %3$s>%2$s</a>', esc_url( $field_value ), esc_html( $link ), $target );
	}

	/**
	 * Get the link target
	 *
	 * @param int $field_id field id.
	 *
	 * @return string
	 */
	public static function get_link_target( $field_id ) {

		if ( ! $field_id ) {
			return '';
		}

		return bp_xprofile_get_meta( $field_id, 'field', 'link_target', true );
	}
}
