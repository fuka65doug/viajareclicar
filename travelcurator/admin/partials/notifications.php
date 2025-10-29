<?php
/**
 * Admin Notifications Partial
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/admin/partials
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// This file displays admin notices and notifications
// The actual notifications are handled by the TravelCurator_Notifications class
?>

<div class="wrap travelcurator-notifications-page">
    <h1><?php _e('Central de Notificações', 'travelcurator'); ?></h1>

    <div class="notification-settings-box">
        <h2><?php _e('Configurações de Notificações', 'travelcurator'); ?></h2>
        
        <form method="post" action="options.php">
            <?php settings_fields('travelcurator_notification_settings'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <?php _e('Notificações por E-mail', 'travelcurator'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="travelcurator_notify_new_lead" value="1" <?php checked(get_option('travelcurator_notify_new_lead', '1'), '1'); ?>>
                                <?php _e('Notificar quando um novo lead for recebido', 'travelcurator'); ?>
                            </label>
                            <br><br>
                            <label>
                                <input type="checkbox" name="travelcurator_notify_lead_status_change" value="1" <?php checked(get_option('travelcurator_notify_lead_status_change'), '1'); ?>>
                                <?php _e('Notificar quando o status de um lead mudar', 'travelcurator'); ?>
                            </label>
                            <br><br>
                            <label>
                                <input type="checkbox" name="travelcurator_notify_package_views" value="1" <?php checked(get_option('travelcurator_notify_package_views'), '1'); ?>>
                                <?php _e('Relatório semanal de visualizações de pacotes', 'travelcurator'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="notification_email"><?php _e('E-mail para Notificações', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="notification_email" name="travelcurator_notification_email" value="<?php echo esc_attr(get_option('travelcurator_notification_email', get_option('admin_email'))); ?>" class="regular-text">
                        <p class="description"><?php _e('E-mail que receberá as notificações', 'travelcurator'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <?php _e('Notificações no Painel', 'travelcurator'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="travelcurator_dashboard_notifications" value="1" <?php checked(get_option('travelcurator_dashboard_notifications', '1'), '1'); ?>>
                                <?php _e('Exibir notificações no painel do WordPress', 'travelcurator'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Salvar Configurações', 'travelcurator')); ?>
        </form>
    </div>

    <div class="notification-history-box">
        <h2><?php _e('Histórico de Notificações', 'travelcurator'); ?></h2>
        
        <?php
        // Get recent notifications from database or transient
        global $wpdb;
        $leads_table = $wpdb->prefix . 'travelcurator_leads';
        $recent_activity = $wpdb->get_results("
            SELECT id, name, package_name, status, created_at 
            FROM $leads_table 
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        ?>

        <?php if (!empty($recent_activity)) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Tipo', 'travelcurator'); ?></th>
                        <th><?php _e('Descrição', 'travelcurator'); ?></th>
                        <th><?php _e('Data', 'travelcurator'); ?></th>
                        <th><?php _e('Status', 'travelcurator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_activity as $activity) : ?>
                        <tr>
                            <td>
                                <span class="dashicons dashicons-admin-users"></span>
                                <?php _e('Novo Lead', 'travelcurator'); ?>
                            </td>
                            <td>
                                <?php
                                printf(
                                    __('Lead de %s para o pacote "%s"', 'travelcurator'),
                                    '<strong>' . esc_html($activity->name) . '</strong>',
                                    esc_html($activity->package_name)
                                );
                                ?>
                            </td>
                            <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($activity->created_at)); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($activity->status); ?>">
                                    <?php echo esc_html(ucfirst($activity->status)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p class="no-notifications">
                <span class="dashicons dashicons-info"></span>
                <?php _e('Nenhuma notificação recente.', 'travelcurator'); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="notification-test-box">
        <h2><?php _e('Testar Notificações', 'travelcurator'); ?></h2>
        <p><?php _e('Envie um e-mail de teste para verificar se as notificações estão funcionando corretamente.', 'travelcurator'); ?></p>
        
        <button type="button" id="test-notification-btn" class="button button-secondary">
            <span class="dashicons dashicons-email"></span>
            <?php _e('Enviar E-mail de Teste', 'travelcurator'); ?>
        </button>
        
        <div id="test-notification-result" style="margin-top: 15px;"></div>
    </div>

</div>

<style>
.travelcurator-notifications-page {
    max-width: 1200px;
}

.notification-settings-box,
.notification-history-box,
.notification-test-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.notification-settings-box h2,
.notification-history-box h2,
.notification-test-box h2 {
    margin-top: 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
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

.no-notifications {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 4px;
    color: #666;
}

.no-notifications .dashicons {
    font-size: 24px;
}

#test-notification-btn .dashicons {
    line-height: 1.8;
}

.notice {
    margin: 15px 0;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('#test-notification-btn').on('click', function() {
        var $btn = $(this);
        var $result = $('#test-notification-result');
        
        $btn.prop('disabled', true).text('<?php _e('Enviando...', 'travelcurator'); ?>');
        $result.html('');
        
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'test_notification_email',
                nonce: '<?php echo wp_create_nonce('travelcurator_test_notification'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $result.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                } else {
                    $result.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $result.html('<div class="notice notice-error"><p><?php _e('Erro ao enviar e-mail de teste.', 'travelcurator'); ?></p></div>');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-email"></span> <?php _e('Enviar E-mail de Teste', 'travelcurator'); ?>');
            }
        });
    });
});
</script>
