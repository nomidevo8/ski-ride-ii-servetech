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

    private function __construct() {
        // Enqueue admin scripts/styles
        add_action('admin_enqueue_scripts', [$this, 'myplugin_enqueue_admin_assets']);

        // Register parent menu and submenus
        add_action('admin_menu', [$this, 'dev_register_all_menus']);

        // Register settings
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_init', [$this, 'dev_register_settings']);
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


     public function dev_register_all_menus() {
        $parent_slug = 'srs-settings';

        // Parent menu
        add_menu_page(
            __('Ski Ride Content', 'ski-ride-servetech'),
            __('Ski Ride Content', 'ski-ride-servetech'),
            'manage_options',
            $parent_slug,
            [$this, 'render_settings_page'],
            'dashicons-edit',
            30
        );

        // Submenus
        $submenus = [
            'locations'       => 'Locations',
            'abilities'       => 'Abilities',
            'renting-options' => 'Renting Options',
            'packages'        => 'Packages',
            'gloves'          => 'Gloves',
            'goggles'         => 'Goggles',
            'socks'           => 'Socks',
            'boots'           => 'Boots',
            'extra-gear'      => 'Extra Gear',
            // 'passes'          => 'Passes',
            'insurance'       => 'Insurance'
        ];

        foreach ($submenus as $slug => $title) {
            $class_name = 'SRS_' . str_replace('-', '_', ucwords($slug, '-'));
            $file = plugin_dir_path(__FILE__) . "inc/{$slug}.php";
        
            if (file_exists($file)) include $file;

            if (class_exists($class_name)) {
                $instance = new $class_name();

                add_submenu_page(
                    $parent_slug,
                    $title,
                    $title,
                    'manage_options',
                    'dev-ski-' . $slug,
                    [$instance, 'render_page']  
                );
            } else {
                add_submenu_page(
                    $parent_slug,
                    $title,
                    $title,
                    'manage_options',
                    'dev-ski-' . $slug,
                    function() use ($slug) {
                        echo "<h2>{$slug} page not found</h2>";
                    }
                );
            }
        }

    }

    /**
     * Register main plugin settings
     */
    public function register_settings() {
        register_setting('srs_settings_group', $this->option_key, [
            'sanitize_callback' => [$this, 'sanitize_settings']
        ]);
    }

    /**
     * Register settings for each submenu
     */
    public function dev_register_settings() {
        $submenus = [
            'locations', 'abilities', 'renting-options', 'packages',
            'extra-gear', 'gloves', 'goggles', 'socks', 'passes', 'insurance', 'boots'
        ];

        foreach ($submenus as $slug) {
            $option_key = 'dev_ski_' . $slug;
            register_setting('dev_ski_group_' . $slug, $option_key, 'dev_cleanly_sanitize_settings');
        }
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

        // 🔹 Boots Discount
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

                $main_key = in_array('name', $keys) ? 'name' : 'title';
                $names_or_titles = $input[$gear_type][$main_key] ?? [];
                $prices = $input[$gear_type]['price'] ?? [];
                $descs = $input[$gear_type]['desc'] ?? [];
                $product_ids = $input[$gear_type]['product_id'] ?? [];
                $renting_options = $input[$gear_type]['renting_options'] ?? [];
                $renting_prices_input = $input[$gear_type]['renting_prices'] ?? [];

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

                    // Skip empty rows
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

                    // 🔹 Handle dynamic day prices
                    $item_data['renting_prices'] = [];
                    if (!empty($renting_prices_input[$i])) {
                        $days = $renting_prices_input[$i]['day'] ?? [];
                        $day_prices = $renting_prices_input[$i]['price'] ?? [];
                        $extra_day = floatval($renting_prices_input[$i]['extra_day'] ?? 0);

                        for ($j = 0; $j < max(count($days), count($day_prices)); $j++) {
                            $day = intval($days[$j] ?? 0);
                            $d_price = floatval($day_prices[$j] ?? 0);
                            if ($day > 0 && $d_price > 0) {
                                $item_data['renting_prices'][$day] = $d_price;
                            }
                        }

                        $item_data['renting_prices']['extra_day'] = $extra_day;
                    }

                    // Sync with WooCommerce
                    $item_data['product_id'] = $this->sync_woocommerce_product($gear_type, $item_data);

                    // Save _rental_prices meta
                    update_post_meta($item_data['product_id'], '_rental_prices', $item_data['renting_prices']);

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
        if (!class_exists('WC_Product')) return 0;

        $product_name = $item['name'] ?? $item['title'] ?? '';
        $price = $item['price'] ?? 0;
        $product_id = intval($item['product_id'] ?? 0);

        if (empty($product_name)) return 0;

        $product = $product_id ? wc_get_product($product_id) : false;

        if ($product) {
            $product->set_regular_price($price);
            $product->set_name($product_name);
            $product->set_catalog_visibility('hidden');
            if (in_array($gear_type, ['packages','gears','insurance'])) {
                update_post_meta($product->get_id(), 'is_rental', 'yes');
            }
            $product->save();
            return $product->get_id();
        } else {
            $new_product = new \WC_Product_Simple();
            $new_product->set_name($product_name);
            $new_product->set_regular_price($price);
            $new_product->set_catalog_visibility('hidden');
            $new_product->save();
            $new_product_id = $new_product->get_id();
            if (in_array($gear_type, ['packages','gears','insurance'])) {
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
        </div>
        <?php
    }


}