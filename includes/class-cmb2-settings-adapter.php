<?php

namespace Deep_Web_Solutions\Plugins\CMB2;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter;
use Deep_Web_Solutions\Admin\Settings\DWS_Adapter_Base;

if (!defined('ABSPATH')) { exit; }
/**
 * Adapter for the CMB2 plugin.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 */
final class DWS_CMB2_Adapter extends DWS_Adapter_Base implements DWS_Adapter {
    //region CLASS INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Adapter_Base::set_framework_slug()
     */
    public function set_fields() {
        $this->framework_slug = 'cmb2';
        $this->init_hook = 'cmb2_init';
    }

    //endregion

    //region INTERFACE INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $page_title
     * @param   string  $menu_title
     * @param   string  $capability
     * @param   string  $menu_slug
     * @param   array   $other
     *
     * @return  false|array     The validated and final page settings.
     */
    public static function register_settings_page($page_title, $menu_title, $capability, $menu_slug, $other = array()) {
        if (!function_exists('new_cmb2_box'))  { return false; }
        $args = wp_parse_args($other, array(
            'id'                        => md5($menu_slug),
            'title'                     => $page_title,
            'object_types'              => array( 'options-page' ),
            'option_key'                => $menu_slug,
            'icon_url'                  => '',
            'menu_title'                => $menu_title,
            'position'                  => '',
            'parent_slug'               => '',
            'capability'                => $capability,
            'display_cb'                => false,
            'save_button'               => __('Save', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
            'disable_settings_errors'   => false,
            'message_cb'                => ''
        ));
        new_cmb2_box($args);
        return $args;
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $parent_slug
     * @param   string  $page_title
     * @param   string  $menu_title
     * @param   string  $capability
     * @param   string  $menu_slug
     * @param   array   $other
     *
     * @return  false|array
     */
    public static function register_settings_subpage($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $other = array()) {
        if (!function_exists('new_cmb2_box')) { return false; }
        $args = wp_parse_args($other, array(
            'id'                        => md5($menu_slug),
            'title'                     => $page_title,
            'object_types'              => array( 'options-page' ),
            'option_key'                => $menu_slug,
            'icon_url'                  => '',
            'menu_title'                => $menu_title,
            'parent_slug'               => $parent_slug,
            'capability'                => $capability,
            'position'                  => '',
            'display_cb'                => false,
            'save_button'               => __('Save', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
            'disable_settings_errors'   => false,
            'message_cb'                => ''
        ));
        new_cmb2_box($args);
        return $args;
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $key
     * @param   string  $title
     * @param   string  $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_settings_page_group($key, $title, $location, $fields = null, $other = array()) {
        if (!class_exists('CMB2')) { return; }
        if (isset($other['fields']) && !empty($other['fields']) && empty($fields)) {
            $fields = $other['fields'];
            unset($other['fields']);
        }
        $args = wp_parse_args($other, array(
            'id' => $key,
            'type' => 'group',
            'repeatable'  => false,
            'options' => array(
                'group_title'       => $title
            )
        ));
        $cmb = cmb2_get_metabox(md5($location));
        $group_field_id = $cmb->add_field($args);
        if (isset($fields)) {
            foreach ($fields as $field) {
                if ($field['type'] == 'repeater') {
                    $other = array(
                        'id' => $field['key'],
                        'type' => 'group',
                        'repeatable'  => true,
                        'options' => array(
                            'group_title'       => isset($field['title']) ? $field['title'] : $field['label']
                        ),
                        'fields' => $field['sub_fields']
                    );
                    self::register_settings_page_group($field['key'], $field['title'], $location, null, $other);
                } else {
                    self::register_field_to_group($group_field_id, $field['key'], $field['type'], $field, $location);
                }
            }
        }
    }

    /**
     * @since   2.0.2
     * @version 2.0.2
     *
     * @param   string  $key
     * @param   string  $title
     * @param   array   $location
     * @param   array   $fields
     * @param   array   $other
     */
    public static function register_generic_group($key, $title, $location, $fields, $other = array()) {
        if (!class_exists('CMB2')) { return; }
        if (isset($other['fields']) && !empty($other['fields']) && empty($fields)) {
            $fields = $other['fields'];
            unset($other['fields']);
        }
        $args = wp_parse_args($other, array(
            'id' => $key,
            'type' => 'group',
            'repeatable'  => false,
            'options' => array(
                'group_title'       => $title
            )
        ));
        $cmb = cmb2_get_metabox(md5($location[0][0]['value']));
        $group_field_id = $cmb->add_field($args);
        if (isset($fields)) {
            foreach ($fields as $field) {
                if ($field['type'] == 'repeater') {
                    $other = array(
                        'id' => $field['key'],
                        'type' => 'group',
                        'repeatable'  => true,
                        'options' => array(
                            'group_title'       => isset($field['title']) ? $field['title'] : $field['label']
                        ),
                        'fields' => $field['sub_fields']
                    );
                    self::register_generic_group($field['key'], $field['title'], $location, null, $other);
                } else {
                    self::register_field_to_group($group_field_id, $field['key'], $field['type'], $field, $location[0][0]['value']);
                }
            }
        }
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string              $group_id
     * @param   string              $key
     * @param   string              $type
     * @param   array               $parameters
     * @param   string              $location
     */
    public static function register_field_to_group($group_id, $key, $type, $parameters, $location) {
        if (!class_exists('CMB2')) { return; }
        $cmb = cmb2_get_metabox(md5($location));
        $cmb->add_group_field($group_id, self::formatting_settings_field($key, $type, $parameters));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $key
     * @param   string  $type
     * @param   string  $location
     * @param   array   $parameters
     * @param   null    $parent_id
     *
     */
    public static function register_field($key, $type, $location, $parameters, $parent_id = null) {
        if (!function_exists('add_field')) { return; }
        $cmb = cmb2_get_metabox(md5($location));
        $cmb->add_field(self::formatting_settings_field($key, $type, $parameters));
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string  $field
     * @param   string  $option_page_slug
     *
     * @return  mixed   Option value.
     */
    public static function get_settings_field_value($field, $option_page_slug) {
        if (!class_exists('CMB2')) { return null; }
        $settings = get_option($option_page_slug, array());
        if (!is_array($settings)) { return null; }
        foreach ($settings as $groups) {
            foreach ($groups as $group) {
                if (in_array($group[$field], $group)) {
                    return $group[$field];
                }
            }
        }
        return null;
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string      $field
     * @param   false|int   $post_id
     *
     * @return  mixed   Option value.
     */
    public static function get_field_value($field, $post_id = false) {
        if (!function_exists('get_post_meta')) { return null; }
        return get_post_meta($post_id, $field);
    }

    //endregion

    //region HELPERS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @param   string              $key
     * @param   string              $type
     * @param   array               $parameters
     *
     * @return  array   Formatted array for registering generic ACF field
     */
    private static function formatting_settings_field($key, $type, $parameters) {
//        We will ignore conditional logic for CMB2 for now
//        if (isset($parameters['conditional_logic']) && !empty($parameters['conditional_logic'])) {
//            $parameters['show_on_cb'] = 'cmb_show_on_meta_value';
        unset($parameters['conditional_logic']);
//        }
        if(isset($parameters['label']) && !empty($parameters['label'])) {
            $parameters['name'] = $parameters['label'];
        }
        if(isset($parameters['wrapper']['class']) && !empty($parameters['wrapper']['class'])) {
            $parameters['classes'] = $parameters['wrapper']['class'];
            if($type != 'color_picker' || $type != 'colorpicker') { unset($parameters['wrapper']); }
        }
        $args = wp_parse_args($parameters, array(
            'desc'          => $parameters['instructions'],
            'id'            => $key,
            'type'          => $type,
            'repeatable'    => false,
            'default'       => $parameters['default_value'],
            'show_names'    => true
        ));
        switch ($args['type']) {
            case 'wysiwyg':
            case 'multicheck':
            case 'multicheck_inline':
            case 'radio':
            case 'radio_inline':
            case 'image':
            case 'file':
            case 'text_small':
            case 'text_medium':
            case 'text_email':
            case 'text_money':
            case 'textarea':
            case 'textarea_small':
            case 'textarea_code':
            case 'oembed':
            case 'checkbox':
            case 'hidden':
            case 'select_timezone':
            case 'text':
                break;
            case 'text_date_timestamp':
            case 'text_datetime_timestamp':
            case 'text_datetime_timestamp_timezone':
            case 'text_date':
                $args = wp_parse_args($args, array(
                    'date_format'       => 'l jS \of F Y'
                ));
                break;
            case 'taxonomy_radio_inline':
            case 'taxonomy_radio_hierarchical':
            case 'taxonomy_multicheck':
            case 'taxonomy_multicheck_inline':
            case 'taxonomy_multicheck_hierarchical':
            case 'taxonomy_radio':
            case 'taxonomy_select':
                $args = wp_parse_args($args, array(
                    'remove_default'    => true
                ));
                break;
            case 'taxonomy':
                $args['type'] = 'taxonomy_select';
                $args = wp_parse_args($args, array(
                    'remove_default'    => true
                ));
                break;
            case 'text_time':
            case 'time_picker':
                $args['type'] = 'text_time';
                break;
            case 'text_url':
            case 'url':
                $args['type'] = 'text_url';
                $args = wp_parse_args($args, array(
                    'protocols' => array( 'http', 'https', 'ftp', 'ftps', 'mailto', 'news', 'irc', 'gopher', 'nntp', 'feed', 'telnet' )
                ));
                break;
            case 'gallery':
            case 'file_list':
                $args['type'] = 'file_list';
                $args = wp_parse_args($args, array(
                    'preview_size' => array(50, 50)
                ));
                break;
            case 'number':
            case 'password':
                $args['type'] = 'text';
                break;
            case 'email':
                $args['type'] = 'text_email';
                break;
            case 'select':
                $args['type'] = 'select';
                $args['options'] = isset($parameters['choices']) ? $parameters['choices'] : $parameters['options'];
                break;
            case 'true_false':
                $args['type'] = 'select';
                $args['options'] = array(
                    'false' => __('False', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN),
                    'true'  => __('True', DWS_CUSTOM_EXTENSIONS_LANG_DOMAIN)
                );
                break;
            case 'colorpicker':
            case 'color_picker':
                $args['type'] = 'colorpicker';
                break;
            case 'acf_code_field':
                $args['type'] = 'textarea_code';
                break;
            default:
                $args['type'] = 'text';
                error_log("The field type \"" . $type . "\" for field " . $key . " is not available in CMB2 and its adapter. Defaulting to text field type.");
        }
        return $args;
    }

//    /**
//     * @since   2.0.0
//     * @version 2.0.0
//     *
//     * @param   object  $field
//     *
//     * @return  bool    display or not
//     */
//    function cmb_show_on_meta_value( $field ) {
//        $field_id = $field['conditional_logic'][0][0]['field'];
//        $operator = $field['conditional_logic'][0][0]['field'];
//        $wanted_value = $field['conditional_logic'][0][0]['value'];
//
//        $post_id = 0;
//
//        // If we're showing it based on ID, get the current ID
//        if (isset($_GET['post'])) {
//            $post_id = $_GET['post'];
//        } elseif (isset($_POST['post_ID'])) {
//            $post_id = $_POST['post_ID'];
//        }
//
//        if (! $post_id) {
//            return 1;
//        }
//
//        $value = get_post_meta( $post_id, $field_id, true );
//
//        if (empty($wanted_value)) {
//            return 1;
//        }
//
//        return self::compare($value, $operator, $wanted_value);
//    }
//
//    /**
//     * @since   2.0.0
//     * @version 2.0.0
//     *
//     * @param   $field_value
//     * @param   $operator
//     * @param   $wanted_value
//     *
//     * @return  bool
//     */
//    public static function compare($field_value, $operator, $wanted_value) {
//
//        if (is_numeric($wanted_value)) { $wanted_value = intval($wanted_value); }
//        if ($wanted_value === 'false') { $wanted_value = false; }
//        if ($wanted_value === 'true') { $wanted_value = true; }
//
//        switch ($operator) {
//            case '==':
//                $result = $field_value == $wanted_value ? true : false;
//                break;
//            case '===':
//                $result = $field_value === $wanted_value ? true : false;
//                break;
//            case '!=':
//                $result = $field_value != $wanted_value ? true : false;
//                break;
//            case '<>':
//                $result = $field_value <> $wanted_value ? true : false;
//                break;
//            case '!==':
//                $result = $field_value !== $wanted_value ? true : false;
//                break;
//            case '<':
//                $result = $field_value < $wanted_value ? true : false;
//                break;
//            case '>':
//                $result = $field_value > $wanted_value ? true : false;
//                break;
//            case '<=':
//                $result = $field_value <= $wanted_value ? true : false;
//                break;
//            case '>=':
//                $result = $field_value >= $wanted_value ? true : false;
//                break;
//            default:
//                $result = false;
//        }
//        return $result;
//    }

    //endregion
}