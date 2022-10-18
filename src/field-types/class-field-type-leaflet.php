<?php
/**
 * Email Field.
 *
 * @package    BuddyPress Leaflet Custom Field Types
 * @subpackage Field_Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Field_Types;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Field type email.
 */
class Field_Type_Leaflet extends \BP_XProfile_Field_Type {

	
	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct();

		$this->name     = _x( 'Leaflet location field', 'xprofile field type', 'bp-xprofile-custom-field-types' );
		$this->category = _x( 'Custom Fields', 'xprofile field type category', 'bp-xprofile-custom-field-types' );

		$this->do_settings_section = true;
		$this->field_prefixe = "bpxcftr_leaflet_";
		$this->accepts_null_value = true;
		$this->validation_regex = array('{"lat":\d+\.?\d+,"lng":\d+\.?\d+,"name":".+"}'); // Valid format {"lat":45.12345,"lng":5.12345,"name":"Adress, City, Country"}
		
		do_action( 'bp_xprofile_field_type_leaflet', $this );
	}

	/**
	 * Edit field html.
	 *
	 * @param array $raw_properties properties.
	 */
	public function edit_field_html( array $raw_properties = array() ) {
		global $field;
				
		// Défault values
		$latitude 	= bp_xprofile_get_meta( $field->id, 'field', $this->field_prefixe.'latitude', true );
		$longitude  = bp_xprofile_get_meta( $field->id, 'field', $this->field_prefixe.'longitude', true );
		$zoom     	= bp_xprofile_get_meta( $field->id, 'field', $this->field_prefixe.'zoom', true );
		$height     = bp_xprofile_get_meta( $field->id, 'field', $this->field_prefixe.'height', true );		
		$city		= "";


		try {
			$value = bp_get_the_profile_field_edit_value();
			$value = json_decode(html_entity_decode($value));
			if ($value!=null) {
				$latitude =  $value->lat;
				$longitude = $value->lng;
				$city = $value->name;			
			}
		}
		catch (\Exception $e) {
			$value=null;
		}

		$html = $this->get_edit_field_html_elements(
			array_merge(
				array(
					'type'  => 'hidden',
					'value' => bp_get_the_profile_field_edit_value(),					
				),
				$raw_properties
			)
		);
		?>

		<legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php
		// Errors.
		do_action( bp_get_the_profile_field_errors_action() );
		// Input.
		?>

        <input <?php echo $html; ?> />
		<div class="bp-x-laeflet-label-wrapper">
			<label id="<?php echo $this->field_prefixe?>_label"><?php echo $city?></label>			
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>			
		</div>
		<div id="<?php echo $this->field_prefixe?>_map" class="bpx-leaflet-map" data-longitude="<?php echo $longitude?>" data-latitude="<?php echo $latitude?>" data-zoom="<?php echo $zoom?>" style="height: <?php echo $height?>;"></div>

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
				array( 'type' => 'text' ),
				$raw_properties
			)
		);
		?>

        <input <?php echo $html; ?> />

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

		$class             	= $current_field->type != $type ? 'display: none;' : '';
		$current_latitude 	= bp_xprofile_get_meta( $current_field->id, 'field', $this->field_prefixe.'latitude', true );
		$current_longitude  = bp_xprofile_get_meta( $current_field->id, 'field', $this->field_prefixe.'longitude', true );
		$current_zoom     	= bp_xprofile_get_meta( $current_field->id, 'field', $this->field_prefixe.'zoom', true );
		$height     		= bp_xprofile_get_meta( $current_field->id, 'field', $this->field_prefixe.'height', true );
		
		
		?>
        <div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
            <h3><?php esc_html_e( 'Options', 'bp-xprofile-custom-field-types' ); ?></h3>
            <div class="inside">
				<p>
					<label for="bpxcftr_leaflet_latitude"><?php _e('Latitude', 'bp-xprofile-custom-field-types');?></label>
					<input type="text" value="<?php echo esc_attr( $current_latitude);?>" name="bpxcftr_leaflet_latitude" placeholder="E.g. 45.12345"/>
				</p>
                <p>
                    <label for="bpxcftr_leaflet_longitude"><?php _e('Longitude', 'bp-xprofile-custom-field-types');?></label>
                    <input type="text" value="<?php echo esc_attr( $current_longitude);?>" name="bpxcftr_leaflet_longitude" placeholder="E.g. 1.12345" />
                </p>
                <p>
                    <label for="bpxcftr_leaflet_zoom"><?php _e('Zoom', 'bp-xprofile-custom-field-types');?></label>
                    <input type="text" value="<?php echo esc_attr( $current_zoom);?>" name="bpxcftr_leaflet_zoom" placeholder="E.g. 10"/>
                </p>
				<p>
                    <label for="bpxcftr_leaflet_height"><?php _e('Height map', 'bp-xprofile-custom-field-types');?></label>
                    <input type="text" value="<?php echo esc_attr( $height);?>" name="bpxcftr_leaflet_height" placeholder="E.g. 200px"/>
                </p>				
            </div>
        </div>
		<?php
	}	

	/**
	 * Check if it is a valid location field.
	 *
	 * @param string $values value.
	 *
	 * @return bool
	 */
	public function is_valid( $value ) {		
		try {
			$field = bpxcftr_get_current_field();			
			$validated = false;
			$redirect_url = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() . '/edit/group/' . bp_action_variable( 1 ) );
			if (!empty($value)) {
				$valueStd = json_decode(stripslashes($value));					
				$validated = !empty($valueStd->lat) && !empty($valueStd->lng) && !empty($valueStd->name);
				if (!$validated) {
					bp_core_add_message('Incorrect address','error');					
					bp_core_redirect( $redirect_url );
				}
			}
			
			return $validated;
		}
		catch (\Exception $e) {
			return false;
		}
		
	}

	/**
	 * Modify the appearance of value.
	 *
	 * @param  string $field_value Original value of field.
	 * @param  int    $field_id Id of field.
	 *
	 * @return string   Value formatted
	 */
	public static function display_filter( $field_value, $field_id = 0 ) {
		try {	
			if (empty( $field_value )) {return "";}		
			$value = json_decode(html_entity_decode($field_value));
			if ($value!=null) {
				$latitude =  $value->lat;
				$longitude = $value->lng;
				$city = $value->name;			
				return $city;
			}
			return "";
		} catch (\Exception $e) {
			return "-- ERROR--";
		}
	
	}


	/**
	 * Save settings from the field edit screen in the Dashboard.
	 *
	 * @param int   $field_id ID of the field.
	 * @param array $settings Array of settings posted($_POST['field-settings'] ).
	 * @return bool True on success.
	 */
	public function admin_save_settings( $field_id, $settings ) {
		
		$saved_settings = array_filter($_POST,function($key) { return substr($key,0,strlen($this->field_prefixe))===$this->field_prefixe;}, ARRAY_FILTER_USE_KEY);
		
		foreach ( $saved_settings as $setting_key => $setting_value ) {
			bp_xprofile_update_meta( $field_id, 'field', $setting_key, $setting_value );
		}
		
		return true;
	}
}
