<?php
/**
 * The file that defines the core plugin class
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/includes
 */

/**
 * The core plugin class.
 */
class TravelCurator {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        if (defined('TRAVELCURATOR_VERSION')) {
            $this->version = TRAVELCURATOR_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'travelcurator';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_post_types_hooks();
        $this->define_taxonomies_hooks();
        $this->define_meta_boxes_hooks();
        $this->define_leads_hooks();
        $this->define_notifications_hooks();
        $this->define_api_hooks();
        $this->define_elementor_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-loader.php';

        /**
         * The class responsible for defining internationalization functionality.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-travelcurator-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing side.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-travelcurator-public.php';

        /**
         * Custom post types handler
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-post-types.php';

        /**
         * Taxonomies handler
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-taxonomies.php';

        /**
         * Meta boxes handler
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-meta-boxes.php';

        /**
         * Leads handler
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-leads.php';

        /**
         * Notifications handler
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-notifications.php';

        /**
         * API handler
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-travelcurator-api.php';

        /**
         * Elementor integration
         */
        if (did_action('elementor/loaded')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/elementor/class-travelcurator-elementor.php';
        }

        $this->loader = new TravelCurator_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() {
        $plugin_i18n = new TravelCurator_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        $plugin_admin = new TravelCurator_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'settings_init');
        $this->loader->add_action('admin_notices', $plugin_admin, 'display_admin_notices');
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        $plugin_public = new TravelCurator_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('wp_footer', $plugin_public, 'add_whatsapp_button');
        $this->loader->add_filter('template_include', $plugin_public, 'template_include');
    }

    /**
     * Register all hooks related to custom post types.
     */
    private function define_post_types_hooks() {
        $post_types = new TravelCurator_Post_Types();
        $this->loader->add_action('init', $post_types, 'register_post_types');
        $this->loader->add_filter('post_updated_messages', $post_types, 'travel_package_updated_messages');
        $this->loader->add_filter('manage_travel_package_posts_columns', $post_types, 'add_travel_package_columns');
        $this->loader->add_action('manage_travel_package_posts_custom_column', $post_types, 'display_travel_package_columns', 10, 2);
        $this->loader->add_filter('manage_edit-travel_package_sortable_columns', $post_types, 'make_travel_package_columns_sortable');
        $this->loader->add_action('pre_get_posts', $post_types, 'handle_travel_package_sorting');
    }

    /**
     * Register all hooks related to taxonomies.
     */
    private function define_taxonomies_hooks() {
        $taxonomies = new TravelCurator_Taxonomies();
        $this->loader->add_action('init', $taxonomies, 'register_taxonomies');
        $this->loader->add_action('init', $taxonomies, 'create_default_terms');
        $this->loader->add_action('admin_init', $taxonomies, 'add_taxonomy_custom_fields');
    }

    /**
     * Register all hooks related to meta boxes.
     */
    private function define_meta_boxes_hooks() {
        $meta_boxes = new TravelCurator_Meta_Boxes();
        // Hooks are registered in the constructor
    }

    /**
     * Register all hooks related to leads.
     */
    private function define_leads_hooks() {
        $leads = new TravelCurator_Leads();
        // AJAX hooks are registered in the constructor
    }

    /**
     * Register all hooks related to notifications.
     */
    private function define_notifications_hooks() {
        $notifications = new TravelCurator_Notifications();
        // Hooks are registered in the constructor
    }

    /**
     * Register all hooks related to API.
     */
    private function define_api_hooks() {
        $api = new TravelCurator_API();
        // REST API hooks are registered in the constructor
    }

    /**
     * Register all hooks related to Elementor integration.
     */
    private function define_elementor_hooks() {
        if (did_action('elementor/loaded')) {
            $elementor = new TravelCurator_Elementor();
            $this->loader->add_action('elementor/widgets/register', $elementor, 'register_widgets');
            $this->loader->add_action('elementor/elements/categories_registered', $elementor, 'register_category');
            $this->loader->add_action('elementor/frontend/after_enqueue_styles', $elementor, 'enqueue_styles');
            $this->loader->add_action('elementor/preview/enqueue_styles', $elementor, 'enqueue_styles');
            $this->loader->add_action('elementor/frontend/after_enqueue_scripts', $elementor, 'enqueue_scripts');
            $this->loader->add_action('elementor/preview/enqueue_scripts', $elementor, 'enqueue_scripts');
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
