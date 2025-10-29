<?php

/**
 * The leads functionality of the plugin.
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/includes
 */

/**
 * The leads functionality of the plugin.
 */
class TravelCurator_Leads {

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        add_action('wp_ajax_travelcurator_submit_lead', array($this, 'handle_lead_submission'));
        add_action('wp_ajax_nopriv_travelcurator_submit_lead', array($this, 'handle_lead_submission'));
        add_action('wp_ajax_travelcurator_get_leads', array($this, 'get_leads_ajax'));
        add_action('wp_ajax_travelcurator_update_lead_status', array($this, 'update_lead_status_ajax'));
        add_action('wp_ajax_travelcurator_delete_lead', array($this, 'delete_lead_ajax'));
        add_action('wp_ajax_travelcurator_export_leads', array($this, 'export_leads_ajax'));
        add_action('init', array($this, 'create_leads_table'));
    }

    /**
     * Create leads table on plugin activation
     */
    public function create_leads_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            message text,
            package_id mediumint(9),
            status varchar(20) DEFAULT 'new',
            source varchar(50) DEFAULT 'website',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY package_id (package_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Handle AJAX lead submission
     */
    public function handle_lead_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'travelcurator_nonce')) {
            wp_die(__('Erro de segurança. Tente novamente.', 'travelcurator'));
        }

        // Sanitize input data
        $name = sanitize_text_field($_POST['lead_name']);
        $email = sanitize_email($_POST['lead_email']);
        $phone = sanitize_text_field($_POST['lead_phone']);
        $message = sanitize_textarea_field($_POST['lead_message']);
        $package_id = intval($_POST['package_id']);

        // Validate required fields
        if (empty($name) || empty($email) || empty($phone)) {
            wp_send_json_error(array(
                'message' => __('Por favor, preencha todos os campos obrigatórios.', 'travelcurator')
            ));
        }

        // Validate email
        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => __('Por favor, insira um email válido.', 'travelcurator')
            ));
        }

        // Insert lead into database
        $lead_id = $this->insert_lead($name, $email, $phone, $message, $package_id);

        if ($lead_id) {
            // Send notification emails
            $this->send_lead_notifications($lead_id);

            // Send success response
            wp_send_json_success(array(
                'message' => __('Sua solicitação foi enviada com sucesso! Entraremos em contato em breve.', 'travelcurator'),
                'lead_id' => $lead_id
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erro ao processar sua solicitação. Tente novamente.', 'travelcurator')
            ));
        }
    }

    /**
     * Insert lead into database
     */
    private function insert_lead($name, $email, $phone, $message, $package_id = 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';

        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'message' => $message,
                'package_id' => $package_id,
                'status' => 'new',
                'source' => 'website'
            ),
            array(
                '%s', '%s', '%s', '%s', '%d', '%s', '%s'
            )
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Send lead notification emails
     */
    private function send_lead_notifications($lead_id) {
        $lead = $this->get_lead($lead_id);
        
        if (!$lead) {
            return;
        }

        // Get admin email
        $admin_email = get_option('travelcurator_admin_email', get_option('admin_email'));
        
        // Get package info if exists
        $package_info = '';
        if ($lead->package_id) {
            $package = get_post($lead->package_id);
            if ($package) {
                $package_info = sprintf(
                    __('Pacote de interesse: %s (%s)', 'travelcurator'),
                    $package->post_title,
                    get_permalink($package->ID)
                );
            }
        }

        // Prepare admin notification email
        $admin_subject = sprintf(__('[%s] Novo Lead Recebido', 'travelcurator'), get_bloginfo('name'));
        $admin_message = sprintf(
            __("Olá!\n\nVocê recebeu um novo lead através do site:\n\nNome: %s\nEmail: %s\nTelefone: %s\nMensagem: %s\n%s\n\nData: %s\n\nAcesse o painel administrativo para mais detalhes.", 'travelcurator'),
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->message,
            $package_info,
            date_i18n('d/m/Y H:i:s', strtotime($lead->created_at))
        );

        // Send admin notification
        wp_mail($admin_email, $admin_subject, $admin_message);

        // Prepare customer confirmation email
        $customer_subject = sprintf(__('Confirmação de contato - %s', 'travelcurator'), get_bloginfo('name'));
        $customer_message = sprintf(
            __("Olá %s!\n\nRecebemos seu contato e agradecemos o interesse em nossos serviços.\n\n%s\n\nEm breve entraremos em contato para fornecer mais informações.\n\nAtenciosamente,\nEquipe %s", 'travelcurator'),
            $lead->name,
            $package_info,
            get_bloginfo('name')
        );

        // Send customer confirmation
        wp_mail($lead->email, $customer_subject, $customer_message);
    }

    /**
     * Get lead by ID
     */
    public function get_lead($lead_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $lead_id
        ));
    }

    /**
     * Get leads with filters
     */
    public function get_leads($args = array()) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';

        $defaults = array(
            'status' => '',
            'package_id' => 0,
            'date_from' => '',
            'date_to' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $where_conditions = array('1=1');
        $where_values = array();

        // Filter by status
        if (!empty($args['status'])) {
            $where_conditions[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        // Filter by package
        if (!empty($args['package_id'])) {
            $where_conditions[] = 'package_id = %d';
            $where_values[] = $args['package_id'];
        }

        // Filter by date range
        if (!empty($args['date_from'])) {
            $where_conditions[] = 'created_at >= %s';
            $where_values[] = $args['date_from'] . ' 00:00:00';
        }

        if (!empty($args['date_to'])) {
            $where_conditions[] = 'created_at <= %s';
            $where_values[] = $args['date_to'] . ' 23:59:59';
        }

        $where_clause = implode(' AND ', $where_conditions);
        $order_clause = sprintf('ORDER BY %s %s', $args['orderby'], $args['order']);

        $sql = "SELECT * FROM $table_name WHERE $where_clause $order_clause";

        if ($args['limit'] > 0) {
            $sql .= $wpdb->prepare(' LIMIT %d OFFSET %d', $args['limit'], $args['offset']);
        }

        if (!empty($where_values)) {
            $sql = $wpdb->prepare($sql, $where_values);
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Get leads count
     */
    public function get_leads_count($args = array()) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';

        $where_conditions = array('1=1');
        $where_values = array();

        // Filter by status
        if (!empty($args['status'])) {
            $where_conditions[] = 'status = %s';
            $where_values[] = $args['status'];
        }

        // Filter by package
        if (!empty($args['package_id'])) {
            $where_conditions[] = 'package_id = %d';
            $where_values[] = $args['package_id'];
        }

        // Filter by date range
        if (!empty($args['date_from'])) {
            $where_conditions[] = 'created_at >= %s';
            $where_values[] = $args['date_from'] . ' 00:00:00';
        }

        if (!empty($args['date_to'])) {
            $where_conditions[] = 'created_at <= %s';
            $where_values[] = $args['date_to'] . ' 23:59:59';
        }

        $where_clause = implode(' AND ', $where_conditions);
        $sql = "SELECT COUNT(*) FROM $table_name WHERE $where_clause";

        if (!empty($where_values)) {
            $sql = $wpdb->prepare($sql, $where_values);
        }

        return (int) $wpdb->get_var($sql);
    }

    /**
     * Update lead status
     */
    public function update_lead_status($lead_id, $status) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';

        $valid_statuses = array('new', 'contacted', 'qualified', 'converted', 'lost');

        if (!in_array($status, $valid_statuses)) {
            return false;
        }

        return $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $lead_id),
            array('%s'),
            array('%d')
        );
    }

    /**
     * Delete lead
     */
    public function delete_lead($lead_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';

        return $wpdb->delete(
            $table_name,
            array('id' => $lead_id),
            array('%d')
        );
    }

    /**
     * AJAX handler for getting leads
     */
    public function get_leads_ajax() {
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta função.', 'travelcurator'));
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'travelcurator_admin_nonce')) {
            wp_die(__('Erro de segurança.', 'travelcurator'));
        }

        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 20;
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;

        $args = array(
            'status' => $status,
            'package_id' => $package_id,
            'limit' => $per_page,
            'offset' => ($page - 1) * $per_page
        );

        $leads = $this->get_leads($args);
        $total = $this->get_leads_count($args);

        // Format leads data
        $formatted_leads = array();
        foreach ($leads as $lead) {
            $package_title = '';
            if ($lead->package_id) {
                $package = get_post($lead->package_id);
                $package_title = $package ? $package->post_title : __('Pacote removido', 'travelcurator');
            }

            $formatted_leads[] = array(
                'id' => $lead->id,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'message' => $lead->message,
                'package_id' => $lead->package_id,
                'package_title' => $package_title,
                'status' => $lead->status,
                'source' => $lead->source,
                'created_at' => date_i18n('d/m/Y H:i', strtotime($lead->created_at)),
                'updated_at' => date_i18n('d/m/Y H:i', strtotime($lead->updated_at))
            );
        }

        wp_send_json_success(array(
            'leads' => $formatted_leads,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ));
    }

    /**
     * AJAX handler for updating lead status
     */
    public function update_lead_status_ajax() {
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta função.', 'travelcurator'));
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'travelcurator_admin_nonce')) {
            wp_die(__('Erro de segurança.', 'travelcurator'));
        }

        $lead_id = intval($_POST['lead_id']);
        $status = sanitize_text_field($_POST['status']);

        if ($this->update_lead_status($lead_id, $status)) {
            wp_send_json_success(array(
                'message' => __('Status atualizado com sucesso!', 'travelcurator')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erro ao atualizar status.', 'travelcurator')
            ));
        }
    }

    /**
     * AJAX handler for deleting lead
     */
    public function delete_lead_ajax() {
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta função.', 'travelcurator'));
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'travelcurator_admin_nonce')) {
            wp_die(__('Erro de segurança.', 'travelcurator'));
        }

        $lead_id = intval($_POST['lead_id']);

        if ($this->delete_lead($lead_id)) {
            wp_send_json_success(array(
                'message' => __('Lead removido com sucesso!', 'travelcurator')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erro ao remover lead.', 'travelcurator')
            ));
        }
    }

    /**
     * Export leads to CSV
     */
    public function export_leads_csv($args = array()) {
        $leads = $this->get_leads($args);

        if (empty($leads)) {
            return false;
        }

        $csv_data = array();
        
        // Add header row
        $csv_data[] = array(
            __('ID', 'travelcurator'),
            __('Nome', 'travelcurator'),
            __('Email', 'travelcurator'),
            __('Telefone', 'travelcurator'),
            __('Mensagem', 'travelcurator'),
            __('Pacote', 'travelcurator'),
            __('Status', 'travelcurator'),
            __('Origem', 'travelcurator'),
            __('Data de Criação', 'travelcurator'),
            __('Última Atualização', 'travelcurator')
        );

        // Add data rows
        foreach ($leads as $lead) {
            $package_title = '';
            if ($lead->package_id) {
                $package = get_post($lead->package_id);
                $package_title = $package ? $package->post_title : __('Pacote removido', 'travelcurator');
            }

            $csv_data[] = array(
                $lead->id,
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->message,
                $package_title,
                $lead->status,
                $lead->source,
                date_i18n('d/m/Y H:i:s', strtotime($lead->created_at)),
                date_i18n('d/m/Y H:i:s', strtotime($lead->updated_at))
            );
        }

        return $csv_data;
    }

    /**
     * AJAX handler for exporting leads
     */
    public function export_leads_ajax() {
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para acessar esta função.', 'travelcurator'));
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'travelcurator_admin_nonce')) {
            wp_die(__('Erro de segurança.', 'travelcurator'));
        }

        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;
        $date_from = isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : '';
        $date_to = isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : '';

        $args = array(
            'status' => $status,
            'package_id' => $package_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'limit' => 0 // Export all
        );

        $csv_data = $this->export_leads_csv($args);

        if (!$csv_data) {
            wp_send_json_error(array(
                'message' => __('Nenhum lead encontrado para exportar.', 'travelcurator')
            ));
        }

        // Generate CSV content
        $csv_content = '';
        foreach ($csv_data as $row) {
            $csv_content .= '"' . implode('","', array_map('str_replace', array('"', '""'), $row)) . '"' . "\n";
        }

        // Set headers for download
        $filename = 'leads-travelcurator-' . date('Y-m-d') . '.csv';
        
        wp_send_json_success(array(
            'csv_content' => base64_encode($csv_content),
            'filename' => $filename
        ));
    }

    /**
     * Get leads statistics
     */
    public function get_leads_stats($period = 'month') {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';
        
        $date_condition = '';
        switch ($period) {
            case 'today':
                $date_condition = "DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'year':
                $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
            default:
                $date_condition = '1=1';
        }

        // Total leads
        $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $date_condition");

        // Leads by status
        $status_stats = $wpdb->get_results(
            "SELECT status, COUNT(*) as count FROM $table_name WHERE $date_condition GROUP BY status"
        );

        // Most popular packages
        $popular_packages = $wpdb->get_results(
            "SELECT package_id, COUNT(*) as count FROM $table_name 
             WHERE $date_condition AND package_id > 0 
             GROUP BY package_id 
             ORDER BY count DESC 
             LIMIT 5"
        );

        // Format popular packages with titles
        $formatted_packages = array();
        foreach ($popular_packages as $package) {
            $post = get_post($package->package_id);
            $formatted_packages[] = array(
                'package_id' => $package->package_id,
                'package_title' => $post ? $post->post_title : __('Pacote removido', 'travelcurator'),
                'count' => $package->count
            );
        }

        return array(
            'total_leads' => $total_leads,
            'status_stats' => $status_stats,
            'popular_packages' => $formatted_packages,
            'period' => $period
        );
    }

    /**
     * Get conversion rate
     */
    public function get_conversion_rate($period = 'month') {
        global $wpdb;

        $table_name = $wpdb->prefix . 'travelcurator_leads';
        
        $date_condition = '';
        switch ($period) {
            case 'today':
                $date_condition = "DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case 'month':
                $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                break;
            case 'year':
                $date_condition = "created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
            default:
                $date_condition = '1=1';
        }

        $total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $date_condition");
        $converted_leads = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $date_condition AND status = 'converted'");

        $conversion_rate = $total_leads > 0 ? ($converted_leads / $total_leads) * 100 : 0;

        return array(
            'total_leads' => $total_leads,
            'converted_leads' => $converted_leads,
            'conversion_rate' => round($conversion_rate, 2),
            'period' => $period
        );
    }
}