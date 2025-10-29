<?php
/**
 * Admin Leads Management Partial
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/admin/partials
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$leads_table = $wpdb->prefix . 'travelcurator_leads';

// Handle status filter
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

// Handle search
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Pagination
$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Build query
$where = "WHERE 1=1";
if ($status_filter) {
    $where .= $wpdb->prepare(" AND status = %s", $status_filter);
}
if ($search) {
    $where .= $wpdb->prepare(" AND (name LIKE %s OR email LIKE %s OR package_name LIKE %s)", 
        '%' . $wpdb->esc_like($search) . '%',
        '%' . $wpdb->esc_like($search) . '%',
        '%' . $wpdb->esc_like($search) . '%'
    );
}

$total_leads = $wpdb->get_var("SELECT COUNT(*) FROM $leads_table $where");
$total_pages = ceil($total_leads / $per_page);

$leads = $wpdb->get_results(
    "SELECT * FROM $leads_table $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset"
);

// Get counts for filters
$status_counts = $wpdb->get_results(
    "SELECT status, COUNT(*) as count FROM $leads_table GROUP BY status",
    OBJECT_K
);
?>

<div class="wrap travelcurator-leads-page">
    <h1 class="wp-heading-inline">
        <?php _e('Gerenciar Leads', 'travelcurator'); ?>
    </h1>
    
    <hr class="wp-header-end">

    <div class="leads-filters">
        <ul class="subsubsub">
            <li class="all">
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads'); ?>" class="<?php echo empty($status_filter) ? 'current' : ''; ?>">
                    <?php _e('Todos', 'travelcurator'); ?>
                    <span class="count">(<?php echo $total_leads; ?>)</span>
                </a> |
            </li>
            <li class="pending">
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads&status=pending'); ?>" class="<?php echo $status_filter === 'pending' ? 'current' : ''; ?>">
                    <?php _e('Pendentes', 'travelcurator'); ?>
                    <span class="count">(<?php echo isset($status_counts['pending']) ? $status_counts['pending']->count : 0; ?>)</span>
                </a> |
            </li>
            <li class="contacted">
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads&status=contacted'); ?>" class="<?php echo $status_filter === 'contacted' ? 'current' : ''; ?>">
                    <?php _e('Contatados', 'travelcurator'); ?>
                    <span class="count">(<?php echo isset($status_counts['contacted']) ? $status_counts['contacted']->count : 0; ?>)</span>
                </a> |
            </li>
            <li class="converted">
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads&status=converted'); ?>" class="<?php echo $status_filter === 'converted' ? 'current' : ''; ?>">
                    <?php _e('Convertidos', 'travelcurator'); ?>
                    <span class="count">(<?php echo isset($status_counts['converted']) ? $status_counts['converted']->count : 0; ?>)</span>
                </a> |
            </li>
            <li class="lost">
                <a href="<?php echo admin_url('admin.php?page=travelcurator-leads&status=lost'); ?>" class="<?php echo $status_filter === 'lost' ? 'current' : ''; ?>">
                    <?php _e('Perdidos', 'travelcurator'); ?>
                    <span class="count">(<?php echo isset($status_counts['lost']) ? $status_counts['lost']->count : 0; ?>)</span>
                </a>
            </li>
        </ul>

        <form method="get" class="search-form">
            <input type="hidden" name="page" value="travelcurator-leads">
            <?php if ($status_filter) : ?>
                <input type="hidden" name="status" value="<?php echo esc_attr($status_filter); ?>">
            <?php endif; ?>
            <p class="search-box">
                <label class="screen-reader-text" for="lead-search-input"><?php _e('Buscar leads:', 'travelcurator'); ?></label>
                <input type="search" id="lead-search-input" name="s" value="<?php echo esc_attr($search); ?>">
                <input type="submit" class="button" value="<?php _e('Buscar Leads', 'travelcurator'); ?>">
            </p>
        </form>
    </div>

    <?php if (!empty($leads)) : ?>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th class="check-column">
                        <input type="checkbox" id="select-all-leads">
                    </th>
                    <th><?php _e('Nome', 'travelcurator'); ?></th>
                    <th><?php _e('Contato', 'travelcurator'); ?></th>
                    <th><?php _e('Pacote', 'travelcurator'); ?></th>
                    <th><?php _e('Data da Viagem', 'travelcurator'); ?></th>
                    <th><?php _e('Viajantes', 'travelcurator'); ?></th>
                    <th><?php _e('Status', 'travelcurator'); ?></th>
                    <th><?php _e('Data', 'travelcurator'); ?></th>
                    <th><?php _e('Ações', 'travelcurator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead) : ?>
                    <tr id="lead-<?php echo $lead->id; ?>" data-lead-id="<?php echo $lead->id; ?>">
                        <td class="check-column">
                            <input type="checkbox" name="lead_ids[]" value="<?php echo $lead->id; ?>">
                        </td>
                        <td>
                            <strong><?php echo esc_html($lead->name); ?></strong>
                            <div class="row-actions">
                                <span class="view">
                                    <a href="#" class="view-lead-details" data-lead-id="<?php echo $lead->id; ?>">
                                        <?php _e('Ver detalhes', 'travelcurator'); ?>
                                    </a> |
                                </span>
                                <span class="delete">
                                    <a href="#" class="delete-lead" data-lead-id="<?php echo $lead->id; ?>" style="color: #a00;">
                                        <?php _e('Excluir', 'travelcurator'); ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <div>
                                    <span class="dashicons dashicons-email"></span>
                                    <a href="mailto:<?php echo esc_attr($lead->email); ?>"><?php echo esc_html($lead->email); ?></a>
                                </div>
                                <div>
                                    <span class="dashicons dashicons-phone"></span>
                                    <a href="tel:<?php echo esc_attr($lead->phone); ?>"><?php echo esc_html($lead->phone); ?></a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($lead->package_id) : ?>
                                <a href="<?php echo get_edit_post_link($lead->package_id); ?>">
                                    <?php echo esc_html($lead->package_name); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html($lead->package_name); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $lead->travel_date ? date_i18n(get_option('date_format'), strtotime($lead->travel_date)) : '-'; ?>
                        </td>
                        <td><?php echo esc_html($lead->travelers); ?></td>
                        <td>
                            <select class="lead-status-select" data-lead-id="<?php echo $lead->id; ?>">
                                <option value="pending" <?php selected($lead->status, 'pending'); ?>><?php _e('Pendente', 'travelcurator'); ?></option>
                                <option value="contacted" <?php selected($lead->status, 'contacted'); ?>><?php _e('Contatado', 'travelcurator'); ?></option>
                                <option value="converted" <?php selected($lead->status, 'converted'); ?>><?php _e('Convertido', 'travelcurator'); ?></option>
                                <option value="lost" <?php selected($lead->status, 'lost'); ?>><?php _e('Perdido', 'travelcurator'); ?></option>
                            </select>
                        </td>
                        <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($lead->created_at)); ?></td>
                        <td>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead->phone); ?>?text=<?php echo urlencode('Olá ' . $lead->name . ', vi sua solicitação para o pacote ' . $lead->package_name); ?>" class="button button-small" target="_blank">
                                <span class="dashicons dashicons-whatsapp"></span>
                                WhatsApp
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                    ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="no-leads">
            <p><?php _e('Nenhum lead encontrado.', 'travelcurator'); ?></p>
        </div>
    <?php endif; ?>

</div>

<!-- Lead Details Modal -->
<div id="lead-details-modal" class="travelcurator-modal" style="display: none;">
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <div id="lead-details-content"></div>
    </div>
</div>

<style>
.travelcurator-leads-page {
    max-width: 1400px;
}

.leads-filters {
    margin: 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.contact-info {
    font-size: 13px;
}

.contact-info div {
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 3px 0;
}

.contact-info .dashicons {
    width: 16px;
    height: 16px;
    font-size: 16px;
}

.lead-status-select {
    padding: 5px;
    border-radius: 4px;
}

.button .dashicons {
    line-height: 1.8;
}

.travelcurator-modal {
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
    position: relative;
}

.modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.modal-close:hover {
    color: #000;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Change lead status
    $('.lead-status-select').on('change', function() {
        var leadId = $(this).data('lead-id');
        var newStatus = $(this).val();
        
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'update_lead_status',
                lead_id: leadId,
                status: newStatus,
                nonce: '<?php echo wp_create_nonce('travelcurator_lead_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    var $row = $('#lead-' + leadId);
                    $row.css('background-color', '#d4edda');
                    setTimeout(function() {
                        $row.css('background-color', '');
                    }, 2000);
                }
            }
        });
    });

    // Delete lead
    $('.delete-lead').on('click', function(e) {
        e.preventDefault();
        if (!confirm('<?php _e('Tem certeza que deseja excluir este lead?', 'travelcurator'); ?>')) {
            return;
        }
        
        var leadId = $(this).data('lead-id');
        
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'delete_lead',
                lead_id: leadId,
                nonce: '<?php echo wp_create_nonce('travelcurator_lead_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#lead-' + leadId).fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            }
        });
    });

    // View lead details
    $('.view-lead-details').on('click', function(e) {
        e.preventDefault();
        var leadId = $(this).data('lead-id');
        
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'get_lead_details',
                lead_id: leadId,
                nonce: '<?php echo wp_create_nonce('travelcurator_lead_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#lead-details-content').html(response.data.html);
                    $('#lead-details-modal').fadeIn();
                }
            }
        });
    });

    // Close modal
    $('.modal-close, .travelcurator-modal').on('click', function(e) {
        if (e.target === this) {
            $('#lead-details-modal').fadeOut();
        }
    });

    // Select all
    $('#select-all-leads').on('change', function() {
        $('input[name="lead_ids[]"]').prop('checked', $(this).prop('checked'));
    });
});
</script>
