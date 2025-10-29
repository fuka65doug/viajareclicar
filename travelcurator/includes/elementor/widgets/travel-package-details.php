<?php
/**
 * Travel Package Details Widget for Elementor
 *
 * @package TravelCurator
 */

if (!defined('ABSPATH')) {
    exit;
}

class TravelCurator_Package_Details_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'travelcurator-package-details';
    }

    public function get_title() {
        return 'Detalhes do Pacote';
    }

    public function get_icon() {
        return 'eicon-product-info';
    }

    public function get_categories() {
        return ['travelcurator'];
    }

    public function get_keywords() {
        return ['travel', 'package', 'details', 'viagem', 'pacote', 'detalhes'];
    }

    protected function register_controls() {

        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => 'Configurações',
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'package_id',
            [
                'label' => 'ID do Pacote',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_travel_packages(),
                'description' => 'Selecione um pacote específico ou deixe em branco para usar o pacote atual',
            ]
        );

        $this->add_control(
            'show_elements',
            [
                'label' => 'Mostrar Elementos',
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'default' => ['title', 'price', 'description', 'meta', 'gallery', 'itinerary'],
                'options' => [
                    'title' => 'Título',
                    'price' => 'Preço',
                    'description' => 'Descrição',
                    'meta' => 'Informações Meta',
                    'gallery' => 'Galeria',
                    'itinerary' => 'Roteiro',
                    'accommodation' => 'Hospedagem',
                    'includes' => 'O que está incluso',
                    'excludes' => 'O que não está incluso',
                    'button' => 'Botão de Ação',
                    'reviews' => 'Avaliações',
                ],
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => 'Layout',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'vertical',
                'options' => [
                    'vertical' => 'Vertical',
                    'horizontal' => 'Horizontal',
                    'cards' => 'Em Cards',
                    'tabs' => 'Em Abas',
                ],
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => 'Texto do Botão',
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'Solicitar Orçamento',
                'condition' => [
                    'show_elements' => 'button',
                ],
            ]
        );

        $this->add_control(
            'button_action',
            [
                'label' => 'Ação do Botão',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'modal',
                'options' => [
                    'modal' => 'Abrir Modal de Lead',
                    'whatsapp' => 'Abrir WhatsApp',
                    'email' => 'Abrir E-mail',
                    'phone' => 'Ligar',
                    'link' => 'Link Personalizado',
                ],
                'condition' => [
                    'show_elements' => 'button',
                ],
            ]
        );

        $this->add_control(
            'custom_link',
            [
                'label' => 'Link Personalizado',
                'type' => \Elementor\Controls_Manager::URL,
                'condition' => [
                    'show_elements' => 'button',
                    'button_action' => 'link',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => 'Estilo Geral',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'section_spacing',
            [
                'label' => 'Espaçamento entre Seções',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .detail-section:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'section_background',
            [
                'label' => 'Cor de Fundo das Seções',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .detail-section' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'section_padding',
            [
                'label' => 'Padding das Seções',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 20,
                    'right' => 20,
                    'bottom' => 20,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .detail-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'section_border_radius',
            [
                'label' => 'Border Radius das Seções',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 8,
                    'right' => 8,
                    'bottom' => 8,
                    'left' => 8,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .detail-section' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'section_shadow',
                'label' => 'Sombra das Seções',
                'selector' => '{{WRAPPER}} .detail-section',
            ]
        );

        $this->end_controls_section();

        // Typography Section
        $this->start_controls_section(
            'typography_section',
            [
                'label' => 'Tipografia',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => 'Cor do Título',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1A3A5F',
                'selectors' => [
                    '{{WRAPPER}} .package-title' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_elements' => 'title',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => 'Tipografia do Título',
                'selector' => '{{WRAPPER}} .package-title',
                'condition' => [
                    'show_elements' => 'title',
                ],
            ]
        );

        $this->add_control(
            'section_title_color',
            [
                'label' => 'Cor dos Títulos das Seções',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#1A3A5F',
                'selectors' => [
                    '{{WRAPPER}} .section-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'section_title_typography',
                'label' => 'Tipografia dos Títulos das Seções',
                'selector' => '{{WRAPPER}} .section-title',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => 'Cor do Preço',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F2B705',
                'selectors' => [
                    '{{WRAPPER}} .package-price .price-value' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_elements' => 'price',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => 'Tipografia do Preço',
                'selector' => '{{WRAPPER}} .package-price .price-value',
                'condition' => [
                    'show_elements' => 'price',
                ],
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => 'Cor do Conteúdo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => [
                    '{{WRAPPER}} .detail-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => 'Tipografia do Conteúdo',
                'selector' => '{{WRAPPER}} .detail-content',
            ]
        );

        $this->end_controls_section();

        // Button Style Section
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => 'Estilo do Botão',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_elements' => 'button',
                ],
            ]
        );

        $this->start_controls_tabs('button_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            [
                'label' => 'Normal',
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => 'Cor de Fundo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F2B705',
                'selectors' => [
                    '{{WRAPPER}} .package-action-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => 'Cor do Texto',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .package-action-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => 'Borda',
                'selector' => '{{WRAPPER}} .package-action-button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            [
                'label' => 'Hover',
            ]
        );

        $this->add_control(
            'button_hover_bg_color',
            [
                'label' => 'Cor de Fundo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#e6a500',
                'selectors' => [
                    '{{WRAPPER}} .package-action-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_text_color',
            [
                'label' => 'Cor do Texto',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .package-action-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => 'Cor da Borda',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .package-action-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'button_border_radius',
            [
                'label' => 'Border Radius',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 25,
                ],
                'selectors' => [
                    '{{WRAPPER}} .package-action-button' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => 'Padding',
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'default' => [
                    'top' => 15,
                    'right' => 30,
                    'bottom' => 15,
                    'left' => 30,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .package-action-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => 'Tipografia',
                'selector' => '{{WRAPPER}} .package-action-button',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Get package ID
        $package_id = !empty($settings['package_id']) ? $settings['package_id'] : get_the_ID();
        
        if (!$package_id || get_post_type($package_id) !== 'travel_package') {
            echo '<div class="elementor-alert elementor-alert-warning">Por favor, selecione um pacote válido.</div>';
            return;
        }

        // Get package data
        $package = get_post($package_id);
        $price = get_post_meta($package_id, '_travelcurator_price', true);
        $duration = get_post_meta($package_id, '_travelcurator_duration', true);
        $difficulty = get_post_meta($package_id, '_travelcurator_difficulty', true);
        $location = get_post_meta($package_id, '_travelcurator_location', true);
        $start_date = get_post_meta($package_id, '_travelcurator_start_date', true);
        $end_date = get_post_meta($package_id, '_travelcurator_end_date', true);
        $gallery = get_post_meta($package_id, '_travelcurator_gallery', true);
        $itinerary = get_post_meta($package_id, '_travelcurator_itinerary', true);
        $hotel_name = get_post_meta($package_id, '_travelcurator_hotel_name', true);
        $hotel_stars = get_post_meta($package_id, '_travelcurator_hotel_stars', true);
        $hotel_desc = get_post_meta($package_id, '_travelcurator_hotel_description', true);
        $include_items = get_post_meta($package_id, '_travelcurator_include_items', true);
        $exclude_items = get_post_meta($package_id, '_travelcurator_exclude_items', true);
        
        $show_elements = $settings['show_elements'];
        $layout_class = 'layout-' . $settings['layout'];
        ?>

        <div class="travelcurator-package-details <?php echo esc_attr($layout_class); ?>">
            
            <?php if ($settings['layout'] === 'tabs') : ?>
            <div class="package-tabs-navigation">
                <?php if (in_array('title', $show_elements)) : ?>
                <button class="tab-button active" data-tab="overview">Visão Geral</button>
                <?php endif; ?>
                <?php if (in_array('itinerary', $show_elements) && !empty($itinerary)) : ?>
                <button class="tab-button" data-tab="itinerary">Roteiro</button>
                <?php endif; ?>
                <?php if (in_array('accommodation', $show_elements) && $hotel_name) : ?>
                <button class="tab-button" data-tab="accommodation">Hospedagem</button>
                <?php endif; ?>
                <?php if (in_array('gallery', $show_elements) && !empty($gallery)) : ?>
                <button class="tab-button" data-tab="gallery">Galeria</button>
                <?php endif; ?>
                <?php if (in_array('includes', $show_elements) && (!empty($include_items) || !empty($exclude_items))) : ?>
                <button class="tab-button" data-tab="includes">Incluso/Não Incluso</button>
                <?php endif; ?>
                <?php if (in_array('reviews', $show_elements)) : ?>
                <button class="tab-button" data-tab="reviews">Avaliações</button>
                <?php endif; ?>
            </div>

            <div class="package-tabs-content">
                <div class="tab-content active" id="overview">
            <?php endif; ?>

            <?php if (in_array('title', $show_elements)) : ?>
            <div class="detail-section title-section">
                <h2 class="package-title"><?php echo esc_html($package->post_title); ?></h2>
            </div>
            <?php endif; ?>

            <?php if (in_array('price', $show_elements) && $price) : ?>
            <div class="detail-section price-section">
                <div class="package-price">
                    <span class="price-label">A partir de</span>
                    <span class="price-value">R$ <?php echo number_format($price, 2, ',', '.'); ?></span>
                    <span class="price-per">por pessoa</span>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array('meta', $show_elements)) : ?>
            <div class="detail-section meta-section">
                <h3 class="section-title"><i class="fas fa-info-circle"></i> Informações Rápidas</h3>
                <div class="package-meta-grid">
                    <?php if ($location) : ?>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Destino</strong>
                            <span><?php echo esc_html($location); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($duration) : ?>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Duração</strong>
                            <span><?php echo esc_html($duration); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($difficulty) : ?>
                    <div class="meta-item">
                        <i class="fas fa-signal"></i>
                        <div>
                            <strong>Dificuldade</strong>
                            <span class="difficulty-<?php echo esc_attr($difficulty); ?>">
                                <?php echo ucfirst($difficulty); ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($start_date && $end_date) : ?>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <div>
                            <strong>Período</strong>
                            <span><?php echo date('d/m/Y', strtotime($start_date)) . ' - ' . date('d/m/Y', strtotime($end_date)); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php
                    // Show taxonomies
                    $purposes = get_the_terms($package_id, 'travel_purpose');
                    if ($purposes && !is_wp_error($purposes)) :
                    ?>
                    <div class="meta-item">
                        <i class="fas fa-heart"></i>
                        <div>
                            <strong>Propósito Emocional</strong>
                            <div class="purpose-tags">
                                <?php foreach ($purposes as $purpose) : ?>
                                <span class="purpose-tag"><?php echo esc_html($purpose->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php
                    $amenities = get_the_terms($package_id, 'travel_amenity');
                    if ($amenities && !is_wp_error($amenities)) :
                    ?>
                    <div class="meta-item">
                        <i class="fas fa-star"></i>
                        <div>
                            <strong>Comodidades</strong>
                            <div class="amenities-tags">
                                <?php foreach ($amenities as $amenity) : ?>
                                <span class="amenity-tag"><?php echo esc_html($amenity->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array('description', $show_elements)) : ?>
            <div class="detail-section description-section">
                <h3 class="section-title"><i class="fas fa-file-alt"></i> Sobre este Pacote</h3>
                <div class="package-description detail-content">
                    <?php echo apply_filters('the_content', $package->post_content); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($settings['layout'] === 'tabs') : ?>
                </div> <!-- End overview tab -->
            <?php endif; ?>

            <?php if (in_array('gallery', $show_elements) && !empty($gallery)) : ?>
            <div class="detail-section gallery-section <?php echo $settings['layout'] === 'tabs' ? 'tab-content' : ''; ?>" <?php echo $settings['layout'] === 'tabs' ? 'id="gallery"' : ''; ?>>
                <h3 class="section-title"><i class="fas fa-images"></i> Galeria de Fotos</h3>
                <div class="package-gallery">
                    <?php foreach ($gallery as $image_id) : 
                        $image = wp_get_attachment_image_src($image_id, 'large');
                        $thumb = wp_get_attachment_image_src($image_id, 'medium');
                        if ($image && $thumb) :
                    ?>
                    <div class="gallery-item" onclick="openLightbox('<?php echo esc_url($image[0]); ?>')">
                        <img src="<?php echo esc_url($thumb[0]); ?>" alt="<?php echo esc_attr($package->post_title); ?>" loading="lazy">
                        <div class="gallery-overlay">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array('itinerary', $show_elements) && !empty($itinerary)) : ?>
            <div class="detail-section itinerary-section <?php echo $settings['layout'] === 'tabs' ? 'tab-content' : ''; ?>" <?php echo $settings['layout'] === 'tabs' ? 'id="itinerary"' : ''; ?>>
                <h3 class="section-title"><i class="fas fa-route"></i> Roteiro Detalhado</h3>
                <div class="itinerary-timeline">
                    <?php foreach ($itinerary as $day) : ?>
                    <div class="itinerary-day">
                        <div class="day-number">
                            <span>Dia</span>
                            <strong><?php echo esc_html($day['day']); ?></strong>
                        </div>
                        <div class="day-content">
                            <h4><?php echo esc_html($day['title']); ?></h4>
                            <div class="detail-content">
                                <p><?php echo wp_kses_post($day['description']); ?></p>
                                <?php if (!empty($day['activities'])) : ?>
                                <ul class="activities-list">
                                    <?php foreach ($day['activities'] as $activity) : ?>
                                    <li><i class="fas fa-check"></i> <?php echo esc_html($activity); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array('accommodation', $show_elements) && $hotel_name) : ?>
            <div class="detail-section accommodation-section <?php echo $settings['layout'] === 'tabs' ? 'tab-content' : ''; ?>" <?php echo $settings['layout'] === 'tabs' ? 'id="accommodation"' : ''; ?>>
                <h3 class="section-title"><i class="fas fa-bed"></i> Hospedagem</h3>
                <div class="accommodation-card">
                    <div class="hotel-header">
                        <h4><?php echo esc_html($hotel_name); ?></h4>
                        <?php if ($hotel_stars) : ?>
                        <div class="hotel-stars">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <i class="fas fa-star <?php echo $i <= $hotel_stars ? 'active' : 'inactive'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($hotel_desc) : ?>
                    <div class="detail-content">
                        <p><?php echo wp_kses_post($hotel_desc); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array('includes', $show_elements) && (!empty($include_items) || !empty($exclude_items))) : ?>
            <div class="detail-section includes-section <?php echo $settings['layout'] === 'tabs' ? 'tab-content' : ''; ?>" <?php echo $settings['layout'] === 'tabs' ? 'id="includes"' : ''; ?>>
                <div class="includes-grid">
                    <?php if (!empty($include_items)) : ?>
                    <div class="includes-column">
                        <h3 class="section-title"><i class="fas fa-check-circle text-success"></i> O que está incluso</h3>
                        <ul class="include-list detail-content">
                            <?php foreach ($include_items as $item) : ?>
                            <li><i class="fas fa-check"></i> <?php echo esc_html($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($exclude_items)) : ?>
                    <div class="excludes-column">
                        <h3 class="section-title"><i class="fas fa-times-circle text-warning"></i> O que não está incluso</h3>
                        <ul class="exclude-list detail-content">
                            <?php foreach ($exclude_items as $item) : ?>
                            <li><i class="fas fa-times"></i> <?php echo esc_html($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array('reviews', $show_elements)) : ?>
            <div class="detail-section reviews-section <?php echo $settings['layout'] === 'tabs' ? 'tab-content' : ''; ?>" <?php echo $settings['layout'] === 'tabs' ? 'id="reviews"' : ''; ?>>
                <h3 class="section-title"><i class="fas fa-star"></i> Avaliações</h3>
                <?php echo $this->render_reviews($package_id); ?>
            </div>
            <?php endif; ?>

            <?php if ($settings['layout'] === 'tabs') : ?>
            </div> <!-- End tabs content -->
            <?php endif; ?>

            <?php if (in_array('button', $show_elements)) : ?>
            <div class="detail-section button-section">
                <?php echo $this->render_action_button($settings, $package_id); ?>
            </div>
            <?php endif; ?>

        </div>

        <!-- Lightbox -->
        <div id="packageLightbox" class="travelcurator-lightbox" onclick="closeLightbox()" style="display: none;">
            <span class="lightbox-close">&times;</span>
            <img id="lightboxImage" class="lightbox-content">
        </div>

        <script>
        function openLightbox(imageSrc) {
            document.getElementById('packageLightbox').style.display = 'block';
            document.getElementById('lightboxImage').src = imageSrc;
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('packageLightbox').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Tabs functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    const targetContent = document.getElementById(tabId);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });
        });
        </script>

        <?php
    }

    private function render_action_button($settings, $package_id) {
        $button_text = $settings['button_text'] ? $settings['button_text'] : 'Solicitar Orçamento';
        $button_action = $settings['button_action'];
        
        $onclick = '';
        $href = '#';
        
        switch ($button_action) {
            case 'modal':
                $onclick = "openLeadModal({$package_id})";
                break;
            case 'whatsapp':
                $settings_option = get_option('travelcurator_settings', array());
                $whatsapp_number = isset($settings_option['whatsapp_number']) ? $settings_option['whatsapp_number'] : '';
                $whatsapp_message = isset($settings_option['whatsapp_message']) ? $settings_option['whatsapp_message'] : 'Olá, tenho interesse no pacote: ' . get_the_title($package_id);
                $href = 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp_number) . '?text=' . urlencode($whatsapp_message);
                break;
            case 'email':
                $settings_option = get_option('travelcurator_settings', array());
                $contact_email = isset($settings_option['contact_email']) ? $settings_option['contact_email'] : get_option('admin_email');
                $subject = 'Interesse no pacote: ' . get_the_title($package_id);
                $href = 'mailto:' . $contact_email . '?subject=' . urlencode($subject);
                break;
            case 'phone':
                $settings_option = get_option('travelcurator_settings', array());
                $contact_phone = isset($settings_option['contact_phone']) ? $settings_option['contact_phone'] : '';
                $href = 'tel:' . preg_replace('/\D/', '', $contact_phone);
                break;
            case 'link':
                $custom_link = $settings['custom_link'];
                $href = !empty($custom_link['url']) ? $custom_link['url'] : '#';
                break;
        }

        $target = ($button_action === 'link' && !empty($settings['custom_link']['is_external'])) ? 'target="_blank"' : '';
        $onclick_attr = $onclick ? 'onclick="' . esc_attr($onclick) . '"' : '';

        return '<a href="' . esc_url($href) . '" class="package-action-button" ' . $target . ' ' . $onclick_attr . '>' .
               '<i class="fas fa-paper-plane"></i> ' . esc_html($button_text) . '</a>';
    }

    private function render_reviews($package_id) {
        global $wpdb;
        
        $reviews_table = $wpdb->prefix . 'travelcurator_reviews';
        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $reviews_table 
             WHERE package_id = %d AND status = 'approved' 
             ORDER BY created_at DESC LIMIT 10",
            $package_id
        ));

        if (empty($reviews)) {
            return '<div class="no-reviews detail-content"><p>Ainda não há avaliações para este pacote.</p></div>';
        }

        // Calculate average rating
        $total_rating = 0;
        foreach ($reviews as $review) {
            $total_rating += $review->rating;
        }
        $average_rating = round($total_rating / count($reviews), 1);

        $output = '<div class="reviews-summary">';
        $output .= '<div class="average-rating">';
        $output .= '<div class="rating-number">' . $average_rating . '</div>';
        $output .= '<div class="rating-stars">';
        for ($i = 1; $i <= 5; $i++) {
            $class = $i <= $average_rating ? 'fas fa-star active' : 'fas fa-star inactive';
            $output .= '<i class="' . $class . '"></i>';
        }
        $output .= '</div>';
        $output .= '<div class="rating-count">Baseado em ' . count($reviews) . ' avaliações</div>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= '<div class="reviews-list">';
        foreach ($reviews as $review) {
            $output .= '<div class="review-item">';
            $output .= '<div class="review-header">';
            $output .= '<div class="reviewer-name">' . esc_html($review->reviewer_name) . '</div>';
            $output .= '<div class="review-rating">';
            for ($i = 1; $i <= 5; $i++) {
                $class = $i <= $review->rating ? 'fas fa-star active' : 'fas fa-star inactive';
                $output .= '<i class="' . $class . '"></i>';
            }
            $output .= '</div>';
            $output .= '<div class="review-date">' . date('d/m/Y', strtotime($review->created_at)) . '</div>';
            $output .= '</div>';
            
            if ($review->title) {
                $output .= '<div class="review-title">' . esc_html($review->title) . '</div>';
            }
            
            if ($review->content) {
                $output .= '<div class="review-content detail-content">' . wp_kses_post($review->content) . '</div>';
            }
            
            if ($review->verified) {
                $output .= '<div class="review-verified"><i class="fas fa-check-circle"></i> Avaliação verificada</div>';
            }
            
            $output .= '</div>';
        }
        $output .= '</div>';

        return $output;
    }

    private function get_travel_packages() {
        $packages = get_posts(array(
            'post_type' => 'travel_package',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));

        $options = array('' => 'Pacote Atual');
        foreach ($packages as $package) {
            $options[$package->ID] = $package->post_title;
        }

        return $options;
    }
}