<?php
if (!defined('ABSPATH')) exit;
require_once plugin_dir_path(__FILE__) . 'sanitize-functions.php';
require_once plugin_dir_path(__FILE__) . 'woocommerce-sync.php';

class SRS_Goggles {

    private $option_key = 'srs_goggles';

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_settings() {
        register_setting('srs_goggles_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    public function sanitize_settings($input) {
    // Use the universal sanitizer for gears
        $output = SRS_Sanitize_Settings::sanitize($input, 'goggles');

        // Sync products to WooCommerce automatically
        if (!empty($output['goggles'])) {
            foreach ($output['goggles'] as &$item) {
                $item['product_id'] = SRS_Sync_WooCommerce::sync_product($item, 'goggles');
            }
        }

        return $output;
    }

    
    // public function sanitize_settings($input) {
    //     $output = ['gears' => []];

    //     if (!empty($input['gears']) && is_array($input['gears'])) {
    //         foreach ($input['gears'] as $gear) {
    //             $name = sanitize_text_field($gear['name'] ?? '');
    //             if (empty($name)) continue;

    //             $desc = isset($gear['desc']) ? sanitize_textarea_field($gear['desc']) : '';

    //             $prices = [];
    //             if (!empty($gear['prices']['day']) && is_array($gear['prices']['day'])) {
    //                 foreach ($gear['prices']['day'] as $i => $day) {
    //                     $day = intval($day);
    //                     $val = isset($gear['prices']['value'][$i]) ? floatval($gear['prices']['value'][$i]) : 0;
    //                     if ($day > 0 || $val > 0) {
    //                         $prices[$day] = $val;
    //                     }
    //                 }
    //             }

    //             if (!empty($gear['prices']['extra'])) {
    //                 $prices['extra'] = floatval($gear['prices']['extra']);
    //             }

    //             $renting_options = [];
    //             if (!empty($gear['renting_options']) && is_array($gear['renting_options'])) {
    //                 foreach ($gear['renting_options'] as $opt) {
    //                     $renting_options[] = sanitize_text_field($opt);
    //                 }
    //             }

    //             $item = [
    //                 'name'            => $name,
    //                 'desc'            => $desc,
    //                 'prices'          => $prices,
    //                 'renting_options' => $renting_options,
    //                 'product_id'      => intval($gear['product_id'] ?? 0),
    //             ];

    //             $item['product_id'] = $this->sync_woocommerce_product($item);

    //             $output['gears'][] = $item;
    //         }
    //     }

    //     return $output;
    // }

    // private function sync_woocommerce_product($item) {
    //     if (!class_exists('WC_Product')) return 0;

    //     $product_name = $item['name'] ?? '';
    //     $base_price   = $item['prices'][1] ?? 0;
    //     $product_id   = intval($item['product_id'] ?? 0);

    //     if (empty($product_name)) return 0;

    //     $product = $product_id ? wc_get_product($product_id) : false;

    //     if ($product) {
    //         $product->set_name($product_name);
    //         $product->set_regular_price($base_price);
    //         $product->set_catalog_visibility('hidden');
    //         update_post_meta($product->get_id(), 'is_rental', 'yes');
    //         update_post_meta($product->get_id(), '_rental_prices', $item['prices']);
    //         $product->save();
    //         return $product->get_id();
    //     } else {
    //         $new_product = new WC_Product_Simple();
    //         $new_product->set_name($product_name);
    //         $new_product->set_regular_price($base_price);
    //         $new_product->set_catalog_visibility('hidden');
    //         $new_product->save();
    //         $new_product_id = $new_product->get_id();
    //         update_post_meta($new_product_id, 'is_rental', 'yes');
    //         update_post_meta($new_product_id, '_rental_prices', $item['prices']);
    //         return $new_product_id;
    //     }
    // }

    public function render_page() {
        $options = get_option($this->option_key, []);
        $gears = $options['gears'] ?? [];
        $renting_options = get_option('srs_renting_options', []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1 class="wp-heading-inline"><?php _e('Goggles', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_goggles_group'); ?>

                <div class="container p-4 mt-3">
                    <table class="table table-bordered table-striped" id="goggles-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?php _e('Goggle Name', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Description', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Day-wise Prices', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Extra Day Price', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Renting Options', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gears)): ?>
                                <?php foreach ($gears as $gear_index => $gear): ?>
                                    <tr class="gear-row" data-gear-index="<?php echo $gear_index; ?>">
                                        <td>
                                            <input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gears][<?php echo $gear_index; ?>][name]"
                                                value="<?php echo esc_attr($gear['name'] ?? ''); ?>">
                                            <input type="hidden"
                                                name="<?php echo $this->option_key; ?>[gears][<?php echo $gear_index; ?>][product_id]"
                                                value="<?php echo esc_attr($gear['product_id'] ?? 0); ?>">
                                        </td>
                                        <td>
                                            <textarea class="form-control" rows="2"
                                                name="<?php echo $this->option_key; ?>[gears][<?php echo $gear_index; ?>][desc]"><?php echo esc_textarea($gear['desc'] ?? ''); ?></textarea>
                                        </td>
                                        <td>
                                            <table class="table table-sm table-bordered day-prices-table">
                                                <thead>
                                                    <tr>
                                                        <th><?php _e('Day', 'ski-ride-servetech'); ?></th>
                                                        <th><?php _e('Price', 'ski-ride-servetech'); ?></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($gear['prices'])): ?>
                                                        <?php foreach ($gear['prices'] as $day => $price): ?>
                                                            <?php if ($day === 'extra') continue; ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="number" min="1" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[gears][<?php echo $gear_index; ?>][prices][day][]"
                                                                        value="<?php echo esc_attr($day); ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[gears][<?php echo $gear_index; ?>][prices][value][]"
                                                                        value="<?php echo esc_attr($price); ?>">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-day">×</button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-sm btn-primary add-day"><?php _e('Add Day', 'ski-ride-servetech'); ?></button>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gears][<?php echo $gear_index; ?>][prices][extra]"
                                                value="<?php echo esc_attr($gear['prices']['extra'] ?? ''); ?>">
                                        </td>
                                        <td>
                                            <?php if (!empty($renting_options)): 
                                                $selected = $gear['renting_options'] ?? [];
                                                ?>
                                                <select class="form-select" name="<?php echo $this->option_key; ?>[gears][<?php echo $gear_index; ?>][renting_options][]" multiple>
                                                    <?php foreach ($renting_options as $option): ?>
                                                        <option value="<?php echo esc_attr($option); ?>" <?php echo in_array($option, $selected) ? 'selected' : ''; ?>>
                                                            <?php echo esc_html($option); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <div class="alert alert-warning p-2">
                                                    <?php _e('First add the renting options.', 'ski-ride-servetech'); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"><?php _e('Remove Goggle', 'ski-ride-servetech'); ?></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-success add-row"><?php _e('Add Goggle', 'ski-ride-servetech'); ?></button>
                </div>

                <div class="mt-3">
                    <?php submit_button(__('Save Goggles', 'ski-ride-servetech')); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'ski-ride-content_page_dev-ski-goggles') return;

        $renting_options = get_option('srs_renting_options', []);

        wp_enqueue_script(
            'srs-goggles-js',
            plugin_dir_url(__DIR__) . '../../assets/js/goggles.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('srs-goggles-js', 'srsGoggles', [
            'rentingOptions' => $renting_options,
            'noOptionsMsg'   => __('First add the renting options.', 'ski-ride-servetech'),
        ]);
    }
}
