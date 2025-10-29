<?php
/**
 * Register all taxonomies for the plugin
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/includes
 */

/**
 * Register all taxonomies for the plugin.
 */
class TravelCurator_Taxonomies {

    /**
     * Register custom taxonomies.
     */
    public function register_taxonomies() {
        $this->register_travel_category_taxonomy();
        $this->register_emotional_purpose_taxonomy();
        $this->register_travel_destination_taxonomy();
        $this->register_travel_facilities_taxonomy();
    }

    /**
     * Register the travel_category taxonomy.
     */
    private function register_travel_category_taxonomy() {
        $labels = array(
            'name'                       => _x('Categorias de Pacote', 'Taxonomy General Name', 'travelcurator'),
            'singular_name'              => _x('Categoria de Pacote', 'Taxonomy Singular Name', 'travelcurator'),
            'menu_name'                  => __('Categorias', 'travelcurator'),
            'all_items'                  => __('Todas as Categorias', 'travelcurator'),
            'parent_item'                => __('Categoria Pai', 'travelcurator'),
            'parent_item_colon'          => __('Categoria Pai:', 'travelcurator'),
            'new_item_name'              => __('Nova Categoria', 'travelcurator'),
            'add_new_item'               => __('Adicionar Nova Categoria', 'travelcurator'),
            'edit_item'                  => __('Editar Categoria', 'travelcurator'),
            'update_item'                => __('Atualizar Categoria', 'travelcurator'),
            'view_item'                  => __('Ver Categoria', 'travelcurator'),
            'separate_items_with_commas' => __('Separar categorias com vírgulas', 'travelcurator'),
            'add_or_remove_items'        => __('Adicionar ou remover categorias', 'travelcurator'),
            'choose_from_most_used'      => __('Escolher das mais usadas', 'travelcurator'),
            'popular_items'              => __('Categorias Populares', 'travelcurator'),
            'search_items'               => __('Buscar Categorias', 'travelcurator'),
            'not_found'                  => __('Não Encontrado', 'travelcurator'),
            'no_terms'                   => __('Nenhuma categoria', 'travelcurator'),
            'items_list'                 => __('Lista de categorias', 'travelcurator'),
            'items_list_navigation'      => __('Navegação da lista de categorias', 'travelcurator'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array(
                'slug'                       => 'categoria-viagem',
                'with_front'                 => false,
            ),
            'query_var'                  => true,
        );

        register_taxonomy('travel_category', array('travel_package'), $args);
    }

    /**
     * Register the emotional_purpose taxonomy.
     */
    private function register_emotional_purpose_taxonomy() {
        $labels = array(
            'name'                       => _x('Propósitos Emocionais', 'Taxonomy General Name', 'travelcurator'),
            'singular_name'              => _x('Propósito Emocional', 'Taxonomy Singular Name', 'travelcurator'),
            'menu_name'                  => __('Propósitos', 'travelcurator'),
            'all_items'                  => __('Todos os Propósitos', 'travelcurator'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'new_item_name'              => __('Novo Propósito', 'travelcurator'),
            'add_new_item'               => __('Adicionar Novo Propósito', 'travelcurator'),
            'edit_item'                  => __('Editar Propósito', 'travelcurator'),
            'update_item'                => __('Atualizar Propósito', 'travelcurator'),
            'view_item'                  => __('Ver Propósito', 'travelcurator'),
            'separate_items_with_commas' => __('Separar propósitos com vírgulas', 'travelcurator'),
            'add_or_remove_items'        => __('Adicionar ou remover propósitos', 'travelcurator'),
            'choose_from_most_used'      => __('Escolher dos mais usados', 'travelcurator'),
            'popular_items'              => __('Propósitos Populares', 'travelcurator'),
            'search_items'               => __('Buscar Propósitos', 'travelcurator'),
            'not_found'                  => __('Não Encontrado', 'travelcurator'),
            'no_terms'                   => __('Nenhum propósito', 'travelcurator'),
            'items_list'                 => __('Lista de propósitos', 'travelcurator'),
            'items_list_navigation'      => __('Navegação da lista de propósitos', 'travelcurator'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array(
                'slug'                       => 'proposito',
                'with_front'                 => false,
            ),
            'query_var'                  => true,
        );

        register_taxonomy('emotional_purpose', array('travel_package'), $args);
    }

    /**
     * Register the travel_destination taxonomy.
     */
    private function register_travel_destination_taxonomy() {
        $labels = array(
            'name'                       => _x('Destinos', 'Taxonomy General Name', 'travelcurator'),
            'singular_name'              => _x('Destino', 'Taxonomy Singular Name', 'travelcurator'),
            'menu_name'                  => __('Destinos', 'travelcurator'),
            'all_items'                  => __('Todos os Destinos', 'travelcurator'),
            'parent_item'                => __('Destino Pai', 'travelcurator'),
            'parent_item_colon'          => __('Destino Pai:', 'travelcurator'),
            'new_item_name'              => __('Novo Destino', 'travelcurator'),
            'add_new_item'               => __('Adicionar Novo Destino', 'travelcurator'),
            'edit_item'                  => __('Editar Destino', 'travelcurator'),
            'update_item'                => __('Atualizar Destino', 'travelcurator'),
            'view_item'                  => __('Ver Destino', 'travelcurator'),
            'separate_items_with_commas' => __('Separar destinos com vírgulas', 'travelcurator'),
            'add_or_remove_items'        => __('Adicionar ou remover destinos', 'travelcurator'),
            'choose_from_most_used'      => __('Escolher dos mais usados', 'travelcurator'),
            'popular_items'              => __('Destinos Populares', 'travelcurator'),
            'search_items'               => __('Buscar Destinos', 'travelcurator'),
            'not_found'                  => __('Não Encontrado', 'travelcurator'),
            'no_terms'                   => __('Nenhum destino', 'travelcurator'),
            'items_list'                 => __('Lista de destinos', 'travelcurator'),
            'items_list_navigation'      => __('Navegação da lista de destinos', 'travelcurator'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array(
                'slug'                       => 'destino',
                'with_front'                 => false,
            ),
            'query_var'                  => true,
        );

        register_taxonomy('travel_destination', array('travel_package'), $args);
    }

    /**
     * Register the travel_facilities taxonomy.
     */
    private function register_travel_facilities_taxonomy() {
        $labels = array(
            'name'                       => _x('Comodidades', 'Taxonomy General Name', 'travelcurator'),
            'singular_name'              => _x('Comodidade', 'Taxonomy Singular Name', 'travelcurator'),
            'menu_name'                  => __('Comodidades', 'travelcurator'),
            'all_items'                  => __('Todas as Comodidades', 'travelcurator'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'new_item_name'              => __('Nova Comodidade', 'travelcurator'),
            'add_new_item'               => __('Adicionar Nova Comodidade', 'travelcurator'),
            'edit_item'                  => __('Editar Comodidade', 'travelcurator'),
            'update_item'                => __('Atualizar Comodidade', 'travelcurator'),
            'view_item'                  => __('Ver Comodidade', 'travelcurator'),
            'separate_items_with_commas' => __('Separar comodidades com vírgulas', 'travelcurator'),
            'add_or_remove_items'        => __('Adicionar ou remover comodidades', 'travelcurator'),
            'choose_from_most_used'      => __('Escolher das mais usadas', 'travelcurator'),
            'popular_items'              => __('Comodidades Populares', 'travelcurator'),
            'search_items'               => __('Buscar Comodidades', 'travelcurator'),
            'not_found'                  => __('Não Encontrado', 'travelcurator'),
            'no_terms'                   => __('Nenhuma comodidade', 'travelcurator'),
            'items_list'                 => __('Lista de comodidades', 'travelcurator'),
            'items_list_navigation'      => __('Navegação da lista de comodidades', 'travelcurator'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => false,
            'show_in_nav_menus'          => false,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rewrite'                    => false,
            'query_var'                  => true,
        );

        register_taxonomy('travel_facilities', array('travel_package'), $args);
    }

    /**
     * Create default taxonomy terms.
     */
    public function create_default_terms() {
        // Only run once
        if (get_option('travelcurator_default_terms_created')) {
            return;
        }

        $this->create_travel_categories();
        $this->create_emotional_purposes();
        $this->create_default_facilities();
        $this->create_sample_destinations();

        // Mark as created
        update_option('travelcurator_default_terms_created', true);
    }

    /**
     * Create travel categories.
     */
    private function create_travel_categories() {
        $categories = array(
            'Individual' => 'Viagens para pessoas que querem viajar sozinhas',
            'Casal' => 'Experiências românticas para dois',
            'Família' => 'Aventuras para toda a família',
            'Grupo' => 'Viagens para grupos de amigos',
            'Empresarial' => 'Viagens corporativas e de negócios',
            'Lua de Mel' => 'Destinos especiais para recém-casados',
            'Aventura' => 'Para os amantes de adrenalina e natureza'
        );
        
        foreach ($categories as $name => $description) {
            if (!term_exists($name, 'travel_category')) {
                wp_insert_term($name, 'travel_category', array(
                    'description' => $description
                ));
            }
        }
    }

    /**
     * Create emotional purposes.
     */
    private function create_emotional_purposes() {
        $purposes = array(
            'Reconexão' => array(
                'description' => 'Para redescobrir vínculos importantes',
                'color' => '#1A3A5F'
            ),
            'Celebração' => array(
                'description' => 'Para marcar momentos especiais',
                'color' => '#D4B254'
            ),
            'Descoberta' => array(
                'description' => 'Para expandir horizontes',
                'color' => '#C85F4D'
            ),
            'Transformação' => array(
                'description' => 'Para crescimento pessoal',
                'color' => '#8A8582'
            ),
            'Descanso' => array(
                'description' => 'Para renovar energias',
                'color' => '#46b450'
            )
        );
        
        foreach ($purposes as $name => $data) {
            if (!term_exists($name, 'emotional_purpose')) {
                $term = wp_insert_term($name, 'emotional_purpose', array(
                    'description' => $data['description']
                ));
                
                if (!is_wp_error($term)) {
                    update_term_meta($term['term_id'], 'purpose_color', $data['color']);
                }
            }
        }
    }

    /**
     * Create default facilities.
     */
    private function create_default_facilities() {
        $facilities = array(
            'Wi-Fi Gratuito', 'Piscina', 'Spa', 'Academia', 'Restaurante',
            'Bar', 'Room Service', 'Ar Condicionado', 'TV a Cabo', 'Cofre',
            'Frigobar', 'Varanda', 'Vista para o Mar', 'Transfer Incluído',
            'Guia Local', 'Seguro Viagem', 'Café da Manhã', 'Estacionamento',
            'Pet Friendly', 'Acessibilidade', 'Lavanderia', 'Centro de Negócios'
        );
        
        foreach ($facilities as $facility) {
            if (!term_exists($facility, 'travel_facilities')) {
                wp_insert_term($facility, 'travel_facilities');
            }
        }
    }

    /**
     * Create sample destinations.
     */
    private function create_sample_destinations() {
        $destinations = array(
            'Brasil' => array(
                'Rio de Janeiro' => 'A Cidade Maravilhosa',
                'São Paulo' => 'A metrópole brasileira',
                'Salvador' => 'Capital da Bahia',
                'Florianópolis' => 'Ilha da Magia',
                'Foz do Iguaçu' => 'As famosas cataratas',
                'Fernando de Noronha' => 'Paraíso ecológico',
                'Bonito' => 'Ecoturismo no Mato Grosso do Sul',
                'Gramado' => 'Serra Gaúcha',
                'Jericoacoara' => 'Praia paradisíaca no Ceará',
                'Chapada Diamantina' => 'Aventura na Bahia'
            ),
            'Internacional' => array(
                'Paris' => 'Cidade Luz',
                'Roma' => 'Cidade Eterna',
                'Londres' => 'Capital da Inglaterra',
                'Tóquio' => 'Metrópole japonesa',
                'Nova York' => 'A cidade que nunca dorme',
                'Buenos Aires' => 'Capital da Argentina',
                'Lisboa' => 'Capital de Portugal',
                'Bariloche' => 'Patagônia argentina',
                'Cancún' => 'Caribe mexicano',
                'Machu Picchu' => 'Peru histórico'
            )
        );
        
        foreach ($destinations as $continent => $cities) {
            // Create parent term
            $parent_term = null;
            if (!term_exists($continent, 'travel_destination')) {
                $parent_term = wp_insert_term($continent, 'travel_destination');
            } else {
                $parent_term = get_term_by('name', $continent, 'travel_destination');
                $parent_term = array('term_id' => $parent_term->term_id);
            }
            
            // Create child terms
            if (!is_wp_error($parent_term)) {
                foreach ($cities as $city => $description) {
                    if (!term_exists($city, 'travel_destination')) {
                        wp_insert_term($city, 'travel_destination', array(
                            'parent' => $parent_term['term_id'],
                            'description' => $description
                        ));
                    }
                }
            }
        }
    }

    /**
     * Add custom fields to taxonomy terms.
     */
    public function add_taxonomy_custom_fields() {
        // Add color field to emotional_purpose taxonomy
        add_action('emotional_purpose_add_form_fields', array($this, 'add_emotional_purpose_color_field'));
        add_action('emotional_purpose_edit_form_fields', array($this, 'edit_emotional_purpose_color_field'), 10, 2);
        add_action('created_emotional_purpose', array($this, 'save_emotional_purpose_color_field'), 10, 2);
        add_action('edited_emotional_purpose', array($this, 'save_emotional_purpose_color_field'), 10, 2);

        // Add image field to travel_destination taxonomy
        add_action('travel_destination_add_form_fields', array($this, 'add_destination_image_field'));
        add_action('travel_destination_edit_form_fields', array($this, 'edit_destination_image_field'), 10, 2);
        add_action('created_travel_destination', array($this, 'save_destination_image_field'), 10, 2);
        add_action('edited_travel_destination', array($this, 'save_destination_image_field'), 10, 2);

        // Add icon field to travel_facilities
        add_action('travel_facilities_add_form_fields', array($this, 'add_facility_icon_field'));
        add_action('travel_facilities_edit_form_fields', array($this, 'edit_facility_icon_field'), 10, 2);
        add_action('created_travel_facilities', array($this, 'save_facility_icon_field'), 10, 2);
        add_action('edited_travel_facilities', array($this, 'save_facility_icon_field'), 10, 2);
    }

    /**
     * Add color field to emotional purpose add form.
     */
    public function add_emotional_purpose_color_field() {
        ?>
        <div class="form-field">
            <label for="purpose_color"><?php _e('Cor do Propósito', 'travelcurator'); ?></label>
            <input type="color" name="purpose_color" id="purpose_color" value="#1A3A5F" />
            <p class="description"><?php _e('Escolha uma cor para representar este propósito emocional.', 'travelcurator'); ?></p>
        </div>
        <?php
    }

    /**
     * Edit color field for emotional purpose.
     */
    public function edit_emotional_purpose_color_field($term, $taxonomy) {
        $color = get_term_meta($term->term_id, 'purpose_color', true);
        $color = $color ? $color : '#1A3A5F';
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="purpose_color"><?php _e('Cor do Propósito', 'travelcurator'); ?></label></th>
            <td>
                <input type="color" name="purpose_color" id="purpose_color" value="<?php echo esc_attr($color); ?>" />
                <p class="description"><?php _e('Escolha uma cor para representar este propósito emocional.', 'travelcurator'); ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save emotional purpose color field.
     */
    public function save_emotional_purpose_color_field($term_id, $taxonomy) {
        if (isset($_POST['purpose_color']) && !empty($_POST['purpose_color'])) {
            update_term_meta($term_id, 'purpose_color', sanitize_hex_color($_POST['purpose_color']));
        }
    }

    /**
     * Add image field to destination add form.
     */
    public function add_destination_image_field() {
        ?>
        <div class="form-field">
            <label for="destination_image"><?php _e('Imagem do Destino', 'travelcurator'); ?></label>
            <input type="hidden" id="destination_image" name="destination_image" value="" />
            <div id="destination_image_preview" style="margin-bottom: 10px;"></div>
            <button type="button" class="button" id="destination_image_upload"><?php _e('Escolher Imagem', 'travelcurator'); ?></button>
            <button type="button" class="button" id="destination_image_remove" style="display: none;"><?php _e('Remover Imagem', 'travelcurator'); ?></button>
            <p class="description"><?php _e('Imagem representativa do destino.', 'travelcurator'); ?></p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var frame;
            
            $('#destination_image_upload').on('click', function(e) {
                e.preventDefault();
                
                if (frame) {
                    frame.open();
                    return;
                }
                
                frame = wp.media({
                    title: '<?php _e('Escolher Imagem do Destino', 'travelcurator'); ?>',
                    button: {
                        text: '<?php _e('Usar esta imagem', 'travelcurator'); ?>'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#destination_image').val(attachment.id);
                    $('#destination_image_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 200px; height: auto;" />');
                    $('#destination_image_remove').show();
                });
                
                frame.open();
            });
            
            $('#destination_image_remove').on('click', function(e) {
                e.preventDefault();
                $('#destination_image').val('');
                $('#destination_image_preview').html('');
                $(this).hide();
            });
        });
        </script>
        <?php
    }

    /**
     * Edit image field for destination.
     */
    public function edit_destination_image_field($term, $taxonomy) {
        $image_id = get_term_meta($term->term_id, 'destination_image', true);
        $image_src = $image_id ? wp_get_attachment_image_src($image_id, 'thumbnail') : '';
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="destination_image"><?php _e('Imagem do Destino', 'travelcurator'); ?></label></th>
            <td>
                <input type="hidden" id="destination_image" name="destination_image" value="<?php echo esc_attr($image_id); ?>" />
                <div id="destination_image_preview" style="margin-bottom: 10px;">
                    <?php if ($image_src): ?>
                        <img src="<?php echo esc_url($image_src[0]); ?>" style="max-width: 200px; height: auto;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="destination_image_upload"><?php _e('Escolher Imagem', 'travelcurator'); ?></button>
                <button type="button" class="button" id="destination_image_remove" <?php echo $image_id ? '' : 'style="display: none;"'; ?>><?php _e('Remover Imagem', 'travelcurator'); ?></button>
                <p class="description"><?php _e('Imagem representativa do destino.', 'travelcurator'); ?></p>
                
                <script>
                jQuery(document).ready(function($) {
                    var frame;
                    
                    $('#destination_image_upload').on('click', function(e) {
                        e.preventDefault();
                        
                        if (frame) {
                            frame.open();
                            return;
                        }
                        
                        frame = wp.media({
                            title: '<?php _e('Escolher Imagem do Destino', 'travelcurator'); ?>',
                            button: {
                                text: '<?php _e('Usar esta imagem', 'travelcurator'); ?>'
                            },
                            multiple: false
                        });
                        
                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();
                            $('#destination_image').val(attachment.id);
                            $('#destination_image_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 200px; height: auto;" />');
                            $('#destination_image_remove').show();
                        });
                        
                        frame.open();
                    });
                    
                    $('#destination_image_remove').on('click', function(e) {
                        e.preventDefault();
                        $('#destination_image').val('');
                        $('#destination_image_preview').html('');
                        $(this).hide();
                    });
                });
                </script>
            </td>
        </tr>
        <?php
    }

    /**
     * Save destination image field.
     */
    public function save_destination_image_field($term_id, $taxonomy) {
        if (isset($_POST['destination_image'])) {
            update_term_meta($term_id, 'destination_image', absint($_POST['destination_image']));
        }
    }

    /**
     * Add icon field to facility add form.
     */
    public function add_facility_icon_field() {
        ?>
        <div class="form-field">
            <label for="facility_icon"><?php _e('Ícone da Comodidade', 'travelcurator'); ?></label>
            <input type="text" name="facility_icon" id="facility_icon" value="" placeholder="fas fa-wifi" />
            <p class="description"><?php _e('Classe do ícone Font Awesome (ex: fas fa-wifi, fas fa-swimming-pool)', 'travelcurator'); ?></p>
        </div>
        <?php
    }

    /**
     * Edit icon field for facility.
     */
    public function edit_facility_icon_field($term, $taxonomy) {
        $icon = get_term_meta($term->term_id, 'facility_icon', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="facility_icon"><?php _e('Ícone da Comodidade', 'travelcurator'); ?></label></th>
            <td>
                <input type="text" name="facility_icon" id="facility_icon" value="<?php echo esc_attr($icon); ?>" placeholder="fas fa-wifi" />
                <p class="description"><?php _e('Classe do ícone Font Awesome (ex: fas fa-wifi, fas fa-swimming-pool)', 'travelcurator'); ?></p>
                <?php if ($icon): ?>
                <p><i class="<?php echo esc_attr($icon); ?>"></i> Preview do ícone</p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    /**
     * Save facility icon field.
     */
    public function save_facility_icon_field($term_id, $taxonomy) {
        if (isset($_POST['facility_icon'])) {
            update_term_meta($term_id, 'facility_icon', sanitize_text_field($_POST['facility_icon']));
        }
    }

    /**
     * Add custom columns to taxonomy list tables.
     */
    public function add_custom_taxonomy_columns() {
        // Add color column to emotional_purpose
        add_filter('manage_edit-emotional_purpose_columns', array($this, 'add_purpose_color_column'));
        add_filter('manage_emotional_purpose_custom_column', array($this, 'display_purpose_color_column'), 10, 3);

        // Add image column to travel_destination
        add_filter('manage_edit-travel_destination_columns', array($this, 'add_destination_image_column'));
        add_filter('manage_travel_destination_custom_column', array($this, 'display_destination_image_column'), 10, 3);

        // Add icon column to travel_facilities
        add_filter('manage_edit-travel_facilities_columns', array($this, 'add_facility_icon_column'));
        add_filter('manage_travel_facilities_custom_column', array($this, 'display_facility_icon_column'), 10, 3);
    }

    /**
     * Add color column to emotional purpose.
     */
    public function add_purpose_color_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key === 'posts') {
                $new_columns['color'] = __('Cor', 'travelcurator');
            }
            $new_columns[$key] = $value;
        }
        return $new_columns;
    }

    /**
     * Display color column content.
     */
    public function display_purpose_color_column($content, $column_name, $term_id) {
        if ($column_name === 'color') {
            $color = get_term_meta($term_id, 'purpose_color', true);
            if ($color) {
                $content = '<div style="width: 20px; height: 20px; background-color: ' . esc_attr($color) . '; border-radius: 50%; display: inline-block; border: 1px solid #ddd;"></div>';
            }
        }
        return $content;
    }

    /**
     * Add image column to destination.
     */
    public function add_destination_image_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key === 'posts') {
                $new_columns['image'] = __('Imagem', 'travelcurator');
            }
            $new_columns[$key] = $value;
        }
        return $new_columns;
    }

    /**
     * Display image column content.
     */
    public function display_destination_image_column($content, $column_name, $term_id) {
        if ($column_name === 'image') {
            $image_id = get_term_meta($term_id, 'destination_image', true);
            if ($image_id) {
                $image = wp_get_attachment_image_src($image_id, 'thumbnail');
                if ($image) {
                    $content = '<img src="' . esc_url($image[0]) . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" />';
                }
            }
        }
        return $content;
    }

    /**
     * Add icon column to facility.
     */
    public function add_facility_icon_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key === 'posts') {
                $new_columns['icon'] = __('Ícone', 'travelcurator');
            }
            $new_columns[$key] = $value;
        }
        return $new_columns;
    }

    /**
     * Display icon column content.
     */
    public function display_facility_icon_column($content, $column_name, $term_id) {
        if ($column_name === 'icon') {
            $icon = get_term_meta($term_id, 'facility_icon', true);
            if ($icon) {
                $content = '<i class="' . esc_attr($icon) . '" style="font-size: 18px; color: #1A3A5F;"></i>';
            }
        }
        return $content;
    }
}