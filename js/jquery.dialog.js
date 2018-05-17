/**
 * @detail
 * Additional function to handle content
 * http://zourbuth.com/
 */

(function($) { tcDialog = {
	init : function(){
		$('.total-options').closest(".widget-inside").addClass("total-widget");
		$('.total-options').closest(".inside").addClass("total-meta");
		
		$('ul.nav-tabs li').live("click", function(){
			tcDialog.tabs(this)
		});
		
		$("a.addThumbnail").live("click", function(){
			tcDialog.addThumbnail(this); return false;
		});
		
		$("a.add-image").live("click", function(){
			tcDialog.addImage(this); return false;
		});
		
		$("a.remove-image").live("click", function(){
			tcDialog.removeImage(this); return false;
		});

		//$(this).ajaxComplete(function() {
		//	$('.total-options .color-picker').wpColorPicker();  
		//});
	},
	
	tabs : function(tab){
		var t, i, c, tabname;
		
		t = $(tab);
		i = t.index();
		tabname = t.text().toLowerCase();	

		c = t.parent("ul").next().children("li").eq(i);
		t.addClass('active').siblings("li").removeClass('active');
		$(c).show().addClass('active').siblings().hide().removeClass('active');
		$(t).closest(".total-options").find(".tab").val( tabname ); // update tab value 
	},
	
	addImage : function(el){
		var $el = $(el), frame, attachment, img, input, removebtn;		
	
		img = $el.siblings('img');
		input = $el.siblings('input');
		removebtn = $el.siblings('a');
	
		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create the media frame.
		frame = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),

			// Tell the modal to show only images.
			library: {
				type: 'image'
			},

			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: $el.data('update'),
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: false
			}
		});

		// When an image is selected, run a callback.
		frame.on( 'select', function() {
			// Grab the selected attachment.
			attachment = frame.state().get('selection').first();		
			input.val(attachment.attributes.url);
			img.attr('src', attachment.attributes.url).slideDown();
			removebtn.removeClass("hidden");
			frame.close();			
		});

		// Finally, open the modal.
		frame.open();
		return false;
	},
	
	addThumbnail : function(el){
		var $el = $(el), frame, attachment, img, input, removebtn;		
	
		img = $el.siblings('img');
		input = $el.siblings('input');
		removebtn = $el.siblings('a');
	
		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create the media frame.
		frame = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),

			// Tell the modal to show only images.
			library: {
				type: 'image'
			},

			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: $el.data('update'),
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: false
			}
		});

		// When an image is selected, run a callback.
		frame.on( 'select', function() {
			// Grab the selected attachment.
			attachment = frame.state().get('selection').first();			
			input.val(attachment.id);
			img.attr('src', attachment.attributes.url).slideDown();
			removebtn.removeClass("hidden");
			frame.close();
		});

		// Finally, open the modal.
		frame.open();
		return false;
	},
	
	removeImage : function(el){
		var t = $(el);
		
		t.next().val('');
		t.siblings('img').slideUp();
		t.siblings('a.filelink').attr("href", "").text("");
		t.addClass('hidden');
		t.fadeOut();
		return false;
	}	
};

$(document).ready(function(){tcDialog.init();});
})(jQuery);