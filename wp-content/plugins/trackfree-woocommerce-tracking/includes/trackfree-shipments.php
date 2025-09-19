<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('wp_ajax_tfree_get_order_url', 'tfree_get_order_url');
    add_action('wp_ajax_nopriv_tfree_get_order_url', 'tfree_get_order_url');
    function tfree_get_order_url()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $tf_post_data = file_get_contents('php://input');
        $request_data = json_decode($tf_post_data, true);
        if ($request_data['order_id']) {
            $order = wc_get_order($request_data['order_id']);
            if (is_callable([$order, 'get_edit_order_url'])) {
                $order_url = $order->get_edit_order_url();
                $response = [
                    'status' => 'success',
                    'order_url' => $order_url
                ];
                wp_send_json_success($response);
            } else {
                wp_send_json_error(array('status' => 'error'));
            }
        } else {
            wp_send_json_error(array('status' => 'error'));
        }
        wp_die();
    }

    add_action('wp_ajax_tfree_get_track_page_url', 'tfree_get_track_page_url');
    add_action('wp_ajax_nopriv_tfree_get_track_page_url', 'tfree_get_track_page_url');
    function tfree_get_track_page_url()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $tf_post_data = file_get_contents('php://input');
        $request_data = json_decode($tf_post_data, true);
        if ($request_data['tracking_num']) {
            $response = [
                'status' => 'success',
                'track_page_url' => trackfree_track_page_url($request_data['tracking_num'])
            ];
            wp_send_json_success($response);
        } else {
            wp_send_json_error(array('status' => 'error'));
        }
        wp_die();
    }
}
?>
