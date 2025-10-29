<?php
/**
 * Elementor Widgets Manager
 *
 * @package TravelCurator
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TravelCurator_Elementor_Widgets {

    public function __construct() {
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_elementor_widget_categories'));
        add_action('elementor/frontend/after_enqueue_styles', array($this, 'widget_styles'));
        add_action('elementor/frontend/after_register_scripts', array($this, 'widget_scripts'));
    }

    /**
     * Register widgets
     */
    public function register_widgets() {
        require_once TRAVELCURATOR_PLUGIN_PATH . 'includes/elementor/widgets/travel-packages-grid.php';
        require_once TRAVELCURATOR_PLUGIN_PATH . 'includes/elementor/widgets/travel-package-details.php';
        require_once TRAVELCURATOR_PLUGIN_PATH . 'includes/elementor/widgets/travel-search-filter.php';
        require_once TRAVELCURATOR_PLUGIN_PATH . 'includes/elementor/widgets/travel-lead-form.php';

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \TravelCurator_Packages_Grid_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \TravelCurator_Package_Details_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \TravelCurator_Search_Filter_Widget());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \TravelCurator_Lead_Form_Widget());
    }

    /**
     * Add widget categories
     */
    public function add_elementor_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'travelcurator',
            array(
                'title' => 'TravelCurator',
                'icon' => 'fa fa-plane',
            )
        );
    }

    /**
     * Widget styles
     */
    public function widget_styles() {
        wp_enqueue_style(
            'travelcurator-elementor-widgets',
            TRAVELCURATOR_PLUGIN_URL . 'includes/elementor/assets/widgets.css',
            array(),
            TRAVELCURATOR_VERSION
        );
    }

    /**
     * Widget scripts
     */
    public function widget_scripts() {
        wp_register_script(
            'travelcurator-elementor-widgets',
            TRAVELCURATOR_PLUGIN_URL . 'includes/elementor/assets/widgets.js',
            array('jquery'),
            TRAVELCURATOR_VERSION,
            true
        );

        wp_localize_script('travelcurator-elementor-widgets', 'travelcurator_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('travelcurator_nonce')
        ));
    }
}

// Initialize Elementor Widgets
new TravelCurator_Elementor_Widgets();