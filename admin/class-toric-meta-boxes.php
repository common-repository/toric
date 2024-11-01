<?php
/**
 * Contains Toric_Meta_Boxes class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/admin
 */

/**
 * Class responsible for creating metaboxes.
 *
 * @since 1.0.0
 */
class Toric_Meta_Boxes {

	/**
	 * Holds instances of meta boxes.
	 *
	 * @var array
	 */
	protected $meta_boxes = array();

	/**
	 * Holds post type of the plugin
	 *
	 * @var [type]
	 */
	protected string $post_type;

	/**
	 * Holds Toric instance
	 *
	 * @since    1.0.0
	 *
	 * @var Toric
	 */
	protected Toric $toric;

	/**
	 * Constructs Toric_Meta_Boxes.
	 *
	 * @since 1.0.0
	 *
	 * @param Toric $toric instance of class Toric.
	 */
	public function __construct( Toric $toric ) {
		$this->toric     = $toric;
		$this->post_type = $toric->get_plugin_name();
		$this->register_meta_boxes();
	}

	/**
	 * Creates metaboxes instances and registers them.
	 *
	 * @since 1.0.0
	 */
	protected function register_meta_boxes() {
		$meta_boxes_class_name = array( 'Toric_Content_Meta_Box', 'Toric_Preview_Meta_Box' );
		foreach ( $meta_boxes_class_name as $meta_box_class_name ) {
			$this->register_meta_box( new $meta_box_class_name( $this->toric ) );
		}
	}

	/**
	 * Adds metabox instance to $this->meta_boxes
	 *
	 * @since 1.0.0
	 *
	 * @param Toric_Meta_Box $meta_box_instance metabox instance.
	 */
	protected function register_meta_box( Toric_Meta_Box $meta_box_instance ) {
		$meta_box_instance->meta_box_id                   = $this->post_type . '-' . $meta_box_instance->get_id(); // create unique ID in WordPress space.
		$this->meta_boxes[ $meta_box_instance->get_id() ] = $meta_box_instance;
	}

	/**
	 * Adds metabox
	 *
	 * @since 1.0.0
	 *
	 * @param [type] $post_type CPT.
	 */
	public function add( $post_type ) {
		$post_types = array( $this->post_type );

		if ( in_array( $post_type, $post_types, true ) ) {
			foreach ( $this->meta_boxes as $meta_box ) {
				add_meta_box(
					$meta_box->meta_box_id,
					$meta_box->get_label(),
					array( $this, 'render' ),
					$post_type,
					'advanced',
					'high',
					array( 'meta_box' => $meta_box )
				);
			}
		}

	}

	/**
	 * Saves metaboxes in the post ID
	 *
	 * @since 1.0.0
	 *
	 * @param integer $post_id ID of the current post.
	 */
	public function save( int $post_id ) {
		// check if no meta boxes added.
		if ( empty( $this->meta_boxes ) ) {
			return $post_id;
		}
		foreach ( $this->meta_boxes as $meta_box ) {
			// check if nonce is valid.
			if ( ! isset( $_POST[ $meta_box->meta_box_id . '_nonce_field' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $meta_box->meta_box_id . '_nonce_field' ] ) ), $meta_box->meta_box_id . '_nonce_action' ) ) {
				return $post_id;
			}
			// Checks if user has permission to edit the post type.
			if ( isset( $_POST['post_type'] ) ) {
				$posted_post_type = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
				if ( $this->post_type === $posted_post_type ) {
					if ( ! current_user_can( 'edit_post', $post_id ) || ! current_user_can( 'edit_post_meta', $post_id ) ) {
						return $post_id;
					}
				}
			} else {
				return $post_id;
			}
			$meta_box->save( $post_id );
		}
	}

	/**
	 * Replaces excerpt metabox with custom metabox.
	 *
	 * @since    1.0.0
	 */
	public function replace_excerpt_metabox() {
		remove_meta_box( 'postexcerpt', 'toric', 'normal' );
		add_meta_box( 'postexcerpt', __( 'Identifier' ), array( $this, 'toric_post_excerpt_meta_box' ), 'toric', 'side', 'low' );
	}


	/**
	 * Undocumented function
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Post $post Post.
	 *
	 * @return void
	 */
	public function toric_post_excerpt_meta_box( WP_Post $post ) {
		?>
		<label class="screen-reader-text" for="excerpt"><?php esc_attr_e( 'Identifier' ); ?></label>
		<input type="text" name="excerpt" id="excerpt" class="toric identifier" value="<?php echo esc_attr( $post->post_excerpt ); ?>">
		<?php
	}

	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post          current post being rendered.
	 * @param array   $callback_args parameters.
	 */
	public function render( WP_Post $post, array $callback_args ) {
		$meta_box = $callback_args['args']['meta_box'];
		wp_nonce_field( $meta_box->meta_box_id . '_nonce_action', $meta_box->meta_box_id . '_nonce_field' );
		$meta_box->render( $post, $callback_args );
	}

	/**
	 * Gets $toric
	 *
	 * @since 1.0.0
	 *
	 * @return Toric
	 */
	public function get_toric() {
		return $this->toric;
	}
}
