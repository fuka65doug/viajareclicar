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
        ?>

        <div class="travelcurator-packages-grid elementor-widget" data-columns="<?php echo esc_attr($settings['columns']); ?>">
            
            <?php if ($settings['show_filters'] === 'yes') : ?>
            <div class="packages-filters">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">Todos</button>
                    <?php
                    $categories = get_terms(array('taxonomy' => 'travel_category', 'hide_empty' => true));
                    foreach ($categories as $category) :
                    ?>
                    <button class="filter-tab" data-filter="<?php echo esc_attr($category->slug); ?>">
                        <?php echo esc_html($category->name); ?>
                    </button>
                    <?php endforeach; ?>
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
                        
                        // Get categories for filtering
                        $categories = get_the_terms($package_id, 'travel_category');
                        $category_classes = '';
                        if ($categories && !is_wp_error($categories)) {
                            foreach ($categories as $category) {
                                $category_classes .= ' filter-' . $category->slug;
                            }
                        }
                    ?>
                    <article class="package-card<?php echo esc_attr($category_classes); ?>">
                        <div class="card-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'card-img')); ?>
                                <?php else : ?>
                                    <div class="placeholder-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                            
                            <?php if ($categories && !is_wp_error($categories)) : ?>
                            <div class="package-labels">
                                <span class="category-label"><?php echo esc_html($categories[0]->name); ?></span>
                            </div>
                            <?php endif; ?>

                            <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $package_id; ?>)">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>

                        <div class="card-content">
                            <h3 class="package-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <div class="package-location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo esc_html($location); ?>
                            </div>

                            <div class="package-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </div>

                            <div class="package-meta">
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo esc_html($duration); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-signal"></i>
                                    <span class="difficulty-<?php echo esc_attr($difficulty); ?>">
                                        <?php echo ucfirst($difficulty); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="package-price">
                                    <span class="price-label">A partir de</span>
                                    <span class="price-value">R$ <?php echo number_format((float)$price, 2, ',', '.'); ?></span>
                                    <span class="price-per">por pessoa</span>
                                </div>
                                <button class="btn btn-primary" onclick="openLeadModal(<?php echo $package_id; ?>)">
                                    <i class="fas fa-paper-plane"></i> Solicitar
                                </button>
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
                    'prev_text' => '<i class="fas fa-chevron-left"></i>',
                    'next_text' => '<i class="fas fa-chevron-right"></i>',
                ));
                ?>
            </div>
            <?php endif; ?>
        </div>

        <?php wp_reset_postdata(); ?>

        <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.filter-tab');
            const packageCards = document.querySelectorAll('.package-card');

            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs
                    filterTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');

                    packageCards.forEach(card => {
                        if (filter === 'all' || card.classList.contains('filter-' + filter)) {
                            card.style.display = 'block';
                            card.style.animation = 'fadeInUp 0.5s ease';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });

        // Wishlist functionality
        function toggleWishlist(packageId) {
            const btn = event.target.closest('.wishlist-btn');
            const icon = btn.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.add('active');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('active');
            }

            // Save to localStorage
            let wishlist = JSON.parse(localStorage.getItem('travelcurator_wishlist') || '[]');
            const index = wishlist.indexOf(packageId);
            
            if (index === -1) {
                wishlist.push(packageId);
            } else {
                wishlist.splice(index, 1);
            }
            
            localStorage.setItem('travelcurator_wishlist', JSON.stringify(wishlist));
        }

        // Lead modal functionality
        function openLeadModal(packageId) {
            // This would open the lead modal - implementation depends on your modal system
            if (typeof window.openLeadModal === 'function') {
                window.openLeadModal(packageId);
            }
        }
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