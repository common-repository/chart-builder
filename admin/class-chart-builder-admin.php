<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/admin
 * @author     Chart Builder Team <info@ays-pro.com>
 */
class Chart_Builder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The capability of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $capability    The capability for users access to this plugin.
	 */
    private $capability;

	/**
	 * @var Chart_Builder_DB_Actions
	 */
	private $db_obj;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

        add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);
		add_filter('set_screen_option_cb_charts_per_page', array(__CLASS__, 'set_screen'), 10, 3);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook_suffix) {
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        // Not enqueue styles if they are not on the current plugin page
        if (false === strpos($hook_suffix, $this->plugin_name)) return;
        //
		wp_enqueue_style( $this->plugin_name . '-normalize', plugin_dir_url( __FILE__ ) . 'css/normalize.css', array(), $this->version . time(), 'all' );
		wp_enqueue_style( $this->plugin_name . '-admin-general', plugin_dir_url( __FILE__ ) . 'css/admin-general.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-banner', plugin_dir_url( __FILE__ ) . 'css/banner.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '-animate', plugin_dir_url(__FILE__) .  'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-font-awesome', plugin_dir_url(__FILE__) .  'css/ays-font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-font-awesome-icons', plugin_dir_url(__FILE__) .  'css/ays-font-awesome-icons.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url(__FILE__) .  'css/ays-select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-chosen', plugin_dir_url(__FILE__) .  'css/chosen.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style( $this->plugin_name . '-data-bootstrap', plugin_dir_url(__FILE__) . 'css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');

		wp_enqueue_style( $this->plugin_name . '-layer', plugin_dir_url( __FILE__ ) . 'css/chart-builder-admin-layer.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chart-builder-admin.css', array(), $this->version . time(), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook_suffix) {
		if (false !== strpos($hook_suffix, "plugins.php")){
            wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, true);
			wp_localize_script($this->plugin_name . '-admin',  'chart_builder_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        // Not enqueue scripts if they are not on the current plugin page
        if (false === strpos($hook_suffix, $this->plugin_name)) return;
        //
        global $wp_version;
        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.5';
        $versionCompare = CBFunctions()->versionCompare( $version1, $operator, $version2 );

        if ( $versionCompare ) {
            wp_enqueue_script( $this->plugin_name.'-wp-load-scripts', plugin_dir_url(__FILE__) . 'js/load-scripts.js', array(), $this->version, true);
        }

        wp_enqueue_script( 'jquery' );

        /*
        ==========================================
           * Bootstrap
           * select2
           * jQuery DataTables
        ==========================================
        */
        wp_enqueue_script( $this->plugin_name . "-popper", plugin_dir_url(__FILE__) . 'js/popper.min.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . "-bootstrap", plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name . '-select2js', plugin_dir_url( __FILE__ ) . 'js/ays-select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-chosen', plugin_dir_url( __FILE__ ) . 'js/chosen.jquery.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . '-datatable-min', plugin_dir_url( __FILE__ ) . 'js/chart-builder-datatable.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script( $this->plugin_name . "-db4.min.js", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, true );

		$table_col_mapping  = CBFunctions()->get_all_db_tables_column_mapping( 1 );

		wp_enqueue_code_editor(
			array(
				'type' => 'sql',
				'codemirror' => array(
					'autofocus'         => true,
					'lineWrapping'      => true,
					'dragDrop'          => false,
					'matchBrackets'     => true,
					'autoCloseBrackets' => true,
					'extraKeys'         => array( 'Ctrl-Space' => 'autocomplete' ),
					'hintOptions'       => array( 'tables' => $table_col_mapping ),
				),
			)
		);

        wp_enqueue_script( $this->plugin_name . '-charts-google', plugin_dir_url(__FILE__) . 'js/google-chart.js', array('jquery'), $this->version, true);

		wp_enqueue_script( $this->plugin_name . '-functions', plugin_dir_url( __FILE__ ) . 'js/functions.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chart-builder-admin.js', array( 'jquery' ), $this->version .time(), true );

		wp_enqueue_script( $this->plugin_name . "-general-js", plugin_dir_url( __FILE__ ) . 'js/chart-builder-admin-general.js', array( 'jquery' ), $this->version, true );
		wp_localize_script( $this->plugin_name, 'aysChartBuilderAdmin', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'selectUserRoles'                   => __( 'Select user roles', "chart-builder" ),
			'delete'                            => __( 'Delete', "chart-builder" ),
			'selectQuestionDefaultType'         => __( 'Select question default type', "chart-builder" ),
			'yes'                               => __( 'Yes', "chart-builder" ),
			'cancel'                            => __( 'Cancel', "chart-builder" ),
			'somethingWentWrong'                => __( "Maybe something went wrong.", "chart-builder" ),
			'failed'                            => __( 'Failed', "chart-builder" ),
			'selectPage'                        => __( 'Select page', "chart-builder" ),
			'selectPostType'                    => __( 'Select post type', "chart-builder" ),
			'copied'                            => __( 'Copied!', "chart-builder"),
			'clickForCopy'                      => __( 'Click for copy', "chart-builder"),
			'selectForm'                        => __( 'Select form', "chart-builder"),
		) );

		wp_localize_script(
			$this->plugin_name, 'aysChartBuilderChartSettings', array(
				'types' => CBFunctions()->getAllowedTypes(),
				'max_selected_options' => 2,
				'l10n' => array(
					'invalid_source'      => esc_html__( 'You have entered invalid URL. Please, insert proper URL.', "chart-builder" ),
					'loading'             => esc_html__( 'Loading...', "chart-builder" ),
					'filter_config_error' => esc_html__( 'Please check the filters you have configured.', "chart-builder" ),
					'select_columns'      => esc_html__( 'Please select a few columns to include in the chart.', "chart-builder" ),
					'save_settings'       => __( 'You have modified the chart\'s settings. To modify the source/data again, you must save this chart and reopen it for editing. If you continue without saving the chart, you may lose your changes.', "chart-builder" ),
				),
				'ajax' => array(
					'url' => admin_url( 'admin-ajax.php' ),
					'nonces' => array(
						'filter_get_props' => wp_create_nonce( 'cbuilder-fetch-post-type-props' ),
						'filter_get_data'  => wp_create_nonce( 'cbuilder-fetch-post-type-data' ),
					),
					'actions' => array(
						'filter_get_props' => 'fetch_post_type_props',
						'filter_get_data' => 'fetch_post_type_data',
					),
				),
				'db_query' => array(
					'tables' => $table_col_mapping,
				),
			)
		);
	}


    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu(){

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        global $wpdb;
        // $sql = "SELECT COUNT(*) FROM " . esc_sql( $wpdb->prefix . EXAM_MAKER_DB_PREFIX ) . "submissions WHERE `read` = 0 OR `read` = 2 ";
        // $unread_results_count = intval( $wpdb->get_var( $sql ) );
        $menu_item = __( 'Chart Builder', "chart-builder" );// : 'Exam Maker' . '<span class="ays-survey-menu-badge ays-survey-results-bage">' . $unread_results_count . '</span>';

        $this->capability = 'manage_options';// $this->survey_maker_capabilities();
        // $this->current_user_can_edit = Survey_Maker_Data::survey_maker_capabilities_for_editing();

        $hook_page_view = add_menu_page(
            __( 'Charts', "chart-builder" ),
            $menu_item,
            $this->capability,
            $this->plugin_name,
            array($this, 'display_plugin_charts_page'),
            CHART_BUILDER_ADMIN_URL . '/images/icons/ays_chart_logo_icon.png',
            '46.00'
        );

	    add_action( "load-$hook_page_view", array( $this, 'screen_option_charts' ) );

    }

	public function screen_option_charts(){
		$option = 'per_page';
		$args = array(
			'label' => __('Charts', "chart-builder"),
			'default' => 5,
			'option' => 'cb_charts_per_page'
		);

		if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
			add_screen_option( $option, $args );
		}

	//		$this->charts_obj = new Surveys_List_Table($this->plugin_name);
		$this->db_obj = new Chart_Builder_DB_Actions( $this->plugin_name );
	//        var_dump($this);
	//		$this->settings_obj = new Survey_Maker_Settings_Actions($this->plugin_name);
	}

	public function display_plugin_charts_page(){
        global $ays_chart_db_actions;

        $action = (isset($_GET['action'])) ? sanitize_text_field( $_GET['action'] ) : '';
		$id = (isset($_GET['id'])) ? absint( esc_attr($_GET['id']) ) : 0;

        switch ($action) {
            case 'trash':
                if( $id > 0 ){
                    $this->db_obj->trash_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'trashed'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'restore':
                if( $id > 0 ){
                    $this->db_obj->restore_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'restored'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'delete':
                if( $id > 0 ){
                    $this->db_obj->delete_item( $id );
	                $url = remove_query_arg( array('action', 'id', '_wpnonce') );
	                $url = esc_url_raw( add_query_arg( array(
		                "status" => 'deleted'
	                ), $url ) );
	                wp_redirect( $url );
	                exit;
                }
                break;
            case 'add':
                include_once('partials/charts/actions/chart-builder-charts-actions.php');
                break;
            case 'edit':
                include_once('partials/charts/actions/chart-builder-charts-actions.php');
                break;
            default:
                include_once('partials/charts/chart-builder-charts-display.php');
        }
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links( $links ){
        /*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', "chart-builder") . '</a>',
            '<a href="https://ays-demo.com/chart-builder-demo/" target="_blank">' . __('Demo', "chart-builder") . '</a>',
        );
        return array_merge( $settings_link, $links );

    }


    public static function set_screen($status, $option, $value){
        return $value;
    }

	public function ays_admin_ajax(){
		global $wpdb;

		$response = array(
			"status" => false
		);

		$function = isset($_REQUEST['function']) ? sanitize_text_field( $_REQUEST['function'] ) : null;

		if($function !== null){
			$response = array();
			if( is_callable( array( $this, $function ) ) ){
				$response = $this->$function();

	            ob_end_clean();
	            $ob_get_clean = ob_get_clean();
				echo json_encode( $response );
				wp_die();
			}

        }

        ob_end_clean();
        $ob_get_clean = ob_get_clean();
		echo json_encode( $response );
		wp_die();
	}

    public function deactivate_plugin_option(){
        $request_value = esc_attr($_REQUEST['upgrade_plugin']);
        $upgrade_option = get_option( 'ays_chart_builder_upgrade_plugin', '' );
        if($upgrade_option === ''){
            add_option( 'ays_chart_builder_upgrade_plugin', $request_value );
        }else{
            update_option( 'ays_chart_builder_upgrade_plugin', $request_value );
        }
		ob_end_clean();
        $ob_get_clean = ob_get_clean();
        return json_encode( array( 'option' => get_option( 'ays_chart_builder_upgrade_plugin', '' ) ) );
		wp_die();
    }

    public function chart_builder_admin_footer($a){
        if(isset($_REQUEST['page'])){
            if(false !== strpos( sanitize_text_field( $_REQUEST['page'] ), $this->plugin_name)){
                ?>
                <p style="font-size:13px;text-align:center;font-style:italic;">
                    <span style="margin-left:0px;margin-right:10px;" class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                    <span><?php echo esc_html(__( "If you love our plugin, please do big favor and rate us on", "chart-builder")); ?></span>
                    <a target="_blank" href='https://wordpress.org/support/plugin/chart-builder/reviews/'>WordPress.org</a>
                    <span class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                </p>
            <?php
            }
        }
    }

    public function fetch_post_type_props(){
	    $nonce = isset( $_POST['nonce'] ) ? wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'cbuilder-fetch-post-type-props' ) : '';
	    if ( $nonce ) {
            $results = CBFunctions()->get_post_type_properties( sanitize_text_field( $_POST['post_type'] ) );

		    return array(
			    'success' => true,
			    'fields'  => $results,
		    );
	    }

	    return array(
		    'success' => false,
	    );
    }

	/**
	 * Chart page action hooks
	 */

	/**
     * Chart page sources contents
	 * @param $args
	 */
	public function ays_chart_page_source_contents( $args ){

		$sources_contents = apply_filters( 'ays_cb_chart_page_sources_contents_settings', array(), $args );

		$source_type = $args['source_type'];

		$sources = array();
		foreach ( $sources_contents as $key => $sources_content ) {
            $collapsed = $key == $source_type ? 'false' : 'true';

			$content = '<fieldset class="ays-accordion-options-container" data-collapsed="'. $collapsed .'">';
			if( isset( $sources_content['title'] ) ){
				$content .= '<legend class="ays-accordion-options-header">';
				$content .= '<svg class="ays-accordion-arrow '. ( $key == $source_type ? 'ays-accordion-arrow-down' : 'ays-accordion-arrow-right' ) .'" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                    <g>
                        <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: rgb(0, 140, 255);" vector-effect="non-scaling-stroke" />
                    </g>
                </svg>';

				$content .= '<span>'. $sources_content['title'] .'</span></legend>';
			}

			$content .= '<div class="ays-accordion-options-content">';
			$content .= $sources_content['content'];
			$content .= '</div>';

			$content .= '</fieldset>';

			$sources[] = $content;
		}
		$content_for_escape = implode('' , $sources );
		echo html_entity_decode(esc_html( $content_for_escape ));
	}

    public function source_contents_manual_settings( $sources, $args ){
	    $html_class_prefix = $args['html_class_prefix'];
	    $html_name_prefix = $args['html_name_prefix'];
        $source = $args['source'];
        $settings = $args['settings'];

        ob_start();
	    ?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>source-data-main-wrap">
                <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-main">
                    <!-- <div class="<//?= $html_class_prefix ?>icons-box">
                        <img class="<//?= $html_class_prefix ?>add-new-row" src="<//?php echo CHART_BUILDER_ADMIN_URL; ?>/images/icons/add-circle-outline.svg">
                    </div> -->
					<div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-content-container">
                    	<div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-content">
                    	    <?php if(!empty($source)):
                    	        foreach($source as $source_id => $source_value):
                    	            if(!empty($source_value) ): ?>
                    	                <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id = "<?php echo esc_attr($source_id); ?>">
                    	                    <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>icons-remove-box">
												<svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block" data-trigger="hover" data-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
													<path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;"/>
												</svg>
                    	                    </div>
                    	                    <?php foreach($source_value as $each_source_id => $each_source_value): ?>
												<?php if ($each_source_id == 0): ?>
                    	                        	<div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box">
                    	                        	    <input type="text" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo stripslashes(esc_attr($each_source_value)); ?>">
                    	                        	</div>
												<?php else: ?>
													<div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box <?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-number">
                    	                        	    <input type="number" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[<?php echo esc_attr($source_id); ?>][]" value="<?php echo stripslashes(esc_attr($each_source_value)); ?>">
                    	                        	</div>
												<?php endif; ?>
                    	                    <?php endforeach; ?>
                    	                </div>
                    	            <?php endif; ?>
                    	        <?php endforeach; ?>
                    	    <?php else:?>
                    	        <div class = "<?php echo esc_attr($html_class_prefix) ?>chart-source-data-edit-block" data-source-id="1">
                    	            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box">
                    	                <input type="text" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[1][]" >
                    	            </div>
                    	            <div class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-box <?php echo esc_attr($html_class_prefix) ?>chart-source-data-input-number">
                    	                <input type="number" class="ays-text-input form-control" name="<?php echo esc_attr($html_name_prefix); ?>chart_source_data[1][]">
                    	            </div>
                    	            <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>icons-remove-box" >
										<svg class="<?php echo esc_attr($html_class_prefix) ?>chart-source-data-remove-block" data-trigger="hover" data-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
											<path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;"/>
										</svg>
                    	            </div>
                    	        </div>
                    	    <?php endif; ?>
                    	</div>
						<div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>add-new-column-box" style="display: none;">
							<img class="<?php echo esc_attr($html_class_prefix) ?>add-new-column" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/add-circle-outline.svg">
							Add column
						</div>
					</div>
                    <div class="<?php echo esc_attr($html_class_prefix) ?>icons-box <?php echo esc_attr($html_class_prefix) ?>add-new-row-box">
                        <img class="<?php echo esc_attr($html_class_prefix) ?>add-new-row" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/add-circle-outline.svg">
						Add row
                    </div>
                    <br>
                    <button class="<?php echo esc_attr($html_class_prefix) ?>show-on-chart-bttn button button-primary ays-button">Show on Chart</button>
                </div>
            </div>
        </div>
	    <?php
        $content = ob_get_clean();

	    $title = __( 'Manual data', "chart-builder" ) . ' <a class="ays_help" data-toggle="tooltip" title="' . __("Add the data manually. By clicking on the Add Row button you will be able to add as many rows as you need. While choosing the Line Chart type you will be able to also add the columns.","chart-builder") . '">
						<i class="ays_fa ays_fa_info_circle"></i>
					</a>';

	    $sources['manual'] = array(
		    'content' => $content,
		    'title' => $title
	    );

        return $sources;
    }

	/**
	 * Chart page settings contents
	 * @param $args
	 */
	public function ays_chart_page_settings_contents( $args ){

		$sources_contents = apply_filters( 'ays_cb_chart_page_settings_contents_settings', array(), $args );

		$sources = array();
		foreach ( $sources_contents as $key => $sources_content ) {
			$collapsed = $key == 'general_settings' ? 'false' : 'true';

			$content = '<fieldset class="ays-accordion-options-container" data-collapsed="' . $collapsed . '">';
			if(isset($sources_content['title'])){
				$content .= '<legend class="ays-accordion-options-header">';
				$content .= '<svg class="ays-accordion-arrow ays-accordion-arrow-right" version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" overflow="visible" preserveAspectRatio="none" viewBox="0 0 24 24" width="20" height="20">
                    <g>
                        <path xmlns:default="http://www.w3.org/2000/svg" d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z" style="fill: rgb(0, 140, 255);" vector-effect="non-scaling-stroke" />
                    </g>
                </svg>';

				$content .= '<span>'. esc_html($sources_content['title']) .'</span></legend>';
			}

			$content .= '<div class="ays-accordion-options-content">';
				$content .= $sources_content['content'];
			$content .= '</div>';

			$content .= '</fieldset>';

			$sources[] = $content;
		}
		$content_for_escape = implode('' , $sources );
		echo html_entity_decode(esc_html( $content_for_escape ));
	}

	public function settings_contents_general_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$status = $args['status'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-status" class="form-label">
                            <?php echo esc_html(__('Chart status', "chart-builder")); ?>
                            <a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("Decide whether the chart is active or not. If the chart is a draft, it won't be shown anywhere on your website (you don't need to remove shortcodes).","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-2 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
						<label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-status" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>status" value="published" <?php echo $status == 'published' ? 'checked' : ''; ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div> <!-- Status -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'General Settings', "chart-builder" );

		$sources['general_settings'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

	public function settings_contents_styles_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
		$width = $settings['width'];
		$height = $settings['height'];
		$font_size = $settings['font_size'];
		$title_color = $settings['title_color'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-width" class="form-label">
                            <?php echo esc_html(__( "Width", "chart-builder" )); ?>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-width" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[width]" value="<?php echo esc_attr($width) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box"><img src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL); ?>/images/icons/percent.svg"></div>
                    </div>
                </div> <!-- Width -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-height" class="form-label">
                            <?php echo esc_html(__( "Height", "chart-builder" )); ?>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-height" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[height]" value="<?php echo esc_attr($height) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Height -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-font-size" class="form-label">
                            <?php echo esc_html(__( "Font size", "chart-builder" )); ?>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>option-input">
                        <input class="ays-text-input form-control <?php echo esc_attr($html_class_prefix) ?>option-text-input" id="ays-chart-option-font-size" type="number" name="<?php echo esc_attr($html_name_prefix); ?>settings[font_size]" value="<?php echo esc_attr($font_size) ?>">
						<div class="<?php echo esc_attr($html_class_prefix) ?>option-desc-box">px</div>
                    </div>
                </div> <!-- Font size -->
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-title-color">
				            <?php echo esc_html(__( "Chart title color", "chart-builder" )); ?>
                        </label>
                    </div>
                    <div class="col-sm-7 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <input id="ays-chart-option-title-color" class="form-control-color" type="color" name="<?php echo esc_attr($html_name_prefix); ?>settings[title_color]" value="<?php echo esc_attr($title_color) ?>">
                    </div>
                </div> <!-- Chart title color -->
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Styles', "chart-builder" );

		$sources['styles'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

	public function settings_contents_tooltip_settings( $sources, $args ){
		$html_class_prefix = $args['html_class_prefix'];
		$html_name_prefix = $args['html_name_prefix'];
		$settings = $args['settings'];
		$width = $settings['width'];
		$tooltip_trigger_options = $settings['tooltip_trigger_options'];
		$tooltip_trigger = $settings['tooltip_trigger'];
		$show_color_code = $settings['show_color_code'];

		ob_start();
		?>
        <div class="ays-accordion-data-main-wrap">
            <div class="<?php echo esc_attr($html_class_prefix) ?>settings-data-main-wrap">
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-tooltip-trigger">
				            <?php echo esc_html(__( "Trigger", "chart-builder" )); ?>
							<a class="ays_help" data-toggle="tooltip" title="<?php echo htmlspecialchars( __("Choose when to display the results on the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7">
                        <select class="<?php echo esc_attr($html_class_prefix) ?>option-select-input form-select" id="ays-chart-option-tooltip-trigger" name="<?php echo esc_attr($html_name_prefix); ?>settings[tooltip_trigger]">
				            <?php
				            foreach ( $tooltip_trigger_options as $option_slug => $option ):
					            $selected = ( $tooltip_trigger == $option_slug ) ? 'selected' : '';
					            ?>
                                <option value="<?php echo esc_attr($option_slug); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($option); ?></option>
				            <?php
				            endforeach;
				            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row mb-2 <?php echo esc_attr($html_class_prefix) ?>options-section">
                    <div class="col-sm-5 d-flex align-items-center <?php echo esc_attr($html_class_prefix) ?>option-title">
                        <label for="ays-chart-option-show-color-code">
				            <?php echo esc_html(__( "Show Color Code", "chart-builder" )); ?>
							<a class="ays_help" data-toggle="tooltip" title="<?php echo esc_attr( __("The color will be displayed while clicking on a particular part of the chart.","chart-builder") ); ?>">
                                <i class="ays_fa ays_fa_info_circle"></i>
                            </a>
                        </label>
                    </div>
                    <div class="col-sm-7 py-2 <?php echo esc_attr($html_class_prefix) ?>input-align-right">
                        <label class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-switch">
                            <input class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch" id="ays-chart-option-show-color-code" type="checkbox" name="<?php echo esc_attr($html_name_prefix); ?>settings[show_color_code]" value="on" <?php echo esc_attr($show_color_code); ?> >
                            <span class="<?php echo esc_attr($html_class_prefix) ?>toggle-switch-slider <?php echo esc_attr($html_class_prefix) ?>toggle-switch-round"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
		<?php
		$content = ob_get_clean();

		$title = __( 'Tooltip', "chart-builder" );

		$sources['tooltip'] = array(
			'content' => $content,
			'title' => $title
		);

		return $sources;
	}

}
