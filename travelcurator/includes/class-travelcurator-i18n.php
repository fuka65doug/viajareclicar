<?php
/**
 * Define the internationalization functionality
 *
 * @package TravelCurator
 */

if (!defined('ABSPATH')) {
    exit;
}

class TravelCurator_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'travelcurator',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

    /**
     * Initialize the internationalization functionality
     */
    public function init() {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
        add_action('init', array($this, 'register_strings'));
        add_filter('locale', array($this, 'set_locale'));
    }

    /**
     * Register translatable strings for WPML/Polylang
     */
    public function register_strings() {
        if (function_exists('pll_register_string')) {
            // Polylang integration
            pll_register_string('travelcurator', 'Request Quote Button', 'travelcurator');
            pll_register_string('travelcurator', 'View Details Button', 'travelcurator');
            pll_register_string('travelcurator', 'WhatsApp Message', 'travelcurator');
            pll_register_string('travelcurator', 'Contact Form Title', 'travelcurator');
            pll_register_string('travelcurator', 'Success Message', 'travelcurator');
            pll_register_string('travelcurator', 'Error Message', 'travelcurator');
        }

        if (function_exists('icl_register_string')) {
            // WPML integration
            icl_register_string('travelcurator', 'Request Quote Button', __('Request Quote', 'travelcurator'));
            icl_register_string('travelcurator', 'View Details Button', __('View Details', 'travelcurator'));
            icl_register_string('travelcurator', 'WhatsApp Message', __('Hi! I\'m interested in your travel packages.', 'travelcurator'));
            icl_register_string('travelcurator', 'Contact Form Title', __('Request Your Quote', 'travelcurator'));
            icl_register_string('travelcurator', 'Success Message', __('Thank you! We\'ll contact you soon.', 'travelcurator'));
            icl_register_string('travelcurator', 'Error Message', __('An error occurred. Please try again.', 'travelcurator'));
        }
    }

    /**
     * Set plugin locale based on WordPress locale
     */
    public function set_locale($locale) {
        // Only modify locale for our plugin context
        if (isset($_REQUEST['travelcurator_locale'])) {
            return sanitize_text_field($_REQUEST['travelcurator_locale']);
        }
        return $locale;
    }

    /**
     * Get available languages
     */
    public function get_available_languages() {
        $languages = array(
            'pt_BR' => 'Português (Brasil)',
            'en_US' => 'English (US)',
            'es_ES' => 'Español',
            'fr_FR' => 'Français',
            'de_DE' => 'Deutsch',
            'it_IT' => 'Italiano'
        );

        return apply_filters('travelcurator_available_languages', $languages);
    }

    /**
     * Get current language
     */
    public function get_current_language() {
        // WPML
        if (function_exists('icl_get_current_language')) {
            return icl_get_current_language();
        }

        // Polylang
        if (function_exists('pll_current_language')) {
            return pll_current_language();
        }

        // WordPress locale
        return get_locale();
    }

    /**
     * Get default language
     */
    public function get_default_language() {
        // WPML
        if (function_exists('icl_get_default_language')) {
            return icl_get_default_language();
        }

        // Polylang
        if (function_exists('pll_default_language')) {
            return pll_default_language();
        }

        // WordPress default
        return 'pt_BR';
    }

    /**
     * Translate string with fallback
     */
    public function translate_string($string, $context = 'travelcurator', $language = null) {
        if (!$language) {
            $language = $this->get_current_language();
        }

        // WPML
        if (function_exists('icl_t')) {
            return icl_t($context, $string, $string, false, false, $language);
        }

        // Polylang
        if (function_exists('pll__')) {
            return pll__($string);
        }

        // WordPress default
        return __($string, 'travelcurator');
    }

    /**
     * Get multilingual post/page ID
     */
    public function get_translated_post_id($post_id, $language = null) {
        if (!$language) {
            $language = $this->get_current_language();
        }

        // WPML
        if (function_exists('icl_object_id')) {
            return icl_object_id($post_id, 'travel_package', true, $language);
        }

        // Polylang
        if (function_exists('pll_get_post')) {
            $translated_id = pll_get_post($post_id, $language);
            return $translated_id ? $translated_id : $post_id;
        }

        return $post_id;
    }

    /**
     * Get language flag URL
     */
    public function get_language_flag_url($language_code) {
        $flags = array(
            'pt_BR' => TRAVELCURATOR_PLUGIN_URL . 'admin/images/flags/br.png',
            'en_US' => TRAVELCURATOR_PLUGIN_URL . 'admin/images/flags/us.png',
            'es_ES' => TRAVELCURATOR_PLUGIN_URL . 'admin/images/flags/es.png',
            'fr_FR' => TRAVELCURATOR_PLUGIN_URL . 'admin/images/flags/fr.png',
            'de_DE' => TRAVELCURATOR_PLUGIN_URL . 'admin/images/flags/de.png',
            'it_IT' => TRAVELCURATOR_PLUGIN_URL . 'admin/images/flags/it.png'
        );

        return isset($flags[$language_code]) ? $flags[$language_code] : '';
    }

    /**
     * Format currency by language
     */
    public function format_currency($amount, $currency = 'BRL', $language = null) {
        if (!$language) {
            $language = $this->get_current_language();
        }

        $amount = floatval($amount);

        switch ($language) {
            case 'pt_BR':
                return 'R$ ' . number_format($amount, 2, ',', '.');
            case 'en_US':
                if ($currency === 'USD') {
                    return '$' . number_format($amount, 2, '.', ',');
                }
                return '$' . number_format($amount * 0.2, 2, '.', ','); // Approximate conversion
            case 'es_ES':
                if ($currency === 'EUR') {
                    return '€' . number_format($amount, 2, ',', '.');
                }
                return '€' . number_format($amount * 0.17, 2, ',', '.'); // Approximate conversion
            case 'fr_FR':
                return number_format($amount, 2, ',', ' ') . ' €';
            case 'de_DE':
                return number_format($amount, 2, ',', '.') . ' €';
            case 'it_IT':
                return '€ ' . number_format($amount, 2, ',', '.');
            default:
                return 'R$ ' . number_format($amount, 2, ',', '.');
        }
    }

    /**
     * Format date by language
     */
    public function format_date($date, $format = null, $language = null) {
        if (!$language) {
            $language = $this->get_current_language();
        }

        if (!$format) {
            switch ($language) {
                case 'pt_BR':
                    $format = 'd/m/Y';
                    break;
                case 'en_US':
                    $format = 'm/d/Y';
                    break;
                case 'es_ES':
                case 'fr_FR':
                case 'it_IT':
                    $format = 'd/m/Y';
                    break;
                case 'de_DE':
                    $format = 'd.m.Y';
                    break;
                default:
                    $format = 'd/m/Y';
            }
        }

        if (is_string($date)) {
            $date = strtotime($date);
        }

        return date($format, $date);
    }

    /**
     * Get localized difficulty levels
     */
    public function get_difficulty_levels($language = null) {
        if (!$language) {
            $language = $this->get_current_language();
        }

        $difficulties = array(
            'pt_BR' => array(
                'easy' => 'Fácil',
                'moderate' => 'Moderado',
                'hard' => 'Difícil'
            ),
            'en_US' => array(
                'easy' => 'Easy',
                'moderate' => 'Moderate',
                'hard' => 'Hard'
            ),
            'es_ES' => array(
                'easy' => 'Fácil',
                'moderate' => 'Moderado',
                'hard' => 'Difícil'
            ),
            'fr_FR' => array(
                'easy' => 'Facile',
                'moderate' => 'Modéré',
                'hard' => 'Difficile'
            ),
            'de_DE' => array(
                'easy' => 'Einfach',
                'moderate' => 'Moderat',
                'hard' => 'Schwer'
            ),
            'it_IT' => array(
                'easy' => 'Facile',
                'moderate' => 'Moderato',
                'hard' => 'Difficile'
            )
        );

        return isset($difficulties[$language]) ? $difficulties[$language] : $difficulties['pt_BR'];
    }

    /**
     * Get localized travel duration labels
     */
    public function get_duration_labels($language = null) {
        if (!$language) {
            $language = $this->get_current_language();
        }

        $labels = array(
            'pt_BR' => array(
                'days' => 'dias',
                'day' => 'dia',
                'nights' => 'noites',
                'night' => 'noite'
            ),
            'en_US' => array(
                'days' => 'days',
                'day' => 'day',
                'nights' => 'nights',
                'night' => 'night'
            ),
            'es_ES' => array(
                'days' => 'días',
                'day' => 'día',
                'nights' => 'noches',
                'night' => 'noche'
            ),
            'fr_FR' => array(
                'days' => 'jours',
                'day' => 'jour',
                'nights' => 'nuits',
                'night' => 'nuit'
            ),
            'de_DE' => array(
                'days' => 'Tage',
                'day' => 'Tag',
                'nights' => 'Nächte',
                'night' => 'Nacht'
            ),
            'it_IT' => array(
                'days' => 'giorni',
                'day' => 'giorno',
                'nights' => 'notti',
                'night' => 'notte'
            )
        );

        return isset($labels[$language]) ? $labels[$language] : $labels['pt_BR'];
    }

    /**
     * Generate language switcher
     */
    public function language_switcher($show_flags = true, $show_names = true) {
        $current_lang = $this->get_current_language();
        $available_langs = $this->get_available_languages();

        if (count($available_langs) <= 1) {
            return '';
        }

        $output = '<div class="travelcurator-language-switcher">';
        
        foreach ($available_langs as $lang_code => $lang_name) {
            $is_current = ($lang_code === $current_lang);
            $class = $is_current ? 'current-language' : 'language-option';
            
            $output .= '<a href="' . esc_url(add_query_arg('lang', $lang_code)) . '" class="' . esc_attr($class) . '">';
            
            if ($show_flags) {
                $flag_url = $this->get_language_flag_url($lang_code);
                if ($flag_url) {
                    $output .= '<img src="' . esc_url($flag_url) . '" alt="' . esc_attr($lang_name) . '" width="20" height="15">';
                }
            }
            
            if ($show_names) {
                $output .= '<span class="language-name">' . esc_html($lang_name) . '</span>';
            }
            
            $output .= '</a>';
        }
        
        $output .= '</div>';

        return $output;
    }

    /**
     * RTL language support
     */
    public function is_rtl_language($language = null) {
        if (!$language) {
            $language = $this->get_current_language();
        }

        $rtl_languages = array('ar', 'he', 'fa', 'ur');
        $lang_code = substr($language, 0, 2);

        return in_array($lang_code, $rtl_languages);
    }

    /**
     * Get text direction for current language
     */
    public function get_text_direction($language = null) {
        return $this->is_rtl_language($language) ? 'rtl' : 'ltr';
    }
}

// Initialize internationalization
new TravelCurator_i18n();