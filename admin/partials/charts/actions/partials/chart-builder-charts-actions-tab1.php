<div id="tab1" class="ays-chart-tab-content ays-chart-tab-content-active">
    <div class="form-group row">
        <div class="col-sm-2">
            <label for='ays-title'>
                <?php echo esc_html(__('Title', "chart-builder")); ?>
                <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_html(__('Set the chart title.',"chart-builder")); ?>">
                    <i class="ays_fa ays_fa_info_circle"></i>
                </a>
            </label>
        </div>
        <div class="col-sm-10">
            <input type="text" class="ays-text-input form-control" id='ays-title' name='<?php echo esc_attr($html_name_prefix); ?>title' value="<?php echo esc_attr($title); ?>" />
        </div>
    </div> <!-- Title -->
    <div class='<?php echo esc_attr($html_class_prefix); ?>type-info-box'>
        <span class='<?php echo esc_attr($html_class_prefix); ?>type-info-box-text'>
            <?php echo esc_html(__('Chart type:' , "chart-builder")); ?> 
            <span class='<?php echo esc_attr($html_class_prefix); ?>type-info-box-text-changeable'>
                <?php echo esc_attr($chart_types[$source_chart_type]); ?>
            </span>
        </span>
        <input type="hidden" class="form-control" id="ays-chart-option-chart-type" name="<?php echo esc_attr($html_name_prefix); ?>source_chart_type" value="<?php echo esc_attr($source_chart_type) ?>">
    </div>
    <hr/>
    <div class="row">
        <div class="col-sm-6">
            <div class="<?php echo esc_attr($html_class_prefix) ?>charts-main-container" id="<?php echo esc_attr($html_class_prefix).esc_attr($source_chart_type) ?>" style="width: <?php echo esc_attr($settings['width']) ?>%; height: <?php echo esc_attr($settings['height']) ?>px"></div>
        </div>
        <div class="col-sm-6">
            <div>
                <div class="nav-tab-wrapper <?php echo esc_attr($html_class_prefix) ?>nav-tab-wrapper-chart">
                    <a href="#source" data-tab="tab1" class="<?php echo esc_attr($html_class_prefix) ?>nav-tab-chart nav-tab nav-tab-active">
                        <?php echo esc_html(__("Source", "chart-builder")); ?>
                    </a>
                    <a href="#settings" data-tab="tab2" class="<?php echo esc_attr($html_class_prefix) ?>nav-tab-chart nav-tab">
                        <?php echo esc_html(__("Settings", "chart-builder")); ?>
                    </a>
                </div>
            </div>

            <div class="ays-form-tabs-wrapper">
                <div id="source" class="ays-tab-content ays-tab-content-active">
                    <input type="hidden" name="<?php echo esc_attr($html_name_prefix); ?>source_type" value="<?php echo esc_attr($source_type) ?>">
                    <br>
                    <div class="ays-accordions-container">
                        <?php
                        do_action( 'ays_cb_chart_page_sources_contents', array(
                            'chart' => $chart,
                            'chart_id' => $id,
                            'html_class_prefix' => $html_class_prefix,
                            'html_name_prefix' => $html_name_prefix,
                            'source' => $source,
                            'source_type' => $source_type,
                            'settings' => $settings,
                        ) );
                        ?>
                    </div>
                </div>
                <div id="settings" class="ays-tab-content ">
                    <br>
                    <div class="ays-accordions-container">
	                    <?php
	                    do_action( 'ays_cb_chart_page_settings_contents', array(
		                    'chart_id' => $id,
		                    'html_class_prefix' => $html_class_prefix,
		                    'html_name_prefix' => $html_name_prefix,
		                    'source' => $source,
		                    'source_type' => $source_type,
		                    'status' => $status,
		                    'settings' => $settings,
	                    ) );
	                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
