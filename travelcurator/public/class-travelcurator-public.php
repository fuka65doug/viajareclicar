<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/public
 */

/**
 * The public-facing functionality of the plugin.
 */
class TravelCurator_Public {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // Register AJAX handlers
        $this->register_ajax_handlers();
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/travelcurator-public.css',
            array(),
            $this->version,
            'all'
        );
        
        // Google Fonts
        wp_enqueue_style(
            'travelcurator-fonts',
            'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap',
            array(),
            null
        );
        
        // Font Awesome para ícones (opcional)
        if (get_option('travelcurator_load_fontawesome', true)) {
            wp_enqueue_style(
                'font-awesome',
                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
                array(),
                '6.4.0'
            );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/travelcurator-public.js',
            array('jquery'),
            $this->version,
            false
        );

        // Localize script
        wp_localize_script($this->plugin_name, 'travelcurator_public', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('travelcurator_public_nonce'),
            'currency_symbol' => get_option('travelcurator_currency_symbol', 'R$'),
            'whatsapp_number' => get_option('travelcurator_whatsapp_number', ''),
            'strings' => array(
                'loading' => __('Carregando...', 'travelcurator'),
                'error' => __('Erro ao processar solicitação.', 'travelcurator'),
                'success' => __('Operação realizada com sucesso!', 'travelcurator'),
                'required_field' => __('Este campo é obrigatório.', 'travelcurator'),
                'invalid_email' => __('Email inválido.', 'travelcurator'),
                'sending' => __('Enviando...', 'travelcurator')
            )
        ));

        // jQuery Mask for phone inputs
        wp_enqueue_script(
            'jquery-mask',
            'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js',
            array('jquery'),
            '1.14.16',
            true
        );
    }

    /**
     * Handle template inclusion.
     */
    public function template_include($template) {
        // Single travel package template
        if (is_singular('travel_package')) {
            $single_template = locate_template('single-travel_package.php');
            if (!$single_template) {
                $single_template = plugin_dir_path(__FILE__) . '../templates/single-travel_package.php';
            }
            if (file_exists($single_template)) {
                return $single_template;
            }
        }
        
        // Archive travel package template
        if (is_post_type_archive('travel_package')) {
            $archive_template = locate_template('archive-travel_package.php');
            if (!$archive_template) {
                $archive_template = plugin_dir_path(__FILE__) . '../templates/archive-travel_package.php';
            }
            if (file_exists($archive_template)) {
                return $archive_template;
            }
        }
        
        return $template;
    }

    /**
     * Add WhatsApp floating button.
     */
    public function add_whatsapp_button() {
        $whatsapp_number = get_option('travelcurator_whatsapp_number');
        if (empty($whatsapp_number)) {
            return;
        }

        $position = get_option('travelcurator_whatsapp_position', 'bottom-right');
        $color = get_option('travelcurator_whatsapp_color', '#25D366');
        
        // Only show on travel package pages or if globally enabled
        $show_button = false;
        if (is_singular('travel_package') || is_post_type_archive('travel_package')) {
            $show_button = true;
        } elseif (get_option('travelcurator_whatsapp_global', false)) {
            $show_button = true;
        }
        
        if (!$show_button) {
            return;
        }
        
        $package_id = is_singular('travel_package') ? get_the_ID() : 0;
        $package_title = is_singular('travel_package') ? get_the_title() : '';
        
        $message = get_option('travelcurator_whatsapp_message', 'Olá! Tenho interesse no pacote {package_title}. Pode me dar mais informações?');
        $message = str_replace('{package_title}', $package_title, $message);
        
        $whatsapp_url = 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp_number) . '?text=' . urlencode($message);
        
        echo '<a href="' . esc_url($whatsapp_url) . '" class="tc-whatsapp-float position-' . esc_attr($position) . ' tc-whatsapp-btn" data-package-id="' . esc_attr($package_id) . '" data-package-title="' . esc_attr($package_title) . '" target="_blank" rel="noopener">';
        echo '<i class="fab fa-whatsapp icon"></i>';
        echo '</a>';
        
        // Add custom styles
        echo '<style>
            .tc-whatsapp-float {
                background: ' . esc_attr($color) . ' !important;
                box-shadow: 0 4px 20px ' . esc_attr($color) . '66 !important;
            }
            .tc-whatsapp-float:hover {
                box-shadow: 0 6px 25px ' . esc_attr($color) . '99 !important;
            }
        </style>';
    }

    /**
     * Register AJAX handlers.
     */
    private function register_ajax_handlers() {
        add_action('wp_ajax_travelcurator_submit_interest', array($this, 'ajax_submit_interest'));
        add_action('wp_ajax_nopriv_travelcurator_submit_interest', array($this, 'ajax_submit_interest'));
        
        add_action('wp_ajax_travelcurator_filter_packages', array($this, 'ajax_filter_packages'));
        add_action('wp_ajax_nopriv_travelcurator_filter_packages', array($this, 'ajax_filter_packages'));
        
        add_action('wp_ajax_travelcurator_whatsapp_click', array($this, 'ajax_whatsapp_click'));
        add_action('wp_ajax_nopriv_travelcurator_whatsapp_click', array($this, 'ajax_whatsapp_click'));
        
        add_action('wp_ajax_travelcurator_track_package_view', array($this, 'ajax_track_package_view'));
        add_action('wp_ajax_nopriv_travelcurator_track_package_view', array($this, 'ajax_track_package_view'));
    }

    /**
     * AJAX handler for submitting interest.
     */
    public function ajax_submit_interest() {
        if (!wp_verify_nonce($_POST['nonce'], 'travelcurator_public_nonce')) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Erro de segurança.', 'travelcurator'))
            )));
        }

        // Validate required fields
        $package_id = absint($_POST['package_id']);
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $message = sanitize_textarea_field($_POST['message']);

        if (empty($package_id) || empty($name) || empty($email)) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Por favor, preencha todos os campos obrigatórios.', 'travelcurator'))
            )));
        }

        if (!is_email($email)) {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Email inválido.', 'travelcurator'))
            )));
        }

        // Check if package exists
        if (get_post_type($package_id) !== 'travel_package') {
            wp_die(json_encode(array(
                'success' => false,
                'data' => array('message' => __('Pacote não encontrado.', 'travelcurator'))
            )));
        }

        // Use leads handler to create lead
        if (class_exists('TravelCurator_Leads')) {
            $leads_handler = new TravelCurator_Leads();
            
            $lead_id = $leads_handler->create_lead(array(
                'package_id' => $package_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'message' => $message,
                'source' => 'frontend_form',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'ip_address' => $this->get_client_ip()
            ));

            if ($lead_id) {
                // Fire action hook
                do_action('travelcurator_new_lead', $lead_id, $package_id);

                wp_die(json_encode(array(
                    'success' => true,
                    'data' => array('message' => __('Obrigado pelo interesse! Entraremos em contato em breve.', 'travelcurator'))
                )));
            }
        }

        wp_die(json_encode(array(
            'success' => false,
            'data' => array('message' => __('Erro interno. Tente novamente.', 'travelcurator'))
        )));
    }

    /**
     * AJAX handler for filtering packages.
     */
    public function ajax_filter_packages() {
        if (!wp_verify_nonce($_POST['nonce'], 'travelcurator_public_nonce')) {
            wp_die(json_encode(array('success' => false)));
        }

        $filters = $_POST['filters'];
        
        // Build query args
        $args = array(
            'post_type' => 'travel_package',
            'post_status' => 'publish',
            'posts_per_page' => get_option('travelcurator_posts_per_page', 12),
            'meta_query' => array(),
            'tax_query' => array()
        );

        // Price filter
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $price_query = array(
                'key' => 'package_price',
                'type' => 'NUMERIC'
            );
            
            if (isset($filters['min_price']) && $filters['min_price'] !== '') {
                $price_query['value'] = array(floatval($filters['min_price']));
                $price_query['compare'] = '>=';
            }
            
            if (isset($filters['max_price']) && $filters['max_price'] !== '') {
                if (isset($price_query['value'])) {
                    $price_query['value'][] = floatval($filters['max_price']);
                    $price_query['compare'] = 'BETWEEN';
                } else {
                    $price_query['value'] = floatval($filters['max_price']);
                    $price_query['compare'] = '<=';
                }
            }
            
            $args['meta_query'][] = $price_query;
        }

        // Duration filter
        if (isset($filters['duration']) && $filters['duration'] !== '') {
            $args['meta_query'][] = array(
                'key' => 'package_duration_days',
                'value' => absint($filters['duration']),
                'compare' => '<='
            );
        }

        // Search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $args['s'] = sanitize_text_field($filters['search']);
        }

        // Taxonomy filters
        $taxonomies = array('travel_category', 'emotional_purpose', 'travel_destination');
        foreach ($taxonomies as $taxonomy) {
            if (isset($filters[$taxonomy]) && !empty($filters[$taxonomy])) {
                $terms = is_array($filters[$taxonomy]) ? $filters[$taxonomy] : array($filters[$taxonomy]);
                $terms = array_map('sanitize_text_field', $terms);
                
                $args['tax_query'][] = array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $terms,
                    'operator' => 'IN'
                );
            }
        }

        // Set tax_query relation
        if (count($args['tax_query']) > 1) {
            $args['tax_query']['relation'] = 'AND';
        }

        // Ordering
        if (isset($filters['orderby'])) {
            switch ($filters['orderby']) {
                case 'price_low':
                    $args['meta_key'] = 'package_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'ASC';
                    break;
                case 'price_high':
                    $args['meta_key'] = 'package_price';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                case 'rating':
                    $args['meta_key'] = 'package_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    break;
                case 'duration':
                    $args['meta_key'] = 'package_duration_days';
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'ASC';
                    break;
                default:
                    $args['orderby'] = 'date';
                    $args['order'] = 'DESC';
            }
        }

        $query = new WP_Query($args);
        
        ob_start();
        if ($query->have_posts()) {
            echo '<div class="tc-packages-grid">';
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_package_card(get_the_ID());
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<div class="tc-no-results">';
            echo '<h3>' . __('Nenhum pacote encontrado', 'travelcurator') . '</h3>';
            echo '<p>' . __('Tente ajustar os filtros para encontrar outros pacotes.', 'travelcurator') . '</p>';
            echo '</div>';
        }
        $html = ob_get_clean();

        wp_die(json_encode(array(
            'success' => true,
            'data' => array(
                'html' => $html,
                'count' => $query->found_posts
            )
        )));
    }

    /**
     * AJAX handler for WhatsApp clicks.
     */
    public function ajax_whatsapp_click() {
        $package_id = absint($_POST['package_id']);
        
        if ($package_id && class_exists('TravelCurator_Leads')) {
            $leads_handler = new TravelCurator_Leads();
            
            // Track the click
            global $wpdb;
            $table = $wpdb->prefix . 'travelcurator_interests';
            
            $wpdb->insert($table, array(
                'package_id' => $package_id,
                'name' => 'WhatsApp Click',
                'email' => '',
                'phone' => '',
                'message' => '',
                'source' => 'whatsapp',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'ip_address' => $this->get_client_ip(),
                'status' => 'tracked'
            ));

            // Fire action hook
            do_action('travelcurator_whatsapp_clicked', $package_id);
        }

        wp_die(json_encode(array('success' => true)));
    }

    /**
     * AJAX handler for tracking package views.
     */
    public function ajax_track_package_view() {
        $package_id = absint($_POST['package_id']);
        
        if ($package_id) {
            // Update view count
            $views = get_post_meta($package_id, 'package_views', true) ?: 0;
            update_post_meta($package_id, 'package_views', $views + 1);
            
            // Fire action hook
            do_action('travelcurator_package_viewed', $package_id);
        }

        wp_die(json_encode(array('success' => true)));
    }

    /**
     * Render package card.
     */
    public function render_package_card($package_id, $style = 'default') {
        $package = get_post($package_id);
        if (!$package || $package->post_type !== 'travel_package') {
            return;
        }

        // Get package data
        $price = get_post_meta($package_id, 'package_price', true);
        $price_display = get_post_meta($package_id, 'package_price_display', true);
        $duration_days = get_post_meta($package_id, 'package_duration_days', true);
        $duration_nights = get_post_meta($package_id, 'package_duration_nights', true);
        $featured = get_post_meta($package_id, 'package_featured', true);
        $rating = get_post_meta($package_id, 'package_rating', true);
        $short_description = get_post_meta($package_id, 'package_short_description', true);
        $highlights = get_post_meta($package_id, 'package_highlights', true) ?: array();
        
        // Get taxonomies
        $purposes = get_the_terms($package_id, 'emotional_purpose');
        $destinations = get_the_terms($package_id, 'travel_destination');
        $categories = get_the_terms($package_id, 'travel_category');
        
        $thumbnail = get_the_post_thumbnail_url($package_id, 'large');
        $permalink = get_permalink($package_id);
        
        $currency_symbol = get_option('travelcurator_currency_symbol', 'R$');
        ?>
        
        <div class="tc-package-card <?php echo esc_attr($style); ?> tc-fade-in-up">
            <?php if ($thumbnail): ?>
            <div class="tc-package-image">
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($package->post_title); ?>" />
                
                <?php if ($featured): ?>
                <div class="tc-package-badge featured">
                    <?php _e('Destaque', 'travelcurator'); ?>
                </div>
                <?php endif; ?>
                
                <?php if ($rating && $rating > 0): ?>
                <div class="tc-package-rating">
                    <span class="stars">⭐</span>
                    <span><?php echo esc_html($rating); ?>/10</span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="tc-package-content">
                <h3 class="tc-package-title">
                    <a href="<?php echo esc_url($permalink); ?>">
                        <?php echo esc_html($package->post_title); ?>
                    </a>
                </h3>
                
                <?php if ($purposes && !is_wp_error($purposes)): ?>
                <div class="tc-package-purpose" style="background-color: <?php echo esc_attr(get_term_meta($purposes[0]->term_id, 'purpose_color', true) ?: '#1A3A5F'); ?>">
                    <?php echo esc_html($purposes[0]->name); ?>
                </div>
                <?php endif; ?>
                
                <?php if ($short_description): ?>
                <p class="tc-package-excerpt"><?php echo esc_html(wp_trim_words($short_description, 20)); ?></p>
                <?php else: ?>
                <p class="tc-package-excerpt"><?php echo esc_html(wp_trim_words($package->post_excerpt ?: $package->post_content, 20)); ?></p>
                <?php endif; ?>
                
                <div class="tc-package-meta">
                    <?php if ($duration_days || $duration_nights): ?>
                    <div class="tc-package-meta-item">
                        <i class="fas fa-clock icon"></i>
                        <?php 
                        if ($duration_days && $duration_nights) {
                            printf(__('%d dias / %d noites', 'travelcurator'), $duration_days, $duration_nights);
                        } elseif ($duration_days) {
                            printf(_n('%d dia', '%d dias', $duration_days, 'travelcurator'), $duration_days);
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($destinations && !is_wp_error($destinations)): ?>
                    <div class="tc-package-meta-item">
                        <i class="fas fa-map-marker-alt icon"></i>
                        <?php echo esc_html($destinations[0]->name); ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($categories && !is_wp_error($categories)): ?>
                    <div class="tc-package-meta-item">
                        <i class="fas fa-users icon"></i>
                        <?php echo esc_html($categories[0]->name); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($highlights) && count($highlights) > 0): ?>
                <div class="tc-package-highlights">
                    <?php foreach (array_slice($highlights, 0, 3) as $highlight): ?>
                    <div class="tc-package-highlight">
                        <?php if (!empty($highlight['icon'])): ?>
                        <i class="<?php echo esc_attr($highlight['icon']); ?> icon"></i>
                        <?php endif; ?>
                        <span><?php echo esc_html($highlight['text']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="tc-package-footer">
                    <?php if ($price && $price_display): ?>
                    <div class="tc-package-price">
                        <span class="currency"><?php echo esc_html($currency_symbol); ?></span><?php echo esc_html(number_format($price, 2, ',', '.')); ?>
                        <small class="period"><?php _e('por pessoa', 'travelcurator'); ?></small>
                    </div>
                    <?php endif; ?>
                    
                    <div class="tc-package-actions">
                        <a href="<?php echo esc_url($permalink); ?>" class="tc-btn tc-btn-small">
                            <?php _e('Ver Detalhes', 'travelcurator'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
    }

    /**
     * Get client IP address.
     */
    private function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field($ip);
    }

    /**
     * Add custom body classes.
     */
    public function add_body_classes($classes) {
        if (is_singular('travel_package')) {
            $classes[] = 'single-travel-package';
        } elseif (is_post_type_archive('travel_package')) {
            $classes[] = 'archive-travel-package';
        }
        
        return $classes;
    }

    /**
     * Modify the main query for travel packages archive.
     */
    public function modify_main_query($query) {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('travel_package')) {
            $posts_per_page = get_option('travelcurator_posts_per_page', 12);
            $query->set('posts_per_page', $posts_per_page);
            
            // Default ordering by featured first, then date
            $query->set('meta_key', 'package_featured');
            $query->set('orderby', array(
                'meta_value' => 'DESC',
                'date' => 'DESC'
            ));
        }
    }

    /**
     * Add custom meta tags for SEO.
     */
    public function add_meta_tags() {
        if (is_singular('travel_package')) {
            $package_id = get_the_ID();
            $description = get_post_meta($package_id, 'package_short_description', true);
            $price = get_post_meta($package_id, 'package_price', true);
            $currency = get_option('travelcurator_currency_symbol', 'R$');
            $image = get_the_post_thumbnail_url($package_id, 'large');
            
            if ($description) {
                echo '<meta name="description" content="' . esc_attr($description) . '" />' . "\n";
            }
            
            // Open Graph tags
            echo '<meta property="og:type" content="product" />' . "\n";
            echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '" />' . "\n";
            if ($description) {
                echo '<meta property="og:description" content="' . esc_attr($description) . '" />' . "\n";
            }
            if ($image) {
                echo '<meta property="og:image" content="' . esc_url($image) . '" />' . "\n";
            }
            
            // Schema.org structured data
            $this->add_schema_markup($package_id);
        }
    }

    /**
     * Add Schema.org structured data.
     */
    private function add_schema_markup($package_id) {
        $package = get_post($package_id);
        $price = get_post_meta($package_id, 'package_price', true);
        $rating = get_post_meta($package_id, 'package_rating', true);
        $reviews_count = get_post_meta($package_id, 'total_reviews', true);
        $currency = 'BRL'; // Brazilian Real
        $image = get_the_post_thumbnail_url($package_id, 'large');
        $description = get_post_meta($package_id, 'package_short_description', true) ?: wp_trim_words($package->post_content, 30);
        
        $schema = array(
            '@context' => 'https://schema.org/',
            '@type' => 'TouristTrip',
            'name' => $package->post_title,
            'description' => $description,
            'url' => get_permalink($package_id),
        );
        
        if ($image) {
            $schema['image'] = $image;
        }
        
        if ($price) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => $currency,
                'availability' => 'https://schema.org/InStock'
            );
        }
        
        if ($rating && $reviews_count) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $rating,
                'ratingCount' => $reviews_count,
                'bestRating' => 10,
                'worstRating' => 1
            );
        }
        
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}