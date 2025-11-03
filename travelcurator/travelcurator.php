<?php
/**
 * Plugin Name: TravelCurator
 * Plugin URI: https://viajareclicar.com.br
 * Description: Plugin completo para agência de viagens Viajar & Clicar - Curadora emocional de experiências de viagem.
 * Version: 1.0.2
 * Author: Viajar & Clicar
 * Text Domain: travelcurator
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * Network: false
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('TRAVELCURATOR_VERSION', '1.0.2');
define('TRAVELCURATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TRAVELCURATOR_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TRAVELCURATOR_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('TRAVELCURATOR_TEXT_DOMAIN', 'travelcurator');

/**
 * The code that runs during plugin activation.
 */
function travelcurator_activate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-travelcurator-activator.php';
    TravelCurator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function travelcurator_deactivate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-travelcurator-deactivator.php';
    TravelCurator_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'travelcurator_activate');
register_deactivation_hook(__FILE__, 'travelcurator_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-travelcurator.php';

/**
 * Begins execution of the plugin.
 */
function travelcurator_run() {
    $plugin = new TravelCurator();
    $plugin->run();
}

// Initialize the plugin
travelcurator_run();

/**
 * Add action links to plugin page
 */
function travelcurator_action_links($links) {
    $settings_link = '<a href="admin.php?page=travelcurator">' . __('Configurações', 'travelcurator') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . TRAVELCURATOR_PLUGIN_BASENAME, 'travelcurator_action_links');

/**
 * Check if Elementor is active
 */
function travelcurator_check_elementor() {
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>' . __('TravelCurator recomenda o Elementor para funcionalidades completas.', 'travelcurator') . '</p>';
            echo '</div>';
        });
    }
}
add_action('plugins_loaded', 'travelcurator_check_elementor');

/**
 * Load plugin textdomain
 */
function travelcurator_load_textdomain() {
    load_plugin_textdomain('travelcurator', false, dirname(TRAVELCURATOR_PLUGIN_BASENAME) . '/languages/');
}
add_action('plugins_loaded', 'travelcurator_load_textdomain');
