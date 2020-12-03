<?php
/**
 * Xprofile kses filter
 *
 * @package    Allow extra tags in xprofile data
 * @subpackage Handlers
 * @copyright  Copyright (c) 2020, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.1.8
 */

namespace BPXProfileCFTR\Filters;

// Do not allow direct access over web.
use BPXProfileCFTR\Field_Types\Field_Type_Web;

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility helper for BP Profile Search.
 */
class Xprofile_Kses_Filter {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup hooks.
	 */
	public function setup() {
		add_filter( 'xprofile_allowed_tags', array( $this, 'filter' ), 10, 3 );
	}

	/**
	 * Filters xrofile allowed tags and extends for target attribute if needed.
	 *
	 * @param array                        $xprofile_allowedtags Array of allowed tags for profile field values.
	 * @param \BP_XProfile_ProfileData|null $data_obj            The BP_XProfile_ProfileData object.
	 * @param int|null                     $field_id             The ID of the profile field.
	 *
	 * @return array
	 */
	public function filter( $xprofile_allowedtags, $data_obj, $field_id ) {

		if ( ! $field_id ) {
			return $xprofile_allowedtags;
		}

		$field = new \BP_XProfile_Field( $field_id );
		if ( 'web' === $field->type && Field_Type_Web::get_link_target( $field_id ) === '_blank' && isset( $xprofile_allowedtags['a'] ) ) {
			$xprofile_allowedtags['a']['target'] = array();
		}

		return $xprofile_allowedtags;
	}
}
