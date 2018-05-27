<?php
/*
    The Countdown 1.1
    http://zourbuth.com/plugins/the-countdown
    Copyright 2011  zourbuth.com  (email : zourbuth@gmail.com)

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
 * The Countdown Class
 * 
 * @since 1.2.0
 */	
if ( ! class_exists( 'The_Countdown' )) { 
class The_Countdown {
	 
	/**
	 * Class constructor
	 * 
	 * @since 1.2.0
	 */		
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
		
		add_shortcode( 'the-countdown', array( $this, 'shortcode' ) );
		add_shortcode( 'the-countdown-widget', array( $this, 'widget_shortcode' ) );		
	}
	
			
	/**
	 * Enqueue countdown scripts and styles
	 * @since 1.0.0
	**/
	function enqueue_styles() {
		wp_enqueue_style( 'the-countdown', THE_COUNTDOWN_URL . 'css/styles.css' );
	}
		
			
	/**
	 * Enqueue countdown scripts and styles
	 * @since 1.0.0
	**/
	function admin_enqueue_scripts() {
		wp_enqueue_style( 'forms' );
		wp_enqueue_style( 'total-dialog', THE_COUNTDOWN_URL . 'css/dialog.css', array(), THE_COUNTDOWN_VERSION );
		wp_register_script( 'total-dialog', THE_COUNTDOWN_URL . 'js/jquery.dialog.js', array( 'jquery' ), THE_COUNTDOWN_VERSION );
		wp_enqueue_script( 'countdown-dialog', THE_COUNTDOWN_URL . 'js/jquery.countdown-dialog.js', array( 'total-dialog' ), THE_COUNTDOWN_VERSION );
	}
			
			
	/**
	 * Enqueue countdown scripts and styles
	 * @since 1.0.0
	**/
	function enqueue_scripts() {
		wp_register_script( 'jquery-plugin', THE_COUNTDOWN_URL . 'js/jquery.plugin.min.js', array( 'jquery' ), '1.0.0' );		
		wp_enqueue_script( 'the-countdown', THE_COUNTDOWN_URL . 'js/jquery.countdown.min.js', array( 'jquery', 'jquery-plugin' ), '2.1.0' );
		
		wp_localize_script( 'the-countdown', '_thecountdown', apply_filters( 'the_countdown_localize', array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'post_id'	=> (int) get_the_ID(),
			'nonce'		=> wp_create_nonce( 'the-countdown' ),
		)));		
	}
	
	
	/*
	 * Main function to generate shortcode using total_users_pro() function
	 * Shortcode does not generate the custom style and script 
	**/
	function shortcode( $atts ) {
		
		// **NOTE: this function uses strtolower()
		foreach( array( 'until', 'compactLabels', 'cLabels', 'cLabels1' ) as $i ) 
			if( isset( $atts[ strtolower($i) ] ) )
				$atts[$i] = explode( ",", $atts[ strtolower($i) ] );
		
		$atts['expiryUrl'] = isset( $atts['expiryurl'] ) ? $atts['expiryurl'] : '';
		$atts['expiryText'] = isset( $atts['expirytext'] ) ? rawurldecode( $atts['expirytext'] ) : '';
		$atts['serverSync'] = $atts['serversync'];
		$atts['alwaysExpire'] = $atts['alwaysexpire'];
		$atts['onExpiry'] = isset( $atts['onexpiry'] ) ? rawurldecode( $atts['onexpiry'] ) : '';
		$atts['onTick'] = isset( $atts['ontick'] ) ? rawurldecode( $atts['ontick'] ) : '';
		$atts['tickInterval'] = $atts['tickinterval'];					

		$atts = wp_parse_args( $atts, countdown_default_args() ); // merge with the defaults.			
		
		return $this->the_countdown_pro( $atts ) . countdown_styles( $atts, false ) . countdown_scripts( $atts, false, false ) ;
	}

	
	/*
	 * Main function to generate shortcode using total_users_pro() function
	 * See $defaults arguments for using total_users_pro() function
	 * Shortcode does not generate the custom style and script
	 * @since 1.2.0
	 */
	function widget_shortcode( $atts, $content ) {
		$options = get_option( 'widget_the-countdown' );
		$args = $options[ $atts['id'] ]; 	// overwrite
		$args['id'] = $atts['id'];
		return the_countdown( $args );
	}
	
} new The_Countdown(); };


/**
 * Print the countdown main section to the front page
 * This function can be placed in template files, 
 * see $args in other file for complete arguments
 * @param $args (array) instance values
 * @since 1.0
**/
function the_countdown( $args ) {
	
	// Double check, set up the default values
	$args = wp_parse_args( (array) $args, the_countdown_default_args() ); // merge the user-selected arguments with the defaults.
	$args = the_countdown_update_instance( $args ); // update to latest version		
	
	$html = the_countdown_script_var( $args );
	
	$html .='<div id="the-countdown-'. $args['id'] . '" class="the-countdown">'.
			__( 'Loading...', 'the-countdown' ).
		 '</div>';
		 
	// For development purpose, this only visible for site admin and debug mode
	if ( current_user_can( 'administrator' ) && defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
		$html .= '<p class="tc-dev-tools-description">'. 
					'<strong>'. __( 'Debugging Tools.', 'the-countdown' ) .'</strong><br />'.
					__( 'This only visible for admin and with WP_DEBUG enabled.', 'the-countdown' ) .'</p>';
		$html .= '<ul class="tc-dev-tools">';
			$html .= '<li>'. __( 'Server Time', 'the-countdown' ). ' - ' . current_time( 'mysql' ) .'</li>';
			$html .= '<li>'. __( 'Computer Time', 'the-countdown' ). ' - ' . date("Y-m-d H:i:s") .'</li>';
			$html .= '<li><a class="tc-button tc-toggle" href="#">'. __( 'Pause', 'the-countdown' ) .'</a>'.
						__( 'Use this button to inspect HTML elements.', 'the-countdown' ) .'</li>';	
			$html .= '<li><a class="tc-button tc-setting" href="#">'. __( 'Arguments', 'the-countdown' ) .'</a>'.
						__( 'Current countdown arguments.', 'the-countdown' ) .
						'<pre>'. print_r( $args, true ) . '</pre>' .
					 '</li>';
		$html .= '</ul>';
	}
		
	// Allow filter for countdown templating @since 1.2.0
	$template = apply_filters( 'the_countdown_template', $args );
	$html .= '<div style="display:none" class="countdown-template">'. $template .'</div>';

	// Print the countdown message here to avoid script break ' or "
	if ( isset( $args['onExpiry']['message'] ) )
		$html .= '<div style="display:none" class="countdown-message">'. $args['onExpiry']['message'] .'</div>';
	
	return $html;
}


/**
 * Print the countdown main section to the front page
 * @param $args (array) instance values
 * @since 1.2.0
**/
function the_countdown_script_var( $i ) {
	$arr = array(); $html = '';
	$server_time = gmdate( 'M j, Y H:i:s O', current_time( 'timestamp', 1 ) );
		
	$arr['id'] = $i['id']; // for selector
		
	$ts = $i['timestamp'];
	$arr[$ts[0]] = "new Date( '{$ts[1]}/{$ts[2]}/{$ts[3]} {$ts[4]}:{$ts[5]}' )"; // month/date/year hour:second

	switch( $i['onExpiry']['action'] ) {
		case 'message':
			$arr['expiryText'] = "'.countdown-message'"; // string
			break;
		case 'url':
			$arr['expiryUrl'] = "'{$i['onExpiry']['url']}'"; // string
			break;
		case 'function':
			$arr['onExpiry'] = "'{$i['onExpiry']['function']}'"; // JavaScript function name
			break;
	}
	
	if ( $i['alwaysExpire'] )
		$arr['alwaysExpire'] = $i['alwaysExpire'];
	if ( $i['onTick'] )
		$arr['onTick'] =  "'{$i['onTick']}'";
	if ( $i['tickInterval'] )
		$arr['tickInterval'] = "{$i['tickInterval']}";		
	if ( $i['format'] )
		$arr['format'] = "'{$i['format']}'"; // string			
	if ( $i['compact'] )
		$arr['compact'] = "{$i['compact']}"; // bool		
	if ( $i['serverSync'] )
		$arr['serverSync'] = "'$server_time'"; // bool
			
	if ( $i['cLabels'] && ! is_array( $i['cLabels'] ) ) // don't parse for old version
		$arr['labels'] = "['". implode( "', '", array_map('trim', explode( ',', $i['cLabels'] ) ) ) ."']"; // string[7]
	if ( $i['cLabels1'] && ! is_array( $i['cLabels1'] ) )
		$arr['labels1'] = "['". implode( "', '", array_map('trim', explode( ',', $i['cLabels1'] ) ) ) ."']"; // string[7]
	if ( $i['compactLabels'] && ! is_array( $i['compactLabels'] ) )
		$arr['compactLabels'] = "['". implode( "', '", array_map('trim', explode( ',', $i['compactLabels'] ) ) ) ."']"; // string[4]

	$attr = array();
	foreach( $arr as $k => $a )
		$attr[] = "	$k: $a";

	$_var = "_thecountdown_{$i['id']}";
	$_obj = implode( ", \n", $attr );
	
	$html .= "\n";
	$html .= '<script type="text/javascript">';
	$html .= "\n". '/* <![CDATA[ */' . "\n";
		$html .= "var $_var = { \n";
		$html .= "$_obj \n";
		$html .= "};";
	$html .= "\n". '/* ]]> */' . "\n";
	$html .= '</script>';
	$html .= "\n";
	
	return $html;
}

/**
 * Render dialog for widget or shortcode
 * 
 * @param (Array)	$instance	dialog instance for widget or shortcode
 * @param (Object)	$class		class functions, refer WP_Widget class
 * 
 * @since 1.1.8
**/
function the_countdown_dialog( $instance, $class ) {	
	$instance = wp_parse_args( (array) $instance, the_countdown_default_args() ); // merge the user-selected arguments with the defaults.		
	$instance = the_countdown_update_instance( $instance ); // update to latest version		
	//_tc_debugr( $instance );		
	$sections = $options = array(); // rearrange arguments based on section name		
	foreach ( the_countdown_arguments( $instance ) as $k => $arg ) // add $instance paramater for template options
		if ( isset( $arg['section'] ) && $arg['section'] )
			$sections[ $arg['section'] ][$k] = $arg;
	?>
	<div class="pluginName"><?php echo THE_COUNTDOWN_NAME; ?>
		<span class="pluginVersion"><?php echo THE_COUNTDOWN_VERSION; ?></span></div>

	<div id="tc-<?php echo $class->id ; ?>" class="total-options tabbable tabs-left">
		<input type="hidden" class="tab" name="<?php echo $class->get_field_name( 'section' ); ?>" 
			value="<?php echo $instance['section']; ?>" />

		<ul class="nav nav-tabs">
			<?php foreach( $sections as $k => $v ) : ?>
				<li class="<?php echo $k == $instance['section'] ? 'active' : ''; ?>"><?php echo ucfirst( $k ); ?></li>
			<?php endforeach; ?>
		</ul>
		
		<ul class="tab-content">
			<?php foreach( $sections as $sect => $section ) : ?>
				<li class="tab-pane<?php echo $sect == $instance['section'] ? ' active' : ''; ?>">
					<ul><?php
						foreach( $section as $k => $args ) {							
							$args['_number'] 	= $class->number; // pass widget or shortcode details
							$args['_id'] 		= $class->id;

							if ( isset( $args['children'] ) ) {
								foreach( $args['children'] as $c => $child ) { // loop children
									$child['id']	= $class->get_field_id( "$k-$c" );
									$child['name']	= $class->get_field_name( "{$k}[$c]" );
									$child['value']	= $instance[$k][$c];
									Gumaraphous_Dialog::create_dialog( $child );										
								}
							} else {
								$args['id']		= $class->get_field_id( $k );
								$args['name']	= $class->get_field_name( $k );
								$args['value']	= $instance[$k];
								Gumaraphous_Dialog::create_dialog( $args );
							}
						}
						?>
					</ul>
				</li>
			<?php endforeach; ?>			
		</ul>
	</div>
	<?php
}
