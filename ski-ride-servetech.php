<?php
/**
 * Plugin Name: Ski Ride Servetech
 * Description: Multi-step ride booking form Elementor widget.
 * Version: 1.0.0
 * Author: Serve Tech Global
 * Author URI: https://servetechglobal.com
 * Text Domain: ski-ride-servetech
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // No direct access
}

// Define constants
define( 'SRS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'SRS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Autoload or manual includes
require_once SRS_PLUGIN_PATH . 'includes/class-plugin.php';

// Bootstrap
\SkiRideServetech\Plugin::instance();