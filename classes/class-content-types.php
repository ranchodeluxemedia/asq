<?php

class Asq_Content_Types
{
	function __construct()
	{
		// Init
		add_action( 'init', array( &$this, 'register_post_types' ) );
		add_action( 'init', array( &$this, 'register_taxonomies' ) );

		// Shortcodes
		add_shortcode( 'faq', array( &$this, 'shortcode' ) );
	}

	/**
	 * Add post types for Asq
	 *
	 * @author 	Gijs Jorissen
	 * @since 	0.1
	 */
	function register_post_types() 
	{
		$labels = array(
		    'name' 					=> sprintf( _x( '%s', 'post type general name', 'office' ), 'Questions' ),
			'singular_name' 		=> sprintf( _x( '%s', 'post type singular title', 'office' ), 'Question' ),
			'menu_name' 			=> sprintf( __( '%s', 'office' ), 'FAQ' ),
			'all_items' 			=> sprintf( __( 'All %s', 'office' ), 'Questions' ),
			'add_new' 				=> sprintf( _x( 'Add New', '%s', 'office' ), 'Question' ),
			'add_new_item' 			=> sprintf( __( 'Add New %s', 'office' ), 'Question' ),
			'edit_item' 			=> sprintf( __( 'Edit %s', 'office' ), 'Question' ),
			'new_item' 				=> sprintf( __( 'New %s', 'office' ), 'Question' ),
			'view_item' 			=> sprintf( __( 'View %s', 'office' ), 'Question' ),
			'items_archive'			=> sprintf( __( '%s Archive', 'office' ), 'Question' ),
			'search_items' 			=> sprintf( __( 'Search %s', 'office' ), 'Questions' ),
			'not_found' 			=> sprintf( __( 'No %s found', 'office' ), 'Questions' ),
			'not_found_in_trash' 	=> sprintf( __( 'No %s found in trash', 'office' ), 'Questions' ),
			'parent_item_colon'		=> sprintf( __( '%s Parent', 'office' ), 'Question' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'question' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => apply_filters( 'asq_question_supports', array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ) )
		);

		register_post_type( 'asq_question', $args );
	}

	function register_taxonomies()
	{
		$labels = array(
			'name' 					=> sprintf( _x( '%s', 'taxonomy general name', 'office' ), 'Categories' ),
			'singular_name' 		=> sprintf( _x( '%s', 'taxonomy singular name', 'office' ), 'Category' ),
		    'search_items' 			=> sprintf( __( 'Search %s', 'office' ), 'Categories' ),
		    'all_items' 			=> sprintf( __( 'All %s', 'office' ), 'Categories' ),
		    'parent_item' 			=> sprintf( __( 'Parent %s', 'office' ), 'Category' ),
		    'parent_item_colon' 	=> sprintf( __( 'Parent %s:', 'office' ), 'Category' ),
		    'edit_item' 			=> sprintf( __( 'Edit %s', 'office' ), 'Category' ), 
		    'update_item' 			=> sprintf( __( 'Update %s', 'office' ), 'Category' ),
		    'add_new_item' 			=> sprintf( __( 'Add New %s', 'office' ), 'Category' ),
		    'new_item_name' 		=> sprintf( __( 'New %s Name', 'office' ), 'Category' ),
		    'menu_name' 			=> sprintf( __( '%s', 'office' ), 'Categories' )
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'faq-category' ),
		);

		register_taxonomy( 'faq_category', array( 'asq_question' ), $args );
	}

	function shortcode( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'count'			=> 10,
			'category'		=> ''
		), $atts ) );

		$query 		= new WP_Query( array( 'post_type' => 'question' ) );

		ob_start();

		echo '<div class="asq">';
			do_action( 'asq_before_accordion' );
			if( $query->have_posts() ) : 
				echo '<div class="asq-accordion">';
					while ( $query->have_posts() ) : $query->the_post();
						echo '<h4>' . get_the_title() . '</h4>';
						echo '<div>' . get_the_content() . '</div>';
					endwhile; 
				echo '</div>';
			endif;
			wp_reset_postdata();
			do_action( 'asq_after_accordion' );
		echo '</div>';

		$output = ob_get_clean(); return $output;
	}
}