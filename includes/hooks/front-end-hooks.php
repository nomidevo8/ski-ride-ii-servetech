<?php
namespace SkiRideServetech\Hooks;


// Style Controls 

class Frontend_Hooks
{
    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct()
    {
        add_action('wp_ajax_save_customer_group', [$this, 'save_customer_group']);
        add_action('wp_ajax_nopriv_save_customer_group', [$this, 'save_customer_group']);
        add_action('wp_ajax_check_user_group', [$this, 'check_user_group']);
        add_action("wp_ajax_ski_ride_get_member", [$this, "get_member"]);
        add_action("wp_ajax_ski_ride_delete_member", [$this, "delete_member"]);
    }


    public function save_customer_group()
    {
        if (
            !isset($_POST['customer_group_nonce']) ||
            !wp_verify_nonce($_POST['customer_group_nonce'], 'save_customer_group_nonce')
        ) {
            wp_send_json_error('Invalid request.');
        }

        if (!is_user_logged_in()) {
            wp_send_json_error('You must be logged in.');
        }

        $current_user_id = get_current_user_id();

        // Collect person details
        $person = [
            'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
            'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
            'renting_option' => sanitize_text_field($_POST['renting_option'] ?? ''),
            'ability' => sanitize_text_field($_POST['ability'] ?? ''),
            'age' => sanitize_text_field($_POST['age'] ?? ''),
            'weight' => sanitize_text_field($_POST['weight'] ?? ''),
            'height' => sanitize_text_field($_POST['height'] ?? ''),
        ];

        // Booking details (saved at group-level, not per person)
        $fitting_location = sanitize_text_field($_POST['fitting_location'] ?? '');
        $fitting_date = sanitize_text_field($_POST['fitting_date'] ?? '');
        $last_ski_date = sanitize_text_field($_POST['last_ski_date'] ?? '');

        // 🔹 Find existing group for this user
        $existing = get_posts([
            'post_type' => 'customer_groups',
            'post_status' => 'publish',
            'author' => $current_user_id,
            'numberposts' => 1,
        ]);

        if ($existing) {
            $post_id = $existing[0]->ID;
        } else {
            // Create a new group if none
            $post_id = wp_insert_post([
                'post_title' => 'Group of ' . wp_get_current_user()->display_name,
                'post_type' => 'customer_groups',
                'post_status' => 'publish',
                'post_author' => $current_user_id,
            ]);
        }

        if (is_wp_error($post_id)) {
            wp_send_json_error('Failed to create group.');
        }

        // Save booking meta at group-level
        update_post_meta($post_id, 'fitting_location', $fitting_location);
        update_post_meta($post_id, 'fitting_date', $fitting_date);
        update_post_meta($post_id, 'last_ski_date', $last_ski_date);

        // 🔹 Add new person to group_members
        $members = get_post_meta($post_id, 'group_members', true);
        if (!is_array($members)) {
            $members = [];
        }
        $members[] = $person;
        update_post_meta($post_id, 'group_members', $members);

        wp_send_json_success([
            'post_id' => $post_id,
            'members' => $members
        ]);
    }


    public function check_user_group()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(['has_group' => false]);
        }

        $current_user_id = get_current_user_id();
        $existing = get_posts([
            'post_type' => 'customer_groups',
            'post_status' => 'publish',
            'author' => $current_user_id,
            'numberposts' => 1,
        ]);

        if ($existing) {
            $group_id = $existing[0]->ID;
            $members = get_post_meta($group_id, 'group_members', true);

            // group-level booking details
            $fitting_location = get_post_meta($group_id, 'fitting_location', true);
            $fitting_date = get_post_meta($group_id, 'fitting_date', true);
            $last_ski_date = get_post_meta($group_id, 'last_ski_date', true);

            ob_start();
            if ($members) {
                echo '<ul class="list-group">';
                foreach ($members as $index => $m) {
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center member-item" data-index="' . $index . '">';
                    echo esc_html($m['first_name'] . ' ' . $m['last_name']);
                    echo '<div>
                    <button class="btn btn-sm btn-outline-info edit-member" data-index="' . $index . '">Edit</button>
                    <button class="btn btn-sm btn-outline-danger delete-member" data-index="' . $index . '">Delete</button>
                </div>';
                    echo '</li>';
                }
                echo '</ul>';
            }

            $html = ob_get_clean();

            wp_send_json_success([
                'has_group' => true,
                'html' => $html,
                'fitting_location' => $fitting_location,
                'fitting_date' => $fitting_date,
                'last_ski_date' => $last_ski_date,
                'members' => $members,
            ]);
        }

        wp_send_json_success(['has_group' => false]);
    }



    public function get_member()
    {
        $index = intval($_POST["member_index"]);
        $current_user_id = get_current_user_id();

        $existing = get_posts([
            'post_type' => 'customer_groups',
            'post_status' => 'publish',
            'author' => $current_user_id,
            'numberposts' => 1,
        ]);

        if ($existing) {
            $group_id = $existing[0]->ID;
            $members = get_post_meta($group_id, 'group_members', true);

            if (isset($members[$index])) {
                wp_send_json_success($members[$index]);
            }
        }

        wp_send_json_error();
    }

    public function delete_member()
    {
        $index = intval($_POST["member_index"]);
        $current_user_id = get_current_user_id();

        $existing = get_posts([
            'post_type' => 'customer_groups',
            'post_status' => 'publish',
            'author' => $current_user_id,
            'numberposts' => 1,
        ]);

        if ($existing) {
            $group_id = $existing[0]->ID;
            $members = get_post_meta($group_id, 'group_members', true);

            if (isset($members[$index])) {
                unset($members[$index]);
                $members = array_values($members); // reindex
                update_post_meta($group_id, 'group_members', $members);

                wp_send_json_success();
            }
        }

        wp_send_json_error();
    }
}