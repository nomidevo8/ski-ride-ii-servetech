<?php
if (!defined('ABSPATH')) exit;
require_once plugin_dir_path(__FILE__) . 'sanitize-functions.php';
require_once plugin_dir_path(__FILE__) . 'woocommerce-sync.php';

class SRS_Boots {

    private $option_key = 'srs_boots';
    private $type_options = SRS_TYPE_OPTIONS;
    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_settings() {
        register_setting('srs_boots_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    public function sanitize_settings($input) {
    // Use the universal sanitizer for gears
        $output = SRS_Sanitize_Settings::sanitize($input, 'boots');

        // Sync products to WooCommerce automatically
        if (!empty($output['boots'])) {
            foreach ($output['boots'] as &$item) {
                $item['product_id'] = SRS_Sync_WooCommerce::sync_product($item, 'boots');
            }
        }

        return $output;
    }

    public function render_page() {
        $options = get_option($this->option_key, []);
        $boots = $options['boots'] ?? [];
        $renting_options = get_option('srs_renting_options', []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1 class="wp-heading-inline"><?php _e('Boots', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_boots_group'); ?>

                <div class="container p-4 mt-3">
                    <table class="table table-bordered table-striped" id="boots-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?php _e('Boot Name', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Day-wise Prices', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Extra Day Price', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Renting Options', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Type', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($boots)): ?>
                                <?php foreach ($boots as $index => $boot): ?>
                                    <tr class="boot-row" data-boot-index="<?php echo $index; ?>">
                                        <td>
                                            <input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[boots][<?php echo $index; ?>][name]"
                                                value="<?php echo esc_attr($boot['name'] ?? ''); ?>">
                                            <input type="hidden"
                                                name="<?php echo $this->option_key; ?>[boots][<?php echo $index; ?>][product_id]"
                                                value="<?php echo esc_attr($boot['product_id'] ?? 0); ?>">
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
                                                    <?php if (!empty($boot['prices'])): ?>
                                                        <?php foreach ($boot['prices'] as $day => $price): ?>
                                                            <?php if ($day === 'extra') continue; ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="number" min="1" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[boots][<?php echo $index; ?>][prices][day][]"
                                                                        value="<?php echo esc_attr($day); ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control"
                                                                        name="<?php echo $this->option_key; ?>[boots][<?php echo $index; ?>][prices][value][]"
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
                                                name="<?php echo $this->option_key; ?>[boots][<?php echo $index; ?>][prices][extra]"
                                                value="<?php echo esc_attr($boot['prices']['extra'] ?? ''); ?>">
                                        </td>
                                        <td>
                                            <?php if (!empty($renting_options)): 
                                                $selected = $boot['renting_options'] ?? [];
                                                ?>
                                                <select class="form-select" name="<?php echo $this->option_key; ?>[boots][<?php echo $index; ?>][renting_options][]" multiple>
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
                                        <?php 
                                           
                                            ?>
                                            <select class="form-select" name="<?php echo $this->option_key; ?>[boots][<?php echo $index; ?>][type_options][]" multiple>
                                                
                                                <?php
                                                $selected_types = $boot['type_options'] ?? [];

                                                 foreach ($this->type_options as $type): ?>
                                                    <option value="<?php echo esc_attr($type); ?>" <?php echo in_array($type, $selected_types) ? 'selected' : ''; ?>>
                                                        <?php echo esc_html($type); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"><?php _e('Remove Boot', 'ski-ride-servetech'); ?></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-success add-row"><?php _e('Add Boot', 'ski-ride-servetech'); ?></button>
                </div>

                <div class="mt-3">
                    <?php submit_button(__('Save Boots', 'ski-ride-servetech')); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'ski-ride-content_page_dev-ski-boots') return;

        $renting_options = get_option('srs_renting_options', []);

        wp_enqueue_script(
            'srs-boots-js',
            plugin_dir_url(__DIR__) . '../../assets/js/boots.js',
            ['jquery'],
            SRS_PLUGIN_VERSION,
            true
        );

        wp_localize_script('srs-boots-js', 'srsBoots', [
            'rentingOptions' => $renting_options,
            'typeOptions' => $this->type_options,
            'noOptionsMsg'   => __('First add the renting options.', 'ski-ride-servetech'),
        ]);
    }
}
