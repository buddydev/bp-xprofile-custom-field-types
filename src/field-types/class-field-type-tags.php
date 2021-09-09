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

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Multi select tags field type
 */
class Field_Type_Tags extends \BP_XProfile_Field_Type_Textbox {

	/**
	 * Constructor for the tags field type.
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );
		$this->name     = _x( 'Tags', 'xprofile field type', 'bp-xprofile-custom-field-types' );

		$this->supports_multiple_defaults = false;
		$this->accepts_null_value         = true;
		$this->supports_options           = false;

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
	    $type = array_search( get_class( $this ), bp_xprofile_get_field_types(), false );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';
		$text  = join( ',', self::get_default_tags( $current_field->id ) );

		// phpcs:disable
		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box"
             style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
            <h3><?php esc_html_e( 'Default tags List(separate tags by comma(,) eg. One,Two etc):', 'bp-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
                <p>
                    <textarea name="bpxcftr_tags_default_tags" id="bpxcftr_tags_default_tags" rows="5" cols="60"><?php echo $text; ?></textarea>
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="bpxcftr_tags_allow_new_tags" id="bpxcftr_tags_allow_new_tags" value="1" <?php checked(true, self::allow_new_tags( $current_field->id ) );?> />
			            <?php _e( 'Allow users to add new tags( If you tick this option, please make sure to <strong>enable select2</strong>strong> from right sidebar).', 'bp-xprofile-custom-field-types' ); ?>
                    </label>
                </p>
            </div>
        </div>
        <?php
        // phpcs:enable
	}

	/**
	 * Output the edit field HTML for this field type.
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param array $raw_properties Optional key/value array of
	 *                              {@link http://dev.w3.org/html5/markup/input.text.html permitted attributes}
	 *                              that you want to add.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		} else {
			$user_id = bp_displayed_user_id();
		}

		$r = bp_parse_args(
			$raw_properties,
			array(
				'multiple' => 'multiple',
				'id'       => bp_get_the_profile_field_input_name() . '[]',
				'name'     => bp_get_the_profile_field_input_name() . '[]',
			)
		);

		$field_id = bp_get_the_profile_field_id();

		$default_tags = self::get_default_tags( bp_get_the_profile_field_id() );
		$user_tags    = xprofile_get_field_data( $field_id, $user_id );
		$user_tags    = $user_tags ? $user_tags : array();

		$tags = array_unique( array_map( 'trim', array_merge( $default_tags, $user_tags ) ) );

		$options = array();

		if ( ! $default_tags && ! $user_tags ) {
			//$options[''] = __( '', 'bp-xprofile-custom-field-types' );
		}

		$options = array_merge( $options, array_combine( $tags, $tags ) );

		?>

        <legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
        </legend>

		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>

        <select <?php echo $this->get_edit_field_html_elements( $r ); ?> aria-labelledby="<?php bp_the_profile_field_input_name(); ?>-1" aria-describedby="<?php bp_the_profile_field_input_name(); ?>-3">
			<?php foreach ( $options as $value => $option ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( in_array( $value, $user_tags ), true )?>><?php echo esc_html( $option ); ?></option>
            <?php endforeach; ?>
        </select>

		<?php if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php if ( ! bp_get_the_profile_field_is_required() ) : ?>

            <a class="clear-value" href="javascript:clear( '<?php echo esc_js( bp_get_the_profile_field_input_name() ); ?>[]' );">
				<?php esc_html_e( 'Clear', 'buddypress' ); ?>
            </a>

		<?php endif; ?>

		<?php
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
	 * Get default tags
	 *
	 * @param int $field_id field id.
	 *
	 * @return array
	 */
	public static function get_default_tags( $field_id ) {

		if ( ! $field_id ) {
			return array();
		}

		$default_tags = bp_xprofile_get_meta( $field_id, 'field', 'default_tags', true );

		if ( empty( $default_tags ) ) {
			return array();
		}

		return array_map( 'trim', explode( ',', $default_tags ) );
	}
}
