<?php
/**
 * Admin Notifications Page Template
 *
 * @package TravelCurator
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap travelcurator-admin">
    <h1><?php _e('Notificações e Atividades', 'travelcurator'); ?></h1>
    
    <div class="notifications-content">
        <div class="notifications-summary">
            <h2><?php _e('Atividade Recente', 'travelcurator'); ?></h2>
            <p><?php printf(__('Total de atividades: %d', 'travelcurator'), $notifications_data['total_activities']); ?></p>
        </div>
        
        <?php if (!empty($notifications_data['activities'])) : ?>
            <div class="activities-list">
                <?php foreach ($notifications_data['activities'] as $activity) : ?>
                    <div class="activity-item activity-<?php echo esc_attr($activity['type']); ?>">
                        <div class="activity-icon">
                            <?php if ($activity['type'] === 'lead') : ?>
                                <span class="dashicons dashicons-businessman"></span>
                            <?php elseif ($activity['type'] === 'package') : ?>
                                <span class="dashicons dashicons-palmtree"></span>
                            <?php else : ?>
                                <span class="dashicons dashicons-info"></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="activity-content">
                            <h3><?php echo esc_html($activity['title']); ?></h3>
                            <p><?php echo esc_html($activity['description']); ?></p>
                            <div class="activity-meta">
                                <span class="activity-date">
                                    <?php echo esc_html(date_i18n('d/m/Y H:i', strtotime($activity['date']))); ?>
                                </span>
                                <?php if (!empty($activity['status'])) : ?>
                                    <span class="activity-status status-<?php echo esc_attr($activity['status']); ?>">
                                        <?php
                                        $status_names = array(
                                            'new' => __('Novo', 'travelcurator'),
                                            'contacted' => __('Contatado', 'travelcurator'),
                                            'qualified' => __('Qualificado', 'travelcurator'),
                                            'converted' => __('Convertido', 'travelcurator'),
                                            'lost' => __('Perdido', 'travelcurator'),
                                            'publish' => __('Publicado', 'travelcurator'),
                                            'draft' => __('Rascunho', 'travelcurator'),
                                        );
                                        echo esc_html($status_names[$activity['status']] ?? $activity['status']);
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="no-activities">
                <p><?php _e('Nenhuma atividade recente encontrada.', 'travelcurator'); ?></p>
                <p><?php _e('As atividades aparecerão aqui quando houver novos leads, pacotes criados, etc.', 'travelcurator'); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="notifications-settings">
            <h2><?php _e('Configurações de Notificação', 'travelcurator'); ?></h2>
            
            <form method="post" action="">
                <?php wp_nonce_field('travelcurator_notifications'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php _e('Notificar por E-mail', 'travelcurator'); ?>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><?php _e('Opções de notificação por e-mail', 'travelcurator'); ?></legend>
                                <label>
                                    <input type="checkbox" name="notify_new_leads" value="1" 
                                           <?php checked(get_option('travelcurator_notify_new_leads', 1)); ?> />
                                    <?php _e('Novos leads recebidos', 'travelcurator'); ?>
                                </label>
                                <br>
                                <label>
                                    <input type="checkbox" name="notify_lead_status_change" value="1" 
                                           <?php checked(get_option('travelcurator_notify_lead_status_change', 0)); ?> />
                                    <?php _e('Mudanças de status de leads', 'travelcurator'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="notification_frequency"><?php _e('Frequência de Resumo', 'travelcurator'); ?></label>
                        </th>
                        <td>
                            <select name="notification_frequency" id="notification_frequency">
                                <option value="immediate" <?php selected(get_option('travelcurator_notification_frequency', 'immediate'), 'immediate'); ?>>
                                    <?php _e('Imediata', 'travelcurator'); ?>
                                </option>
                                <option value="daily" <?php selected(get_option('travelcurator_notification_frequency', 'immediate'), 'daily'); ?>>
                                    <?php _e('Diária', 'travelcurator'); ?>
                                </option>
                                <option value="weekly" <?php selected(get_option('travelcurator_notification_frequency', 'immediate'), 'weekly'); ?>>
                                    <?php _e('Semanal', 'travelcurator'); ?>
                                </option>
                                <option value="none" <?php selected(get_option('travelcurator_notification_frequency', 'immediate'), 'none'); ?>>
                                    <?php _e('Não enviar', 'travelcurator'); ?>
                                </option>
                            </select>
                            <p class="description"><?php _e('Com que frequência receber resumos por e-mail.', 'travelcurator'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Salvar Configurações de Notificação', 'travelcurator')); ?>
            </form>
        </div>
    </div>
</div>

<style>
.travelcurator-admin .notifications-content {
    max-width: 800px;
}

.travelcurator-admin .notifications-summary {
    background: #fff;
    border: 1px solid #e1e1e1;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .activities-list {
    background: #fff;
    border: 1px solid #e1e1e1;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .activity-item {
    display: flex;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid #f1f1f1;
}

.travelcurator-admin .activity-item:last-child {
    border-bottom: none;
}

.travelcurator-admin .activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.travelcurator-admin .activity-lead .activity-icon {
    background: #fff3cd;
    color: #856404;
}

.travelcurator-admin .activity-package .activity-icon {
    background: #d1ecf1;
    color: #0c5460;
}

.travelcurator-admin .activity-content {
    flex: 1;
}

.travelcurator-admin .activity-content h3 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #333;
}

.travelcurator-admin .activity-content p {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 14px;
}

.travelcurator-admin .activity-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 12px;
}

.travelcurator-admin .activity-date {
    color: #999;
}

.travelcurator-admin .activity-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.travelcurator-admin .status-new,
.travelcurator-admin .status-draft {
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

.travelcurator-admin .status-converted,
.travelcurator-admin .status-publish {
    background: #d4edda;
    color: #155724;
}

.travelcurator-admin .status-lost {
    background: #f8d7da;
    color: #721c24;
}

.travelcurator-admin .no-activities {
    background: #fff;
    border: 1px solid #e1e1e1;
    padding: 40px 20px;
    text-align: center;
    color: #666;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .notifications-settings {
    background: #fff;
    border: 1px solid #e1e1e1;
    padding: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .notifications-settings h2 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
    border-bottom: 1px solid #e1e1e1;
    padding-bottom: 10px;
}

.travelcurator-admin .notifications-settings fieldset label {
    display: block;
    margin-bottom: 8px;
}

@media (max-width: 768px) {
    .travelcurator-admin .activity-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .travelcurator-admin .activity-icon {
        margin-bottom: 10px;
        margin-right: 0;
    }
    
    .travelcurator-admin .activity-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>

<?php
// Handle settings form submission
if (isset($_POST['submit']) && isset($_POST['_wpnonce'])) {
    if (wp_verify_nonce($_POST['_wpnonce'], 'travelcurator_notifications')) {
        // Save notification settings
        update_option('travelcurator_notify_new_leads', isset($_POST['notify_new_leads']) ? 1 : 0);
        update_option('travelcurator_notify_lead_status_change', isset($_POST['notify_lead_status_change']) ? 1 : 0);
        
        if (isset($_POST['notification_frequency'])) {
            update_option('travelcurator_notification_frequency', sanitize_text_field($_POST['notification_frequency']));
        }
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Configurações de notificação salvas com sucesso!', 'travelcurator') . '</p></div>';
    }
}
?>