<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 */
class TravelCurator_Admin {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/travelcurator-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/travelcurator-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name,
            'travelcurator_admin_ajax',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('travelcurator_admin_nonce')
            )
        );
    }

    public function add_plugin_admin_menu() {
		// Main menu page
		add_menu_page(
			__('TravelCurator', 'travelcurator'),
			__('TravelCurator', 'travelcurator'),
			'manage_options',
			'travelcurator',
			array($this, 'display_plugin_admin_dashboard'),
			'dashicons-palmtree',
			30
		);

		// Dashboard submenu
		add_submenu_page(
			'travelcurator',
			__('Dashboard', 'travelcurator'),
			__('Dashboard', 'travelcurator'),
			'manage_options',
			'travelcurator',
			array($this, 'display_plugin_admin_dashboard')
		);

		// Travel Packages submenu
		add_submenu_page(
			'travelcurator',
			__('Todos os Pacotes', 'travelcurator'),
			__('Todos os Pacotes', 'travelcurator'),
			'edit_posts',
			'edit.php?post_type=travel_package'
		);

		// Add New Package submenu
		add_submenu_page(
			'travelcurator',
			__('Adicionar Pacote', 'travelcurator'),
			__('Adicionar Pacote', 'travelcurator'),
			'edit_posts',
			'post-new.php?post_type=travel_package'
		);
		// Taxonomies submenus
		add_submenu_page(
			'travelcurator',
			__('Categorias', 'travelcurator'),
			__('Categorias', 'travelcurator'),
			'manage_categories',
			'edit-tags.php?taxonomy=travel_category&post_type=travel_package'
		);
		add_submenu_page(
			'travelcurator',
			__('Destinos', 'travelcurator'),
			__('Destinos', 'travelcurator'),
			'manage_categories',
			'edit-tags.php?taxonomy=travel_destination&post_type=travel_package'
		);
		add_submenu_page(
			'travelcurator',
			__('Propósitos', 'travelcurator'),
			__('Propósitos', 'travelcurator'),
			'manage_categories',
			'edit-tags.php?taxonomy=travel_purpose&post_type=travel_package'
		);
		add_submenu_page(
			'travelcurator',
			__('Comodidades', 'travelcurator'),
			__('Comodidades', 'travelcurator'),
			'manage_categories',
			'edit-tags.php?taxonomy=travel_facilities&post_type=travel_package'
		);
		// Plugin-specific pages - MANTER
		add_submenu_page(
			'travelcurator',
			__('Leads', 'travelcurator'),
			__('Leads', 'travelcurator'),
			'manage_options',
			'travelcurator-leads',
			array($this, 'display_leads_page')
		);

		add_submenu_page(
			'travelcurator',
			__('Configurações', 'travelcurator'),
			__('Configurações', 'travelcurator'),
			'manage_options',
			'travelcurator-settings',
			array($this, 'display_settings_page')
		);

		add_submenu_page(
			'travelcurator',
			__('Notificações', 'travelcurator'),
			__('Notificações', 'travelcurator'),
			'manage_options',
			'travelcurator-notifications',
			array($this, 'display_notifications_page')
		);
	}

    /**
     * Initialize settings
     */
    public function settings_init() {
        register_setting('travelcurator_settings', 'travelcurator_settings');
        
        // WhatsApp Settings Section
        add_settings_section(
            'travelcurator_whatsapp_section',
            __('Configurações do WhatsApp', 'travelcurator'),
            array($this, 'whatsapp_section_callback'),
            'travelcurator_settings'
        );

        add_settings_field(
            'whatsapp_number',
            __('Número do WhatsApp', 'travelcurator'),
            array($this, 'whatsapp_number_callback'),
            'travelcurator_settings',
            'travelcurator_whatsapp_section'
        );

        add_settings_field(
            'whatsapp_message',
            __('Mensagem Padrão', 'travelcurator'),
            array($this, 'whatsapp_message_callback'),
            'travelcurator_settings',
            'travelcurator_whatsapp_section'
        );

        // Email Settings Section
        add_settings_section(
            'travelcurator_email_section',
            __('Configurações de E-mail', 'travelcurator'),
            array($this, 'email_section_callback'),
            'travelcurator_settings'
        );

        add_settings_field(
            'admin_email',
            __('E-mail do Administrador', 'travelcurator'),
            array($this, 'admin_email_callback'),
            'travelcurator_settings',
            'travelcurator_email_section'
        );
    }

    /**
     * WhatsApp section callback
     */
    public function whatsapp_section_callback() {
        echo '<p>' . __('Configure as opções do botão WhatsApp flutuante.', 'travelcurator') . '</p>';
    }

    /**
     * WhatsApp number field callback
     */
    public function whatsapp_number_callback() {
        $number = get_option('travelcurator_whatsapp_number', '');
        echo '<input type="text" name="travelcurator_whatsapp_number" value="' . esc_attr($number) . '" placeholder="5511999999999" />';
        echo '<p class="description">' . __('Digite o número no formato internacional (ex: 5511999999999)', 'travelcurator') . '</p>';
    }

    /**
     * WhatsApp message field callback
     */
    public function whatsapp_message_callback() {
        $message = get_option('travelcurator_whatsapp_message', __('Olá! Gostaria de mais informações sobre os pacotes de viagem.', 'travelcurator'));
        echo '<textarea name="travelcurator_whatsapp_message" rows="3" cols="50">' . esc_textarea($message) . '</textarea>';
        echo '<p class="description">' . __('Mensagem padrão que será enviada pelo WhatsApp.', 'travelcurator') . '</p>';
    }

    /**
     * Email section callback
     */
    public function email_section_callback() {
        echo '<p>' . __('Configure as opções de e-mail para notificações.', 'travelcurator') . '</p>';
    }

    /**
     * Admin email field callback
     */
    public function admin_email_callback() {
        $email = get_option('travelcurator_admin_email', get_option('admin_email'));
        echo '<input type="email" name="travelcurator_admin_email" value="' . esc_attr($email) . '" />';
        echo '<p class="description">' . __('E-mail que receberá notificações de novos leads.', 'travelcurator') . '</p>';
    }

    /**
     * Display admin notices
     */
    public function display_admin_notices() {
        if (isset($_GET['page']) && strpos($_GET['page'], 'travelcurator') === 0) {
            if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p>' . __('Configurações salvas com sucesso!', 'travelcurator') . '</p>';
                echo '</div>';
            }
        }
    }

    /**
     * Display dashboard page
     */
    public function display_plugin_admin_dashboard() {
        $leads_stats = $this->get_statistics();
        include_once plugin_dir_path(__FILE__) . 'partials/travelcurator-admin-dashboard.php';
    }

    /**
     * Display leads page
     */
    public function display_leads_page() {
        $leads_data = $this->get_leads_data();
        include_once plugin_dir_path(__FILE__) . 'partials/travelcurator-admin-leads.php';
    }

    /**
     * Display settings page
     */
    public function display_settings_page() {
        $default_settings = $this->get_default_settings();
        include_once plugin_dir_path(__FILE__) . 'partials/travelcurator-admin-settings.php';
    }

    /**
     * Display notifications page
     */
    public function display_notifications_page() {
        $notifications_data = $this->get_notifications_data();
        include_once plugin_dir_path(__FILE__) . 'partials/travelcurator-admin-notifications.php';
    }

    /**
     * Get statistics for dashboard
     */
    public function get_statistics() {
        global $wpdb;
        
        $leads_table = $wpdb->prefix . 'travelcurator_leads';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$leads_table'") == $leads_table;
        
        if (!$table_exists) {
            return array(
                'total_leads' => 0,
                'new_leads' => 0,
                'converted_leads' => 0,
                'total_packages' => 0,
                'recent_leads' => array()
            );
        }

        // Get total leads
        $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table");
        
        // Get new leads (last 7 days)
        $new_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        
        // Get converted leads
        $converted_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE status = 'converted'");
        
        // Get total packages
        $total_packages = wp_count_posts('travel_package')->publish;
        
        // Get recent leads
        $recent_leads = $wpdb->get_results("SELECT * FROM $leads_table ORDER BY created_at DESC LIMIT 5");

        return array(
            'total_leads' => (int) $total_leads,
            'new_leads' => (int) $new_leads,
            'converted_leads' => (int) $converted_leads,
            'total_packages' => (int) $total_packages,
            'recent_leads' => $recent_leads
        );
    }

    /**
     * Get leads data
     */
    public function get_leads_data() {
        global $wpdb;
        
        $leads_table = $wpdb->prefix . 'travelcurator_leads';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$leads_table'") == $leads_table;
        
        if (!$table_exists) {
            return array(
                'leads' => array(),
                'total' => 0,
                'statuses' => array()
            );
        }

        // Get all leads
        $leads = $wpdb->get_results("SELECT * FROM $leads_table ORDER BY created_at DESC");
        
        // Get total count
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table");
        
        // Get status counts
        $statuses = $wpdb->get_results("SELECT status, COUNT(*) as count FROM $leads_table GROUP BY status");

        return array(
            'leads' => $leads,
            'total' => (int) $total,
            'statuses' => $statuses
        );
    }

    /**
     * Get default settings
     */
    public function get_default_settings() {
        return array(
            'whatsapp_number' => get_option('travelcurator_whatsapp_number', ''),
            'whatsapp_message' => get_option('travelcurator_whatsapp_message', __('Olá! Gostaria de mais informações sobre os pacotes de viagem.', 'travelcurator')),
            'admin_email' => get_option('travelcurator_admin_email', get_option('admin_email')),
            'currency_symbol' => get_option('travelcurator_currency_symbol', 'R$'),
            'date_format' => get_option('travelcurator_date_format', 'd/m/Y'),
            'items_per_page' => get_option('travelcurator_items_per_page', 10)
        );
    }

    /**
     * Get notifications data
     */
    public function get_notifications_data() {
        // Get recent notifications/activities
        $activities = array();
        
        // Get recent leads as notifications
        global $wpdb;
        $leads_table = $wpdb->prefix . 'travelcurator_leads';
        
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$leads_table'") == $leads_table;
        
        if ($table_exists) {
            $recent_leads = $wpdb->get_results("SELECT * FROM $leads_table ORDER BY created_at DESC LIMIT 10");
            
            foreach ($recent_leads as $lead) {
                $activities[] = array(
                    'type' => 'lead',
                    'title' => sprintf(__('Novo lead: %s', 'travelcurator'), $lead->name),
                    'description' => sprintf(__('E-mail: %s | Telefone: %s', 'travelcurator'), $lead->email, $lead->phone),
                    'date' => $lead->created_at,
                    'status' => $lead->status
                );
            }
        }
        
        // Get recent packages
        $recent_packages = get_posts(array(
            'post_type' => 'travel_package',
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        foreach ($recent_packages as $package) {
            $activities[] = array(
                'type' => 'package',
                'title' => sprintf(__('Novo pacote: %s', 'travelcurator'), $package->post_title),
                'description' => sprintf(__('Publicado em %s', 'travelcurator'), get_the_date('d/m/Y', $package->ID)),
                'date' => $package->post_date,
                'status' => $package->post_status
            );
        }
        
        // Sort activities by date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array(
            'activities' => array_slice($activities, 0, 20),
            'total_activities' => count($activities)
        );
    }

    /**
     * Handle settings form submission
     */
    public function handle_settings_form() {
        if (isset($_POST['submit']) && isset($_POST['_wpnonce'])) {
            if (wp_verify_nonce($_POST['_wpnonce'], 'travelcurator_settings')) {
                // Save WhatsApp settings
                if (isset($_POST['travelcurator_whatsapp_number'])) {
                    update_option('travelcurator_whatsapp_number', sanitize_text_field($_POST['travelcurator_whatsapp_number']));
                }
                
                if (isset($_POST['travelcurator_whatsapp_message'])) {
                    update_option('travelcurator_whatsapp_message', sanitize_textarea_field($_POST['travelcurator_whatsapp_message']));
                }
                
                // Save email settings
                if (isset($_POST['travelcurator_admin_email'])) {
                    update_option('travelcurator_admin_email', sanitize_email($_POST['travelcurator_admin_email']));
                }
                
                wp_redirect(admin_url('admin.php?page=travelcurator-settings&settings-updated=1'));
                exit;
            }
        }
    }
}
