<?php
/*
    Gumaraphous Dialog Class 1.0.2
    
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
	
	Change Logs:
	1.0.0 	- Initial release
	1.0.1 	- Replaced description class from 'controlDesc' to 'description'
			- Added url text type
			- Added placeholder
			- Added shorcode/function
			- Update multi dialog
	1.0.2 	- Use echo to replace return
			- Replace create_element with create_field
*/


if ( ! defined( 'ABSPATH' ) ) // exit if is accessed directly
	exit;


/**
 * Gumaraphous Dialog Class
 * 
 * @since 0.0.1
 */	
if ( ! class_exists( 'Gumaraphous_Dialog' )) { class Gumaraphous_Dialog {
	
	private $default;
	
	/**
	 * Enqueu scripts and styles
	 * 
	 * @since 0.0.1
	 */		
	function __construct() {
		add_action( 'admin_print_scripts-widgets.php', array( &$this, 'enqueue_scripts' ), 1 );
		add_action( 'admin_print_footer_scripts-widgets.php', array( &$this, 'custom_scripts' ), 99 );
		add_action( 'customize_controls_print_footer_scripts', array( &$this, 'custom_scripts' ) );
	}


	/**
	 * Enqueue script to admin section
	 * @param $hook_suffix
	 * @since 0.0.1
	 */	
	function enqueue_scripts( $hook_suffix ) {	 
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( array( 'jquery', 'wp-color-picker' ) );
	}
	
	
	/**
	 * Color picker custom script
	 * @param $hook_suffix
	 * @since 0.0.1
	 */		
	function custom_scripts( $hook_suffix ) { 
		?><script type="text/javascript">
		( function( $ ) {
			var params = { 
				change: function(e, ui) { // enable widget "Save" button
					$( e.target ).val( ui.color.toString() );
					$( e.target ).trigger('change');
				},
			}
			
			// Note: e and widget is undefined in ready
			$(document).on( 'ready widget-added widget-updated', function(e, widget){			
				$('.gcolor-picker').not('[id*="__i__"]').wpColorPicker( params );
			});			
		})( jQuery );
		</script><?php
		echo "\n";
	}		
		
	
	/**
	 * Default parameters
	 * 
	 * @since 1.0.1
	 */
	public static function default_args() {
		return array(
			'id' => '',
			'name' => '',
			'label' => '',
			'type' => '',
			'description' => '',
			'rows' => 4,
			'size' => 3,
			'options' => '',
			'class' => '',
			'placeholder' => '',
			'value' => '',
			'onchange' => '',
			'spinner' => false,
			'default' => '',
		);
	}
	
	
	/**
	 * Create dialog
	 * 
	 * @param $args (array)
	 * @since 0.0.1
	 */	
	public static function create_dialog( $args ) {	
	
		$array = array();
		$args = wp_parse_args( (array) $args, self::default_args() );
		extract( $args );
		
		$description = $description ? "<span class='description'>$description</span>" : '';
		$id = is_array( $id ) ? $id[0] : $id;
		
		echo "<li>";
		
		// Put label and its description, not for checkboxes
		if( 'checkbox' != $type && $label )
			echo "<label for='$id'>$label</label>$description";	

		// Check if dialog contain more than one fields and placed in inline
		// For example: color and background color
		if ( is_array( $type ) ) {
			$new = (array) $args;
			foreach ( $type as $k => $n ) { 	
				foreach ( $args as $i => $arg ) {
					if ( is_array( $arg ) && isset( $arg[$k] ) )
						$new[$i] = $arg[$k];										
				}
			
				$new['value'] = isset( $args['value'][$k] ) ? $args['value'][$k] : $args['default'][$k]; // special case for default value
				
				$new['id'] = ( $k > 0 ) ? $args['id']. "-$k" : $args['id']; // keep the first array as htmlFor for label
				$new['name'] = $args['name']. "[$k]";	
	
				echo self::create_field( $new );
			}
		} else {
			echo self::create_field( $args );
		}		
		
		echo "</li>";
	}

	
	/**
	 * Create element
	 * 
	 * @param $args (array)
	 * @since 0.0.1
	 */	
	public static function create_field( $args ) {		
		$html = '';
		
		$args = wp_parse_args( (array) $args, self::default_args() );
		
		extract( $args );
		
		$_onchange 	= $onchange ? "onchange='$onchange'" : '';
		$_spinner 	= $spinner ? "<span class='spinner g-spinner'></span>" : '';
		
		switch( $type ) {
			case 'text':
			case 'number':
			case 'url':
				$class = $class ? $class : ( 'number' == $type ? 'column-last' : 'widefat' );
				$html .= "<input class='$class' id='$id' name='$name' type='$type' value='$value' placeholder='$default' size='$size' />";																	
			break;
			
			case 'checkbox':
				$checked = checked( $value, true, false );
				$html .= "<label for='$id'>
					<input $checked class='checkbox $class' id='$id' name='$name' type='checkbox' />$label</label>";
				
				$html .= $description ? "<span class='description'>$description</span>" : '';
			break;
			
			case 'color':
				$html .= "<input class='gcolor-picker $class' type='text' id='$id' name='$name' value='$value' />";
			break;
			
			case 'select':				
				$html .= "<select id='$id' name='$name' class='$class' $_onchange>";
					foreach ( $options as $k => $option ) {
						$selected = selected( $value, $k, false );
						$option = esc_html( $option );
						$html .= "<option value='$k' $selected>$option</option>";
					}
				$html .= "</select>$_spinner";				
				break;
			
			case 'textarea':
				$class = $class ? $class : 'widefat';
				$html .= "<textarea class='$class' id='$id' rows='$rows' name='$name'>$value</textarea>";
				break;
				
			case 'description':
				$html .= "";
				break;
				
			case 'readonly':
				$value = str_replace( array('%_number%', '%_id%'), array($_number, $_id), $default );
				$html .= "<input class='widefat shortcode $class' type='text' value='$value' onClick='this.select();' readonly />";
				break;
		}
		
		echo apply_filters( 'gumaraphous_create_field', $html, $args );
	}

} new Gumaraphous_Dialog(); };