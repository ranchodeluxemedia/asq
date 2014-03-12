<?php

if( ! defined( 'ABSPATH' ) ) exit;

class Asq_Shortcodes
{
	function __construct()
	{
		add_shortcode( 'faq', array( &$this, 'shortcode' ) );
	}

	function shortcode( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'class'				=> '',
			'count'				=> 10,
			'category'			=> ''
		), $atts ) );

		$query 		= new WP_Query( array( 'post_type' => 'asq_question' ) );

		ob_start();

		echo '<div class="asq ' . $class . '">';
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
}