<?php
namespace SkiRideServetech\Wocommerce;

if (!defined('ABSPATH'))
    exit;

class Wocommerce_Init
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
        // Wocommerce Init
        add_filter('woocommerce_add_cart_item_data', [$this, 'dev_woocommerce_add_cart_item_data'], 10, 3);
        add_action('woocommerce_before_calculate_totals', [$this, 'dev_woocommerce_before_calculate_totals'], 10, 3);
        add_filter('woocommerce_get_item_data', [$this, 'dev_woocommerce_get_item_data'], 10, 2);
        add_filter('woocommerce_cart_calculate_fees', [$this, 'dev_woocommerce_cart_calculate_fees'], 10, 2);

        // Hooks For Woocoomerce handling 
        add_action('wp_ajax_srs_add_rental_to_cart', [$this, 'srs_add_rental_to_cart']);
        add_action('wp_ajax_nopriv_srs_add_rental_to_cart', [$this, 'srs_add_rental_to_cart']);
    }

    public function dev_woocommerce_add_cart_item_data($cart_item_data, $product_id, $variation_id)
    {
        if (get_post_meta($product_id, 'is_rental', true) === 'yes') {
            $rental_days = $cart_item_data['rental_days'] ?? intval($_POST['rental_days'] ?? 1);
            $cart_item_data['rental_days'] = $rental_days;
            // create unique key so multiple rentals can exist
            $cart_item_data['unique_key'] = md5(microtime() . rand());
        }
        return $cart_item_data;
    }

    public function dev_woocommerce_before_calculate_totals($cart)
    {
        foreach ($cart->get_cart() as &$cart_item) { 
            if (isset($cart_item['rental_days'])) {
                $days = floatval($cart_item['rental_days']);
                $original_price = $cart_item['data']->get_regular_price();
                $cart_item['data']->set_price($original_price * $days);
            }
        }
    }

    public function dev_woocommerce_get_item_data($item_data, $cart_item)
    {
        if (isset($cart_item['rental_days'])) {
            $item_data[] = [
                'name' => 'Rental Days',
                'value' => $cart_item['rental_days']
            ];
        }
        return $item_data;
    }

    public function dev_woocommerce_cart_calculate_fees($cart)
    {
        if (is_admin() && !defined('DOING_AJAX')) return;

        // Apply boots discount
        $discount = floatval(WC()->session->get('boots_discount'));
        if ($discount > 0) {
            $cart->add_fee('Boots Discount', -$discount);
        }

        // Check payment type
        $payment_type = WC()->session->get('payment_type');
        if ($payment_type === 'deposit') {
            $total = $cart->get_subtotal() - $discount;
            $deposit_amount = $total * 0.10;

            // Reduce cart total to deposit amount using a negative fee
            $remaining = $total - $deposit_amount;
            $cart->add_fee('10% Deposit (Remaining 90% due on arrival)', -$remaining);
        }
    }

    public function srs_add_rental_to_cart() {
        if (!isset($_POST['booking_data'])) {
            wp_send_json_error(['message' => 'No booking data received']);
        }

        $booking = $_POST['booking_data'];
        $rental_days = $booking['rental_days'];

        if (!class_exists('WC_Cart')) {
            wp_send_json_error(['message' => 'WooCommerce not active']);
        }

        // Empty current cart before adding new items
        WC()->cart->empty_cart();

        // Loop through products
        if (!empty($booking['products'])) {
            foreach ($booking['products'] as $item) {
                $product_id = intval($item['product_id']);
                $quantity   = intval($item['quantity']) ?: 1;

                $is_rental = get_post_meta($product_id, 'is_rental', true) === 'yes';
                
                // Only add rental_days if product is rental
                $cart_item_data = [];
                if ($is_rental) {
                   $cart_item_data = ['rental_days' => $rental_days];
                }

                // Add to cart
                WC()->cart->add_to_cart(
                    $product_id,
                    $quantity,
                    0,
                    [],
                    $cart_item_data
                );

                // Store rental meta for display only
                if ($is_rental) {
                    foreach (WC()->cart->get_cart() as $key => $cart_item) {
                        if ($cart_item['product_id'] == $product_id && isset($cart_item_data['rental_days'])) {
                            WC()->cart->cart_contents[$key]['rental_data'] = [
                                'type' => $item['name'],
                                'days' => $rental_days,
                            ];
                        }
                    }
                }
            }
        }

        // Apply boots discount if available
        if (!empty($booking['boots_discount'])) {
            WC()->session->set('boots_discount', floatval($booking['boots_discount']));
        }

        // Payment Type 
        if (!empty($booking['payment_type'])) {
            WC()->session->set('payment_type', sanitize_text_field($booking['payment_type']));
        }

        // Store customer data in session to prefill checkout
        if (!empty($booking['customer']) && is_array($booking['customer'])) {
            foreach ($booking['customer'] as $key => $value) {
                WC()->session->set($key, sanitize_text_field($value));
            }
        }

        // Redirect to checkout
        wp_send_json_success(['redirect' => wc_get_checkout_url()]);
    }

}