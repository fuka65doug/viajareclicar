<?php
/**
 * Admin Dashboard Template
 *
 * @package TravelCurator
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap travelcurator-admin">
    <h1><?php _e('TravelCurator Dashboard', 'travelcurator'); ?></h1>
    
    <div class="travelcurator-dashboard">
        <div class="dashboard-stats">
            <div class="stat-box">
                <div class="stat-number"><?php echo esc_html($leads_stats['total_leads']); ?></div>
                <div class="stat-label"><?php _e('Total de Leads', 'travelcurator'); ?></div>
            </div>
            
            <div class="stat-box">
                <div class="stat-number"><?php echo esc_html($leads_stats['new_leads']); ?></div>
                <div class="stat-label"><?php _e('Novos Leads (7 dias)', 'travelcurator'); ?></div>
            </div>
            
            <div class="stat-box">
                <div class="stat-number"><?php echo esc_html($leads_stats['converted_leads']); ?></div>
                <div class="stat-label"><?php _e('Leads Convertidos', 'travelcurator'); ?></div>
            </div>
            
            <div class="stat-box">
                <div class="stat-number"><?php echo esc_html($leads_stats['total_packages']); ?></div>
                <div class="stat-label"><?php _e('Pacotes Publicados', 'travelcurator'); ?></div>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="dashboard-section">
                <h2><?php _e('Leads Recentes', 'travelcurator'); ?></h2>
                
                <?php if (!empty($leads_stats['recent_leads'])) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Nome', 'travelcurator'); ?></th>
                                <th><?php _e('Email', 'travelcurator'); ?></th>
                                <th><?php _e('Telefone', 'travelcurator'); ?></th>
                                <th><?php _e('Status', 'travelcurator'); ?></th>
                                <th><?php _e('Data', 'travelcurator'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads_stats['recent_leads'] as $lead) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html($lead->name); ?></strong></td>
                                    <td><?php echo esc_html($lead->email); ?></td>
                                    <td><?php echo esc_html($lead->phone); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo esc_attr($lead->status); ?>">
                                            <?php
                                            $statuses = array(
                                                'new' => __('Novo', 'travelcurator'),
                                                'contacted' => __('Contatado', 'travelcurator'),
                                                'qualified' => __('Qualificado', 'travelcurator'),
                                                'converted' => __('Convertido', 'travelcurator'),
                                                'lost' => __('Perdido', 'travelcurator')
                                            );
                                            echo esc_html($statuses[$lead->status] ?? $lead->status);
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html(date_i18n('d/m/Y H:i', strtotime($lead->created_at))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <p class="dashboard-link">
                        <a href="<?php echo admin_url('admin.php?page=travelcurator-leads'); ?>" class="button">
                            <?php _e('Ver Todos os Leads', 'travelcurator'); ?>
                        </a>
                    </p>
                <?php else : ?>
                    <p><?php _e('Nenhum lead encontrado.', 'travelcurator'); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-section">
                <h2><?php _e('Ações Rápidas', 'travelcurator'); ?></h2>
                
                <div class="quick-actions">
                    <a href="<?php echo admin_url('post-new.php?post_type=travel_package'); ?>" class="button button-primary" target="_blank">
                        <?php _e('Criar Novo Pacote', 'travelcurator'); ?>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=travelcurator-leads'); ?>" class="button">
                        <?php _e('Gerenciar Leads', 'travelcurator'); ?>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=travelcurator-settings'); ?>" class="button">
                        <?php _e('Configurações', 'travelcurator'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.travelcurator-admin .dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.travelcurator-admin .stat-box {
    background: #fff;
    border: 1px solid #e1e1e1;
    border-left: 4px solid #0073aa;
    padding: 20px;
    text-align: center;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .stat-number {
    font-size: 32px;
    font-weight: 600;
    color: #0073aa;
    margin-bottom: 5px;
}

.travelcurator-admin .stat-label {
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.travelcurator-admin .dashboard-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.travelcurator-admin .dashboard-section {
    background: #fff;
    border: 1px solid #e1e1e1;
    padding: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .dashboard-section h2 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
    font-size: 18px;
}

.travelcurator-admin .status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.travelcurator-admin .status-new {
    background: #fff3cd;
    color: #856404;
}

.travelcurator-admin .status-contacted {
    background: #d1ecf1;
    color: #0c5460;
}

.travelcurator-admin .status-qualified {
    background: #cce5ff;
    color: #004085;
}

.travelcurator-admin .status-converted {
    background: #d4edda;
    color: #155724;
}

.travelcurator-admin .status-lost {
    background: #f8d7da;
    color: #721c24;
}

.travelcurator-admin .quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.travelcurator-admin .dashboard-link {
    text-align: center;
    margin-top: 15px;
}

@media (max-width: 768px) {
    .travelcurator-admin .dashboard-content {
        grid-template-columns: 1fr;
    }
    
    .travelcurator-admin .dashboard-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .travelcurator-admin .dashboard-stats {
        grid-template-columns: 1fr;
    }
}
</style>
