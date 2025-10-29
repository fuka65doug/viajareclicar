<?php
/**
 * Admin Dashboard Partial
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/admin/partials
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get statistics
global $wpdb;
$packages_count = wp_count_posts('travel_package')->publish;
$leads_table = $wpdb->prefix . 'travelcurator_leads';
$total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table");
$pending_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE status = 'pending'");
$converted_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table WHERE status = 'converted'");

// Get recent leads
$recent_leads = $wpdb->get_results("SELECT * FROM $leads_table ORDER BY created_at DESC LIMIT 5");

// Get popular packages
$popular_packages = $wpdb->get_results("
    SELECT package_id, package_name, COUNT(*) as lead_count
    FROM $leads_table
    WHERE package_id IS NOT NULL
    GROUP BY package_id, package_name
    ORDER BY lead_count DESC
    LIMIT 5
");
?>

<div class="wrap travelcurator-dashboard">
    <h1 class="wp-heading-inline">
        <?php _e('Dashboard - TravelCurator', 'travelcurator'); ?>
    </h1>
    
    <hr class="wp-header-end">

    <div class="travelcurator-stats-grid">
        
        <div class="stat-card stat-packages">
            <div class="stat-icon">
                <span class="dashicons dashicons-admin-site-alt3"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format_i18n($packages_count); ?></div>
                <div class="stat-label"><?php _e('Pacotes Publicados', 'travelcurator'); ?></div>
            </div>
            <div class="stat-action">
                <a href="<?php echo admin_url('post-new.php?post_type=travel_package'); ?>" class="button">
                    <?php _e('Novo Pacote', 'travelcurator'); ?>
                </a>
            </div>
        </div>

        <div class="stat-card stat-leads">
            <div class="stat-icon">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format_i18n($total_leads); ?></div>
                <div class="stat-label"><?php _e('Total de Leads', 'travelcurator'); ?></div>
            </div>
            <div class="stat-action">
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads'); ?>" class="button">
                    <?php _e('Ver Todos', 'travelcurator'); ?>
                </a>
            </div>
        </div>

        <div class="stat-card stat-pending">
            <div class="stat-icon">
                <span class="dashicons dashicons-clock"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format_i18n($pending_leads); ?></div>
                <div class="stat-label"><?php _e('Leads Pendentes', 'travelcurator'); ?></div>
            </div>
            <div class="stat-action">
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads&status=pending'); ?>" class="button button-primary">
                    <?php _e('Responder', 'travelcurator'); ?>
                </a>
            </div>
        </div>

        <div class="stat-card stat-converted">
            <div class="stat-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format_i18n($converted_leads); ?></div>
                <div class="stat-label"><?php _e('Leads Convertidos', 'travelcurator'); ?></div>
            </div>
            <div class="stat-action">
                <?php 
                $conversion_rate = $total_leads > 0 ? ($converted_leads / $total_leads) * 100 : 0;
                ?>
                <span class="conversion-rate"><?php echo number_format($conversion_rate, 1); ?>%</span>
            </div>
        </div>

    </div>

    <div class="travelcurator-dashboard-grid">
        
        <div class="dashboard-section recent-leads">
            <div class="section-header">
                <h2><?php _e('Leads Recentes', 'travelcurator'); ?></h2>
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads'); ?>">
                    <?php _e('Ver todos', 'travelcurator'); ?>
                </a>
            </div>
            
            <?php if (!empty($recent_leads)) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Nome', 'travelcurator'); ?></th>
                            <th><?php _e('Pacote', 'travelcurator'); ?></th>
                            <th><?php _e('Status', 'travelcurator'); ?></th>
                            <th><?php _e('Data', 'travelcurator'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_leads as $lead) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($lead->name); ?></strong><br>
                                    <small><?php echo esc_html($lead->email); ?></small>
                                </td>
                                <td><?php echo esc_html($lead->package_name); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr($lead->status); ?>">
                                        <?php echo esc_html(ucfirst($lead->status)); ?>
                                    </span>
                                </td>
                                <td><?php echo date_i18n(get_option('date_format'), strtotime($lead->created_at)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p class="no-items"><?php _e('Nenhum lead registrado ainda.', 'travelcurator'); ?></p>
            <?php endif; ?>
        </div>

        <div class="dashboard-section popular-packages">
            <div class="section-header">
                <h2><?php _e('Pacotes Mais Procurados', 'travelcurator'); ?></h2>
            </div>
            
            <?php if (!empty($popular_packages)) : ?>
                <ul class="popular-packages-list">
                    <?php foreach ($popular_packages as $package) : ?>
                        <li class="popular-package-item">
                            <div class="package-info">
                                <span class="package-name"><?php echo esc_html($package->package_name); ?></span>
                                <span class="package-leads"><?php echo number_format_i18n($package->lead_count); ?> leads</span>
                            </div>
                            <div class="package-bar">
                                <?php $percentage = ($package->lead_count / $total_leads) * 100; ?>
                                <div class="bar-fill" style="width: <?php echo $percentage; ?>%;"></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="no-items"><?php _e('Nenhum dado disponível ainda.', 'travelcurator'); ?></p>
            <?php endif; ?>
        </div>

    </div>

    <div class="travelcurator-quick-links">
        <h2><?php _e('Links Rápidos', 'travelcurator'); ?></h2>
        <div class="quick-links-grid">
            <a href="<?php echo admin_url('post-new.php?post_type=travel_package'); ?>" class="quick-link">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php _e('Novo Pacote', 'travelcurator'); ?>
            </a>
            <a href="<?php echo admin_url('edit.php?post_type=travel_package'); ?>" class="quick-link">
                <span class="dashicons dashicons-admin-site-alt3"></span>
                <?php _e('Todos os Pacotes', 'travelcurator'); ?>
            </a>
            <a href="<?php echo admin_url('edit-tags.php?taxonomy=destination&post_type=travel_package'); ?>" class="quick-link">
                <span class="dashicons dashicons-location"></span>
                <?php _e('Destinos', 'travelcurator'); ?>
            </a>
            <a href="<?php echo admin_url('edit-tags.php?taxonomy=travel_category&post_type=travel_package'); ?>" class="quick-link">
                <span class="dashicons dashicons-category"></span>
                <?php _e('Categorias', 'travelcurator'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=travelcurator-settings'); ?>" class="quick-link">
                <span class="dashicons dashicons-admin-settings"></span>
                <?php _e('Configurações', 'travelcurator'); ?>
            </a>
            <a href="<?php echo get_post_type_archive_link('travel_package'); ?>" class="quick-link" target="_blank">
                <span class="dashicons dashicons-visibility"></span>
                <?php _e('Ver Site', 'travelcurator'); ?>
            </a>
        </div>
    </div>

</div>

<style>
.travelcurator-dashboard {
    max-width: 1400px;
}

.travelcurator-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.stat-icon {
    font-size: 48px;
    opacity: 0.7;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    color: #666;
    margin-top: 5px;
}

.travelcurator-dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.dashboard-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-header h2 {
    margin: 0;
    font-size: 18px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending { background: #fef6e6; color: #f59e0b; }
.status-contacted { background: #e6f2ff; color: #0066cc; }
.status-converted { background: #e6f9f0; color: #10b981; }
.status-lost { background: #fee; color: #dc2626; }

.popular-packages-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.popular-package-item {
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.popular-package-item:last-child {
    border-bottom: none;
}

.package-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.package-name {
    font-weight: 600;
}

.package-leads {
    color: #666;
    font-size: 13px;
}

.package-bar {
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    transition: width 0.3s ease;
}

.travelcurator-quick-links {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.quick-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.quick-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
}

.quick-link:hover {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.quick-link .dashicons {
    font-size: 32px;
    margin-bottom: 10px;
}

.conversion-rate {
    font-size: 20px;
    font-weight: bold;
    color: #10b981;
}
</style>
