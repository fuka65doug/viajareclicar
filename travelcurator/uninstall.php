<?php
/**
 * Uninstall TravelCurator Plugin
 *
 * This file is executed when the plugin is uninstalled (deleted).
 * It removes all plugin data from the database if the option is enabled.
 *
 * @package    TravelCurator
 * @subpackage TravelCurator
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has opted to delete data on uninstall
$delete_data = get_option('travelcurator_delete_data_on_uninstall');

if ($delete_data == '1') {
    
    global $wpdb;
    
    // Delete all travel packages
    $packages = get_posts(array(
        'post_type' => 'travel_package',
        'posts_per_page' => -1,
        'post_status' => 'any',
    ));
    
    foreach ($packages as $package) {
        wp_delete_post($package->ID, true);
    }
    
    // Delete all terms from custom taxonomies
    $taxonomies = array('destination', 'travel_category');
    
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                wp_delete_term($term->term_id, $taxonomy);
            }
        }
    }
    
    // Delete leads table
    $leads_table = $wpdb->prefix . 'travelcurator_leads';
    $wpdb->query("DROP TABLE IF EXISTS {$leads_table}");
    
    // Delete all plugin options
    $options = array(
        // General settings
        'travelcurator_company_name',
        'travelcurator_company_email',
        'travelcurator_company_phone',
        'travelcurator_currency',
        'travelcurator_archive_per_page',
        
        // Email settings
        'travelcurator_email_notify_leads',
        'travelcurator_notification_recipients',
        'travelcurator_email_from_name',
        'travelcurator_email_from_address',
        'travelcurator_email_subject',
        'travelcurator_email_auto_reply',
        'travelcurator_auto_reply_message',
        
        // WhatsApp settings
        'travelcurator_whatsapp_enabled',
        'travelcurator_whatsapp_number',
        'travelcurator_whatsapp_message',
        'travelcurator_whatsapp_position',
        
        // Advanced settings
        'travelcurator_disable_css',
        'travelcurator_disable_js',
        'travelcurator_custom_css',
        'travelcurator_delete_data_on_uninstall',
        
        // Notification settings
        'travelcurator_notify_new_lead',
        'travelcurator_notify_lead_status_change',
        'travelcurator_notify_package_views',
        'travelcurator_notification_email',
        'travelcurator_dashboard_notifications',
        
        // Version
        'travelcurator_version',
        'travelcurator_db_version',
    );
    
    foreach ($options as $option) {
        delete_option($option);
    }
    
    // Delete all transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_travelcurator_%' OR option_name LIKE '_transient_timeout_travelcurator_%'");
    
    // Delete all post meta associated with travel packages
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_travel_%'");
    
    // Clear any cached data
    wp_cache_flush();
}

// Clean up on multisite
if (is_multisite()) {
    
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        
        if (get_option('travelcurator_delete_data_on_uninstall') == '1') {
            // Repeat the cleanup process for each site
            
            // Delete packages
            $packages = get_posts(array(
                'post_type' => 'travel_package',
                'posts_per_page' => -1,
                'post_status' => 'any',
            ));
            
            foreach ($packages as $package) {
                wp_delete_post($package->ID, true);
            }
            
            // Delete leads table
            $leads_table = $wpdb->prefix . 'travelcurator_leads';
            $wpdb->query("DROP TABLE IF EXISTS {$leads_table}");
            
            // Delete options
            $options = array(
                'travelcurator_company_name',
                'travelcurator_company_email',
                'travelcurator_company_phone',
                'travelcurator_currency',
                'travelcurator_archive_per_page',
                'travelcurator_email_notify_leads',
                'travelcurator_notification_recipients',
                'travelcurator_email_from_name',
                'travelcurator_email_from_address',
                'travelcurator_email_subject',
                'travelcurator_email_auto_reply',
                'travelcurator_auto_reply_message',
                'travelcurator_whatsapp_enabled',
                'travelcurator_whatsapp_number',
                'travelcurator_whatsapp_message',
                'travelcurator_whatsapp_position',
                'travelcurator_disable_css',
                'travelcurator_disable_js',
                'travelcurator_custom_css',
                'travelcurator_delete_data_on_uninstall',
                'travelcurator_notify_new_lead',
                'travelcurator_notify_lead_status_change',
                'travelcurator_notify_package_views',
                'travelcurator_notification_email',
                'travelcurator_dashboard_notifications',
                'travelcurator_version',
                'travelcurator_db_version',
            );
            
            foreach ($options as $option) {
                delete_option($option);
            }
        }
        
        restore_current_blog();
    }
}
