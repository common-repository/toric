<?php
/**
 * Contains Toric_Content_Meta_Box.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/admin/meta-boxes
 */

/**
 * Settings metabox.
 *
 * @since 1.0.0
 */
class Toric_Content_Meta_Box extends Toric_Meta_Boxes implements Toric_Meta_Box {
	/**
	 * Unique ID
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Metabox Label or title
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $label;

	/**
	 * Fields added to the metabox.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Unique ID for the metabox.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $meta_box_id;

	/**
	 * Constructs $this instance.
	 *
	 * @since 1.0.0
	 *
	 * @param  Toric $toric Toric instance.
	 */
	public function __construct( Toric $toric ) {
		$this->id                         = 'content-meta-box';
		$this->label                      = __( 'Enter Details', 'toric' );
		$this->toric                      = $toric;
		$this->meta_box_id                = $toric->get_plugin_name() . '-' . $this->id; // create unique ID in WordPress space.
		$inputs                           = array(
			'private' => __( 'Signed in', 'toric' ),
			'both'    => __( 'Both', 'toric' ),
			'public'  => __( 'Signed out', 'toric' ),
		);
		$field                            = new Toric_Select_Field( 'select_barcode_type', __( 'Barcode Type:', 'toric' ), $this );
		$this->fields[ $field->get_id() ] = $field;
		$field                            = new Toric_Text_Field( 'content', __( 'Enter QR code content:', 'toric' ), $this );
		$this->fields[ $field->get_id() ] = $field;
	}

	/**
	 * Returns ID used to generate unique meta box ID
	 *
	 * @return string
	 */
	public function get_id() : string {
		return $this->id;
	}

	/**
	 * Get unique ID for the metabox.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_meta_box_id() {
		return $this->meta_box_id;
	}

	/**
	 * Return meta header label
	 *
	 * @return string
	 */
	public function get_label() : string {
		return $this->label;
	}

	/**
	 * Function to save meta values on post save
	 *
	 * @param int $post_id The ID of the current post being saved.
	 */
	public function save( $post_id ) {

		foreach ( $this->fields as $field ) {
			$field->save( $post_id );
		}
	}

	/**
	 * Logic for rendering content inside the meta box
	 *
	 * @param WP_Post $post current post object being viewed.
	 * @param array   $callback_args Callback args.
	 * @return void
	 */
	public function render( WP_Post $post, array $callback_args ) {

		$meta_box = $callback_args['args']['meta_box'];
		foreach ( $this->fields as $field ) {
			$field->render( $post, $meta_box );
		}
	}

}
