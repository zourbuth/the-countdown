<?php
/*
	The Countdown Widget
	@since 0.0.1
	For another improvement, you can drop email to zourbuth@gmail.com or visit http://zourbuth.com/
	Copyright 2017 zourbuth.com (email: zourbuth@gmail.com)

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

class The_Countdown_Widget extends WP_Widget {

	// Prefix for the widget.
	var $prefix;

	// Textdomain for the widget.
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.6.0
	 */
	function __construct() {
	
		$this->prefix = 'the-countdown';
		$this->textdomain = 'the-countdown';
	
		// Give your own prefix name eq. your-theme-name-
		$prefix = '';
		
		// Set up the widget options
		$widget_options = array(
			'classname' => 'the-countdown-widget',
			'description' => esc_html__( 'Advanced widget gives you total control over the countdown.', $this->textdomain )
		);

		// Set up the widget control options
		$control_options = array(
			'width' => 460,
			'height' => 350,
			'id_base' => $this->prefix
		);

		// Create the widget
		parent::__construct( $this->prefix, esc_attr__( 'The Countdown', $this->textdomain ), $widget_options, $control_options );
		
		add_action( 'load-widgets.php', array( &$this, 'load_scripts_styles' ) );
		add_action( 'admin_print_styles', array( &$this, 'admin_print_styles' ) );
		
		// Print the user costum style sheet
		if ( is_active_widget( false, false, $this->id_base, false ) ) {
			add_action( 'wp_head', array( &$this, 'custom_header'), 99 );
			add_action( 'wp_footer', array( &$this, 'custom_footer'), 99 );
		}
	}


	/**
	 * Push the widget stylesheet widget.css into widget admin page
	 * @since 0.6.0
	 */
	function load_scripts_styles() {
		wp_enqueue_style( 'total-dialog', THE_COUNTDOWN_URL . 'css/dialog.css', array(), THE_COUNTDOWN_VERSION );
		wp_register_script( 'total-dialog', THE_COUNTDOWN_URL . 'js/jquery.dialog.js', array( 'jquery' ), THE_COUNTDOWN_VERSION );
		wp_enqueue_script( 'countdown-dialog', THE_COUNTDOWN_URL . 'js/jquery.countdown-dialog.js', array( 'total-dialog' ), THE_COUNTDOWN_VERSION );
	}
	
	
	/**
	 * Custom admin styles
	 * Timestamp icons
	 * @since 0.6.0
	 */	
	function admin_print_styles() {
		echo '
		<style type="text/css">
			.total-options .timestamp { 
				background-image: url(images/date-button.gif); 
				background-position: left top; 
				background-repeat: no-repeat; 
				padding-left: 18px; 
			}
		</style>';
	}
	
	
	/**
	 * Push the widget stylesheet widget.css into widget admin page
	 * @since 0.6.0
	 */		
	function custom_header() {
		$settings = $this->get_settings();
		foreach ( $settings as $key => $setting )
			if( isset( $setting['header'] ) /* for customizer */ && $setting['header'] )
				echo $setting['header'] ."\n";
	}
		
	
	/**
	 * Custom footer
	 * @since 1.1.6
	 */		
	function custom_footer() {
		$settings = $this->get_settings();
		foreach ( $settings as $key => $setting )
			if( $setting['footer'] )
				echo $setting['footer'] ."\n";	
	}
	
	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 * @since 0.6.0
	 */
	function widget( $args, $instance ) {
		extract( $args ); // contains before_widget, after_widget, before_title, after_title, .etc.
	
		// Add the countdown id
		$instance['id'] = $this->number;
		
		echo $before_widget; // output the theme's widget wrapper

		// If a title was input by the user, display it
		if ( ! empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			

		// Print intro text if exist
		if ( ! empty( $instance['intro'] ) )
			echo '<div class="'. $this->id . '-intro-text intro-text">' . $instance['intro'] . '</div>';			
			
		// Countdown block
		echo the_countdown( $instance );
		
		// Print outro text if exist
		if ( ! empty( $instance['outro'] ) )
			echo '<div class="'. $this->id . '-outro-text outro-text">' . $instance['outro'] . '</div>';		
		
		echo $after_widget; // close the theme's widget wrapper
	}

	
	/**
	 * Updates the widget control options for the particular instance of the widget.
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {		
		
		$arguments = the_countdown_arguments( $new_instance );

		// Unset all previous template styles if new template selected
		if ( $new_instance['template'] !== $old_instance['template'] )
			foreach( $arguments as $k => $arg )
				if ( 'template' === $arg['section'] && 'template' !== $k )
					unset( $new_instance[ $k ] );

		$instance = $old_instance;	
		
		// Merge with default value
		foreach ( $arguments as $k => $arg ) {			
			if ( isset( $arg['children'] ) ) {
				foreach ( $arg['children'] as $c => $child ) {			
					if ( 'checkbox' == $child['type'] )
						$instance[ $k ][ $c ] = isset( $new_instance[ $k ][ $c ] ) ? 1 : 0;		
					else
						$instance[ $k ][ $c ] = isset( $new_instance[ $k ][ $c ] ) ? $new_instance[ $k ][ $c ] : $child['default'];					}
			
			// The same approach as above
			} else {
				if ( 'checkbox' == $arg['type'] )
					$instance[ $k ] = isset( $new_instance[ $k ] ) ? 1 : 0;		
				else
					$instance[ $k ] = isset( $new_instance[ $k ] ) ? $new_instance[ $k ] : $arg['default'];	
			}
		}
		
		return $instance;
	}

	
	/**
	 * Displays the widget control options in the Widgets admin screen.
	 * @since 0.6.0
	 */
	function form( $instance ) {
		/*
		// Set up the default form values
		// date-time: mm jj aa hh mn
		$defaults = array(
			'title' 			=> esc_attr__( 'Countdown', $this->textdomain ),
			'title_icon'		=> '',
			'counter' 			=> 'until',
			'until' 			=> array( 0 => date('m'), 1 => date('j'), 2 => date('Y'), 3 => 16, 4 => 53 ),
			'cLabels' 			=> array( 0 => 'Years', 1 => 'Months', 2 => 'Weeks', 3 => 'Days', 4 => 'Hours', 5 => 'Minutes', 6 => 'Seconds' ),
			'cLabels1' 			=> array( 0 => 'Year', 1 => 'Month', 2 => 'Week', 3 => 'Day', 4 => 'Hour', 5 => 'Minute', 6 => 'Second' ),
			'compactLabels' 	=> array( 0 => 'y', 1 => 'm', 2 => 'w', 3 => 'd' ),
			'format' 			=> 'dHMS',
			'expiryUrl' 		=> '',
			'expiryText' 		=> '',
			'alwaysExpire' 		=> false,
			'compact' 			=> false,
			'onExpiry' 			=> '',
			'onTick' 			=> '',
			'tickInterval' 		=> 1,
			'bg_color' 			=> '#f6f7f6',
			'counter_image' 	=> '',
			'counter_color' 	=> '#444444',
			'label_color' 		=> '#444444',
			'intro' 			=> '',
			'outro' 			=> '',
			'header' 			=> '',
			'footer' 			=> '',
			'tab'		=> array( 0 => true, 1 => false, 2 => false, 3 => false, 4 => false, 5 => false )
		);
		*/
		
		the_countdown_dialog( $instance, $this );
	}
}