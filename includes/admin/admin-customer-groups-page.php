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
        add_filter('manage_customer_groups_posts_columns', [$this, 'dev_manage_customer_groups_posts_columns']);
        add_action('manage_customer_groups_posts_custom_column', [$this, 'dev_manage_customer_groups_posts_custom_column'], 10, 2);

        // Status updation 
        add_action('add_meta_boxes', [$this, 'register_status_meta_box']);
        add_action('save_post_customer_groups', [$this, 'save_status_meta_box']);
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
        // 🔹 Booking info
        // $fitting_location = get_post_meta($post->ID, 'fitting_location', true);
        // $fitting_date = get_post_meta($post->ID, 'fitting_date', true);
        // $last_ski_date = get_post_meta($post->ID, 'last_ski_date', true);

        $payment_type = get_post_meta($post->ID, 'payment_type', true);
        // $rental_days = get_post_meta($post->ID, 'rental_days', true);
        $boots_discount = get_post_meta($post->ID, 'boots_discount', true);
        // 🔹 Members
        $members = get_post_meta($post->ID, 'group_members', true);

        // =====================
        // Members
        // =====================
 
        if (empty($members) || !is_array($members)) {
            echo '<p>' . esc_html__('No members added yet.', 'ski-ride-servetech') . '</p>';
        } else {
            echo '<table class="widefat striped">';
            echo '<thead><tr>
                <th>' . esc_html__('First Name', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Last Name', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Renting Option', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Ability', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Age', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Weight', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Height', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Location', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Start Date', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('End Date', 'ski-ride-servetech') . '</th>
            </tr></thead><tbody>';
            foreach ($members as $m) {
                echo '<tr>';
                echo '<td>' . esc_html($m['first_name'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['last_name'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['renting_option'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['ability'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['age'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['weight'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['height'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['fitting_location'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['fitting_date'] ?? '-') . '</td>';
                echo '<td>' . esc_html($m['last_ski_date'] ?? '-') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }

        // 🔹 Customer details
        $customer = [
            'first_name' => get_post_meta($post->ID, 'customer_first_name', true),
            'last_name' => get_post_meta($post->ID, 'customer_last_name', true),
            'email' => get_post_meta($post->ID, 'customer_email', true),
            'phone' => get_post_meta($post->ID, 'customer_phone', true),
            'delivery_address' => get_post_meta($post->ID, 'customer_delivery_address', true),
            'collection_address' => get_post_meta($post->ID, 'customer_collection_address', true),
            'hear_about' => get_post_meta($post->ID, 'customer_hear_about', true),
            'promo_code' => get_post_meta($post->ID, 'customer_promo_code', true),
            'comments' => get_post_meta($post->ID, 'customer_comments', true),
            'marketing_optin' => get_post_meta($post->ID, 'customer_marketing_optin', true),
        ];

        // 🔹 Products
        $products_json = get_post_meta($post->ID, 'products', true);
        $products = $products_json ? json_decode($products_json, true) : [];



        // =====================
        // Customer Details
        // =====================
        echo '<h4>' . esc_html__('Customer Details', 'ski-ride-servetech') . '</h4>';
        echo '<table class="widefat striped" style="margin-bottom:20px"><tbody>';
        foreach ($customer as $key => $val) {
            echo '<tr><th>' . esc_html(ucwords(str_replace('_', ' ', $key))) . '</th><td>' . esc_html($val ?: '-') . '</td></tr>';
        }
        echo '</tbody></table>';

       // =====================
        // Products per Member
        // =====================
        echo '<h4>' . esc_html__('Products', 'ski-ride-servetech') . '</h4>';

        if (!empty($products)) {
            $total = 0;

            echo '<table class="widefat striped" style="margin-bottom:20px">';
            echo '<thead><tr>
                <th>' . esc_html__('Product', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Quantity', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Days', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Unit Price', 'ski-ride-servetech') . '</th>
                <th>' . esc_html__('Line Total', 'ski-ride-servetech') . '</th>
            </tr></thead><tbody>';

            // Group non-rental products by name to combine
            $non_rental_group = [];

            foreach ($products as $p) {
                $is_rental = get_post_meta($p['product_id'], 'is_rental', true) === 'yes';
                $qty   = intval($p['quantity'] ?? 0);
                $price = floatval($p['price'] ?? 0);

                // Get member info for this product
                $member_name = $p['member_name'] ?? '-';
                $fitting_location = $p['fitting_location'] ?? '-';
                $start_date = $p['fitting_date'] ?? '-';
                $end_date = $p['last_ski_date'] ?? '-';

                // Days for rental products
                $days = $is_rental ? intval($p['rental_days'] ?? 1) : 1;

                // Line total
                $line_total = $qty * $days * $price;

                // Accumulate non-rental products to combine
                if (!$is_rental) {
                    $key = $p['name'] ?? 'Unknown';
                    if (!isset($non_rental_group[$key])) {
                        $non_rental_group[$key] = [
                            'name' => $key,
                            'qty' => 0,
                            'price' => $price,
                            'line_total' => 0,
                        ];
                    }
                    $non_rental_group[$key]['qty'] += $qty;
                    $non_rental_group[$key]['line_total'] += $line_total;
                    $total += $line_total;
                    continue;
                }

                // Output rental product row
                $total += $line_total;
                echo '<tr>';
                echo '<td>' . esc_html($p['name'] ?? '-') . '</td>';
                echo '<td>' . esc_html($qty) . '</td>';
                echo '<td>' . esc_html($days) . '</td>';
                echo '<td>' . wc_price($price) . '</td>';
                echo '<td>' . wc_price($line_total) . '</td>';
                echo '</tr>';
            }

            // Output combined non-rental products
            foreach ($non_rental_group as $nr) {
                echo '<tr>';
                echo '<td>' . esc_html($nr['name']) . '</td>';
                echo '<td>' . esc_html($nr['qty']) . '</td>';
                echo '<td>-</td>';
                echo '<td>' . wc_price($nr['price']) . '</td>';
                echo '<td>' . wc_price($nr['line_total']) . '</td>';
                echo '</tr>';
            }

            $final_total = $total - $boots_discount;

            // Subtotal row
            echo '<tr style="font-weight:bold;background:#f9f9f9">';
            echo '<td colspan="4" style="text-align:right;">' . esc_html__('Subtotal:', 'ski-ride-servetech') . '</td>';
            echo '<td>' . wc_price($total) . '</td>';
            echo '</tr>';

            // Discount row
            if ($boots_discount > 0) {
                echo '<tr style="background:#fdf7f7">';
                echo '<td colspan="4" style="text-align:right;color:#c00;">' . esc_html__('Boots Discount:', 'ski-ride-servetech') . '</td>';
                echo '<td style="color:#c00;">-' . wc_price($boots_discount) . '</td>';
                echo '</tr>';
            }

            // Final total row
            echo '<tr style="font-weight:bold;background:#eefbee">';
            echo '<td colspan="4" style="text-align:right;">' . esc_html__('Total After Discount:', 'ski-ride-servetech') . '</td>';
            echo '<td>' . wc_price($final_total) . '</td>';
            echo '</tr>';
            $payment_amount = ($payment_type === 'deposit') ? $final_total * 0.1 : $final_total;
            // Payment row
            if ( !empty($payment_type) && isset($payment_amount) ) {
                echo '<tr style="font-weight:bold;background:#dfe6f0">';
                echo '<td colspan="4" style="text-align:right;">' . esc_html__('Payment Type: ', 'ski-ride-servetech') . esc_html(ucfirst($payment_type)) . '</td>';
                echo '<td>' . wc_price($payment_amount) . '</td>';
                echo '</tr>';
            }


            echo '</tbody></table>';
        } else {
            echo '<p>' . esc_html__('No products found.', 'ski-ride-servetech') . '</p>';
        }   

        
        // =====================
        // Booking Details Table
        // =====================
        $status = get_post_meta($post->ID, 'booking_status', true) ?: 'person_added';
        echo '<h4>' . esc_html__('Booking Status', 'ski-ride-servetech') . '</h4>';
        echo '<table class="widefat striped" style="margin-bottom:20px"><tbody>';
        echo '<tr><th>' . esc_html__('Current Status', 'ski-ride-servetech') . '</th><td>' . esc_html(ucwords(str_replace('_', ' ', $status))) . '</td></tr>';
        echo '</tbody></table>';


    }

    public function dev_manage_customer_groups_posts_columns($columns)
    {
        $columns['booking_status'] = __('Status', 'ski-ride-servetech');
        return $columns;
    }

    public function dev_manage_customer_groups_posts_custom_column($column, $post_id)
    {
        if ($column === 'booking_status') {
            $status = get_post_meta($post_id, 'booking_status', true) ?: 'person_added';
            echo esc_html(ucwords(str_replace('_', ' ', $status)));
        }
    }


    // Status Updation 

    /**
     * ✅ Render booking status meta box
     */
    public function render_status_meta_box($post)
    {
        $status = get_post_meta($post->ID, 'booking_status', true) ?: 'person_added';
        ?>
        <label for="booking_status"><?php esc_html_e('Select Status:', 'ski-ride-servetech'); ?></label>
        <select name="booking_status" id="booking_status">
            <option value="person_added" <?php selected($status, 'person_added'); ?>>Person Added</option>
            <option value="pending_payment" <?php selected($status, 'pending_payment'); ?>>Payment Pending</option>
            <option value="paid" <?php selected($status, 'paid'); ?>>Paid</option>
        </select>
        <?php
    }


    /**
     * ✅ Register booking status meta box
     */

    public function register_status_meta_box()
    {
        add_meta_box(
            'booking_status_meta',
            __('Booking Status', 'ski-ride-servetech'),
            [$this, 'render_status_meta_box'],
            'customer_groups',
            'side'
        );
    }

    /**
     * ✅ Save booking status meta box
     */
    public function save_status_meta_box($post_id)
    {
        if (isset($_POST['booking_status'])) {
            update_post_meta($post_id, 'booking_status', sanitize_text_field($_POST['booking_status']));
        }
    }



}