<?php
    require_once( CHART_BUILDER_ADMIN_PATH . "/partials/charts/actions/chart-builder-charts-actions-options.php" );
?>
<div class="wrap">
    <div class="container-fluid">
        <form class="ays-charts-form" id="ays-charts-form" method="post">
            <h1>
		        <?php echo esc_attr($heading); ?>
                <input type="submit" name="ays_submit_top" value="<?php echo esc_html(__('Save and close', "chart-builder")) ?>" class="button button-primary ays-button ays-chart-loader-banner" id="ays-button-top-save">
                <input type="submit" name="ays_apply_top" value="<?php echo esc_html(__('Save', "chart-builder")) ?>" class="button button-secondary ays-button ays-chart-loader-banner" id="ays-button-top-apply">
            </h1>
            <hr/>

            <?php
                for($tab_ind = 1; $tab_ind <= 1; $tab_ind++){
                    require_once( CHART_BUILDER_ADMIN_PATH . "/partials/charts/actions/partials/chart-builder-charts-actions-tab".$tab_ind.".php" );
                }
            ?>

            <input type="hidden" name="<?php echo esc_attr($html_name_prefix); ?>date_created" value="<?php echo esc_attr($date_created); ?>">
            <input type="hidden" name="<?php echo esc_attr($html_name_prefix); ?>date_modified" value="<?php echo esc_attr($date_modified); ?>">
            <hr/>
            <?php
                wp_nonce_field('chart_builder_action', 'chart_builder_action');

//                $other_attributes = array('id' => 'ays-button-save');
//                submit_button(__('Save and close', "chart-builder"), 'btn btn-primary ays-button ays-survey-loader-banner', 'ays_submit', false, $other_attributes);

//                $other_attributes = array('id' => 'ays-button-save-new');
//                submit_button(__('Save and new', "chart-builder"), 'primary ays-button ays-survey-loader-banner', 'ays_save_new', false, $other_attributes);

//                $other_attributes = array('id' => 'ays-button-apply');
//                submit_button(__('Save', "chart-builder"), 'btn btn-secondary ays-button ays-survey-loader-banner', 'ays_apply', false, $other_attributes);

            ?>
            <input type="submit" name="ays_submit" value="<?php echo esc_html(__('Save and close', "chart-builder")) ?>" class="button button-primary ays-button ays-chart-loader-banner" id="ays-button-save">
            <input type="submit" name="ays_apply" value="<?php echo esc_html(__('Save', "chart-builder")) ?>" class="button button-secondary ays-button ays-chart-loader-banner" id="ays-button-apply">
            <?php 
                if($id === 0 && !isset($_GET['status'])){
                    require_once( CHART_BUILDER_ADMIN_PATH . "/partials/charts/actions/partials/chart-builder-charts-add-new-layer-page.php" );
                }
            ?>
        </form>

        <div class="ays-modal" id="ays-chart-db-query-results">
            <div class="ays-modal-content">
                <div class="ays-preloader">
                    <img class="loader" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/loaders/tail-spin-result.svg" alt="" width="100">
                </div>

                <!-- Modal Header -->
                <div class="ays-modal-header">
                    <span class="ays-close">&times;</span>
                    <h2><?php echo esc_html(__('Database query results', "chart-builder")); ?></h2>
                </div>

                <!-- Modal body -->
                <div class="ays-modal-body">
                    <div class="db-wizard-results"></div>
                </div>

                <!-- Modal footer -->
            </div>
        </div>
    </div>
</div>
