<?php

/**
 * Travel Lead Form Widget for Elementor
 *
 * @package TravelCurator
 */

class TravelCurator_Lead_Form_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'travel-lead-form';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Formulário de Lead', 'travelcurator');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    /**
     * Get widget categories
     */
    public function get_categories() {
        return ['travelcurator'];
    }

    /**
     * Register widget controls
     */
    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Configurações do Formulário', 'travelcurator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'form_title',
            [
                'label' => __('Título do Formulário', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Solicitar Informações', 'travelcurator'),
                'placeholder' => __('Digite o título', 'travelcurator'),
            ]
        );

        $this->add_control(
            'form_description',
            [
                'label' => __('Descrição', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Preencha o formulário abaixo e entraremos em contato em breve.', 'travelcurator'),
                'placeholder' => __('Digite a descrição', 'travelcurator'),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Texto do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Enviar Solicitação', 'travelcurator'),
                'placeholder' => __('Digite o texto do botão', 'travelcurator'),
            ]
        );

        $this->add_control(
            'show_package_field',
            [
                'label' => __('Mostrar Campo de Pacote', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sim', 'travelcurator'),
                'label_off' => __('Não', 'travelcurator'),
            ]
        );

        $this->add_control(
            'selected_package',
            [
                'label' => __('Pacote Pré-selecionado', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_travel_packages(),
                'condition' => [
                    'show_package_field' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Estilo', 'travelcurator'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'form_background_color',
            [
                'label' => __('Cor de Fundo', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#f8f9fa',
                'selectors' => [
                    '{{WRAPPER}} .travel-lead-form-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_padding',
            [
                'label' => __('Espaçamento Interno', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => '40',
                    'right' => '40',
                    'bottom' => '40',
                    'left' => '40',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .travel-lead-form-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Cor do Título', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .form-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Tipografia do Título', 'travelcurator'),
                'selector' => '{{WRAPPER}} .form-title',
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => __('Cor de Fundo do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0073aa',
                'selectors' => [
                    '{{WRAPPER}} .lead-submit-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Cor do Texto do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .lead-submit-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Get travel packages for select field
     */
    private function get_travel_packages() {
        $packages = get_posts([
            'post_type' => 'travel_package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ]);

        $options = [
            '' => __('Selecione um pacote', 'travelcurator'),
        ];

        foreach ($packages as $package) {
            $options[$package->ID] = $package->post_title;
        }

        return $options;
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $form_title = !empty($settings['form_title']) ? $settings['form_title'] : __('Solicitar Informações', 'travelcurator');
        $form_description = !empty($settings['form_description']) ? $settings['form_description'] : '';
        $button_text = !empty($settings['button_text']) ? $settings['button_text'] : __('Enviar Solicitação', 'travelcurator');
        $show_package_field = $settings['show_package_field'] === 'yes';
        $selected_package = !empty($settings['selected_package']) ? $settings['selected_package'] : 0;
        ?>

        <div class="travel-lead-form-widget">
            <div class="travel-lead-form-wrapper">
                <h3 class="form-title"><?php echo esc_html($form_title); ?></h3>
                
                <?php if (!empty($form_description)) : ?>
                    <p class="form-description"><?php echo esc_html($form_description); ?></p>
                <?php endif; ?>
                
                <form class="travel-lead-form" data-package-id="<?php echo esc_attr($selected_package); ?>">
                    <?php wp_nonce_field('travelcurator_lead_form', 'travelcurator_lead_nonce'); ?>
                    
                    <?php if ($show_package_field) : ?>
                        <div class="form-group">
                            <label for="lead_package"><?php _e('Pacote de Interesse', 'travelcurator'); ?></label>
                            <select id="lead_package" name="lead_package">
                                <?php
                                $packages = get_posts([
                                    'post_type' => 'travel_package',
                                    'posts_per_page' => -1,
                                    'post_status' => 'publish',
                                ]);
                                ?>
                                <option value=""><?php _e('Selecione um pacote', 'travelcurator'); ?></option>
                                <?php foreach ($packages as $package) : ?>
                                    <option value="<?php echo esc_attr($package->ID); ?>" <?php selected($selected_package, $package->ID); ?>>
                                        <?php echo esc_html($package->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="lead_name"><?php _e('Nome completo *', 'travelcurator'); ?></label>
                        <input type="text" id="lead_name" name="lead_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lead_email"><?php _e('E-mail *', 'travelcurator'); ?></label>
                        <input type="email" id="lead_email" name="lead_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lead_phone"><?php _e('Telefone/WhatsApp *', 'travelcurator'); ?></label>
                        <input type="tel" id="lead_phone" name="lead_phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lead_message"><?php _e('Mensagem', 'travelcurator'); ?></label>
                        <textarea id="lead_message" name="lead_message" rows="4" placeholder="<?php esc_attr_e('Conte-nos sobre suas preferências de viagem...', 'travelcurator'); ?>"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="lead-submit-button">
                            <?php echo esc_html($button_text); ?>
                        </button>
                    </div>
                    
                    <div class="form-messages"></div>
                </form>
            </div>
        </div>

        <style>
        .travel-lead-form-widget .travel-lead-form-wrapper {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .travel-lead-form-widget .form-title {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 600;
            color: #333;
            text-align: center;
        }

        .travel-lead-form-widget .form-description {
            margin: 0 0 30px 0;
            color: #666;
            text-align: center;
            font-size: 16px;
        }

        .travel-lead-form-widget .form-group {
            margin-bottom: 25px;
        }

        .travel-lead-form-widget .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .travel-lead-form-widget .form-group input,
        .travel-lead-form-widget .form-group select,
        .travel-lead-form-widget .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
        }

        .travel-lead-form-widget .form-group input:focus,
        .travel-lead-form-widget .form-group select:focus,
        .travel-lead-form-widget .form-group textarea:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 3px rgba(0,115,170,0.1);
        }

        .travel-lead-form-widget .lead-submit-button {
            width: 100%;
            background: #0073aa;
            color: white;
            padding: 18px;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .travel-lead-form-widget .lead-submit-button:hover {
            background: #005177;
            transform: translateY(-2px);
        }

        .travel-lead-form-widget .lead-submit-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .travel-lead-form-widget .form-messages {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }

        .travel-lead-form-widget .form-messages.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            display: block;
        }

        .travel-lead-form-widget .form-messages.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            display: block;
        }

        @media (max-width: 768px) {
            .travel-lead-form-widget .travel-lead-form-wrapper {
                padding: 20px;
            }
            
            .travel-lead-form-widget .form-title {
                font-size: 24px;
            }
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('.travel-lead-form').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var button = form.find('.lead-submit-button');
                var messages = form.find('.form-messages');
                
                // Disable button
                button.prop('disabled', true).text('<?php _e('Enviando...', 'travelcurator'); ?>');
                
                // Get form data
                var formData = {
                    action: 'travelcurator_submit_lead',
                    nonce: '<?php echo wp_create_nonce('travelcurator_nonce'); ?>',
                    lead_name: form.find('#lead_name').val(),
                    lead_email: form.find('#lead_email').val(),
                    lead_phone: form.find('#lead_phone').val(),
                    lead_message: form.find('#lead_message').val(),
                    package_id: form.find('#lead_package').val() || form.data('package-id') || 0
                };
                
                // Submit via AJAX
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', formData)
                    .done(function(response) {
                        if (response.success) {
                            messages.removeClass('error').addClass('success').text(response.data.message).show();
                            form[0].reset();
                        } else {
                            messages.removeClass('success').addClass('error').text(response.data.message).show();
                        }
                    })
                    .fail(function() {
                        messages.removeClass('success').addClass('error').text('<?php _e('Erro ao enviar. Tente novamente.', 'travelcurator'); ?>').show();
                    })
                    .always(function() {
                        button.prop('disabled', false).text('<?php echo esc_js($button_text); ?>');
                    });
            });
        });
        </script>
        <?php
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <div class="travel-lead-form-widget">
            <div class="travel-lead-form-wrapper">
                <h3 class="form-title">{{{ settings.form_title || '<?php _e('Solicitar Informações', 'travelcurator'); ?>' }}}</h3>
                
                <# if ( settings.form_description ) { #>
                    <p class="form-description">{{{ settings.form_description }}}</p>
                <# } #>
                
                <form class="travel-lead-form">
                    <# if ( settings.show_package_field === 'yes' ) { #>
                        <div class="form-group">
                            <label><?php _e('Pacote de Interesse', 'travelcurator'); ?></label>
                            <select>
                                <option><?php _e('Selecione um pacote', 'travelcurator'); ?></option>
                            </select>
                        </div>
                    <# } #>
                    
                    <div class="form-group">
                        <label><?php _e('Nome completo *', 'travelcurator'); ?></label>
                        <input type="text" placeholder="<?php esc_attr_e('Seu nome completo', 'travelcurator'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?php _e('E-mail *', 'travelcurator'); ?></label>
                        <input type="email" placeholder="<?php esc_attr_e('seu@email.com', 'travelcurator'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?php _e('Telefone/WhatsApp *', 'travelcurator'); ?></label>
                        <input type="tel" placeholder="<?php esc_attr_e('(11) 99999-9999', 'travelcurator'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><?php _e('Mensagem', 'travelcurator'); ?></label>
                        <textarea rows="4" placeholder="<?php esc_attr_e('Conte-nos sobre suas preferências de viagem...', 'travelcurator'); ?>"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="button" class="lead-submit-button">
                            {{{ settings.button_text || '<?php _e('Enviar Solicitação', 'travelcurator'); ?>' }}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
}