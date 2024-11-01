<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Toric
 * @subpackage Toric/includes
 * @author     Alvin Muthui <alvin.muthui@toriajax.com>
 */
class Toric {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Toric_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Ajax Access Capability
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $capability;

	/**
	 * Plugin base_dir.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin_base_dir;

	/**
	 * To hold an instance of \Com\Tecnick\Barcode\Barcode
	 *
	 * @since 1.0.0
	 *
	 * @var \Com\Tecnick\Barcode\Barcode
	 */
	protected $barcode;

	/**
	 * To hold an instance of Toric_Codes
	 *
	 * @since 1.0.0
	 *
	 * @var Toric_Codes
	 */
	protected $toric_codes;

	/**
	 * $square_barcode_types variable.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $square_barcode_types;

	/**
	 * $linear_barcode_types variable.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $linear_barcode_types;

	/**
	 * The WordPress major and minor version stored as int or float.
	 *
	 * @since    1.0.1
	 * @access   protected
	 * @var      string    $wp_version_value    Format: major.minor.
	 */
	protected $wp_version_value;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TORIC_VERSION' ) ) {
			$this->version = TORIC_VERSION;
		} else {
			$this->version = '1.0.1';
		}
		$this->plugin_name = 'toric';

		$this->set_settings();
		$this->plugin_base_dir = plugin_dir_path( __DIR__ );
		$this->set_wp_version_value();

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Set settings variables
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function set_settings() {
		$this->capability           = 'update_core';
		$this->linear_barcode_types = array(
			'C128A'    => __( 'CODE 128 A', 'toric' ),
			'C128B'    => __( 'CODE 128 B', 'toric' ),
			'C128C'    => __( 'CODE 128 C', 'toric' ),
			'C128'     => __( 'CODE 128', 'toric' ),
			'C39E+'    => __( 'CODE 39 EXTENDED + CHECKSUM', 'toric' ),
			'C39E'     => __( 'CODE 39 EXTENDED', 'toric' ),
			'C39+'     => __( 'CODE 39 + CHECKSUM', 'toric' ),
			'C39'      => __( 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9', 'toric' ),
			'C93'      => __( 'CODE 93 - USS-93', 'toric' ),
			'CODABAR'  => __( 'CODABAR', 'toric' ),
			'CODE11'   => __( 'CODE 11', 'toric' ),
			'EAN13'    => __( 'EAN 13', 'toric' ),
			'EAN2'     => __( 'EAN 2-Digits UPC-Based Extension', 'toric' ),
			'EAN5'     => __( 'EAN 5-Digits UPC-Based Extension', 'toric' ),
			'EAN8'     => __( 'EAN 8', 'toric' ),
			'I25+'     => __( 'Interleaved 2 of 5 + CHECKSUM', 'toric' ),
			'I25'      => __( 'Interleaved 2 of 5', 'toric' ),
			'IMB'      => __( 'IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200', 'toric' ),
			'IMBPRE'   => __( 'IMB pre-processed', 'toric' ),
			'KIX'      => __( 'KIX (Klant index - Customer index)', 'toric' ),
			'MSI+'     => __( 'MSI + CHECKSUM (modulo 11)', 'toric' ),
			'MSI'      => __( 'MSI (Variation of Plessey code)', 'toric' ),
			'PHARMA2T' => __( 'PHARMACODE TWO-TRACKS', 'toric' ),
			'PHARMA'   => __( 'PHARMACODE', 'toric' ),
			'PLANET'   => __( 'PLANET', 'toric' ),
			'POSTNET'  => __( 'POSTNET', 'toric' ),
			'RMS4CC'   => __( 'RMS4CC (Royal Mail 4-state Customer Bar Code)', 'toric' ),
			'S25+'     => __( 'Standard 2 of 5 + CHECKSUM', 'toric' ),
			'S25'      => __( 'Standard 2 of 5', 'toric' ),
			'UPCA'     => __( 'UPC-A', 'toric' ),
			'UPCE'     => __( 'UPC-E', 'toric' ),
		);

		$this->square_barcode_types = array(
			'QRCODE,H,ST,0,0'  => __( 'QR-CODE WITH PARAMETERS', 'toric' ),
			'QRCODE'           => __( 'QR-CODE', 'toric' ),
			'LRAW'             => __( '1D RAW MODE (comma-separated rows of 01 strings)', 'toric' ),
			'SRAW'             => __( '2D RAW MODE (comma-separated rows of 01 strings)', 'toric' ),
			'PDF417'           => __( 'PDF417 (ISO/IEC 15438:2006)', 'toric' ),
			'DATAMATRIX'       => __( 'DATAMATRIX (ISO/IEC 16022) SQUARE', 'toric' ),
			'DATAMATRIX,R'     => __( 'DATAMATRIX Rectangular (ISO/IEC 16022) RECTANGULAR', 'toric' ),
			'DATAMATRIX,S,GS1' => __( 'GS1 DATAMATRIX (ISO/IEC 16022) SQUARE GS1', 'toric' ),
			'DATAMATRIX,R,GS1' => __( 'GS1 DATAMATRIX (ISO/IEC 16022) RECTANGULAR GS1', 'toric' ),
		);

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Toric_Loader. Orchestrates the hooks of the plugin.
	 * - Toric_i18n. Defines internationalization functionality.
	 * - Toric_Admin. Defines all hooks for the admin area.
	 * - Toric_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-toric-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-toric-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-toric-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/interface-toric-fields.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/interface-toric-meta-box.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-toric-meta-boxes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-boxes/class-toric-content-meta-box.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/meta-boxes/class-toric-preview-meta-box.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/fields/class-toric-textarea-field.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/fields/class-toric-text-field.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/fields/class-toric-select-field.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views/class-toric-preview.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-toric-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/codes/class-toric-codes.php';

		/**
		 * The class responsible for creating the AJAX
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ajax/class-toric-ajax.php';

		if ( ! class_exists( '\Com\Tecnick\Barcode\Barcode' ) ) {
			spl_autoload_register( array( $this, 'third_party_lib_auto_loader' ) );
		}
		$this->barcode     = new \Com\Tecnick\Barcode\Barcode();
		$this->toric_codes = new Toric_Codes( $this );
		$this->loader      = new Toric_Loader();
	}

	/**
	 * Third party libraries auto loader.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $class_name Class Name.
	 *
	 * @return void
	 */
	public function third_party_lib_auto_loader( $class_name ) {
		if ( str_contains( $class_name, 'Com\\Tecnick\\Barcode\\' ) || str_contains( $class_name, 'Com\\Tecnick\\Color\\' ) ) {
			$file = $class_name . '.php';
			$file = str_replace( 'Com\\Tecnick\\Barcode\\', '/tc-lib-barcode/src/', $file );
			$file = str_replace( 'Com\\Tecnick\\Color\\', '/tc-lib-color/src/', $file );
			$file = $this->get_plugin_base_dir() . 'includes/codes' . $file;
			require_once $file;
		}

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Toric_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Toric_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Toric_Admin( $this );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'init' );
		$this->loader->add_filter( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes' );
		$this->loader->add_filter( 'do_meta_boxes', $plugin_admin, 'do_meta_boxes' );
		$this->loader->add_filter( 'save_post_' . $this->plugin_name, $plugin_admin, 'save_post', 10, 3 );
		// manages admin columns.
		$this->loader->add_filter( 'manage_' . $this->plugin_name . '_posts_columns', $plugin_admin, 'set_custom_columns' );// Sets custom columns in cpt list.
		$this->loader->add_filter( 'manage_' . $this->plugin_name . '_posts_custom_column', $plugin_admin, 'set_custom_columns_data', 10, 2 );// Add data to columns.
		$this->loader->add_filter( 'manage_edit-' . $this->plugin_name . '_sortable_columns', $plugin_admin, 'set_custom_columns_sortable' );// Make columns sortable.
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin, 'custom_columns_orderby' );// Make columns sortable.

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Toric_Public( $this );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'init' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Toric_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Returns $this->capability
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_capability() {
		return apply_filters( 'toric/setting/capability', $this->capability, $this );// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
	}

	/**
	 * Gets plugin_base_dir
	 *
	 * @since 1.0.0
	 *
	 * @string void
	 */
	public function get_plugin_base_dir() {
		return $this->plugin_base_dir;
	}

	/**
	 * Gets barcode
	 *
	 * @since 1.0.0
	 *
	 * @return \Com\Tecnick\Barcode\Barcode
	 */
	public function get_barcode() {
		return $this->barcode;
	}

	/**
	 * Gets toric_codes
	 *
	 * @since 1.0.0
	 *
	 * @return Toric_Codes
	 */
	public function get_toric_codes() {
		return $this->toric_codes;
	}

	/**
	 * Retrieves $linear_barcode_types
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_linear_barcode_types() {
		return $this->linear_barcode_types;
	}

	/**
	 * Retrieves $square_barcode_types
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_square_barcode_types() {
		return $this->square_barcode_types;
	}

	function get_allowed_svg_html(){
		return array(
			'svg'  => array(
				'xmlns'   => true,
				'width'   => true,
				'height'  => true,
				'viewbox' => true,
				'version' => true,
			),
			'desc' => true,
			'rect' => array(
				'x'              => true,
				'y'              => true,
				'width'          => true,
				'height'         => true,
				'fill'           => true,
				'stroke'         => true,
				'stroke-width'   => true,
				'stroke-linecap' => true,
			),
			'g'    => array(
				'id'             => true,
				'fill'           => true,
				'stroke'         => true,
				'stroke-width'   => true,
				'stroke-linecap' => true,
			),
		);
	}

	function get_allowed_ajax_html(){
		return $this->get_allowed_svg_html();
	}

	/**
	 * Set $wp_version_value value for use when checking WordPress versions.
	 *
	 * @since    1.0.1
	 * @access   private
	 */
	private function set_wp_version_value() {
		$wp_version_array       = explode( '.', get_bloginfo( 'version' ) );
		$wp_version_array_count = count( $wp_version_array );
		for ( $i = 0;$i < $wp_version_array_count;$i++ ) {
			if ( 0 === $i ) {
				$this->wp_version_value = (int) $wp_version_array[ $i ];
			} elseif ( 1 === $i ) {
				$this->wp_version_value += ( (int) $wp_version_array[ $i ] ) * 0.1;
				break;
			}
		}
	}

	/**
	 * Retrieve the WordPress major and minor version value.
	 *
	 * @since     1.0.1
	 * @return    float|int   The WordPress major and minor version value.
	 */
	public function get_wp_version_value() {
		return $this->wp_version_value;
	}

}
