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
 * The Countdown Utilities Class
 * 
 * @since 1.2.0
 */	
if ( ! class_exists( 'The_Countdown_Utilities' )) { 
class The_Countdown_Utilities {
	 
	/**
	 * Class constructor
	 * 
	 * @since 1.2.0
	 */		
	function __construct() {		
		add_filter( 'gumaraphous_create_field', array( &$this, 'coutdown_dialog' ), 1, 2 );		
	} 
	
	
	/**
	 * Create element
	 * 
	 * @param $html (HTML) returned output
	 * @param $args (array) instance value
	 * @since 1.2.0
	 */
	function coutdown_dialog( $html, $args ) {		
		$el = array();
		extract( $args );
		//_tc_debugr( $args);
		
		switch( $type ) {			
			case 'timestamp':
				global $wp_locale;
				$time_adj = current_time( 'timestamp' );			
				$counters = array( 'until' => __( 'Until', 'the-countdown') , 'since' => __( 'Since', 'the-countdown'  ));
				$gettimestamp = $wp_locale->get_month_abbrev( $wp_locale->get_month( $value[1] ) ) . ' ' . $value[2] . ', ' . $value[3] . ' @ ' . $value[4] . ':' . $value[5];
				
				$html = "
				<div class='curtime tc-curtime'>
					<select class='smallfat' id='$id' name='{$name}[]'>";

						foreach ( $counters as $k => $v )
							$html .= "<option value='$k' ". selected( $value[0], $k, false ) .">$v</option>";		
				
				$html .= "
					</select>
					<span class='timestamp'><span>$gettimestamp</span></span>
					<a tabindex='4' class='edit-timestamp hide-if-no-js' href='#'>". __( 'Edit', 'the-countdown' ) ."</a>
					<div class='hide-if-js timestampdiv'>
						<div class='timestamp-wrap'>";
							
							$month = "<select class='mm' name='{$name}[]'>";
							for ( $i = 1; $i < 13; $i = $i +1 ) {
								$monthnum = zeroise($i, 2);
								$month .= "\t\t\t" . '<option value="' . $monthnum . '"';
								if ( $i == $value[1] )
									$month .= ' selected="selected"';
								/* translators: 1: month number (01, 02, etc.), 2: month abbreviation */
								$month .= '>' . sprintf( __( '%1$s-%2$s' ), $monthnum, $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) ) . "</option>\n";
							}
							$month .= '</select>';
							$html .= $month;

							$html .= "
							<input type='text' autocomplete='off' tabindex='4' maxlength='2' size='2' value='{$value[2]}' name='{$name}[]' class='jj' />, 
							<input type='text' autocomplete='off' tabindex='4' maxlength='4' size='4' value='{$value[3]}' name='{$name}[]' class='aa' /> @ 
							<input type='text' autocomplete='off' tabindex='4' maxlength='2' size='2' value='{$value[4]}' name='{$name}[]' class='hh' /> : 
							<input type='text' autocomplete='off' tabindex='4' maxlength='2' size='2' value='{$value[5]}' name='{$name}[]' class='mn' />

							<a class='save-timestamp hide-if-no-js button' href='#'>". __( 'OK', 'the-countdown' ) ."</a>
							<a class='cancel-timestamp hide-if-no-js' href='#'>". __( 'Cancel', 'the-countdown' ) ."</a>
						</div>
						
						<input type='hidden' value='11' name='ss' class='ss' />
						<input type='hidden' value='". esc_attr( $value[1] ) ."' name='hidden_mm' class='hidden_mm'>
						<input type='hidden' value='". gmdate( 'd', $time_adj ) ."' name='cur_mm' class='cur_mm'>
						<input type='hidden' value='". esc_attr( $value[2] ) ."' name='hidden_jj' class='hidden_jj'>
						<input type='hidden' value='". gmdate( 'm', $time_adj ) ."' name='cur_jj' class='cur_jj'>
						<input type='hidden' value='". esc_attr( $value[3] ) ."' name='hidden_aa' class='hidden_aa'>
						<input type='hidden' value='". gmdate( 'Y', $time_adj ) ."' name='cur_aa' class='cur_aa'>
						<input type='hidden' value='". esc_attr( $value[4] ) ."' name='hidden_hh' class='hidden_hh'>
						<input type='hidden' value='". gmdate( 'h', $time_adj ) ."' name='cur_hh' class='cur_hh'>
						<input type='hidden' value='". esc_attr( $value[5] ) ."' name='hidden_mn' class='hidden_mn'>
						<input type='hidden' value='". gmdate( 'i', $time_adj ) ."' name='cur_mn' class='cur_mn'>
					</div>
				</div>";					
				break;
			
			case 'tcupgrade':
				$html = wp_remote_fopen( require( THE_COUNTDOWN_PATH . 'inc/upgrade.php' ) );
				break;
			
			case 'tcsupport':
				$html = wp_remote_fopen( require( THE_COUNTDOWN_PATH . 'inc/support.php' ) );		
				break;
			
			case 'tctemplate':
				$html = "<select id='$id' name='{$name}[name]' onchange='wpWidgets.save(jQuery(this).closest(\"div.widget\"),0,1,0);'>";
					foreach ( $options as $k => $option ) {
						$selected = selected( $value['name'], $k, false );
						$html .= "<option value='$k' $selected>". esc_html( $option ) ."</option>";
					}
				$html .= "</select><span class='spinner'></span>";					
				break;
		}
			
		echo $html;
	}
	
} 
new The_Countdown_Utilities(); };


/**
 * Data sanitazion before saving to database
 * 
 * @since 1.2.0
 */		
function the_countdown_data_sanitize( $instance ) {
	$args = the_countdown_arguments();
	
	foreach( $instance as $k => $val ) {
		switch( $type ) {
			case 'text':
			case 'number':
			case 'select':
				$instance[k] = sanitize_text_field( $val );
				break;
			case 'url':
				$instance[k] = esc_url( $val );
				break;
			case 'textarea':
				$instance[k] = esc_textarea( $val );
				break;
			case 'checkbox':
				$instance[k] = (bool) $val;
				break;		
			default:
				$instance[k] = sanitize_text_field( $val );
				break;
		}
	}
}


/**
 * Update instance to the latest version
 * 
 * @since 1.2.0
 */	
function the_countdown_update_instance( $instance = array() ) {
	//_tc_debugr( $instance );	
	if ( ! isset( $instance['timestamp'] ) ) { // this is the sign of old version
		
		// Update timestamp. Old: (string) 'counter' and (array) 'until' [ year, month, date, hour, minute ]
		// New: (array) [ counter, year, month, date, hour, minute ]
		$instance['timestamp'] = array();
		$instance['timestamp'][] = $instance['counter'];

		foreach( $instance['until'] as $until )
			$instance['timestamp'][] = $until;

		// Update labels
		$instance['cLabels'] = implode( ', ', $instance['cLabels'] );
		$instance['cLabels1'] = implode( ', ', $instance['cLabels1'] );
		$instance['compactLabels'] = implode( ', ', $instance['compactLabels'] );

		// Update expiry
		$expiry = array();
		if ( $instance['expiryUrl'] ) {
			$expiry['action'] = 'url';
			$expiry['url'] = $instance['expiryUrl'];
		} elseif ( $instance['expiryText'] ) {
			$expiry['action'] = 'message';
			$expiry['message'] = $instance['expiryText'];
		} elseif ( $instance['onExpiry'] ) {
			$expiry['action'] = 'function';
			$expiry['function'] = $instance['onExpiry'];
		} else {
			$expiry['action'] = 'message';
			$expiry['message'] = __( 'This is the expiry message to show.', 'the-countdown' );
		}
		$instance['onExpiry'] = $expiry; // overwrite

		//Update dialog tab
		$instance['tab'] = $instance['toggle_active']; // overwrite

		// Unset old instance
		unset( $instance['counter'] );
		unset( $instance['until'] );
		unset( $instance['expiryUrl'] );
		unset( $instance['expiryText'] );
		unset( $instance['toggle_active'] );		
	}
	
	return $instance;
}


/**
 * Debugging purpose
 * 
 * @params $arr array()
 * @since 1.2.0
 */	
function _tc_debugr( $arr ) {
	echo '<pre style="font-size:10px;line-height:10px;overflow-y:hidden;">'. print_r( $arr, true ) . '</pre>'; 
}
?>