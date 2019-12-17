<?php
if (!defined('WPINC')) {
    die('security by preventing any direct access to your plugin file');
}
if (!function_exists('write_log')) {

    function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}

final class WMS_Shipping
{
    protected static $instance = null;

    public function __construct() {
        $this->plugins_loaded();
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function plugins_loaded() {
        require_once "WMS_Hooks.php";
        $this->load_plugin_textdomain();
    }

    public function load_plugin_textdomain() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        unload_textdomain('woo-maji-shopping');
        load_textdomain('woo-maji-shopping', WP_LANG_DIR . '/woo-maji-shopping/woo-maji-shopping-' . $locale . '.mo');
        load_plugin_textdomain('woo-maji-shopping', false, plugin_basename(dirname(WMS_PLUGIN_FILE)) . '/languages');
    }


}

/**
 * @return null|WMS_Shipping
 */
function WMSInit() {
    return WMS_Shipping::get_instance();
}

register_activation_hook(WMS_PLUGIN_FILE, [WFSBP_Install::class, 'activate']);

add_action('plugins_loaded', 'WMSInit');