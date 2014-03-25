<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Asq_Shortcodes
{
	function __construct()
	{
		add_shortcode( 'faq', array( &$this, 'faq' ) );
		add_shortcode( 'faq-overview', array( &$this, 'faq_overview' ) );
		add_shortcode( 'faq-category-list', array( &$this, 'faq_category_list' ) );
	}

	function faq( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'class'				=> '',
			'count'				=> -1,
			'category'			=> '',
			'show_title'		=> false,
			'before_title'		=> '<h3>',
			'after_title'		=> '</h3>'
		), $atts ) );

		$args		= array( 'post_type' => 'asq_question' );

		if( ! empty( $category ) )
		{
			$field = 'slug';

			$args['tax_query'] = array( 
				array( 
					'taxonomy' 	=> 'asq_category', 
					'field' 	=> $field, 
					'terms' 	=> $category 
				) 
			);
		}

		$args['posts_per_page'] = ! empty( $count ) ? $count : -1;

		$query 		= new WP_Query( $args );
		$category 	= get_term_by( 'slug', $category, 'asq_category' );

		ob_start();

		echo '<div class="asq ' . $class . '">';
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

	function faq_overview( $atts, $content = null )
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

		foreach( $terms as $term )
			echo do_shortcode('[faq category="' . $category->slug . '" show_title=true]');
		endforeach;

		$output = ob_get_clean(); return $output;
	}

	function faq_category_list( $atts, $content = null )
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