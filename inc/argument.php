<?php
/*
    The Countdown Utilty 
    http://zourbuth.com/plugins/the-countdown
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
 * The countdown parameters
 * 
 * @param $instance (array) widget or shortcode instance
 * @since 1.2.0
 */	
function the_countdown_arguments( $instance = null ) {

	$ts_singular = implode( ', ', array( // allow for translation 
		__( 'Year', 'the-countdown'),
		__( 'Month', 'the-countdown'),
		__( 'Week', 'the-countdown'),
		__( 'Day', 'the-countdown'),
		__( 'Hour', 'the-countdown'),
		__( 'Minute', 'the-countdown'),
		__( 'Second', 'the-countdown'),
	));
	$ts_plural = implode( ', ', array( // allow for translation 
		__( 'Years', 'the-countdown'),
		__( 'Months', 'the-countdown'),
		__( 'Weeks', 'the-countdown'),
		__( 'Days', 'the-countdown'),
		__( 'Hours', 'the-countdown'),
		__( 'Minutes', 'the-countdown'),
		__( 'Seconds', 'the-countdown'),
	));	

	$templates = apply_filters( 'the_countdown_register_template', array() );

	$arguments = array(
		/* Stored current selected section/tab */
		'section' => array(
			'label' => '',
			'type' => '_section',
			'description' => '',
			'default' => 'general',
		),
		
		/* General */
		'title' => array(
			'section' => 'general',
			'label' => 'Widget Title',
			'type' => 'text',
			'description' => __( 'Give the widget title, or leave it empty for no title.', 'the-countdown' ),
			'default' => __( 'The Countdown', 'the-countdown' ),
		),
		'timestamp' => array(
			'section' => 'general',
			'label' => 'Timestamp Picker',
			'type' => 'timestamp',
			'description' => __( "Date/time to count up from or numeric for seconds offset, or string for unit offset(s): 'Y' years, 'O' months, 'W' weeks, 'D' days, 'H' hours, 'M' minutes, 'S' seconds.", 'the-countdown' ),
			'default' => array( // counter: year, month, date, hour, minute
				0 => 'until', 1 => date('m'), 2 => date('j'), 
				3 => date('Y'), 4 => 16, 5 => 53 
			), 
		),
		
		/* On expiry group */
		'onExpiry' => array(
			'section' 		=> 'general',
			'type' 			=> 'group',
			'default' 		=> array(),
			'children'		=> array(
				'action' => array(
					'label' 		=> __( 'On Expiry', 'the-countdown' ),
					'type' 			=> 'select',
					'description' 	=> __( 'Select an action below to do after counter expired.', 'the-countdown' ),
					'options' 		=> array(
						'message'		=> __( 'Show text or HTML message', 'the-countdown' ),
						'url'			=> __( 'Redirect to URL', 'the-countdown' ),
						'function'		=> __( 'Run JavaScript function', 'the-countdown' ),
					),
					'onchange'		=> "wpWidgets.save(jQuery(this).closest(\"div.widget\"),0,1,0)",
					'spinner'		=> true,
					'default' 		=> 'message'
				),
			)
		),
		
		/** Advanced **/
		'serverSync' => array(
			'section' => 'advanced',
			'label' => __( 'Server Sync', 'the-countdown' ),
			'type' => 'checkbox',
			'description' => __( 'Synchronise the client\'s time with that of the server', 'the-countdown' ),
			'default' => true,
		),
		'alwaysExpire' => array(
			'section' => 'advanced',
			'label' => __( 'Always Expire', 'the-countdown' ),
			'type' => 'checkbox',
			'description' => __( 'Check if you want to trigger onExpiry even if never counted down', 'the-countdown' ),
			'default' => false,
		),
		'compact' => array(
			'section' => 'advanced',
			'label' => 'Compact Version',
			'type' => 'checkbox',
			'description' => __( 'True to display in a compact format, false for an expanded one', 'the-countdown' ),
			'default' => false,
		),
		'onTick' => array(
			'section' => 'advanced',
			'label' => 'On Tick',
			'type' => 'text',
			'description' => __( 'A callback function name that is invoked each time the countdown updates itself. The array of current countdown periods (int[7] - based on the format setting) is passed as a parameter: [0] is years, [1] is months, [2] is weeks, [3] is days, [4] is hours, [5] is minutes, and [6] is seconds.', 'the-countdown' ),
			'default' => '',
		),
		'tickInterval' => array(
			'section' => 'advanced',
			'label' => 'Tick Interval',
			'type' => 'number',
			'description' => __( 'Interval (seconds) between onTick callbacks.', 'the-countdown' ),
			'default' => 1,
			'class' => 'smallfat',
		),
		
		/** Format **/ 
		'format' => array(
			'section' => 'format',
			'label' => 'Date Format',
			'type' => 'text',
			'description' => __( 'Format for display - uppercase for always, lower case only if non-zero, \'Y\' years, \'O\' months, \'W\' weeks, \'D\' days, \'H\' hours, \'M\' minutes, \'S\' seconds', 'the-countdown' ),
			'default' => 'dHMS',
			'placeholder' => 'dHMS',
		),
		'cLabels1' => array(
			'section' => 'format',
			'label' => 'Singular Timestap Label',
			'type' => 'text',
			'description' => __( 'The display text for singular timestamp, comma separated.', 'the-countdown' ),
			'default' => $ts_singular,
			'placeholder' => $ts_singular
		),
		'cLabels' => array(
			'section' => 'format',
			'label' => 'Plural Timestap Label',
			'type' => 'text',
			'description' => __( 'The display text for plural timestamp, comma separated.', 'the-countdown' ),
			'default' => $ts_plural,
			'placeholder' => $ts_singular,
		),
		'compactLabels' => array(
			'section' => 'format',
			'label' => 'Compact Labels',
			'type' => 'text',
			'description' => __( 'The compact texts for the year, month, week and date. Separate by comma.', 'the-countdown' ),
			'default' => 'y, m, w, d',
			'placeholder' => 'y, m, w, d',
		),	
		
		/** Templating **/
		'template' => array(
			'section' => 'template',
			'label' => 'Template',
			'type' => 'select',
			'description' => __( 'Select one template from the list below. <a href="http://goo.gl/jYQoM"><strong>Upgrade to Pro</strong></a> version for template customizations, font size and color, background color, and many more.', 'the-countdown' ),
			'options' => $templates,
			'default' => 'default',
			'onchange' => "wpWidgets.save(jQuery(this).closest(\"div.widget\"),0,1,0)",
			'spinner' => true
		),

		
		/** Customs **/
		'intro' => array(
			'section' => 'custom',
			'label' => 'Intro Text',
			'type' => 'textarea',
			'description' => __( 'This option will display addtional text before the widget content and supports HTML.', 'the-countdown' ),
			'rows' => 2,
			'default' => '',
		),	
		'outro' => array(
			'section' => 'custom',
			'label' => 'Outro Text',
			'type' => 'textarea',
			'description' => __( 'This option will display addtional text after widget and supports HTML.', 'the-countdown' ),
			'rows' => 2,
			'default' => '',
		),	
		'header' => array(
			'section' => 'custom',
			'label' => 'Frontpage Header',
			'type' => 'textarea',
			'description' => __( 'Print custom scripts or styles to the front page header.', 'the-countdown' ),
			'default' => '',
		),	
		'footer' => array(
			'section' => 'custom',
			'label' => 'Frontpage Footer',
			'type' => 'textarea',
			'description' => __( 'Print custom scripts or styles to the front page footer.', 'the-countdown' ),
			'default' => '',
		),
		'selector' => array(
			'section' => 'custom',
			'label' => 'Selector',
			'type' => 'readonly',
			'description' => __( 'Use this for JavaScript or CSS widget selector.', 'the-countdown' ),
			'default' => '#%_id%',
		),
		'shortcode' => array(
			'section' => 'custom',
			'label' => 'Shortcode',
			'type' => 'readonly',
			'description' => __( 'Shortcode for post or page content. Drag this widget to the "Inactive Widgets" at the bottom of this page if you want to use this as a shortcode to your content without using defined sidebars.', 'the-countdown' ),
			'default' => '[the-countdown-widget id="%_number%"]',
		),
		'function' => array(
			'section' => 'custom',
			'label' => 'PHP Function',
			'type' => 'readonly',
			'description' => __( 'This PHP function can be used in template files.', 'the-countdown' ),
			'default' => "&lt;?php do_shortcode( &apos;[the-countdown-widget id=&quot;%_number%&quot;]&apos; ); ?>",
		),
		
		/* Upgrade and support section */
		'upgrade' => array(
			'section' => 'upgrade',
			'label' => '',
			'type' => 'tcupgrade',
			'description' => '',
			'default' => '',
		),	
		'support' => array(
			'section' => 'support',
			'label' => '',
			'type' => 'tcsupport',
			'description' => '',
			'default' => '',
		),		

	);
	
	// Register on expiry child arguments, displayed based on selected action
	$selected_action = isset( $instance['onExpiry']['action'] ) ? 
		$instance['onExpiry']['action'] : $arguments['onExpiry']['children']['action']['default'];
	
	switch( $selected_action ) {				
		case 'message':
			$arguments['onExpiry']['children']['message'] = array(
				'label' => __( 'Text/HTML Content', 'the-countdown' ),
				'type' => 'textarea',
				'description' => __( 'The content displayed after expired.', 'the-countdown' )
			);
			break;
		case 'url':
			$arguments['onExpiry']['children']['url'] = array(
				'label' => __( 'Redirect URL', 'the-countdown' ),
				'type' => 'url',
				'description' => __( 'This will be the URL to visit after expired.', 'the-countdown' ),
			);		
			break;
		case 'function':
			$arguments['onExpiry']['children']['function'] = array(
				'label' => __( 'JavaScript Function Name', 'the-countdown' ),
				'type' => 'url',
				'description' => __( 'Operator <tt>this</tt> refers to the division that holds the widget.', 'the-countdown' ),
			);		
			break;
	}
	
	return apply_filters( 'the_countdown_arguments', $arguments, $instance );
}


/**
 * Get default arguments
 * 
 * @since 1.2.0
 */		
function the_countdown_default_args() {
	$args = array();

	foreach( the_countdown_arguments() as $k => $arg ) {
		if ( isset( $arg['children'] ) ) {
			foreach( $arg['children'] as $j => $child ) {
				$args[$k][$j] = isset( $child['default'] ) ? $child['default'] : '';
			}
		} else {		
			$args[$k] = isset( $arg['default'] ) ? $arg['default'] : '';		
		}
	}

	return $args;
}
