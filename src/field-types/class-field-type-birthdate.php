<?php
/**
 * BuddyPress Xprofile Custom Field Types Birthdate field type
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Birthdate field
 */
class Field_Type_Birthdate extends \BP_XProfile_Field_Type_Datebox {

    /**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Birthdate Selector', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		do_action( 'bp_xprofile_field_type_birthdate', $this );
	}

	/**
	 * Generate the settings markup for Date fields.
	 *
	 * This is in overridden version of parent::admin_new_field_html() since there was no other way to inject extra info.
	 *
	 * @param \BP_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string             $control_type  Optional. HTML input type used to render the current
	 *                                         field's child options.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

	    $type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class       = $current_field->type != $type ? 'display: none;' : '';
		$show_age    = self::show_age( $current_field->id ) ? 1 : 0;
		$min_age     = self::get_min_age( $current_field->id );
		$age_label   = self::get_age_label( $current_field->id );
		$hide_months = self::hide_months( $current_field->id );
		// settings from date field.
		$settings = self::get_field_settings( $current_field->id );

		?>

        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
            <table class="form-table bp-date-options bpxcftr-date-options">
                <tr>
                    <th scope="row">
						<?php esc_html_e( 'Date format', 'bp-xprofile-custom-field-types' ); ?>
                    </th>

                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
								<?php esc_html_e( 'Date format', 'bp-xprofile-custom-field-types' ); ?>
                            </legend>

							<?php foreach ( $this->get_date_formats() as $format ): ?>
                                <div class="bp-date-format-option">
                                    <label for="bpxcftr-birthdate-date-format-<?php echo esc_attr( $format ); ?>">
                                        <input type="radio" name="field-settings[bpxcftr_birthdate_date_format]" id="bpxcftr-birthdate-date-format-<?php echo esc_attr( $format ); ?>" value="<?php echo esc_attr( $format ); ?>" <?php checked( $format, $settings['date_format'] ); ?> />
                                        <span class="date-format-label"><?php echo date_i18n( $format ); ?></span>
                                        <code><?php echo esc_html( $format ); ?></code>
                                    </label>
                                </div>
							<?php endforeach;?>

                            <div class="bp-date-format-option">
                                <label for="bpxcftr-birthdate-date-format-elapsed">
                                    <input type="radio" name="field-settings[bpxcftr_birthdate_date_format]" id="bpxcftr-birthdate-date-format-elapsed" <?php checked( 'elapsed', $settings['date_format'] ); ?> value="elapsed" aria-describedby="bpxcftr-birthdate-date-format-elapsed-setting" />
                                    <span class="date-format-label" id="bpxcftr-birthdate-date-format-elapsed-setting"><?php esc_html_e( 'Time elapsed', 'bp-xprofile-custom-field-types' ); ?></span> <?php _e( '<code>4 years ago</code>, <code>4 years from now</code>', 'bp-xprofile-custom-field-types' ); ?>
                                </label>
                            </div>

                            <div class="bp-date-format-option">
                                <label for="bpxcftr-birthdate-date-format-custom">
                                    <input type="radio" name="field-settings[bpxcftr_birthdate_date_format]" id="bpxcftr-birthdate-date-format-custom" <?php checked( 'custom', $settings['date_format'] ); ?> value="custom" />
                                    <span class="date-format-label"><?php esc_html_e( 'Custom:', 'bp-xprofile-custom-field-types' ); ?></span>
                                </label>
                                <label for="bpxcftr-birthdate-date-format-custom-value" class="screen-reader-text"><?php esc_html_e( 'Enter custom time format', 'bp-xprofile-custom-field-types' ); ?></label>
                                <input type="text" name="field-settings[date_format_custom]" id="bpxcftr-birthdate-date-format-custom-value" class="date-format-custom-value" value="<?php echo esc_attr( $settings['date_format_custom'] ); ?>" aria-describedby="bpxcftr-birthdate-date-format-custom-example" /> <span class="screen-reader-text"><?php esc_html_e( 'Example:', 'bp-xprofile-custom-field-types' ); ?></span><span class="date-format-custom-example" id="date-format-custom-sample"><?php if ( $settings['date_format_custom'] ) : ?><?php echo esc_html( date_i18n( $settings['date_format_custom'] ) ); endif; ?></span><span class="spinner" id="bpxcftr-birthdate-date-format-custom-spinner" aria-hidden="true"></span>

                                <p><a href="https://codex.wordpress.org/Formatting_Date_and_Time"><?php esc_html_e( 'Documentation on date and time formatting', 'bp-xprofile-custom-field-types' ); ?></a></p>
                            </div>

                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
						<?php esc_html_e( 'Range', 'bp-xprofile-custom-field-types' ); ?>
                    </th>

                    <td>
                        <fieldset class="bp-range-types">
                            <legend class="screen-reader-text">
								<?php esc_html_e( 'Range', 'bp-xprofile-custom-field-types' ); ?>
                            </legend>

                            <div class="bp-date-format-option">
                                <div class="bp-date-range-type-label">
                                    <label for="bpxcftr-birthdate-range-type-absolute">
                                        <input type="radio" name="field-settings[bpxcftr_birthdate_range_type]" id="bpxcftr-birthdate-range-type-absolute" value="absolute" <?php checked( 'absolute', $settings['range_type'] ); ?> />
										<?php esc_html_e( 'Absolute', 'bp-xprofile-custom-field-types' ); ?>
                                    </label>
                                </div>

                                <div class="bp-date-range-type-values">
                                    <label for="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_absolute_start]" aria-label="Year"><?php esc_html_e( 'Start:', 'bp-xprofile-custom-field-types' ); ?></label>
									<?php printf( '<input class="date-range-numeric" type="text" name="field-settings[bpxcftr_birthdate_range_absolute_start]" id="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_absolute_start]" value="%s" />', esc_attr( $settings['range_absolute_start'] ) ); ?>
                                    <label for="bpxcftr-birthdate-field-settings[range_absolute_end]" aria-label="Year"><?php esc_html_e( 'End:', 'bp-xprofile-custom-field-types' ); ?></label>
									<?php printf( '<input class="date-range-numeric" type="text" name="field-settings[bpxcftr_birthdate_range_absolute_end]" id="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_absolute_end]" value="%s" />', esc_attr( $settings['range_absolute_end'] ) ); ?>
                                </div>
                            </div>

                            <div class="bp-date-format-option">
                                <div class="bp-date-range-type-label">
                                    <label for="bpxcftr-birthdate-range_type_relative">
                                        <input type="radio" name="field-settings[bpxcftr_birthdate_range_type]" id="bpxcftr-birthdate-range_type_relative" value="relative" <?php checked( 'relative', $settings['range_type'] ); ?> />
										<?php esc_html_e( 'Relative', 'bp-xprofile-custom-field-types' ); ?>
                                    </label>
                                </div>

                                <div class="bp-date-range-type-values">
                                    <label for="bpxcftr-birthdate-field-settings[range_relative_start]"><?php esc_html_e( 'Start:', 'bp-xprofile-custom-field-types' ); ?></label>
									<?php printf( '<input type="text" class="date-range-numeric" name="field-settings[bpxcftr_birthdate_range_relative_start]" id="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_relative_start]" value="%s" />',
										esc_attr( abs( $settings['range_relative_start'] ) )
									);
									?>

                                    <label class="screen-reader-text" for="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_relative_start_type]"><?php esc_html_e( 'Select range', 'bp-xprofile-custom-field-types' ); ?></label>
									<?php printf( '<select name="field-settings[bpxcftr_birthdate_range_relative_start_type]" id="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_relative_start_type]"><option value="past" %s>%s</option><option value="future" %s>%s</option></select>',
										selected( true, $settings['range_relative_start'] <= 0, false ),
										esc_attr__( 'years ago', 'bp-xprofile-custom-field-types' ),
										selected( true, $settings['range_relative_start'] > 0, false ),
										esc_attr__( 'years from now', 'bp-xprofile-custom-field-types' )
									);
									?>

                                    <label for="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_relative_end]"><?php esc_html_e( 'End:', 'bp-xprofile-custom-field-types' ); ?></label>
									<?php printf( '<input type="text" class="date-range-numeric" name="field-settings[bpxcftr_birthdate_range_relative_end]" id="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_relative_end]" value="%s" />',
										esc_attr( abs( $settings['range_relative_end'] ) )
									);
									?>
                                    <label class="screen-reader-text" for="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_relative_end_type]"><?php esc_html_e( 'Select range', 'bp-xprofile-custom-field-types' ); ?></label>
									<?php printf( '<select name="field-settings[bpxcftr_birthdate_range_relative_end_type]" id="bpxcftr-birthdate-field-settings[bpxcftr_birthdate_range_relative_end_type]"><option value="past" %s>%s</option><option value="future" %s>%s</option></select>',
										selected( true, $settings['range_relative_end'] <= 0, false ),
										esc_attr__( 'years ago', 'bp-xprofile-custom-field-types' ),
										selected( true, $settings['range_relative_end'] > 0, false ),
										esc_attr__( 'years from now', 'bp-xprofile-custom-field-types' )
									);
									?>
                                </div>
                            </div>

                        </fieldset>
                    </td>
                </tr>
            </table>
            <h3><?php esc_html_e( 'Show age:', 'bp-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
                <p>
			        <?php _e( 'Check this if you want to show age instead of birthdate:', 'bp-xprofile-custom-field-types' ); ?>

                    <input type="checkbox" name="bpxcftr_birtdate_show_age" id="bpxcftr_birtdate_show_age" value="1" <?php checked(1, $show_age ); ?>/>
                </p>
                <p>
                    <label for="bpxcftr_birthdate_age_label">
	                    <?php _e( 'Display Label for age:', 'bp-xprofile-custom-field-types' );?>
                        <input type="text" placeholder="<?php esc_attr_e( 'e.g Age.', 'bp-xprofile-custom-field-types') ;?>" name="bpxcftr_birthdate_age_label"  value="<?php echo esc_attr( $age_label );?>"/>
                    </label>
                </p>
                <p>
		            <?php _e( 'Hide months while showing age?', 'bp-xprofile-custom-field-types' ); ?>

                    <input type="checkbox" name="bpxcftr_birtdate_hide_months" id="bpxcftr_birtdate_hide_months" value="1" <?php checked(1, $hide_months ); ?>/>
                </p>
            </div>

            <h3><?php esc_html_e( 'Define a minimum age:', 'bp-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
                <p>
			        <?php _e( 'Minimum age:', 'bp-xprofile-custom-field-types' ); ?>
                    <input type="number" name="bpxcftr_birtdate_min_age" id="bpxcftr_birtdate_min_age" min="1" value="<?php echo esc_attr( $min_age );?>"/>
                </p>
            </div>
        </div>
		<?php
	}


	/**
	 * Display formatting.
	 *
	 * @param string $field_value field value.
	 * @param int    $field_id field id.
	 *
	 * @return string
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {

		if ( empty( $field_value ) ) {
			return $field_value;
		}

		if ( ! self::show_age( $field_id ) ) {
			return parent::display_filter( $field_value, $field_id );
		}
		// If Unix timestamp.
		if ( ! is_numeric( $field_value ) ) {
			$field_value = strtotime( $field_value );
		}

		$now = new \DateTime();

		$birthdate    = new \DateTime( "@$field_value" );
		$age_interval = $now->diff( $birthdate );

		$age = sprintf( __( '%s years', 'bp-xprofile-custom-field-types' ), $age_interval->y );

		if ( $age_interval->m && ! self::hide_months( $field_id ) ) {
			$age .= sprintf( _n( ', %s month', ', %s months', $age_interval->m, 'bp-xprofile-custom-field-types' ), $age_interval->m );
		}

		return apply_filters( 'bpxcftr_birthdate_age_display_data', $age, $field_value, $age_interval, $field_id );
	}

	/**
	 * Checks if we should hide months in display.
	 *
	 * @param int $field_id field id.
	 *
	 * @return bool
	 */
	public static function hide_months( $field_id ) {
		return (bool) bp_xprofile_get_meta( $field_id, 'field', 'hide_months', true );
	}

	/**
	 * Checks if we should show age in display.
	 *
	 * @param int $field_id field id.
	 *
	 * @return bool
	 */
	public static function show_age( $field_id ) {
		return (bool) bp_xprofile_get_meta( $field_id, 'field', 'show_age', true );
	}

	/**
	 * Returns minimum age needed.
	 *
	 * @param int $field_id field id.
	 *
	 * @return int
	 */
	public static function get_min_age( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'min_age', true );
	}

	/**
	 * Get age label.
	 *
	 * @param int $field_id field id.
	 *
	 * @return string
	 */
	public static function get_age_label( $field_id ) {
		return bp_xprofile_get_meta( $field_id, 'field', 'age_label', true );
	}


	/**
	 * Save settings from the field edit screen in the Dashboard.
	 *
	 * @param int   $field_id ID of the field.
	 * @param array $settings Array of settings posted($_POST['field-settings'] ).
	 * @return bool True on success.
	 */
	public function admin_save_settings( $field_id, $settings ) {
		$existing_settings = self::get_field_settings( $field_id );

		$saved_settings = array();
		$prefix         = 'bpxcftr_birthdate_';

		foreach ( array_keys( $existing_settings ) as $setting ) {
			$key = $prefix . $setting;

			switch ( $setting ) {
				case 'range_relative_start':
				case 'range_relative_end':
					$op_key = $key . '_type';
					if ( isset( $settings[ $op_key ] ) && 'past' === $settings[ $op_key ] ) {
						$value = 0 - intval( $settings[ $key ] );
					} else {
						$value = intval( $settings[ $key ] );
					}

					$saved_settings[ $setting ] = $value;
					break;

				default:
					if ( isset( $settings[ $key ] ) ) {
						$saved_settings[ $setting ] = $settings[ $key ];
					}
					break;
			}
		}

		// Sanitize and validate saved settings.
		$saved_settings = self::validate_settings( $saved_settings );

		foreach ( $saved_settings as $setting_key => $setting_value ) {
			bp_xprofile_update_meta( $field_id, 'field', $setting_key, $setting_value );
		}

		return true;
	}

}
