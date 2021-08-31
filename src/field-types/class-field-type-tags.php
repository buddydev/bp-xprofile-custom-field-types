<?php
/**
 * Field type to select among custom predefined tags or user can create from front-end
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2019, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh, Ravi Sharma
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

use BP_XProfile_Field_Type_Multiselectbox;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Multi select tags field type
 */
class Field_Type_Tags extends BP_XProfile_Field_Type_Multiselectbox {

	/**
	 * Constructor for the tags field type.
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );
		$this->name     = _x( 'Tags', 'xprofile field type', 'bp-xprofile-custom-field-types' );

		$this->supports_multiple_defaults = true;
		$this->accepts_null_value         = true;
		$this->supports_options           = true;

		$this->set_format( '/^.+$/', 'replace' );

		/**
		 * Fires inside __construct() method for Field_Type_Tags class.
		 *
		 * @param Field_Type_Tags $this Current instance of the field type tags.
		 */
		do_action( 'bp_xprofile_field_type_tags', $this );
	}
}
