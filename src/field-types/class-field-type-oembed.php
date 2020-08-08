<?php
/**
 * oEmbed Type Field
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
 * Oembed field Type
 */
class Field_Type_Oembed extends \BP_XProfile_Field_Type_URL {

	/**
	 * Field_Type_Oembed constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->name     = _x( 'oEmbed URL', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );
	}

	/**
	 * Format URL values for display.
	 *
	 * @param string     $field_value The URL value, as saved in the database.
	 * @param string|int $field_id Optional. ID of the field.
	 *
	 * @return string URL converted to  oembed content.
	 */
	public static function display_filter( $field_value, $field_id = '' ) {
		// can not cache oEmbed response currently. We will need either data id or user id to allow us caching.
		if ( ! empty( $field_value ) ) {
			return wp_oembed_get( $field_value );
		}

		return $field_value;
	}
}
