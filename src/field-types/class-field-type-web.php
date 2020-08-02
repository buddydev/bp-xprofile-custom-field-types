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
}
