<?php
/**
 * Handle notifications functionality
 */
class TravelCurator_Notifications {

    public function __construct() {
        add_action('wp_ajax_travelcurator_mark_notification_read', array($this, 'mark_notification_read'));
        add_action('wp_ajax_travelcurator_mark_all_notifications_read', array($this, 'mark_all_notifications_read'));
        add_action('wp_ajax_travelcurator_get_notifications', array($this, 'get_notifications_ajax'));
        add_action('wp_ajax_travelcurator_update_notification_preferences', array($this, 'update_notification_preferences'));
        
        // Schedule events
        add_action('travelcurator_send_digest_notifications', array($this, 'send_digest_notifications'));
        add_action('travelcurator_cleanup_old_notifications', array($this, 'cleanup_old_notifications'));
        
        if (!wp_next_scheduled('travelcurator_send_digest_notifications')) {
            wp_schedule_event(time(), 'daily', 'travelcurator_send_digest_notifications');
        }
        if (!wp_next_scheduled('travelcurator_cleanup_old_notifications')) {
            wp_schedule_event(time(), 'weekly', 'travelcurator_cleanup_old_notifications');
        }
    }

    /**
     * Create notification.
     */
    public function create_notification($args) {
        $defaults = array(
            'user_id' => 1,
            'type' => '',
            'title' => '',
            'message' => '',
            'data' => '',
            'read_status' => 0
        );
        $args = wp_parse_args($args, $defaults);
        
        if (empty($args['type']) || empty($args['title'])) return false;

        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        $result = $wpdb->insert($table, array(
            'user_id' => absint($args['user_id']),
            'type' => sanitize_text_field($args['type']),
            'title' => sanitize_text_field($args['title']),
            'message' => sanitize_textarea_field($args['message']),
            'data' => is_string($args['data']) ? $args['data'] : wp_json_encode($args['data']),
            'read_status' => absint($args['read_status']),
            'created_at' => current_time('mysql')
        ), array('%d', '%s', '%s', '%s', '%s', '%d', '%s'));

        if ($result) {
            $notification_id = $wpdb->insert_id;
            $this->maybe_send_email_notification($notification_id);
            return $notification_id;
        }
        return false;
    }

    /**
     * Get notifications for user.
     */
    public function get_notifications($user_id = 0, $args = array()) {
        if (!$user_id) $user_id = get_current_user_id();
        if (!$user_id) return array();

        $defaults = array('limit' => 20, 'offset' => 0, 'read_status' => null, 'type' => '', 'orderby' => 'created_at', 'order' => 'DESC');
        $args = wp_parse_args($args, $defaults);

        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        $where_conditions = array('user_id = %d');
        $where_values = array($user_id);

        if ($args['read_status'] !== null) {
            $where_conditions[] = 'read_status = %d';
            $where_values[] = absint($args['read_status']);
        }
        if ($args['type']) {
            $where_conditions[] = 'type = %s';
            $where_values[] = sanitize_text_field($args['type']);
        }

        $where_clause = implode(' AND ', $where_conditions);
        $orderby = in_array($args['orderby'], array('created_at', 'type', 'read_status')) ? $args['orderby'] : 'created_at';
        $order = strtoupper($args['order']) === 'ASC' ? 'ASC' : 'DESC';
        $query = "SELECT * FROM $table WHERE $where_clause ORDER BY $orderby $order";
        
        if ($args['limit'] > 0) {
            $query .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        }
        return $wpdb->get_results($wpdb->prepare($query, $where_values));
    }

    /**
     * Mark notification as read.
     */
    public function mark_notification_read() {
        if (!current_user_can('read')) {
            wp_die(json_encode(array('success' => false, 'message' => 'Unauthorized')));
        }
        $notification_id = absint($_POST['notification_id']);
        if (!$notification_id) {
            wp_die(json_encode(array('success' => false, 'message' => 'Invalid notification ID')));
        }
        $result = $this->mark_as_read($notification_id, get_current_user_id());
        wp_die(json_encode(array('success' => $result)));
    }

    /**
     * Mark as read.
     */
    public function mark_as_read($notification_id, $user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        $result = $wpdb->update($table, array('read_status' => 1), array('id' => absint($notification_id), 'user_id' => absint($user_id)), array('%d'), array('%d', '%d'));
        return $result !== false;
    }

    /**
     * Get unread count.
     */
    public function get_unread_count($user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();
        if (!$user_id) return 0;
        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d AND read_status = 0", $user_id));
    }

    /**
     * Send email notification if enabled.
     */
    private function maybe_send_email_notification($notification_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        $notification = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $notification_id));
        if (!$notification) return false;

        $preferences = $this->get_user_preferences($notification->user_id);
        $type_prefs = $preferences[$notification->type] ?? array();
        if (empty($type_prefs['email_enabled']) || $type_prefs['frequency'] !== 'immediate') return false;

        $user = get_userdata($notification->user_id);
        if (!$user || !$user->user_email) return false;

        $subject = $notification->title;
        $message = $this->get_email_template($notification);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        return wp_mail($user->user_email, $subject, $message, $headers);
    }

    /**
     * Get user preferences.
     */
    public function get_user_preferences($user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notification_preferences';
        $preferences = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id));

        $default_preferences = array(
            'new_lead' => array('email_enabled' => 1, 'dashboard_enabled' => 1, 'frequency' => 'immediate'),
            'whatsapp_click' => array('email_enabled' => 0, 'dashboard_enabled' => 1, 'frequency' => 'daily'),
            'package_view' => array('email_enabled' => 0, 'dashboard_enabled' => 0, 'frequency' => 'weekly')
        );

        $user_preferences = array();
        foreach ($preferences as $pref) {
            $user_preferences[$pref->notification_type] = array(
                'email_enabled' => $pref->email_enabled,
                'dashboard_enabled' => $pref->dashboard_enabled,
                'frequency' => $pref->frequency,
                'filters' => $pref->filters ? json_decode($pref->filters, true) : array()
            );
        }

        foreach ($default_preferences as $type => $defaults) {
            if (!isset($user_preferences[$type])) {
                $user_preferences[$type] = $defaults;
            }
        }
        return $user_preferences;
    }

    /**
     * Get email template.
     */
    private function get_email_template($notification) {
        $template = get_option('travelcurator_email_template', $this->get_default_email_template());
        $variables = array(
            '{title}' => $notification->title,
            '{message}' => $notification->message,
            '{date}' => date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($notification->created_at)),
            '{site_name}' => get_bloginfo('name'),
            '{site_url}' => home_url()
        );
        return str_replace(array_keys($variables), array_values($variables), $template);
    }

    /**
     * Get default email template.
     */
    private function get_default_email_template() {
        return '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #1A3A5F; border-bottom: 3px solid #D4B254; padding-bottom: 10px;">{title}</h1>
                <div style="background: #f9f9f9; padding: 20px; border-radius: 5px;">
                    <p>{message}</p>
                </div>
                <p style="margin-top: 20px; color: #666; font-size: 14px;">
                    <strong>Data:</strong> {date}<br>
                    <strong>Site:</strong> <a href="{site_url}">{site_name}</a>
                </p>
            </div>
        </body></html>';
    }

    /**
     * Send digest notifications.
     */
    public function send_digest_notifications() {
        $users = get_users(array('role__in' => array('administrator', 'editor')));
        foreach ($users as $user) {
            $this->send_user_digest($user->ID, 'daily');
        }
        if (date('N') == 1) { // Monday
            foreach ($users as $user) {
                $this->send_user_digest($user->ID, 'weekly');
            }
        }
    }

    /**
     * Cleanup old notifications.
     */
    public function cleanup_old_notifications() {
        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        $wpdb->query("DELETE FROM $table WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    }

    /**
     * Send user digest.
     */
    private function send_user_digest($user_id, $frequency) {
        $preferences = $this->get_user_preferences($user_id);
        $digest_types = array();
        foreach ($preferences as $type => $prefs) {
            if ($prefs['frequency'] === $frequency && $prefs['email_enabled']) {
                $digest_types[] = $type;
            }
        }
        if (empty($digest_types)) return;

        $date_from = $frequency === 'daily' ? date('Y-m-d') : date('Y-m-d', strtotime('-7 days'));
        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        $placeholders = implode(',', array_fill(0, count($digest_types), '%s'));
        $query_params = array_merge(array($user_id, $date_from), $digest_types);
        $notifications = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d AND DATE(created_at) >= %s AND type IN ($placeholders) ORDER BY created_at DESC", $query_params));

        if (empty($notifications)) return;
        $user = get_userdata($user_id);
        if (!$user || !$user->user_email) return;

        $subject = sprintf(__('Resumo %s - TravelCurator', 'travelcurator'), $frequency === 'daily' ? __('Diário', 'travelcurator') : __('Semanal', 'travelcurator'));
        $message = $this->get_digest_template($notifications, $frequency);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail($user->user_email, $subject, $message, $headers);
    }

    /**
     * Get digest template.
     */
    private function get_digest_template($notifications, $frequency) {
        $period = $frequency === 'daily' ? __('hoje', 'travelcurator') : __('esta semana', 'travelcurator');
        $html = '<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">';
        $html .= '<div style="max-width: 600px; margin: 0 auto; padding: 20px;">';
        $html .= '<h1 style="color: #1A3A5F; border-bottom: 3px solid #D4B254; padding-bottom: 10px;">';
        $html .= sprintf(__('Resumo de notificações - %s', 'travelcurator'), $period);
        $html .= '</h1>';
        $html .= '<p>' . sprintf(__('Você tem %d notificações %s:', 'travelcurator'), count($notifications), $period) . '</p>';
        $html .= '<div style="background: #f9f9f9; padding: 20px; border-radius: 5px;">';
        foreach ($notifications as $notification) {
            $html .= '<div style="border-bottom: 1px solid #ddd; padding: 15px 0;">';
            $html .= '<h3 style="margin: 0 0 10px 0; color: #1A3A5F;">' . esc_html($notification->title) . '</h3>';
            $html .= '<p style="margin: 0 0 5px 0;">' . esc_html($notification->message) . '</p>';
            $html .= '<small style="color: #666;">' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($notification->created_at)) . '</small>';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '<p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666;">';
        $html .= sprintf(__('Este email foi enviado pelo %s. Para alterar suas preferências de notificação, <a href="%s">clique aqui</a>.', 'travelcurator'), get_bloginfo('name'), admin_url('admin.php?page=travelcurator-notifications'));
        $html .= '</p></div></body></html>';
        return $html;
    }

    /**
     * Mark all as read.
     */
    public function mark_all_notifications_read() {
        if (!current_user_can('read')) {
            wp_die(json_encode(array('success' => false, 'message' => 'Unauthorized')));
        }
        $result = $this->mark_all_as_read(get_current_user_id());
        wp_die(json_encode(array('success' => $result)));
    }

    /**
     * Mark all as read for user.
     */
    public function mark_all_as_read($user_id = 0) {
        if (!$user_id) $user_id = get_current_user_id();
        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_notifications';
        $result = $wpdb->update($table, array('read_status' => 1), array('user_id' => absint($user_id)), array('%d'), array('%d'));
        return $result !== false;
    }
}