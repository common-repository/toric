<?php
/**
 * Contains Toric_Fields interface.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/admin
 */

/**
 * Blueprint for defining fields
 *
 * @since 1.0.0
 */
interface Toric_Fields {

	/**
	 * Returns ID used to generate unique meta box ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_id() : string;

	/**
	 * Retrieves the fields meta value stored in the DB
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id the ID of the current post.
	 * @return mixed
	 */
	public function get_database_value( int $post_id );

	/**
	 * Retrieves the POSTed value of the field
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_post_value();

	/**
	 * Renders the field view.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post        $post current object.
	 * @param Toric_Meta_Box $meta_box current Metabox.
	 * @return void
	 */
	public function render( WP_Post $post, Toric_Meta_Box $meta_box );

	/**
	 * Saves the field value
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id for post been updated.
	 * @return void
	 */
	public function save( int $post_id );

}




