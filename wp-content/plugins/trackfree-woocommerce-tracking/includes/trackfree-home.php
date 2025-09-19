<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('wp_ajax_tfree_get_recent_order', 'tfree_get_recent_order');
    add_action('wp_ajax_nopriv_tfree_get_recent_order', 'tfree_get_recent_order');

    function tfree_get_recent_order()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $orders = wc_get_orders(array(
            'limit' => 1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        if (!empty($orders)) {
            $order_id = $orders[0]->get_id();
            $order_url = admin_url('post.php?post=' . $order_id . '&action=edit');
        } else {
            $order_url = admin_url('edit.php?post_type=shop_order');
        }

        $response = array(
            'status' => 'success',
            'order_url' => $order_url
        );
        wp_send_json_success($response);
        wp_die();
    }

    add_action('wp_ajax_tfree_get_carriers', 'tfree_get_carriers');
    add_action('wp_ajax_nopriv_tfree_get_carriers', 'tfree_get_carriers');

    function tfree_get_carriers()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $search_term = isset($_REQUEST['search_term']) ? sanitize_text_field($_REQUEST['search_term']) : '';

        $json_data = file_get_contents( plugins_url('/trackfree-woocommerce-tracking/assets/js/trackfree_couriers.json'));
        $all_options = json_decode($json_data, true);
        $filtered_options = array_filter($all_options, function($option) use ($search_term) {
            return stripos($option['name'], $search_term) !== false;
        });
        $options = array_slice($filtered_options, 0, 10);
        $response = array(
            'status' => 'success',
            'carriers' => $options
        );
        wp_send_json_success($response);
        wp_die();
    }

    add_action( 'wp_ajax_tfree_dismiss_quick_start_info', 'tfree_dismiss_quick_start_info' );

    function tfree_dismiss_quick_start_info()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }
        add_option('trackfree_hide_quick_start_info');
        update_option('trackfree_hide_quick_start_info', 1);
        wp_die();
    }
}
?>
