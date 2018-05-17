/**
 * @detail
 * Additional function to handle content
 * http://zourbuth.com/
 */

jQuery.fn.tcDateTime = function (options) {

	var defaults = {}, $;
	$ = jQuery;
	options  = $.extend(defaults, options);
	
	
	return this.each( function () {

		var stamp, selector, c ;
		stamp = $('.timestamp span', selector).html();
		selector = c = this;
		
		function updateText() {
			var attemptedDate, originalDate, currentDate, publishOn,
				aa = $('.aa', selector).val(),
				mm = $('.mm', selector).val(), 
				jj = $('.jj', selector).val(), 
				hh = $('.hh', selector).val(), 
				mn = $('.mn', selector).val();
			
			attemptedDate = new Date( aa, mm - 1, jj, hh, mn );
			originalDate = new Date( $('.hidden_aa', selector).val(), $('.hidden_mm', selector).val() -1, $('.hidden_jj', selector).val(), $('.hidden_hh', selector).val(), $('.hidden_mn', selector).val() );
			currentDate = new Date( $('.cur_aa', selector).val(), $('.cur_mm', selector).val() -1, $('.cur_jj', selector).val(), $('.cur_hh', selector).val(), $('.cur_mn', selector).val() );

			if ( attemptedDate.getFullYear() != aa || (1 + attemptedDate.getMonth()) != mm || attemptedDate.getDate() != jj || attemptedDate.getMinutes() != mn ) {
				$('.timestamp-wrap', selector).addClass('form-invalid');
				return false;
			} else {
				$('.timestamp-wrap', selector).removeClass('form-invalid');
			}

			if ( originalDate.toUTCString() == attemptedDate.toUTCString() ) { //hack
				$('.timestamp span', selector).html(stamp);
			} else {
				$('.timestamp span', selector).html(
					$('option[value="' + $( '.mm', selector ).val() + '"]', ( '.mm', selector ) ).text().substring(3) + ' ' +
					jj + ', ' +
					aa + ' @ ' +
					hh + ':' +
					mn
				);
			}

			return true;
		}

		//$('.timestampdiv', selector).siblings('a.edit-timestamp').click(function(){
		$(document).on("click", 'a.edit-timestamp', function() {
			c = $(this).parents(".tc-curtime");
			if ($('.timestampdiv', c).is(":hidden")) {
				$('.timestampdiv', c).slideDown('fast');
				$(this).hide();
			}
			return false;
		});

		//$('.cancel-timestamp', selector).click(function() {
		$(document).on("click", '.cancel-timestamp', function() {
			$('.timestampdiv', c).slideUp('fast');
			$('.mm', c).val($('.hidden_mm', c).val());
			$('.jj', c).val($('.hidden_jj', c).val());
			$('.aa', c).val($('.hidden_aa', c).val());
			$('.hh', c).val($('.hidden_hh', c).val());
			$('.mn', c).val($('.hidden_mn', c).val());
			$('.timestampdiv', c).siblings('a.edit-timestamp').show();
			updateText();
			return false;
		});

		//$('.save-timestamp', selector).click(function () { // crazyhorse - multiple ok cancels
		$(document).on("click", '.save-timestamp', function() {
			if ( updateText() ) {
				$('.timestampdiv', c).slideUp('fast');
				$('.timestampdiv', c).siblings('a.edit-timestamp').show();
			}
			
			return false;
		});
	});
};

jQuery(document).ready( function($){
	$('.tc-curtime').tcDateTime();
});

jQuery(document).on( 'widget-added widget-updated', function(e, widget){
	jQuery('.tc-curtime').tcDateTime();
});