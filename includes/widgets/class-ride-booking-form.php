<?php
namespace SkiRideServetech\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit;

// Style Controls 

require_once __DIR__ . '/inc/elementor-controls/form-style-controls.php';
require_once __DIR__ . '/inc/elementor-controls/form-content-controls.php';


class Ride_Booking_Form extends Widget_Base
{

    public function get_name()
    {
        return 'ride-booking-form';
    }

    public function get_title()
    {
        return __('Ride Booking Form', 'ski-ride-servetech');
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['general']; // You can create your own category later
    }

    public function get_keywords()
    {
        return ['booking', 'form', 'ride', 'ski'];
    }

    protected function register_controls()
    {
        // Content Controls 
        Form_Content_Controls::register($this);

        //Styling Controls
        Form_Style_Controls::register($this);

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        // This will create PHP variables for each control
        extract($settings);

        // Pass settings to template
        $form_title = !empty($settings['form_title']) ? $settings['form_title'] : __('Book Your Ride', 'ski-ride-servetech');

        // Load template file
        $template = SRS_PLUGIN_PATH . 'includes/widgets/templates/ride-booking-form.php';

        if (file_exists($template)) {
            include $template;
        } else {
            echo '<p>' . esc_html__('Template not found.', 'ski-ride-servetech') . '</p>';
        }
    }

}
