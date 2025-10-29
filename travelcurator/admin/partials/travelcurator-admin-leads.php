<?php
/**
 * Admin Leads Page Template
 *
 * @package TravelCurator
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap travelcurator-admin">
    <h1><?php _e('Gerenciar Leads', 'travelcurator'); ?></h1>
    
    <div class="leads-summary">
        <h2><?php _e('Resumo de Leads', 'travelcurator'); ?></h2>
        <p><?php printf(__('Total de leads: %d', 'travelcurator'), $leads_data['total']); ?></p>
        
        <?php if (!empty($leads_data['statuses'])) : ?>
            <div class="status-summary">
                <?php foreach ($leads_data['statuses'] as $status) : ?>
                    <span class="status-count">
                        <?php
                        $status_names = array(
                            'new' => __('Novos', 'travelcurator'),
                            'contacted' => __('Contatados', 'travelcurator'),
                            'qualified' => __('Qualificados', 'travelcurator'),
                            'converted' => __('Convertidos', 'travelcurator'),
                            'lost' => __('Perdidos', 'travelcurator')
                        );
                        echo esc_html($status_names[$status->status] ?? $status->status) . ': ' . esc_html($status->count);
                        ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="leads-content">
        <?php if (!empty($leads_data['leads'])) : ?>
            <table class="wp-list-table widefat fixed striped leads-table">
                <thead>
                    <tr>
                        <th><?php _e('Nome', 'travelcurator'); ?></th>
                        <th><?php _e('Email', 'travelcurator'); ?></th>
                        <th><?php _e('Telefone', 'travelcurator'); ?></th>
                        <th><?php _e('Pacote', 'travelcurator'); ?></th>
                        <th><?php _e('Status', 'travelcurator'); ?></th>
                        <th><?php _e('Data', 'travelcurator'); ?></th>
                        <th><?php _e('Ações', 'travelcurator'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads_data['leads'] as $lead) : ?>
                        <tr>
                            <td>
                                <strong><?php echo esc_html($lead->name); ?></strong>
                                <?php if (!empty($lead->message)) : ?>
                                    <div class="lead-message">
                                        <?php echo esc_html(wp_trim_words($lead->message, 10)); ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="mailto:<?php echo esc_attr($lead->email); ?>">
                                    <?php echo esc_html($lead->email); ?>
                                </a>
                            </td>
                            <td>
								<a href="tel:<?php echo esc_attr($lead->phone); ?>">
                                    <?php echo esc_html($lead->phone); ?>
                                </a>
                            </td>
                            <td>
                                <?php 
                                if ($lead->package_id) {
                                    $package = get_post($lead->package_id);
                                    if ($package) {
                                        echo '<a href="' . get_edit_post_link($package->ID) . '">';
                                        echo esc_html($package->post_title);
                                        echo '</a>';
                                    } else {
                                        echo '<em>' . __('Pacote removido', 'travelcurator') . '</em>';
                                    }
                                } else {
                                    echo '<em>' . __('Não especificado', 'travelcurator') . '</em>';
                                }
                                ?>
                            </td>
                            <td>
                                <select class="status-select" data-lead-id="<?php echo esc_attr($lead->id); ?>">
                                    <option value="new" <?php selected($lead->status, 'new'); ?>><?php _e('Novo', 'travelcurator'); ?></option>
                                    <option value="contacted" <?php selected($lead->status, 'contacted'); ?>><?php _e('Contatado', 'travelcurator'); ?></option>
                                    <option value="qualified" <?php selected($lead->status, 'qualified'); ?>><?php _e('Qualificado', 'travelcurator'); ?></option>
                                    <option value="converted" <?php selected($lead->status, 'converted'); ?>><?php _e('Convertido', 'travelcurator'); ?></option>
                                    <option value="lost" <?php selected($lead->status, 'lost'); ?>><?php _e('Perdido', 'travelcurator'); ?></option>
                                </select>
                            </td>
                            <td>
                                <?php echo esc_html(date_i18n('d/m/Y H:i', strtotime($lead->created_at))); ?>
                            </td>
                            <td>
                                <button type="button" class="button view-lead" data-lead-id="<?php echo esc_attr($lead->id); ?>">
                                    <?php _e('Ver', 'travelcurator'); ?>
                                </button>
                                <button type="button" class="button button-small button-link-delete delete-lead" data-lead-id="<?php echo esc_attr($lead->id); ?>">
                                    <?php _e('Excluir', 'travelcurator'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="leads-actions">
                <button type="button" class="button" id="export-leads">
                    <?php _e('Exportar CSV', 'travelcurator'); ?>
                </button>
            </div>
        <?php else : ?>
            <div class="no-leads">
                <p><?php _e('Nenhum lead encontrado.', 'travelcurator'); ?></p>
                <p><?php _e('Os leads aparecerão aqui quando os visitantes preencherem os formulários de contato.', 'travelcurator'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Lead Details Modal -->
<div id="lead-modal" class="lead-modal" style="display: none;">
    <div class="lead-modal-content">
        <span class="lead-modal-close">&times;</span>
        <h2><?php _e('Detalhes do Lead', 'travelcurator'); ?></h2>
        <div id="lead-modal-body"></div>
    </div>
</div>

<style>
.travelcurator-admin .leads-summary {
    background: #fff;
    border: 1px solid #e1e1e1;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .status-summary {
    margin-top: 15px;
}

.travelcurator-admin .status-count {
    display: inline-block;
    margin-right: 20px;
    padding: 5px 10px;
    background: #f1f1f1;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.travelcurator-admin .leads-content {
    background: #fff;
    border: 1px solid #e1e1e1;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .leads-table {
    margin-bottom: 0;
}

.travelcurator-admin .lead-message {
    font-size: 12px;
    color: #666;
    font-style: italic;
    margin-top: 5px;
}

.travelcurator-admin .status-select {
    width: 120px;
    font-size: 12px;
}

.travelcurator-admin .leads-actions {
    padding: 20px;
    border-top: 1px solid #e1e1e1;
}

.travelcurator-admin .no-leads {
    padding: 40px 20px;
    text-align: center;
    color: #666;
}

/* Modal Styles */
.lead-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.lead-modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 4px;
}

.lead-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.lead-modal-close:hover {
    color: #000;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Handle status changes
    $('.status-select').change(function() {
        var leadId = $(this).data('lead-id');
        var newStatus = $(this).val();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'travelcurator_update_lead_status',
                lead_id: leadId,
                status: newStatus,
                nonce: travelcurator_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>')
                        .insertAfter('.wrap h1')
                        .delay(3000)
                        .fadeOut();
                }
            }
        });
    });
    
    // Handle lead deletion
    $('.delete-lead').click(function() {
        if (!confirm('<?php _e('Tem certeza que deseja excluir este lead?', 'travelcurator'); ?>')) {
            return;
        }
        
        var leadId = $(this).data('lead-id');
        var row = $(this).closest('tr');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'travelcurator_delete_lead',
                lead_id: leadId,
                nonce: travelcurator_admin_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    row.fadeOut(function() {
                        $(this).remove();
                    });
                }
            }
        });
    });
    
    // Handle export
    $('#export-leads').click(function() {
        window.location.href = ajaxurl + '?action=travelcurator_export_leads&nonce=' + travelcurator_admin_ajax.nonce;
    });
    
    // Handle view lead details
    $('.view-lead').click(function() {
        var leadId = $(this).data('lead-id');
        
        // Find lead data from the table row
        var row = $(this).closest('tr');
        var name = row.find('td:first strong').text();
        var email = row.find('td:nth-child(2) a').text();
        var phone = row.find('td:nth-child(3) a').text();
        var message = row.find('.lead-message').text() || '<?php _e('Nenhuma mensagem', 'travelcurator'); ?>';
        
        var modalContent = '<div class="lead-details">' +
            '<p><strong><?php _e('Nome:', 'travelcurator'); ?></strong> ' + name + '</p>' +
            '<p><strong><?php _e('Email:', 'travelcurator'); ?></strong> ' + email + '</p>' +
            '<p><strong><?php _e('Telefone:', 'travelcurator'); ?></strong> ' + phone + '</p>' +
            '<p><strong><?php _e('Mensagem:', 'travelcurator'); ?></strong></p>' +
            '<div style="background: #f9f9f9; padding: 15px; border-radius: 4px;">' + message + '</div>' +
            '</div>';
        
        $('#lead-modal-body').html(modalContent);
        $('#lead-modal').show();
    });
    
    // Close modal
    $('.lead-modal-close, .lead-modal').click(function(e) {
        if (e.target === this) {
            $('#lead-modal').hide();
        }
    });
});
</script>