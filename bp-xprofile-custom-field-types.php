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

/**
 * Plugin Name: BuddyPress Xprofile Custom Field Types
 * Plugin URI: https://buddydev.com/plugins/buddypress-xprofile-custom-field-types/
 * Description: Have all the extra field types at your disposal.
 * Version: 1.2.0
 * Requires PHP: 5.3
 * Author: BuddyDev
 * Author URI: https://buddydev.com
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  bp-xprofile-custom-field-types
 * Domain Path:  /languages
 */

use BPXProfileCFTR\Bootstrap\Autoloader;
use BPXProfileCFTR\Bootstrap\Bootstrapper;
use BPXProfileCFTR\Core\Data_Store;

// Do not allow direct access over web.
defined( 'ABSPATH' ) || exit;

/**
 * Class BP_Xprofile_CFTR
 *
 * @property-read string                $path absolute path to the plugin directory.
 * @property-read string                $url absolute url to the plugin directory.
 * @property-read string                $basename plugin base name.
 * @property-read string                $version plugin version.
 * @property-read Data_Store            $data temporary data store.
 */
class BP_Xprofile_CFTR {

	/**
	 * Plugin Version.
	 *
	 * @var string
	 */
	private $version = '1.2.0';

	/**
	 * Class instance
	 *
	 * @var static
	 */
	private static $instance = null;

	/**
	 * Plugins directory path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Plugins directory url
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Plugin Basename.
	 *
	 * @var string
	 */
	private $basename;

	/**
	 * Temporary data store object.
	 *
	 * @var Data_Store
	 */
	private $data = null;

	/**
	 * Protected properties. These properties are inaccessible via magic method.
	 *
	 * @var array
	 */
	private static $protected = array( 'instance' );

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->bootstrap();
		$this->setup();
	}

	/**
	 * Get class instance
	 *
	 * @return static
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Bootstrap the core.
	 */
	private function bootstrap() {
		// Setup general properties.
		$this->path     = plugin_dir_path( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->basename = plugin_basename( __FILE__ );
		// Load autoloader.
		require_once $this->path . 'src/bootstrap/class-autoloader.php';

		// Register autoloader.
		spl_autoload_register( new Autoloader( 'BPXProfileCFTR\\', __DIR__ . '/src/' ) );
		register_activation_hook( __FILE__, array( $this, 'on_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'on_deactivation' ) );
	}

	/**
	 * Load plugin core files and assets.
	 */
	private function setup() {
		$this->data = new Data_Store();

		Bootstrapper::boot();
	}

	/**
	 * On activation create table
	 */
	public function on_activation() {
		update_option( 'bpxcftr_notices', array() );
	}

	/**
	 * On deactivation create table
	 */
	public function on_deactivation() {
		delete_option( 'bpxcftr_notices' );
	}

	/**
	 * Magic method for accessing property as readonly(It's a lie, references can be updated).
	 *
	 * @param string $name property name.
	 *
	 * @return mixed|null
	 */
	public function __get( $name ) {

		if ( ! in_array( $name, self::$protected, true ) && property_exists( $this, $name ) ) {
			return $this->{$name};
		}

		return null;
	}
}

/**
 * Return object of class
 *
 * @return BP_Xprofile_CFTR
 */
function bp_xprofile_cftr() {
	return BP_Xprofile_CFTR::get_instance();
}

bp_xprofile_cftr();
