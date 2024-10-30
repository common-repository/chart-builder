(function($) {
    'use strict';
    function AysChartBuilder(element, options){
        this.el = element;
        this.$el = $(element);
        this.ajaxAction = 'ays_chart_admin_ajax';
        this.htmlClassPrefix = 'ays-chart-';
        this.htmlNamePrefix = 'ays_';
        this.dbOptions = undefined;
        this.chartSourceData = undefined;
		this.chartObj = undefined;
		this.chartOptions = null;
		this.chartData = null;
		this.chartTempData = null;

		this.chartSources = {
			'bar_chart'    : 'Bar Chart',
			'pie_chart'    : 'Pie Chart',
			'column_chart' : 'Column Chart'
		}

        this.init();

        return this;
    }

    AysChartBuilder.prototype.init = function() {
        var _this = this;
		_this.chartSourceData = window.ChartBuilderSourceData;
        _this.setEvents();
        _this.initLibraries();
        _this.setAccordionEvents();

    };

	// Set events
    AysChartBuilder.prototype.setEvents = function(e){
        var _this = this;
		/* == Choose poll type from modal == */
			_this.$el.on('dblclick'  , '.'+_this.htmlClassPrefix+'layer_box_blocks label.'+_this.htmlClassPrefix+'dblclick-layer:not(.'+_this.htmlClassPrefix+'type-pro-feature)', function(){
				_this.$el.find('.'+_this.htmlClassPrefix+'select_button_layer input.'+_this.htmlClassPrefix+'layer_button').trigger('click');
			}); 

			_this.$el.on('change'  , '.'+_this.htmlClassPrefix+'choose-source', function(){
				_this.$el.find('.'+_this.htmlClassPrefix+'select_button_layer input.'+_this.htmlClassPrefix+'layer_button').prop('disabled',false).attr("data-type" , $(this).val());
				// _this.$el.find('#poll_choose_type').val($(this).val());
			}); 
			
			_this.$el.on('click', '.'+_this.htmlClassPrefix+'layer_button' ,function(){
				var getCheckedChartTpye = _this.$el.find('.'+_this.htmlClassPrefix+'choose-source:checked').val();
				_this.$el.find('.'+_this.htmlClassPrefix+'layer_container').css({'position':'unset' , 'display':'none'});
				_this.$el.find('.'+_this.htmlClassPrefix+'type-info-box-text-changeable').text(_this.chartSources[getCheckedChartTpye]);
				_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-type').val(getCheckedChartTpye);
				_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').attr('id' , _this.htmlClassPrefix+getCheckedChartTpye);
				_this.loadChartBySource( getCheckedChartTpye );
			});
		/* */

		/* == Tabulation == */
			_this.$el.on('click', '.nav-tab-wrapper a.nav-tab' , _this.changeTabs);
		/* */

		// /* == Range slider functions == */
		// _this.$el.on('input', '#ays-chart-option-font-size', _this.rangeSliderChange);
		// _this.$el.on('mouseenter', '#ays-chart-option-font-size', function(e) {
		// 	$('#ays-chart-range-value').show();
		// });
		// _this.$el.on('mouseleave', '#ays-chart-option-font-size', function(e) {
		// 	$('#ays-chart-range-value').hide();
		// });
		// /* */

		/* == Notifications dismiss button == */
			_this.$el.on('click', '.notice-dismiss', function (e) {
				_this.changeCurrentUrl('status');
			});

			_this.$el.on('click', '.toggle_ddmenu' , _this.toggleDDmenu);
		/* */

		/* Add Manual data */
			_this.$el.on("click"  , '.'+_this.htmlClassPrefix+'add-new-row-box', function(){
				_this.addChartDataRow();
			});

			_this.$el.on("click"  , '.'+_this.htmlClassPrefix+'add-new-column-box', function(){
				_this.addLineChartDataColumn();
			});

			_this.$el.on('click', '.'+_this.htmlClassPrefix+'show-on-chart-bttn', function(e){		
				var loadedChartType = _this.$el.find('.'+_this.htmlClassPrefix+'choose-source:checked').val();
				var chartType = (loadedChartType === undefined) ? _this.chartSourceData.chartType : loadedChartType;
				e.preventDefault();
				_this.showOnChart($(this), chartType);
			});

		/* */

		/* Delete data */ 
			_this.$el.on("click"  , '.'+_this.htmlClassPrefix+'chart-source-data-remove-block', function(){
				_this.deleteChartDataRow($(this));
			}); 
			_this.$el.on('mouseenter', '.'+_this.htmlClassPrefix+'chart-source-data-remove-block', function() {
				$(this).find('path').css('fill', '#ff0000');
			});
			_this.$el.on('mouseleave', '.'+_this.htmlClassPrefix+'chart-source-data-remove-block', function() {
				$(this).find('path').css('fill', '#b8b8b8');
			});
		/* */

		/* Load data on the edit page */ 
		if(_this.chartSourceData.action == 'edit'){
			_this.loadChartBySource( _this.chartSourceData.chartType );
		}
		/* */

		/* Save with Ctrl + S */
		_this.$el.on('keydown', $(document), _this.quickSaveHotKeys);
		/* */

		// Submit buttons disabling
		_this.$el.on('click', '.'+_this.htmlClassPrefix+'loader-banner', _this.submitOnce);
		/* */
		
		// Disabling submit when press enter button on inputing
		$(document).on("keypress", '.ays-text-input', _this.disableSubmit);
		/* */

		// Modal close
		$(document).on('click', '.ays-close', function () {
			$(this).parents('.ays-modal').aysModal('hide');
		});
    }

	// Disabling submit when pressing enter button on input
	AysChartBuilder.prototype.disableSubmit = function(e){
		if(e.which == 13){
			if($(document).find("#ays-charts-form").length !== 0 || $(document).find("#ays-settings-form").length !== 0){
				return false;
			}
		}
	}

	// Change tabs (tabulation)
	AysChartBuilder.prototype.changeTabs = function(e){
		if(! $(this).hasClass('no-js')){
			var elemenetID = $(this).attr('href');
			var active_tab = $(this).attr('data-tab');
			$(document).find('.nav-tab-wrapper a.nav-tab').each(function () {
				if ($(this).hasClass('nav-tab-active')) {
					$(this).removeClass('nav-tab-active');
				}
			});
			$(this).addClass('nav-tab-active');
			$(document).find('.ays-tab-content').each(function () {
				$(this).css('display', 'none');
			});
			$(document).find("[name='ays_form_tab']").val(active_tab);
			$('.ays-tab-content' + elemenetID).css('display', 'block');
			e.preventDefault();
		}
	}

	// AysChartBuilder.prototype.rangeSliderChange = function(e){
	// 	var min = e.target.min;
	// 	var max = e.target.max;
	// 	var val = e.target.value;
	// 	var width = $('#ays-chart-option-font-size').width();

	// 	$(e.target).css('backgroundSize', (val - min) / (max - min) * 100 + '% 100%');

	// 	$('#ays-chart-range-value').css('left', 12.8 + (val - min) / (max - min + 1) * width + 'px');
	// 	$('#ays-chart-range-value span').html(val);
	// }

	AysChartBuilder.prototype.changeCurrentUrl = function(e){
		var linkModified = location.href.split('?')[1].split('&');
		for(var i = 0; i < linkModified.length; i++){
			if(linkModified[i].split("=")[0] == key){
				linkModified.splice(i, 1);
			}
		}
		linkModified = linkModified.join('&');
		window.history.replaceState({}, document.title, '?'+linkModified);
	}

	AysChartBuilder.prototype.toggleDDmenu = function(e){
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
	}

	AysChartBuilder.prototype.submitOnce = function(el) {
        setTimeout(function() {
			$(document).find('.ays-chart-loader-banner').attr('disabled', true);
        }, 50);

        setTimeout(function() {
            $(document).find('.ays-chart-loader-banner').attr('disabled', false);
        }, 5000);
	}

	// Load charts by given type main function
	AysChartBuilder.prototype.initLibraries = function (){
		var _this = this;

	}

	// Load charts by given type main function
	AysChartBuilder.prototype.loadChartBySource = function( chartType ){
		var _this = this;

		if( ! chartType ){
			chartType = _this.chartSourceData.chartType;
		}

		if(typeof chartType !== undefined && chartType){
			switch (chartType) {
				case 'pie_chart':
					_this.pieChartView( chartType );
					break;
				case 'bar_chart':
					_this.barChartView( chartType );
					break;
				case 'column_chart':
					_this.columnChartView( chartType );
					break;
				default:
					_this.pieChartView( chartType );
					break;
			}
		}		
	}

	// Load chart by pie chart
	AysChartBuilder.prototype.pieChartView = function( chartType ){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;

		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.chartSourceData.settings;
		var chartFontSize = settings['font_size'];
		var tooltipTrigger = settings['tooltip_trigger'];
		var showColorCode = (settings['show_color_code'] == 'checked') ? true : false;

		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			_this.chartData = google.visualization.arrayToDataTable( dataTypes );

			_this.chartOptions = {
				backgroundColor: 'transparent',
				fontSize: chartFontSize,
				tooltip: { 
					trigger: tooltipTrigger,
					showColorCode: showColorCode
				}
			};

			// height: 250,
			// 	chartArea: {
			// 	width: '80%',
			// 		height: '80%'
			// },

			_this.chartObj = new google.visualization.PieChart( document.getElementById(_this.htmlClassPrefix + chartType) );

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}

	// Load chart by bar chart
	AysChartBuilder.prototype.barChartView = function( chartType ){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;
		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.chartSourceData.settings;
		var chartFontSize = settings['font_size'];
		var tooltipTrigger = settings['tooltip_trigger'];
		var showColorCode = (settings['show_color_code'] == 'checked') ? true : false;

		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			_this.chartData = google.visualization.arrayToDataTable(dataTypes);

			_this.chartOptions = {
				backgroundColor: 'transparent',
				fontSize: chartFontSize,
				legend: { position: 'none' },
				tooltip: { 
					trigger: tooltipTrigger,
					showColorCode: showColorCode
				}
			};

			_this.chartObj = new google.visualization.BarChart(document.getElementById(_this.htmlClassPrefix + chartType));

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}

	// Load chart by column chart
	AysChartBuilder.prototype.columnChartView = function( chartType ){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;

		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.chartSourceData.settings;
		var chartFontSize = settings['font_size'];
		var tooltipTrigger = settings['tooltip_trigger'];
		var showColorCode = (settings['show_color_code'] == 'checked') ? true : false;

		// Collect data in new array for chart rendering (Column chart)

		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			_this.chartData = google.visualization.arrayToDataTable(dataTypes);

			_this.chartOptions = {
				backgroundColor: 'transparent',
				fontSize: chartFontSize,
				legend: { position: 'none' },
				tooltip: { 
					trigger: tooltipTrigger,
					showColorCode: showColorCode
				}
			};

			_this.chartObj = new google.visualization.ColumnChart(document.getElementById(_this.htmlClassPrefix + chartType));

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}

	// Detect window resize moment to draw charts responsively 
	AysChartBuilder.prototype.resizeChart = function(){
		var _this = this;

		//create trigger to resizeEnd event     
		$(window).resize(function() {
			if(this.resizeTO) clearTimeout(this.resizeTO);
			this.resizeTO = setTimeout(function() {
				$(this).trigger('resizeEnd');
			}, 100);
		});
	
		//redraw graph when window resize is completed  
		$(window).on('resizeEnd', function() {
			_this.chartObj.draw( _this.chartData, _this.chartOptions );
		});
		
	}

	// Load chart by pie chart
	AysChartBuilder.prototype.chartConvertData = function( data ){
		var _this = this;
		// var dataTypes = [['Option', 'Value', { role: 'style' }]];
		var dataTypes = [['Option', 'Value']];

		// var defaultColors = _this.chartSourceData.settings['colors'];

		// Collect data in new array for chart rendering
		for ( var key in data ) {
			if ( data.hasOwnProperty( key ) ) {
				// var columnColor = (key <= 10) ? defaultColors[key] : Math.floor( Math.random() * 16777215 ).toString(16);
				dataTypes.push([
					data[key][0], +(data[key][1])
				]);
			}
		}

		return dataTypes;
	}

	// Update chart data and display immediately
	AysChartBuilder.prototype.updateChartData =  function( newData ){
		var _this = this;
		_this.chartObj.draw( newData, _this.chartOptions );
	}

	AysChartBuilder.prototype.addChartDataRow = function (element){
        // var deleteImageUrl = ChartBuilderSourceData.removeManualDataRow;
        var content = '';

        var addedTermsandConds = this.$el.find("."+this.htmlClassPrefix+"chart-source-data-edit-block");
        var addedTermsandCondsId = this.$el.find("."+this.htmlClassPrefix+"chart-source-data-edit-block:last-child");
        var dataId = addedTermsandConds.length >= 1 ? addedTermsandCondsId.data("sourceId") + 1 : 1;
		var colCount = addedTermsandConds.first().children().length - 1;

        var termsCondsMessageAttrName = this.newTermsCondsMessageAttrName(  this.htmlNamePrefix + 'chart_source_data' ,  dataId );

            // content += '<div class = "'+this.htmlClassPrefix+'chart-source-data-edit-block" data-source-id="' + dataId + '">';
            //     content += '<div class="'+this.htmlClassPrefix+'chart-source-data-input-box">';
            //     	content += '<input type="text" class="ays-text-input form-control" name="' + termsCondsMessageAttrName + '">';
            //     content += '</div>';
            //     content += '<div class="'+this.htmlClassPrefix+'chart-source-data-input-box">';
            //     	content += '<input type="text" class="ays-text-input form-control" name="' + termsCondsMessageAttrName + '">';
            //     content += '</div>';
            //     content += '<div class="'+this.htmlClassPrefix+'icons-box '+this.htmlClassPrefix+'icons-remove-box">';
            //         content += '<img class="'+this.htmlClassPrefix+'chart-source-data-remove-block" src="' + deleteImageUrl + '">';
            //     content += '</div>';
            // content += '</div>';

		content += '<div class = "'+this.htmlClassPrefix+'chart-source-data-edit-block" data-source-id="' + dataId + '" >';
			content += '<div class="'+this.htmlClassPrefix+'icons-box '+this.htmlClassPrefix+'icons-remove-box">';
				content += '<svg class="'+this.htmlClassPrefix+'chart-source-data-remove-block" data-trigger="hover" data-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;"/></svg>';
			content += '</div>';
			for (var i = 0; i < colCount; i++) {
				if (i == 0) {
					content += '<div class="'+this.htmlClassPrefix+'chart-source-data-input-box">';
						content += '<input type="text" class="ays-text-input form-control" name="' + termsCondsMessageAttrName + '">';
					content += '</div>';
				} else {
					content += '<div class="'+this.htmlClassPrefix+'chart-source-data-input-box '+this.htmlClassPrefix+'chart-source-data-input-number">';
						content += '<input type="number" class="ays-text-input form-control" name="' + termsCondsMessageAttrName + '">';
					content += '</div>';
				}
			}
		content += '</div>';


		this.$el.find('.'+this.htmlClassPrefix+'chart-source-data-content').append(content);
	}

	AysChartBuilder.prototype.deleteChartDataRow = function (element){
		var $this = element;
		var $thisMainParent = $this.parent().parent();
		$this.tooltip('hide');
		$thisMainParent.remove();
	}

	AysChartBuilder.prototype.newTermsCondsMessageAttrName = function (termCondName, termCondId){
		var _this = this;
		return termCondName + '['+ termCondId +'][]';	
	}
    
    AysChartBuilder.prototype.setAccordionEvents = function(e){
        var _this = this;
        _this.$el.on('click', '.ays-accordion-options-header', function(e){
			_this.openCloseAccordion(e, _this);
		});
    }
    
    AysChartBuilder.prototype.openCloseAccordion = function(e, _this){

        var container = $(e.target).parents('.ays-accordion-options-container');

        if( container.attr('data-collapsed') === 'true' ){
			_this.closeAllAccordions( container.parents('.ays-tab-content').attr('id') );
            // container.find('.ays-accordion-options-content').removeClass('display_none');
            container.attr('data-collapsed', 'false');
            container.find('.ays-accordion-options-header .ays-accordion-arrow').removeClass('ays-accordion-arrow-right').addClass('ays-accordion-arrow-down');
        }else{
            // container.find('.ays-accordion-options-content').addClass('display_none');
            container.attr('data-collapsed', 'true');
            container.find('.ays-accordion-options-header .ays-accordion-arrow').removeClass('ays-accordion-arrow-down').addClass('ays-accordion-arrow-right');
        }
    }
    
    AysChartBuilder.prototype.closeAllAccordions = function( tab ){
		var _this = this;
        var container = _this.$el.find('#' + tab + ' .ays-accordions-container');

		container.find('.ays-accordion-options-container').each(function (){
			$(this).attr('data-collapsed', 'true');
			$(this).find('.ays-accordion-options-header .ays-accordion-arrow').removeClass('ays-accordion-arrow-down').addClass('ays-accordion-arrow-right');
		});
    }

	AysChartBuilder.prototype.startAjax = function( element ){
		element.lock();
	}

	AysChartBuilder.prototype.endAjax = function( element ){
		element.unlock();
	}

	AysChartBuilder.prototype.quickSaveHotKeys = function() {
		$(document).on('keydown', function(e){
			var saveButton = $(document).find('input#ays-button-apply');
			if ( saveButton.length > 0 ) {
                if (!(e.which == 83 && e.ctrlKey) && !(e.which == 19)){
                    return true;
                }
                saveButton.trigger("click");
                e.preventDefault();
                return false;
            }
		});
	}

	// Manual data show on chart button
	AysChartBuilder.prototype.showOnChart = function (button, chartType) {
		var _this = this;
		
		var form = $(document).find("#ays-charts-form");
		var data = form.serializeFormJSON();
		// var count = $(document).find(".ays-chart-chart-source-data-edit-block").length;
		var lastId = $(document).find(".ays-chart-chart-source-data-edit-block:last-child").attr('data-source-id');
		var chartData = [];
		for (var i = 0; i <= lastId; i++) {
			if (data['ays_chart_source_data['+i+'][]'] != undefined) {
				chartData.push(data['ays_chart_source_data['+i+'][]']);
			}
		}
		chartData = _this.chartConvertData( chartData );

		chartData = window.google.visualization.arrayToDataTable( chartData );

		_this.updateChartData( chartData );

		chartData = null;
	}

	$.fn.AysChartBuilderMain = function(options) {
        return this.each(function() {
            if (!$.data(this, 'AysChartBuilderMain')) {
                $.data(this, 'AysChartBuilderMain', new AysChartBuilder(this, options));
            } else {
                try {
                    $(this).data('AysChartBuilderMain').init();
                } catch (err) {
                    console.error('AysChartBuilderMain has not initiated properly');
                }
            }
        });
    };
    $(document).find('#ays-charts-form').AysChartBuilderMain();

})(jQuery);

(function ($) {

	$.fn.serializeFormJSON = function () {
		let o = {},
			a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

	$.fn.lock = function () {
		$(this).each(function () {
			var $this = $(this);
			var position = $this.css('position');

			if (!position) {
				position = 'static';
			}

			switch (position) {
				case 'absolute':
				case 'relative':
					break;
				default:
					$this.css('position', 'relative');
					break;
			}
			$this.data('position', position);

			var width = $this.width(),
				height = $this.height();

			var locker = $('<div class="locker"></div>');
			locker.width(width).height(height);

			var loader = $('<div class="locker-loader"></div>');
			loader.width(width).height(height);

			locker.append(loader);
			$this.append(locker);
			$(window).resize(function () {
				$this.find('.locker,.locker-loader').width($this.width()).height($this.height());
			});
		});

		return $(this);
	};

	$.fn.unlock = function () {
		$(this).each(function () {
			$(this).find('.locker').remove();
			$(this).css('position', $(this).data('position'));
		});

		return $(this);
	};
})(jQuery);