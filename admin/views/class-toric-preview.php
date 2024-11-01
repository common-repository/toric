<?php
/**
 * Contains Toric_Preview class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/admin/views
 */

/**
 * Creates a preview view.
 *
 * @since 1.0.0
 */
class Toric_Preview implements Toric_Fields {
	/**
	 * The unique name for the field
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $id;

	/**
	 * Field's summary
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	protected $description;

	/**
	 * Textarea attributes
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected array $attrs;

	/**
	 * Holds Toric instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Toric
	 */
	protected Toric $toric;

	/**
	 * Constructs Preview
	 *
	 * @since 1.0.0
	 *
	 * @param Toric          $toric Toric instance.
	 * @param Toric_Meta_Box $meta_box current metabox.
	 * @param array          $attrs array of textarea attributes.
	 */
	public function __construct( Toric $toric, Toric_Meta_Box $meta_box, $attrs = array() ) {
		$this->toric = $toric;
		$this->id    = $meta_box->get_meta_box_id() . '-preview';
		$this->attrs = $attrs;
	}

	/**
	 * Returns ID used to generate unique meta box ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_id() : string {
		return $this->id;
	}

	/**
	 * Retrieves the fields meta value stored in the DB.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id the ID of the current post.
	 * @return mixed
	 */
	public function get_database_value( int $post_id ) {
		return esc_attr( get_post_meta( $post_id, $this->id, true ) );
	}

	/**
	 * Retrieves the fields meta value stored in the DB by meta key.
	 *
	 * @since 1.0.0
	 *
	 * @param  integer $post_id ID of current post.
	 * @param  string  $meta_key Meta key.
	 *
	 * @return string
	 */
	public function get_database_value_by_meta_key( int $post_id, $meta_key ) {
		return esc_attr( get_post_meta( $post_id, $meta_key, true ) );
	}

	/**
	 * Retrieves the POSTed value of the field
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_post_value() {
		$posted_value = null;
		// nonce verified in admin\class-toric-meta-boxes.php#L122.
		if ( isset( $_POST[ $this->id ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$posted_value = sanitize_text_field( wp_unslash( $_POST[ $this->id ] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		}
		return $posted_value;
	}

	/**
	 * Renders the field view.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post        $post current object.
	 * @param Toric_Meta_Box $meta_box current Metabox.
	 * @return void
	 */
	public function render( WP_Post $post, Toric_Meta_Box $meta_box ) {
		echo '<div class="toric ' . esc_attr( $this->id ) . '" id="' . esc_attr( $this->id ) . '" >';
		$value = $this->get_database_value_by_meta_key( $post->ID, 'toric-content-meta-box-content' );
		if ( $value ) {
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
			echo wp_kses( $this->toric->get_toric_codes()->get_qr_code( $value ), $allowed_svg_tags_attrs );
		} else {
			echo esc_html( __( 'Please enter details', 'toric' ) );
		}
		echo '</div>';

	}

	/**
	 * Saves the value
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id for post been updated.
	 * @return void
	 */
	public function save( int $post_id ) {

	}
}
