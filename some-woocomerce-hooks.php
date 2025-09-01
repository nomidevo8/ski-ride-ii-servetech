<?php

add_filter('woocommerce_add_cart_item_data', function($cart_item_data, $product_id, $variation_id){
    if(get_post_meta($product_id, 'is_rental', true) === 'yes'){
        $rental_days = intval($_POST['rental_days'] ?? 1);
        $cart_item_data['rental_days'] = $rental_days;
        // create unique key so multiple rentals can exist
        $cart_item_data['unique_key'] = md5(microtime().rand());
    }
    return $cart_item_data;
}, 10, 3);


add_action('woocommerce_before_calculate_totals', function($cart){
    if(is_admin() && !defined('DOING_AJAX')) return;

    foreach($cart->get_cart() as $cart_item){
        if(isset($cart_item['rental_days'])){
            $original_price = $cart_item['data']->get_regular_price();
            $cart_item['data']->set_price($original_price * $cart_item['rental_days']);
        }
    }
});


add_filter('woocommerce_get_item_data', function($item_data, $cart_item){
    if(isset($cart_item['rental_days'])){
        $item_data[] = [
            'name' => 'Rental Days',
            'value' => $cart_item['rental_days']
        ];
    }
    return $item_data;
}, 10, 2);
