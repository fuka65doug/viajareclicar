<?php
/**
 * Admin Settings Partial
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/admin/partials
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
?>

<div class="wrap travelcurator-settings-page">
    <h1><?php _e('Configurações - TravelCurator', 'travelcurator'); ?></h1>

    <h2 class="nav-tab-wrapper">
        <a href="?page=travelcurator-settings&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Geral', 'travelcurator'); ?>
        </a>
        <a href="?page=travelcurator-settings&tab=email" class="nav-tab <?php echo $active_tab === 'email' ? 'nav-tab-active' : ''; ?>">
            <?php _e('E-mail', 'travelcurator'); ?>
        </a>
        <a href="?page=travelcurator-settings&tab=whatsapp" class="nav-tab <?php echo $active_tab === 'whatsapp' ? 'nav-tab-active' : ''; ?>">
            <?php _e('WhatsApp', 'travelcurator'); ?>
        </a>
        <a href="?page=travelcurator-settings&tab=advanced" class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Avançado', 'travelcurator'); ?>
        </a>
    </h2>

    <form method="post" action="options.php">
        <?php
        if ($active_tab === 'general') {
            settings_fields('travelcurator_general_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="company_name"><?php _e('Nome da Empresa', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="company_name" name="travelcurator_company_name" value="<?php echo esc_attr(get_option('travelcurator_company_name')); ?>" class="regular-text">
                        <p class="description"><?php _e('Nome da sua empresa de turismo', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="company_email"><?php _e('E-mail da Empresa', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="company_email" name="travelcurator_company_email" value="<?php echo esc_attr(get_option('travelcurator_company_email')); ?>" class="regular-text">
                        <p class="description"><?php _e('E-mail principal para receber notificações', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="company_phone"><?php _e('Telefone da Empresa', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="tel" id="company_phone" name="travelcurator_company_phone" value="<?php echo esc_attr(get_option('travelcurator_company_phone')); ?>" class="regular-text">
                        <p class="description"><?php _e('Telefone de contato (com DDD)', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="currency"><?php _e('Moeda', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <select id="currency" name="travelcurator_currency">
                            <option value="BRL" <?php selected(get_option('travelcurator_currency', 'BRL'), 'BRL'); ?>>Real (R$)</option>
                            <option value="USD" <?php selected(get_option('travelcurator_currency'), 'USD'); ?>>Dólar (US$)</option>
                            <option value="EUR" <?php selected(get_option('travelcurator_currency'), 'EUR'); ?>>Euro (€)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="archive_per_page"><?php _e('Pacotes por página', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="archive_per_page" name="travelcurator_archive_per_page" value="<?php echo esc_attr(get_option('travelcurator_archive_per_page', '12')); ?>" min="1" max="100" class="small-text">
                        <p class="description"><?php _e('Quantidade de pacotes exibidos na página de arquivo', 'travelcurator'); ?></p>
                    </td>
                </tr>
            </table>
            <?php
        } elseif ($active_tab === 'email') {
            settings_fields('travelcurator_email_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php _e('Notificar novos leads', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="travelcurator_email_notify_leads" value="1" <?php checked(get_option('travelcurator_email_notify_leads', '1'), '1'); ?>>
                            <?php _e('Enviar e-mail quando um novo lead for registrado', 'travelcurator'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="notification_recipients"><?php _e('Destinatários', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="notification_recipients" name="travelcurator_notification_recipients" value="<?php echo esc_attr(get_option('travelcurator_notification_recipients', get_option('admin_email'))); ?>" class="regular-text">
                        <p class="description"><?php _e('E-mails separados por vírgula para receber notificações', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_from_name"><?php _e('Nome do Remetente', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="email_from_name" name="travelcurator_email_from_name" value="<?php echo esc_attr(get_option('travelcurator_email_from_name', get_bloginfo('name'))); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_from_address"><?php _e('E-mail do Remetente', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="email" id="email_from_address" name="travelcurator_email_from_address" value="<?php echo esc_attr(get_option('travelcurator_email_from_address', get_option('admin_email'))); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="email_subject"><?php _e('Assunto do E-mail', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="email_subject" name="travelcurator_email_subject" value="<?php echo esc_attr(get_option('travelcurator_email_subject', __('Novo Lead Recebido', 'travelcurator'))); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php _e('Auto-resposta ao cliente', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="travelcurator_email_auto_reply" value="1" <?php checked(get_option('travelcurator_email_auto_reply', '1'), '1'); ?>>
                            <?php _e('Enviar e-mail de confirmação ao cliente', 'travelcurator'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="auto_reply_message"><?php _e('Mensagem de Auto-resposta', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <?php
                        $default_message = "Olá {nome},\n\nRecebemos sua solicitação para o pacote {pacote}.\n\nEm breve nossa equipe entrará em contato.\n\nObrigado!";
                        wp_editor(
                            get_option('travelcurator_auto_reply_message', $default_message),
                            'auto_reply_message',
                            array(
                                'textarea_name' => 'travelcurator_auto_reply_message',
                                'textarea_rows' => 10,
                                'media_buttons' => false,
                            )
                        );
                        ?>
                        <p class="description">
                            <?php _e('Variáveis disponíveis: {nome}, {email}, {telefone}, {pacote}, {data}, {viajantes}', 'travelcurator'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php
        } elseif ($active_tab === 'whatsapp') {
            settings_fields('travelcurator_whatsapp_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php _e('Ativar Botão WhatsApp', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="travelcurator_whatsapp_enabled" value="1" <?php checked(get_option('travelcurator_whatsapp_enabled', '1'), '1'); ?>>
                            <?php _e('Exibir botão flutuante do WhatsApp no site', 'travelcurator'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="whatsapp_number"><?php _e('Número do WhatsApp', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <input type="tel" id="whatsapp_number" name="travelcurator_whatsapp_number" value="<?php echo esc_attr(get_option('travelcurator_whatsapp_number')); ?>" class="regular-text" placeholder="5511999999999">
                        <p class="description"><?php _e('Número com código do país e DDD, sem espaços ou caracteres especiais', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="whatsapp_message"><?php _e('Mensagem Padrão', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <textarea id="whatsapp_message" name="travelcurator_whatsapp_message" rows="4" class="large-text"><?php echo esc_textarea(get_option('travelcurator_whatsapp_message', __('Olá! Gostaria de mais informações sobre os pacotes de viagem.', 'travelcurator'))); ?></textarea>
                        <p class="description"><?php _e('Mensagem que será pré-preenchida quando o usuário clicar no botão', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="whatsapp_position"><?php _e('Posição do Botão', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <select id="whatsapp_position" name="travelcurator_whatsapp_position">
                            <option value="bottom-right" <?php selected(get_option('travelcurator_whatsapp_position', 'bottom-right'), 'bottom-right'); ?>><?php _e('Inferior Direito', 'travelcurator'); ?></option>
                            <option value="bottom-left" <?php selected(get_option('travelcurator_whatsapp_position'), 'bottom-left'); ?>><?php _e('Inferior Esquerdo', 'travelcurator'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php
        } elseif ($active_tab === 'advanced') {
            settings_fields('travelcurator_advanced_settings');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php _e('Desabilitar CSS Padrão', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="travelcurator_disable_css" value="1" <?php checked(get_option('travelcurator_disable_css'), '1'); ?>>
                            <?php _e('Não carregar os estilos CSS do plugin', 'travelcurator'); ?>
                        </label>
                        <p class="description"><?php _e('Use apenas se você vai estilizar tudo com seu próprio CSS', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php _e('Desabilitar JavaScript Padrão', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="travelcurator_disable_js" value="1" <?php checked(get_option('travelcurator_disable_js'), '1'); ?>>
                            <?php _e('Não carregar os scripts JavaScript do plugin', 'travelcurator'); ?>
                        </label>
                        <p class="description"><?php _e('Atenção: isso pode quebrar algumas funcionalidades', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="custom_css"><?php _e('CSS Personalizado', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <textarea id="custom_css" name="travelcurator_custom_css" rows="10" class="large-text code"><?php echo esc_textarea(get_option('travelcurator_custom_css')); ?></textarea>
                        <p class="description"><?php _e('Adicione CSS personalizado que será carregado no frontend', 'travelcurator'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php _e('Limpar dados ao desinstalar', 'travelcurator'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="travelcurator_delete_data_on_uninstall" value="1" <?php checked(get_option('travelcurator_delete_data_on_uninstall'), '1'); ?>>
                            <?php _e('Remover todos os dados (pacotes, leads, configurações) ao desinstalar o plugin', 'travelcurator'); ?>
                        </label>
                        <p class="description" style="color: #dc3232;">
                            <?php _e('⚠️ ATENÇÃO: Esta ação é irreversível!', 'travelcurator'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php
        }
        
        submit_button();
        ?>
    </form>

</div>

<style>
.travelcurator-settings-page {
    max-width: 1000px;
}

.form-table th {
    width: 250px;
}

.nav-tab-wrapper {
    margin: 20px 0;
}
</style>
