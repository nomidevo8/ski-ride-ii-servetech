<?php
if (!defined('ABSPATH')) exit;

class SRS_Packages {

    private $option_key = 'srs_packages';

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_settings() {
        register_setting('srs_packages_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    public function sanitize_settings($input) {
    // Use the universal sanitizer for gears
        $output = SRS_Sanitize_Settings::sanitize($input, 'packages');

        // Sync products to WooCommerce automatically
        if (!empty($output['packages'])) {
            foreach ($output['packages'] as &$item) {
                $item['product_id'] = SRS_Sync_WooCommerce::sync_product($item, 'packages');
            }
        }

        return $output;
    }

    public function render_page() {
        $options = get_option($this->option_key, []);
        $gears = $options['gears'] ?? [];
        $renting_options = get_option('srs_renting_options', []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1 class="wp-heading-inline"><?php _e('Packages', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_packages_group'); ?>

                <div class="container p-4 mt-3">
                    <table class="table table-bordered table-striped" id="packages-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?php _e('Package Name', 'ski-ride-servetech'); ?></th>
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
                                            <button type="button" class="btn btn-danger btn-sm remove-packages-row"><?php _e('Remove Package', 'ski-ride-servetech'); ?></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-success add-packages-row"><?php _e('Add Package', 'ski-ride-servetech'); ?></button>
                </div>

                <div class="mt-3">
                    <?php submit_button(__('Save Packages', 'ski-ride-servetech')); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'ski-ride-content_page_dev-ski-packages') return;

        $renting_options = get_option('srs_renting_options', []);

        wp_enqueue_script(
            'srs-packages-js',
            plugin_dir_url(__DIR__) . '../../assets/js/packages.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('srs-packages-js', 'srsPackages', [
            'rentingOptions' => $renting_options,
            'noOptionsMsg'   => __('First add the renting options.', 'ski-ride-servetech'),
        ]);
    }
}
