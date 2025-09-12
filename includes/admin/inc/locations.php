<?php
if (!defined('ABSPATH')) exit;

class SRS_Locations {

    private $option_key = 'srs_locations';

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_settings() {
        register_setting('srs_locations_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    /**
     * Sanitize and save settings
     */
    public function sanitize_settings($input) {
        // Use the universal sanitizer for gears
        $output = SRS_Sanitize_Settings::sanitize($input, 'locations');
        return $output;
    }


    /**
     * Render admin page
     */
    public function render_page() {
        $options = get_option($this->option_key, []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1 class="wp-heading-inline"><?php _e('Locations', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_locations_group'); ?>

                <div class="container p-4 mt-3">
                    <table class="table table-bordered table-striped" id="locations-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?php _e('Location Name', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($options)): ?>
                                <?php foreach ($options as $index => $name): ?>
                                    <tr class="location-row" data-location-index="<?php echo $index; ?>">
                                        <td>
                                            <input type="text" class="form-control"
                                                   name="<?php echo $this->option_key; ?>[<?php echo $index; ?>]"
                                                   value="<?php echo esc_attr($name); ?>">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row"><?php _e('Remove', 'ski-ride-servetech'); ?></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success add-row"><?php _e('Add Location', 'ski-ride-servetech'); ?></button>
                </div>

                <div class="mt-3">
                    <?php submit_button(__('Save Locations', 'ski-ride-servetech')); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'ski-ride-content_page_dev-ski-locations') return;

        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], null, true);

        wp_enqueue_script(
            'srs-locations-js',
            plugin_dir_url(__DIR__) . '../../assets/js/locations.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }
}
