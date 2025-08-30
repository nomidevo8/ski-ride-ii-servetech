<?php
namespace SkiRideServetech\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

// A helper class for reusing controls
class Form_Style_Controls {
    public static function register($widget) {
 // Style Section
        $widget->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'ski-ride-servetech'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $widget->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-title' => 'color: {{VALUE}};',
                ],
            ]
        );


        /**
         * Form Container
         */
        $widget->add_control(
            'form_background',
            [
                'label' => __('Form Background', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_responsive_control(
            'form_padding',
            [
                'label' => __('Form Padding', 'ski-ride-servetech'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .srs-form-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $widget->add_responsive_control(
            'form_margin',
            [
                'label' => __('Form Margin', 'ski-ride-servetech'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .srs-form-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $widget->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'selector' => '{{WRAPPER}} .srs-form-wrapper',
            ]
        );

        $widget->add_control(
            'form_border_radius',
            [
                'label' => __('Border Radius', 'ski-ride-servetech'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .srs-form-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $widget->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'form_box_shadow',
                'selector' => '{{WRAPPER}} .srs-form-wrapper',
            ]
        );

        /**
         * Title
         */
        $widget->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .srs-form-title',
            ]
        );

        /**
         * Labels
         */
        $widget->add_control(
            'label_color',
            [
                'label' => __('Label Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .srs-form-label',
            ]
        );

        /**
         * Inputs
         */
        $widget->add_control(
            'input_background',
            [
                'label' => __('Input Background', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-input' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_control(
            'input_text_color',
            [
                'label' => __('Input Text Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-input' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'input_typography',
                'selector' => '{{WRAPPER}} .srs-form-input',
            ]
        );

        $widget->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'input_border',
                'selector' => '{{WRAPPER}} .srs-form-input',
            ]
        );

        $widget->add_responsive_control(
            'input_padding',
            [
                'label' => __('Input Padding', 'ski-ride-servetech'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .srs-form-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        /**
         * Buttons
         */
        $widget->add_control(
            'button_heading',
            [
                'label' => __('Button', 'ski-ride-servetech'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $widget->start_controls_tabs('tabs_button_style');

        $widget->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'ski-ride-servetech'),
            ]
        );

        $widget->add_control(
            'button_bg_color',
            [
                'label' => __('Background Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->end_controls_tab();

        $widget->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'ski-ride-servetech'),
            ]
        );

        $widget->add_control(
            'button_hover_bg_color',
            [
                'label' => __('Hover Background Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->add_control(
            'button_hover_text_color',
            [
                'label' => __('Hover Text Color', 'ski-ride-servetech'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .srs-form-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->end_controls_tab();

        $widget->end_controls_tabs();

        $widget->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .srs-form-button',
            ]
        );

        $widget->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'selector' => '{{WRAPPER}} .srs-form-button',
            ]
        );

        $widget->add_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'ski-ride-servetech'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .srs-form-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $widget->end_controls_section();
    }
}