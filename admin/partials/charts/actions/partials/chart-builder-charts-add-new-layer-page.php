<div class="<?php echo esc_attr($html_class_prefix); ?>layer_container">
    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_content">
        <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box">
            <div class="<?php echo esc_attr($html_class_prefix); ?>close-type">
                <a href="?page=chart-builder">
                    <img src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/cross.png">
                </a>
            </div>
            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_blocks">
                <!-- <div class="<?//= $html_class_prefix; ?>layer_box_each_block">
                    <div class="<?//= $html_class_prefix; ?>layer_box_layer_block">
                        <label class='<?//= $html_class_prefix; ?>dblclick-layer'>
                            <input id="<?php // echo $this->plugin_name; ?>-modal_content_shortcode" type="radio" name="<?php // echo $this->plugin_name; ?>[modal_content]" class="<?//= $html_class_prefix; ?>choose-source" value="line_chart">
                            <div class="<?//= $html_class_prefix; ?>layer_item">
                                <div class="<?//= $html_class_prefix; ?>layer_item_logo">
                                    <div class="<?//= $html_class_prefix; ?>layer_item_logo_overlay">
                                        <img class="<?//= $html_class_prefix; ?>layer_icons" src="<?//= CHART_BUILDER_ADMIN_URL; ?>/images/icons/line-chart-logo.png">
                                    </div>
                                    <div class="<?//= $html_class_prefix; ?>layer_item_checked">
                                        <img src="<?//= CHART_BUILDER_ADMIN_URL; ?>/images/icons/check.svg">
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="<?//= $html_class_prefix; ?>layer_item_separate_title">
                        <span><?php // echo __('Line Chart', "chart-builder") ?></span>
                    </div>
                </div> -->
                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_each_block">
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_layer_block">
                        <label class='<?php echo esc_attr($html_class_prefix); ?>dblclick-layer'>
                            <input id="<?php echo esc_attr($this->plugin_name); ?>-modal_content_custom_html" type="radio" name="<?php echo esc_attr($this->plugin_name); ?>[modal_content]" class="<?php echo esc_attr($html_class_prefix); ?>choose-source" value="bar_chart">
                            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item">
                                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo">
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo_overlay">
                                        <img class="<?php echo esc_attr($html_class_prefix); ?>layer_icons" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/bar-chart-logo.png">
                                    </div>
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_checked">
                                        <img src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/check.svg">
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_separate_title">
                        <span><?php echo  __('Bar Chart', "chart-builder") ?></span>
                    </div>
                </div>
                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_each_block">
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_layer_block">
                        <label class='<?php echo esc_attr($html_class_prefix); ?>dblclick-layer'>
                            <input id="<?php echo esc_attr($this->plugin_name); ?>-modal_content_subscription" type="radio" name="<?php echo esc_attr($this->plugin_name); ?>[modal_content]" class="<?php echo esc_attr($html_class_prefix); ?>choose-source" value="pie_chart" >
                            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item">
                                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo">
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo_overlay">
                                        <img class="<?php echo esc_attr($html_class_prefix); ?>layer_icons" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/pie-chart-logo.png">
                                    </div>
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_checked">
                                        <img src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/check.svg">
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_separate_title">
                        <span><?php echo  __('Pie Chart', "chart-builder") ?></span>

                    </div>
                </div>
                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_each_block">
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_box_layer_block">
                        <label class='<?php echo esc_attr($html_class_prefix); ?>dblclick-layer'>
                            <input id="<?php echo esc_attr($this->plugin_name); ?>-modal_content_subscription" type="radio" name="<?php echo esc_attr($this->plugin_name); ?>[modal_content]" class="<?php echo esc_attr($html_class_prefix); ?>choose-source" value="column_chart" >
                            <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item">
                                <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo">
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_logo_overlay">
                                        <img class="<?php echo esc_attr($html_class_prefix); ?>layer_icons" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/column-chart-logo.png">
                                    </div>
                                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_checked">
                                        <img src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/check.svg">
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="<?php echo esc_attr($html_class_prefix); ?>layer_item_separate_title">
                        <span><?php echo  __('Column Chart', "chart-builder") ?></span>
                    </div>
                </div>
            </div>
            <div class="<?php echo esc_attr($html_class_prefix); ?>select_button_layer">
                <div class="<?php echo esc_attr($html_class_prefix); ?>select_button_item">
                    <input type="button" class="<?php echo esc_attr($html_class_prefix); ?>layer_button" name="" value="Next >" disabled>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="<?//= $html_class_prefix; ?>select_button_layer">
        <div class="<?//= $html_class_prefix; ?>select_button_item">
            <input type="button" class="<?//= $html_class_prefix; ?>layer_button" name="" value="Next >" disabled>
        </div>
    </div> -->
</div>