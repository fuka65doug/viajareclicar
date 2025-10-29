<?php
/**
 * Fired during plugin activation
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/includes
 */

/**
 * Fired during plugin activation.
 */
class TravelCurator_Activator {

    /**
     * Activate the plugin.
     */
    public static function activate() {
        // Create database tables
        self::create_tables();
        
        // Register post types and taxonomies temporarily for rewrite rules
        self::register_post_types();
        self::register_taxonomies();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Create default terms
        self::create_default_terms();
        
        // Set default options
        self::set_default_options();
        
        // Create upload directories
        self::create_upload_directories();
        
        // Set activation flag
        update_option('travelcurator_activated', true);
    }

    /**
     * Create plugin database tables
     */
    private static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Table for interests/leads
        $table_interests = $wpdb->prefix . 'travelcurator_interests';
        $sql_interests = "CREATE TABLE $table_interests (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            package_id bigint(20) NOT NULL,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) DEFAULT '',
            message text DEFAULT '',
            source varchar(50) DEFAULT 'form',
            user_agent text DEFAULT '',
            ip_address varchar(45) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'new',
            assigned_to bigint(20) DEFAULT NULL,
            notes text DEFAULT '',
            PRIMARY KEY (id),
            KEY package_id (package_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        // Table for notifications
        $table_notifications = $wpdb->prefix . 'travelcurator_notifications';
        $sql_notifications = "CREATE TABLE $table_notifications (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            type varchar(50) NOT NULL,
            title varchar(255) NOT NULL,
            message text NOT NULL,
            data longtext DEFAULT '',
            read_status tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY type (type),
            KEY read_status (read_status)
        ) $charset_collate;";

        // Table for notification preferences
        $table_preferences = $wpdb->prefix . 'travelcurator_notification_preferences';
        $sql_preferences = "CREATE TABLE $table_preferences (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            notification_type varchar(50) NOT NULL,
            email_enabled tinyint(1) DEFAULT 1,
            dashboard_enabled tinyint(1) DEFAULT 1,
            frequency varchar(20) DEFAULT 'immediate',
            filters longtext DEFAULT '',
            PRIMARY KEY (id),
            UNIQUE KEY user_type (user_id, notification_type)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_interests);
        dbDelta($sql_notifications);
        dbDelta($sql_preferences);
    }

    /**
     * Register post types temporarily for activation
     */
    private static function register_post_types() {
        register_post_type('travel_package', array(
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'pacotes'),
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields')
        ));
    }

    /**
     * Register taxonomies temporarily for activation
     */
    private static function register_taxonomies() {
        register_taxonomy('travel_category', 'travel_package', array(
            'hierarchical' => true,
            'public' => true,
            'rewrite' => array('slug' => 'categoria-viagem')
        ));
        
        register_taxonomy('emotional_purpose', 'travel_package', array(
            'hierarchical' => false,
            'public' => true,
            'rewrite' => array('slug' => 'proposito')
        ));
        
        register_taxonomy('travel_destination', 'travel_package', array(
            'hierarchical' => true,
            'public' => true,
            'rewrite' => array('slug' => 'destino')
        ));
        
        register_taxonomy('travel_facilities', 'travel_package', array(
            'hierarchical' => false,
            'public' => true
        ));
    }

    /**
     * Create default taxonomy terms
     */
    private static function create_default_terms() {
        // Travel categories
        $categories = array(
            'Individual', 'Casal', 'Família', 'Grupo', 
            'Empresarial', 'Lua de Mel', 'Aventura'
        );
        
        foreach ($categories as $category) {
            if (!term_exists($category, 'travel_category')) {
                wp_insert_term($category, 'travel_category');
            }
        }

        // Emotional purposes
        $purposes = array(
            'Reconexão' => 'Para redescobrir vínculos importantes',
            'Celebração' => 'Para marcar momentos especiais',
            'Descoberta' => 'Para expandir horizontes',
            'Transformação' => 'Para crescimento pessoal',
            'Descanso' => 'Para renovar energias'
        );
        
        foreach ($purposes as $purpose => $description) {
            if (!term_exists($purpose, 'emotional_purpose')) {
                wp_insert_term($purpose, 'emotional_purpose', array(
                    'description' => $description
                ));
            }
        }

        // Travel facilities
        $facilities = array(
            'Wi-Fi Gratuito', 'Piscina', 'Spa', 'Academia', 'Restaurante',
            'Bar', 'Room Service', 'Ar Condicionado', 'TV a Cabo', 'Cofre',
            'Frigobar', 'Varanda', 'Vista para o Mar', 'Transfer Incluído',
            'Guia Local', 'Seguro Viagem'
        );
        
        foreach ($facilities as $facility) {
            if (!term_exists($facility, 'travel_facilities')) {
                wp_insert_term($facility, 'travel_facilities');
            }
        }
        
        // Mark as created
        update_option('travelcurator_default_terms_created', true);
    }

    /**
     * Set default plugin options
     */
    private static function set_default_options() {
        $default_options = array(
            'travelcurator_admin_email' => get_option('admin_email'),
            'travelcurator_currency_symbol' => 'R$',
            'travelcurator_posts_per_page' => 12,
            'travelcurator_show_prices' => 1,
            'travelcurator_enable_reviews' => 1,
            'travelcurator_whatsapp_number' => '',
            'travelcurator_whatsapp_message' => 'Olá! Tenho interesse no pacote {package_title}. Pode me dar mais informações?',
            'travelcurator_enable_notifications' => 1,
            'travelcurator_version' => TRAVELCURATOR_VERSION,
        );

        foreach ($default_options as $option_name => $option_value) {
            if (!get_option($option_name)) {
                add_option($option_name, $option_value);
            }
        }
    }

    /**
     * Create upload directories
     */
    private static function create_upload_directories() {
        $upload_dir = wp_upload_dir();
        $travelcurator_dir = $upload_dir['basedir'] . '/travelcurator';
        
        if (!file_exists($travelcurator_dir)) {
            wp_mkdir_p($travelcurator_dir);
        }
        
        // Create subdirectories
        $subdirs = array('packages', 'galleries', 'temp');
        foreach ($subdirs as $subdir) {
            $dir_path = $travelcurator_dir . '/' . $subdir;
            if (!file_exists($dir_path)) {
                wp_mkdir_p($dir_path);
            }
        }

        // Create .htaccess for security
        $htaccess_content = "Options -Indexes\n<Files *.php>\nDeny from all\n</Files>";
        file_put_contents($travelcurator_dir . '/.htaccess', $htaccess_content);
    }
}
