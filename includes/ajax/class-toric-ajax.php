<?php
/**
 * The file that defines the AJAX API core plugin class
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.1
 *
 * @package    Toric
 * @subpackage Toric/includes
 */

/**
 * The AJAX plugin class.
 *
 * @since      1.0.1
 * @package    Toric
 * @subpackage Toric/includes/ajax
 * @author     Alvin Muthui <alvinmuthui@toriajax.com>
 */
class Toric_Ajax {

	/**
	 * Holds Toric instance
	 *
	 * @since    1.0.1
	 *
	 * @var Toric
	 */
	protected Toric $toric;

	/**
	 * Unique action name
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      string    $action    Unique action name.
	 */
	public $action;

	/**
	 * PHP function to respond to ajax calls
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      string    $php_callback    PHP function to respond to ajax calls.
	 */
	public $php_callback;

	/**
	 * File path containing you Javascript file
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      string    $script_path    File path containing you Javascript file.
	 */
	public $script_path;


	/**
	 * Used to create WP nonce for verification on PHP callback.
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      string    $nonce   Used to create WP nonce for verification on PHP callback.
	 */
	public $nonce;

	/**
	 * Both, private, public
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      string    $nonce   both, private, public
	 */
	public $mode;

	/**
	 * Name of script.
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      string    $ajax_handle   Name of script.
	 */
	public $ajax_handle;

	/**
	 * Name of object to be storing JS variables
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      string    $ajax_object  Name of object to be storing JS variables
	 */
	public $ajax_object;

	/**
	 * Variables to be passed to be available for JS to utilize.
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      array   $ajax_variables  Variables to be passed to be available for JS to utilize.
	 */
	public $ajax_variables;// Variables to be passed to be available for JS to utilize.

	/**
	 * An array of registered script handles this script depends on.
	 *
	 * @since    1.0.2
	 * @access   public
	 * @var      array   $script_depends.
	 */
	public $script_depends;

	/**
	 * String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed Tori Ajax version. If set to null, no version is added.
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      mixed
	 */
	public $script_version;

	/**
	 * (bool) (Optional) Whether to enqueue the script before </body> instead of in the <head>. Default 'true'.
	 *
	 * Default value: true
	 *
	 * @since    1.0.1
	 * @access   public
	 * @var      mixed
	 */
	public $script_in_footer;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.1
	 *
	 * @access   public
	 *
	 * @param Toric  $toric holds instance of Toric class.
	 * @param string $action The name of action.
	 * @param mixed  $php_callback PHP function to respond to ajax calls.
	 * @param string $script_path File path containing you Javascript file.
	 * @param string $mode Determines if script will be exposed to authenticated Ajax actions for logged-in users or non-authenticated Ajax actions for logged-out users or both.
	 * @param array  $ajax_variables Variables to be passed to be available for JS to utilize.
	 * @param string $nonce string used to create WP nonce for verification on PHP callback.
	 * @param string $ajax_object Name of object to be storing JS variables.
	 * @param string $ajax_handle Name of script.
	 * @param array  $script_depends An array of registered script handles this script depends on.
	 * @param mixed  $script_version String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If the version is set to false, a version number is automatically added equal to the current installed Tori Ajax version. If set to null, no version is added.
	 * @param bool   $script_in_footer Whether to enqueue the script before </body> instead of in the <head>.
	 */
	public function __construct(
		Toric $toric,
		$action,
		$php_callback,
		$script_path,
		$mode,
		$ajax_variables,
		$nonce,
		$ajax_object,
		$ajax_handle,
		$script_depends,
		$script_version,
		$script_in_footer
	) {
		// Process variables before use.
		$this->toric = $toric;
		if ( '' === $ajax_handle ) {
			$ajax_handle = $action;
		} else {
			$ajax_handle = $ajax_handle;
		}
		if ( false === $script_version ) {
			$script_version = $this->toric->get_version();
		}

		// Assign values.
		$this->action           = apply_filters( 'toric/ajax/action', $action );// phpcs:ignore
		$this->php_callback     = apply_filters( 'toric/ajax/php_callback', $php_callback, $this->action );// phpcs:ignore
		$this->script_path      = apply_filters( 'toric/ajax/script_path', $script_path, $this->action, $this->php_callback );// phpcs:ignore
		$this->mode             = apply_filters( 'toric/ajax/mode', $mode, $this->action, $this->php_callback, $this->script_path );// phpcs:ignore
		$this->nonce            = apply_filters( 'toric/ajax/nonce', $nonce, $this->action, $this->php_callback, $this->script_path, $this->mode );// phpcs:ignore
		$this->ajax_object      = apply_filters( 'toric/ajax/ajax_object', $ajax_object, $this->action, $this->php_callback, $this->script_path, $this->mode, $this->nonce );// phpcs:ignore
		$this->ajax_handle      = apply_filters( 'toric/ajax/ajax_handle', $ajax_handle, $this->action, $this->php_callback, $this->script_path, $this->mode, $this->nonce, $this->ajax_object );// phpcs:ignore
		$this->script_depends   = apply_filters( 'toric/ajax/script_depends', $script_depends, $this->action, $this->php_callback, $this->script_path, $this->mode, $this->nonce, $this->ajax_object, $this->ajax_handle );// phpcs:ignore
		$this->script_version   = apply_filters( 'toric/ajax/script_version', $script_version, $this->action, $this->php_callback, $this->script_path, $this->mode, $this->nonce, $this->ajax_object, $this->ajax_handle, $this->script_depends );// phpcs:ignore
		$this->script_in_footer = apply_filters( 'toric/ajax/script_in_footer', $script_in_footer, $this->action, $this->php_callback, $this->script_path, $this->mode, $this->nonce, $this->ajax_object, $this->ajax_handle, $this->script_depends, $this->script_version );// phpcs:ignore

		if ( ! array_key_exists( 'ajax_url', $ajax_variables ) ) {
			$ajax_variables['ajax_url'] = admin_url( 'admin-ajax.php' );
		}
		if ( ! array_key_exists( 'action', $ajax_variables ) ) {
			$ajax_variables['action'] = $this->action;
		}
		if ( ! array_key_exists( 'nonce', $ajax_variables ) ) {
			$ajax_variables['nonce'] = wp_create_nonce( $this->nonce );
		}
		// phpcs:ignore
		$this->ajax_variables = apply_filters( 'toric/ajax/ajax_variables', $ajax_variables, $this->action, $this->php_callback, $this->script_path, $this->mode, $this->nonce, $this->ajax_object, $this->ajax_handle, $this->script_depends, $this->script_version, $this->script_in_footer );

		$this->create_ajax();
	}


	/**
	 * Creates Ajax
	 *
	 * @since    1.0.1
	 * @access   public
	 */
	public function create_ajax() {
		if ( is_user_logged_in() ) {
			if ( ( 'both' === $this->mode || 'private' === $this->mode ) ) {
				$this->add_private_ajax();
				$this->admin_enqueue_scripts();
				$this->frontend_enqueue_scripts();
			}
		} else {
			// signed out user on front end.
			if ( ( 'both' === $this->mode || 'public' === $this->mode ) ) {
				$this->add_public_ajax();
				$this->frontend_enqueue_scripts();
			}
		}
	}

	/**
	 * Add admin script
	 *
	 * @since    1.0.1
	 * @access   public
	 */
	public function admin_enqueue_scripts() {
		add_action( 'admin_enqueue_scripts', array( $this, 'toric_enqueue_script_callback' ) );
	}

	/**
	 * Add frond end script
	 *
	 * @since    1.0.1
	 * @access   public
	 */
	public function frontend_enqueue_scripts() {
		add_action( 'wp_enqueue_scripts', array( $this, 'toric_enqueue_script_callback' ) );
	}

	/**
	 * Add public ajax
	 *
	 * @since    1.0.1
	 * @access   public
	 */
	public function add_public_ajax() {
		add_action( 'wp_ajax_nopriv_' . $this->action, array( $this, 'toric_ajax_callback' ) );
	}

	/**
	 * Add private ajax
	 *
	 * @since    1.0.1
	 * @access   public
	 */
	public function add_private_ajax() {
			add_action( 'wp_ajax_' . $this->action, array( $this, 'toric_ajax_callback' ) );
	}

	/**
	 * Toric ajax callback
	 *
	 * @since    1.0.1
	 * @access   public
	 */
	public function toric_ajax_callback() {
		if ( ! isset( $_REQUEST['nonce'] ) ) {
			$msg = __( 'No nonce found', 'toric' );
			$this->terminate( $msg );
		}

		$allowed_html = $this->toric->get_allowed_ajax_html();
		if ( $this->toric->get_wp_version_value() >= 3.6 ) {
			if ( ! wp_verify_nonce( wp_kses( wp_unslash( $_REQUEST['nonce'] ), $allowed_html ), $this->nonce ) ) {
				$msg = __( 'Nonce verification failed', 'toric' );
				$this->terminate( $msg );
			}
		} else {
			if ( ! wp_verify_nonce( wp_kses( stripslashes_deep( $_REQUEST['nonce'] ), $allowed_html ), $this->nonce ) ) {
				$msg = __( 'Nonce verification failed', 'toric' );
				$this->terminate( $msg );
			}
		}

		call_user_func( $this->php_callback );// Fire Ajax callback.

		$this->terminate();
	}



	/**
	 * Toric enqueue script callback
	 *
	 * @since    1.0.1
	 * @access   protected
	 */
	public function toric_enqueue_script_callback() {
		wp_enqueue_script( $this->ajax_handle, $this->script_path, $this->script_depends, $this->script_version, $this->script_in_footer );
		wp_localize_script( $this->ajax_handle, $this->ajax_object, $this->ajax_variables );
	}

	/**
	 * Used in terminating Ajax response.
	 *
	 * @param string $message Error message.
	 *
	 * @since    1.0.1
	 * @access   public
	 */
	private function terminate( $message = '' ) {
		$wp_version_value = $this->toric->get_wp_version_value();
		if ( '' !== $message ) {
			if ( $wp_version_value >= 4.1 ) {
				$message = wp_json_encode( $message );
			} else {
				// phpcs:ignore
				$message = json_encode( $message ); // wp_json_encode was introduced WordPress version 4.1.0.
			}
		}

		if ( $wp_version_value >= 3.4 ) {
			if ( '' !== $message ) {
				wp_die( wp_kses( $message, $this->toric->get_allowed_ajax_html() ) );
			}
			wp_die();
		} else {
			if ( '' !== $message ) {
				die( wp_kses( $message, $this->toric->get_allowed_ajax_html() ) );
			}
			die;
		}

	}



}
