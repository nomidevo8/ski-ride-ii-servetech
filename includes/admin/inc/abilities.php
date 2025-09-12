<?php
if (!defined('ABSPATH')) exit;

class SRS_Abilities {

    private $option_key = 'srs_abilities';

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 1);
    }

    public function register_settings() {
        register_setting('srs_abilities_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    /**
     * Sanitize and save settings
     */
    public function sanitize_settings($input) {
    // Use the universal sanitizer for gears
        $output = SRS_Sanitize_Settings::sanitize($input, 'abilities');
        return $output;
    }


    /**
     * Render admin page
     */
    public function render_page() {
        $options = get_option($this->option_key, []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1 class="wp-heading-inline"><?php _e('Abilities', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_abilities_group'); ?>

                <div class="container p-4 mt-3">
                    <table class="table table-bordered table-striped" id="abilities-table">
                        <thead class="table-dark">
                            <tr>
                                <th><?php _e('Ability Name', 'ski-ride-servetech'); ?></th>
                                <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($options)): ?>
                                <?php foreach ($options as $index => $name): ?>
                                    <tr class="ability-row" data-ability-index="<?php echo $index; ?>">
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
                    <button type="button" class="btn btn-success add-row"><?php _e('Add Ability', 'ski-ride-servetech'); ?></button>
                </div>

                <div class="mt-3">
                    <?php submit_button(__('Save Abilities', 'ski-ride-servetech')); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function enqueue_scripts($hook) {
        if ($hook !== 'ski-ride-content_page_dev-ski-abilities') return;

        wp_enqueue_script(
            'srs-abilities-js',
            plugin_dir_url(__DIR__) . '../../assets/js/abilities.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }
}
