<?php
/**
 * Featured Package Widget for Elementor
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/elementor/widgets
 */

class TravelCurator_Featured_Package_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'travelcurator_featured_package';
    }

    public function get_title() {
        return __('Pacote em Destaque', 'travelcurator');
    }

    public function get_icon() {
        return 'eicon-featured-image';
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

        // Get all travel packages
        $packages = get_posts([
            'post_type' => 'travel_package',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);

        $package_options = [];
        foreach ($packages as $package) {
            $package_options[$package->ID] = $package->post_title;
        }

        $this->add_control(
            'package_id',
            [
                'label' => __('Selecione o Pacote', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $package_options,
                'default' => !empty($package_options) ? array_key_first($package_options) : '',
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => __('Layout', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'side-by-side',
                'options' => [
                    'side-by-side' => __('Lado a Lado', 'travelcurator'),
                    'overlay' => __('Overlay', 'travelcurator'),
                    'card' => __('Card', 'travelcurator'),
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
            'show_destination',
            [
                'label' => __('Mostrar Destino', 'travelcurator'),
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

        $this->add_control(
            'excerpt_length',
            [
                'label' => __('Tamanho do Resumo (palavras)', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 30,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_highlights',
            [
                'label' => __('Mostrar Destaques', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Texto do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Ver Pacote Completo', 'travelcurator'),
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
            'background_color',
            [
                'label' => __('Cor de Fundo', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Cor do Texto', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
            ]
        );

        $this->add_control(
            'accent_color',
            [
                'label' => __('Cor de Destaque', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007bff',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if (empty($settings['package_id'])) {
            echo '<p>' . __('Por favor, selecione um pacote.', 'travelcurator') . '</p>';
            return;
        }

        $package = get_post($settings['package_id']);
        
        if (!$package) {
            echo '<p>' . __('Pacote não encontrado.', 'travelcurator') . '</p>';
            return;
        }

        // Get meta data
        $price = get_post_meta($package->ID, '_travel_price', true);
        $duration = get_post_meta($package->ID, '_travel_duration', true);
        $difficulty = get_post_meta($package->ID, '_travel_difficulty', true);
        $highlights = get_post_meta($package->ID, '_travel_highlights', true);
        $destinations = get_the_terms($package->ID, 'destination');
        
        $style = 'background-color: ' . esc_attr($settings['background_color']) . '; color: ' . esc_attr($settings['text_color']) . ';';
        ?>
        
        <div class="travelcurator-featured-package layout-<?php echo esc_attr($settings['layout']); ?>" style="<?php echo $style; ?>">
            
            <div class="featured-image">
                <?php if (has_post_thumbnail($package->ID)) : ?>
                    <?php echo get_the_post_thumbnail($package->ID, 'large'); ?>
                <?php endif; ?>
                
                <?php if ($difficulty) : ?>
                    <span class="difficulty-badge difficulty-<?php echo esc_attr($difficulty); ?>">
                        <?php echo esc_html(ucfirst($difficulty)); ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="featured-content">
                
                <?php if ($settings['show_destination'] === 'yes' && $destinations) : ?>
                    <div class="package-destination" style="color: <?php echo esc_attr($settings['accent_color']); ?>;">
                        <i class="dashicons dashicons-location"></i>
                        <?php echo esc_html($destinations[0]->name); ?>
                    </div>
                <?php endif; ?>
                
                <h2 class="package-title">
                    <?php echo esc_html($package->post_title); ?>
                </h2>
                
                <div class="package-meta">
                    <?php if ($settings['show_duration'] === 'yes' && $duration) : ?>
                        <span class="meta-item">
                            <i class="dashicons dashicons-clock"></i>
                            <?php echo esc_html($duration); ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_price'] === 'yes' && $price) : ?>
                        <span class="meta-item price" style="color: <?php echo esc_attr($settings['accent_color']); ?>;">
                            <i class="dashicons dashicons-money-alt"></i>
                            R$ <?php echo number_format($price, 2, ',', '.'); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($settings['show_excerpt'] === 'yes') : ?>
                    <div class="package-excerpt">
                        <?php 
                        if ($package->post_excerpt) {
                            echo wp_trim_words($package->post_excerpt, $settings['excerpt_length']);
                        } else {
                            echo wp_trim_words($package->post_content, $settings['excerpt_length']);
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($settings['show_highlights'] === 'yes' && $highlights) : ?>
                    <div class="package-highlights">
                        <h3><?php _e('Destaques:', 'travelcurator'); ?></h3>
                        <?php
                        $highlights_array = explode("\n", $highlights);
                        $highlights_array = array_filter(array_map('trim', $highlights_array));
                        if (!empty($highlights_array)) :
                        ?>
                            <ul>
                                <?php foreach (array_slice($highlights_array, 0, 5) as $highlight) : ?>
                                    <li><?php echo esc_html($highlight); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="package-actions">
                    <a href="<?php echo get_permalink($package->ID); ?>" class="featured-button" style="background-color: <?php echo esc_attr($settings['accent_color']); ?>;">
                        <?php echo esc_html($settings['button_text']); ?>
                        <i class="dashicons dashicons-arrow-right-alt2"></i>
                    </a>
                </div>
                
            </div>
            
        </div>
        
        <?php
    }
}
