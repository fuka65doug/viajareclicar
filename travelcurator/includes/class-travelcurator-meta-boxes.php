<?php

/**
 * The meta boxes functionality of the plugin.
 *
 * @package    TravelCurator
 * @subpackage TravelCurator/includes
 */

/**
 * The meta boxes functionality of the plugin.
 */
class TravelCurator_Meta_Boxes {

    /**
     * Initialize the class and set its properties.
     */
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Add meta boxes for travel packages
     */
    public function add_meta_boxes() {
        add_meta_box(
            'travelcurator_package_details',
            __('Detalhes do Pacote', 'travelcurator'),
            array($this, 'package_details_callback'),
            'travel_package',
            'normal',
            'high'
        );

        add_meta_box(
            'travelcurator_package_pricing',
            __('Preços e Condições', 'travelcurator'),
            array($this, 'package_pricing_callback'),
            'travel_package',
            'side',
            'high'
        );

        add_meta_box(
            'travelcurator_package_gallery',
            __('Galeria de Imagens', 'travelcurator'),
            array($this, 'package_gallery_callback'),
            'travel_package',
            'normal',
            'default'
        );

        add_meta_box(
            'travelcurator_package_itinerary',
            __('Roteiro Detalhado', 'travelcurator'),
            array($this, 'package_itinerary_callback'),
            'travel_package',
            'normal',
            'default'
        );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        global $post;
        
        if ($hook == 'post-new.php' || $hook == 'post.php') {
            if ('travel_package' === $post->post_type) {
                wp_enqueue_media();
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_style('jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
                
                wp_enqueue_script(
                    'travelcurator-admin-meta',
                    TRAVELCURATOR_PLUGIN_URL . 'admin/js/travelcurator-admin-meta.js',
                    array('jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable'),
                    TRAVELCURATOR_VERSION,
                    true
                );

                wp_enqueue_style(
                    'travelcurator-admin-meta',
                    TRAVELCURATOR_PLUGIN_URL . 'admin/css/travelcurator-admin-meta.css',
                    array(),
                    TRAVELCURATOR_VERSION
                );
            }
        }
    }

    /**
     * Package details meta box callback
     */
    public function package_details_callback($post) {
        wp_nonce_field('travelcurator_package_details', 'travelcurator_package_details_nonce');

        // Get saved values
        $duration = get_post_meta($post->ID, '_travelcurator_duration', true);
        $location = get_post_meta($post->ID, '_travelcurator_location', true);
        $start_date = get_post_meta($post->ID, '_travelcurator_start_date', true);
        $end_date = get_post_meta($post->ID, '_travelcurator_end_date', true);
        $max_participants = get_post_meta($post->ID, '_travelcurator_max_participants', true);
        $min_participants = get_post_meta($post->ID, '_travelcurator_min_participants', true);
        $includes = get_post_meta($post->ID, '_travelcurator_includes', true);
        $not_includes = get_post_meta($post->ID, '_travelcurator_not_includes', true);
        $requirements = get_post_meta($post->ID, '_travelcurator_requirements', true);
        $featured = get_post_meta($post->ID, '_travelcurator_featured', true);
        ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="travelcurator_duration"><?php _e('Duração', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="text" id="travelcurator_duration" name="travelcurator_duration" 
                           value="<?php echo esc_attr($duration); ?>" 
                           placeholder="<?php esc_attr_e('Ex: 7 dias e 6 noites', 'travelcurator'); ?>" 
                           style="width: 100%;" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_location"><?php _e('Localização', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="text" id="travelcurator_location" name="travelcurator_location" 
                           value="<?php echo esc_attr($location); ?>" 
                           placeholder="<?php esc_attr_e('Ex: Fernando de Noronha, PE', 'travelcurator'); ?>" 
                           style="width: 100%;" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_start_date"><?php _e('Data de Início', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="date" id="travelcurator_start_date" name="travelcurator_start_date" 
                           value="<?php echo esc_attr($start_date); ?>" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_end_date"><?php _e('Data de Término', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="date" id="travelcurator_end_date" name="travelcurator_end_date" 
                           value="<?php echo esc_attr($end_date); ?>" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_max_participants"><?php _e('Máximo de Participantes', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="number" id="travelcurator_max_participants" name="travelcurator_max_participants" 
                           value="<?php echo esc_attr($max_participants); ?>" min="1" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_min_participants"><?php _e('Mínimo de Participantes', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="number" id="travelcurator_min_participants" name="travelcurator_min_participants" 
                           value="<?php echo esc_attr($min_participants); ?>" min="1" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_includes"><?php _e('Incluso no Pacote', 'travelcurator'); ?></label>
                </th>
                <td>
                    <?php
                    wp_editor($includes, 'travelcurator_includes', array(
                        'textarea_name' => 'travelcurator_includes',
                        'media_buttons' => false,
                        'textarea_rows' => 5,
                        'teeny' => true,
                        'quicktags' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_not_includes"><?php _e('Não Incluso', 'travelcurator'); ?></label>
                </th>
                <td>
                    <?php
                    wp_editor($not_includes, 'travelcurator_not_includes', array(
                        'textarea_name' => 'travelcurator_not_includes',
                        'media_buttons' => false,
                        'textarea_rows' => 5,
                        'teeny' => true,
                        'quicktags' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_requirements"><?php _e('Requisitos', 'travelcurator'); ?></label>
                </th>
                <td>
                    <?php
                    wp_editor($requirements, 'travelcurator_requirements', array(
                        'textarea_name' => 'travelcurator_requirements',
                        'media_buttons' => false,
                        'textarea_rows' => 5,
                        'teeny' => true,
                        'quicktags' => true
                    ));
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_featured"><?php _e('Pacote em Destaque', 'travelcurator'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" id="travelcurator_featured" name="travelcurator_featured" 
                               value="yes" <?php checked($featured, 'yes'); ?> />
                        <?php _e('Marcar este pacote como destaque', 'travelcurator'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Package pricing meta box callback
     */
    public function package_pricing_callback($post) {
        wp_nonce_field('travelcurator_package_pricing', 'travelcurator_package_pricing_nonce');

        // Get saved values
        $price = get_post_meta($post->ID, '_travelcurator_price', true);
        $price_per_person = get_post_meta($post->ID, '_travelcurator_price_per_person', true);
        $installments = get_post_meta($post->ID, '_travelcurator_installments', true);
        $discount = get_post_meta($post->ID, '_travelcurator_discount', true);
        $payment_terms = get_post_meta($post->ID, '_travelcurator_payment_terms', true);
        $cancellation_policy = get_post_meta($post->ID, '_travelcurator_cancellation_policy', true);
        ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="travelcurator_price"><?php _e('Preço Total (R$)', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="number" id="travelcurator_price" name="travelcurator_price" 
                           value="<?php echo esc_attr($price); ?>" step="0.01" min="0" style="width: 100%;" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_price_per_person">
                        <input type="checkbox" id="travelcurator_price_per_person" name="travelcurator_price_per_person" 
                               value="yes" <?php checked($price_per_person, 'yes'); ?> />
                        <?php _e('Preço por pessoa', 'travelcurator'); ?>
                    </label>
                </th>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_installments"><?php _e('Parcelamento', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="text" id="travelcurator_installments" name="travelcurator_installments" 
                           value="<?php echo esc_attr($installments); ?>" 
                           placeholder="<?php esc_attr_e('Ex: 12x sem juros', 'travelcurator'); ?>" 
                           style="width: 100%;" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_discount"><?php _e('Desconto (%)', 'travelcurator'); ?></label>
                </th>
                <td>
                    <input type="number" id="travelcurator_discount" name="travelcurator_discount" 
                           value="<?php echo esc_attr($discount); ?>" step="0.01" min="0" max="100" style="width: 100%;" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_payment_terms"><?php _e('Condições de Pagamento', 'travelcurator'); ?></label>
                </th>
                <td>
                    <textarea id="travelcurator_payment_terms" name="travelcurator_payment_terms" 
                              rows="4" style="width: 100%;"><?php echo esc_textarea($payment_terms); ?></textarea>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="travelcurator_cancellation_policy"><?php _e('Política de Cancelamento', 'travelcurator'); ?></label>
                </th>
                <td>
                    <textarea id="travelcurator_cancellation_policy" name="travelcurator_cancellation_policy" 
                              rows="4" style="width: 100%;"><?php echo esc_textarea($cancellation_policy); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Package gallery meta box callback
     */
    public function package_gallery_callback($post) {
        wp_nonce_field('travelcurator_package_gallery', 'travelcurator_package_gallery_nonce');

        $gallery = get_post_meta($post->ID, '_travelcurator_gallery', true);
        $gallery_ids = !empty($gallery) ? explode(',', $gallery) : array();
        ?>

        <div id="travelcurator_gallery_container">
            <div id="travelcurator_gallery_images">
                <?php if (!empty($gallery_ids)) : ?>
                    <?php foreach ($gallery_ids as $image_id) : 
                        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                        if ($image_url) : ?>
                            <div class="gallery-image" data-id="<?php echo esc_attr($image_id); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="" />
                                <button type="button" class="remove-image">&times;</button>
                            </div>
                        <?php endif;
                    endforeach; ?>
                <?php endif; ?>
            </div>

            <input type="hidden" id="travelcurator_gallery" name="travelcurator_gallery" 
                   value="<?php echo esc_attr($gallery); ?>" />

            <button type="button" id="add_gallery_images" class="button">
                <?php _e('Adicionar Imagens à Galeria', 'travelcurator'); ?>
            </button>
        </div>

        <style>
        #travelcurator_gallery_images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .gallery-image {
            position: relative;
            display: inline-block;
        }
        .gallery-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 2px solid #ddd;
            border-radius: 4px;
        }
        .gallery-image .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 12px;
            line-height: 1;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            // Add gallery images
            $('#add_gallery_images').click(function(e) {
                e.preventDefault();
                
                var mediaUploader = wp.media({
                    title: '<?php _e('Selecionar Imagens da Galeria', 'travelcurator'); ?>',
                    button: {
                        text: '<?php _e('Adicionar à Galeria', 'travelcurator'); ?>'
                    },
                    multiple: true
                });
                
                mediaUploader.on('select', function() {
                    var selection = mediaUploader.state().get('selection');
                    var gallery_ids = $('#travelcurator_gallery').val().split(',').filter(function(id) {
                        return id !== '';
                    });
                    
                    selection.each(function(attachment) {
                        var attachment_id = attachment.id;
                        if (gallery_ids.indexOf(attachment_id.toString()) === -1) {
                            gallery_ids.push(attachment_id);
                            
                            var thumbnail = attachment.attributes.sizes.thumbnail;
                            var image_url = thumbnail ? thumbnail.url : attachment.attributes.url;
                            
                            $('#travelcurator_gallery_images').append(
                                '<div class="gallery-image" data-id="' + attachment_id + '">' +
                                '<img src="' + image_url + '" alt="" />' +
                                '<button type="button" class="remove-image">&times;</button>' +
                                '</div>'
                            );
                        }
                    });
                    
                    $('#travelcurator_gallery').val(gallery_ids.join(','));
                });
                
                mediaUploader.open();
            });
            
            // Remove gallery image
            $(document).on('click', '.remove-image', function() {
                var image_container = $(this).parent();
                var image_id = image_container.data('id');
                var gallery_ids = $('#travelcurator_gallery').val().split(',').filter(function(id) {
                    return id !== '' && id != image_id;
                });
                
                $('#travelcurator_gallery').val(gallery_ids.join(','));
                image_container.remove();
            });
            
            // Make gallery sortable
            $('#travelcurator_gallery_images').sortable({
                update: function() {
                    var gallery_ids = [];
                    $('#travelcurator_gallery_images .gallery-image').each(function() {
                        gallery_ids.push($(this).data('id'));
                    });
                    $('#travelcurator_gallery').val(gallery_ids.join(','));
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Package itinerary meta box callback
     */
    public function package_itinerary_callback($post) {
        wp_nonce_field('travelcurator_package_itinerary', 'travelcurator_package_itinerary_nonce');

        $itinerary = get_post_meta($post->ID, '_travelcurator_itinerary', true);
        if (empty($itinerary)) {
            $itinerary = array(
                array('day' => 1, 'title' => '', 'description' => '')
            );
        }
        ?>

        <div id="travelcurator_itinerary_container">
            <div id="travelcurator_itinerary_days">
                <?php foreach ($itinerary as $index => $day) : ?>
                    <div class="itinerary-day" data-day="<?php echo esc_attr($index); ?>">
                        <div class="day-header">
                            <h4><?php printf(__('Dia %d', 'travelcurator'), $day['day']); ?></h4>
                            <button type="button" class="remove-day button-link-delete"><?php _e('Remover', 'travelcurator'); ?></button>
                        </div>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Título do Dia', 'travelcurator'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="travelcurator_itinerary[<?php echo esc_attr($index); ?>][title]" 
                                           value="<?php echo esc_attr($day['title']); ?>" 
                                           placeholder="<?php esc_attr_e('Ex: Chegada e Check-in', 'travelcurator'); ?>" 
                                           style="width: 100%;" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Descrição das Atividades', 'travelcurator'); ?></label>
                                </th>
                                <td>
                                    <textarea name="travelcurator_itinerary[<?php echo esc_attr($index); ?>][description]" 
                                              rows="4" style="width: 100%;"><?php echo esc_textarea($day['description']); ?></textarea>
                                </td>
                            </tr>
                        </table>
                        
                        <input type="hidden" name="travelcurator_itinerary[<?php echo esc_attr($index); ?>][day]" 
                               value="<?php echo esc_attr($day['day']); ?>" />
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="add_itinerary_day" class="button">
                <?php _e('Adicionar Dia ao Roteiro', 'travelcurator'); ?>
            </button>
        </div>

        <style>
        .itinerary-day {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
        }
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .day-header h4 {
            margin: 0;
        }
        </style>

        <script>
        jQuery(document).ready(function($) {
            var day_count = <?php echo count($itinerary); ?>;
            
            // Add new day
            $('#add_itinerary_day').click(function() {
                day_count++;
                var new_day = '<div class="itinerary-day" data-day="' + day_count + '">' +
                    '<div class="day-header">' +
                        '<h4><?php printf(__("Dia %d", "travelcurator"), "' + day_count + '"); ?></h4>' +
                        '<button type="button" class="remove-day button-link-delete"><?php _e("Remover", "travelcurator"); ?></button>' +
                    '</div>' +
                    '<table class="form-table">' +
                        '<tr>' +
                            '<th scope="row"><label><?php _e("Título do Dia", "travelcurator"); ?></label></th>' +
                            '<td><input type="text" name="travelcurator_itinerary[' + day_count + '][title]" placeholder="<?php esc_attr_e("Ex: Chegada e Check-in", "travelcurator"); ?>" style="width: 100%;" /></td>' +
                        '</tr>' +
                        '<tr>' +
                            '<th scope="row"><label><?php _e("Descrição das Atividades", "travelcurator"); ?></label></th>' +
                            '<td><textarea name="travelcurator_itinerary[' + day_count + '][description]" rows="4" style="width: 100%;"></textarea></td>' +
                        '</tr>' +
                    '</table>' +
                    '<input type="hidden" name="travelcurator_itinerary[' + day_count + '][day]" value="' + day_count + '" />' +
                '</div>';
                
                $('#travelcurator_itinerary_days').append(new_day);
            });
            
            // Remove day
            $(document).on('click', '.remove-day', function() {
                $(this).closest('.itinerary-day').remove();
            });
        });
        </script>
        <?php
    }

    /**
     * Save meta boxes data
     */
    public function save_meta_boxes($post_id) {
        // Check if our nonce is set and verify it
        if (!isset($_POST['travelcurator_package_details_nonce']) || 
            !wp_verify_nonce($_POST['travelcurator_package_details_nonce'], 'travelcurator_package_details')) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (isset($_POST['post_type']) && 'travel_package' == $_POST['post_type']) {
            if (!current_user_can('edit_posts', $post_id)) {
                return;
            }
        }

        // Save package details
        $fields = array(
            'travelcurator_duration',
            'travelcurator_location', 
            'travelcurator_start_date',
            'travelcurator_end_date',
            'travelcurator_max_participants',
            'travelcurator_min_participants',
            'travelcurator_includes',
            'travelcurator_not_includes',
            'travelcurator_requirements'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Save featured status
        if (isset($_POST['travelcurator_featured'])) {
            update_post_meta($post_id, '_travelcurator_featured', 'yes');
        } else {
            update_post_meta($post_id, '_travelcurator_featured', 'no');
        }

        // Save pricing data
        if (isset($_POST['travelcurator_package_pricing_nonce']) && 
            wp_verify_nonce($_POST['travelcurator_package_pricing_nonce'], 'travelcurator_package_pricing')) {
            
            $pricing_fields = array(
                'travelcurator_price',
                'travelcurator_installments',
                'travelcurator_discount',
                'travelcurator_payment_terms',
                'travelcurator_cancellation_policy'
            );

            foreach ($pricing_fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }

            // Save price per person checkbox
            if (isset($_POST['travelcurator_price_per_person'])) {
                update_post_meta($post_id, '_travelcurator_price_per_person', 'yes');
            } else {
                update_post_meta($post_id, '_travelcurator_price_per_person', 'no');
            }
        }

        // Save gallery
        if (isset($_POST['travelcurator_package_gallery_nonce']) && 
            wp_verify_nonce($_POST['travelcurator_package_gallery_nonce'], 'travelcurator_package_gallery')) {
            
            if (isset($_POST['travelcurator_gallery'])) {
                update_post_meta($post_id, '_travelcurator_gallery', sanitize_text_field($_POST['travelcurator_gallery']));
            }
        }

        // Save itinerary
        if (isset($_POST['travelcurator_package_itinerary_nonce']) && 
            wp_verify_nonce($_POST['travelcurator_package_itinerary_nonce'], 'travelcurator_package_itinerary')) {
            
            if (isset($_POST['travelcurator_itinerary']) && is_array($_POST['travelcurator_itinerary'])) {
                $itinerary = array();
                foreach ($_POST['travelcurator_itinerary'] as $day) {
                    $itinerary[] = array(
                        'day' => intval($day['day']),
                        'title' => sanitize_text_field($day['title']),
                        'description' => sanitize_textarea_field($day['description'])
                    );
                }
                update_post_meta($post_id, '_travelcurator_itinerary', $itinerary);
            }
        }
    }
}