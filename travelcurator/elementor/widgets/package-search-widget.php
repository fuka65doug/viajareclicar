<?php
/**
 * Package Search Widget for Elementor
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/elementor/widgets
 */

class TravelCurator_Package_Search_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'travelcurator_package_search';
    }

    public function get_title() {
        return __('Busca de Pacotes', 'travelcurator');
    }

    public function get_icon() {
        return 'eicon-search';
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
            'title',
            [
                'label' => __('Título', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Encontre sua próxima viagem', 'travelcurator'),
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label' => __('Subtítulo', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Busque entre centenas de destinos incríveis', 'travelcurator'),
            ]
        );

        $this->add_control(
            'search_style',
            [
                'label' => __('Estilo', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'inline',
                'options' => [
                    'inline' => __('Em linha', 'travelcurator'),
                    'stacked' => __('Empilhado', 'travelcurator'),
                ],
            ]
        );

        $this->add_control(
            'show_destination_filter',
            [
                'label' => __('Filtro de Destino', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_category_filter',
            [
                'label' => __('Filtro de Categoria', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_date_filter',
            [
                'label' => __('Filtro de Data', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price_filter',
            [
                'label' => __('Filtro de Preço', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => __('Texto do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Buscar Pacotes', 'travelcurator'),
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
                'default' => '#f8f9fa',
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
            'button_color',
            [
                'label' => __('Cor do Botão', 'travelcurator'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007bff',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $destinations = get_terms(['taxonomy' => 'destination', 'hide_empty' => false]);
        $categories = get_terms(['taxonomy' => 'travel_category', 'hide_empty' => false]);
        
        $style = 'background-color: ' . esc_attr($settings['background_color']) . '; color: ' . esc_attr($settings['text_color']) . ';';
        ?>
        
        <div class="travelcurator-search-widget style-<?php echo esc_attr($settings['search_style']); ?>" style="<?php echo $style; ?>">
            <div class="search-header">
                <?php if ($settings['title']) : ?>
                    <h2 class="search-title"><?php echo esc_html($settings['title']); ?></h2>
                <?php endif; ?>
                
                <?php if ($settings['subtitle']) : ?>
                    <p class="search-subtitle"><?php echo esc_html($settings['subtitle']); ?></p>
                <?php endif; ?>
            </div>
            
            <form class="package-search-form" action="<?php echo esc_url(get_post_type_archive_link('travel_package')); ?>" method="get">
                
                <div class="search-fields">
                    
                    <?php if ($settings['show_destination_filter'] === 'yes') : ?>
                        <div class="search-field">
                            <label for="search-destination">
                                <i class="dashicons dashicons-location"></i>
                                <?php _e('Destino', 'travelcurator'); ?>
                            </label>
                            <select id="search-destination" name="destination">
                                <option value=""><?php _e('Todos os destinos', 'travelcurator'); ?></option>
                                <?php foreach ($destinations as $destination) : ?>
                                    <option value="<?php echo esc_attr($destination->slug); ?>">
                                        <?php echo esc_html($destination->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_category_filter'] === 'yes') : ?>
                        <div class="search-field">
                            <label for="search-category">
                                <i class="dashicons dashicons-category"></i>
                                <?php _e('Categoria', 'travelcurator'); ?>
                            </label>
                            <select id="search-category" name="category">
                                <option value=""><?php _e('Todas as categorias', 'travelcurator'); ?></option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo esc_attr($category->slug); ?>">
                                        <?php echo esc_html($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_date_filter'] === 'yes') : ?>
                        <div class="search-field">
                            <label for="search-date">
                                <i class="dashicons dashicons-calendar"></i>
                                <?php _e('Data', 'travelcurator'); ?>
                            </label>
                            <input type="date" id="search-date" name="date" min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($settings['show_price_filter'] === 'yes') : ?>
                        <div class="search-field">
                            <label for="search-price">
                                <i class="dashicons dashicons-money-alt"></i>
                                <?php _e('Preço máximo', 'travelcurator'); ?>
                            </label>
                            <input type="number" id="search-price" name="max_price" placeholder="R$" min="0" step="100">
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="search-button" style="background-color: <?php echo esc_attr($settings['button_color']); ?>;">
                        <i class="dashicons dashicons-search"></i>
                        <?php echo esc_html($settings['button_text']); ?>
                    </button>
                </div>
                
            </form>
        </div>
        
        <?php
    }
}
