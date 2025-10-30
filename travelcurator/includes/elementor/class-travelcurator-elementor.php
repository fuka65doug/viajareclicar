<?php

/**
 * Elementor Integration for TravelCurator
 *
 * @package TravelCurator
 */

class TravelCurator_Elementor {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'register_category'));
    }

    /**
     * Check if Elementor is active
     */
    public function is_elementor_active() {
        return did_action('elementor/loaded');
    }

    /**
     * Register custom category
     */
    public function register_category($elements_manager) {
        $elements_manager->add_category(
            'travelcurator',
            array(
                'title' => __('TravelCurator', 'travelcurator'),
                'icon' => 'eicon-gallery-grid',
            )
        );
    }

    /**
     * Register widgets
     */
    public function register_widgets($widgets_manager) {
        if (!$this->is_elementor_active()) {
            return;
        }

        // Load widget base files
        $widgets_path = TRAVELCURATOR_PLUGIN_PATH . 'includes/elementor/';

        // Load main widgets class
        if (file_exists($widgets_path . 'class-travelcurator-elementor-widgets.php')) {
            require_once $widgets_path . 'class-travelcurator-elementor-widgets.php';
        }

        // Load individual widgets
        $widget_files = array(
            'widgets/travel-packages-grid.php',
            'widgets/travel-package-details.php', 
            'widgets/travel-search-filter.php',
            'widgets/travel-lead-form.php'
        );

        foreach ($widget_files as $widget_file) {
            $file_path = $widgets_path . $widget_file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }

        // Register widgets using new API
        if (class_exists('TravelCurator_Packages_Grid_Widget')) {
            $widgets_manager->register(new TravelCurator_Packages_Grid_Widget());
        }
        
        if (class_exists('TravelCurator_Package_Details_Widget')) {
            $widgets_manager->register(new TravelCurator_Package_Details_Widget());
        }
        
        if (class_exists('TravelCurator_Search_Filter_Widget')) {
            $widgets_manager->register(new TravelCurator_Search_Filter_Widget());
        }
        
        if (class_exists('TravelCurator_Lead_Form_Widget')) {
            $widgets_manager->register(new TravelCurator_Lead_Form_Widget());
        }
    }

    /**
     * Enqueue styles for Elementor frontend
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            'travelcurator-elementor-widgets',
            TRAVELCURATOR_PLUGIN_URL . 'includes/elementor/assets/widgets.css',
            array(),
            TRAVELCURATOR_VERSION
        );
    }
    /**
     * Enqueue scripts for Elementor frontend
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'travelcurator-elementor-widgets',
            TRAVELCURATOR_PLUGIN_URL . 'includes/elementor/assets/widgets.js',
            array('jquery'),
            TRAVELCURATOR_VERSION,
            true
        );
    }
}