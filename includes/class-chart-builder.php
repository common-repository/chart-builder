<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Chart_Builder
 * @subpackage Chart_Builder/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Chart_Builder
 * @subpackage Chart_Builder/includes
 * @author     Chart Builder Team <info@ays-pro.com>
 */
class Chart_Builder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Chart_Builder_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CHART_BUILDER_VERSION' ) ) {
			$this->version = CHART_BUILDER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = "chart-builder";

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Chart_Builder_Loader. Orchestrates the hooks of the plugin.
	 * - Chart_Builder_i18n. Defines internationalization functionality.
	 * - Chart_Builder_Admin. Defines all hooks for the admin area.
	 * - Chart_Builder_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

        if ( ! class_exists( 'WP_List_Table' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }

		/**
		 * The classes responsible for defining all actions including db that occur in the plugin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chart-builder-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chart-builder-db-actions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chart-builder-actions.php';

		/**
		 * The class responsible for defining all functions for getting all form integrations
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chart-builder-integrations.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chart-builder-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-chart-builder-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-chart-builder-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-chart-builder-public.php';

		$this->loader = new Chart_Builder_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Chart_Builder_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Chart_Builder_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Chart_Builder_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );


        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_general_settings_submenu', 120 );


        // Add Chart page sources contents
		$this->loader->add_action( 'ays_cb_chart_page_sources_contents', $plugin_admin, 'ays_chart_page_source_contents', 10, 3 );
		// $this->loader->add_filter( 'ays_cb_chart_page_sources_contents_settings', $plugin_admin, 'source_contents_import_from_wordpress_settings', 50, 2 );
		$this->loader->add_filter( 'ays_cb_chart_page_sources_contents_settings', $plugin_admin, 'source_contents_manual_settings', 70, 2 );

		// Add Chart page settings contents
		$this->loader->add_action( 'ays_cb_chart_page_settings_contents', $plugin_admin, 'ays_chart_page_settings_contents', 10, 3 );
		$this->loader->add_filter( 'ays_cb_chart_page_settings_contents_settings', $plugin_admin, 'settings_contents_general_settings', 50, 2 );
		$this->loader->add_filter( 'ays_cb_chart_page_settings_contents_settings', $plugin_admin, 'settings_contents_styles_settings', 60, 2 );
		$this->loader->add_filter( 'ays_cb_chart_page_settings_contents_settings', $plugin_admin, 'settings_contents_tooltip_settings', 70, 2 );


        // Add Settings link to the plugin
        $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
        $this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );


        // Admin AJAX action
        $this->loader->add_action( 'wp_ajax_ays_chart_admin_ajax', $plugin_admin, 'ays_admin_ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_chart_admin_ajax', $plugin_admin, 'ays_admin_ajax' );
		$this->loader->add_action( 'wp_ajax_deactivate_plugin_option_cb', $plugin_admin, 'deactivate_plugin_option');
        $this->loader->add_action( 'wp_ajax_nopriv_deactivate_plugin_option_cb', $plugin_admin, 'deactivate_plugin_option');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Chart_Builder_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}
    
	/**
	 * Register all of the hooks related to the integrations functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Chart_Builder_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
