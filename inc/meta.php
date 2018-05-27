<?php
/*
	The Countdown Meta
	@since 1.1.8
	
	Copyright 2018 zourbuth.com (email: zourbuth@gmail.com)

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

class The_Countdown_Meta {
	public $number = false;	
	public $meta_id = false;	
	public $id = false;	

	var $textdomain;
	var $slug;
	var $version;
	
	public function __construct() {
		$this->slug = THE_COUNTDOWN_SLUG;
		$this->textdomain = THE_COUNTDOWN_TEXTDOMAIN;
		$this->version = THE_COUNTDOWN_VERSION;
		
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'wp_ajax_the_countdown_shortcode_action', array( $this, 'ajax_action' ) );		
	}



	/**
	 * Creating the metabox
	 * Check if the current user can edit post or other post type
	 * Add the meta box if current custom post type is selected
	 * add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	 * @param $key, 'side', 'high' for custom position
	 * @since 1.1.8
	**/
	function add_meta_box() {
		if( ! current_user_can( 'edit_others_posts' ) )
			return;

		$options = get_option( 'the_countdown' );
		
		// add_meta_box( string $id, string $title, callable $callback, string|array|WP_Screen $screen = null, 
		//     string $context = 'advanced', string $priority = 'default', array $callback_args = null )
		$post_types = array( 'post', 'page' );
		add_meta_box( 'the-countdown', __( 'The Countdown Shortcodes', $this->textdomain ), array( $this, 'meta_box' ), 
			$post_types, 'side', 'high' );
	}

	/**
	 * Creating the metabox fields
	 * We don't find any match to use the fields as a global variable, manually but best at least for now
	 * Using the name field [] for array results
	 * @param string $post_id
	 * @since 1.1.8
	**/
	function meta_box() {
		global $post, $post_id;
		?>
		<div class="g-meta">
			<p>Copy the shortcode below and paste to the editor content.</p>
			<p><button class="button widget-control-add">Add Shortcode</button><span class="spinner"></span></p>

			<div class="g-shortcodes">

			</div>
		</div>
		<?php
	}
	
	/**
	 * Mimics WP_Widget public function to output shortcode id and class
	 * @since 1.1.8
	**/
	public function get_field_name($field_name) { return "the-countdown[{$this->meta_id}][$field_name]"; }
	public function get_field_id( $field_name ) { return "the-countdown-{$this->meta_id}-$field_name"; }	

	/**
	 * Saving metabox data on save action
	 * Checking the nonce, make sure the current post type have sidebar option enable
	 * Save the post metadata with update_post_meta for the current $post_id in array
	 * @param string $post_id
	 * @since 1.1.8
	**/
	function shortcode( $instance, $post_id, $meta_id ) {
		$this->meta_id = $meta_id;
		
		$this->number = "$post_id-$meta_id"; // for shortcode id and css selector
		$this->id = "the-countdown-$post_id-$meta_id"; 
		?>
		<div class="widget g-shortcode">		
			<div class="widget-top">
				<div class="widget-title-action">
					<button type="button" class="widget-action hide-if-no-js">
						<span class="screen-reader-text">Edit widget: The Countdown</span>
						<span class="toggle-indicator"></span>
					</button>
					<a class="widget-control-edit hide-if-js" href="#">
						<span class="edit"><?php _e( 'Edit', $this->textdomain ); ?></span>
						<span class="add"><?php _e( 'Add', $this->textdomain ); ?></span>
						<span class="screen-reader-text"><?php _e( 'The Countdown', $this->textdomain ); ?></span>
					</a>
				</div>
				<div class="widget-title ui-sortable-handle">
					<h3><?php echo $instance['title']; ?>&nbsp;
						<span class="in-widget-title"><?php echo $post_id;?>-<?php echo $meta_id; ?></span></h3>
				</div>
			</div>

			<div class="widget-inside total-widget" style="display: none;">
				<div class="widget-content">
					
					<?php the_countdown_dialog( $instance, $this ); ?>
	
					<div class="widget-control-actions">
						<div class="alignleft">
							<button type="button" class="button-link button-link-delete widget-control-remove">
								<?php _e( 'Delete', $this->textdomain ); ?></button>
							<span class="widget-control-close-wrapper">
								|
								<button type="button" class="button-link widget-control-close">
									<?php _e( 'Done', $this->textdomain ); ?></button>
							</span>
						</div>
						<div class="alignright">
							<input type="submit" name="savewidget" id="savewidget-<?php echo $meta_id; ?>" 
								class="button button-primary widget-control-save right" 
								data-meta-id="<?php echo $meta_id; ?>" 
								value="<?php _e( 'Save', $this->textdomain ); ?>" />
							<span class="spinner"></span>
						</div>
						<br class="clear" />
					</div>
				</div>
			</div>			
				
		</div>
		<?php
	}
	
	/**
	 * Saving metabox data on save action
	 * Checking the nonce, make sure the current post type have sidebar option enable
	 * Save the post metadata with update_post_meta for the current $post_id in array
	 * @param string $post_id
	 * @since 1.1.8
	**/
	function ajax_action() {
		if( ! wp_verify_nonce( $_POST['nonce'], 'the-countdown' ) )
			die();

		//$meta_votes = get_post_meta( $post_id, 'super_post_votes', true );
		//update_post_meta( $post_id, 'super_post_votes', $votes );
		$post_id = (int) $_POST['post_id'];
		
		
		switch( $_POST['method'] ) {
			case 'add':
				// Modify default widget arguments to support shortcode
				add_filter( 'the_countdown_arguments', function ( $arguments, $instance  ) {
					$arguments['shortcode']['description'] = __( 'Shortcode for post or page content.', 'the-countdown' );
					$arguments['shortcode']['default'] = '[the-countdown id="%_number%"]';
					$arguments['function']['default'] = "&lt;?php do_shortcode( &apos;[the-countdown id=&quot;%_number%&quot;]&apos; ); ?>";	
					return $arguments;
				}, 1, 2 );
				
				$args = the_countdown_default_args();				
				$meta_id = add_post_meta( $post_id, '_the_countdown', $args );
				$this->shortcode( $args, $post_id, $meta_id );
			break;
			
			case 'save':				
				parse_str( $_POST['serialize'], $array );
				print_r( $array );
			break;
			
			case 'delete':
				
			break;
		}

		exit;
	}
	
	/**
	 * Saving metabox data on save action
	 * Checking the nonce, make sure the current post type have sidebar option enable
	 * Save the post metadata with update_post_meta for the current $post_id in array
	 * @param string $post_id
	 * @since 1.1.8
	**/
	function save_metabox( $post_id ) {

		// Verify this came from the our screen with proper authorization,
		// because save_post can be triggered at other times
		if ( isset($_POST['tcp_nonce']) && !wp_verify_nonce( $_POST['tcp_nonce'], plugin_basename(__FILE__) ))
			return $post_id;

		// Verify if this is an auto save routine. If our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
			return $post_id;

		$options = get_option( 'the_countdown' );

		// Check permissions if this post type is use the sidebar meta option
		// Array value [cpt] => Array ( [testimonial] => 1 [statement] => 1 )
		if ( isset($_POST['post_type']) && isset($options['cpt']) && array_key_exists($_POST['post_type'], $options['cpt']) )  {
			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		// Alright folks, we're authenticated, let process
		if ( $parent_id = wp_is_post_revision($post_id) )
			$post_id = $parent_id;

		// Save the post meta data
		if ( isset( $_POST['tcp'] ) ) {
			$settings = array();
			foreach ( $_POST['tcp'] as $key => $data ) {
				$settings[$key] = $data;
			}
			
			update_post_meta( $post_id, 'the_countdown', $settings );
		}
		
		if( isset( $_POST['tcp'] ) )
			do_action( 'tcp_addon_save_meta', $post_id, $_POST['tcp'] );	// save meta action for addons
	}


	/**
	 * Load custom style or script to the current page admin
	 * Enqueue the jQuery library including UI, colorpicker, 
	 * the popup window and some custom styles/scripts
	 * @param string $hook.
	 * @since 1.1.8
	**/
	function admin_enqueue($hook) {
		if( 'post.php' != $hook && 'post-new.php' != $hook )
			return;

		wp_enqueue_style( 'total-dialog', THE_COUNTDOWN_URL . 'css/dialog.css', array(), THE_COUNTDOWN_VERSION );
		wp_register_script( 'total-dialog', THE_COUNTDOWN_URL . 'js/jquery.dialog.js', array( 'jquery' ), THE_COUNTDOWN_VERSION );
		wp_enqueue_script( 'countdown-meta', THE_COUNTDOWN_URL . 'js/meta.js', array( 'jquery' ), THE_COUNTDOWN_VERSION );	
		wp_localize_script( 'countdown-meta', 'theCountdown', apply_filters( 'the_countdown_localize', array(
			'post_id'	=> (int) get_the_ID(),
			'nonce'		=> wp_create_nonce( 'the-countdown' ),
		)));	
	}
} new The_Countdown_Meta();
?>