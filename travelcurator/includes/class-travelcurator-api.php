<?php
/**
 * REST API functionality
 */
class TravelCurator_API {

    const NAMESPACE = 'travelcurator/v1';

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
        add_action('rest_api_init', array($this, 'add_custom_fields_to_api'));
    }

    /**
     * Register REST API routes.
     */
    public function register_routes() {
        // Packages routes
        register_rest_route(self::NAMESPACE, '/packages', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_packages'),
            'permission_callback' => '__return_true',
            'args' => $this->get_package_query_args()
        ));

        register_rest_route(self::NAMESPACE, '/packages/(?P<id>\d+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_package'),
            'permission_callback' => '__return_true',
            'args' => array('id' => array('validate_callback' => function($param) { return is_numeric($param); }))
        ));

        // Filtered packages
        register_rest_route(self::NAMESPACE, '/packages/filter', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'filter_packages'),
            'permission_callback' => '__return_true',
            'args' => $this->get_filter_args()
        ));

        // Submit interest
        register_rest_route(self::NAMESPACE, '/interest', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'submit_interest'),
            'permission_callback' => '__return_true',
            'args' => array(
                'package_id' => array('required' => true, 'type' => 'integer', 'validate_callback' => function($param) { return is_numeric($param) && $param > 0; }),
                'name' => array('required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field'),
                'email' => array('required' => true, 'type' => 'string', 'validate_callback' => function($param) { return is_email($param); }),
                'phone' => array('type' => 'string', 'sanitize_callback' => 'sanitize_text_field'),
                'message' => array('type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field')
            )
        ));

        // Track WhatsApp click
        register_rest_route(self::NAMESPACE, '/whatsapp-click', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'track_whatsapp_click'),
            'permission_callback' => '__return_true',
            'args' => array(
                'package_id' => array('required' => true, 'type' => 'integer', 'validate_callback' => function($param) { return is_numeric($param) && $param > 0; })
            )
        ));

        // Taxonomies
        register_rest_route(self::NAMESPACE, '/taxonomies/(?P<taxonomy>[a-zA-Z_]+)', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_taxonomy_terms'),
            'permission_callback' => '__return_true',
            'args' => array('taxonomy' => array('required' => true, 'validate_callback' => function($param) { return taxonomy_exists($param); }))
        ));

        // Stats (admin only)
        register_rest_route(self::NAMESPACE, '/stats', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_statistics'),
            'permission_callback' => function() { return current_user_can('manage_options'); }
        ));
    }

    /**
     * Get packages.
     */
    public function get_packages($request) {
        $params = $request->get_params();
        $args = array(
            'post_type' => 'travel_package',
            'post_status' => 'publish',
            'posts_per_page' => isset($params['per_page']) ? absint($params['per_page']) : 12,
            'paged' => isset($params['page']) ? absint($params['page']) : 1,
            'orderby' => isset($params['orderby']) ? sanitize_text_field($params['orderby']) : 'date',
            'order' => isset($params['order']) ? strtoupper($params['order']) : 'DESC'
        );

        if (isset($params['status']) && $params['status']) {
            $args['meta_query'] = array(array('key' => 'package_status', 'value' => sanitize_text_field($params['status']), 'compare' => '='));
        }

        if (isset($params['featured']) && $params['featured'] === 'true') {
            $args['meta_query'] = isset($args['meta_query']) ? $args['meta_query'] : array();
            $args['meta_query'][] = array('key' => 'package_featured', 'value' => '1', 'compare' => '=');
        }

        $query = new WP_Query($args);
        $packages = array();
        foreach ($query->posts as $post) {
            $packages[] = $this->prepare_package_for_response($post);
        }

        $response = rest_ensure_response($packages);
        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);
        return $response;
    }

    /**
     * Get single package.
     */
    public function get_package($request) {
        $id = $request['id'];
        $post = get_post($id);
        if (!$post || $post->post_type !== 'travel_package') {
            return new WP_Error('not_found', __('Pacote não encontrado.', 'travelcurator'), array('status' => 404));
        }
        $this->track_package_view($id);
        return rest_ensure_response($this->prepare_package_for_response($post, true));
    }

    /**
     * Submit interest via API.
     */
    public function submit_interest($request) {
        $params = $request->get_params();
        $package = get_post($params['package_id']);
        if (!$package || $package->post_type !== 'travel_package') {
            return new WP_Error('invalid_package', __('Pacote não encontrado.', 'travelcurator'), array('status' => 400));
        }

        if (class_exists('TravelCurator_Leads')) {
            $leads = new TravelCurator_Leads();
            $lead_id = $leads->create_lead(array(
                'package_id' => $params['package_id'],
                'name' => $params['name'],
                'email' => $params['email'],
                'phone' => $params['phone'] ?? '',
                'message' => $params['message'] ?? '',
                'source' => 'api',
                'user_agent' => $request->get_header('User-Agent') ?? '',
                'ip_address' => $this->get_client_ip()
            ));

            if ($lead_id) {
                do_action('travelcurator_new_lead', $lead_id, $params['package_id']);
                return rest_ensure_response(array(
                    'success' => true,
                    'message' => __('Interesse registrado com sucesso!', 'travelcurator'),
                    'lead_id' => $lead_id
                ));
            }
        }
        return new WP_Error('submission_failed', __('Erro ao processar interesse.', 'travelcurator'), array('status' => 500));
    }

    /**
     * Track WhatsApp click via API.
     */
    public function track_whatsapp_click($request) {
        $package_id = $request['package_id'];
        $package = get_post($package_id);
        if (!$package || $package->post_type !== 'travel_package') {
            return new WP_Error('invalid_package', __('Pacote não encontrado.', 'travelcurator'), array('status' => 400));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'travelcurator_interests';
        $result = $wpdb->insert($table, array(
            'package_id' => $package_id,
            'name' => 'WhatsApp Click',
            'email' => '',
            'phone' => '',
            'message' => '',
            'source' => 'whatsapp',
            'user_agent' => $request->get_header('User-Agent') ?? '',
            'ip_address' => $this->get_client_ip(),
            'status' => 'tracked'
        ));

        if ($result) {
            do_action('travelcurator_whatsapp_clicked', $package_id);
            return rest_ensure_response(array('success' => true, 'message' => __('Click registrado.', 'travelcurator')));
        }
        return new WP_Error('tracking_failed', __('Erro ao rastrear click.', 'travelcurator'), array('status' => 500));
    }

    /**
     * Get taxonomy terms.
     */
    public function get_taxonomy_terms($request) {
        $taxonomy = $request['taxonomy'];
        $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC'));
        if (is_wp_error($terms)) return $terms;

        $formatted_terms = array();
        foreach ($terms as $term) {
            $formatted_terms[] = array(
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'description' => $term->description,
                'count' => $term->count,
                'color' => get_term_meta($term->term_id, 'purpose_color', true),
                'image' => get_term_meta($term->term_id, 'destination_image', true)
            );
        }
        return rest_ensure_response($formatted_terms);
    }

    /**
     * Get statistics (admin only).
     */
    public function get_statistics($request) {
        $stats = array();
        $package_stats = wp_count_posts('travel_package');
        $stats['total_packages'] = $package_stats->publish;

        if (class_exists('TravelCurator_Leads')) {
            $leads = new TravelCurator_Leads();
            $lead_stats = $leads->get_statistics();
            $stats = array_merge($stats, $lead_stats);
        }
        return rest_ensure_response($stats);
    }

    /**
     * Prepare package for response.
     */
    private function prepare_package_for_response($post, $detailed = false) {
        $package = array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'slug' => $post->post_name,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'status' => $post->post_status,
            'date' => $post->post_date,
            'modified' => $post->post_modified,
            'link' => get_permalink($post->ID),
            'featured_image' => get_the_post_thumbnail_url($post->ID, 'large'),
            'meta' => array(
                'price' => get_post_meta($post->ID, 'package_price', true),
                'price_display' => get_post_meta($post->ID, 'package_price_display', true),
                'duration_days' => get_post_meta($post->ID, 'package_duration_days', true),
                'duration_nights' => get_post_meta($post->ID, 'package_duration_nights', true),
                'status' => get_post_meta($post->ID, 'package_status', true),
                'featured' => get_post_meta($post->ID, 'package_featured', true),
                'max_people' => get_post_meta($post->ID, 'package_max_people', true),
                'short_description' => get_post_meta($post->ID, 'package_short_description', true),
                'rating' => get_post_meta($post->ID, 'package_rating', true),
                'total_reviews' => get_post_meta($post->ID, 'total_reviews', true)
            ),
            'taxonomies' => array(
                'categories' => wp_get_post_terms($post->ID, 'travel_category'),
                'purposes' => wp_get_post_terms($post->ID, 'travel_purpose'),
                'destinations' => wp_get_post_terms($post->ID, 'travel_destination'),
                'facilities' => wp_get_post_terms($post->ID, 'travel_facilities')
            )
        );

        if ($detailed) {
            $package['meta']['highlights'] = get_post_meta($post->ID, 'package_highlights', true);
            $package['meta']['itinerary'] = get_post_meta($post->ID, 'package_itinerary', true);
            $package['meta']['inclusions'] = get_post_meta($post->ID, 'package_inclusions', true);
            $package['meta']['exclusions'] = get_post_meta($post->ID, 'package_exclusions', true);
            $package['meta']['additional_info'] = get_post_meta($post->ID, 'package_additional_info', true);
            $package['meta']['gallery'] = get_post_meta($post->ID, 'package_gallery', true);
            $package['meta']['video_url'] = get_post_meta($post->ID, 'package_video_url', true);
            $package['meta']['virtual_tour'] = get_post_meta($post->ID, 'package_virtual_tour', true);
            $package['meta']['available_dates'] = get_post_meta($post->ID, 'available_dates', true);
            $package['accommodation'] = array(
                'name' => get_post_meta($post->ID, 'accommodation_name', true),
                'type' => get_post_meta($post->ID, 'accommodation_type', true),
                'stars' => get_post_meta($post->ID, 'accommodation_stars', true),
                'description' => get_post_meta($post->ID, 'accommodation_description', true)
            );
        }
        return $package;
    }

    /**
     * Get package query args.
     */
    private function get_package_query_args() {
        return array(
            'per_page' => array('description' => 'Items per page', 'type' => 'integer', 'default' => 12, 'minimum' => 1, 'maximum' => 100),
            'page' => array('description' => 'Current page', 'type' => 'integer', 'default' => 1, 'minimum' => 1),
            'orderby' => array('description' => 'Order by', 'type' => 'string', 'enum' => array('date', 'title', 'price', 'rating'), 'default' => 'date'),
            'order' => array('description' => 'Order direction', 'type' => 'string', 'enum' => array('ASC', 'DESC'), 'default' => 'DESC'),
            'status' => array('description' => 'Package status', 'type' => 'string', 'enum' => array('active', 'inactive', 'coming_soon')),
            'featured' => array('description' => 'Featured packages only', 'type' => 'string', 'enum' => array('true', 'false'))
        );
    }

    /**
     * Get filter args.
     */
    private function get_filter_args() {
        return array(
            'min_price' => array('description' => 'Minimum price', 'type' => 'number'),
            'max_price' => array('description' => 'Maximum price', 'type' => 'number'),
            'duration_days' => array('description' => 'Duration in days', 'type' => 'integer'),
            'travel_category' => array('description' => 'Category IDs', 'type' => 'array'),
            'travel_purpose' => array('description' => 'Purpose IDs', 'type' => 'array'),
            'travel_destination' => array('description' => 'Destination IDs', 'type' => 'array'),
            'search' => array('description' => 'Search term', 'type' => 'string')
        );
    }

    /**
     * Track package view.
     */
    private function track_package_view($package_id) {
        $views = get_post_meta($package_id, 'package_views', true) ?: 0;
        update_post_meta($package_id, 'package_views', $views + 1);
        do_action('travelcurator_package_viewed', $package_id);
    }

    /**
     * Get client IP.
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
     * Add custom fields to API.
     */
    public function add_custom_fields_to_api() {
        register_rest_field('travel_package', 'travelcurator_meta', array(
            'get_callback' => array($this, 'get_package_meta_for_api'),
            'schema' => array(
                'description' => 'TravelCurator package metadata',
                'type' => 'object'
            )
        ));
    }

    /**
     * Get package meta for API.
     */
    public function get_package_meta_for_api($post) {
        return array(
            'price' => get_post_meta($post['id'], 'package_price', true),
            'duration_days' => get_post_meta($post['id'], 'package_duration_days', true),
            'duration_nights' => get_post_meta($post['id'], 'package_duration_nights', true),
            'featured' => get_post_meta($post['id'], 'package_featured', true),
            'rating' => get_post_meta($post['id'], 'package_rating', true)
        );
    }
}