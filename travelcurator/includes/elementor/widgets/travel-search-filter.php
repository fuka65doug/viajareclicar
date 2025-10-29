<?php

/**
 * Travel Search Filter Widget for Elementor
 *
 * @package TravelCurator
 */

class TravelCurator_Search_Filter_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name
     */
    public function get_name() {
        return 'travel-search-filter';
    }

    /**
     * Get widget title
     */
    public function get_title() {
        return __('Filtro de Busca de Viagens', 'travelcurator');
    }

    /**
     * Get widget icon
     */
    public function get_icon() {
        return 'eicon-search';
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
                'label' => __('Configurações do Filtro', 'travelcurator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_category',
            [
                'label' => __('Mostrar Categoria', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sim', 'travelcurator'),
                'label_off' => __('Não', 'travelcurator'),
            ]
        );

        $this->add_control(
            'show_destination',
            [
                'label' => __('Mostrar Destino', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sim', 'travelcurator'),
                'label_off' => __('Não', 'travelcurator'),
            ]
        );

        $this->add_control(
            'show_difficulty',
            [
                'label' => __('Mostrar Dificuldade', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Sim', 'travelcurator'),
                'label_off' => __('Não', 'travelcurator'),
            ]
        );

        $this->add_control(
            'show_price_range',
            [
                'label' => __('Mostrar Faixa de Preço', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __('Sim', 'travelcurator'),
                'label_off' => __('Não', 'travelcurator'),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Texto do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Buscar Pacotes', 'travelcurator'),
                'placeholder' => __('Digite o texto do botão', 'travelcurator'),
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
                'label' => __('Cor de Fundo do Formulário', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .travel-search-form' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'form_border_color',
            [
                'label' => __('Cor da Borda', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e0e0e0',
                'selectors' => [
                    '{{WRAPPER}} .travel-search-form' => 'border-color: {{VALUE}};',
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
                    'top' => '30',
                    'right' => '30',
                    'bottom' => '30',
                    'left' => '30',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .travel-search-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_color',
            [
                'label' => __('Cor de Fundo do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#0073aa',
                'selectors' => [
                    '{{WRAPPER}} .search-button' => 'background-color: {{VALUE}};',
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
                    '{{WRAPPER}} .search-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $show_category = $settings['show_category'] === 'yes';
        $show_destination = $settings['show_destination'] === 'yes';
        $show_difficulty = $settings['show_difficulty'] === 'yes';
        $show_price_range = $settings['show_price_range'] === 'yes';
        $button_text = !empty($settings['button_text']) ? $settings['button_text'] : __('Buscar Pacotes', 'travelcurator');
        ?>

        <div class="travel-search-filter-widget">
            <form class="travel-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <input type="hidden" name="post_type" value="travel_package">
                
                <div class="search-fields">
                    <?php if ($show_category) : ?>
                        <div class="search-field">
                            <label for="travel_category"><?php _e('Categoria:', 'travelcurator'); ?></label>
                            <?php 
                            $categories = get_terms([
                                'taxonomy' => 'travel_category',
                                'hide_empty' => false,
                            ]);
                            
                            if (!empty($categories) && !is_wp_error($categories)) : ?>
                                <select name="travel_category" id="travel_category">
                                    <option value=""><?php _e('Todas as categorias', 'travelcurator'); ?></option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?php echo esc_attr($category->slug); ?>" <?php selected(get_query_var('travel_category'), $category->slug); ?>>
                                            <?php echo esc_html($category->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($show_destination) : ?>
                        <div class="search-field">
                            <label for="travel_destination"><?php _e('Destino:', 'travelcurator'); ?></label>
                            <?php 
                            $destinations = get_terms([
                                'taxonomy' => 'travel_destination',
                                'hide_empty' => false,
                            ]);
                            
                            if (!empty($destinations) && !is_wp_error($destinations)) : ?>
                                <select name="travel_destination" id="travel_destination">
                                    <option value=""><?php _e('Todos os destinos', 'travelcurator'); ?></option>
                                    <?php foreach ($destinations as $destination) : ?>
                                        <option value="<?php echo esc_attr($destination->slug); ?>" <?php selected(get_query_var('travel_destination'), $destination->slug); ?>>
                                            <?php echo esc_html($destination->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($show_difficulty) : ?>
                        <div class="search-field">
                            <label for="travel_difficulty"><?php _e('Dificuldade:', 'travelcurator'); ?></label>
                            <?php 
                            $difficulties = get_terms([
                                'taxonomy' => 'travel_difficulty',
                                'hide_empty' => false,
                            ]);
                            
                            if (!empty($difficulties) && !is_wp_error($difficulties)) : ?>
                                <select name="travel_difficulty" id="travel_difficulty">
                                    <option value=""><?php _e('Todas as dificuldades', 'travelcurator'); ?></option>
                                    <?php foreach ($difficulties as $difficulty) : ?>
                                        <option value="<?php echo esc_attr($difficulty->slug); ?>" <?php selected(get_query_var('travel_difficulty'), $difficulty->slug); ?>>
                                            <?php echo esc_html($difficulty->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($show_price_range) : ?>
                        <div class="search-field price-range-field">
                            <label><?php _e('Faixa de Preço:', 'travelcurator'); ?></label>
                            <div class="price-inputs">
                                <input type="number" name="min_price" placeholder="<?php esc_attr_e('Preço mín.', 'travelcurator'); ?>" 
                                       value="<?php echo esc_attr(get_query_var('min_price')); ?>" min="0" step="0.01">
                                <span class="price-separator"><?php _e('até', 'travelcurator'); ?></span>
                                <input type="number" name="max_price" placeholder="<?php esc_attr_e('Preço máx.', 'travelcurator'); ?>" 
                                       value="<?php echo esc_attr(get_query_var('max_price')); ?>" min="0" step="0.01">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="search-button">
                        <?php echo esc_html($button_text); ?>
                    </button>
                </div>
            </form>
        </div>

        <style>
        .travel-search-filter-widget .travel-search-form {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .travel-search-filter-widget .search-fields {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .travel-search-filter-widget .search-field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .travel-search-filter-widget .search-field select,
        .travel-search-filter-widget .search-field input[type="number"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .travel-search-filter-widget .search-field select:focus,
        .travel-search-filter-widget .search-field input[type="number"]:focus {
            outline: none;
            border-color: #0073aa;
            box-shadow: 0 0 0 2px rgba(0,115,170,0.1);
        }

        .travel-search-filter-widget .price-inputs {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .travel-search-filter-widget .price-inputs input {
            flex: 1;
        }

        .travel-search-filter-widget .price-separator {
            color: #666;
            font-size: 14px;
            white-space: nowrap;
        }

        .travel-search-filter-widget .search-actions {
            text-align: center;
        }

        .travel-search-filter-widget .search-button {
            background: #0073aa;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .travel-search-filter-widget .search-button:hover {
            background: #005177;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .travel-search-filter-widget .search-fields {
                grid-template-columns: 1fr;
            }
            
            .travel-search-filter-widget .price-inputs {
                flex-direction: column;
                gap: 10px;
            }
            
            .travel-search-filter-widget .price-separator {
                display: none;
            }
        }
        </style>
        <?php
    }

    /**
     * Render widget output in the editor
     */
    protected function content_template() {
        ?>
        <div class="travel-search-filter-widget">
            <form class="travel-search-form">
                <div class="search-fields">
                    <# if ( settings.show_category === 'yes' ) { #>
                    <div class="search-field">
                        <label><?php _e('Categoria:', 'travelcurator'); ?></label>
                        <select>
                            <option><?php _e('Todas as categorias', 'travelcurator'); ?></option>
                        </select>
                    </div>
                    <# } #>
                    
                    <# if ( settings.show_destination === 'yes' ) { #>
                    <div class="search-field">
                        <label><?php _e('Destino:', 'travelcurator'); ?></label>
                        <select>
                            <option><?php _e('Todos os destinos', 'travelcurator'); ?></option>
                        </select>
                    </div>
                    <# } #>
                    
                    <# if ( settings.show_difficulty === 'yes' ) { #>
                    <div class="search-field">
                        <label><?php _e('Dificuldade:', 'travelcurator'); ?></label>
                        <select>
                            <option><?php _e('Todas as dificuldades', 'travelcurator'); ?></option>
                        </select>
                    </div>
                    <# } #>

                    <# if ( settings.show_price_range === 'yes' ) { #>
                    <div class="search-field price-range-field">
                        <label><?php _e('Faixa de Preço:', 'travelcurator'); ?></label>
                        <div class="price-inputs">
                            <input type="number" placeholder="<?php esc_attr_e('Preço mín.', 'travelcurator'); ?>">
                            <span class="price-separator"><?php _e('até', 'travelcurator'); ?></span>
                            <input type="number" placeholder="<?php esc_attr_e('Preço máx.', 'travelcurator'); ?>">
                        </div>
                    </div>
                    <# } #>
                </div>
                
                <div class="search-actions">
                    <button type="button" class="search-button">
                        {{{ settings.button_text || '<?php _e('Buscar Pacotes', 'travelcurator'); ?>' }}}
                    </button>
                </div>
            </form>
        </div>
        <?php
    }
}