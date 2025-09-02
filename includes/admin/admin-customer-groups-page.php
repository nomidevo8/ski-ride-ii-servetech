<?php
namespace SkiRideAdminGroupsServetech;

if (!defined('ABSPATH'))
    exit;

class Admin_groups_page
{
    private static $_instance = null;
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */

    private function __construct()
    {
        add_action('init', [$this, 'register_customer_groups_post_type']);
        add_action('add_meta_boxes', [$this, 'register_meta_box']);
    }

    /**
     * Register custom post type for Customer Groups
     */

    public function register_customer_groups_post_type()
    {
        $labels = [
            'name' => __('Customer Groups', 'ski-ride-servetech'),
            'singular_name' => __('Customer Group', 'ski-ride-servetech'),
            'menu_name' => __('Customer Groups', 'ski-ride-servetech'),
            'name_admin_bar' => __('Customer Group', 'ski-ride-servetech'),
            'add_new' => __('Add New', 'ski-ride-servetech'),
            'add_new_item' => __('Add New Group', 'ski-ride-servetech'),
            'new_item' => __('New Group', 'ski-ride-servetech'),
            'edit_item' => __('Edit Group', 'ski-ride-servetech'),
            'view_item' => __('View Group', 'ski-ride-servetech'),
            'all_items' => __('All Groups', 'ski-ride-servetech'),
            'search_items' => __('Search Groups', 'ski-ride-servetech'),
            'not_found' => __('No groups found.', 'ski-ride-servetech'),
            'not_found_in_trash' => __('No groups found in Trash.', 'ski-ride-servetech')
        ];

        $args = [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'ski-group'], 
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-groups',
            'supports' => ['title', 'custom-fields'],
        ];

        // 👇 new post type key
        register_post_type('customer_groups', $args);
    }

    /**
     * Register meta box on the Group CPT edit screen
     */
    public function register_meta_box()
    {
        add_meta_box(
            'group_members_meta',
            __('Group Members', 'ski-ride-servetech'),
            [$this, 'render_meta_box'],
            'customer_groups',
            'normal',
            'default'
        );
    }

    /**
     * Render the meta box content
     */

    public function render_meta_box($post)
    {
        // 🔹 Fetch booking info
        $fitting_location = get_post_meta($post->ID, 'fitting_location', true);
        $fitting_date = get_post_meta($post->ID, 'fitting_date', true);
        $last_ski_date = get_post_meta($post->ID, 'last_ski_date', true);

        echo '<h4>' . esc_html__('Booking Details', 'ski-ride-servetech') . '</h4>';
        echo '<table class="widefat striped" style="margin-bottom:20px">';
        echo '<tbody>';
        echo '<tr><th>' . esc_html__('Fitting Location', 'ski-ride-servetech') . '</th><td>' . esc_html($fitting_location ?: '-') . '</td></tr>';
        echo '<tr><th>' . esc_html__('Fitting Date', 'ski-ride-servetech') . '</th><td>' . esc_html($fitting_date ?: '-') . '</td></tr>';
        echo '<tr><th>' . esc_html__('Last Ski Date', 'ski-ride-servetech') . '</th><td>' . esc_html($last_ski_date ?: '-') . '</td></tr>';
        echo '</tbody></table>';

        // 🔹 Fetch members
        $members = get_post_meta($post->ID, 'group_members', true);

        if (empty($members) || !is_array($members)) {
            echo '<p>' . esc_html__('No members added yet.', 'ski-ride-servetech') . '</p>';
            return;
        }

        echo '<h4>' . esc_html__('Group Members', 'ski-ride-servetech') . '</h4>';
        echo '<table class="widefat striped">';
        echo '<thead><tr>
                <th>' . esc_html__('First Name', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Last Name', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Renting Option', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Ability', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Age', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Weight', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Height', 'ski-ride-servetech') . '</th>
        </tr></thead><tbody>';

        foreach ($members as $member) {
            echo '<tr>';
            echo '<td>' . esc_html($member['first_name'] ?? '') . '</td>';
            echo '<td>' . esc_html($member['last_name'] ?? '') . '</td>';
            echo '<td>' . esc_html($member['renting_option'] ?? '') . '</td>';
            echo '<td>' . esc_html($member['ability'] ?? '') . '</td>';
            echo '<td>' . esc_html($member['age'] ?? '') . '</td>';
            echo '<td>' . esc_html($member['weight'] ?? '') . '</td>';
            echo '<td>' . esc_html($member['height'] ?? '') . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }


}