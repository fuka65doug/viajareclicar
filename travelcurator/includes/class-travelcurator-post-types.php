<?php
/**
 * Register all custom post types for the plugin
 *
 * @package TravelCurator
 */

if (!defined('ABSPATH')) {
    exit;
}

class TravelCurator_Post_Types {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
        add_filter('post_updated_messages', array($this, 'travel_package_updated_messages'));
        add_filter('manage_travel_package_posts_columns', array($this, 'add_travel_package_columns'));
        add_action('manage_travel_package_posts_custom_column', array($this, 'display_travel_package_columns'), 10, 2);
        add_filter('manage_edit-travel_package_sortable_columns', array($this, 'make_travel_package_columns_sortable'));
        add_action('pre_get_posts', array($this, 'handle_travel_package_sorting'));
        add_action('restrict_manage_posts', array($this, 'add_admin_filters'));
        add_action('parse_query', array($this, 'handle_admin_filters'));
        add_filter('post_row_actions', array($this, 'add_custom_row_actions'), 10, 2);
        add_action('admin_head', array($this, 'admin_styles'));
    }

    /**
     * Register custom post types
     */
    public function register_post_types() {
        $this->register_travel_package_post_type();
    }

    /**
     * Register travel package post type
     */
    private function register_travel_package_post_type() {
        $labels = array(
            'name' => _x('Pacotes de Viagem', 'Post Type General Name', 'travelcurator'),
            'singular_name' => _x('Pacote de Viagem', 'Post Type Singular Name', 'travelcurator'),
            'menu_name' => __('Pacotes de Viagem', 'travelcurator'),
            'name_admin_bar' => __('Pacote de Viagem', 'travelcurator'),
            'archives' => __('Arquivo de Pacotes', 'travelcurator'),
            'attributes' => __('Atributos do Pacote', 'travelcurator'),
            'parent_item_colon' => __('Pacote Pai:', 'travelcurator'),
            'all_items' => __('Todos os Pacotes', 'travelcurator'),
            'add_new_item' => __('Adicionar Novo Pacote', 'travelcurator'),
            'add_new' => __('Adicionar Novo', 'travelcurator'),
            'new_item' => __('Novo Pacote', 'travelcurator'),
            'edit_item' => __('Editar Pacote', 'travelcurator'),
            'update_item' => __('Atualizar Pacote', 'travelcurator'),
            'view_item' => __('Ver Pacote', 'travelcurator'),
            'view_items' => __('Ver Pacotes', 'travelcurator'),
            'search_items' => __('Buscar Pacotes', 'travelcurator'),
            'not_found' => __('Nenhum pacote encontrado', 'travelcurator'),
            'not_found_in_trash' => __('Nenhum pacote encontrado na lixeira', 'travelcurator'),
            'featured_image' => __('Imagem Destacada', 'travelcurator'),
            'set_featured_image' => __('Definir imagem destacada', 'travelcurator'),
            'remove_featured_image' => __('Remover imagem destacada', 'travelcurator'),
            'use_featured_image' => __('Usar como imagem destacada', 'travelcurator'),
            'insert_into_item' => __('Inserir no pacote', 'travelcurator'),
            'uploaded_to_this_item' => __('Enviado para este pacote', 'travelcurator'),
            'items_list' => __('Lista de pacotes', 'travelcurator'),
            'items_list_navigation' => __('Navegação da lista de pacotes', 'travelcurator'),
            'filter_items_list' => __('Filtrar lista de pacotes', 'travelcurator'),
        );

        $args = array(
            'label' => __('Pacote de Viagem', 'travelcurator'),
            'description' => __('Pacotes de viagem para sua agência', 'travelcurator'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'page-attributes'),
            'taxonomies' => array('travel_category', 'travel_destination', 'travel_purpose', 'travel_amenity'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => 'travelcurator',
            'menu_position' => 5,
            'menu_icon' => 'dashicons-palmtree',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => 'pacotes-de-viagem',
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'show_in_rest' => true,
            'rest_base' => 'travel-packages',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite' => array(
                'slug' => 'pacote-de-viagem',
                'with_front' => false,
                'pages' => true,
                'feeds' => true,
            ),
        );

        register_post_type('travel_package', $args);
    }

    /**
     * Update messages for travel package post type
     */
    public function travel_package_updated_messages($messages) {
        $post = get_post();
        $post_type = get_post_type($post);
        $post_type_object = get_post_type_object($post_type);

        if ($post_type !== 'travel_package') {
            return $messages;
        }

        $permalink = get_permalink($post->ID);

        $messages['travel_package'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => __('Package updated.', 'travelcurator') . ($permalink ? ' <a target="_blank" href="' . esc_url($permalink) . '">' . __('View package', 'travelcurator') . '</a>' : ''),
            2  => __('Custom field updated.', 'travelcurator'),
            3  => __('Custom field deleted.', 'travelcurator'),
            4  => __('Package updated.', 'travelcurator'),
            5  => isset($_GET['revision']) ? sprintf(__('Package restored to revision from %s', 'travelcurator'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6  => __('Package published.', 'travelcurator') . ($permalink ? ' <a href="' . esc_url($permalink) . '">' . __('View package', 'travelcurator') . '</a>' : ''),
            7  => __('Package saved.', 'travelcurator'),
            8  => __('Package submitted.', 'travelcurator') . ($permalink ? ' <a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', $permalink)) . '">' . __('Preview package', 'travelcurator') . '</a>' : ''),
            9  => sprintf(__('Package scheduled for: <strong>%1$s</strong>.', 'travelcurator'), date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date))) . ($permalink ? ' <a target="_blank" href="' . esc_url($permalink) . '">' . __('Preview package', 'travelcurator') . '</a>' : ''),
            10 => __('Package draft updated.', 'travelcurator') . ($permalink ? ' <a target="_blank" href="' . esc_url(add_query_arg('preview', 'true', $permalink)) . '">' . __('Preview package', 'travelcurator') . '</a>' : ''),
        );

        return $messages;
    }

    /**
     * Add custom columns to travel package list
     */
    public function add_travel_package_columns($columns) {
        $new_columns = array();

        // Keep checkbox and title
        if (isset($columns['cb'])) {
            $new_columns['cb'] = $columns['cb'];
        }
        if (isset($columns['title'])) {
            $new_columns['title'] = $columns['title'];
        }

        // Add custom columns
        $new_columns['featured_image'] = __('Imagem', 'travelcurator');
        $new_columns['price'] = __('Preço', 'travelcurator');
        $new_columns['duration'] = __('Duração', 'travelcurator');
        $new_columns['difficulty'] = __('Dificuldade', 'travelcurator');
        $new_columns['status'] = __('Status', 'travelcurator');
        $new_columns['leads_count'] = __('Leads', 'travelcurator');
        $new_columns['travel_category'] = __('Categoria', 'travelcurator');
        $new_columns['travel_destination'] = __('Destino', 'travelcurator');

        // Add remaining columns
        if (isset($columns['date'])) {
            $new_columns['date'] = $columns['date'];
        }

        return $new_columns;
    }

    /**
     * Display custom column content
     */
    public function display_travel_package_columns($column, $post_id) {
        switch ($column) {
            case 'featured_image':
                if (has_post_thumbnail($post_id)) {
                    echo '<a href="' . esc_url(get_edit_post_link($post_id)) . '">';
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                    echo '</a>';
                } else {
                    echo '<span class="dashicons dashicons-format-image" style="color: #ccc; font-size: 30px;"></span>';
                }
                break;

            case 'price':
                $price = get_post_meta($post_id, '_travelcurator_price', true);
                if ($price) {
                    echo '<strong>R$ ' . number_format($price, 2, ',', '.') . '</strong>';
                } else {
                    echo '<span style="color: #999;">—</span>';
                }
                break;

            case 'duration':
                $duration = get_post_meta($post_id, '_travelcurator_duration', true);
                echo $duration ? esc_html($duration) : '<span style="color: #999;">—</span>';
                break;

            case 'difficulty':
                $difficulty = get_post_meta($post_id, '_travelcurator_difficulty', true);
                if ($difficulty) {
                    $difficulty_class = 'difficulty-' . $difficulty;
                    $difficulty_label = ucfirst($difficulty);
                    echo '<span class="difficulty-badge ' . esc_attr($difficulty_class) . '">' . esc_html($difficulty_label) . '</span>';
                } else {
                    echo '<span style="color: #999;">—</span>';
                }
                break;

            case 'status':
                $status = get_post_meta($post_id, '_travelcurator_status', true);
                $status = $status ? $status : 'draft';
                
                $status_labels = array(
                    'active' => array('label' => __('Ativo', 'travelcurator'), 'color' => '#46b450'),
                    'inactive' => array('label' => __('Inativo', 'travelcurator'), 'color' => '#dc3232'),
                    'draft' => array('label' => __('Rascunho', 'travelcurator'), 'color' => '#ffb900'),
                    'sold_out' => array('label' => __('Esgotado', 'travelcurator'), 'color' => '#826eb4')
                );
                
                if (isset($status_labels[$status])) {
                    echo '<span class="status-badge" style="background: ' . esc_attr($status_labels[$status]['color']) . '; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px;">';
                    echo esc_html($status_labels[$status]['label']);
                    echo '</span>';
                }
                break;

            case 'leads_count':
                global $wpdb;
                $leads_table = $wpdb->prefix . 'travelcurator_leads';
                $count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM $leads_table WHERE package_id = %d",
                    $post_id
                ));
                
                if ($count > 0) {
                    echo '<a href="' . esc_url(admin_url('admin.php?page=travelcurator-leads&package_id=' . $post_id)) . '" class="leads-count">';
                    echo '<strong>' . intval($count) . '</strong>';
                    echo '</a>';
                } else {
                    echo '<span style="color: #999;">0</span>';
                }
                break;

            case 'travel_category':
                $terms = get_the_terms($post_id, 'travel_category');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = '<a href="' . esc_url(get_edit_tag_link($term->term_id, 'travel_category')) . '">' . esc_html($term->name) . '</a>';
                    }
                    echo implode(', ', $term_links);
                } else {
                    echo '<span style="color: #999;">—</span>';
                }
                break;

            case 'travel_destination':
                $terms = get_the_terms($post_id, 'travel_destination');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_links = array();
                    foreach ($terms as $term) {
                        $term_links[] = '<a href="' . esc_url(get_edit_tag_link($term->term_id, 'travel_destination')) . '">' . esc_html($term->name) . '</a>';
                    }
                    echo implode(', ', $term_links);
                } else {
                    echo '<span style="color: #999;">—</span>';
                }
                break;
        }
    }

    /**
     * Make custom columns sortable
     */
    public function make_travel_package_columns_sortable($columns) {
        $columns['price'] = 'price';
        $columns['duration'] = 'duration';
        $columns['difficulty'] = 'difficulty';
        $columns['status'] = 'status';
        $columns['leads_count'] = 'leads_count';

        return $columns;
    }

    /**
     * Handle sorting for custom columns
     */
    public function handle_travel_package_sorting($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $orderby = $query->get('orderby');

        switch ($orderby) {
            case 'price':
                $query->set('meta_key', '_travelcurator_price');
                $query->set('orderby', 'meta_value_num');
                break;

            case 'duration':
                $query->set('meta_key', '_travelcurator_duration');
                $query->set('orderby', 'meta_value');
                break;

            case 'difficulty':
                $query->set('meta_key', '_travelcurator_difficulty');
                $query->set('orderby', 'meta_value');
                break;

            case 'status':
                $query->set('meta_key', '_travelcurator_status');
                $query->set('orderby', 'meta_value');
                break;

            case 'leads_count':
                // This would require a custom query with JOIN
                // For now, we'll skip this complex sorting
                break;
        }
    }

    /**
     * Add admin filters
     */
    public function add_admin_filters() {
        global $typenow;

        if ($typenow !== 'travel_package') {
            return;
        }

        // Status filter
        $current_status = isset($_GET['package_status']) ? $_GET['package_status'] : '';
        $statuses = array(
            '' => __('Todos os Status', 'travelcurator'),
            'active' => __('Ativo', 'travelcurator'),
            'inactive' => __('Inativo', 'travelcurator'),
            'draft' => __('Rascunho', 'travelcurator'),
            'sold_out' => __('Esgotado', 'travelcurator')
        );

        echo '<select name="package_status">';
        foreach ($statuses as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($current_status, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';

        // Difficulty filter
        $current_difficulty = isset($_GET['package_difficulty']) ? $_GET['package_difficulty'] : '';
        $difficulties = array(
            '' => __('Todas as Dificuldades', 'travelcurator'),
            'easy' => __('Fácil', 'travelcurator'),
            'moderate' => __('Moderado', 'travelcurator'),
            'hard' => __('Difícil', 'travelcurator')
        );

        echo '<select name="package_difficulty">';
        foreach ($difficulties as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($current_difficulty, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';

        // Price range filter
        $current_price_range = isset($_GET['package_price_range']) ? $_GET['package_price_range'] : '';
        $price_ranges = array(
            '' => __('Todos os Preços', 'travelcurator'),
            '0-1000' => __('R$ 0 - R$ 1.000', 'travelcurator'),
            '1001-2500' => __('R$ 1.001 - R$ 2.500', 'travelcurator'),
            '2501-5000' => __('R$ 2.501 - R$ 5.000', 'travelcurator'),
            '5001-10000' => __('R$ 5.001 - R$ 10.000', 'travelcurator'),
            '10001-99999' => __('Acima de R$ 10.000', 'travelcurator')
        );

        echo '<select name="package_price_range">';
        foreach ($price_ranges as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($current_price_range, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    /**
     * Handle admin filters
     */
    public function handle_admin_filters($query) {
        global $pagenow, $typenow;

        if ($pagenow !== 'edit.php' || $typenow !== 'travel_package') {
            return;
        }

        $meta_query = array();

        // Status filter
        if (isset($_GET['package_status']) && $_GET['package_status'] !== '') {
            $meta_query[] = array(
                'key' => '_travelcurator_status',
                'value' => sanitize_text_field($_GET['package_status']),
                'compare' => '='
            );
        }

        // Difficulty filter
        if (isset($_GET['package_difficulty']) && $_GET['package_difficulty'] !== '') {
            $meta_query[] = array(
                'key' => '_travelcurator_difficulty',
                'value' => sanitize_text_field($_GET['package_difficulty']),
                'compare' => '='
            );
        }

        // Price range filter
        if (isset($_GET['package_price_range']) && $_GET['package_price_range'] !== '') {
            $price_range = explode('-', sanitize_text_field($_GET['package_price_range']));
            if (count($price_range) === 2) {
                $meta_query[] = array(
                    'key' => '_travelcurator_price',
                    'value' => array(intval($price_range[0]), intval($price_range[1])),
                    'type' => 'NUMERIC',
                    'compare' => 'BETWEEN'
                );
            }
        }

        if (!empty($meta_query)) {
            if (count($meta_query) > 1) {
                $meta_query['relation'] = 'AND';
            }
            $query->set('meta_query', $meta_query);
        }
    }

    /**
     * Add custom row actions
     */
    public function add_custom_row_actions($actions, $post) {
        if ($post->post_type !== 'travel_package') {
            return $actions;
        }

        // Add duplicate action
        $duplicate_url = wp_nonce_url(
            admin_url('admin.php?action=travelcurator_duplicate_package&post_id=' . $post->ID),
            'travelcurator_duplicate_package',
            'duplicate_nonce'
        );
        
        $actions['duplicate'] = '<a href="' . esc_url($duplicate_url) . '">' . __('Duplicate', 'travelcurator') . '</a>';

        // Add view leads action
        global $wpdb;
        $leads_table = $wpdb->prefix . 'travelcurator_leads';
        $leads_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $leads_table WHERE package_id = %d",
            $post->ID
        ));

        if ($leads_count > 0) {
            $leads_url = admin_url('admin.php?page=travelcurator-leads&package_id=' . $post->ID);
            $actions['view_leads'] = '<a href="' . esc_url($leads_url) . '">' . sprintf(__('View Leads (%d)', 'travelcurator'), $leads_count) . '</a>';
        }

        return $actions;
    }

    /**
     * Admin styles for custom columns
     */
    public function admin_styles() {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'travel_package') {
            ?>
            <style>
            .difficulty-badge {
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 11px;
                font-weight: bold;
                text-transform: uppercase;
            }
            .difficulty-easy {
                background: #d4edda;
                color: #155724;
            }
            .difficulty-moderate {
                background: #fff3cd;
                color: #856404;
            }
            .difficulty-hard {
                background: #f8d7da;
                color: #721c24;
            }
            .leads-count {
                color: #0073aa;
                font-weight: bold;
            }
            .leads-count:hover {
                color: #005177;
            }
            .column-featured_image {
                width: 60px;
            }
            .column-price {
                width: 100px;
            }
            .column-duration {
                width: 80px;
            }
            .column-difficulty {
                width: 80px;
            }
            .column-status {
                width: 80px;
            }
            .column-leads_count {
                width: 60px;
            }
            </style>
            <?php
        }
    }

    /**
     * Handle package duplication
     */
    public static function handle_package_duplication() {
        if (!isset($_GET['action']) || $_GET['action'] !== 'travelcurator_duplicate_package') {
            return;
        }

        if (!isset($_GET['post_id']) || !wp_verify_nonce($_GET['duplicate_nonce'], 'travelcurator_duplicate_package')) {
            wp_die(__('Invalid request', 'travelcurator'));
        }

        if (!current_user_can('edit_travel_packages')) {
            wp_die(__('You do not have permission to duplicate packages', 'travelcurator'));
        }

        $post_id = intval($_GET['post_id']);
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'travel_package') {
            wp_die(__('Package not found', 'travelcurator'));
        }

        // Create duplicate
        $new_post = array(
            'post_title' => $post->post_title . ' ' . __('(Copy)', 'travelcurator'),
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_status' => 'draft',
            'post_type' => $post->post_type,
            'post_author' => get_current_user_id(),
            'menu_order' => $post->menu_order
        );

        $new_post_id = wp_insert_post($new_post);

        if ($new_post_id) {
            // Copy meta fields
            $meta_keys = get_post_meta($post_id);
            foreach ($meta_keys as $key => $values) {
                foreach ($values as $value) {
                    add_post_meta($new_post_id, $key, maybe_unserialize($value));
                }
            }

            // Copy taxonomies
            $taxonomies = get_post_taxonomies($post_id);
            foreach ($taxonomies as $taxonomy) {
                $terms = get_the_terms($post_id, $taxonomy);
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_ids = array();
                    foreach ($terms as $term) {
                        $term_ids[] = $term->term_id;
                    }
                    wp_set_post_terms($new_post_id, $term_ids, $taxonomy);
                }
            }

            // Copy featured image
            $featured_image_id = get_post_thumbnail_id($post_id);
            if ($featured_image_id) {
                set_post_thumbnail($new_post_id, $featured_image_id);
            }

            // Redirect to edit the new post
            wp_redirect(admin_url('post.php?post=' . $new_post_id . '&action=edit&duplicated=1'));
            exit;
        } else {
            wp_die(__('Error creating duplicate package', 'travelcurator'));
        }
    }

    /**
     * Add capabilities to roles
     */
    public static function add_capabilities() {
        $roles = array('administrator', 'editor');
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            if ($role) {
                $role->add_cap('edit_travel_packages');
                $role->add_cap('edit_others_travel_packages');
                $role->add_cap('publish_travel_packages');
                $role->add_cap('read_private_travel_packages');
                $role->add_cap('delete_travel_packages');
                $role->add_cap('delete_private_travel_packages');
                $role->add_cap('delete_published_travel_packages');
                $role->add_cap('delete_others_travel_packages');
                $role->add_cap('edit_private_travel_packages');
                $role->add_cap('edit_published_travel_packages');
                $role->add_cap('manage_travel_categories');
                $role->add_cap('edit_travel_categories');
                $role->add_cap('delete_travel_categories');
                $role->add_cap('assign_travel_categories');
            }
        }

        // Author role gets limited permissions
        $author = get_role('author');
        if ($author) {
            $author->add_cap('edit_travel_packages');
            $author->add_cap('publish_travel_packages');
            $author->add_cap('delete_travel_packages');
            $author->add_cap('edit_published_travel_packages');
            $author->add_cap('delete_published_travel_packages');
        }
    }

    /**
     * Get package statistics
     */
    public static function get_package_statistics() {
        global $wpdb;

        $stats = array();

        // Total packages
        $stats['total'] = wp_count_posts('travel_package');

        // Active packages
        $stats['active'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} p 
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
             WHERE p.post_type = 'travel_package' 
             AND p.post_status = 'publish' 
             AND pm.meta_key = '_travelcurator_status' 
             AND pm.meta_value = 'active'"
        );

        // Packages by difficulty
        $stats['difficulty'] = array();
        $difficulties = array('easy', 'moderate', 'hard');
        
        foreach ($difficulties as $difficulty) {
            $stats['difficulty'][$difficulty] = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} p 
                 LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
                 WHERE p.post_type = 'travel_package' 
                 AND p.post_status = 'publish' 
                 AND pm.meta_key = '_travelcurator_difficulty' 
                 AND pm.meta_value = %s",
                $difficulty
            ));
        }

        // Average price
        $stats['average_price'] = $wpdb->get_var(
            "SELECT AVG(CAST(pm.meta_value AS DECIMAL(10,2))) 
             FROM {$wpdb->posts} p 
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
             WHERE p.post_type = 'travel_package' 
             AND p.post_status = 'publish' 
             AND pm.meta_key = '_travelcurator_price' 
             AND pm.meta_value != ''"
        );

        // Price ranges
        $stats['price_ranges'] = array(
            '0-1000' => 0,
            '1001-2500' => 0,
            '2501-5000' => 0,
            '5001-10000' => 0,
            '10000+' => 0
        );

        $price_data = $wpdb->get_results(
            "SELECT CAST(pm.meta_value AS DECIMAL(10,2)) as price 
             FROM {$wpdb->posts} p 
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
             WHERE p.post_type = 'travel_package' 
             AND p.post_status = 'publish' 
             AND pm.meta_key = '_travelcurator_price' 
             AND pm.meta_value != ''"
        );

        foreach ($price_data as $item) {
            $price = floatval($item->price);
            if ($price <= 1000) {
                $stats['price_ranges']['0-1000']++;
            } elseif ($price <= 2500) {
                $stats['price_ranges']['1001-2500']++;
            } elseif ($price <= 5000) {
                $stats['price_ranges']['2501-5000']++;
            } elseif ($price <= 10000) {
                $stats['price_ranges']['5001-10000']++;
            } else {
                $stats['price_ranges']['10000+']++;
            }
        }

        return $stats;
    }

    /**
     * Get popular packages
     */
    public static function get_popular_packages($limit = 10) {
        global $wpdb;

        $leads_table = $wpdb->prefix . 'travelcurator_leads';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT p.*, COUNT(l.id) as lead_count
             FROM {$wpdb->posts} p
             LEFT JOIN $leads_table l ON p.ID = l.package_id
             WHERE p.post_type = 'travel_package' 
             AND p.post_status = 'publish'
             GROUP BY p.ID
             ORDER BY lead_count DESC, p.post_date DESC
             LIMIT %d",
            $limit
        ));

        return $results;
    }

    /**
     * Search packages by various criteria
     */
    public static function search_packages($args = array()) {
        $defaults = array(
            'search' => '',
            'category' => '',
            'destination' => '',
            'purpose' => '',
            'amenity' => '',
            'min_price' => '',
            'max_price' => '',
            'difficulty' => '',
            'status' => 'active',
            'orderby' => 'date',
            'order' => 'DESC',
            'posts_per_page' => 10,
            'paged' => 1
        );

        $args = wp_parse_args($args, $defaults);

        $query_args = array(
            'post_type' => 'travel_package',
            'post_status' => 'publish',
            'posts_per_page' => $args['posts_per_page'],
            'paged' => $args['paged'],
            'orderby' => $args['orderby'],
            'order' => $args['order']
        );

        // Search by title/content
        if (!empty($args['search'])) {
            $query_args['s'] = $args['search'];
        }

        // Meta query
        $meta_query = array();
        
        if (!empty($args['status'])) {
            $meta_query[] = array(
                'key' => '_travelcurator_status',
                'value' => $args['status'],
                'compare' => '='
            );
        }

        if (!empty($args['difficulty'])) {
            $meta_query[] = array(
                'key' => '_travelcurator_difficulty',
                'value' => $args['difficulty'],
                'compare' => '='
            );
        }

        // Price range
        if (!empty($args['min_price']) || !empty($args['max_price'])) {
            $price_query = array(
                'key' => '_travelcurator_price',
                'type' => 'NUMERIC'
            );

            if (!empty($args['min_price']) && !empty($args['max_price'])) {
                $price_query['value'] = array($args['min_price'], $args['max_price']);
                $price_query['compare'] = 'BETWEEN';
            } elseif (!empty($args['min_price'])) {
                $price_query['value'] = $args['min_price'];
                $price_query['compare'] = '>=';
            } else {
                $price_query['value'] = $args['max_price'];
                $price_query['compare'] = '<=';
            }

            $meta_query[] = $price_query;
        }

        if (!empty($meta_query)) {
            if (count($meta_query) > 1) {
                $meta_query['relation'] = 'AND';
            }
            $query_args['meta_query'] = $meta_query;
        }

        // Tax query
        $tax_query = array();

        if (!empty($args['category'])) {
            $tax_query[] = array(
                'taxonomy' => 'travel_category',
                'field' => 'slug',
                'terms' => $args['category']
            );
        }

        if (!empty($args['destination'])) {
            $tax_query[] = array(
                'taxonomy' => 'travel_destination',
                'field' => 'slug',
                'terms' => $args['destination']
            );
        }

        if (!empty($args['purpose'])) {
            $tax_query[] = array(
                'taxonomy' => 'travel_purpose',
                'field' => 'slug',
                'terms' => $args['purpose']
            );
        }

        if (!empty($args['amenity'])) {
            $tax_query[] = array(
                'taxonomy' => 'travel_amenity',
                'field' => 'slug',
                'terms' => $args['amenity']
            );
        }

        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'AND';
            }
            $query_args['tax_query'] = $tax_query;
        }

        return new WP_Query($query_args);
    }

    /**
     * Get package schema markup for SEO
     */
    public static function get_package_schema($post_id) {
        $package = get_post($post_id);
        if (!$package || $package->post_type !== 'travel_package') {
            return '';
        }

        $price = get_post_meta($post_id, '_travelcurator_price', true);
        $location = get_post_meta($post_id, '_travelcurator_location', true);
        $duration = get_post_meta($post_id, '_travelcurator_duration', true);
        $start_date = get_post_meta($post_id, '_travelcurator_start_date', true);
        $end_date = get_post_meta($post_id, '_travelcurator_end_date', true);

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $package->post_title,
            'description' => $package->post_excerpt ? $package->post_excerpt : wp_trim_words($package->post_content, 30),
            'url' => get_permalink($post_id)
        );

        if ($price) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => 'BRL',
                'availability' => 'https://schema.org/InStock'
            );
        }

        if ($location) {
            $schema['touristDestination'] = array(
                '@type' => 'Place',
                'name' => $location
            );
        }

        if ($start_date && $end_date) {
            $schema['startDate'] = date('Y-m-d', strtotime($start_date));
            $schema['endDate'] = date('Y-m-d', strtotime($end_date));
        }

        if (has_post_thumbnail($post_id)) {
            $schema['image'] = get_the_post_thumbnail_url($post_id, 'large');
        }

        // Add provider information
        $settings = get_option('travelcurator_settings', array());
        if (!empty($settings['company_name'])) {
            $schema['provider'] = array(
                '@type' => 'Organization',
                'name' => $settings['company_name']
            );

            if (!empty($settings['company_logo'])) {
                $schema['provider']['logo'] = $settings['company_logo'];
            }
        }

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}