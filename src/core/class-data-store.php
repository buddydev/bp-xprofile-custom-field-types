<?php
/**
 * Temporary In memory data store for saving small pieces of data.
 *
 * @package    BuddyPress Xprofile Custom Field Types
 * @subpackage Core
 * @copyright  Copyright (c) 2018, Brajesh Singh
 * @license    https://www.gnu.org/licenses/gpl.html GNU Public License
 * @author     Brajesh Singh
 * @since      1.0.0
 */

namespace BPXProfileCFTR\Core;

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Temporary, In memory data store.
 */
class Data_Store {
	/**
	 * Private data store.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Get the property value.
	 *
	 * @param string $name dynamic property name.
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {
		return isset( $this->data[ $name ] ) ? $this->data[ $name ] : null;
	}

	/**
	 * Check if a property is set.
	 *
	 * @param string $name property name.
	 *
	 * @return bool
	 */
	public function __isset( $name ) {
		return isset( $this->data[ $name ] );
	}

	/**
	 * Set a property.
	 *
	 * @param string $name property name.
	 * @param mixed  $value value.
	 */
	public function __set( $name, $value ) {
		$this->data[ $name ] = $value;
	}

	/**
	 * Unset a property.
	 *
	 * @param string $name property name.
	 */
	public function __unset( $name ) {
		unset( $this->data[ $name ] );
	}
}
