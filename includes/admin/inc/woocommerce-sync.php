<?php
if (!defined('ABSPATH')) exit;

class SRS_Sync_WooCommerce {

    /**
     * Sync gear or insurance item with WooCommerce
     * 
     * @param array $item ['name','prices','product_id', ...]
     * @param string $type 'gear' or 'insurance'
     * @return int Product ID
     */
    public static function sync_product($item, $type = 'gear') {
        if (!class_exists('WC_Product')) return 0;

        $product_name = $item['name'] ?? '';
        $is_rental    = !empty($item['is_rental']);
        $rental_prices = is_array($item['prices'] ?? null) ? $item['prices'] : [];
        $base_price   = $is_rental ? ($rental_prices[1] ?? 0) : floatval($item['base_price'] ?? 0);
        $product_id   = intval($item['product_id'] ?? 0);

        if (empty($product_name)) return 0;

        $meta_key_prices = '_rental_prices';
        $meta_key_flag   = 'is_rental';

        $product = $product_id ? wc_get_product($product_id) : false;

        if ($product) {
            $product->set_name($product_name);
            $product->set_regular_price($base_price);
            $product->set_catalog_visibility('hidden');
            if ($is_rental) {
                update_post_meta($product->get_id(), $meta_key_flag, 'yes');
                update_post_meta($product->get_id(), $meta_key_prices, $rental_prices);
            } else {
                update_post_meta($product->get_id(), $meta_key_flag, 'no');
                delete_post_meta($product->get_id(), $meta_key_prices);
            }
            $product->save();
            return $product->get_id();
        } else {
            $new_product = new WC_Product_Simple();
            $new_product->set_name($product_name);
            $new_product->set_regular_price($base_price);
            $new_product->set_catalog_visibility('hidden');
            $new_product->save();

            $new_product_id = $new_product->get_id();
            if ($is_rental) {
                update_post_meta($new_product_id, $meta_key_flag, 'yes');
                update_post_meta($new_product_id, $meta_key_prices, $rental_prices);
            } else {
                update_post_meta($new_product_id, $meta_key_flag, 'no');
            }
            return $new_product_id;
        }
    }
}
