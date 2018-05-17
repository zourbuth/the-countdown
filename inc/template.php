<?php

if ( ! class_exists( 'The_Countdown_Template' )) { 
class The_Countdown_Template {
	 
	/**
	 * Class constructor
	 * 
	 * @since 1.2.0
	 */		
	function __construct() {
		if ( is_admin() ) { 
			add_filter( 'the_countdown_register_template', array( &$this, 'register_template' ), 1, 1 );		
			add_filter( 'the_countdown_arguments', array( &$this, 'template_options' ), 1, 2 );			
		
		} else { // template is generated in front-end only	
			add_filter( 'the_countdown_template', array( &$this, 'countdown_template' ), 1, 1 );		
		}
	} 
	
	
	/**
	 * Create element
	 * 
	 * @param $html (HTML) returned output
	 * @param $args (array) instance value
	 * @since 1.2.0
	 */
	function register_template( $templates ) {
		$templates['other'] = __( 'Others', 'the-countdown' );
		return $templates;
	}
	
	/**
	 * Create element
	 * 
	 * @param $html (HTML) returned output
	 * @param $args (array) instance value
	 * @since 1.2.0
	 */
	function template_options( $args, $instance ) {
		
		$templates = array();
		
		$font_sizes = array(
			'section' => 'template',
			'label' => 'Font Size',
			'type' => array( 'text', 'text', 'select' ),
			'options' => array( '', '', array('px'=>'px', 'em'=>'em', 'pt'=>'pt') ),
			'description' => __( 'Counter and period font size with unit respectively.', 'the-countdown' ),			
			'default' => array( '2.5', '1', 'em' ),
			'class' => 'smallfat',		
		);		
		$font_colors = array(
			'section' => 'template',
			'label' => 'Font Color',
			'type' => array( 'color', 'color' ),
			'class' => 'smallfat',
			'description' => __( 'Counter and period font color respectively.', 'the-countdown' ),
			'default' => array( 'inherit', '' )
		);
		$background_colors = array(
			'section' => 'template',
			'label' => 'Background Color',
			'type' => array( 'color', 'color' ),
			'class' => 'smallfat',
			'description' => __( 'Counter and period background color respectively.', 'the-countdown' ),
			'default' => array( '#eeeeee', '' )
		);
		
		switch( $instance['template'] ) {				
			case 'minimal':
				// Modify as needed
				$font_sizes['type'] = array( 'text', 'select' );
				$font_sizes['options'] = array( '', array('px'=>'px', 'em'=>'em', 'pt'=>'pt') );
				$font_sizes['default'] = array( '1.5', 'em' );
				$font_sizes['description'] = __( 'Counter font size.', 'the-countdown' );
				
				$font_colors['type'] = 'color';
				$font_colors['default'] = 'inherit';				
				$font_colors['description'] = __( 'Counter font color.', 'the-countdown' );		

				$templates = array( 
					'font_sizes' => $font_sizes, 
					'font_colors' => $font_colors, 
				);
				break;

			case 'default':
			default:
				$templates = array( 
					'font_sizes' => $font_sizes, 
					'font_colors' => $font_colors, 
					'background_color' => $background_colors
				);			
				break;
		}
		//_tc_debugr( array_merge( $args, $templates ) );
		return array_merge( $args, $templates );
	}
	
	
	/**
	 * Create element
	 * 
	 * @param $html (HTML) returned output
	 * @param $args (array) instance value
	 * @since 1.2.0
	 */
	function countdown_template( $args ) {		
		_tc_debugr( $args );
		$cols = strlen( $args['format'] );

		switch( $args['template'] ) {				
			case 'minimal':
				$style = " style='font-size:{$args['font_sizes'][0]}{$args['font_sizes'][1]};
								color:{$args['font_colors'][0]};'";			
				return "<span class='tc-minimal' $style>
						{y<}{yn} {yl}{y>} {d<}{dn} {dl}{d>} {hnn}{sep}{mnn}{sep}{snn}
					</span>";
				break;

			default:
				$style0 = " style='font-size:{$args['font_sizes'][0]}{$args['font_sizes'][2]};
								color:{$args['font_colors'][0]}; background-color:{$args['background_color'][0]};'";
				$style1 = " style='font-size:{$args['font_sizes'][1]}{$args['font_sizes'][2]};
								color:{$args['font_colors'][1]}; background-color:{$args['background_color'][1]};'";
				return "
				<div class='tc-default tc-clearfix tc-col-$cols'>
					{y<}<span class='tc-year'>		<span$style0>{yn}</span>	<span$style1>{yl}</span>	</span>{y>}
					{o<}<span class='tc-month'>		<span$style0>{on}</span>	<span$style1>{ol}</span>	</span>{o>}
					{w<}<span class='tc-week'>		<span$style0>{wn}</span>	<span$style1>{wl}</span>	</span>{w>}
					{d<}<span class='tc-day'>		<span$style0>{dn}</span>	<span$style1>{dl}</span>	</span>{d>}
					{h<}<span class='tc-hour'>		<span$style0>{hn}</span>	<span$style1>{hl}</span>	</span>{h>}
					{m<}<span class='tc-minute'>	<span$style0>{mn}</span>	<span$style1>{ml}</span>	</span>{m>}
					{s<}<span class='tc-second'>	<span$style0>{sn}</span>	<span$style1>{sl}</span>	</span>{s>}
				</div>";
				break;
		}
	}	
} };

new The_Countdown_Template();