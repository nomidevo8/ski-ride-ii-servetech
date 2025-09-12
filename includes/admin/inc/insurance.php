<?php
if (!defined('ABSPATH')) exit;

class SRS_Insurance {

    private $option_key = 'srs_insurance';
    private $type_options = ['Child', 'Adult'];

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_settings() {
        register_setting('srs_insurance_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    public function sanitize_settings($input) {
    // Use the universal sanitizer for gears
        $output = SRS_Sanitize_Settings::sanitize($input, 'insurances');

        // Sync products to WooCommerce automatically
        if (!empty($output['insurances'])) {
            foreach ($output['insurances'] as &$item) {
                $item['product_id'] = SRS_Sync_WooCommerce::sync_product($item, 'insurances');
            }
        }

        return $output;
    }

    private function sync_woocommerce_product($item) {
        if (!class_exists('WC_Product')) return 0;

        $product_name = $item['name'] ?? '';
        $base_price   = $item['prices'][1] ?? 0;
        $product_id   = intval($item['product_id'] ?? 0);

        if (empty($product_name)) return 0;

        $product = $product_id ? wc_get_product($product_id) : false;

        if ($product) {
            $product->set_name($product_name);
            $product->set_regular_price($base_price);
            $product->set_catalog_visibility('hidden');
            update_post_meta($product->get_id(), 'is_insurance', 'yes');
            update_post_meta($product->get_id(), '_insurance_prices', $item['prices']);
            $product->save();
            return $product->get_id();
        } else {
            $new_product = new WC_Product_Simple();
            $new_product->set_name($product_name);
            $new_product->set_regular_price($base_price);
            $new_product->set_catalog_visibility('hidden');
            $new_product->save();
            $new_product_id = $new_product->get_id();
            update_post_meta($new_product_id, 'is_insurance', 'yes');
            update_post_meta($new_product_id, '_insurance_prices', $item['prices']);
            return $new_product_id;
        }
    }

    public function render_page() {
        $options = get_option($this->option_key, []);
        $insurances = $options['insurances'] ?? [];
        echo "<pre>";
        print_r($options);
        echo "</pre>";
        $renting_options = get_option('srs_renting_options', []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1 class="wp-heading-inline"><?php _e('Insurance', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_insurance_group'); ?>

                <div class="container p-4 mt-3">
                    <table class="table table-bordered table-striped" id="insurance-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?php _e('Insurance Name', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Day-wise Prices', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Extra Day Price', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Renting Options', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Type', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($insurances)): ?>
                                <?php foreach ($insurances as $index => $insurance): ?>
                                    <tr class="insurance-row" data-insurance-index="<?php echo $index; ?>">
                                        <td>
                                            <input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[insurances][<?php echo $index; ?>][name]"
                                                value="<?php echo esc_attr($insurance['name'] ?? ''); ?>">
                                            <input type="hidden"
                                                name="<?php echo $this->option_key; ?>[insurances][<?php echo $index; ?>][product_id]"
                                                value="<?php echo esc_attr($insurance['product_id'] ?? 0); ?>">
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
                                                    <?php if (!empty($insurance['prices'])): ?>
                                                        <?php foreach ($insurance['prices'] as $day => $price): ?>
                                                            <?php if ($day === 'extra') continue; ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="number" min="1" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[insurances][<?php echo $index; ?>][prices][day][]"
                                                                        value="<?php echo esc_attr($day); ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[insurances][<?php echo $index; ?>][prices][value][]"
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
                                                name="<?php echo $this->option_key; ?>[insurances][<?php echo $index; ?>][prices][extra]"
                                                value="<?php echo esc_attr($insurance['prices']['extra'] ?? ''); ?>">
                                        </td>
                                        <td>
                                            <?php if (!empty($renting_options)): 
                                                $selected = $insurance['renting_options'] ?? [];
                                              
                                                ?>
                                                <select class="form-select" name="<?php echo $this->option_key; ?>[insurances][<?php echo $index; ?>][renting_options][]" multiple>
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
                                            <select class="form-select" name="<?php echo $this->option_key; ?>[insurances][<?php echo $index; ?>][type_options][]" multiple>
                                                <?php 
                                                $selected_types = $insurance['type_options'] ?? [];
                                                foreach ($this->type_options as $type): ?>
                                                    <option value="<?php echo esc_attr($type); ?>" <?php echo in_array($type, $selected_types) ? 'selected' : ''; ?>>
                                                        <?php echo esc_html($type); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"><?php _e('Remove Insurance', 'ski-ride-servetech'); ?></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-success add-row"><?php _e('Add Insurance', 'ski-ride-servetech'); ?></button>
                </div>

                <div class="mt-3">
                    <?php submit_button(__('Save Insurance', 'ski-ride-servetech')); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'ski-ride-content_page_dev-ski-insurance') return;

        $renting_options = get_option('srs_renting_options', []);

        wp_enqueue_script(
            'srs-insurance-js',
            plugin_dir_url(__DIR__) . '../../assets/js/insurance.js',
            ['jquery'],
            SRS_PLUGIN_VERSION,
            true
        );

        wp_localize_script('srs-insurance-js', 'srsInsurance', [
            'rentingOptions' => $renting_options,
            'typeOptions' => $this->type_options,
            'noOptionsMsg'   => __('First add the renting options.', 'ski-ride-servetech'),
        ]);
    }
}
