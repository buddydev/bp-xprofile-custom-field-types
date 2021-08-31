<?php
/**
 * Bootstrapper. Initializes the plugin.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Bootstrap;

use BPXProfileCFTR\Admin\Admin;
use BPXProfileCFTR\Filters\BP_Profile_Search_Helper;
use BPXProfileCFTR\Filters\Xprofile_Kses_Filter;
use BPXProfileCFTR\Handlers\Birthdate_Field_Validator;
use BPXProfileCFTR\Handlers\Field_Upload_Helper;
use BPXProfileCFTR\Handlers\From_To_Helper;
use BPXProfileCFTR\Handlers\Label_Filter;
use BPXProfileCFTR\Handlers\Taxonomy_Terms_Creator;
use BPXProfileCFTR\Handlers\Field_Settings_Handler;
use BPXProfileCFTR\Admin\Field_Settings_Helper as Admin_Field_Settings_Helper;
use BPXProfileCFTR\Handlers\Signup_Validator;
use BPXProfileCFTR\Handlers\Tags_Creator;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Bootstrapper.
 */
class Bootstrapper {

	/**
	 * Setup the bootstrapper.
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Bind hooks
	 */
	private function setup() {

		add_action( 'bp_loaded', array( $this, 'load' ) );
		add_action( 'bp_init', array( $this, 'load_translations' ) );

		// register.
		add_filter( 'bp_xprofile_get_field_types', array( $this, 'register_field_types' ), 10, 1 );
	}

	/**
	 * Load core functions/template tags.
	 * These are non auto loadable constructs.
	 */
	public function load() {
		$this->load_common();
		$this->load_admin();
	}

	/**
	 * Load translations.
	 */
	public function load_translations() {
		load_plugin_textdomain( 'bp-xprofile-custom-field-types', false, basename( dirname( bp_xprofile_cftr()->path ) ) . '/languages' );
	}

	/**
	 * Register the field types.
	 *
	 * @param array $fields field types.
	 *
	 * @return array
	 */
	public function register_field_types( $fields ) {
		$fields = array_merge( $fields, bpxcftr_get_field_types() );
		return $fields;
	}

	/**
	 * Load files common to each request type.
	 */
	private function load_common() {
		$path = bp_xprofile_cftr()->path;

		$files = array(
			'src/core/bp-xprofile-custom-field-types-functions.php',
		);

		if ( is_admin() ) {
		}

		foreach ( $files as $file ) {
			require_once $path . $file;
		}

		// Boot the app.
		Assets_Loader::boot();
		Field_Upload_Helper::boot();
		Taxonomy_Terms_Creator::boot();
		Birthdate_Field_Validator::boot();
		Field_Settings_Handler::boot();
		Signup_Validator::boot();
		From_To_Helper::boot();
		Xprofile_Kses_Filter::boot();
		// BP profile Search.
		BP_Profile_Search_Helper::boot();
		Label_Filter::boot();
		Tags_Creator::boot();
	}

	/**
	 * Load admin.
	 */
	private function load_admin() {

		if ( ! is_admin() ) {
			return;
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			Admin::boot();
		}

		Admin_Field_Settings_Helper::boot();
	}
}
