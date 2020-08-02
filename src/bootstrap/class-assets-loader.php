<?php
/**
 * Asset Loader for BuddyPress Xprofile Custom Field Types.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Bootstrap
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Bootstrap;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Assets Loader.
 */
class Assets_Loader {

	/**
	 * Data to be send as localized js.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Boot itself
	 */
	public static function boot() {
		$self = new self();
		$self->setup();
	}

	/**
	 * Setup
	 */
	public function setup() {
		add_action( 'bp_enqueue_scripts', array( $this, 'register_front_assets' ) );
		add_action( 'bp_admin_enqueue_scripts', array( $this, 'register_admin_assets' ) );

		add_action( 'bp_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'bp_admin_enqueue_scripts', array( $this, 'load_admin_assets' ) );
		add_action( 'bp_admin_enqueue_scripts', array( $this, 'load_user_admin_assets' ) );
	}

	/**
	 * Register front assets.
	 */
	public function register_front_assets() {

		if ( ! $this->should_load_front_assets() ) {
			return;
		}

		$this->register();
	}

	/**
	 * Load plugin assets
	 */
	public function load_assets() {
		// css should be always loaded.
		wp_enqueue_style( 'bp-xprofile-custom-field-types' );

		if ( ! $this->should_load_front_assets() ) {
			return;
		}
		$this->enqueue_vendors();
		$this->enqueue_front();
	}

	/**
	 * Register admin assets.
	 */
	public function register_admin_assets() {

		if ( ! $this->should_load_admin_assets() ) {
			return;
		}

		$this->register();
	}

	/**
	 * Load plugin assets
	 */
	public function load_admin_assets() {

		if ( ! $this->should_load_admin_assets() ) {
			return;
		}

		$this->enqueue_vendors();
		$this->enqueue_admin();
	}

	/**
	 * Load plugin assets
	 */
	public function load_user_admin_assets() {

		$load = isset( $_GET['page'] ) && 'bp-profile-edit' === $_GET['page'];
		$load = apply_filters( 'bpxcftr_load_user_admin_assets', $load );

		if ( ! $load ) {
			return;
		}

		$this->enqueue_vendors();
		$this->enqueue_user_admin();
	}

	/**
	 * Register assets.
	 */
	private function register() {
		$this->register_vendors();
		$this->register_core();
		$this->register_admin();
	}

	/**
	 * Enqueue front end assets.
	 */
	public function enqueue_front() {
		wp_enqueue_script( 'bp-xprofile-custom-field-types' );

		wp_localize_script( 'bp-xprofile-custom-field-types', 'BPXprofileCFTR', $this->data );
	}

	/**
	 * Load vendor assets.
	 */
	public function enqueue_vendors() {
		wp_enqueue_style( 'select2' );
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'jscolor' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'select2-i18n' );
	}

	/**
	 * Enqueue admin assets for add/edit field screen..
	 */
	public function enqueue_admin() {
		wp_enqueue_script( 'bp-xprofile-custom-field-types-admin' );

		wp_localize_script( 'bp-xprofile-custom-field-types', 'BPXprofileCFTR', $this->data );

		wp_localize_script( 'bp-xprofile-custom-field-types-admin', 'BPXprofileCFTRAdmin', $this->data );
	}

	/**
	 * Enqueue admin assets needed for user profile edit page.
	 */
	public function enqueue_user_admin() {
		wp_enqueue_script( 'bp-xprofile-custom-field-types' );
		wp_localize_script( 'bp-xprofile-custom-field-types', 'BPXprofileCFTR', $this->data );
	}

	/**
	 * Register vendor scripts.
	 */
	private function register_vendors() {

		$url  = bp_xprofile_cftr()->url;
		$path = bp_xprofile_cftr()->path;

		$version = bp_xprofile_cftr()->version;

		wp_register_script( 'modernizr', $url . 'assets/vendors/modernizr.js', array(), $version, false );
		wp_register_script( 'jscolor', $url . 'assets/vendors/jscolor/jscolor.js', array(), '1.4.1', true );

		wp_register_script( 'select2', $url . 'assets/vendors/select2/select2.full.min.js', array( 'jquery' ), '4.0.12', true );

		$locale = apply_filters( 'bpxcftr_select2_js_locale', get_locale() );
		// Select 2, locale.
		$locale_js = is_readable( $path . "assets/vendors/select2/i18n/{$locale}.js" ) ? "{$locale}.js" : 'en.js';

		wp_register_script( 'select2-i18n', $url . "assets/vendors/select2/i18n/{$locale_js}", array( 'select2' ), '4.0.12', true );

		wp_register_style( 'select2', $url . 'assets/vendors/select2/select2.min.css', array(), '4.0.2' );

	}

	/**
	 * Register core assets.
	 */
	private function register_core() {
		$url     = bp_xprofile_cftr()->url;
		$version = bp_xprofile_cftr()->version;

		wp_register_style( 'bp-xprofile-custom-field-types', $url . 'assets/css/bp-xprofile-custom-field-types.css', false, $version );

		wp_register_script( 'bp-xprofile-custom-field-types', $url . 'assets/js/bp-xprofile-custom-field-types.js', array( 'jquery' ), $version, true );

		$this->data = array();
	}

	/**
	 * Register core assets.
	 */
	private function register_admin() {
		$url     = bp_xprofile_cftr()->url;
		$version = bp_xprofile_cftr()->version;

		wp_register_script( 'bp-xprofile-custom-field-types-admin', $url . 'assets/js/bp-xprofile-custom-field-types-admin.js', array( 'jquery' ), $version, true );

		$this->data = array(
			'selectableTypes' => bpxcftr_get_selectable_field_types(),
		);
	}

	/**
	 * Should we load admin assets?
	 *
	 * @return bool
	 */
	private function should_load_admin_assets() {
		$load = isset( $_GET['page'] ) && ( 'bp-profile-setup' === $_GET['page'] || 'bp-profile-edit' == $_GET['page'] );

		return apply_filters( 'bpxcftr_load_admin_assets', $load );
	}

	/**
	 * Should we load front assets.
	 *
	 * @return bool
	 */
	private function should_load_front_assets() {
		$load = bp_is_user_profile_edit() || bp_is_register_page();

		return apply_filters( 'bpxcftr_load_front_assets', $load );
	}

}
