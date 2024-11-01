<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Toric
 * @subpackage Toric/public
 * @author     Alvin Muthui <alvin.muthui@toriajax.com>
 */
class Toric_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Holds Toric instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Toric
	 */
	protected Toric $toric;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param     Toric $toric      Instance of Toric class.
	 */
	public function __construct( Toric $toric ) {
		$this->toric       = $toric;
		$this->plugin_name = $this->toric->get_plugin_name();
		$this->version     = $this->toric->get_version();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Toric_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Toric_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/toric-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Toric_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Toric_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/toric-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Fired on init action
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_shortcode( $this->plugin_name, array( $this, 'toric_shortcode' ) );
	}

	/**
	 * Generates Frontend content
	 *
	 * @since 1.0.0
	 *
	 * @param  array $atts Attributes.
	 *
	 * @return string
	 */
	public function toric_shortcode( $atts ) {
		// normalize attribute keys, lowercase.
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );
		$a    = shortcode_atts(
			array(
				'id' => false,
			),
			$atts
		);
		$id   = $a['id'];
		ob_start();
		echo '<div class="toric shortcode-output">';
		$value = $this->get_database_value_by_meta_key( $id, 'toric-content-meta-box-content' );
		if ( $value ) {
			$content = $this->toric->get_toric_codes()->get_qr_code( $value );
			$allowed_svg_tags_attrs = array(
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
			echo wp_kses( $content, $allowed_svg_tags_attrs );
		} else {
			echo esc_html( __( 'Please enter details', 'toric' ) );
		}
		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Retrieves the fields meta value stored in the DB by meta key.
	 *
	 * @since 1.0.0
	 *
	 * @param  integer $post_id Post ID.
	 * @param  string  $meta_key Meta key.
	 *
	 * @return string
	 */
	public function get_database_value_by_meta_key( int $post_id, $meta_key ) {
		return esc_attr( get_post_meta( $post_id, $meta_key, true ) );
	}

}
