<?php
if (!defined('ABSPATH')) exit;

class SRS_Sanitize_Settings {

    /**
     * Sanitize input for any module type
     * 
     * @param array $input Raw input from options form
     * @param string $type Module type: gear, insurance, locations, abilities, renting_options, boots
     * @return array Sanitized output
     */
    public static function sanitize($input, $type = 'gear') {
        $output = [];

        switch ($type) {
            // Gear-like modules
            case 'gear':
            case 'extra_gear':
            case 'gloves':
            case 'goggles':
            case 'socks':
            case 'packages':
                if (!empty($input[$type]) && is_array($input[$type])) {
                    foreach ($input[$type] as $gear) {
                        $name = sanitize_text_field($gear['name'] ?? '');
                        if (empty($name)) continue;

                        $desc = isset($gear['desc']) ? sanitize_textarea_field($gear['desc']) : '';
                        $prices = self::sanitize_prices($gear['prices'] ?? []);
                        $renting_options = self::sanitize_array($gear['renting_options'] ?? []);
                        $type_options = self::sanitize_array($insurance['type_options'] ?? []);
                        $item = [
                            'name'            => $name,
                            'desc'            => $desc,
                            'prices'          => $prices,
                            'renting_options' => $renting_options,
                            'type_options'    => $type_options,
                            'product_id'      => intval($gear['product_id'] ?? 0),
                        ];

                        $output[$type][] = $item;
                    }
                }
                break;

            // Insurance
            case 'insurance':
            case 'boots':
                if (!empty($input[$type]) && is_array($input[$type])) {
                    foreach ($input[$type] as $insurance) {
                        $name = sanitize_text_field($insurance['name'] ?? '');
                        if (empty($name)) continue;

                        $prices = self::sanitize_prices($insurance['prices'] ?? []);
                        $renting_options = self::sanitize_array($insurance['renting_options'] ?? []);
                        $type_options = self::sanitize_array($insurance['type_options'] ?? []);
                        $item = [
                            'name'            => $name,
                            'prices'          => $prices,
                            'renting_options' => $renting_options,
                            'type_options'    => $type_options,
                            'product_id'      => intval($insurance['product_id'] ?? 0),
                        ];

                        $output[$type][] = $item;
                    }
                }
                break;

            // Flat array modules
            case 'locations':
            case 'abilities':
            case 'renting_options':
                $output = self::sanitize_array($input);
                break;

        }

        return $output;
    }

    /**
     * Sanitize numeric prices array
     */
    private static function sanitize_prices($prices) {
        $out = [];
        if (!empty($prices['day']) && is_array($prices['day'])) {
            foreach ($prices['day'] as $i => $day) {
                $day = intval($day);
                $val = isset($prices['value'][$i]) ? floatval($prices['value'][$i]) : 0;
                if ($day > 0 || $val > 0) $out[$day] = $val;
            }
        }
        if (!empty($prices['extra'])) $out['extra'] = floatval($prices['extra']);
        return $out;
    }

    /**
     * Sanitize simple array of strings
     */
    private static function sanitize_array($arr) {
        $out = [];
        if (!empty($arr) && is_array($arr)) {
            foreach ($arr as $item) {
                $item = sanitize_text_field($item);
                if (!empty($item)) $out[] = $item;
            }
        }
        return $out;
    }
}
