(function($) {
	'use strict';

	function AysChartPlugin(element, options) {
		this.el = element;
		this.$el = $(element);
		this.htmlClassPrefix = 'ays-chart-';
		this.htmlNamePrefix = 'ays_';
		this.uniqueId;
		this.dbData = undefined;
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
	
	AysChartPlugin.prototype.init = function() {
		var _this = this;
		_this.uniqueId = _this.$el.data('id');

		if ( typeof window.aysChartOptions != 'undefined' ) {
            _this.dbData = JSON.parse( atob( window.aysChartOptions[ _this.uniqueId ] ) );
        }

		_this.setEvents();
	}
	
	AysChartPlugin.prototype.setEvents = function(e){
		var _this = this;
		
		var chartType = _this.dbData.chart_type;

		_this.loadChartBySource( chartType );
	}

	// Load charts by given type main function
	AysChartPlugin.prototype.loadChartBySource = function( chartType ){
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
	AysChartPlugin.prototype.pieChartView = function( chartType ){
		var _this = this;
		var getChartSource = _this.dbData.source;

		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.dbData.options;
		var chartFontSize = settings['font_size'];
		var tooltipTrigger = settings['tooltip_trigger'];
		var showColorCode = (settings['show_color_code'] == 'on') ? true : false;


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
	AysChartPlugin.prototype.barChartView = function( chartType ){
		var _this = this;
		var getChartSource = _this.dbData.source;
		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.dbData.options;
		var chartFontSize = settings['font_size'];
		var tooltipTrigger = settings['tooltip_trigger'];
		var showColorCode = (settings['show_color_code'] == 'on') ? true : false;

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
	AysChartPlugin.prototype.columnChartView = function( chartType ){
		var _this = this;
		var getChartSource = _this.dbData.source;

		// Collect data in new array for chart rendering (Column chart)
		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.dbData.options;
		var chartFontSize = settings['font_size'];
		var tooltipTrigger = settings['tooltip_trigger'];
		var showColorCode = (settings['show_color_code'] == 'on') ? true : false;

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
	AysChartPlugin.prototype.resizeChart = function(){
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
	AysChartPlugin.prototype.chartConvertData = function( data ){
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
	AysChartPlugin.prototype.updateChartData =  function( newData ){
		var _this = this;
		_this.chartObj.draw( newData, _this.chartOptions );
	}

	$.fn.AysChartBuilder = function(options) {
		return this.each(function() {
			if (!$.data(this, 'AysChartBuilder')) {
				$.data(this, 'AysChartBuilder', new AysChartPlugin(this, options));
			} else {
				try {
					$(this).data('AysChartBuilder').init();
				} catch (err) {
					console.error('AysChartBuilder has not initiated properly');
				}
			}
		});
	};

})(jQuery);
