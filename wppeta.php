<?php

/*
Plugin Name: Plugin PETA
Plugin URI: 
Description: 
Version: 1.0
Author: PETA
Author URI: http://swsolutions.info
License: 
License URI: 
*/

if ( ! defined( 'ABSPATH' ) ) exit;

class wp_PETA {

	public function __construct() {

		// Set the constants needed by the plugin.
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

		// Load the functions files.
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

		// Load the admin style and script.
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( &$this, 'admin_scripts' ) );

		// Register widget.
		add_action( 'widgets_init', array( &$this, 'register_widget' ) );

		// Enqueue the front-end styles.
		add_action( 'wp_enqueue_scripts', array( &$this, 'plugin_style' ), 99 );

	}

	// Defines constants used by the plugin.

	public function constants() {

		// Set constant path to the plugin directory.
		define( 'wppeta_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		// Set the constant path to the plugin directory URI.
		define( 'wppeta_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		// Set the constant path to the includes directory.
		define( 'wppeta_INCLUDES', wppeta_DIR . trailingslashit( 'includes' ) );

		// Set the constant path to the assets directory.
		define( 'wppeta_ASSETS', wppeta_URI . trailingslashit( 'assets' ) );

	}

	
	// Loads the initial files needed by the plugin.
	
	public function includes() {
		require_once( wppeta_INCLUDES . 'functions.php' );
		require_once( wppeta_INCLUDES . 'helpers.php' );
		require_once( wppeta_INCLUDES . 'widget.php' );
	}

	
	// Register custom style and script for the widget settings.

	public function admin_scripts() {
		wp_enqueue_style( 'wppeta-admin-style', trailingslashit( wppeta_ASSETS ) . 'css/wppeta-admin.css', null, null );
		wp_enqueue_script( 'wppeta-cookie-script', trailingslashit( wppeta_ASSETS ) . 'js/cookie.js', array( 'jquery-ui-tabs' ) );
	}

	/**
	 * Register the widget.
	 */
	public function register_widget() {
		register_widget( 'PETA_RECENT_POSTS_WIDGET' );
	}

	/**
	 * Enqueue front-end style.
	 */
	public function plugin_style() {
		wp_enqueue_style( 'wppeta-style', trailingslashit( wppeta_ASSETS ) . 'css/wppeta-frontend.css' );
	}

}

new wp_PETA;
