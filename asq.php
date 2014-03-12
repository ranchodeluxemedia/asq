<?php

/*
Plugin Name: 	Asq
Plugin URI: 	http://github.com/gizburdt
Description: 	Managing FAQ
Version: 		0.1
Author: 		Gizburdt
Author URI: 	http://gizburdt.com
License: 		GPL2
*/

if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'Asq' ) ) :

/**
 * Aqs
 */
class Asq
{
	private static $instance;

	public static function instance()
	{
		if ( ! isset( self::$instance ) ) 
		{
			self::$instance = new Asq;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->add_hooks();
			self::$instance->execute();
		}
		
		return self::$instance;
	}

	function setup_constants()
	{
		if( ! defined( 'ASQ_VERSION' ) ) 
			define( 'ASQ_VERSION', '0.1' );

		if( ! defined( 'ASQ_DIR' ) ) 
			define( 'ASQ_DIR', plugin_dir_path( __FILE__ ) );

		if( ! defined( 'ASQ_URL' ) ) 
			define( 'ASQ_URL', plugin_dir_url( __FILE__ ) );
	}

	function includes()
	{
		include( ASQ_DIR . 'classes/class-content-types.php' );
		include( ASQ_DIR . 'classes/class-shortcodes.php' );
	}

	function add_hooks()
	{
		// Styles
		add_action( 'wp_enqueue_scripts',	array( &$this, 'register_styles' ) );
		add_action( 'wp_enqueue_scripts', 	array( &$this, 'enqueue_styles' ) );
		
		// Scripts
		add_action( 'wp_enqueue_scripts', 	array( &$this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', 	array( &$this, 'enqueue_scripts' ) );
	}

	function execute()
	{
		self::$instance->content_types 		= new Asq_Content_Types;
		self::$instance->shortcodes 		= new Asq_Shortcodes;
	}

	function register_styles()
	{		
		wp_register_style( 'asq', ASQ_URL . 'assets/css/asq.css', false, ASQ_VERSION, 'screen' );
	}

	function enqueue_styles()
	{
		wp_enqueue_style( 'asq' );
	}

	function register_scripts()
	{
		wp_register_script( 'asq', ASQ_URL . 'assets/js/asq.js', null, ASQ_VERSION );
	}
	
	function enqueue_scripts()
	{
		wp_enqueue_script( 'asq' );
		
		self::localize_scripts();
	}

	function localize_scripts()
	{
		wp_localize_script( 'asq', 'Asq', array(
			'home_url'			=> get_home_url(),
			'ajax_url'			=> admin_url( 'admin-ajax.php' ),
			'wp_version'		=> get_bloginfo( 'version' )
		) );
	}
}

endif; // End class_exists check

Asq::instance();