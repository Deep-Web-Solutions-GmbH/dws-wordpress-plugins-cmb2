<?php

namespace Deep_Web_Solutions\Plugins;
use Deep_Web_Solutions\Base\DWS_Functionality_Template;

if (!defined('ABSPATH')) { exit; }

/**
 * Adapter for the CMB2 plugin.
 *
 * @since   2.0.0
 * @version 2.0.0
 * @author  Fatine Tazi <f.tazi@deep-web-solutions.de>
 *
 * @wordpress-plugin
 * Plugin Name:         DeepWebSolutions CMB2 Compatibility
 * Description:         This plugin handles all the core custom extensions to the 'CMB2' plugin.
 * Version:             2.0.0
 * Author:              Deep Web Solutions GmbH
 * Author URI:          https://www.deep-web-solutions.de
 * License:             GPL-3.0+
 * License URI:         http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:         dws_custom-extensions_dh8gfh38g7hr38gd
 * Domain Path:         /languages
 */
final class CMB2_Compatibility extends DWS_Functionality_Template {
    //region INHERITED FUNCTIONS

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Functionality_Template::are_prerequisites_fulfilled()
     *
     * @return  bool
     */
    protected static function are_prerequisites_fulfilled() {
        return is_plugin_active('cmb2/init.php');
    }

    /**
     * @since   2.0.0
     * @version 2.0.0
     *
     * @see     DWS_Functionality_Template::load_dependencies()
     */
    protected function load_dependencies() {
        /** @noinspection PhpIncludeInspection */
        /** Force load CMB2 at this point in time ... */
        require_once(WP_PLUGIN_DIR . '/cmb2/init.php');

        /** @noinspection PhpIncludeInspection */
        /** The CMB2 Adapter. */
        require_once(self::get_includes_base_path() . 'class-cmb2-settings-adapter.php');
        CMB2\DWS_CMB2_Adapter::maybe_initialize_singleton('fbgvnh7ufv9847hnu3veo', true, self::get_root_id());
    }

    //endregion
} CMB2_Compatibility::maybe_initialize_singleton('fny47ytvihnvcjvn8nuhyce8', true);