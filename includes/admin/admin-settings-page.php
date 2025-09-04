<?php
namespace SkiRideAdminServetech;

if (!defined('ABSPATH'))
    exit;

class Admin_settings_page
{
    private static $_instance = null;
    private $option_key = 'srs_form_settings';
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'myplugin_enqueue_admin_assets']);
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }


    public function myplugin_enqueue_admin_assets($hook)
    {
        // Load Bootstrap CSS
        wp_enqueue_style(
            'bootstrap-css',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            array(),
            '5.3.3'
        );

        // Load Bootstrap JS
        wp_enqueue_script(
            'bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            array('jquery'),
            '5.3.3',
            true
        );
    }


    public function register_menu()
    {
        add_menu_page(
            __('Ski Ride Content', 'ski-ride-servetech'),
            __('Ski Ride Content', 'ski-ride-servetech'),
            'manage_options',
            'srs-settings',
            [$this, 'render_settings_page'],
            'dashicons-edit',
            30
        );
    }

    public function register_settings()
    {
        register_setting('srs_settings_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }



    /**
     * Sanitize plugin settings
     */
    public function sanitize_settings($input)
    {
        $output = [];

        // Form Title
        if (!empty($input['form_title'])) {
            $output['form_title'] = sanitize_text_field($input['form_title']);
        }

        // Locations & Abilities
        $fields_to_map = ['locations', 'abilities', 'renting_options'];
        foreach ($fields_to_map as $field) {
            if (!empty($input[$field]) && is_array($input[$field])) {
                $output[$field] = array_map('sanitize_text_field', $input[$field]);
            }
        }

        // 🔹 Insurance & Boots Discount
        $output['insurance_price'] = !empty($input['insurance_price']) ? floatval($input['insurance_price']) : 0;
        $insurance_product_id      = !empty($input['insurance_product_id']) ? intval($input['insurance_product_id']) : 0;
        if ($output['insurance_price'] > 0) {
            $insurance_item = [
                'name' => 'Insurance',
                'price' => $output['insurance_price'],
                'product_id' => $insurance_product_id,
            ];
            $output['insurance_product_id'] = $this->sync_woocommerce_product('insurance', $insurance_item);
        }

        // 🔹 Boots Discount (always just stored, applied as fee)
        $output['boots_discount'] = !empty($input['boots_discount']) ? floatval($input['boots_discount']) : 0;



        // Gear Types Configuration
        $gear_types = [
            'packages' => ['name', 'price', 'desc', 'product_id'],
            'gears' => ['name', 'price', 'product_id'],
            'gloves' => ['name', 'price', 'desc', 'product_id'],
            'goggles' => ['name', 'price', 'desc', 'product_id'],
            'socks' => ['name', 'price', 'desc', 'product_id'],
            'passes' => ['title', 'price', 'product_id'],
        ];

        foreach ($gear_types as $gear_type => $keys) {
            if (!empty($input[$gear_type]) && is_array($input[$gear_type])) {
                $output[$gear_type] = [];

                // Determine main key (name or title)
                $main_key = in_array('name', $keys) ? 'name' : 'title';
                $names_or_titles = $input[$gear_type][$main_key] ?? [];
                $prices = $input[$gear_type]['price'] ?? [];
                $descs = $input[$gear_type]['desc'] ?? [];
                $product_ids     = $input[$gear_type]['product_id'] ?? [];
                $renting_options = $input[$gear_type]['renting_options'] ?? [];

                $count = max(
                    count($names_or_titles),
                    count($prices),
                    count($descs),
                    count($product_ids),
                    count($renting_options)
                );

                for ($i = 0; $i < $count; $i++) {
                    $title = $names_or_titles[$i] ?? '';
                    $price = $prices[$i] ?? 0;
                    $desc = $descs[$i] ?? '';
                    $product_id = $product_ids[$i] ?? 0;
                    $rent_option = $renting_options[$i] ?? [];

                    // Skip completely empty rows
                    if (empty($title) && empty($price) && empty($desc)) {
                        continue;
                    }

                    $item_data = [
                        $main_key => sanitize_text_field($title),
                        'price' => floatval($price),
                        'product_id' => intval($product_id),
                        'renting_options' => is_array($rent_option) ? array_map('sanitize_text_field', $rent_option) : [],
                    ];

                    if (in_array('desc', $keys)) {
                        $item_data['desc'] = sanitize_text_field($desc);
                    }

                    // Sync with WooCommerce and update product_id
                    $item_data['product_id'] = $this->sync_woocommerce_product($gear_type, $item_data);

                    $output[$gear_type][] = $item_data;
                }
            }
        }

        return $output;
    }


    /**
     * 🔹 Sync a gear item with WooCommerce product
     */
    private function sync_woocommerce_product($gear_type, $item)
    {
        if (!class_exists('WC_Product')) {
            return 0; // WooCommerce not active
        }

        $product_name = $item['name'] ?? $item['title'] ?? '';
        $price        = $item['price'] ?? 0;
        $product_id   = intval($item['product_id'] ?? 0);

        if (empty($product_name)) {
            return 0;
        }

        // Try to load by provided product_id first
        $product = $product_id ? wc_get_product($product_id) : false;

        // If no valid product, try finding by title
        if (!$product) {
            $existing = get_page_by_title($product_name, OBJECT, 'product');
            if ($existing) {
                $product_id = $existing->ID;
                $product    = wc_get_product($product_id);
            }
        }

        if ($product) {
            // Update existing product
            $product->set_regular_price($price);
            $product->set_name($product_name);
            $product->set_catalog_visibility('hidden'); 
            if (in_array($gear_type, ['packages', 'gears', 'insurance'])) {
                update_post_meta($product->get_id(), 'is_rental', 'yes');
            }
            $product->save();
            return $product->get_id();
        } else {
            // Create new product
            $new_product = new \WC_Product_Simple();
            $new_product->set_name($product_name);
            $new_product->set_regular_price($price);
            $new_product->set_catalog_visibility('hidden'); 
            $new_product->save();   
            $new_product_id = $new_product->get_id();

            if (in_array($gear_type, ['packages', 'gears', 'insurance'])) {
                update_post_meta($new_product_id, 'is_rental', 'yes');
            }

            return $new_product_id;
        }
    }


    public function render_settings_page()
    {
        $options = get_option($this->option_key, []);
        ?>
        <div class="wrap bootstrap-wrapper">
            <h1><?php _e('Ski Ride Form Settings', 'ski-ride-servetech'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('srs_settings_group'); ?>

                <!-- Bootstrap Nav Tabs -->
                <ul class="nav nav-tabs mb-4" id="srsSettingsTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab"
                            href="#tab-locations"><?php _e('Locations', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-abilities"><?php _e('Abilities', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-renting"><?php _e('Renting Options', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-packages"><?php _e('Packages', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-gears"><?php _e('Extra Gear', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-gloves"><?php _e('Gloves', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-goggles"><?php _e('Goggles', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-socks"><?php _e('Socks', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-passes"><?php _e('Passes', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-insurance"><?php _e('Insurance', 'ski-ride-servetech'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab"
                            href="#tab-boots"><?php _e('Boots Discount', 'ski-ride-servetech'); ?></a></li>
                </ul>

                <div class="tab-content">

                    <!-- LOCATIONS -->
                    <div class="tab-pane fade show active" id="tab-locations">
                        <h4><?php _e('Manage Locations', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="locations">
                            <thead>
                                <tr>
                                    <th><?php _e('Location Name', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $locations = $options['locations'] ?? [];
                                foreach ($locations as $loc) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[locations][]"
                                                value="<?php echo esc_attr($loc); ?>"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="locations">+
                            <?php _e('Add Location', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- ABILITIES -->
                    <div class="tab-pane fade" id="tab-abilities">
                        <h4><?php _e('Ability Levels', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="abilities">
                            <thead>
                                <tr>
                                    <th><?php _e('Ability', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $abilities = $options['abilities'] ?? [];
                                foreach ($abilities as $ability) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[abilities][]"
                                                value="<?php echo esc_attr($ability); ?>"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="abilities">+
                            <?php _e('Add Ability', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- RENTING OPTIONS -->
                    <div class="tab-pane fade" id="tab-renting">
                        <h4><?php _e('Renting Options', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="renting_options">
                            <thead>
                                <tr>
                                    <th><?php _e('Option', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $renting = $options['renting_options'] ?? [];
                                foreach ($renting as $opt) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[renting_options][]"
                                                value="<?php echo esc_attr($opt); ?>"></td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="renting_options">+
                            <?php _e('Add Renting Option', 'ski-ride-servetech'); ?></button>
                    </div>


                    <!-- PACKAGES -->
                    <div class="tab-pane fade" id="tab-packages">
                        <h4><?php _e('Packages', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="packages">
                            <thead>
                                <tr>
                                    <th><?php _e('Name', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Price/per day', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Description', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Assign Renting Options', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $packages = $options['packages'] ?? [];
                                $renting_options = $options['renting_options'] ?? [];
                                foreach ($packages as $i => $package) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[packages][name][]"
                                                value="<?php echo esc_attr($package['name'] ?? ''); ?>"></td>
                                        <td><input type="number" class="form-control"
                                                name="<?php echo $this->option_key; ?>[packages][price][]"
                                                value="<?php echo esc_attr($package['price'] ?? ''); ?>"></td>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[packages][desc][]"
                                                value="<?php echo esc_attr($package['desc'] ?? ''); ?>"></td>
                                        <input type="hidden"
                                            name="<?php echo $this->option_key; ?>[packages][product_id][]"
                                            value="<?php echo esc_attr($package['product_id'] ?? 0); ?>">
                                        <!-- Multi-select renting options -->
                                        <td>
                                            <select
                                                name="<?php echo $this->option_key; ?>[packages][renting_options][<?php echo $i; ?>][]"
                                                class="form-control" multiple>
                                                <?php foreach ($renting_options as $rIndex => $renting) {
                                                    $selected = in_array($rIndex, $package['renting_options'] ?? []) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?php echo $rIndex; ?>" <?php echo $selected; ?>>
                                                        <?php echo esc_html($renting); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </td>

                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="packages">+
                            <?php _e('Add Package', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- EXTRA GEAR -->
                    <div class="tab-pane fade" id="tab-gears">
                        <h4><?php _e('Extra Gear', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="gears">
                            <thead>
                                <tr>
                                    <th><?php _e('Gear Name', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Price/per day', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Assign Renting Options', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $gears = $options['gears'] ?? [];
                                $renting_options = $options['renting_options'] ?? ['Option 1', 'Option 2', 'Option 3'];
                                foreach ($gears as $i => $gear) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gears][name][]"
                                                value="<?php echo esc_attr($gear['name'] ?? ''); ?>"></td>
                                        <td><input type="number" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gears][price][]"
                                                value="<?php echo esc_attr($gear['price'] ?? ''); ?>"></td>
                                        <input type="hidden" name="<?php echo $this->option_key; ?>[gears][product_id][]"
                                            value="<?php echo esc_attr($gear['product_id'] ?? 0); ?>">
                                        <td>
                                            <select
                                                name="<?php echo $this->option_key; ?>[gears][renting_options][<?php echo $i; ?>][]"
                                                class="form-control" multiple>
                                                <?php foreach ($renting_options as $rIndex => $rName) {
                                                    $selected = in_array($rIndex, $gear['renting_options'] ?? []) ? 'selected' : '';
                                                    echo "<option value='{$rIndex}' {$selected}>" . esc_html($rName) . "</option>";
                                                } ?>
                                            </select>
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="gears">+
                            <?php _e('Add Gear', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- GLOVES -->
                    <div class="tab-pane fade" id="tab-gloves">
                        <h4><?php _e('Gloves', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="gloves">
                            <thead>
                                <tr>
                                    <th><?php _e('Name', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Price', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Description', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Assign Renting Options', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $gloves = $options['gloves'] ?? [];
                                foreach ($gloves as $i => $glove) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gloves][name][]"
                                                value="<?php echo esc_attr($glove['name'] ?? ''); ?>"></td>
                                        <td><input type="number" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gloves][price][]"
                                                value="<?php echo esc_attr($glove['price'] ?? ''); ?>"></td>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[gloves][desc][]"
                                                value="<?php echo esc_attr($glove['desc'] ?? ''); ?>"></td>
                                        <input type="hidden" name="<?php echo $this->option_key; ?>[gloves][product_id][]"
                                            value="<?php echo esc_attr($glove['product_id'] ?? 0); ?>">                                                
                                        <td>
                                            <select
                                                name="<?php echo $this->option_key; ?>[gloves][renting_options][<?php echo $i; ?>][]"
                                                class="form-control" multiple>
                                                <?php foreach ($renting_options as $rIndex => $rName) {
                                                    $selected = in_array($rIndex, $glove['renting_options'] ?? []) ? 'selected' : '';
                                                    echo "<option value='{$rIndex}' {$selected}>" . esc_html($rName) . "</option>";
                                                } ?>
                                            </select>
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="gloves">+
                            <?php _e('Add Glove', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- GOGGLES -->
                    <div class="tab-pane fade" id="tab-goggles">
                        <h4><?php _e('Goggles', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="goggles">
                            <thead>
                                <tr>
                                    <th><?php _e('Name', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Price', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Description', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Assign Renting Options', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $goggles = $options['goggles'] ?? [];
                                foreach ($goggles as $i => $goggle) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[goggles][name][]"
                                                value="<?php echo esc_attr($goggle['name'] ?? ''); ?>"></td>
                                        <td><input type="number" class="form-control"
                                                name="<?php echo $this->option_key; ?>[goggles][price][]"
                                                value="<?php echo esc_attr($goggle['price'] ?? ''); ?>"></td>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[goggles][desc][]"
                                                value="<?php echo esc_attr($goggle['desc'] ?? ''); ?>"></td>
                                        <input type="hidden" name="<?php echo $this->option_key; ?>[goggles][product_id][]"
                                            value="<?php echo esc_attr($goggle['product_id'] ?? 0); ?>">                                                
                                        <td>
                                            <select
                                                name="<?php echo $this->option_key; ?>[goggles][renting_options][<?php echo $i; ?>][]"
                                                class="form-control" multiple>
                                                <?php foreach ($renting_options as $rIndex => $rName) {
                                                    $selected = in_array($rIndex, $goggle['renting_options'] ?? []) ? 'selected' : '';
                                                    echo "<option value='{$rIndex}' {$selected}>" . esc_html($rName) . "</option>";
                                                } ?>
                                            </select>
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="goggles">+
                            <?php _e('Add Goggle', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- SOCKS -->
                    <div class="tab-pane fade" id="tab-socks">
                        <h4><?php _e('Socks', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="socks">
                            <thead>
                                <tr>
                                    <th><?php _e('Name', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Price', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Description', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Assign Renting Options', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $socks = $options['socks'] ?? [];
                                foreach ($socks as $i => $sock) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[socks][name][]"
                                                value="<?php echo esc_attr($sock['name'] ?? ''); ?>"></td>
                                        <td><input type="number" class="form-control"
                                                name="<?php echo $this->option_key; ?>[socks][price][]"
                                                value="<?php echo esc_attr($sock['price'] ?? ''); ?>"></td>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[socks][desc][]"
                                                value="<?php echo esc_attr($sock['desc'] ?? ''); ?>"></td>
                                        <input type="hidden" name="<?php echo $this->option_key; ?>[socks][product_id][]"
                                            value="<?php echo esc_attr($sock['product_id'] ?? 0); ?>">
                                        <td>
                                            <select
                                                name="<?php echo $this->option_key; ?>[socks][renting_options][<?php echo $i; ?>][]"
                                                class="form-control" multiple>
                                                <?php foreach ($renting_options as $rIndex => $rName) {
                                                    $selected = in_array($rIndex, $sock['renting_options'] ?? []) ? 'selected' : '';
                                                    echo "<option value='{$rIndex}' {$selected}>" . esc_html($rName) . "</option>";
                                                } ?>
                                            </select>
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="socks">+
                            <?php _e('Add Sock', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- PASSES -->
                    <div class="tab-pane fade" id="tab-passes">
                        <h4><?php _e('Passes', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle repeater-table" data-field="passes">
                            <thead>
                                <tr>
                                    <th><?php _e('Title', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Price', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Assign Renting Options', 'ski-ride-servetech'); ?></th>
                                    <th><?php _e('Actions', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $passes = $options['passes'] ?? [];
                                foreach ($passes as $i => $pass) { ?>
                                    <tr>
                                        <td><input type="text" class="form-control"
                                                name="<?php echo $this->option_key; ?>[passes][title][]"
                                                value="<?php echo esc_attr($pass['title'] ?? ''); ?>"></td>
                                        <td><input type="number" class="form-control"
                                                name="<?php echo $this->option_key; ?>[passes][price][]"
                                                value="<?php echo esc_attr($pass['price'] ?? ''); ?>"></td>
                                        <input type="hidden" name="<?php echo $this->option_key; ?>[passes][product_id][]"
                                            value="<?php echo esc_attr($pass['product_id'] ?? 0); ?>">
                                        <td>
                                            <select
                                                name="<?php echo $this->option_key; ?>[passes][renting_options][<?php echo $i; ?>][]"
                                                class="form-control" multiple>
                                                <?php foreach ($renting_options as $rIndex => $rName) {
                                                    $selected = in_array($rIndex, $pass['renting_options'] ?? []) ? 'selected' : '';
                                                    echo "<option value='{$rIndex}' {$selected}>" . esc_html($rName) . "</option>";
                                                } ?>
                                            </select>
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-success add-row" data-field="passes">+
                            <?php _e('Add Pass', 'ski-ride-servetech'); ?></button>
                    </div>

                    <!-- INSURANCE -->
                    <div class="tab-pane fade" id="tab-insurance">
                        <h4><?php _e('Insurance Settings', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th><?php _e('Insurance Price (per day)', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="number" step="0.01" class="form-control"
                                            name="<?php echo $this->option_key; ?>[insurance_price]"
                                            value="<?php echo esc_attr($options['insurance_price'] ?? ''); ?>">
                                        <input type="hidden"
                                            name="<?php echo $this->option_key; ?>[insurance_product_id][]"
                                            value="<?php echo esc_attr($options['insurance_product_id'] ?? 0); ?>">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- BOOTS DISCOUNT -->
                    <div class="tab-pane fade" id="tab-boots">
                        <h4><?php _e('Boots Discount Settings', 'ski-ride-servetech'); ?></h4>
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th><?php _e('Boots Discount (per day)', 'ski-ride-servetech'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="number" step="0.01" class="form-control"
                                            name="<?php echo $this->option_key; ?>[boots_discount]"
                                            value="<?php echo esc_attr($options['boots_discount'] ?? ''); ?>">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>



                </div>

                <?php submit_button(__('Save Settings', 'ski-ride-servetech'), 'primary'); ?>
            </form>
        </div>

        <!-- Dynamic Row Script -->
        <script>
            jQuery(document).ready(function ($) {
                // 🔹 Make renting options available to JS
                let rentingOptions = <?php echo json_encode($options['renting_options'] ?? []); ?>;

                $(".add-row").on("click", function () {
                    let field = $(this).data("field");
                    let table = $(".repeater-table[data-field='" + field + "'] tbody");

                    let row = "";

                    // 🔹 Build renting options select HTML
                    let selectHtml = "<select class='form-control' multiple name='<?php echo $this->option_key; ?>[" + field + "][renting_options][]'>";
                    $.each(rentingOptions, function (index, name) {
                        selectHtml += "<option value='" + index + "'>" + name + "</option>";
                    });
                    selectHtml += "</select>";

                    if (["packages", "gloves", "goggles", "socks"].includes(field)) {
                        row = `<tr>
                            <td><input type="text" class="form-control" name="<?php echo $this->option_key; ?>[`+ field + `][name][]" placeholder="Name"></td>
                            <td><input type="number" class="form-control" name="<?php echo $this->option_key; ?>[`+ field + `][price][]" placeholder="Price"></td>
                            <td><input type="text" class="form-control" name="<?php echo $this->option_key; ?>[`+ field + `][desc][]" placeholder="Description"></td>
                            <td>${selectHtml}</td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                        </tr>`;
                    } else if (field === "passes" || field === "gears") {
                        row = `<tr>
                            <td><input type="text" class="form-control" name="<?php echo $this->option_key; ?>[`+ field + `][title][]" placeholder="Title"></td>
                            <td><input type="number" class="form-control" name="<?php echo $this->option_key; ?>[`+ field + `][price][]" placeholder="Price"></td>
                            <td>${selectHtml}</td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                        </tr>`;
                    } else {
                        row = `<tr>
                            <td><input type="text" class="form-control" name="<?php echo $this->option_key; ?>[`+ field + `][]" placeholder="Enter value"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                        </tr>`;
                    }

                    table.append(row);
                });

                // 🔹 Remove row
                $(document).on("click", ".remove-row", function () {
                    $(this).closest("tr").remove();
                });
            });
        </script>

        <?php
    }


}