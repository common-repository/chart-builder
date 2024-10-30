<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/public
 * @author     Chart Builder Team <info@ays-pro.com>
 */
class Chart_Builder_Public {

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
	 * @var string
	 */
	private $html_class_prefix = 'ays-chart-';

	/**
	 * @var string
	 */
	private $html_name_prefix = 'ays_chart_';

	/**
	 * @var string
	 */
	private $name_prefix = 'chart_';

	/**
	 * @var
	 */
	private $unique_id;

	/**
	 * @var Chart_Builder_DB_Actions
	 */
	private $db_object;

	/**
	 * @var array
	 */
	private $data;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->db_object =  new Chart_Builder_DB_Actions( $this->plugin_name );

		add_shortcode( 'ays_chart', array( $this, 'ays_generate_chart_method' ) );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chart_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chart_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/chart-builder-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Chart_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Chart_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . '-plugin', plugin_dir_url( __FILE__ ) . 'js/chart-builder-public-plugin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/chart-builder-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-datatable-min', plugin_dir_url( __FILE__ ) . '/js/chart-builder-datatable.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script( $this->plugin_name . "-db4.min.js", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name . '-charts-google', plugin_dir_url(__FILE__) . 'js/google-chart.js', array('jquery'), $this->version, true);


	}

	public function ays_generate_chart_method( $attr ) {
		$id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;

		if (is_null($id)) {
			return "<p class='wrong_shortcode_text' style='color:red;'>" . __( 'Wrong shortcode initialized', "chart-builder" ) . "</p>";
		}

		$content = $this->show_chart( $id, $attr );

		$this->enqueue_styles();
		$this->enqueue_scripts();

		return str_replace( array( "\r\n", "\n", "\r" ), '', $content );
	}

	public function show_chart( $id, $attr ) {

		$chartData = CBActions()->get_chart_data( $id );
		$chart = $chartData['chart'];
		$settings = $chartData['settings'];

		$unique_id = uniqid();
		$this->unique_id = $unique_id;

		$data = array();

		if ( is_null( $chart ) ) {
			return "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', "chart-builder") . "</p>";
		}

		$status = isset( $chart['status'] ) && $chart['status'] != '' ? $chart['status'] : '';

		if ( $status != 'published' ) {
			return "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', "chart-builder") . "</p>";
		}

		$chart_title = (isset($chart['title']) && $chart['title'] != '') ? stripslashes ( sanitize_text_field( $chart['title'] ) ) : '';
		$chart_description = (isset($chart['description']) && $chart['description'] != '') ? stripslashes ( sanitize_text_field( $chart['description'] ) ) : '';

		$data['chart_type'] = $chartData['source_chart_type'];
		$data['source'] = $chartData['source'];


		$settings['colors'] = ['#3366cc', '#dc3912', '#ff9900', '#109618', '#990099', '#0099c6', '#dd4477', '#66aa00', '#b82e2e', '#316395'];

		$data['options'] = $settings;

		$content = array();

		$content[] = "<div class='" . $this->html_class_prefix . "container' id='" . $this->html_class_prefix . "container" . $unique_id . "' data-id='" . $unique_id . "'>";

		$content[] = "<div class='" . $this->html_class_prefix . "header-container'>";
		$content[] = "<p class=" . $this->html_class_prefix . "charts-title>";
		$content[] = $chart_title;
		$content[] = "</p>";

		$content[] = "<p class=" . $this->html_class_prefix . "charts-description>";
		$content[] = $chart_description;
		$content[] = "</p>";
		$content[] = "</div>";

		$content[] = "<div class=" . $this->html_class_prefix . "charts-main-container id=" . $this->html_class_prefix . $chartData['source_chart_type'] . "></div>";

		$content[] = "</div>";

		$content[] = "<style>";

		$content[] = "." . $this->html_class_prefix . "charts-main-container {
							width: " . $settings['width'] . "%;
							height: " . $settings['height'] . "px;
						}";

		$content[] = "." . $this->html_class_prefix . "charts-title {
							color: " . $settings['title_color'] . ";
						}";

		$content[] = "</style>";

		$this->data = $data;

		$content[] = $this->get_encoded_options();

		return implode( '', $content );
	}

	public function get_encoded_options () {
		$content = array();
		$data = $this->data;

		$content[] = '<script type="text/javascript">';

		$content[] = "
                if(typeof aysChartOptions === 'undefined'){
                    var aysChartOptions = [];
                }
                aysChartOptions['" . $this->unique_id . "']  = '" . base64_encode( json_encode( $data ) ) . "';";

		$content[] = '</script>';

		return implode( '', $content );
	}


}
