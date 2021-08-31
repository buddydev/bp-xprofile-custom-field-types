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

use BP_XProfile_Field;
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

	/**
	 * Output HTML for this field type on the wp-admin Profile Fields screen.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param BP_XProfile_Field $current_field Current field object.
	 * @param string            $control_type Current field object.
	 */
	public function admin_new_field_html( $current_field, $control_type = '' ) {
		add_action( 'bp_xprofile_admin_new_field_additional_settings', array( $this, 'add_settings' ) );

		parent::admin_new_field_html( $current_field, $control_type );
	}

	/**
	 * I new term creation allowed?
	 *
	 * @param int $field_id field id.
	 *
	 * @return bool
	 */
	public static function allow_new_tags( $field_id ) {

		if ( ! $field_id ) {
			return false;
		}

		return (bool) bp_xprofile_get_meta( $field_id, 'field', 'allow_new_tags', true );
	}

	/**
	 * Add additional field settings.
	 *
	 * @param BP_XProfile_Field $current_field Current field object.
	 */
	public function add_settings( BP_XProfile_Field $current_field ) {

		if ( 'tags' != $current_field->type ) {
			return;
		}

        ?>
        <p>
            <label>
                <input type="checkbox" name="bpxcftr_tags_allow_new_tags" id="bpxcftr_tags_allow_new_tags" value="1" <?php checked(true, self::allow_new_tags( $current_field->id ) );?> />
				<?php _e( 'Allow users to add new tags', 'bp-xprofile-custom-field-types' ); ?>
            </label>
        </p>
        <?php
	}
}
