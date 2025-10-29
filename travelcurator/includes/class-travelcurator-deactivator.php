<?php
/**
 * Fired during plugin deactivation
 *
 * @package TravelCurator
 */

if (!defined('ABSPATH')) {
    exit;
}

class TravelCurator_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        self::clear_scheduled_events();
        self::flush_rewrite_rules();
        self::clear_cache();
        self::cleanup_temp_files();
        self::log_deactivation();
    }

    /**
     * Clear all scheduled cron events
     */
    private static function clear_scheduled_events() {
        // Clear scheduled events
        wp_clear_scheduled_hook('travelcurator_cleanup_old_leads');
        wp_clear_scheduled_hook('travelcurator_cleanup_old_notifications');
        wp_clear_scheduled_hook('travelcurator_send_weekly_report');
        wp_clear_scheduled_hook('travelcurator_backup_data');
        wp_clear_scheduled_hook('travelcurator_sync_external_data');
        wp_clear_scheduled_hook('travelcurator_check_package_availability');
    }

    /**
     * Flush rewrite rules
     */
    private static function flush_rewrite_rules() {
        flush_rewrite_rules();
    }

    /**
     * Clear plugin cache
     */
    private static function clear_cache() {
        // Clear object cache
        wp_cache_flush();
        
        // Clear transients
        global $wpdb;
        
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_travelcurator_%' 
             OR option_name LIKE '_transient_timeout_travelcurator_%'"
        );

        // Clear file cache if exists
        $cache_dir = WP_CONTENT_DIR . '/cache/travelcurator/';
        if (is_dir($cache_dir)) {
            self::delete_directory($cache_dir);
        }
    }

    /**
     * Clean up temporary files
     */
    private static function cleanup_temp_files() {
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/travelcurator/temp/';
        
        if (is_dir($temp_dir)) {
            $files = glob($temp_dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        // Clean up old export files
        $exports_dir = $upload_dir['basedir'] . '/travelcurator/exports/';
        if (is_dir($exports_dir)) {
            $files = glob($exports_dir . '*.csv');
            $cutoff_time = time() - (7 * 24 * 60 * 60); // 7 days ago
            
            foreach ($files as $file) {
                if (filemtime($file) < $cutoff_time) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Log plugin deactivation
     */
    private static function log_deactivation() {
        $deactivation_data = array(
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'plugin_version' => TRAVELCURATOR_VERSION,
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'reason' => self::get_deactivation_reason()
        );

        update_option('travelcurator_last_deactivation', $deactivation_data);

        // Optional: Send deactivation data to analytics (if user consented)
        $settings = get_option('travelcurator_settings', array());
        if (isset($settings['allow_usage_tracking']) && $settings['allow_usage_tracking']) {
            self::send_deactivation_data($deactivation_data);
        }
    }

    /**
     * Get deactivation reason from user input (if available)
     */
    private static function get_deactivation_reason() {
        // This would typically be captured from a deactivation survey
        // For now, we'll check if there's a reason in the request
        if (isset($_POST['travelcurator_deactivation_reason'])) {
            return sanitize_text_field($_POST['travelcurator_deactivation_reason']);
        }
        
        return 'unknown';
    }

    /**
     * Send deactivation data for analytics (optional)
     */
    private static function send_deactivation_data($data) {
        $endpoint = 'https://api.travelcurator.com/v1/deactivations';
        
        wp_remote_post($endpoint, array(
            'timeout' => 5,
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'TravelCurator/' . TRAVELCURATOR_VERSION
            ),
            'body' => json_encode($data)
        ));
    }

    /**
     * Recursively delete directory
     */
    private static function delete_directory($dir) {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                self::delete_directory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }

    /**
     * Show deactivation notice (optional)
     */
    public static function show_deactivation_notice() {
        if (current_user_can('activate_plugins')) {
            ?>
            <div class="notice notice-info">
                <p>
                    <?php _e('TravelCurator has been deactivated. Your data is safe and will be available if you reactivate the plugin.', 'travelcurator'); ?>
                </p>
                <p>
                    <a href="<?php echo esc_url(admin_url('plugins.php')); ?>" class="button button-primary">
                        <?php _e('Manage Plugins', 'travelcurator'); ?>
                    </a>
                    <a href="mailto:support@travelcurator.com?subject=Plugin Deactivation Feedback" class="button">
                        <?php _e('Send Feedback', 'travelcurator'); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Optional: Show deactivation survey
     */
    public static function show_deactivation_survey() {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        $reasons = array(
            'temporary' => __('It\'s a temporary deactivation', 'travelcurator'),
            'found_better' => __('I found a better plugin', 'travelcurator'),
            'not_needed' => __('I no longer need the plugin', 'travelcurator'),
            'not_working' => __('The plugin didn\'t work as expected', 'travelcurator'),
            'too_complicated' => __('Too complicated to use', 'travelcurator'),
            'missing_features' => __('Missing features I need', 'travelcurator'),
            'other' => __('Other', 'travelcurator')
        );

        ?>
        <div id="travelcurator-deactivation-survey" style="display: none;">
            <div class="travelcurator-survey-overlay">
                <div class="travelcurator-survey-modal">
                    <h3><?php _e('Quick Feedback', 'travelcurator'); ?></h3>
                    <p><?php _e('If you have a moment, please let us know why you\'re deactivating TravelCurator:', 'travelcurator'); ?></p>
                    
                    <form id="travelcurator-deactivation-form">
                        <?php foreach ($reasons as $key => $reason) : ?>
                        <label>
                            <input type="radio" name="reason" value="<?php echo esc_attr($key); ?>">
                            <?php echo esc_html($reason); ?>
                        </label>
                        <?php endforeach; ?>
                        
                        <div id="travelcurator-other-reason" style="display: none;">
                            <textarea name="other_reason" placeholder="<?php _e('Please tell us more...', 'travelcurator'); ?>" rows="3" cols="50"></textarea>
                        </div>
                        
                        <div class="travelcurator-survey-actions">
                            <button type="button" id="travelcurator-survey-skip" class="button">
                                <?php _e('Skip & Deactivate', 'travelcurator'); ?>
                            </button>
                            <button type="submit" class="button button-primary">
                                <?php _e('Submit & Deactivate', 'travelcurator'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Show survey when deactivation link is clicked
            $('[data-slug="travelcurator"] .deactivate a').on('click', function(e) {
                e.preventDefault();
                var deactivateUrl = $(this).attr('href');
                
                $('#travelcurator-deactivation-survey').show();
                
                // Handle other reason textarea
                $('input[name="reason"][value="other"]').change(function() {
                    if ($(this).is(':checked')) {
                        $('#travelcurator-other-reason').show();
                    } else {
                        $('#travelcurator-other-reason').hide();
                    }
                });
                
                // Handle form submission
                $('#travelcurator-deactivation-form').on('submit', function(e) {
                    e.preventDefault();
                    
                    var reason = $('input[name="reason"]:checked').val();
                    var otherReason = $('textarea[name="other_reason"]').val();
                    
                    // Send feedback
                    $.post(ajaxurl, {
                        action: 'travelcurator_deactivation_feedback',
                        reason: reason,
                        other_reason: otherReason,
                        nonce: '<?php echo wp_create_nonce('travelcurator_deactivation_feedback'); ?>'
                    });
                    
                    // Proceed with deactivation
                    window.location.href = deactivateUrl;
                });
                
                // Handle skip
                $('#travelcurator-survey-skip').on('click', function() {
                    window.location.href = deactivateUrl;
                });
                
                // Close on overlay click
                $('.travelcurator-survey-overlay').on('click', function(e) {
                    if (e.target === this) {
                        $('#travelcurator-deactivation-survey').hide();
                    }
                });
            });
        });
        </script>

        <style>
        .travelcurator-survey-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 100000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .travelcurator-survey-modal {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .travelcurator-survey-modal h3 {
            margin-top: 0;
            color: #1A3A5F;
        }
        
        .travelcurator-survey-modal label {
            display: block;
            margin-bottom: 10px;
            cursor: pointer;
        }
        
        .travelcurator-survey-modal input[type="radio"] {
            margin-right: 8px;
        }
        
        .travelcurator-survey-modal textarea {
            width: 100%;
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .travelcurator-survey-actions {
            margin-top: 20px;
            text-align: right;
        }
        
        .travelcurator-survey-actions .button {
            margin-left: 10px;
        }
        </style>
        <?php
    }

    /**
     * Handle deactivation feedback AJAX
     */
    public static function handle_deactivation_feedback() {
        check_ajax_referer('travelcurator_deactivation_feedback', 'nonce');
        
        $reason = sanitize_text_field($_POST['reason']);
        $other_reason = sanitize_textarea_field($_POST['other_reason']);
        
        $feedback_data = array(
            'reason' => $reason,
            'other_reason' => $other_reason,
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'site_url' => home_url(),
            'plugin_version' => TRAVELCURATOR_VERSION
        );
        
        // Save feedback locally
        $feedbacks = get_option('travelcurator_deactivation_feedbacks', array());
        $feedbacks[] = $feedback_data;
        update_option('travelcurator_deactivation_feedbacks', $feedbacks);
        
        // Send to remote server (optional)
        wp_remote_post('https://api.travelcurator.com/v1/feedback', array(
            'timeout' => 5,
            'body' => json_encode($feedback_data),
            'headers' => array('Content-Type' => 'application/json')
        ));
        
        wp_die();
    }

    /**
     * Clean up on uninstall (if user chooses to delete data)
     */
    public static function uninstall_cleanup() {
        // This method would be called from uninstall.php
        // Only if user specifically chooses to delete all data
        
        global $wpdb;
        
        // Drop custom tables
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}travelcurator_leads");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}travelcurator_notifications");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}travelcurator_analytics");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}travelcurator_reviews");
        
        // Delete all posts of our custom post type
        $posts = get_posts(array(
            'post_type' => 'travel_package',
            'numberposts' => -1,
            'post_status' => 'any'
        ));
        
        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }
        
        // Delete all options
        delete_option('travelcurator_settings');
        delete_option('travelcurator_version');
        delete_option('travelcurator_db_version');
        delete_option('travelcurator_activation_date');
        delete_option('travelcurator_last_deactivation');
        delete_option('travelcurator_deactivation_feedbacks');
        delete_option('travelcurator_email_notifications');
        delete_option('travelcurator_dashboard_notifications');
        delete_option('travelcurator_notification_retention');
        delete_option('travelcurator_notification_sound');
        delete_option('travelcurator_desktop_notifications');
        
        // Delete all transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options} 
             WHERE option_name LIKE '_transient_travelcurator_%' 
             OR option_name LIKE '_transient_timeout_travelcurator_%'"
        );
        
        // Delete user meta
        $wpdb->query(
            "DELETE FROM {$wpdb->usermeta} 
             WHERE meta_key LIKE 'travelcurator_%'"
        );
        
        // Delete upload directory
        $upload_dir = wp_upload_dir();
        $plugin_upload_dir = $upload_dir['basedir'] . '/travelcurator/';
        if (is_dir($plugin_upload_dir)) {
            self::delete_directory($plugin_upload_dir);
        }
        
        // Clear all caches
        wp_cache_flush();
        
        // Remove capabilities
        $roles = array('administrator', 'editor', 'author');
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->remove_cap('manage_travel_packages');
                $role->remove_cap('edit_travel_packages');
                $role->remove_cap('edit_others_travel_packages');
                $role->remove_cap('publish_travel_packages');
                $role->remove_cap('read_private_travel_packages');
                $role->remove_cap('delete_travel_packages');
                $role->remove_cap('delete_private_travel_packages');
                $role->remove_cap('delete_published_travel_packages');
                $role->remove_cap('delete_others_travel_packages');
                $role->remove_cap('edit_private_travel_packages');
                $role->remove_cap('edit_published_travel_packages');
                $role->remove_cap('manage_travel_categories');
                $role->remove_cap('edit_travel_categories');
                $role->remove_cap('delete_travel_categories');
                $role->remove_cap('assign_travel_categories');
            }
        }
    }
}