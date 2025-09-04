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
        add_action('wp_ajax_nopriv_check_user_group', [$this, 'check_user_group']);
        add_action('wp_ajax_check_user_group', [$this, 'check_user_group']);
        add_action("wp_ajax_ski_ride_get_member", [$this, "get_member"]);
        add_action("wp_ajax_ski_ride_delete_member", [$this, "delete_member"]);

        // Create User 

        // Log in & Register
        add_action('wp_ajax_nopriv_popup_user_login', [$this, 'dev_popup_user_login']);
        add_action('wp_ajax_popup_user_login', [$this, 'dev_popup_user_login']);
        add_action('wp_ajax_nopriv_popup_user_register', [$this, 'dev_popup_user_register']);
        add_action('wp_ajax_popup_user_register', [$this, 'dev_popup_user_register']);
        add_action('wp_ajax_nopriv_popup_user_forgot_password', [$this, 'dev_popup_user_forgot_password']);
        add_action('wp_ajax_popup_user_forgot_password', [$this, 'dev_popup_user_forgot_password']);
    }


    public function save_customer_group() {
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
            'fitting_location' => sanitize_text_field($_POST['fitting_location'] ?? ''),
            'fitting_date' => sanitize_text_field($_POST['fitting_date'] ?? ''),
            'last_ski_date' => sanitize_text_field($_POST['last_ski_date'] ?? ''),
        ];

        // Booking details (saved at group-level, not per person)
        // $fitting_location = sanitize_text_field($_POST['fitting_location'] ?? '');
        // $fitting_date = sanitize_text_field($_POST['fitting_date'] ?? '');
        // $last_ski_date = sanitize_text_field($_POST['last_ski_date'] ?? '');

        // Find existing group
        $existing = get_posts([
            'post_type' => 'customer_groups',
            'post_status' => 'publish',
            'author' => $current_user_id,
            'numberposts' => 1,
        ]);

        if ($existing) {
            $post_id = $existing[0]->ID;
        } else {
            // Create new group if none
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

        // Save booking meta
        // update_post_meta($post_id, 'fitting_location', $fitting_location);
        // update_post_meta($post_id, 'fitting_date', $fitting_date);
        // update_post_meta($post_id, 'last_ski_date', $last_ski_date);

        // Get existing members
        $members = get_post_meta($post_id, 'group_members', true);
        if (!is_array($members)) {
            $members = [];
        }

        // Check if editing an existing member
        $member_index = isset($_POST['hidden-member-index']) && $_POST['hidden-member-index'] != "" ? intval($_POST['hidden-member-index']) : null;
        if ($member_index !== null && isset($members[$member_index])) {
            // Update existing member
            $members[$member_index] = $person;
            $message = 'Person details updated successfully.';
        } else {
            // Add new member
            $members[] = $person;
            $message = 'Person added to your group successfully.';
        }

        update_post_meta($post_id, 'group_members', $members);

        wp_send_json_success([
            'post_id' => $post_id,
            'members' => $members,
            'message' => $message
        ]);
    }



    public function check_user_group()
    {
        if (!is_user_logged_in()) {
            wp_send_json_error(['reason' => 'not_logged_in']);
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
            // $fitting_location = get_post_meta($group_id, 'fitting_location', true);
            // $fitting_date = get_post_meta($group_id, 'fitting_date', true);
            // $last_ski_date = get_post_meta($group_id, 'last_ski_date', true);

            ob_start();
            if ($members) {
                echo '<ul class="list-group w-100">';
                foreach ($members as $index => $m) {
                    echo '<li class="list-group-item member-item d-flex align-items-center" data-index="' . $index . '">';
                    
                    // Edit button (left)
                    echo '<button class="btn btn-sm edit-member me-3 d-flex align-content-center gap-1" data-index="' . $index . '">
                            <span><i class="fas fa-edit"></i></span> <span>Edit</span>
                        </button>';
                    
                    // Member name (centered flex-grow)
                    echo '<span class="flex-grow-1 text-center fw-bold">' . esc_html($m['first_name'] . ' ' . $m['last_name']) . '</span>';
                    
                    // Delete button (right)
                    echo '<button class="btn btn-sm delete-member ms-3 d-flex align-content-center gap-1" data-index="' . $index . '">
                            <span><i class="fas fa-trash"></i></span> <span>Remove</span>
                        </button>';
                    
                    echo '</li>';
                }
                echo '</ul>';
            }
            $html = ob_get_clean();


            wp_send_json_success([
                'has_group' => true,
                'html' => $html,
                // 'fitting_location' => $fitting_location,
                // 'fitting_date' => $fitting_date,
                // 'last_ski_date' => $last_ski_date,
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



    // Login
    public function dev_popup_user_login() {
        $email    = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);

        $user = wp_authenticate($email, $password);
        if (is_wp_error($user)) {
            wp_send_json_error(['message' => 'Invalid credentials']);
        }

        wp_set_auth_cookie($user->ID, true);
        wp_send_json_success(['message' => 'Logged in']);
    }

    // Register
    public function dev_popup_user_register() {
        $email    = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $name     = sanitize_text_field($_POST['name']);
        $phone    = sanitize_text_field($_POST['phone']);

        if (email_exists($email)) {
            wp_send_json_error(['message' => 'Email already registered. Please login.']);
        }

        $username = sanitize_user(current(explode('@', $email)));
        $user_id  = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_send_json_error(['message' => 'Registration failed']);
        }

        // Save meta
        update_user_meta($user_id, 'phone', $phone);
        update_user_meta($user_id, 'full_name', $name);

        wp_set_auth_cookie($user_id, true);
        wp_send_json_success(['message' => 'Registered & logged in']);
    }


    public function dev_popup_user_forgot_password() {
        $email = sanitize_email($_POST['email']);

        if (!email_exists($email)) {
            wp_send_json_error(['message' => 'No account found with that email']);
        }

        $user = get_user_by('email', $email);

        // Generate password reset key
        $reset_key = get_password_reset_key($user);

        if (is_wp_error($reset_key)) {
            wp_send_json_error(['message' => 'Could not generate reset link']);
        }

        // Build reset link (uses WordPress built-in reset page)
        $reset_link = wp_lostpassword_url() . "?key=$reset_key&login=" . rawurlencode($user->user_login);

        // Send email
        $subject = "Password Reset Request";
        $message = "Hi, \n\nClick the following link to reset your password:\n\n" . $reset_link;
        wp_mail($email, $subject, $message);

        wp_send_json_success(['message' => 'Reset email sent']);
    }

}