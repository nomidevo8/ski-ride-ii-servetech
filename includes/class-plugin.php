<?php
namespace SkiRideServetech;

if (!defined('ABSPATH'))
    exit;

class Plugin
{
    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        // Elementor Init
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init()
    {
        // Check Elementor
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'elementor_missing_notice']);
            return;
        }

        // Check for Woocomerce 
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', [$this, 'woocommerce_missing_notice'] );
            return;
        }

        // Load widget manager
        require_once SRS_PLUGIN_PATH . 'includes/class-widget-manager.php';
        Widget_Manager::instance();

        // Front-end Hooks 

        require_once SRS_PLUGIN_PATH . 'includes/hooks/front-end-hooks.php';
        \SkiRideServetech\Hooks\Frontend_Hooks::instance();

        // Load widget manager
        require_once SRS_PLUGIN_PATH . 'includes/woocommerce/init.php';
        \SkiRideServetech\Wocommerce\Wocommerce_Init::instance();

        // Load admin settings page
        if (is_admin()) {
            require_once SRS_PLUGIN_PATH . 'includes/admin/admin-settings-page.php';
            require_once SRS_PLUGIN_PATH . 'includes/admin/admin-customer-groups-page.php';
            \SkiRideAdminServetech\Admin_settings_page::instance();
            \SkiRideAdminGroupsServetech\Admin_groups_page::instance();
        }
    }

    public function elementor_missing_notice()
    {
        echo '<div class="notice notice-error"><p>';
        esc_html_e('Ski Ride Servetech requires Elementor to be installed and activated.', 'ski-ride-servetech');
        echo '</p></div>';
    }

    public function woocommerce_missing_notice() {
        echo '<div class="notice notice-error"><p>';
        esc_html_e('Ski Ride Servetech requires WooCommerce to be installed and activated.', 'ski-ride-servetech');
        echo '</p></div>';
    }
}
