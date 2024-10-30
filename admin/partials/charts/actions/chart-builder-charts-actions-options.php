<?php

    $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

    $id = (isset($_GET['id'])) ? absint( esc_attr($_GET['id']) ) : 0;

    $html_name_prefix = 'ays_';
    $html_class_prefix = 'ays-chart-';

    $user_id = get_current_user_id();

    $options = array(

    );

    $chart_types = array(
        'bar_chart'    => "Bar Chart",
        'pie_chart'    => "Pie Chart",
        'column_chart' => "Column Chart",
    );

    $object = array(
        'title' => '',
        'description' => '',
        'type' => 'google-charts',
        'source_chart_type' => 'pie_chart',
        'source_type' => 'manual',
        'source' => '',
        'status' => 'published',
        'date_created' => current_time( 'mysql' ),
        'date_modified' => current_time( 'mysql' ),
        'options' => json_encode( $options ),
    );

    $chart_data = array(
        'chart' => $object,
        'source_type' => 'manual',
        'source' => '',
        'settings' => array(),
        'options' => array(),
    );

	$tooltip_trigger_options = array(
		"hover" => __("While hovering", "chart-builder"),
		"selection" => __("When selected", "chart-builder"),
		"none" => __("Disable", "chart-builder")
	);

	$chart_source_default_data = CBActions()->get_charts_default_data();

    $heading = '';
    switch ($action) {
        case 'add':
            $heading = __( 'Add new chart', "chart-builder" );
            break;
        case 'edit':
            $heading = __( 'Edit chart', "chart-builder" );
            $object = $this->db_obj->get_item( $id );
            $chart_data = CBActions()->get_chart_data( $id );
            break;
    }

    if( isset( $_POST['ays_submit'] ) || isset( $_POST['ays_submit_top'] ) ) {
        $this->db_obj->add_or_edit_item( $id );
    }

    if( isset( $_POST['ays_apply'] ) || isset( $_POST['ays_apply_top'] ) ){
        $_POST['save_type'] = 'apply';
        $this->db_obj->add_or_edit_item( $id );
    }

    if( isset( $_POST['ays_save_new'] ) || isset( $_POST['ays_save_new_top'] ) ){
        $_POST['save_type'] = 'save_new';
        $this->db_obj->add_or_edit_item( $id );
    }


    $loader_iamge = '<span class="display_none ays_chart_loader_box"><img src="'. CHART_BUILDER_ADMIN_URL .'/images/loaders/loading.gif"></span>';

    /**
     * Data that need to get form @object variable
     *
     * @object is a data directly from database
     */

    // Date created
    $date_created = isset( $object['date_created'] ) && CBFunctions()->validateDate( $object['date_created'] ) ? esc_attr($object['date_created']) : current_time( 'mysql' );

    // Date modified
    $date_modified = current_time( 'mysql' );


    /**
     * Data that need to get form @chart_data variable
     */



    /**
     * Data that need to get form @chart variable
     */

    // Chart
    $chart = $chart_data['chart'];

    // Source
    $source = isset($chart_data['source']) && $chart_data['source'] != '' ? $chart_data['source'] : $chart_source_default_data;

    // Source type
    $source_type = stripslashes( $chart['source_type'] );

    // Chart type
    $source_chart_type = stripslashes( $chart['source_chart_type'] );

    // Title
    $title = stripcslashes( $chart['title'] );

    // Description
    $description = stripcslashes( $chart['description'] );

    // Status
    $status = stripslashes( $chart['status'] );


    /**
     * Data that need to get form @settings variable
     */

    // Settings
    $settings = $chart_data['settings'];

    // Width
	$settings['width'] = isset( $settings['width'] ) && $settings['width'] != '' ? esc_attr( $settings['width'] ) : '100';

    // Height
	$settings['height'] = isset( $settings['height'] ) && $settings['height'] != '' ? esc_attr( $settings['height'] ) : '400';

    // Font size
	$settings['font_size'] = isset( $settings['font_size'] ) && $settings['font_size'] != '' ? esc_attr( $settings['font_size'] ) : '15';

	// Title color
	$settings['title_color'] = isset( $settings['title_color'] ) && $settings['title_color'] != '' ? esc_attr( $settings['title_color'] ) : '#000000';

	// Tooltip trigger
	$settings['tooltip_trigger'] = isset( $settings['tooltip_trigger'] ) && $settings['tooltip_trigger'] != '' ? esc_attr( $settings['tooltip_trigger'] ) : 'hover';
	$settings['tooltip_trigger_options'] = $tooltip_trigger_options;

	// Show color code
	$settings['show_color_code'] = ( isset( $settings['show_color_code'] ) && $settings['show_color_code'] != '' ) ? $settings['show_color_code'] : 'off';
	$settings['show_color_code'] = isset( $settings['show_color_code'] ) && $settings['show_color_code'] == 'on' ? 'checked' : '';

	$settings['colors'] = ['#3366cc', '#dc3912', '#ff9900', '#109618', '#990099', '#0099c6', '#dd4477', '#66aa00', '#b82e2e', '#316395'];


/**
     * Data that need to get form @options variable
     */

    // Options
    $options = $object['options'];

    // Send data to JS
    $source_data_for_js = array(
        'source' => $source,
        'action' => $action,
        'settings' => $settings,
        'chartType' => $source_chart_type,
        'addManualDataRow' => CHART_BUILDER_ADMIN_URL . '/images/icons/add-circle-outline.svg',
        // 'removeManualDataRow' => CHART_BUILDER_ADMIN_URL . '/images/icons/xmark.svg',
    );
    wp_localize_script($this->plugin_name, "ChartBuilderSourceData" , $source_data_for_js);