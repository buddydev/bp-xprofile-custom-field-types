<?php
/**
 * Manage Uploading/Deleting of uploaded files for the 'file' and 'image' field.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Handlers
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Handlers;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Manage and sync field data.
 */
class Field_Upload_Helper {

	/**
	 * User id.
	 *
	 * @var int
	 */
	private $user_id = null;

	/**
	 * Current field.
	 *
	 * @var \BP_XProfile_Field
	 */
	private $field;

	/**
	 * Upload base directory. relative to wp-content/uploads.
	 *
	 * @var string
	 */
	private $dir_base = 'bpxcftr-profile-uploads';

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
		add_action( 'xprofile_data_before_save', array( $this, 'on_field_data_save' ), 5 );
		add_action( 'xprofile_data_before_delete', array( $this, 'on_field_data_delete' ), 5 );
		add_action( 'wpmu_delete_user', array( $this, 'on_user_data_delete' ), 5 );
		add_action( 'delete_user', array( $this, 'on_user_delete' ), 5 );
		add_action( 'bp_make_spam_user', array( $this, 'on_user_data_delete' ), 5 );
	}

	/**
	 * Cleanup user files|images.
	 *
	 * @param int $user_id user id.
	 */
	public function on_user_data_delete( $user_id ) {
		$this->delete_for_user( $user_id );
	}

	/**
	 * Cleanup user files|images.
	 *
	 * @param int $user_id user id.
	 */
	public function on_user_delete( $user_id ) {
		if ( function_exists( 'bp_remove_user_data_on_delete_user_hook' ) && bp_remove_user_data_on_delete_user_hook( 'xprofile', $user_id ) ) {
			$this->delete_for_user( $user_id );
		}
	}

	/**
	 * Upload files on field save.
	 *
	 * @param \BP_XProfile_ProfileData $data data object.
	 */
	public function on_field_data_save( $data ) {
		$field_id = $data->field_id;
		$field    = new \BP_XProfile_Field( $field_id );

		if ( ! in_array( $field->type, array( 'image', 'file' ), true ) ) {
			return;
		}

		if ( ! empty( $_POST["field_{$field_id}_delete"] ) ) {
			$this->delete_field( $field, $data );
			$data->value = ''; // empty.
		}

		// file not selected?
		if ( empty( $_FILES[ 'field_' . $field_id ] ) || empty( $_FILES[ 'field_' . $field_id ]['tmp_name'] ) ) {
			// If it is our placeholder value, reset.
			if ( $data->value == '-' || empty( $data->value ) ) {
				// @todo improve.
				$data->value = '';
			} else {
				$values = maybe_unserialize( \BP_XProfile_ProfileData::get_value_byid( $field_id, $data->user_id ) );
				$data->value = $values;
			}
			return;
		}

		$redirect_url = trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() . '/edit/group/' . bp_action_variable( 1 ) );

		$value = $this->handle_upload( $field, $data );

		if ( is_wp_error( $value ) ) {
			bp_core_add_message( $value->get_error_message(), 'error' );
			$this->redirect( $redirect_url );
		} else {
			$data->value = $value;
		}
	}

	/**
	 * Redirect if needed.
	 *
	 * @param string $url redirect url.
	 */
	private function redirect( $url ) {
		if ( defined( 'DOING_AJAX' ) ) {
			return; // ;; no redirect.
		}

		bp_core_redirect( $url );
	}

	/**
	 * On Field data delete, delete associated files.
	 *
	 * @param \BP_XProfile_ProfileData $data data object.
	 */
	public function on_field_data_delete( $data ) {
		$field_id = $data->field_id;
		$field    = new \BP_XProfile_Field( $field_id );
		$this->delete_field( $field, $data );
	}

	/**
	 * Delete the field with given data.
	 *
	 * @param \BP_XProfile_Field       $field field object.
	 * @param \BP_XProfile_ProfileData $data data object.
	 *
	 * @return bool
	 */
	private function delete_field( $field, $data ) {

		if ( empty( $data->value ) ) {
			return true;
		}

		$uploads = wp_upload_dir();
		$path    = path_join( $uploads['basedir'], trim( $data->value, '/\\' ) );

		switch ( $field->type ) {

			case 'image':
			case 'file':
				do_action( 'bpxcftr_field_data_file_deleted', $path, $field, $data );
				wp_delete_file( $path );
				break;
		}

		return true;
	}

	/**
	 * Handle Upload.
	 *
	 * @param \BP_XProfile_Field       $field field object.
	 * @param \BP_XProfile_ProfileData $data data object.
	 *
	 * @return string|\WP_Error
	 */
	private function handle_upload( $field, $data ) {

		$user_id = $data->user_id;

		$field_id = $field->id;

		$file_size = round( $_FILES[ 'field_' . $field_id ]['size'] / ( 1024 * 1024 ), 2 );
		$is_upload = ( $file_size > 0 ) && isset( $_FILES[ 'field_' . $field_id ] );

		if ( ! $is_upload ) {
			return new \WP_Error( 'file_err', __( 'Invalid file.', 'bp-xprofile-custom-field-types' ) );
		}

		if ( $file_size <= 0 ) {
			return new \WP_Error( 'file_size_err', __( 'Invalid file size.', 'bp-xprofile-custom-field-types' ) );
		}

		$ext          = strtolower( substr( $_FILES[ 'field_' . $field_id ]['name'], strrpos( $_FILES[ 'field_' . $field_id ]['name'], '.' ) + 1 ) );
		$ext_allowed  = bpxcftr_get_allowed_file_extensions( $field->type );
		$allowed_size = bpxcftr_get_allowed_file_size( $field->type );

		if ( ! in_array( $ext, $ext_allowed ) ) {
			return new \WP_Error( 'invalid_file_type', sprintf( __( 'File type not allowed: (%s).', 'bp-xprofile-custom-field-types' ), implode( ',', $ext_allowed ) ) );
		} elseif ( $file_size > $allowed_size ) {
			return new \WP_Error( sprintf( __( 'Max image upload size: %s MB.', 'bp-xprofile-custom-field-types' ), $allowed_size ) );
		}

		// if we are here, we may proceed to upload.
		require_once( ABSPATH . '/wp-admin/includes/file.php' );

		$this->user_id = $user_id;
		$this->field   = $field;

		add_filter( 'upload_dir', array( $this, 'upload_dir' ), 10 );

		$_POST['action'] = 'wp_handle_upload';

		$uploaded_file   = wp_handle_upload( $_FILES[ 'field_' . $field_id ] );

		remove_filter( 'upload_dir', array( $this, 'upload_dir' ), 10 );

		if ( ! empty( $uploaded_file['error'] ) ) {
			return new \WP_Error( 'file_upload_err', $uploaded_file['error'] );
		}

		// if we are here, all is well.
		// delete the previous file.
		$this->delete_field( $field, $data );

		// find the relative path?
		$value = _wp_relative_upload_path( $uploaded_file['file'] );

		return isset( $value ) ? $value : '';
	}

	/**
	 * Filter upload dir.
	 *
	 * @param array $upload_dir upload dir info.
	 *
	 * @return array
	 */
	public function upload_dir( $upload_dir ) {
		if ( empty( $this->user_id ) ) {
			$this->user_id = bp_displayed_user_id();
		}

		$subdir = $this->user_id . '/' . $this->field->type;
		$subdir = '/' . trim( $this->dir_base, '/\\' ) . '/' . trim( $subdir, '/\\' );

		$upload_dir['path']   = $upload_dir['basedir'] . $subdir;
		$upload_dir['url']    = $upload_dir['baseurl'] . $subdir;
		$upload_dir['subdir'] = $subdir;

		// uploads/bpxcftr-profile-uploads/1/file
		// uploads/bpxcftr-profile-uploads/1/image.
		return $upload_dir;
	}

	/**
	 * Force delete file or image fields for user.
	 *
	 * @param int $user_id user id.
	 */
	private function delete_for_user( $user_id ) {

		if ( ! bp_is_active( 'xprofile' ) ) {
			return;
		}

		$field_ids = $this->get_file_field_ids();
		if ( empty( $field_ids ) ) {
			return;
		}

		foreach ( $field_ids as $field_id ) {
			if ( ! xprofile_get_field_data( $field_id, $user_id ) ) {
				continue;
			}
			// force cleanup routine.
			$field = new \BP_XProfile_ProfileData( $field_id, $user_id );
			$field->delete();
		}
	}

	/**
	 * Get all fields of file|image type.
	 *
	 * @return array
	 */
	private function get_file_field_ids() {
		global $wpdb;
		$bp = buddypress();
		return $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$bp->profile->table_name_fields} WHERE type = %s OR type = %s", 'image', 'file' ) );
	}
}
