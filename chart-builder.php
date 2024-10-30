<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ays-pro.com/
 * @since             1.0.0
 * @package           Chart_Builder
 *
 * @wordpress-plugin
 * Plugin Name:       Chart Builder
 * Plugin URI:        https://ays-pro.com/wordpress/chart-builder
 * Description:       Display the results with various charts and compare the data.
 * Version:           1.0.0
 * Author:            Chart Builder Team
 * Author URI:        https://ays-pro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       chart-builder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CHART_BUILDER_VERSION', '1.0.0' );
define( 'CHART_BUILDER_NAME_VERSION', '1.0.0' );
define( 'CHART_BUILDER_NAME', 'chart-builder' );
define( 'CHART_BUILDER_DB_PREFIX', 'ayschart_' );

if( ! defined( 'CHART_BUILDER_BASENAME' ) )
    define( 'CHART_BUILDER_BASENAME', plugin_basename( __FILE__ ) );

if( ! defined( 'CHART_BUILDER_DIR' ) )
    define( 'CHART_BUILDER_DIR', plugin_dir_path( __FILE__ ) );

if( ! defined( 'CHART_BUILDER_BASE_URL' ) )
    define( 'CHART_BUILDER_BASE_URL', plugin_dir_url(__FILE__ ) );

if( ! defined( 'CHART_BUILDER_ADMIN_PATH' ) )
    define( 'CHART_BUILDER_ADMIN_PATH', plugin_dir_path( __FILE__ ) . 'admin' );

if( ! defined( 'CHART_BUILDER_ADMIN_URL' ) )
    define( 'CHART_BUILDER_ADMIN_URL', plugin_dir_url( __FILE__ ) . 'admin' );

if( ! defined( 'CHART_BUILDER_PUBLIC_PATH' ) )
    define( 'CHART_BUILDER_PUBLIC_PATH', plugin_dir_path( __FILE__ ) . 'public' );

if( ! defined( 'CHART_BUILDER_PUBLIC_URL' ) )
    define( 'CHART_BUILDER_PUBLIC_URL', plugin_dir_url( __FILE__ ) . 'public' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-chart-builder-activator.php
 */
function activate_chart_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chart-builder-activator.php';
	Chart_Builder_Activator::db_update_check();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-chart-builder-deactivator.php
 */
function deactivate_chart_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-chart-builder-deactivator.php';
	Chart_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_chart_builder' );
register_deactivation_hook( __FILE__, 'deactivate_chart_builder' );

add_action( 'plugins_loaded', 'activate_chart_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-chart-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_chart_builder() {

    add_action( 'admin_notices', 'chart_builder_general_admin_notice' );
	$plugin = new Chart_Builder();
	$plugin->run();

}

if( !function_exists( 'chart_builder_general_admin_notice' ) ){
    function chart_builder_general_admin_notice(){
        global $wpdb;
        if ( isset($_GET['page']) && strpos($_GET['page'], CHART_BUILDER_NAME) !== false ) {
            ?>
             <div class="ays-notice-banner">
                <div class="navigation-bar">
                    <div id="navigation-container">
                         <a class="logo-container" href="https://ays-pro.com/wordpress/chart-builder" target="_blank">
                            <img class="logo" src="<?php echo esc_url(CHART_BUILDER_ADMIN_URL) . '/images/ays_chart_logo.png'; ?>" alt="AYS Pro logo" title="AYS Pro logo"/>
                        </a>
                        <ul id="menu">
                            <!-- <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://ays-pro.com/wordpress-chart-builder-user-manual" target="_blank">DOCUMENTATION</a></li> -->
                            <!-- <li class="modile-ddmenu-xs"><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/reviews/" target="_blank">RATE US</a></li> -->
                            <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://ays-demo.com/chart-builder-demo/" target="_blank">DEMO</a></li>
                            <li class="modile-ddmenu-lg take_survay"><a class="ays-btn" href=" https://ays-demo.com/chart-builder-plugin-suggestion-box/" target="_blank">MAKE A SUGGESTION</a></li>
                            <!-- <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/" target="_blank">SUPPORT FORUM</a></li> -->
                            <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://ays-pro.com/plugin-customization/" target="_blank">CUSTOMIZE</a></li>
                            <!-- <li class="modile-ddmenu-lg"><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/" target="_blank">CONTACT US</a></li> -->
                            <li class="modile-ddmenu-md">
                                <a class="toggle_ddmenu" href="javascript:void(0);"><i class="ays_fa ays_fa_ellipsis_h"></i></a>
                                <ul class="ddmenu" data-expanded="false">
                                    <!-- <li><a class="ays-btn" href="https://ays-pro.com/wordpress-chart-builder-user-manual" target="_blank">DOCUMENTATION</a></li> -->
                                    <!-- <li><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/reviews/" target="_blank">RATE US</a></li> -->
                                    <li><a class="ays-btn" href="https://ays-demo.com/chart-builder-demo/" target="_blank">DEMO</a></li>
                                    <li class="take_survay"><a class="ays-btn" href=" https://ays-demo.com/chart-builder-plugin-suggestion-box/" target="_blank">MAKE A SUGGESTION</a></li>
                                    <!-- <li><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/" target="_blank">SUPPORT FORUM</a></li> -->
                                    <li class=""><a class="ays-btn" href="https://ays-pro.com/plugin-customization/" target="_blank">CUSTOMIZATION</a></li>
                                    <!-- <li><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/" target="_blank">CONTACT US</a></li> -->
                                </ul>
                            </li>
                            <li class="modile-ddmenu-sm">
                                <a class="toggle_ddmenu" href="javascript:void(0);"><i class="ays_fa ays_fa_ellipsis_h"></i></a>
                                <ul class="ddmenu" data-expanded="false">
                                    <!-- <li><a class="ays-btn" href="https://ays-pro.com/wordpress-chart-builder-user-manual" target="_blank">DOCUMENTATION</a></li> -->
                                    <!-- <li><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/reviews/" target="_blank">RATE US</a></li> -->
                                    <li><a class="ays-btn" href="https://ays-demo.com/chart-builder-demo/" target="_blank">DEMO</a></li>
                                    <li class="take_survay"><a class="ays-btn" href=" https://ays-demo.com/chart-builder-plugin-suggestion-box/" target="_blank">MAKE A SUGGESTION</a></li>
                                    <!-- <li><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/" target="_blank">SUPPORT FORUM</a></li> -->
                                    <li class=""><a class="ays-btn" href="https://ays-pro.com/plugin-customization/" target="_blank">CUSTOMIZATION</a></li>
                                    <!-- <li><a class="ays-btn" href="https://wordpress.org/support/plugin/chart-builder/" target="_blank">CONTACT US</a></li> -->
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
             </div>
         <?php
        }
    }
}

run_chart_builder();
