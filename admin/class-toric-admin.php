<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/alvinmuthui/
 * @since      1.0.0
 *
 * @package    Toric
 * @subpackage Toric/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Toric
 * @subpackage Toric/admin
 * @author     Alvin Muthui <alvin.muthui@toriajax.com>
 */
class Toric_Admin {

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
	 * Holds Toric instance
	 *
	 * @since    1.0.0
	 *
	 * @var Toric
	 */
	protected Toric $toric;

	/**
	 * Holds Toric_Meta_Boxes instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Toric_Meta_Boxes
	 */
	protected Toric_Meta_Boxes $toric_meta_boxes;

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
		$this->set_toric_meta_boxes()->get_toric_meta_boxes();

	}



	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/toric-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/toric-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-copy-to-clipboard', plugin_dir_url( __FILE__ ) . 'js/toric-copy-to-clipboard.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Fired on init action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->admin_menu();

		$toric_ajax = new Toric_Ajax(
			$this->toric,
			'toric_admin',
			array( $this, 'ajax_callback' ),
			plugin_dir_url( __FILE__ ) . 'js/toric-ajax.js',
			'private',
			array(
				'code_retrieval_error_message' => esc_html( __( 'Failed to retrieve the code. Update the code or Refresh the page', 'toric' ) ),
			), // $ajax_variables,
			'toric-admin-ajax', // $nonce,
			'toric',
			'toric-admin-ajax', // $ajax_handle,
			array( $this->plugin_name . '-admin' ),
			'1.0.1', // $script_version,
			false// $script_in_footer.
		);
	}

	function ajax_callback() {
		// nonce verification on
		if ( isset( $_REQUEST['value'] ) ) {
			$value = $_REQUEST['value'];
		}
		if ( $value ) {
			echo wp_kses( $this->toric->get_toric_codes()->get_qr_code( $value ), $this->toric->get_allowed_svg_html() );
		} else {
			echo esc_html( __( 'Please enter details', 'toric' ) );
		}

	}

	/**
	 * Adds UI menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_menu() {
		$this->create_cpt();
		$this->register_category();
		$this->register_tag();
	}


	/**
	 * Creates Code CPT
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function create_cpt() {
		$capability = $this->toric->get_capability();        // 'update_core'
		$labels     = array(
			'name'                   => __( 'Tori Codes', 'toric' ),
			'singular_name'          => __( 'Tori Code', 'toric' ),
			'all_items'              => __( 'Codes', 'toric' ),
			'add_new'                => __( 'New Code', 'toric' ),
			'add_new_item'           => __( 'Add Code', 'toric' ),
			'edit_item '             => __( 'Edit Code', 'toric' ),
			'new_item'               => __( 'New Code', 'toric' ),
			'update_item'            => __( 'Update Code', 'toric' ),
			'view_item'              => __( 'View Code', 'toric' ),
			'view_items'             => __( 'View Codes', 'toric' ),
			'search_items'           => __( 'Search Codes', 'toric' ),
			'not_found'              => __( 'Code not found', 'toric' ),
			'not_found_in_trash'     => __( 'No Code found in trash', 'toric' ),
			'insert_into_item'       => __( 'Insert into Code', 'toric' ),
			'items_list'             => __( 'Codes list', 'toric' ),
			'items_list_navigation'  => __( 'Code list navigation', 'toric' ),
			'filter_items_list'      => __( 'Filter Codes list', 'toric' ),
			'items_list_navigation'  => __( 'Codes list navigation', 'toric' ),
			'item_reverted_to_draft' => __( 'Code reverted to draft', 'toric' ),
			'item_updated'           => __( 'Code updated', 'toric' ),

		);
		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'description'         => __( 'Add codes to your setup', 'toric' ),
			'hierarchical'        => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false, // Prevents Barcodes from appearing on Appearance > Menus.
			'menu_icon'           => 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 32 32"><path fill="currentColor" d="M5 5v8h2v2h2v-2h4V5zm8 8v2h2v2h-4v2H5v8h8v-8h6v-2h-2v-2h4v-2h2v2h2v-2h2V5h-8v8zm12 2v2h2v-2zm0 2h-2v2h2zm0 2v2h2v-2zm0 2h-2v-2h-2v2h-5v6h2v-4h4v2h2v-2h1zm-3 4h-2v2h2zm1-8v-2h-2v2zm-12 0v-2H9v2zm-4-2H5v2h2zm8-10v4h-1v2h1v1h2V9h1V7h-1V5zM7 7h4v4H7zm14 0h4v4h-4zM8 8v2h2V8zm14 0v2h2V8zM7 21h4v4H7zm1 1v2h2v-2zm17 3v2h2v-2z"/></svg>' ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			'supports'            => array( 'title' ),
			'rewrite'             => false,
			'capabilities'        => array(
				'edit_post'          => $capability,
				'read_post'          => $capability,
				'delete_post'        => $capability,
				'edit_posts'         => $capability,
				'edit_others_posts'  => $capability,
				'delete_posts'       => $capability,
				'publish_posts'      => $capability,
				'read_private_posts' => $capability,
			),
		);
		register_post_type(
			'toric',
			$args
		);
	}

	/**
	 * Registers plugin category
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_category() {

		$labels = array(
			'name'              => __( 'Categories', 'toric' ),
			'singular_name'     => __( 'Category', 'toric' ),
			'search_items'      => __( 'Search Categories', 'toric' ),
			'all_items'         => __( 'All Categories', 'toric' ),
			'view_item'         => __( 'View Category', 'toric' ),
			'parent_item'       => __( 'Parent Category', 'toric' ),
			'parent_item_colon' => __( 'Parent Category:', 'toric' ),
			'edit_item'         => __( 'Edit Category', 'toric' ),
			'update_item'       => __( 'Update Category', 'toric' ),
			'add_new_item'      => __( 'Add New Category', 'toric' ),
			'new_item_name'     => __( 'New Category Name', 'toric' ),
			'not_found'         => __( 'No Categories Found', 'toric' ),
			'back_to_items'     => __( 'Back to Categories', 'toric' ),
			'menu_name'         => __( 'Category', 'toric' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'Category' ),
		);

		register_taxonomy( $this->plugin_name . '_category', $this->plugin_name, $args );

	}

	/**
	 * Registers plugin category
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_tag() {

		$labels = array(
			'name'              => __( 'Tags', 'toric' ),
			'singular_name'     => __( 'Tag', 'toric' ),
			'search_items'      => __( 'Search Tags', 'toric' ),
			'all_items'         => __( 'All Tags', 'toric' ),
			'view_item'         => __( 'View Tag', 'toric' ),
			'parent_item'       => __( 'Parent Tag', 'toric' ),
			'parent_item_colon' => __( 'Parent Tag:', 'toric' ),
			'edit_item'         => __( 'Edit Tag', 'toric' ),
			'update_item'       => __( 'Update Tag', 'toric' ),
			'add_new_item'      => __( 'Add New Tag', 'toric' ),
			'new_item_name'     => __( 'New Tag Name', 'toric' ),
			'not_found'         => __( 'No Tags Found', 'toric' ),
			'back_to_items'     => __( 'Back to Tags', 'toric' ),
			'menu_name'         => __( 'Tag', 'toric' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'Tag' ),
		);

		register_taxonomy( $this->plugin_name . '_tag', $this->plugin_name, $args );

	}



	/**
	 * Fired on save post action
	 *
	 * @since 1.0.0
	 *
	 * @param  int     $post_ID Post ID.
	 * @param  WP_Post $ajax post object.
	 * @param  bool    $update Whether this is an existing post being updated.
	 *
	 * @return void
	 */
	public function save_post( int $post_ID, WP_Post $ajax, bool $update ) {
		$this->get_toric_meta_boxes()->save( $post_ID );
	}

	/**
	 * Fired on add meta boxes to add metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $post_type Post type.
	 *
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {
		$this->get_toric_meta_boxes()->add( $post_type );
	}

	/**
	 * Sets $this->toric_meta_boxes
	 *
	 * @since 1.0.0
	 *
	 * @return Toric_Admin
	 */
	public function set_toric_meta_boxes() {
		$this->toric_meta_boxes = new Toric_Meta_Boxes( $this->toric );
		return $this;
	}

	/**
	 * Return $toric_meta_boxes.
	 *
	 * @since    1.0.0
	 */
	public function get_toric_meta_boxes() {
		return $this->toric_meta_boxes;
	}

	/**
	 * Fired by 'do_meta_boxes'
	 *
	 * @since    1.0.0
	 */
	public function do_meta_boxes() {
	}




	/**
	 * Retrieves terms.
	 *
	 * @param string  $taxonomy taxonomy name.
	 * @param boolean $post_id the id of post. Default is set to false.
	 * @return array array of terms.
	 */
	public function get_attached_terms( string $taxonomy, $post_id = false ) {
		if ( $post_id ) {
			return get_the_terms( $post_id, $taxonomy );
		}
		global $post;
		return get_the_terms( $post->ID, $taxonomy );
	}




	/**
	 * Set custom columns to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $columns Columns.
	 *
	 * @return array $columns
	 */
	public function set_custom_columns( array $columns ) {
		$title = $columns['title'];
		$date  = $columns['date'];
		$cb    = $columns['cb'];
		unset( $columns );// Removes existing columns.
		$columns['cb']                                 = $cb;
		$columns['title']                              = $title;
		$columns['active']                             = esc_html__( 'Active', 'toric' );
		$columns['shortcode']                          = esc_html__( 'Shortcode', 'toric' );
		$columns[ $this->plugin_name . '_categories' ] = esc_html__( 'Categories', 'toric' );
		$columns[ $this->plugin_name . '_tags' ]       = esc_html__( 'Tags', 'toric' );
		$columns['usage']                              = esc_html__( 'Usage', 'toric' );
		$columns['name']                               = esc_html__( 'Author Name', 'toric' );
		$columns['date']                               = $date;

		return $columns;
	}






	/**
	 * Obtain data and set to the columns.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $column Columns.
	 * @param  integer $post_id Post ID.
	 *
	 * @return void
	 */
	public function set_custom_columns_data( string $column, int $post_id ) {
		$data = get_post_meta( $post_id, '', true );
		switch ( $column ) {
			case 'name':
				$name  = isset( $data['name'] ) ? $data['name'] : get_the_author_meta( 'user_login', get_post( $post_id )->post_author );
				$email = isset( $data['email'] ) ? $data['email'] : get_the_author_meta( 'user_email', get_post( $post_id )->post_author );
				echo '<strong>' . esc_attr( $name ) . '</strong><br/><a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
				break;

			case 'shortcode':
				$post_type         = $this->plugin_name;
				$shortcode         = '[' . $post_type . ' id="' . $post_id . '"]';
				$p_id              = $post_type . '-shortcode-' . $post_id;
				$tooltip_id        = $post_type . '-shortcode-tooltip-' . $post_id;
				$button_text       = __( 'Copy shortcode', 'toric' );
				$copy_to_clipboard = __( 'Copy to clipboard', 'toric' );
				/* translators: %s is replaced with the text copied */
				$copied         = __( 'Copied: %s', 'toric' );
				$html_elements  = '<p id="' . $p_id . '">' . $shortcode . '</p>';
				$html_elements .= '<div class="' . $post_type . ' tooltip">';
				$html_elements .= '<button type="button" onclick="toricCopyToClipboard(\'' . $p_id . '\',\'' . $tooltip_id . '\',\'' . $copied . '\')" onmouseout="showTooltipOnCopyToClipboardButton(\'' . $tooltip_id . '\', \'' . $copy_to_clipboard . '\')">';
				$html_elements .= '<span class="' . $post_type . ' tooltiptext" id="' . $tooltip_id . '">' . $copy_to_clipboard . '</span>';
				$html_elements .= $button_text;
				$html_elements .= '</button>';
				$html_elements .= '</div>';
				echo wp_kses(
					$html_elements,
					array(
						'p'      => array(
							'id' => array(),
						),
						'div'    => array(
							'class' => array(),
						),
						'button' => array(
							'type'       => array(),
							'onclick'    => array(),
							'onmouseout' => array(),
						),
						'span'   => array(
							'class' => array(),
							'id'    => array(),
						),
					)
				);
				break;

			case 'active':
				if ( 'publish' === get_post_status( $post_id ) ) {
					echo '<strong>YES</strong>';
				} else {
					echo 'NO';
				}
				break;
			case $this->plugin_name . '_categories':
				$categories = $this->get_attached_terms( $this->plugin_name . '_category', $post_id );
				if ( $categories && ! is_a( $categories, 'WP_Error' ) ) {
					$count = count( $categories );
					for ( $i = 0;$i < $count; $i++ ) {
						$category = $categories[ $i ];
						echo '<a href="' . esc_attr( get_edit_term_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
						if ( ( $count - 1 ) !== $i ) {
							echo ', ';
						}
					}
				} else {
					echo '<span aria-hidden="true">—</span><span class="screen-reader-text">' . esc_html__( 'No categories', 'toric' ) . '</span>';
				}
				break;
			case $this->plugin_name . '_tags':
				$tags = $this->get_attached_terms( $this->plugin_name . '_tags', $post_id );
				if ( $tags && ! is_a( $tags, 'WP_Error' ) ) {
					$count = count( $tags );
					for ( $i = 0;$i < $count; $i++ ) {
						$tag = $tags[ $i ];
						echo '<a href="' . esc_url( get_edit_term_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a>';
						if ( ( $count - 1 ) !== $i ) {
							echo ', ';
						}
					}
				} else {
					echo '<span aria-hidden="true">—</span><span class="screen-reader-text">' . esc_html__( 'No tags', 'toric' ) . '</span>';
				}
				break;

		}

	}


	/**
	 * Allows columns to be sortable
	 *
	 * @since 1.0.0
	 *
	 * @param  array $columns Columns.
	 *
	 * @return array $columns Columns.
	 */
	public function set_custom_columns_sortable( array $columns ) {
		$columns['title']     = 'title';
		$columns['name']      = 'name';
		$columns['shortcode'] = 'shortcode';
		$columns['active']    = 'active';
		$columns['date']      = 'date';
		return $columns;
	}

	/**
	 * Modifies order behavior.
	 *
	 * @since 1.0.0
	 *
	 * @param  WP_Query $query Query.
	 *
	 * @return void
	 */
	public function custom_columns_orderby( WP_Query $query ) {
		if ( ! is_admin() ) {
			return;
		}
		$orderby = $query->get( 'orderby' );

		if ( 'name' === $orderby ) {
			$query->set( 'orderby', 'author_name' );
		} elseif ( 'shortcode' === $orderby ) {
			$query->set( 'orderby', 'ID' );
		} elseif ( 'active' === $orderby ) {
			$order  = $query->get( 'order' );
			$filter = '';
			if ( 'ASC' === strtoupper( $order ) ) {
				$filter = function() {
					return 'post_status ASC';
				};
			} else {
				$filter = function() {
					return 'post_status DESC';
				};
			}
			add_filter( 'posts_orderby', $filter );
		}

	}


}
