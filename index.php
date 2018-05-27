<?php
/*
    Plugin Name: The Countdown
    Plugin URI: http://zourbuth.com/?p=818
    Description: A complete post shortcode, meta options and powerfull widget to use counter in your site. The countdown functionality can easily be added to a content or sidebar area and let your users know the counts. With counting down and up functionality, gives you a full control to your counter.
    Version: 1.2.0
    Author: zourbuth
    Author URI: http://zourbuth.com
    License: GPL2
    
	Copyright 2017  zourbuth.com  (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Exit if accessed directly
 * @since 1.1.2
 */
if ( ! defined( 'ABSPATH' ) )
	exit;


/**
 * Set constant
 * @since 1.0.0
 */
define( 'THE_COUNTDOWN_VERSION', '1.2.0' );
define( 'THE_COUNTDOWN_NAME', 'The Countdown' ); // @since 1.1.8
define( 'THE_COUNTDOWN_SLUG', 'the-countdown' ); // @since 1.1.8
define( 'THE_COUNTDOWN_PATH', plugin_dir_path( __FILE__ ) );
define( 'THE_COUNTDOWN_URL', plugin_dir_url( __FILE__ ) );
define( 'THE_COUNTDOWN_TEXTDOMAIN', 'the-countdown' ); // @since 1.2.0


/**
 * Launch the plugin
 * @since 1.1.2
 */
add_action( 'plugins_loaded', 'the_countdown_plugin_loaded' );


/**
 * Initializes the plugin and it's features with the 'plugins_loaded' action
 * Creating custom constan variable and load necessary file for this plugin
 * Attach the widget on plugin load
 * @since 1.0.0
 */
function the_countdown_plugin_loaded() {
	load_plugin_textdomain( 'the-countdown', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	
	// Load require file
	require_once( THE_COUNTDOWN_PATH . 'inc/g-dialog.php' );
	require_once( THE_COUNTDOWN_PATH . 'inc/argument.php' );
	require_once( THE_COUNTDOWN_PATH . 'inc/main.php' );
	require_once( THE_COUNTDOWN_PATH . 'inc/countdown.php' );
	require_once( THE_COUNTDOWN_PATH . 'inc/template.php' );
	require_once( THE_COUNTDOWN_PATH . 'inc/meta.php' );

	// Loads and registers the widgets
	add_action( 'widgets_init', 'the_countdown_load_widgets' );	
}


/**
 * Load widget, require additional file and register the widget
 * @since 1.0.0
 */
function the_countdown_load_widgets() {
	// Load widget and register the countdown widget
	require_once( THE_COUNTDOWN_PATH . 'inc/widget.php' );
	register_widget( 'The_Countdown_Widget' );
}