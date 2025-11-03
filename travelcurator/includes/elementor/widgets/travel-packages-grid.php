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
                'default' => '#1A3A5F',
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
                'fields_options' => [
                    'font_size' => [
                        'default' => [
                            'size' => 20,
                            'unit' => 'px',
                        ],
                    ],
                    'font_weight' => [
                        'default' => '700',
                    ],
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => 'Cor do Preço',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F2B705',
                'selectors' => [
                    '{{WRAPPER}} .price-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Button Style
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => 'Estilo do Botão',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_bg_color',
            [
                'label' => 'Cor de Fundo',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#F2B705',
                'selectors' => [
                    '{{WRAPPER}} .btn-primary' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => 'Cor do Texto',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .btn-primary' => 'color: {{VALUE}};',
                ],
            ]
        );

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
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 25,
                ],
                'selectors' => [
                    '{{WRAPPER}} .btn-primary' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
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
                <div class="tc-header-content">
                    <h2 class="tc-header-title">Nossas Experiências Curadas</h2>
                    <p class="tc-header-subtitle">Cada experiência é cuidadosamente desenhada para despertar emoções específicas e criar memórias duradouras.</p>
                </div>
            </div>
            <?php endif; ?>

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
                    ?>
                    <!-- NEW CARD DESIGN -->
                    <article class="package-card" data-purpose="<?php echo esc_attr($purpose_slug); ?>">
                        <div class="card-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large'); ?>
                            <?php else : ?>
                                <div style="width:100%;height:100%;background:#e8e8e8;display:flex;align-items:center;justify-content:center;">
                                    <span style="font-size:48px;color:#ccc;">📸</span>
                                </div>
                            <?php endif; ?>

                            <!-- Purpose Badge (Top-Left) -->
                            <?php if ($purposes && !is_wp_error($purposes)) : ?>
                                <div class="purpose-badge"><?php echo esc_html($purposes[0]->name); ?></div>
                            <?php endif; ?>

                            <!-- Centered Icon -->
                            <div class="card-icon"><?php echo $purpose_icon; ?></div>
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
                                    <?php echo esc_html($duration ? $duration : '7 dias / 6 noites'); ?>
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
                        <p>Nenhum pacote encontrado.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($settings['show_pagination'] === 'yes' && $query->max_num_pages > 1) : ?>
            <div class="packages-pagination">
                <?php
                echo paginate_links(array(
                    'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                    'total' => $query->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'format' => '?paged=%#%',
                    'prev_text' => '← Anterior',
                    'next_text' => 'Próxima →',
                ));
                ?>
            </div>
            <?php endif; ?>

            <!-- MODAL POPUPS FOR EACH PACKAGE -->
            <?php
            if ($query->have_posts()) :
                $query->rewind_posts();
                while ($query->have_posts()) : $query->the_post();
                    $package_id = get_the_ID();
                    $price = get_post_meta($package_id, '_travelcurator_price', true);
                    $duration = get_post_meta($package_id, '_travelcurator_duration', true);
                    $highlights = get_post_meta($package_id, '_travelcurator_highlights', true);

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
                                    <?php echo esc_html($duration ? $duration : '7 dias / 6 noites'); ?>
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