<?php
/**
 * Plugin Name: WooCommerce Order Test - WP Fix It
 * Plugin URI:  https://www.wpfixit.com
 * Description: A testing payment gateway for WooCommerce to see if your checkout works like it should. This will be for admin users only.
 * Author:      WP Fix It
 * Author URI:  https://www.wpfixit.com
 * Version:     4.1
 * Text Domain: woo-order-test
 
 * Requires Plugins: woocommerce
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
add_action('admin_init', function () {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>EmailOctopus & Woo Connection:</strong> This plugin requires <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> to be installed and activated.</p></div>';
        });
        // Deactivate plugin if WooCommerce is not active
        deactivate_plugins(plugin_basename(__FILE__));
    }
});

// Redirect to settings page upon plugin activation
add_action('activated_plugin', 'wpfi_redirect_after_activation');

function wpfi_redirect_after_activation($plugin) {
    if ($plugin === plugin_basename(__FILE__)) {
        // Set an option to trigger redirect
        update_option('wpfi_do_activation_redirect', true);
    }
}

add_action('admin_init', 'wpfi_do_activation_redirect');

function wpfi_do_activation_redirect() {
    if (get_option('wpfi_do_activation_redirect')) {
        delete_option('wpfi_do_activation_redirect');
        if (!isset($_GET['activate-multi'])) {
            wp_redirect(admin_url('admin.php?page=wc-settings&tab=checkout&section=wpfi_woo_order_test'));
            exit;
        }
    }
}

require_once __DIR__ . '/admin/functions.php';
// Enqueue plugin CSS
function wpfi_order_test_css() {
    $css_file = plugin_dir_path(__FILE__) . 'admin/assets/wcot.css';
    $version = file_exists($css_file) ? filemtime($css_file) : '1.0';

    wp_enqueue_style(
        'wpfi_order_test_css',
        plugins_url('/admin/assets/wcot.css', __FILE__),
        array(),
        $version
    );
}
add_action('admin_enqueue_scripts', 'wpfi_order_test_css');
// Add links to plugin settings and support page
function wpfi_plugin_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=wpfi_woo_order_test')) . '">' . esc_html__('Settings', 'woo-order-test') . '</a>';
    $support_link  = '<a href="https://www.wpfixit.com/" target="_blank"><b><span class="ticket-link">' . esc_html__('GET HELP', 'woo-order-test') . '</span></b></a>';
    array_unshift($links, $settings_link);
    array_unshift($links, $support_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wpfi_plugin_action_links');
// Activation hook
register_activation_hook(__FILE__, 'wpfi_plugin_activate');
function wpfi_plugin_activate() {
    // Set default options
    update_option('admin_payment_bypass_enabled', 'yes');
}
add_filter('woocommerce_gateway_title', 'wpfi_modify_gateway_title', 10, 2);