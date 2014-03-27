<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Asq_Shortcodes
{
	function __construct()
	{
		add_shortcode( 'faq', array( &$this, 'faq' ) );
		add_shortcode( 'faq-base', array( &$this, 'faq_base' ) );
		add_shortcode( 'faq-table-of-contents', array( &$this, 'faq_table_of_contents' ) );
	}

	function faq( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'class'				=> '',
			'count'				=> -1,
			'category'			=> '',
			'show_title'		=> false,
			'before_title'		=> '<h3>',
			'after_title'		=> '</h3>',
			'orderby'			=> 'name',
			'order'				=> 'ASC'
		), $atts ) );
		ob_start();

		$args 					= array( 'post_type' => 'asq_question' );
		$args['orderby']		= $orderby;
		$args['order']			= $order;
		$args['posts_per_page'] = ! empty( $count ) ? $count : -1;

		if( ! empty( $category ) )
		{
			$args['tax_query'] = array( 
				array( 
					'taxonomy' 	=> 'asq_category', 
					'field' 	=> 'slug', 
					'terms' 	=> $category 
				) 
			);
		}

		$query 					= new WP_Query( $args );
		$category 				= get_term_by( 'slug', $category, 'asq_category' );

		echo '<div class="asq ' . $class . '">';
			echo '<a id="' . $category->slug . '"></a>';
			if( $show_title )
				echo $before_title . $category->name . $after_title;

			do_action( 'asq_before_accordion', $class );
			if( $query->have_posts() ) : 
				echo '<div class="asq-accordion js-asq-accordion">';
					while ( $query->have_posts() ) : $query->the_post();
						echo '<h4>' . get_the_title() . '</h4>';
						echo '<div>' . get_the_content() . '</div>';
					endwhile; 
				echo '</div>';
			endif;
			wp_reset_postdata();
			do_action( 'asq_after_accordion', $class );
		echo '</div>';

		$output = ob_get_clean(); return $output;
	}

	function faq_base( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'class'				=> '',
			'count'				=> -1,
			'category'			=> '',
			'show_title'		=> true,
			'before_title'		=> '<h3>',
			'after_title'		=> '</h3>',
			'orderby'			=> 'name',
			'order'				=> 'ASC'
		), $atts ) );
		ob_start();

		$terms = get_terms( 'asq_category', array(
			'orderby'			=> $orderby,
			'order'				=> $order
		));

		echo '<div class="asq-overview ' . $class . '">';
			foreach( $terms as $term ) :
				echo do_shortcode('[faq category="' . $term->slug . '" show_title=' . $show_title . ']');
			endforeach;
		echo '</div>';
	}

	function faq_table_of_contents( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'class'				=> '',
			'orderby'			=> 'name',
			'order'				=> 'ASC'
		), $atts ) );

		$terms = get_terms( 'asq_category', array(
			'orderby'			=> $orderby,
			'order'				=> $order
		));

		ob_start();

		echo '<div class="asq-category-list ' . $class . '">';
			do_action( 'asq_before_category_list', $class );
			if( ! empty( $terms ) ) : 
				echo '<ul>';
					foreach( $terms as $term ) :
						echo '<li><a href="#' . $term->slug . '">' . $term->name . '</a></li>';
					endforeach; 
				echo '</ul>';
			endif;
			wp_reset_postdata();
			do_action( 'asq_after_category_list', $class );
		echo '</div>';

		$output = ob_get_clean(); return $output;
	}
}