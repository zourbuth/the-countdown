/**
 * @detail
 * Additional function to handle content
 * http://zourbuth.com/
 */

( function($) { TCMeta = {
	init : function(){
		var __this = this;
		
		$( document ).on( 'click', '.g-meta .widget-control-add', function( e ) {
			__this.doRequest( e, 'add' );
		});
		$( document ).on( 'click', '.g-meta .widget-control-save', function( e ) {
			__this.doRequest( e, 'save' );
		});		
		$( document ).on( 'click', '.g-meta .widget-control-remove', function( e ) {
			__this.doRequest( e, 'remove' );
		});
		$( document ).on( 'click', '.g-meta .widget-control-close', function( e ) {
			__this.shortcodeToggle( e );
		});
		$( document ).on( 'click', '.widget-action', function( e ) {
			__this.shortcodeToggle( e );
		});
	},

	doRequest: function( e, method = 'add' ) {
		e.preventDefault();
		
		var widget = $(e.target).closest('.widget'),
		params = {};
		params.action = 'the_countdown_shortcode_action';
		params.method = method;
		params.nonce = window.theCountdown.nonce;
		params.post_id = window.theCountdown.post_id;
		params.meta_id = widget.find('.widget-control-save').attr('meta_id');
		params.serialize = widget.find('input, select, textarea').serialize();;
		console.log( params.serialize );
		if ( 'save' === method || 'remove' === method )
			$( '.spinner', widget ).addClass( 'is-active' );
		else // add shortcode
			$(e.target).next().addClass( 'is-active' );
		
		$.post( ajaxurl, params, function( result ){
			if ( 'add' === method ) {
				$('.g-shortcodes').append( result );
			} else if ( 'remove' === method  ) {				
				widget.remove();
			} else if ( 'save' === method  ) {
				console.log( result );
				console.log('save');
			}
			
			$( '.g-meta .spinner' ).removeClass( 'is-active' );	
		} );
	},	
	
	/**
	 * Open shortcode dialog
	 * @link wp-admin/js/widgets.js:124
	 */
	shortcodeToggle: function( e ) {		
		var target, widget,widget, toggleBtn,
		target = $(e.target);
		widget = target.closest('div.widget');
		inside = widget.children('.widget-inside');
		toggleBtn = target.closest( '.widget' ).find( '.widget-top button.widget-action' );
		postbox = target.closest( '.postbox-container' ).attr('id');
		
		if ( inside.is(':hidden') ) {
			if ( 'postbox-container-1' === postbox ) { // only in side context
				widget.css({
					'z-index': '100',
					'margin-left': '-215px',
				})
			}
			toggleBtn.attr( 'aria-expanded', 'true' );
			inside.slideDown( 'fast', function() {
				widget.addClass( 'open' );
			});
		} else {
			toggleBtn.attr( 'aria-expanded', 'false' );
			inside.slideUp( 'fast', function() {
				widget.attr( 'style', '' );
				widget.removeClass( 'open' );
			});
		}		
	},
};

$(document).ready(function(){TCMeta.init();});
})(jQuery);