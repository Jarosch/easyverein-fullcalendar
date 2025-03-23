<?php
if (! defined('ABSPATH') ) {
    exit;
}
    
    // Statics
    define('EASYVEREIN_REGISTER_ADMIN_CSS', plugin_dir_url(__FILE__).'admin/css/');
    define('EASYVEREIN_REGISTER_PUBLIC_CSS', plugin_dir_url(__FILE__).'public/css/');
    define('EASYVEREIN_REGISTER_ADMIN_JS', plugin_dir_url(__FILE__).'admin/js/');
    define('EASYVEREIN_REGISTER_ADMIN_INCLUDES', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)).'/admin/includes/');
    define('EASYVEREIN_REGISTER_HELPER', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)).'/helper/');
    
    // Load install hook
    require_once WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)) . '/install.php';

    // Admin settings page
    require_once 'admin/easyVereinSettings.php';

    // Shortcodes page
    require_once 'public/easyVereinRegisterShortcodes.php';
?>