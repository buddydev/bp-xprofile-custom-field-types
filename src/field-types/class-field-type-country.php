<?php
/**
 * Country Type Field.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Field_Types
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

use BPXProfileCFTR\Contracts\Field_Type_Selectable;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Country Profile Field Type
 */
class Field_Type_Country extends \BP_XProfile_Field_Type implements Field_Type_Selectable {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->name     = _x( 'Country Selector', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->supports_options = false;

		do_action( 'bp_xprofile_field_type_country', $this );
	}

	/**
	 * Edit field html.
	 *
	 * @param array $raw_properties properties.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		$user_id = bp_displayed_user_id();

		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		}

		$atts = $this->get_edit_field_html_elements( $raw_properties );
		?>

		<legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>

	   <select <?php echo $atts; ?>>
			<option value=""><?php _e( 'Select...', 'bp-xprofile-custom-field-types' ); ?></option>
			<?php bp_the_profile_field_options( "user_id={$user_id}" ); ?>
		</select>

		<?php if ( bp_get_the_profile_field_description() ) : ?>
            <p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
	 * Field Options(dropdown).
	 *
	 * @param array $args args.
	 */
	public function edit_field_options_html( array $args = array() ) {
		global $field;

		$country_selected       = self::get_default_selected_country( $field->id );
		$user_selected_country  = \BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] );

		// fallback to default.
		if ( ! $user_selected_country ) {
			$user_selected_country = $country_selected;
		}

		$html = '';

		if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
			$new_selected_country  = (int) $_POST[ 'field_' . $this->field_obj->id ];
			$user_selected_country = ( $user_selected_country != $new_selected_country ) ? $new_selected_country : $user_selected_country;
		}

		// Get countries of custom post type selected.
		$countries = self::get_countries();

		if ( $countries ) {
			foreach ( $countries as $country_code => $country_name ) {
				$html .= sprintf(
					'<option value="%1$s" %2$s>%3$s</option>',
					$country_code,
					$user_selected_country === $country_code ? ' selected="selected"' : '',
					$country_name
				);
			}
		}

		echo apply_filters( 'bp_get_the_profile_field_country', $html, $args['type'], $country_selected, $this->field_obj->id );
	}

	/**
	 * Dashboard->Users->Profile Fields entry.
	 *
	 * @param array $raw_properties properties.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		$atts = $this->get_edit_field_html_elements( $raw_properties );
		?>
        <select <?php echo $atts; ?>>
			<?php bp_the_profile_field_options(); ?>
        </select>
		<?php
	}

	/**
	 * Dashboard->Users->Profile Fields->New|Edit entry.
	 *
	 * @param \BP_XProfile_Field $current_field object.
	 * @param string             $control_type type.
	 */
	public function admin_new_field_html( \BP_XProfile_Field $current_field, $control_type = '' ) {

		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );

		if ( false === $type ) {
			return;
		}

		$class = $current_field->type != $type ? 'display: none;' : '';

		$selected_country = self::get_default_selected_country( $current_field->id );

		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
                <h3><?php esc_html_e( 'Select country:', 'bp-xprofile-custom-field-types' ); ?></h3>
                <div class="inside">
                    <p>
						<?php _e( 'Select country:', 'bp-xprofile-custom-field-types' ); ?>
                        <select name="bpxcftr_default_country" id="bpxcftr_default_country">
                            <option value=""><?php _e( 'Select...', 'bp-xprofile-custom-field-types' ); ?></option>
							<?php foreach ( self::get_countries() as $country_code => $country ): ?>
                                <option value="<?php echo $country_code; ?>" <?php selected( $selected_country, $country_code, true ); ?>><?php echo $country; ?></option>
							<?php endforeach; ?>
                        </select>
                    </p>
                </div>
        </div>
		<?php
	}

	/**
	 * Check if valid.
	 *
	 * @param string $value Country code.
	 *
	 * @return bool
	 */
	public function is_valid( $value ) {
		return $value && array_key_exists( $value, self::get_countries() );
	}

	/**
	 * Filter value.
	 *
	 * @param mixed  $field_value value.
	 * @param string $field_id field id.
	 *
	 * @return string
	 */
	public static function display_filter( $field_value, $field_id = '' ) {

		if ( empty( $field_value ) ) {
			return '';
		}

		$countries = self::get_countries();

		if ( ! isset( $countries[ $field_value ] ) ) {
			return '';
		}

		return $countries[ $field_value ];
	}

	/**
	 * Get the terms content.
	 *
	 * @param int $field_id field id.
	 *
	 * @return string
	 */
	private static function get_default_selected_country( $field_id ) {

		if ( ! $field_id ) {
			return '';
		}

		return bp_xprofile_get_meta( $field_id, 'field', 'default_country', true );
	}

	/**
	 * Get countries
	 *
	 * @return array
	 */
	private static function get_countries() {
		return array(
			'AF' => __( 'Afghanistan', 'bp-xprofile-custom-field-types' ),
			'AX' => __( 'Åland Islands', 'bp-xprofile-custom-field-types' ),
			'AL' => __( 'Albania', 'bp-xprofile-custom-field-types' ),
			'DZ' => __( 'Algeria', 'bp-xprofile-custom-field-types' ),
			'AS' => __( 'American Samoa', 'bp-xprofile-custom-field-types' ),
			'AD' => __( 'Andorra', 'bp-xprofile-custom-field-types' ),
			'AO' => __( 'Angola', 'bp-xprofile-custom-field-types' ),
			'AI' => __( 'Anguilla', 'bp-xprofile-custom-field-types' ),
			'AQ' => __( 'Antarctica', 'bp-xprofile-custom-field-types' ),
			'AG' => __( 'Antigua and Barbuda', 'bp-xprofile-custom-field-types' ),
			'AR' => __( 'Argentina', 'bp-xprofile-custom-field-types' ),
			'AM' => __( 'Armenia', 'bp-xprofile-custom-field-types' ),
			'AW' => __( 'Aruba', 'bp-xprofile-custom-field-types' ),
			'AU' => __( 'Australia', 'bp-xprofile-custom-field-types' ),
			'AT' => __( 'Austria', 'bp-xprofile-custom-field-types' ),
			'AZ' => __( 'Azerbaijan', 'bp-xprofile-custom-field-types' ),
			'BS' => __( 'Bahamas', 'bp-xprofile-custom-field-types' ),
			'BH' => __( 'Bahrain', 'bp-xprofile-custom-field-types' ),
			'BD' => __( 'Bangladesh', 'bp-xprofile-custom-field-types' ),
			'BB' => __( 'Barbados', 'bp-xprofile-custom-field-types' ),
			'BY' => __( 'Belarus', 'bp-xprofile-custom-field-types' ),
			'BE' => __( 'Belgium', 'bp-xprofile-custom-field-types' ),
			'PW' => __( 'Belau', 'bp-xprofile-custom-field-types' ),
			'BZ' => __( 'Belize', 'bp-xprofile-custom-field-types' ),
			'BJ' => __( 'Benin', 'bp-xprofile-custom-field-types' ),
			'BM' => __( 'Bermuda', 'bp-xprofile-custom-field-types' ),
			'BT' => __( 'Bhutan', 'bp-xprofile-custom-field-types' ),
			'BO' => __( 'Bolivia', 'bp-xprofile-custom-field-types' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'bp-xprofile-custom-field-types' ),
			'BA' => __( 'Bosnia and Herzegovina', 'bp-xprofile-custom-field-types' ),
			'BW' => __( 'Botswana', 'bp-xprofile-custom-field-types' ),
			'BV' => __( 'Bouvet Island', 'bp-xprofile-custom-field-types' ),
			'BR' => __( 'Brazil', 'bp-xprofile-custom-field-types' ),
			'IO' => __( 'British Indian Ocean Territory', 'bp-xprofile-custom-field-types' ),
			'BN' => __( 'Brunei', 'bp-xprofile-custom-field-types' ),
			'BG' => __( 'Bulgaria', 'bp-xprofile-custom-field-types' ),
			'BF' => __( 'Burkina Faso', 'bp-xprofile-custom-field-types' ),
			'BI' => __( 'Burundi', 'bp-xprofile-custom-field-types' ),
			'KH' => __( 'Cambodia', 'bp-xprofile-custom-field-types' ),
			'CM' => __( 'Cameroon', 'bp-xprofile-custom-field-types' ),
			'CA' => __( 'Canada', 'bp-xprofile-custom-field-types' ),
			'CV' => __( 'Cape Verde', 'bp-xprofile-custom-field-types' ),
			'KY' => __( 'Cayman Islands', 'bp-xprofile-custom-field-types' ),
			'CF' => __( 'Central African Republic', 'bp-xprofile-custom-field-types' ),
			'TD' => __( 'Chad', 'bp-xprofile-custom-field-types' ),
			'CL' => __( 'Chile', 'bp-xprofile-custom-field-types' ),
			'CN' => __( 'China', 'bp-xprofile-custom-field-types' ),
			'CX' => __( 'Christmas Island', 'bp-xprofile-custom-field-types' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'bp-xprofile-custom-field-types' ),
			'CO' => __( 'Colombia', 'bp-xprofile-custom-field-types' ),
			'KM' => __( 'Comoros', 'bp-xprofile-custom-field-types' ),
			'CG' => __( 'Congo (Brazzaville)', 'bp-xprofile-custom-field-types' ),
			'CD' => __( 'Congo (Kinshasa)', 'bp-xprofile-custom-field-types' ),
			'CK' => __( 'Cook Islands', 'bp-xprofile-custom-field-types' ),
			'CR' => __( 'Costa Rica', 'bp-xprofile-custom-field-types' ),
			'HR' => __( 'Croatia', 'bp-xprofile-custom-field-types' ),
			'CU' => __( 'Cuba', 'bp-xprofile-custom-field-types' ),
			'CW' => __( 'Cura&ccedil;ao', 'bp-xprofile-custom-field-types' ),
			'CY' => __( 'Cyprus', 'bp-xprofile-custom-field-types' ),
			'CZ' => __( 'Czech Republic', 'bp-xprofile-custom-field-types' ),
			'DK' => __( 'Denmark', 'bp-xprofile-custom-field-types' ),
			'DJ' => __( 'Djibouti', 'bp-xprofile-custom-field-types' ),
			'DM' => __( 'Dominica', 'bp-xprofile-custom-field-types' ),
			'DO' => __( 'Dominican Republic', 'bp-xprofile-custom-field-types' ),
			'EC' => __( 'Ecuador', 'bp-xprofile-custom-field-types' ),
			'EG' => __( 'Egypt', 'bp-xprofile-custom-field-types' ),
			'SV' => __( 'El Salvador', 'bp-xprofile-custom-field-types' ),
			'GQ' => __( 'Equatorial Guinea', 'bp-xprofile-custom-field-types' ),
			'ER' => __( 'Eritrea', 'bp-xprofile-custom-field-types' ),
			'EE' => __( 'Estonia', 'bp-xprofile-custom-field-types' ),
			'ET' => __( 'Ethiopia', 'bp-xprofile-custom-field-types' ),
			'FK' => __( 'Falkland Islands', 'bp-xprofile-custom-field-types' ),
			'FO' => __( 'Faroe Islands', 'bp-xprofile-custom-field-types' ),
			'FJ' => __( 'Fiji', 'bp-xprofile-custom-field-types' ),
			'FI' => __( 'Finland', 'bp-xprofile-custom-field-types' ),
			'FR' => __( 'France', 'bp-xprofile-custom-field-types' ),
			'GF' => __( 'French Guiana', 'bp-xprofile-custom-field-types' ),
			'PF' => __( 'French Polynesia', 'bp-xprofile-custom-field-types' ),
			'TF' => __( 'French Southern Territories', 'bp-xprofile-custom-field-types' ),
			'GA' => __( 'Gabon', 'bp-xprofile-custom-field-types' ),
			'GM' => __( 'Gambia', 'bp-xprofile-custom-field-types' ),
			'GE' => __( 'Georgia', 'bp-xprofile-custom-field-types' ),
			'DE' => __( 'Germany', 'bp-xprofile-custom-field-types' ),
			'GH' => __( 'Ghana', 'bp-xprofile-custom-field-types' ),
			'GI' => __( 'Gibraltar', 'bp-xprofile-custom-field-types' ),
			'GR' => __( 'Greece', 'bp-xprofile-custom-field-types' ),
			'GL' => __( 'Greenland', 'bp-xprofile-custom-field-types' ),
			'GD' => __( 'Grenada', 'bp-xprofile-custom-field-types' ),
			'GP' => __( 'Guadeloupe', 'bp-xprofile-custom-field-types' ),
			'GU' => __( 'Guam', 'bp-xprofile-custom-field-types' ),
			'GT' => __( 'Guatemala', 'bp-xprofile-custom-field-types' ),
			'GG' => __( 'Guernsey', 'bp-xprofile-custom-field-types' ),
			'GN' => __( 'Guinea', 'bp-xprofile-custom-field-types' ),
			'GW' => __( 'Guinea-Bissau', 'bp-xprofile-custom-field-types' ),
			'GY' => __( 'Guyana', 'bp-xprofile-custom-field-types' ),
			'HT' => __( 'Haiti', 'bp-xprofile-custom-field-types' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'bp-xprofile-custom-field-types' ),
			'HN' => __( 'Honduras', 'bp-xprofile-custom-field-types' ),
			'HK' => __( 'Hong Kong', 'bp-xprofile-custom-field-types' ),
			'HU' => __( 'Hungary', 'bp-xprofile-custom-field-types' ),
			'IS' => __( 'Iceland', 'bp-xprofile-custom-field-types' ),
			'IN' => __( 'India', 'bp-xprofile-custom-field-types' ),
			'ID' => __( 'Indonesia', 'bp-xprofile-custom-field-types' ),
			'IR' => __( 'Iran', 'bp-xprofile-custom-field-types' ),
			'IQ' => __( 'Iraq', 'bp-xprofile-custom-field-types' ),
			'IE' => __( 'Ireland', 'bp-xprofile-custom-field-types' ),
			'IM' => __( 'Isle of Man', 'bp-xprofile-custom-field-types' ),
			'IL' => __( 'Israel', 'bp-xprofile-custom-field-types' ),
			'IT' => __( 'Italy', 'bp-xprofile-custom-field-types' ),
			'CI' => __( 'Ivory Coast', 'bp-xprofile-custom-field-types' ),
			'JM' => __( 'Jamaica', 'bp-xprofile-custom-field-types' ),
			'JP' => __( 'Japan', 'bp-xprofile-custom-field-types' ),
			'JE' => __( 'Jersey', 'bp-xprofile-custom-field-types' ),
			'JO' => __( 'Jordan', 'bp-xprofile-custom-field-types' ),
			'KZ' => __( 'Kazakhstan', 'bp-xprofile-custom-field-types' ),
			'KE' => __( 'Kenya', 'bp-xprofile-custom-field-types' ),
			'KI' => __( 'Kiribati', 'bp-xprofile-custom-field-types' ),
			'KW' => __( 'Kuwait', 'bp-xprofile-custom-field-types' ),
			'KG' => __( 'Kyrgyzstan', 'bp-xprofile-custom-field-types' ),
			'LA' => __( 'Laos', 'bp-xprofile-custom-field-types' ),
			'LV' => __( 'Latvia', 'bp-xprofile-custom-field-types' ),
			'LB' => __( 'Lebanon', 'bp-xprofile-custom-field-types' ),
			'LS' => __( 'Lesotho', 'bp-xprofile-custom-field-types' ),
			'LR' => __( 'Liberia', 'bp-xprofile-custom-field-types' ),
			'LY' => __( 'Libya', 'bp-xprofile-custom-field-types' ),
			'LI' => __( 'Liechtenstein', 'bp-xprofile-custom-field-types' ),
			'LT' => __( 'Lithuania', 'bp-xprofile-custom-field-types' ),
			'LU' => __( 'Luxembourg', 'bp-xprofile-custom-field-types' ),
			'MO' => __( 'Macao', 'bp-xprofile-custom-field-types' ),
			'MK' => __( 'North Macedonia', 'bp-xprofile-custom-field-types' ),
			'MG' => __( 'Madagascar', 'bp-xprofile-custom-field-types' ),
			'MW' => __( 'Malawi', 'bp-xprofile-custom-field-types' ),
			'MY' => __( 'Malaysia', 'bp-xprofile-custom-field-types' ),
			'MV' => __( 'Maldives', 'bp-xprofile-custom-field-types' ),
			'ML' => __( 'Mali', 'bp-xprofile-custom-field-types' ),
			'MT' => __( 'Malta', 'bp-xprofile-custom-field-types' ),
			'MH' => __( 'Marshall Islands', 'bp-xprofile-custom-field-types' ),
			'MQ' => __( 'Martinique', 'bp-xprofile-custom-field-types' ),
			'MR' => __( 'Mauritania', 'bp-xprofile-custom-field-types' ),
			'MU' => __( 'Mauritius', 'bp-xprofile-custom-field-types' ),
			'YT' => __( 'Mayotte', 'bp-xprofile-custom-field-types' ),
			'MX' => __( 'Mexico', 'bp-xprofile-custom-field-types' ),
			'FM' => __( 'Micronesia', 'bp-xprofile-custom-field-types' ),
			'MD' => __( 'Moldova', 'bp-xprofile-custom-field-types' ),
			'MC' => __( 'Monaco', 'bp-xprofile-custom-field-types' ),
			'MN' => __( 'Mongolia', 'bp-xprofile-custom-field-types' ),
			'ME' => __( 'Montenegro', 'bp-xprofile-custom-field-types' ),
			'MS' => __( 'Montserrat', 'bp-xprofile-custom-field-types' ),
			'MA' => __( 'Morocco', 'bp-xprofile-custom-field-types' ),
			'MZ' => __( 'Mozambique', 'bp-xprofile-custom-field-types' ),
			'MM' => __( 'Myanmar', 'bp-xprofile-custom-field-types' ),
			'NA' => __( 'Namibia', 'bp-xprofile-custom-field-types' ),
			'NR' => __( 'Nauru', 'bp-xprofile-custom-field-types' ),
			'NP' => __( 'Nepal', 'bp-xprofile-custom-field-types' ),
			'NL' => __( 'Netherlands', 'bp-xprofile-custom-field-types' ),
			'NC' => __( 'New Caledonia', 'bp-xprofile-custom-field-types' ),
			'NZ' => __( 'New Zealand', 'bp-xprofile-custom-field-types' ),
			'NI' => __( 'Nicaragua', 'bp-xprofile-custom-field-types' ),
			'NE' => __( 'Niger', 'bp-xprofile-custom-field-types' ),
			'NG' => __( 'Nigeria', 'bp-xprofile-custom-field-types' ),
			'NU' => __( 'Niue', 'bp-xprofile-custom-field-types' ),
			'NF' => __( 'Norfolk Island', 'bp-xprofile-custom-field-types' ),
			'MP' => __( 'Northern Mariana Islands', 'bp-xprofile-custom-field-types' ),
			'KP' => __( 'North Korea', 'bp-xprofile-custom-field-types' ),
			'NO' => __( 'Norway', 'bp-xprofile-custom-field-types' ),
			'OM' => __( 'Oman', 'bp-xprofile-custom-field-types' ),
			'PK' => __( 'Pakistan', 'bp-xprofile-custom-field-types' ),
			'PS' => __( 'Palestinian Territory', 'bp-xprofile-custom-field-types' ),
			'PA' => __( 'Panama', 'bp-xprofile-custom-field-types' ),
			'PG' => __( 'Papua New Guinea', 'bp-xprofile-custom-field-types' ),
			'PY' => __( 'Paraguay', 'bp-xprofile-custom-field-types' ),
			'PE' => __( 'Peru', 'bp-xprofile-custom-field-types' ),
			'PH' => __( 'Philippines', 'bp-xprofile-custom-field-types' ),
			'PN' => __( 'Pitcairn', 'bp-xprofile-custom-field-types' ),
			'PL' => __( 'Poland', 'bp-xprofile-custom-field-types' ),
			'PT' => __( 'Portugal', 'bp-xprofile-custom-field-types' ),
			'PR' => __( 'Puerto Rico', 'bp-xprofile-custom-field-types' ),
			'QA' => __( 'Qatar', 'bp-xprofile-custom-field-types' ),
			'RE' => __( 'Reunion', 'bp-xprofile-custom-field-types' ),
			'RO' => __( 'Romania', 'bp-xprofile-custom-field-types' ),
			'RU' => __( 'Russia', 'bp-xprofile-custom-field-types' ),
			'RW' => __( 'Rwanda', 'bp-xprofile-custom-field-types' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'bp-xprofile-custom-field-types' ),
			'SH' => __( 'Saint Helena', 'bp-xprofile-custom-field-types' ),
			'KN' => __( 'Saint Kitts and Nevis', 'bp-xprofile-custom-field-types' ),
			'LC' => __( 'Saint Lucia', 'bp-xprofile-custom-field-types' ),
			'MF' => __( 'Saint Martin (French part)', 'bp-xprofile-custom-field-types' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'bp-xprofile-custom-field-types' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'bp-xprofile-custom-field-types' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'bp-xprofile-custom-field-types' ),
			'SM' => __( 'San Marino', 'bp-xprofile-custom-field-types' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'bp-xprofile-custom-field-types' ),
			'SA' => __( 'Saudi Arabia', 'bp-xprofile-custom-field-types' ),
			'SN' => __( 'Senegal', 'bp-xprofile-custom-field-types' ),
			'RS' => __( 'Serbia', 'bp-xprofile-custom-field-types' ),
			'SC' => __( 'Seychelles', 'bp-xprofile-custom-field-types' ),
			'SL' => __( 'Sierra Leone', 'bp-xprofile-custom-field-types' ),
			'SG' => __( 'Singapore', 'bp-xprofile-custom-field-types' ),
			'SK' => __( 'Slovakia', 'bp-xprofile-custom-field-types' ),
			'SI' => __( 'Slovenia', 'bp-xprofile-custom-field-types' ),
			'SB' => __( 'Solomon Islands', 'bp-xprofile-custom-field-types' ),
			'SO' => __( 'Somalia', 'bp-xprofile-custom-field-types' ),
			'ZA' => __( 'South Africa', 'bp-xprofile-custom-field-types' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'bp-xprofile-custom-field-types' ),
			'KR' => __( 'South Korea', 'bp-xprofile-custom-field-types' ),
			'SS' => __( 'South Sudan', 'bp-xprofile-custom-field-types' ),
			'ES' => __( 'Spain', 'bp-xprofile-custom-field-types' ),
			'LK' => __( 'Sri Lanka', 'bp-xprofile-custom-field-types' ),
			'SD' => __( 'Sudan', 'bp-xprofile-custom-field-types' ),
			'SR' => __( 'Suriname', 'bp-xprofile-custom-field-types' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'bp-xprofile-custom-field-types' ),
			'SZ' => __( 'Swaziland', 'bp-xprofile-custom-field-types' ),
			'SE' => __( 'Sweden', 'bp-xprofile-custom-field-types' ),
			'CH' => __( 'Switzerland', 'bp-xprofile-custom-field-types' ),
			'SY' => __( 'Syria', 'bp-xprofile-custom-field-types' ),
			'TW' => __( 'Taiwan', 'bp-xprofile-custom-field-types' ),
			'TJ' => __( 'Tajikistan', 'bp-xprofile-custom-field-types' ),
			'TZ' => __( 'Tanzania', 'bp-xprofile-custom-field-types' ),
			'TH' => __( 'Thailand', 'bp-xprofile-custom-field-types' ),
			'TL' => __( 'Timor-Leste', 'bp-xprofile-custom-field-types' ),
			'TG' => __( 'Togo', 'bp-xprofile-custom-field-types' ),
			'TK' => __( 'Tokelau', 'bp-xprofile-custom-field-types' ),
			'TO' => __( 'Tonga', 'bp-xprofile-custom-field-types' ),
			'TT' => __( 'Trinidad and Tobago', 'bp-xprofile-custom-field-types' ),
			'TN' => __( 'Tunisia', 'bp-xprofile-custom-field-types' ),
			'TR' => __( 'Turkey', 'bp-xprofile-custom-field-types' ),
			'TM' => __( 'Turkmenistan', 'bp-xprofile-custom-field-types' ),
			'TC' => __( 'Turks and Caicos Islands', 'bp-xprofile-custom-field-types' ),
			'TV' => __( 'Tuvalu', 'bp-xprofile-custom-field-types' ),
			'UG' => __( 'Uganda', 'bp-xprofile-custom-field-types' ),
			'UA' => __( 'Ukraine', 'bp-xprofile-custom-field-types' ),
			'AE' => __( 'United Arab Emirates', 'bp-xprofile-custom-field-types' ),
			'GB' => __( 'United Kingdom', 'bp-xprofile-custom-field-types' ),
			'US' => __( 'United States', 'bp-xprofile-custom-field-types' ),
			'UM' => __( 'United States Minor Outlying Islands', 'bp-xprofile-custom-field-types' ),
			'UY' => __( 'Uruguay', 'bp-xprofile-custom-field-types' ),
			'UZ' => __( 'Uzbekistan', 'bp-xprofile-custom-field-types' ),
			'VU' => __( 'Vanuatu', 'bp-xprofile-custom-field-types' ),
			'VA' => __( 'Vatican', 'bp-xprofile-custom-field-types' ),
			'VE' => __( 'Venezuela', 'bp-xprofile-custom-field-types' ),
			'VN' => __( 'Vietnam', 'bp-xprofile-custom-field-types' ),
			'VG' => __( 'Virgin Islands (British)', 'bp-xprofile-custom-field-types' ),
			'VI' => __( 'Virgin Islands (US)', 'bp-xprofile-custom-field-types' ),
			'WF' => __( 'Wallis and Futuna', 'bp-xprofile-custom-field-types' ),
			'EH' => __( 'Western Sahara', 'bp-xprofile-custom-field-types' ),
			'WS' => __( 'Samoa', 'bp-xprofile-custom-field-types' ),
			'YE' => __( 'Yemen', 'bp-xprofile-custom-field-types' ),
			'ZM' => __( 'Zambia', 'bp-xprofile-custom-field-types' ),
			'ZW' => __( 'Zimbabwe', 'bp-xprofile-custom-field-types' ),
		);
	}
}
