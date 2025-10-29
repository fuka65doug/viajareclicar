<?php
/**
 * Package Grid Widget for Elementor
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/elementor/widgets
 */

class TravelCurator_Package_Grid_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'travelcurator_package_grid';
    }

    public function get_title() {
        return __('Pacotes Grid', 'travelcurator');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['travelcurator'];
    }

    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Conteúdo', 'travelcurator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Número de Pacotes', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Colunas', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Ordenar Por', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => __('Data', 'travelcurator'),
                    'title' => __('Título', 'travelcurator'),
                    'rand' => __('Aleatório', 'travelcurator'),
                    'menu_order' => __('Ordem', 'travelcurator'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Ordem', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => __('Crescente', 'travelcurator'),
                    'DESC' => __('Decrescente', 'travelcurator'),
                ],
            ]
        );

        // Taxonomies
        $destinations = get_terms(['taxonomy' => 'destination', 'hide_empty' => false]);
        $destination_options = [];
        foreach ($destinations as $dest) {
            $destination_options[$dest->term_id] = $dest->name;
        }

        $this->add_control(
            'destination',
            [
                'label' => __('Filtrar por Destino', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $destination_options,
            ]
        );

        $categories = get_terms(['taxonomy' => 'travel_category', 'hide_empty' => false]);
        $category_options = [];
        foreach ($categories as $cat) {
            $category_options[$cat->term_id] = $cat->name;
        }

        $this->add_control(
            'category',
            [
                'label' => __('Filtrar por Categoria', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $category_options,
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
            'card_style',
            [
                'label' => __('Estilo do Card', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Padrão', 'travelcurator'),
                    'overlay' => __('Overlay', 'travelcurator'),
                    'minimal' => __('Minimalista', 'travelcurator'),
                ],
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => __('Mostrar Preço', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_duration',
            [
                'label' => __('Mostrar Duração', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => __('Mostrar Resumo', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $args = [
            'post_type' => 'travel_package',
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
        ];

        // Tax query
        $tax_query = [];
        if (!empty($settings['destination'])) {
            $tax_query[] = [
                'taxonomy' => 'destination',
                'field' => 'term_id',
                'terms' => $settings['destination'],
            ];
        }
        if (!empty($settings['category'])) {
            $tax_query[] = [
                'taxonomy' => 'travel_category',
                'field' => 'term_id',
                'terms' => $settings['category'],
            ];
        }
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            ?>
            <div class="travelcurator-package-grid columns-<?php echo esc_attr($settings['columns']); ?> style-<?php echo esc_attr($settings['card_style']); ?>">
                <?php
                while ($query->have_posts()) :
                    $query->the_post();
                    
                    $price = get_post_meta(get_the_ID(), '_travel_price', true);
                    $duration = get_post_meta(get_the_ID(), '_travel_duration', true);
                    $destinations = get_the_terms(get_the_ID(), 'destination');
                    ?>
                    
                    <div class="package-card">
                        <div class="package-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php 
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium_large');
                                } 
                                ?>
                            </a>
                            <?php if ($settings['show_price'] === 'yes' && $price) : ?>
                                <div class="package-price">
                                    R$ <?php echo number_format($price, 2, ',', '.'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="package-content">
                            <?php if ($destinations) : ?>
                                <div class="package-destination">
                                    <i class="dashicons dashicons-location"></i>
                                    <?php echo esc_html($destinations[0]->name); ?>
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="package-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            
                            <?php if ($settings['show_excerpt'] === 'yes' && has_excerpt()) : ?>
                                <div class="package-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($settings['show_duration'] === 'yes' && $duration) : ?>
                                <div class="package-meta">
                                    <span class="duration">
                                        <i class="dashicons dashicons-clock"></i>
                                        <?php echo esc_html($duration); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>" class="package-button">
                                <?php _e('Ver Detalhes', 'travelcurator'); ?>
                            </a>
                        </div>
                    </div>
                    
                <?php endwhile; ?>
            </div>
            <?php
            wp_reset_postdata();
        else :
            echo '<p>' . __('Nenhum pacote encontrado.', 'travelcurator') . '</p>';
        endif;
    }
}
