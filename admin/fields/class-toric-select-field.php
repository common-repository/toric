<?php
/**
 * Contains Toric_Text_Field class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/admin/fields
 */

/**
 * Creates a text area.
 *
 * @since 1.0.0
 */
class Toric_Select_Field implements Toric_Fields {
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
	 * Constructors Toric_Text_Field
	 *
	 * @since 1.0.0
	 *
	 * @param mixed          $id the unique name for the field.
	 * @param string         $description field's summary.
	 * @param Toric_Meta_Box $meta_box current metabox.
	 * @param array          $attrs array of textarea attributes.
	 */
	public function __construct( $id, $description = '', Toric_Meta_Box $meta_box, array $attrs = array() ) {
		$this->toric       = $meta_box->get_toric();
		$this->id          = $meta_box->get_meta_box_id() . '-' . $id;
		$this->description = $description;
		$this->attrs       = $attrs;
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
	 * Retrieves the fields meta value stored in the DB
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
		$field_id    = esc_attr( $this->id );
		$class       = 'toric ' . $field_id . ' ';
		$attrs       = $this->attrs;
		$placeholder = '';
		$value       = $this->get_database_value( $post->ID );
		if ( ! $value ) {
			$value = 'QRCODE,H,ST,0,0';// default.
		}
		if ( array_key_exists( 'placeholder', $attrs ) ) {
			$placeholder = $attrs['placeholder'];
		}
		?>
		<div class="toric field section display-none">
		<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_attr( $this->description ); ?></label>
		<select  class="<?php echo esc_attr( $class ); ?>" name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>">
			<optgroup label="<?php echo esc_attr( __( 'Square Barcodes', 'toric' ) ); ?>">
			<?php
				$square_barcode_types = $this->toric->get_square_barcode_types();
			foreach ( $square_barcode_types as $type => $label ) {
				$selected = '';
				if ( $value === $type ) {
					$selected = 'selected';
				}
				?>
					<option value="<?php echo esc_attr( $type ); ?>" <?php echo esc_html( $selected ); ?>><?php echo esc_html( $label ); ?></option>
					<?php
			}
			?>
			</optgroup>
			<optgroup label="<?php echo esc_attr( __( 'Linear Barcodes', 'toric' ) ); ?>">
				<?php
				$linear_barcode_types = $this->toric->get_linear_barcode_types();
				foreach ( $linear_barcode_types as $type => $label ) {
					$selected = '';
					if ( $value === $type ) {
						$selected = 'selected';
					}
					?>
					<option value="<?php echo esc_attr( $type ); ?>" <?php echo esc_html( $selected ); ?>><?php echo esc_html( $label ); ?></option>
					<?php
				}
				?>
			</optgroup>
		</select>
		</div>
		<?php
	}

	/**
	 * Saves the field value
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id for post been updated.
	 * @return void
	 */
	public function save( int $post_id ) {
		update_post_meta( $post_id, $this->id, $this->get_post_value() );
	}
}
