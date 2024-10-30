(function($) {
    'use strict';

	$(document).ready(function () {

		$('[data-toggle="popover"]').popover();
    	$('[data-toggle="tooltip"]').tooltip();
		
		
    	$(document).find('.form-check-input.select-all').on('click', function() {
			if ($(this).prop('checked') == true) {
				$(document).find('.check-current-row').prop('checked',true)
			} else {
				$(document).find('.check-current-row').prop('checked',false)
			}
		});

		$(document).find('.check-current-row').on('click', function() {
			var checkboxesArr = $(document).find('.check-current-row');
			var selectAllCheckBox = $(document).find('.form-check-input.select-all'); 
			var count = 0;

			for (var i = 0; i < checkboxesArr.length; i++) {
				if (checkboxesArr.eq(i).prop('checked') == true) {
					count++;
				}
			}	

			if (count == 0 && selectAllCheckBox.prop('checked') == true) {
				selectAllCheckBox.prop('checked', false);	
			} else if (count == checkboxesArr.length && selectAllCheckBox.prop('checked') == false) {
				selectAllCheckBox.prop('checked', true);
			}
		});

		$(document).on('mouseenter', '.column-title', function() {
			$(this).find('.chart-list-table-actions-row').css('display', 'flex');
		});
		$(document).on('mouseleave', '.column-title', function() {
			$(this).find('.chart-list-table-actions-row').css('display', 'none');
		});

		$(document).find('.ays-chart-copy-image').on('click', function(){
			var _this = this;
			var input = $(_this).parent().find('input.ays-chart-shortcode-input');
			var length = input.val().length;

			input[0].focus();
			input[0].setSelectionRange(0, length);
			document.execCommand('copy');
			document.getSelection().removeAllRanges();

			$(_this).attr('data-original-title', aysChartBuilderAdmin.copied);
			$(_this).attr("title", aysChartBuilderAdmin.copied);
			$(_this).tooltip('show');
		});

		$(document).find('.ays-chart-copy-image').on('mouseleave', function(){
			var _this = this;

			$(_this).attr('data-original-title', aysChartBuilderAdmin.clickForCopy);
			$(_this).attr('title', aysChartBuilderAdmin.clickForCopy);
			$(_this).tooltip('show');
		});

		$(document).on('click', '.notice-dismiss', function (e) {
			changeCurrentUrl('status');
		});

		var toggle_ddmenu = $(document).find('.toggle_ddmenu');
    	toggle_ddmenu.on('click', function () {
    	    var ddmenu = $(this).next();
    	    var state = ddmenu.attr('data-expanded');
    	    switch (state) {
    	        case 'true':
    	            $(this).find('.ays_fa').css({
    	                transform: 'rotate(0deg)'
    	            });
    	            ddmenu.attr('data-expanded', 'false');
    	            break;
    	        case 'false':
    	            $(this).find('.ays_fa').css({
    	                transform: 'rotate(90deg)'
    	            });
    	            ddmenu.attr('data-expanded', 'true');
    	            break;
    	    }
    	});

		function changeCurrentUrl(key){
			var linkModified = location.href.split('?')[1].split('&');
			for(var i = 0; i < linkModified.length; i++){
				if(linkModified[i].split("=")[0] == key){
					linkModified.splice(i, 1);
				}
			}
			linkModified = linkModified.join('&');
			window.history.replaceState({}, document.title, '?'+linkModified);
		}

	});

})(jQuery);