<?php
/**
 * BuddyPress Xprofile Custom Field Types
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

use BPXProfileCFTR\Contracts\Field_Type_Multi_Valued;
use BPXProfileCFTR\Contracts\Field_Type_Selectable;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Get a mapping of field type to their implementation class.
 *
 * @return array Key/value pairs (field type => class name).
 */
function bpxcftr_get_field_types() {
	$fields = array(
		// I haven't changed the field type name to make it drop in replacement for BuddyPress Xprofile Custom Fields Type plugin.
		'birthdate'                    => 'BPXProfileCFTR\Field_Types\Field_Type_Birthdate',
		'email'                        => 'BPXProfileCFTR\Field_Types\Field_Type_Email',
		'web'                          => 'BPXProfileCFTR\Field_Types\Field_Type_Web',
		'datepicker'                   => 'BPXProfileCFTR\Field_Types\Field_Type_Datepicker',
		'select_custom_post_type'      => 'BPXProfileCFTR\Field_Types\Field_Type_Select_Post_Type',
		'multiselect_custom_post_type' => 'BPXProfileCFTR\Field_Types\Field_Type_Multi_Select_Post_Type',
		'select_custom_taxonomy'       => 'BPXProfileCFTR\Field_Types\Field_Type_Select_Taxonomy',
		'multiselect_custom_taxonomy'  => 'BPXProfileCFTR\Field_Types\Field_Type_Multi_Select_Taxonomy',
		'checkbox_acceptance'          => 'BPXProfileCFTR\Field_Types\Field_Type_Checkbox_Acceptance',
		'image'                        => 'BPXProfileCFTR\Field_Types\Field_Type_Image',
		'file'                         => 'BPXProfileCFTR\Field_Types\Field_Type_File',
		'color'                        => 'BPXProfileCFTR\Field_Types\Field_Type_Color',
		'decimal_number'               => 'BPXProfileCFTR\Field_Types\Field_Type_Decimal_Number',
		'number_minmax'                => 'BPXProfileCFTR\Field_Types\Field_Type_Number_Min_Max',
		'slider'                       => 'BPXProfileCFTR\Field_Types\Field_Type_Slider',
		'fromto'                       => 'BPXProfileCFTR\Field_Types\Field_Type_From_To',
		// end of the BuddyPress Xprofile Custom Fields Type plugin's field type.

	);

	return $fields;
}

/**
 * Get field types which support the selec2  js.
 *
 * @return array
 */
function bpxcftr_get_selectable_field_types() {
	$types = array(
		'selectbox',
		'multiselectbox',
		'select_custom_post_type',
		'multiselect_custom_post_type',
		'select_custom_taxonomy',
		'multiselect_custom_taxonomy',
	);

	return apply_filters( 'bpxcftr_selectable_types', $types );
}
/**
 * Get an array of allowed file extensions.
 *
 * @param string $type possible values 'image', 'file'.
 *
 * @return array
 */
function bpxcftr_get_allowed_file_extensions( $type ) {

	$extensions = array(
		'file'  => array(
			'doc',
			'docx',
			'pdf',
		),
		'image' => array(
			'jpg',
			'jpeg',
			'gif',
			'png',
		),
	);

	$extensions = apply_filters( 'bpxcftr_allowed_extensions', $extensions );

	return isset( $extensions[ $type ] ) ? $extensions[ $type ] : array();
}

/**
 * Get maximum allowed file size.
 *
 * @param string $type field type.
 *
 * @return int|mixed
 */
function bpxcftr_get_allowed_file_size( $type ) {

	$sizes = array(
		'file'  => 8,
		'image' => 8,
	);

	$sizes = apply_filters( 'bpxcftr_allowed_sizes', $sizes );
	return isset( $sizes[ $type ] ) ? $sizes[ $type ] : 0;
}

/**
 * Is field type selectable?
 *
 * Used for deciding when to apply select2
 *
 * @param BP_XProfile_Field $field field object.
 *
 * @return bool
 */
function bpxcftr_is_selectable_field( $field ) {
	$selectable_types = bpxcftr_get_selectable_field_types();
	return in_array( $field->type, $selectable_types ) || $field->type_obj instanceof Field_Type_Selectable ;
}

/**
 * Is field type multi valued?
 *
 * Used for deciding when to apply select2
 *
 * @param BP_XProfile_Field $field field object.
 *
 * @return bool
 */
function bpxcftr_is_multi_valued_field( $field ) {
	$selectable_types = apply_filters( 'bpxcftr_multi_valued_types', array(
		'multiselectbox',
	));

	return in_array( $field->type, $selectable_types ) || $field->type_obj instanceof Field_Type_Multi_Valued ;
}

/**
 * It is a work around to get the field at the time is_valid() is called on field types.
 * BuddyPress does not pass the id at the moment.
 *
 * @return BP_XProfile_Field|null
 */
function bpxcftr_get_current_field() {
	return bp_xprofile_cftr()->data->current_field;
}

/**
 * Save the current field value.
 *
 * @param BP_XProfile_Field|null $field field object or null.
 */
function bpxcftr_set_current_field( $field ) {
	bp_xprofile_cftr()->data->current_field = $field;
}
