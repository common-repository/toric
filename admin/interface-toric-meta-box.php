<?php
/**
 * Contains Toric_Meta_Box interface.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/admin
 */

/**
 * Blueprint for defining meta boxes
 *
 * @since 1.0.0
 */
interface Toric_Meta_Box {

	/**
	 * Constructs Toric_Meta_Box instance.
	 *
	 * @since 1.0.0
	 *
	 * @param  Toric $toric Toric instance.
	 */
	public function __construct( Toric $toric);

	/**
	 * Return ID used to generate unique meta box ID
	 *
	 * @return string
	 */
	public function get_id(): string;

	/**
	 * Get unique ID for the metabox.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_meta_box_id();

	/**
	 * Return meta header label
	 *
	 * @return string
	 */
	public function get_label() : string;

	/**
	 * Logic for saving meta values
	 *
	 * @param int $post_id the ID of the current post being saved.
	 * @return void
	 */
	public function save( int $post_id );

	/**
	 * Logic for rendering content inside the meta box
	 *
	 * @param WP_Post $post current post object being viewed.
	 * @param array   $callback_args Callback args.
	 * @return void
	 */
	public function render( WP_Post $post, array $callback_args );

}
