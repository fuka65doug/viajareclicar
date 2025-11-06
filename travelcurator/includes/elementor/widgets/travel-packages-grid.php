<?php
/**
 * Travel Packages Grid Widget for Elementor
 *
 * @package TravelCurator
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class TravelCurator_Packages_Grid_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'travelcurator-packages-grid';
    }

    public function get_title() {
        return 'Pacotes de Viagem - Grid';
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['travelcurator'];
    }

    public function get_keywords() {
        return ['travel', 'packages', 'grid', 'viagem', 'pacotes'];
    }

    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Configurações do Grid',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => 'Número de Pacotes',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 20,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => 'Colunas',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1 Coluna',
                    '2' => '2 Colunas',
                    '3' => '3 Colunas',
                    '4' => '4 Colunas',
                ],
            ]
        );

        $this->add_control(
            'show_filters',
            [
                'label' => 'Mostrar Filtros',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Sim',
                'label_off' => 'Não',
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => 'Mostrar Paginação',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Sim',
                'label_off' => 'Não',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        // Query Section
        $this->add_control(
            'query_heading',
            [
                'label' => 'Filtros de Consulta',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'categories',
            [
                'label' => 'Categorias',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_travel_categories(),
            ]
        );

        $this->add_control(
            'destinations',
            [
                'label' => 'Destinos',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_travel_destinations(),
            ]
        );

        $this->add_control(
            'purposes',
            [
                'label' => 'Propósitos Emocionais',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $this->get_travel_purposes(),
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => 'Ordenar por',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => 'Data',
                    'title' => 'Título',
                    'menu_order' => 'Ordem Personalizada',
                    'rand' => 'Aleatório',
                    'meta_value_num' => 'Preço',
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => 'Ordem',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => 'Crescente',
                    'DESC' => 'Decrescente',
                ],
            ]
        );

        $this->end_controls_section();

        // Hero/Header Section
        $this->start_controls_section(
            'hero_section',
            [
                'label' => 'Cabeçalho (Hero)',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                'condition' => [
                    'show_filters' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'hero_title',
            [
                'label' => 'Título do Cabeçalho',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Nossas Experiências Curadas',
                'placeholder' => 'Digite o título',
            ]
        );

        $this->add_control(
            'hero_subtitle',
            [
                'label' => 'Subtítulo do Cabeçalho',
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => 'Cada experiência é cuidadosamente desenhada para despertar emoções específicas e criar memórias duradouras.',
                'placeholder' => 'Digite o subtítulo',
            ]
        );

        $this->add_control(
            'show_hero_title',
            [
                'label' => 'Mostrar Título/Subtítulo',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => 'Sim',
                'label_off' => 'Não',
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Layout Section
        $this->start_controls_section(
            'layout_section',
            [
                'label' => 'Layout',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'container_width',
            [
                'label' => 'Largura dos Cards',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'boxed',
                'options' => [
                    'boxed' => 'Encaixotado (Container)',
                    'full' => 'Largura Total (Full Width)',
                ],
                'description' => 'O Hero sempre respeita o full width. Esta opção controla apenas a grade de cards.',
            ]
        );

        $this->add_responsive_control(
            'container_max_width',
            [
                'label' => 'Largura Máxima (Container)',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 600,
                        'max' => 2000,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 1200,
                ],
                'selectors' => [
                    '{{WRAPPER}} .packages-grid-container.boxed' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
                ],
                'condition' => [
                    'container_width' => 'boxed',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Hero
        $this->start_controls_section(
            'hero_style_section',
            [
                'label' => 'Estilo do Cabeçalho',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_filters' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'hero_background',
                'label' => 'Fundo do Cabeçalho',
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .tc-experiences-header',
                'fields_options' => [
                    'background' => [
                        'default' => 'gradient',
                    ],
                    'color' => [
                        'default' => 'var(--wp--preset--color--primary, #C85F4D)',
                    ],
                    'color_b' => [
                        'default' => 'var(--wp--preset--color--secondary, #D4B254)',
                    ],
                    'gradient_angle' => [
                        'default' => [
                            'unit' => 'deg',
                            'size' => 135,
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'hero_title_color',
            [
                'label' => 'Cor do Título',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .tc-header-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hero_title_typography',
                'label' => 'Tipografia do Título',
                'selector' => '{{WRAPPER}} .tc-header-title',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'hero_title_shadow',
                'label' => 'Sombra do Título',
                'selector' => '{{WRAPPER}} .tc-header-title',
            ]
        );

        $this->add_control(
            'hero_subtitle_color',
            [
                'label' => 'Cor do Subtítulo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255,255,255,0.95)',
                'selectors' => [
                    '{{WRAPPER}} .tc-header-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'hero_subtitle_typography',
                'label' => 'Tipografia do Subtítulo',
                'selector' => '{{WRAPPER}} .tc-header-subtitle',
            ]
        );

        $this->add_responsive_control(
            'hero_padding',
            [
                'label' => 'Espaçamento Interno',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 60,
                    'right' => 30,
                    'bottom' => 60,
                    'left' => 30,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .tc-experiences-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hero_margin',
            [
                'label' => 'Margem Externa',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tc-experiences-header' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'hero_border',
                'label' => 'Borda',
                'selector' => '{{WRAPPER}} .tc-experiences-header',
            ]
        );

        $this->add_responsive_control(
            'hero_border_radius',
            [
                'label' => 'Arredondamento das Bordas',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .tc-experiences-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Filter Pills Style
        $this->add_control(
            'filter_pills_heading',
            [
                'label' => 'Estilo dos Filtros (Pills)',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'filter_pill_bg',
            [
                'label' => 'Cor de Fundo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'rgba(255, 255, 255, 0.95)',
                'selectors' => [
                    '{{WRAPPER}} .tc-purpose-pill' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_pill_color',
            [
                'label' => 'Cor do Texto',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--text, #1A3A5F)',
                'selectors' => [
                    '{{WRAPPER}} .tc-purpose-pill' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_pill_active_bg',
            [
                'label' => 'Cor de Fundo (Ativo)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--accent, #D4B254)',
                'selectors' => [
                    '{{WRAPPER}} .tc-purpose-pill.active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_pill_active_color',
            [
                'label' => 'Cor do Texto (Ativo)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .tc-purpose-pill.active' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'filter_pill_typography',
                'label' => 'Tipografia dos Filtros',
                'selector' => '{{WRAPPER}} .tc-purpose-pill',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'Estilo do Card',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .package-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => 'Box Shadow',
                'selector' => '{{WRAPPER}} .package-card',
                'fields_options' => [
                    'box_shadow_type' => [
                        'default' => 'yes',
                    ],
                    'box_shadow' => [
                        'default' => [
                            'horizontal' => 0,
                            'vertical' => 4,
                            'blur' => 20,
                            'spread' => 0,
                            'color' => 'rgba(0,0,0,0.08)',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Cor do Título',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--heading, #1A3A5F)',
                'selectors' => [
                    '{{WRAPPER}} .package-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'Tipografia do Título',
                'selector' => '{{WRAPPER}} .package-title',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => 'Cor do Preço',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--primary, #1A3A5F)',
                'selectors' => [
                    '{{WRAPPER}} .package-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Badges Section
        $this->add_control(
            'badges_heading',
            [
                'label' => 'Badges',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'purpose_badge_bg',
            [
                'label' => 'Cor do Badge de Propósito',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--accent, #D4B254)',
                'selectors' => [
                    '{{WRAPPER}} .purpose-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'category_badge_bg',
            [
                'label' => 'Cor do Badge de Categoria',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--secondary, #C85F4D)',
                'selectors' => [
                    '{{WRAPPER}} .category-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Button Style - Details Button
        $this->start_controls_section(
            'button_details_style_section',
            [
                'label' => 'Botão "Ver Detalhes"',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'btn_details_bg',
            [
                'label' => 'Cor de Fundo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'transparent',
                'selectors' => [
                    '{{WRAPPER}} .btn-details' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_details_color',
            [
                'label' => 'Cor do Texto',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--primary, #1A3A5F)',
                'selectors' => [
                    '{{WRAPPER}} .btn-details' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'btn_details_typography',
                'label' => 'Tipografia',
                'selector' => '{{WRAPPER}} .btn-details',
            ]
        );

        $this->end_controls_section();

        // Button Style - Interest Button
        $this->start_controls_section(
            'button_interest_style_section',
            [
                'label' => 'Botão "Tenho Interesse"',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'btn_interest_bg',
            [
                'label' => 'Cor de Fundo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => 'var(--wp--preset--color--secondary, #C85F4D)',
                'selectors' => [
                    '{{WRAPPER}} .btn-interest' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                    '{{WRAPPER}} .btn-modal-interest' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'btn_interest_color',
            [
                'label' => 'Cor do Texto',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .btn-interest' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .btn-modal-interest' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'btn_interest_typography',
                'label' => 'Tipografia',
                'selector' => '{{WRAPPER}} .btn-interest, {{WRAPPER}} .btn-modal-interest',
            ]
        );

        $this->end_controls_section();

        // Pagination Style Section
        $this->start_controls_section(
            'pagination_style_section',
            [
                'label' => 'Paginação',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_pagination' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_bg_color',
            [
                'label' => 'Cor de Fundo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_text_color',
            [
                'label' => 'Cor do Texto',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1A3A5F',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_border_color',
            [
                'label' => 'Cor da Borda',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e8e8e8',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_heading',
            [
                'label' => 'Estado Hover',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pagination_hover_bg_color',
            [
                'label' => 'Cor de Fundo (Hover)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1A3A5F',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers:hover:not(.current):not(.dots)' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_text_color',
            [
                'label' => 'Cor do Texto (Hover)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers:hover:not(.current):not(.dots)' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_border_color',
            [
                'label' => 'Cor da Borda (Hover)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1A3A5F',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers:hover:not(.current):not(.dots)' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_heading',
            [
                'label' => 'Estado Ativo',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'pagination_active_bg_color',
            [
                'label' => 'Cor de Fundo (Ativo)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4B254',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers.current' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_text_color',
            [
                'label' => 'Cor do Texto (Ativo)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers.current' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_border_color',
            [
                'label' => 'Cor da Borda (Ativo)',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#D4B254',
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers.current' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_typography_heading',
            [
                'label' => 'Tipografia',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'label' => 'Tipografia',
                'selector' => '{{WRAPPER}} .packages-pagination .page-numbers',
            ]
        );

        $this->add_control(
            'pagination_spacing_heading',
            [
                'label' => 'Espaçamento',
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'pagination_button_width',
            [
                'label' => 'Largura do Botão',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 30,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 45,
                ],
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers' => 'min-width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_button_spacing',
            [
                'label' => 'Espaço Entre Botões',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers' => 'margin: 0 {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_border_radius',
            [
                'label' => 'Arredondamento das Bordas',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination .page-numbers' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_margin',
            [
                'label' => 'Margem da Paginação',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 60,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'pagination_padding',
            [
                'label' => 'Espaçamento Interno da Área',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 40,
                    'right' => 20,
                    'bottom' => 40,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .packages-pagination' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $wp;
        $settings = $this->get_settings_for_display();

        // Get WhatsApp number from settings
        $whatsapp_number = get_option('travelcurator_whatsapp_number', '');

        // Query arguments
        $args = array(
            'post_type' => 'travel_package',
            'posts_per_page' => $settings['posts_per_page'],
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_travelcurator_status',
                    'value' => 'active',
                    'compare' => '='
                )
            )
        );

        // Add taxonomy filters
        $tax_query = array('relation' => 'AND');

        if (!empty($settings['categories'])) {
            $tax_query[] = array(
                'taxonomy' => 'travel_category',
                'field' => 'term_id',
                'terms' => $settings['categories'],
            );
        }

        if (!empty($settings['destinations'])) {
            $tax_query[] = array(
                'taxonomy' => 'travel_destination',
                'field' => 'term_id',
                'terms' => $settings['destinations'],
            );
        }

        if (!empty($settings['purposes'])) {
            $tax_query[] = array(
                'taxonomy' => 'travel_purpose',
                'field' => 'term_id',
                'terms' => $settings['purposes'],
            );
        }

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }

        // Order settings
        $args['orderby'] = $settings['order_by'];
        $args['order'] = $settings['order'];

        if ($settings['order_by'] === 'meta_value_num') {
            $args['meta_key'] = '_travelcurator_price';
        }

        // Pagination
        if ($settings['show_pagination'] === 'yes') {
            $args['paged'] = get_query_var('paged') ? get_query_var('paged') : 1;
        }

        $query = new WP_Query($args);

        // Get all purposes for filter
        $all_purposes = get_terms(array(
            'taxonomy' => 'travel_purpose',
            'hide_empty' => false,
        ));
        ?>

        <div class="travelcurator-packages-grid elementor-widget" data-columns="<?php echo esc_attr($settings['columns']); ?>">

            <?php if ($settings['show_filters'] === 'yes') : ?>
            <!-- NEW HEADER WITH EMOTIONAL PURPOSE FILTERS -->
            <div class="tc-experiences-header">
                <div class="tc-purpose-filters">
                    <button class="tc-purpose-pill active" data-purpose="all">Todas as Experiências</button>
                    <?php
                    if (!is_wp_error($all_purposes) && !empty($all_purposes)) :
                        foreach ($all_purposes as $purpose) :
                    ?>
                        <button class="tc-purpose-pill" data-purpose="<?php echo esc_attr($purpose->slug); ?>">
                            <?php echo esc_html($purpose->name); ?>
                        </button>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
                <?php if ($settings['show_hero_title'] === 'yes') : ?>
                <div class="tc-header-content">
                    <h2 class="tc-header-title"><?php echo esc_html($settings['hero_title']); ?></h2>
                    <p class="tc-header-subtitle"><?php echo esc_html($settings['hero_subtitle']); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="packages-grid-container <?php echo esc_attr($settings['container_width']); ?>">
                <div class="packages-grid columns-<?php echo esc_attr($settings['columns']); ?>">
                <?php if ($query->have_posts()) : ?>
                    <?php while ($query->have_posts()) : $query->the_post();
                        $package_id = get_the_ID();
                        $price = get_post_meta($package_id, '_travelcurator_price', true);
                        $duration = get_post_meta($package_id, '_travelcurator_duration', true);
                        $difficulty = get_post_meta($package_id, '_travelcurator_difficulty', true);
                        $location = get_post_meta($package_id, '_travelcurator_location', true);
                        $highlights = get_post_meta($package_id, '_travelcurator_highlights', true);

                        // Get taxonomies
                        $categories = get_the_terms($package_id, 'travel_category');
                        $purposes = get_the_terms($package_id, 'travel_purpose');

                        $purpose_slug = '';
                        if ($purposes && !is_wp_error($purposes)) {
                            $purpose_slug = $purposes[0]->slug;
                        }

                        // Get icon for purpose (you can customize this)
                        $purpose_icons = array(
                            'reconexao' => '🍷',
                            'celebracao' => '🎉',
                            'descoberta' => '🧭',
                            'transformacao' => '🦋',
                            'descanso' => '🌴'
                        );
                        $purpose_icon = isset($purpose_icons[$purpose_slug]) ? $purpose_icons[$purpose_slug] : '✨';

                        // Translate difficulty to Portuguese
                        $difficulty_translations = array(
                            'easy' => 'Fácil',
                            'moderate' => 'Moderado',
                            'difficult' => 'Difícil',
                            'hard' => 'Difícil',
                            'extreme' => 'Extremo',
                            'challenging' => 'Desafiador'
                        );
                        $difficulty_pt = isset($difficulty_translations[strtolower($difficulty)])
                            ? $difficulty_translations[strtolower($difficulty)]
                            : ucfirst($difficulty);
                    ?>
                    <!-- NEW CARD DESIGN -->
                    <article class="package-card" data-purpose="<?php echo esc_attr($purpose_slug); ?>">
                        <div class="card-image">
                            <?php
                            $thumbnail_id = get_post_thumbnail_id($package_id);
                            if ($thumbnail_id) :
                                $image_url = wp_get_attachment_image_url($thumbnail_id, 'large');
                                $image_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                                if (empty($image_alt)) {
                                    $image_alt = get_the_title();
                                }
                            ?>
                                <img src="<?php echo esc_url($image_url); ?>"
                                     alt="<?php echo esc_attr($image_alt); ?>"
                                     class="package-featured-image"
                                     loading="lazy">
                            <?php else : ?>
                                <div class="package-placeholder-image">
                                    <span class="placeholder-icon">📸</span>
                                    <span class="placeholder-text">Sem imagem</span>
                                </div>
                                <!-- Centered Icon (only in placeholder) -->
                                <div class="card-icon"><?php echo $purpose_icon; ?></div>
                            <?php endif; ?>

                            <!-- Purpose Badge (Top-Left) -->
                            <?php if ($purposes && !is_wp_error($purposes)) : ?>
                                <div class="purpose-badge"><?php echo esc_html($purposes[0]->name); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="card-content">
                            <!-- Category Label (Orange Badge) -->
                            <?php if ($categories && !is_wp_error($categories)) : ?>
                                <span class="category-badge"><?php echo esc_html($categories[0]->name); ?></span>
                            <?php endif; ?>

                            <!-- Title -->
                            <h3 class="package-title">
                                <a href="javascript:void(0);" onclick="openPackageModal(<?php echo $package_id; ?>)">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <!-- Description -->
                            <div class="package-description">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>

                            <!-- Meta Info -->
                            <div class="package-meta-info">
                                <div class="package-duration">
                                    <?php if ($duration) : ?>
                                        ⏱️ <?php echo esc_html($duration); ?>
                                    <?php endif; ?>
                                    <?php if ($difficulty) : ?>
                                        <span class="package-difficulty" style="margin-left: 10px;">
                                            🏔️ <?php echo esc_html($difficulty_pt); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="package-price">
                                    <?php if (!empty($price) && $price > 0): ?>
                                        A partir de R$ <?php echo number_format((float)$price, 0, ',', '.'); ?>
                                    <?php else: ?>
                                        Sob Consulta
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="card-buttons">
                                <button class="btn-details" onclick="openPackageModal(<?php echo $package_id; ?>)">
                                    Ver Detalhes
                                </button>
                                <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Olá! Tenho interesse no pacote: ' . get_the_title()); ?>"
                                   class="btn-interest" target="_blank" rel="noopener">
                                    Tenho Interesse
                                </a>
                            </div>
                        </div>
                    </article>
                    <?php endwhile; ?>
                <?php else : ?>
                    <div class="no-packages">
                        <div class="no-packages-icon">😔</div>
                        <h3 class="no-packages-title">Nenhuma experiência encontrada</h3>
                        <p class="no-packages-text">Não encontramos pacotes para este propósito emocional no momento.</p>
                        <p class="no-packages-cta">Mas podemos criar uma experiência personalizada para você!</p>
                        <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Olá! Gostaria de uma experiência personalizada. Podem me ajudar?'); ?>"
                           class="btn-whatsapp-cta" target="_blank" rel="noopener">
                            💬 Fale Conosco no WhatsApp
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($settings['show_pagination'] === 'yes' && $query->max_num_pages > 1) : ?>
            <div class="packages-pagination">
                <?php
                // Get the current page
                $paged = max(1, get_query_var('paged'));

                // Build pagination arguments
                $pagination_args = array(
                    'total' => $query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => '← Anterior',
                    'next_text' => 'Próxima →',
                    'type' => 'plain',
                    'end_size' => 1,
                    'mid_size' => 2,
                );

                // Check if using pretty permalinks
                if (get_option('permalink_structure')) {
                    // Using pretty permalinks - use /page/X/ format
                    $current_url = home_url(add_query_arg(array(), $wp->request));
                    if (empty($wp->request)) {
                        $current_url = home_url('/');
                    } else {
                        $current_url = home_url($wp->request);
                    }

                    // Remove any existing page number from URL
                    $current_url = preg_replace('/\/page\/\d+\/?/', '/', $current_url);
                    $current_url = trailingslashit($current_url);

                    $pagination_args['base'] = $current_url . 'page/%#%/';
                    $pagination_args['format'] = '';
                } else {
                    // Using query strings - use ?paged=X format
                    $pagination_args['base'] = add_query_arg('paged', '%#%');
                    $pagination_args['format'] = '';
                }

                echo paginate_links($pagination_args);
                ?>
            </div>
            <?php endif; ?>
            </div><!-- .packages-grid-container -->

            <!-- MODAL POPUPS FOR EACH PACKAGE -->
            <?php
            if ($query->have_posts()) :
                $query->rewind_posts();
                while ($query->have_posts()) : $query->the_post();
                    $package_id = get_the_ID();
                    $price = get_post_meta($package_id, '_travelcurator_price', true);
                    $duration = get_post_meta($package_id, '_travelcurator_duration', true);
                    $difficulty = get_post_meta($package_id, '_travelcurator_difficulty', true);
                    $highlights = get_post_meta($package_id, '_travelcurator_highlights', true);

                    // Translate difficulty to Portuguese
                    $difficulty_translations = array(
                        'easy' => 'Fácil',
                        'moderate' => 'Moderado',
                        'difficult' => 'Difícil',
                        'hard' => 'Difícil',
                        'extreme' => 'Extremo',
                        'challenging' => 'Desafiador'
                    );
                    $difficulty_pt = isset($difficulty_translations[strtolower($difficulty)])
                        ? $difficulty_translations[strtolower($difficulty)]
                        : ucfirst($difficulty);

                    // Get taxonomies
                    $categories = get_the_terms($package_id, 'travel_category');

                    // Parse highlights - expect newline-separated list
                    $highlights_array = array();
                    if (!empty($highlights)) {
                        $highlights_array = array_filter(explode("\n", $highlights));
                    }

                    // Default highlights if none set
                    if (empty($highlights_array)) {
                        $highlights_array = array(
                            'Hospedagem em acomodação premium',
                            'Passeios e experiências exclusivas',
                            'Guia especializado em português',
                            'Traslados inclusos',
                            'Seguro viagem completo'
                        );
                    }
            ?>
            <div class="tc-modal-overlay" id="tc-modal-<?php echo $package_id; ?>">
                <div class="tc-modal">
                    <button class="tc-modal-close" onclick="document.getElementById('tc-modal-<?php echo $package_id; ?>').classList.remove('active'); document.body.style.overflow = '';">×</button>
                    <div class="tc-modal-content">
                        <h2 class="tc-modal-title"><?php the_title(); ?></h2>

                        <?php if ($categories && !is_wp_error($categories)) : ?>
                            <span class="tc-modal-category"><?php echo esc_html($categories[0]->name); ?></span>
                        <?php endif; ?>

                        <div class="tc-modal-description">
                            <?php the_content(); ?>
                        </div>

                        <div class="tc-modal-highlights">
                            <h4>Destaques da Experiência:</h4>
                            <ul class="tc-highlights-list">
                                <?php foreach ($highlights_array as $highlight) : ?>
                                    <li><?php echo esc_html(trim($highlight)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="tc-modal-footer">
                            <div class="tc-modal-meta">
                                <div class="tc-modal-duration">
                                    <?php if ($duration) : ?>
                                        ⏱️ <?php echo esc_html($duration); ?>
                                    <?php endif; ?>
                                    <?php if ($difficulty) : ?>
                                        <span style="margin-left: 10px;">
                                            🏔️ <?php echo esc_html($difficulty_pt); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="tc-modal-price">
                                    <?php if (!empty($price) && $price > 0): ?>
                                        A partir de R$ <?php echo number_format((float)$price, 0, ',', '.'); ?>
                                    <?php else: ?>
                                        Sob Consulta
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="tc-modal-buttons">
                                <button class="btn-modal-close" onclick="document.getElementById('tc-modal-<?php echo $package_id; ?>').classList.remove('active'); document.body.style.overflow = '';">
                                    Fechar
                                </button>
                                <a href="https://wa.me/<?php echo esc_attr($whatsapp_number); ?>?text=<?php echo urlencode('Olá! Tenho interesse no pacote: ' . get_the_title()); ?>"
                                   class="btn-modal-interest" target="_blank" rel="noopener">
                                    Tenho Interesse
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                endwhile;
            endif;
            ?>
        </div>

        <?php wp_reset_postdata(); ?>

        <script>
        // Modal functionality
        function openPackageModal(packageId) {
            const modal = document.getElementById('tc-modal-' + packageId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        // Pass WhatsApp number to JavaScript
        var travelcuratorData = {
            whatsappNumber: '<?php echo esc_js($whatsapp_number); ?>'
        };
        </script>

        <?php
    }

    private function get_travel_categories() {
        $categories = get_terms(array(
            'taxonomy' => 'travel_category',
            'hide_empty' => false,
            'fields' => 'all',
        ));

        $options = array();
        if (!is_wp_error($categories) && !empty($categories)) {
            foreach ($categories as $category) {
                $options[$category->term_id] = $category->name;
            }
        }

        return $options;
    }

    private function get_travel_destinations() {
        $destinations = get_terms(array(
            'taxonomy' => 'travel_destination',
            'hide_empty' => false,
            'fields' => 'all',
        ));

        $options = array();
        if (!is_wp_error($destinations) && !empty($destinations)) {
            foreach ($destinations as $destination) {
                $options[$destination->term_id] = $destination->name;
            }
        }

        return $options;
    }

    private function get_travel_purposes() {
        $purposes = get_terms(array(
            'taxonomy' => 'travel_purpose',
            'hide_empty' => false,
            'fields' => 'all',
        ));

        $options = array();
        if (!is_wp_error($purposes) && !empty($purposes)) {
            foreach ($purposes as $purpose) {
                $options[$purpose->term_id] = $purpose->name;
            }
        }

        return $options;
    }
}