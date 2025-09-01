<?php
namespace SkiRideServetech\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class Form_Content_Controls
{

    public static function register($widget)
    {
        /**
         * ============================
         * FORM TITLE
         * ============================
         */
        $widget->start_controls_section(
            'section_form_title',
            [
                'label' => __('Form Heading', 'ski-ride-servetech'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $widget->add_control(
            'form_title',
            [
                'label' => __('Form Title', 'ski-ride-servetech'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Book Your Ride', 'ski-ride-servetech'),
            ]
        );

        $widget->end_controls_section();

        /**
         * ============================
         * STEP CONTENT
         * ============================
         */
        $widget->start_controls_section(
            'steps_section',
            [
                'label' => __('Steps Content only 5 step can be added ', 'ski-ride-servetech'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $step_repeater = new Repeater();

        $step_repeater->add_control(
            'step_heading',
            [
                'label' => __('Step Heading', 'ski-ride-servetech'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Step Title', 'ski-ride-servetech'),
                'label_block' => true,
            ]
        );

        // Step Subtitle
        $step_repeater->add_control(
            'step_subtitle',
            [
                'label' => __('Step Subtitle', 'ski-ride-servetech'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('This is the step description.', 'ski-ride-servetech'),
                'label_block' => true,
            ]
        );

        $widget->add_control(
            'steps',
            [
                'label' => __('Steps List', 'ski-ride-servetech'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $step_repeater->get_controls(),
                'default' => [
                    [
                        'step_heading' => __('Step 1', 'ski-ride-servetech'),
                        'step_subtitle' => __('This is step 1 description.', 'ski-ride-servetech'),
                    ],
                    [
                        'step_heading' => __('Step 2', 'ski-ride-servetech'),
                        'step_subtitle' => __('This is step 2 description.', 'ski-ride-servetech'),
                    ],
                ],
                'title_field' => '{{{ step_heading }}}',
            ]
        );

        $widget->end_controls_section();
    }
}
