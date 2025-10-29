<?php
/**
 * Package Carousel Widget for Elementor
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/elementor/widgets
 */

class TravelCurator_Package_Carousel_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'travelcurator_package_carousel';
    }

    public function get_title() {
        return __('Pacotes Carrossel', 'travelcurator');
    }

    public function get_icon() {
        return 'eicon-posts-carousel';
    }

    public function get_categories() {
        return ['travelcurator'];
    }

    public function get_script_depends() {
        return ['jquery', 'slick'];
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
                'default' => 8,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'slides_to_show',
            [
                'label' => __('Slides Visíveis', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 6,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Velocidade do Autoplay (ms)', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'infinite',
            [
                'label' => __('Loop Infinito', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_arrows',
            [
                'label' => __('Mostrar Setas', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_dots',
            [
                'label' => __('Mostrar Pontos', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Filter Section
        $this->start_controls_section(
            'filter_section',
            [
                'label' => __('Filtros', 'travelcurator'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
                    'meta_value_num' => __('Preço', 'travelcurator'),
                ],
            ]
        );

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
            'show_price',
            [
                'label' => __('Mostrar Preço', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_destination',
            [
                'label' => __('Mostrar Destino', 'travelcurator'),
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
            'order' => 'DESC',
        ];

        if ($settings['orderby'] === 'meta_value_num') {
            $args['meta_key'] = '_travel_price';
        }

        if (!empty($settings['destination'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'destination',
                    'field' => 'term_id',
                    'terms' => $settings['destination'],
                ],
            ];
        }

        $query = new WP_Query($args);

        if ($query->have_posts()) :
            $carousel_id = 'carousel-' . $this->get_id();
            ?>
            <div class="travelcurator-package-carousel" id="<?php echo esc_attr($carousel_id); ?>">
                <?php
                while ($query->have_posts()) :
                    $query->the_post();
                    
                    $price = get_post_meta(get_the_ID(), '_travel_price', true);
                    $duration = get_post_meta(get_the_ID(), '_travel_duration', true);
                    $destinations = get_the_terms(get_the_ID(), 'destination');
                    ?>
                    
                    <div class="carousel-slide">
                        <div class="package-card">
                            <div class="package-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php 
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('large');
                                    } 
                                    ?>
                                </a>
                            </div>
                            
                            <div class="package-content">
                                <?php if ($settings['show_destination'] === 'yes' && $destinations) : ?>
                                    <div class="package-destination">
                                        <i class="dashicons dashicons-location"></i>
                                        <?php echo esc_html($destinations[0]->name); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <h3 class="package-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                
                                <?php if ($duration) : ?>
                                    <div class="package-duration">
                                        <i class="dashicons dashicons-clock"></i>
                                        <?php echo esc_html($duration); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($settings['show_price'] === 'yes' && $price) : ?>
                                    <div class="package-price">
                                        <span class="price-label"><?php _e('A partir de', 'travelcurator'); ?></span>
                                        <span class="price-value">R$ <?php echo number_format($price, 2, ',', '.'); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="<?php the_permalink(); ?>" class="package-button">
                                    <?php _e('Ver Pacote', 'travelcurator'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                <?php endwhile; ?>
            </div>
            
            <script>
            jQuery(document).ready(function($) {
                $('#<?php echo esc_js($carousel_id); ?>').slick({
                    slidesToShow: <?php echo intval($settings['slides_to_show']); ?>,
                    slidesToScroll: 1,
                    autoplay: <?php echo $settings['autoplay'] === 'yes' ? 'true' : 'false'; ?>,
                    autoplaySpeed: <?php echo intval($settings['autoplay_speed']); ?>,
                    infinite: <?php echo $settings['infinite'] === 'yes' ? 'true' : 'false'; ?>,
                    arrows: <?php echo $settings['show_arrows'] === 'yes' ? 'true' : 'false'; ?>,
                    dots: <?php echo $settings['show_dots'] === 'yes' ? 'true' : 'false'; ?>,
                    responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 2
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            });
            </script>
            <?php
            wp_reset_postdata();
        else :
            echo '<p>' . __('Nenhum pacote encontrado.', 'travelcurator') . '</p>';
        endif;
    }
}
