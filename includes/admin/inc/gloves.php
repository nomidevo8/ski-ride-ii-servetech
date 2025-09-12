<?php
if (!defined('ABSPATH')) exit;

class SRS_Gloves {

    private $option_key = 'srs_gloves';
    private $type_options = SRS_TYPE_OPTIONS;
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_settings() {
        register_setting('srs_gloves_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    /**
     * Sanitize and save settings
     */
    public function sanitize_settings($input) {
    // Use the universal sanitizer for gears
        $output = SRS_Sanitize_Settings::sanitize($input, 'gloves');

        // Sync products to WooCommerce automatically
        if (!empty($output['gloves'])) {
            foreach ($output['gloves'] as &$item) {
                $item['product_id'] = SRS_Sync_WooCommerce::sync_product($item, 'gloves');
            }
        }

        return $output;
    }

    /**
     * Render admin page
     */
    public function render_page() {
        $options = get_option($this->option_key, []);
        $gears = $options['gloves'] ?? [];

        // Fetch Renting Options (to show as multi-select)
        $renting_options = get_option('srs_renting_options', []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1 class="wp-heading-inline"><?php _e('Gloves', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_gloves_group'); ?>

                <div class="container p-4 mt-3">
                    <table class="table table-bordered table-striped" id="gloves-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?php _e('Glove Name', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Description', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Pricing', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Extra Day Price', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Renting Options', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Type', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($gears)): ?>
                                <?php foreach ($gears as $gear_index => $gear): ?>
                                    <tr class="gear-row" data-gear-index="<?php echo $gear_index; ?>">
                                        <td>
                                            <input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][name]"
                                                value="<?php echo esc_attr($gear['name'] ?? ''); ?>">
                                            <input type="hidden"
                                                name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][product_id]"
                                                value="<?php echo esc_attr($gear['product_id'] ?? 0); ?>">
                                        </td>

                                        <td>
                                            <textarea class="form-control" rows="2"
                                                name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][desc]"><?php echo esc_textarea($gear['desc'] ?? ''); ?></textarea>
                                        </td>

                                        <td>
                                            <div class="form-check form-switch mb-2">
                                                <?php $is_rental = !empty($gear['is_rental']); ?>
                                                <input class="form-check-input rental-toggle" type="checkbox" role="switch"
                                                    id="gl-rental-toggle-<?php echo $gear_index; ?>"
                                                    name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][is_rental]"
                                                    value="1" <?php checked($is_rental, true); ?>>
                                                <label class="form-check-label" for="gl-rental-toggle-<?php echo $gear_index; ?>">
                                                    <?php _e('Rental (multi-day pricing)', 'ski-ride-servetech'); ?>
                                                </label>
                                            </div>

                                            <div class="base-price-wrapper mb-2" style="<?php echo $is_rental ? 'display:none;' : ''; ?>">
                                                <label class="form-label mb-1"><?php _e('Base Price', 'ski-ride-servetech'); ?></label>
                                                <input type="number" step="0.01" class="form-control base-price-input"
                                                    name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][base_price]"
                                                    value="<?php echo esc_attr($gear['base_price'] ?? ''); ?>">
                                            </div>

                                            <table class="table table-sm table-bordered day-prices-table" style="<?php echo $is_rental ? '' : 'display:none;'; ?>">
                                                <thead>
                                                    <tr>
                                                        <th><?php _e('Day', 'ski-ride-servetech'); ?></th>
                                                        <th><?php _e('Price', 'ski-ride-servetech'); ?></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($gear['prices']) && $is_rental): ?>
                                                        <?php foreach ($gear['prices'] as $day => $price): ?>
                                                            <?php if ($day === 'extra') continue; ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="number" min="1" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][prices][day][]"
                                                                        value="<?php echo esc_attr($day); ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][prices][value][]"
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
                                            <button type="button" class="btn btn-sm btn-primary add-day" style="<?php echo $is_rental ? '' : 'display:none;'; ?>"><?php _e('Add Day', 'ski-ride-servetech'); ?></button>
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" class="form-control extra-price-input" <?php echo $is_rental ? '' : 'disabled'; ?>
                                                name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][prices][extra]"
                                                value="<?php echo esc_attr($gear['prices']['extra'] ?? ''); ?>">
                                        </td>

                                        <td>
                                            <?php if (!empty($renting_options)): 
                                                $selected = $gear['renting_options'] ?? [];
                                                ?>
                                                <select class="form-select" name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][renting_options][]" multiple>
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
                                            <select class="form-select" name="<?php echo $this->option_key; ?>[gloves][<?php echo $gear_index; ?>][type_options][]" multiple>
                                                <?php 
                                                $selected_types = $gear['type_options'] ?? [];
                                                foreach ($this->type_options as $type): ?>
                                                    <option value="<?php echo esc_attr($type); ?>" <?php echo in_array($type, $selected_types) ? 'selected' : ''; ?>>
                                                        <?php echo esc_html($type); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"><?php _e('Remove Glove', 'ski-ride-servetech'); ?></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-success add-row"><?php _e('Add Glove', 'ski-ride-servetech'); ?></button>
                </div>

                <div class="mt-3">
                    <?php submit_button(__('Save Gloves', 'ski-ride-servetech')); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts($hook) {
        // adjust this hook to match where the Gloves admin page lives
        if ($hook !== 'ski-ride-content_page_dev-ski-gloves') return;

        $renting_options = get_option('srs_renting_options', []);

        wp_enqueue_script(
            'srs-gloves-js',
            plugin_dir_url(__DIR__) . '../../assets/js/gloves.js',
            ['jquery'],
            SRS_PLUGIN_VERSION,
            true
        );

        wp_localize_script('srs-gloves-js', 'srsGloves', [
            'rentingOptions' => $renting_options,
            'typeOptions' => $this->type_options,
            'noOptionsMsg'   => __('First add the renting options.', 'ski-ride-servetech'),
        ]);
    }
}
