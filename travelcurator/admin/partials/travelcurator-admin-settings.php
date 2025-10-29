<?php
/**
 * Admin Settings Page Template
 *
 * @package TravelCurator
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit']) && isset($_POST['_wpnonce'])) {
    if (wp_verify_nonce($_POST['_wpnonce'], 'travelcurator_settings')) {
        // Save WhatsApp settings
        if (isset($_POST['travelcurator_whatsapp_number'])) {
            update_option('travelcurator_whatsapp_number', sanitize_text_field($_POST['travelcurator_whatsapp_number']));
        }
        
        if (isset($_POST['travelcurator_whatsapp_message'])) {
            update_option('travelcurator_whatsapp_message', sanitize_textarea_field($_POST['travelcurator_whatsapp_message']));
        }
        
        // Save email settings
        if (isset($_POST['travelcurator_admin_email'])) {
            update_option('travelcurator_admin_email', sanitize_email($_POST['travelcurator_admin_email']));
        }
        
        // Save other settings
        if (isset($_POST['travelcurator_currency_symbol'])) {
            update_option('travelcurator_currency_symbol', sanitize_text_field($_POST['travelcurator_currency_symbol']));
        }
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Configurações salvas com sucesso!', 'travelcurator') . '</p></div>';
    }
}
?>

<div class="wrap travelcurator-admin">
    <h1><?php _e('Configurações do TravelCurator', 'travelcurator'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('travelcurator_settings'); ?>
        
        <div class="settings-sections">
            <!-- WhatsApp Settings -->
            <div class="settings-section">
                <h2><?php _e('Configurações do WhatsApp', 'travelcurator'); ?></h2>
                <p class="description"><?php _e('Configure o botão WhatsApp flutuante que aparecerá no site.', 'travelcurator'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="whatsapp_number"><?php _e('Número do WhatsApp', 'travelcurator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="whatsapp_number" name="travelcurator_whatsapp_number" 
                                   value="<?php echo esc_attr(get_option('travelcurator_whatsapp_number', '')); ?>" 
                                   placeholder="5511999999999" class="regular-text" />
                            <p class="description"><?php _e('Digite o número no formato internacional (ex: 5511999999999)', 'travelcurator'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="whatsapp_message"><?php _e('Mensagem Padrão', 'travelcurator'); ?></label>
                        </th>
                        <td>
                            <textarea id="whatsapp_message" name="travelcurator_whatsapp_message" 
                                      rows="3" cols="50" class="large-text"><?php echo esc_textarea(get_option('travelcurator_whatsapp_message', __('Olá! Gostaria de mais informações sobre os pacotes de viagem.', 'travelcurator'))); ?></textarea>
                            <p class="description"><?php _e('Mensagem que será enviada automaticamente quando o usuário clicar no botão WhatsApp.', 'travelcurator'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Email Settings -->
            <div class="settings-section">
                <h2><?php _e('Configurações de E-mail', 'travelcurator'); ?></h2>
                <p class="description"><?php _e('Configure as notificações por e-mail.', 'travelcurator'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="admin_email"><?php _e('E-mail do Administrador', 'travelcurator'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="admin_email" name="travelcurator_admin_email" 
                                   value="<?php echo esc_attr(get_option('travelcurator_admin_email', get_option('admin_email'))); ?>" 
                                   class="regular-text" />
                            <p class="description"><?php _e('E-mail que receberá notificações quando novos leads forem recebidos.', 'travelcurator'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- General Settings -->
            <div class="settings-section">
                <h2><?php _e('Configurações Gerais', 'travelcurator'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="currency_symbol"><?php _e('Símbolo da Moeda', 'travelcurator'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="currency_symbol" name="travelcurator_currency_symbol" 
                                   value="<?php echo esc_attr(get_option('travelcurator_currency_symbol', 'R$')); ?>" 
                                   class="small-text" />
                            <p class="description"><?php _e('Símbolo que aparecerá nos preços dos pacotes.', 'travelcurator'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Shortcodes Help -->
            <div class="settings-section">
                <h2><?php _e('Shortcodes Disponíveis', 'travelcurator'); ?></h2>
                <p class="description"><?php _e('Use estes shortcodes em suas páginas e posts:', 'travelcurator'); ?></p>
                
                <div class="shortcodes-list">
                    <div class="shortcode-item">
                        <code>[travel_packages]</code>
                        <p><?php _e('Exibe uma grade com os pacotes de viagem.', 'travelcurator'); ?></p>
                        <details>
                            <summary><?php _e('Parâmetros disponíveis', 'travelcurator'); ?></summary>
                            <ul>
                                <li><code>limit="6"</code> - <?php _e('Número de pacotes a exibir', 'travelcurator'); ?></li>
                                <li><code>columns="3"</code> - <?php _e('Número de colunas', 'travelcurator'); ?></li>
                                <li><code>category="aventura"</code> - <?php _e('Filtrar por categoria', 'travelcurator'); ?></li>
                                <li><code>featured="yes"</code> - <?php _e('Mostrar apenas pacotes em destaque', 'travelcurator'); ?></li>
                            </ul>
                        </details>
                    </div>
                    
                    <div class="shortcode-item">
                        <code>[travel_search]</code>
                        <p><?php _e('Exibe um formulário de busca de pacotes.', 'travelcurator'); ?></p>
                    </div>
                    
                    <div class="shortcode-item">
                        <code>[travel_lead_form]</code>
                        <p><?php _e('Exibe um formulário para captura de leads.', 'travelcurator'); ?></p>
                        <details>
                            <summary><?php _e('Parâmetros disponíveis', 'travelcurator'); ?></summary>
                            <ul>
                                <li><code>title="Solicite um orçamento"</code> - <?php _e('Título do formulário', 'travelcurator'); ?></li>
                                <li><code>button_text="Enviar"</code> - <?php _e('Texto do botão', 'travelcurator'); ?></li>
                            </ul>
                        </details>
                    </div>
                </div>
            </div>
        </div>
        
        <?php submit_button(__('Salvar Configurações', 'travelcurator')); ?>
    </form>
</div>

<style>
.travelcurator-admin .settings-sections {
    max-width: 800px;
}

.travelcurator-admin .settings-section {
    background: #fff;
    border: 1px solid #e1e1e1;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.travelcurator-admin .settings-section h2 {
    margin-top: 0;
    margin-bottom: 10px;
    color: #333;
    border-bottom: 1px solid #e1e1e1;
    padding-bottom: 10px;
}

.travelcurator-admin .shortcodes-list {
    margin-top: 15px;
}

.travelcurator-admin .shortcode-item {
    background: #f9f9f9;
    border: 1px solid #e1e1e1;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.travelcurator-admin .shortcode-item code {
    background: #333;
    color: #fff;
    padding: 4px 8px;
    border-radius: 3px;
    font-weight: 600;
}

.travelcurator-admin .shortcode-item p {
    margin: 10px 0 5px 0;
    color: #666;
}

.travelcurator-admin .shortcode-item details {
    margin-top: 10px;
}

.travelcurator-admin .shortcode-item summary {
    cursor: pointer;
    font-weight: 600;
    color: #0073aa;
}

.travelcurator-admin .shortcode-item ul {
    margin: 10px 0;
    padding-left: 20px;
}

.travelcurator-admin .shortcode-item li {
    margin-bottom: 5px;
}

.travelcurator-admin .shortcode-item li code {
    background: #0073aa;
    font-size: 11px;
}
</style>
