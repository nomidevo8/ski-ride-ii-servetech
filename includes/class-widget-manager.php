<?php
namespace SkiRideServetech;

if (!defined('ABSPATH'))
    exit;

class Widget_Manager
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
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_widgets($widgets_manager)
    {
        require_once SRS_PLUGIN_PATH . 'includes/widgets/class-ride-booking-form.php';
        $widgets_manager->register(new \SkiRideServetech\Widgets\Ride_Booking_Form());
    }

    public function enqueue_scripts()
    {
        // Later: add your CSS + JS for form steps
        wp_enqueue_style('srs-style', SRS_PLUGIN_URL . 'assets/css/style.css', [], time());
        wp_enqueue_script('srs-script', SRS_PLUGIN_URL . 'assets/js/script.js', ['jquery'], '1.0.0', true);


        // CSS
        wp_enqueue_style(
            'bs-stepper',
            'https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css',
            [],
            '1.7.0'
        );
        // Scripts
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'bs-stepper',
            'https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js',
            ['jquery'],
            '1.7.0',
            true
        );
        wp_enqueue_script('srs-script', SRS_PLUGIN_URL . 'assets/js/script.js', ['jquery', 'jquery-steps'], time(), true);
    }

}
