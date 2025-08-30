<?php
namespace SkiRideServetech\Widgets;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class Form_Content_Controls {

    public static function register($widget) {
        /**
         * ============================
         * FORM TITLE
         * ============================
         */
        $widget->start_controls_section(
            'section_form_title',
            [
                'label' => __('Form Heading', 'ski-ride-servetech'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $widget->add_control(
            'form_title',
            [
                'label'   => __('Form Title', 'ski-ride-servetech'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Book Your Ride', 'ski-ride-servetech'),
            ]
        );

        $widget->end_controls_section();

        /**
         * ============================
         * LOCATIONS
         * ============================
         */
        $widget->start_controls_section(
            'section_locations',
            [
                'label' => __('Locations', 'ski-ride-servetech'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $location_repeater = new Repeater();
        $location_repeater->add_control(
            'location_name',
            [
                'label' => __('Location Name', 'ski-ride-servetech'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $widget->add_control(
            'locations',
            [
                'label'       => __('Add Locations', 'ski-ride-servetech'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $location_repeater->get_controls(),
                'title_field' => '{{{ location_name }}}',
            ]
        );

        $widget->end_controls_section();

        /**
         * ============================
         * RENTING OPTIONS
         * ============================
         */
        $widget->start_controls_section(
            'section_renting',
            [
                'label' => __('What Will They Rent?', 'ski-ride-servetech'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $rent_repeater = new Repeater();
        $rent_repeater->add_control(
            'rent_option',
            [
                'label' => __('Rent Option', 'ski-ride-servetech'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $widget->add_control(
            'renting_options',
            [
                'label'       => __('Renting Options', 'ski-ride-servetech'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $rent_repeater->get_controls(),
                'title_field' => '{{{ rent_option }}}',
            ]
        );

        $widget->end_controls_section();

        /**
         * ============================
         * ABILITIES
         * ============================
         */
        $widget->start_controls_section(
            'section_ability',
            [
                'label' => __('Ability Level', 'ski-ride-servetech'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $ability_repeater = new Repeater();
        $ability_repeater->add_control(
            'ability_name',
            [
                'label' => __('Ability', 'ski-ride-servetech'),
                'type'  => Controls_Manager::TEXT,
            ]
        );

        $widget->add_control(
            'abilities',
            [
                'label'       => __('Abilities', 'ski-ride-servetech'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $ability_repeater->get_controls(),
                'title_field' => '{{{ ability_name }}}',
            ]
        );

        $widget->end_controls_section();

        /**
         * ============================
         * SKI PACKAGES
         * ============================
         */
        $widget->start_controls_section(
            'section_packages',
            [
                'label' => __('Ski Packages', 'ski-ride-servetech'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $widget->add_control(
            'package_section_title',
            [
                'label'   => __('Section Title', 'ski-ride-servetech'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Select a Ski Package', 'ski-ride-servetech'),
            ]
        );

        $package_repeater = new Repeater();
        $package_repeater->add_control('package_name', [
            'label' => __('Package Name', 'ski-ride-servetech'),
            'type'  => Controls_Manager::TEXT,
        ]);
        $package_repeater->add_control('package_price', [
            'label' => __('Price (per day)', 'ski-ride-servetech'),
            'type'  => Controls_Manager::NUMBER,
        ]);
        $package_repeater->add_control('package_desc', [
            'label' => __('Description', 'ski-ride-servetech'),
            'type'  => Controls_Manager::TEXTAREA,
        ]);

        $widget->add_control(
            'packages',
            [
                'label'       => __('Packages', 'ski-ride-servetech'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $package_repeater->get_controls(),
                'title_field' => '{{{ package_name }}} - ${{{ package_price }}}',
            ]
        );

        $widget->end_controls_section();

        /**
         * ============================
         * EXTRA GEAR
         * ============================
         */
        $widget->start_controls_section(
            'section_extra_gear',
            [
                'label' => __('Extra Gear (Gloves, Goggles, Socks)', 'ski-ride-servetech'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $gear_repeater = new Repeater();
        $gear_repeater->add_control('gear_name', [
            'label' => __('Gear Name', 'ski-ride-servetech'),
            'type'  => Controls_Manager::TEXT,
        ]);
        $gear_repeater->add_control('gear_price', [
            'label' => __('Price', 'ski-ride-servetech'),
            'type'  => Controls_Manager::NUMBER,
        ]);
        $gear_repeater->add_control('gear_desc', [
            'label' => __('Description', 'ski-ride-servetech'),
            'type'  => Controls_Manager::TEXTAREA,
        ]);

        $widget->add_control(
            'gears',
            [
                'label'       => __('Gear Packages', 'ski-ride-servetech'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $gear_repeater->get_controls(),
                'title_field' => '{{{ gear_name }}} - ${{{ gear_price }}}',
            ]
        );

        $widget->end_controls_section();

        /**
         * ============================
         * PASSES
         * ============================
         */
        $widget->start_controls_section(
            'section_passes',
            [
                'label' => __('Passes', 'ski-ride-servetech'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $pass_repeater = new Repeater();
        $pass_repeater->add_control('pass_title', [
            'label' => __('Pass Title', 'ski-ride-servetech'),
            'type'  => Controls_Manager::TEXT,
        ]);
        $pass_repeater->add_control('pass_price', [
            'label' => __('Pass Price', 'ski-ride-servetech'),
            'type'  => Controls_Manager::NUMBER,
        ]);

        $widget->add_control(
            'passes',
            [
                'label'       => __('Pass Options', 'ski-ride-servetech'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $pass_repeater->get_controls(),
                'title_field' => '{{{ pass_title }}} - ${{{ pass_price }}}',
            ]
        );

        $widget->end_controls_section();
    }
}
