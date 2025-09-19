<?php
/*
Plugin Name: TrackFree – All-In-One WooCommerce Order Tracking
Plugin URI: https://trackfree.io/
Description: TrackFree is hassle-free automated shipment tracking plugin for WooCommerce.
Version: 3.1.4
Author: TrackFree
Author URI: https://trackfree.io
Text Domain: trackfree-woocommerce-tracking
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TRACKFREE_VERSION', '3.1.4' );

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    register_activation_hook(__FILE__, 'trackfree_activation');

    register_uninstall_hook(__FILE__, 'trackfree_uninstall');

    function trackfree_url() {
        return "https://trackfree.io";
    }

    add_action('admin_init', 'trackfree_admin_init');

    function trackfree_admin_init()
    {
        wp_register_style('trackfree-admin', plugins_url('/assets/css/trackfree.css', __FILE__), [], TRACKFREE_VERSION);

        if (isset($_GET['page']) && $_GET['page'] == 'trackfree-getting-started') {
            $trackfree_account_api_key = get_option('trackfree_account_api_key');
            $trackfree_account_verify  = get_option('trackfree_account_verify');
            if (($trackfree_account_api_key) && ($trackfree_account_verify == 1)) {
                header('Location: ' . admin_url('admin.php?page=trackfree'));
            }
        }
    }

    add_action( 'init', 'trackfree_init' );

    function trackfree_init()
    {
        wp_register_style('trackfree-codemirror', plugins_url('/assets/plugins/codemirror/theme/material-darker.css', __FILE__));

        wp_register_style('trackfree-user-track', plugins_url('/assets/css/tfree-user-track.css', __FILE__), [], TRACKFREE_VERSION);

        wp_register_script('trackfree-user-track', plugins_url('/assets/js/tfree-user-track.js', __FILE__), ['trackfree-leaflet'], TRACKFREE_VERSION);

        wp_register_style('trackfree-leaflet', plugins_url('/assets/plugins/leaflet/leaflet.css', __FILE__), [], '1.8.0');

        wp_register_script('trackfree-leaflet', plugins_url('/assets/plugins/leaflet/leaflet.js', __FILE__), [], '1.8.0');
    }

    add_action( 'admin_enqueue_scripts', 'trackfree_enqueue' );

    function trackfree_enqueue($hook)
    {
        $screen = get_current_screen();
        $screen_id = $screen ? $screen->id : '';

        wp_enqueue_style( 'trackfree-admin' );

        wp_enqueue_script('jquery');

        if ($hook === 'toplevel_page_trackfree') {
            wp_enqueue_style(
                'trackfree-react-app-css',
                plugin_dir_url(__FILE__) . 'dist/static/css/main.f7ca5023.css',
                array(),
                TRACKFREE_VERSION
            );

            wp_enqueue_script(
                'trackfree-react-app-js',
                plugin_dir_url(__FILE__) . 'dist/static/js/main.40207ebc.js',
                array(),
                TRACKFREE_VERSION,
                true
            );

            $trackfree_account_api_key = get_option('trackfree_account_api_key');

            wp_localize_script('trackfree-react-app-js', 'tfreePluginData', array(
                'trackfree_account_api_key' => $trackfree_account_api_key,
                'home_url' => home_url(),
                'plugins_url' => plugins_url(),
                'admin_url' => admin_url(),
                'trackfree_url' => trackfree_url(),
                'trackfree_api_url' => trackfree_url() . '/api/',
                'track_page_url' => trackfree_track_page_url(),
                'track_page_preview_url' => trackfree_track_page_url('', true),
                'tracking_domain_url' => get_option('trackfree_tracking_domain'),
                'preferred_couriers' => get_option('trackfree_preferred_couriers'),
                'hide_quick_start_info' => get_option('trackfree_hide_quick_start_info') == 1 ? true : false,
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('tfree_nonce'),
                'locale' => get_locale(),
            ));
        }

        if ($screen_id === 'toplevel_page_trackfree' || 'shop_order' === $screen->post_type || 'woocommerce_page_wc-orders' === $screen->base) {
            wp_enqueue_script('trackfree-admin-setting', plugins_url('/assets/js/trackfree-settings.js', __FILE__), ['jquery'], TRACKFREE_VERSION);
            wp_localize_script('trackfree-admin-setting', 'ajax_object',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'admin_url' => admin_url(),
                'plugins_url' => plugins_url(),
                'store_name' => get_bloginfo('name'),
                'admin_email' => get_option('admin_email'),
                'preferred_couriers' => get_option('trackfree_preferred_couriers'),
            ]);

            $cm_css_settings = wp_enqueue_code_editor(['type' => 'text/css']);
            $cm_html_settings = wp_enqueue_code_editor(['type' => 'text/html']);

            wp_localize_script('jquery', 'cm_css_settings', $cm_css_settings);
            wp_localize_script('jquery', 'cm_html_settings', $cm_html_settings);

            wp_enqueue_style('wp-codemirror');
            wp_enqueue_style('trackfree-codemirror');
        }

        $track_page_id = get_option('trackfree_track_page_id');

        if (!$track_page_id) {
            global $wpdb;
            $page_name  = 'trackfree';
            $shortcode  = '[tfree-track-page]';

            $page_info = get_post($track_page_id);

            if (!empty($page_info)) {
                if (false !== strpos($page_info->post_content, $shortcode)) {
                    return;
                }
            }

            $sql = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page'", $page_name);
            $page = $wpdb->get_var($sql);

            if ($page) {
                $page_det = get_post($page);
            }

            $page_id = $page_det->ID ?? 0;
            if (empty($page_id)) {
                $tf_page = [
                    'post_title'   => 'Track Order Status',
                    'post_name'    => $page_name,
                    'post_content' => $shortcode,
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_author'  => 1,
                ];
                $page_id = wp_insert_post($tf_page);
            }

            if ($track_page_id != $page_id) {
                update_option('trackfree_track_page_id', $page_id);
            }
        }

        if ((isset($_GET['page']) && in_array($_GET['page'],
        [
            'trackfree-getting-started',
            'trackfree'
        ]))) {
            add_action('admin_head', 'tfree_add_freshworks_widget_code');
        }
    }

    function tfree_add_freshworks_widget_code() {
        ?>
        <script type='text/javascript' src='https://widget.freshworks.com/widgets/153000002433.js' async defer></script>
        <script>
        window.fwSettings={
            'widget_id':153000002433
            };
            !function(){if("function"!=typeof window.FreshworksWidget){var n=function(){n.q.push(arguments)};n.q=[],window.FreshworksWidget=n}}()
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                FreshworksWidget('hide', 'launcher');
            });
        </script>
        <?php
    }

    add_action( 'activated_plugin', 'trackfree_plugin_activated' );

    function trackfree_plugin_activated($filename)
    {
        if ('/trackfree-woocommerce-tracking.php' !== substr( $filename, -35 )) {
            return;
        }

        global $wpdb;
        $page_name  = 'trackfree';
        $shortcode  = '[tfree-track-page]';

        $track_page_id = get_option('trackfree_track_page_id');
        $page_info = get_post($track_page_id);

        if (!empty($page_info)) {
            if (false !== strpos($page_info->post_content, $shortcode)) {
                return;
            }
        }

        $sql = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'page'", $page_name);
        $page = $wpdb->get_var($sql);

        if ($page) {
            $page_det = get_post($page);
        }

        $page_id = $page_det->ID ?? 0;
        if (empty($page_id)) {
            $tf_page = [
                'post_title'   => 'Track Order Status',
                'post_name'    => $page_name,
                'post_content' => $shortcode,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => 1,
            ];
            $page_id = wp_insert_post($tf_page);
        }

        if ($track_page_id != $page_id) {
            update_option('trackfree_track_page_id', $page_id);
        }
    }

    //Update the order status
    function trackfree_update_order_status($shipment_details, $order_id)
    {
        $statuses = [];
        foreach ($shipment_details as $shipment_values) {
            $statuses[] = $shipment_values['status'];
        }
        if (array_unique($statuses) === array('Delivered')) {
            $order = new WC_Order($order_id);
            if (!empty($order)) {
                $order->update_status('completed');
            }
        }
    }

    function trackfree_track_page_url($tracking_num = '', $preview = false)
    {
        $track_page_id = get_option('trackfree_track_page_id');
        $track_page_url = !empty( $track_page_id ) ? get_page_link($track_page_id) : 'Unknown';
        $separate = strpos($track_page_url, '?') ? '&' : '?';

        if ($preview) {
            return "{$track_page_url}{$separate}tracking_num=9405516901479381873433&preview=trackfree";
        }

        if ($tracking_num) {
            return "{$track_page_url}{$separate}tracking_num=$tracking_num";
        }
        return $track_page_url;
    }

    add_shortcode('tfree-track-page', 'trackfree_track_page_function');

    function trackfree_track_page_function()
    {
        wp_enqueue_style( 'trackfree-user-track' );
        wp_enqueue_script( 'trackfree-user-track' );
        wp_enqueue_style( 'trackfree-leaflet' );

        wp_localize_script('trackfree-user-track', 'ajax_object',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'admin_url' => admin_url(),
            'plugins_url' => plugins_url()
        ]);

        wc_get_template( 'user-tracking.php', [], '', plugin_dir_path( __FILE__ ) . 'templates/' );
        return ob_get_clean();
    }

    add_action('wp_ajax_get_shipment_data', 'get_shipment_data_action');
    add_action('wp_ajax_nopriv_get_shipment_data', 'get_shipment_data_action');

    function get_shipment_data_action()
    {
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $post_data = array(
            'tracking_number' => $_POST['tfree_tracking_number'] ? $_POST['tfree_tracking_number'] : '',
            'order_number' => $_POST['tfree_order_number'] ? $_POST['tfree_order_number'] : '',
            'email' => $_POST['tfree_email'] ? $_POST['tfree_email'] : '',
            'preview' => $_POST['preview'] ? $_POST['preview'] : ''
        );
        $response = wp_remote_post(trackfree_url() . '/api/wc_get_shipment_data?key=' . $trackfree_account_api_key,
        array(
            'sslverify' => false,
            'timeout' => 15,
            'body' => $post_data
            )
        );
        $response_data = json_decode( wp_remote_retrieve_body( $response ), true );

        $cont_width = 1200;
        $width_type = 'pixel';
        $pg_bar_color = '#017501';
        $by_ord_num_email = 1;
        $by_tk_num = 1;
        $show_cr_nm = 1;
        $show_track_num = 1;
        $show_track_info = 1;
        $show_product_info = 1;
        $show_map = 1;
        $show_rec_prds = 1;

        $cont_order = 'Order';
        $cont_shipment = 'Shipment';
        $cont_status = 'Status';
        $cont_carrier = 'Carrier';
        $cont_product = 'Product';
        $cont_tracking_number = 'Tracking Number';
        $cont_ordered = 'Ordered';
        $cont_order_ready = 'Order Ready';
        $cont_in_transit = 'In Transit';
        $cont_out_for_delivery = 'Out for Delivery';
        $cont_delivered = 'Delivered';
        $cont_may_like = 'You may also like...';
        $cont_order_not_found = 'Order Not Found';

        $date_format = 1;
        $time_format = 1;

        $tf_dis_opt = get_option('trackfree_display_options');

        if ($tf_dis_opt) {
            $cont_width = isset($tf_dis_opt['cont_width']) ? $tf_dis_opt['cont_width'] : 1200;
            $width_type = isset($tf_dis_opt['width_type']) ? $tf_dis_opt['width_type'] : 'pixel';
            $pg_bar_color = isset($tf_dis_opt['pg_bar_color']) ? $tf_dis_opt['pg_bar_color'] : '#017501';
            $by_ord_num_email = isset($tf_dis_opt['by_ord_num_email']) ? $tf_dis_opt['by_ord_num_email'] : 1;
            $by_tk_num = isset($tf_dis_opt['by_tk_num']) ? $tf_dis_opt['by_tk_num'] : 1;
            $show_cr_nm = isset($tf_dis_opt['show_cr_nm']) ? $tf_dis_opt['show_cr_nm'] : 1;
            $show_track_num = isset($tf_dis_opt['show_track_num']) ?  $tf_dis_opt['show_track_num'] : 1;
            $show_track_info = isset($tf_dis_opt['show_track_info']) ? $tf_dis_opt['show_track_info'] : 1;
            $show_product_info = isset($tf_dis_opt['show_product_info']) ? $tf_dis_opt['show_product_info'] : 1;
            $show_map = isset($tf_dis_opt['show_map']) ? $tf_dis_opt['show_map'] : '';
            $show_rec_prds = isset($tf_dis_opt['show_rec_prds']) ? $tf_dis_opt['show_rec_prds'] : 1;
        }

        $tf_tns_str = get_option('trackfree_trans_strings');

        if ($tf_tns_str) {
            $cont_order = isset($tf_tns_str['order']) ? $tf_tns_str['order'] : 'Order';
            $cont_shipment = isset($tf_tns_str['shipment']) ? $tf_tns_str['shipment'] : 'Shipment';
            $cont_status = isset($tf_tns_str['status']) ? $tf_tns_str['status'] : 'Status';
            $cont_carrier = isset($tf_tns_str['carrier']) ? $tf_tns_str['carrier'] : 'Carrier';
            $cont_product = isset($tf_tns_str['product']) ? $tf_tns_str['product'] : 'Product';
            $cont_tracking_number = isset($tf_tns_str['tracking_number']) ? $tf_tns_str['tracking_number'] : 'Tracking Number';
            $cont_ordered = isset($tf_tns_str['ordered']) ? $tf_tns_str['ordered'] : 'Ordered';
            $cont_order_ready = isset($tf_tns_str['order_ready']) ? $tf_tns_str['order_ready'] : 'Order Ready';
            $cont_in_transit = isset($tf_tns_str['in_transit']) ? $tf_tns_str['in_transit'] : 'In Transit';
            $cont_out_for_delivery = isset($tf_tns_str['out_for_delivery']) ? $tf_tns_str['out_for_delivery'] : 'Out for Delivery';
            $cont_delivered = isset($tf_tns_str['delivered']) ? $tf_tns_str['delivered'] : 'Delivered';
            $cont_may_like = isset($tf_tns_str['may_like']) ? $tf_tns_str['may_like'] : 'You may also like...';
            $cont_order_not_found = isset($tf_tns_str['order_not_found']) ? $tf_tns_str['order_not_found'] : 'Order Not Found';
        }

        $tf_dtm_fms = get_option('trackfree_date_time_formats');

        if ($tf_dtm_fms) {
            $date_format = isset($tf_dtm_fms['date_format']) ? $tf_dtm_fms['date_format'] : 1;
            $time_format = isset($tf_dtm_fms['time_format']) ? $tf_dtm_fms['time_format'] : 1;
        }

        $tracking_settings = array(
            'cont_width' => $cont_width,
            'width_type' => $width_type,
            'pg_bar_color' => $pg_bar_color,
            'by_ord_num_email' => $by_ord_num_email,
            'by_tk_num' => $by_tk_num,
            'show_cr_nm' => $show_cr_nm,
            'show_track_num' => $show_track_num,
            'show_track_info' => $show_track_info,
            'show_product_info' => $show_product_info,
            'show_map' => $show_map,
            'show_rec_prds' => $show_rec_prds,
            'cont_order' => $cont_order,
            'cont_shipment' => $cont_shipment,
            'cont_status' => $cont_status,
            'cont_carrier' => $cont_carrier,
            'cont_product' => $cont_product,
            'cont_tracking_number' => $cont_tracking_number,
            'cont_ordered' => $cont_ordered,
            'cont_order_ready' => $cont_order_ready,
            'cont_in_transit' => $cont_in_transit,
            'cont_out_for_delivery' => $cont_out_for_delivery,
            'cont_delivered' => $cont_delivered,
            'cont_may_like' => $cont_may_like,
            'cont_order_not_found' => $cont_order_not_found,
            'date_format' => $date_format,
            'time_format' => $time_format,
        );

        if ($response_data['status'] == 'success' && isset($response_data['multi_tracking'])) {
            $order_id = $response_data['order_id'];
            $recommend_products = $order_id ? get_recommend_products($order_id) : [];
            show_multi_tracking_details($order_id, $response_data['data'], $tracking_settings, $recommend_products);
        } else if ($response_data['status'] == 'success') {
            $order_id = $response_data['data']['orderId'];
            $recommend_products = $order_id ? get_recommend_products($order_id) : [];
            show_tracking_details($response_data['data'], $tracking_settings, $recommend_products);
        } else {
            $order_id = $_POST['tfree_order_number'];
            $order = wc_get_order($order_id);
            if (!empty($order)) {
                $order_data = $order->get_data();
                if ($order_data['billing']['email'] == $_POST['tfree_email']) {
                    $recommend_products = $order_id ? get_recommend_products($order_id) : [];
                    show_trackfree_order_details($order_data, $tracking_settings, $recommend_products);
                } else {
                    echo '<div style="margin: 32px"><h1>'. $tracking_settings['cont_order_not_found'] . '</h1></div>';
                }
            } else {
                echo '<div style="margin: 32px"><h1>'. $tracking_settings['cont_order_not_found'] . '</h1></div>';
            }
        }
        wp_die();
    }

    add_action( 'rest_api_init', 'trackfree_auto_coupon_generate' );

    function trackfree_auto_coupon_generate() {
        register_rest_route( 'trackfree-wc/v1', 'coupon-generate', array(
            'methods' => 'POST',
            'callback' => 'trackfree_create_auto_coupon',
            'permission_callback' => '__return_true'
        ));
    }

    function trackfree_create_auto_coupon($data)
    {
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $request_api_key = $data['api_key'];
        $coupon_code = $data['coupon_code'];

        if ($trackfree_account_api_key == $request_api_key) {
            $coupon_amount = $data['coupon_amount'];
            $discount_type = $data['coupon_type'];
            $coupon_expires = $data['coupon_expires'];
            $min_order_amount = $data['min_order_amount'];
            $email = $data['email'];

            $coupon = new WC_Coupon();
            $coupon->set_code( $coupon_code );
            $coupon->set_discount_type( $discount_type );
            $coupon->set_amount( $coupon_amount );
            $coupon->set_description( 'TrackFree promotional coupon' );

            $expire_date = '';
            if ($coupon_expires > 0) {
                $date_expires = date('Y-m-d', strtotime("+$coupon_expires days"));
                $coupon->set_date_expires( $date_expires );
            }

            $coupon->set_usage_limit( 1 );
            $coupon->set_usage_limit_per_user( 1 );

            $coupon->set_email_restrictions( $email );

            if ($min_order_amount > 0) {
                 $coupon->set_minimum_amount( $min_order_amount );
            }
            $coupon->save();
            return 'success';
        }
    }

    add_action( 'rest_api_init', 'trackfree_get_order_data' );

    function trackfree_get_order_data() {
        register_rest_route( 'trackfree-wc/v1', 'get-order-data/(?P<order_id>\d+)', array(
            'methods' => 'POST',
            'callback' => 'get_trackfree_order_details',
            'permission_callback' => '__return_true'
        ));
    }

    function get_trackfree_order_details($data)
    {
        $order_id = $data['order_id'];
        $request_api_key = $data['api_key'];
        $trackfree_account_api_key = get_option('trackfree_account_api_key');

        if ($trackfree_account_api_key == $request_api_key) {
            $order = wc_get_order($order_id);
            $order_data = $order->get_data();

            if ($order_data['billing']['email'] == $data['email']) {
                $first_name = $order_data['shipping']['first_name'] ? $order_data['shipping']['first_name'] : $order_data['billing']['first_name'];
                $last_name = $order_data['shipping']['last_name'] ? $order_data['shipping']['last_name'] : $order_data['billing']['last_name'];

                //Refund
                $refund_amount = 0;
                $order_refunds = $order->get_refunds();
                if ($order_refunds) {
                    foreach ($order_refunds as $refund) {
                        $refund_amount += $refund->amount;
                    }
                }

                $order_items = array();

                foreach ($order->get_items() as $item_key => $item_values) {
                    $item_data = $item_values->get_data();
                    $product_data = wc_get_product($item_data['product_id']);

                    if (!empty($product_data)) {
                        $product_attribute_name = '';
                        $variation_image_url = '';
                        if ($item_data['variation_id']) {
                            $product = new WC_Product_Variable($item_data['product_id']);
                            $variations = $product->get_available_variations();
                            foreach ($variations as $variation) {
                                if ($variation['variation_id'] == $item_data['variation_id']) {
                                    $variation_image_url = $variation['image']['url'];
                                    if ($variation['attributes']) {
                                        foreach ($variation['attributes'] as $attribute_name => $attribute_val) {
                                            $product_attribute_name .=  '&' . $attribute_name . '=' . $attribute_val;
                                        }
                                    }
                                }
                            }
                        }

                        $order_items[]  = array(
                            'product_id' => $item_data['product_id'],
                            'variation_id' => $item_data['variation_id'],
                            'name' => $item_data['name'],
                            'price' => wc_format_decimal($item_data['subtotal'] / $item_data['quantity'], 2),
                            'sku' => $product_data->get_sku(),
                            'quantity' => $item_data['quantity'],
                            'slug' => $product_data->get_slug() . $product_attribute_name,
                            'line_total' => wc_format_decimal($item_data['subtotal'], 2),
                            'image' => $variation_image_url ? $variation_image_url : get_the_post_thumbnail_url($product_data->get_id(), 'full')
                        );
                    }
                }

                $shipping_address = array(
                    'shipping_first_name' => $order_data['shipping']['first_name'],
                    'shipping_last_name' => $order_data['shipping']['last_name'],
                    'shipping_company' => $order_data['shipping']['company'],
                    'shipping_address_1' => $order_data['shipping']['address_1'],
                    'shipping_address_2' => $order_data['shipping']['address_2'],
                    'shipping_city' => $order_data['shipping']['city'],
                    'shipping_state' => $order_data['shipping']['state'],
                    'shipping_postcode' => $order_data['shipping']['postcode'],
                    'shipping_country' => $order_data['shipping']['country']
                );

                //Order fee
                $order_fee = array();
                foreach ($order->get_fees() as $fee_id => $fee) {
                    if ($fee['name']) {
                        $order_fee[] = array(
                            'name' => $fee['name'],
                            'total' => $fee['total']
                        );
                    }
                }

                $coupon_details = array();
                $order_coupon_items = $order->get_items('coupon');
                foreach( $order_coupon_items as $item_id => $item ) {
                    $coupon_data = $item->get_data();
                    $coupon_details[] = array(
                        'coupon_code' => $coupon_data['code'],
                        'coupon_amount' => $coupon_data['discount']
                    );
                }

                $order_notes = wc_get_order_notes([
                    'order_id' => $order_data['id']
                ]);

                $order_details = array(
                    'key' => $trackfree_account_api_key,
                    'order_id' => $order_data['id'],
                    'order_number' => $order_data['number'],
                    'order_created_at' => $order_data['date_created'] ? $order_data['date_created']->date('Y-m-d H:i:s') : '',
                    'order_status' => $order_data['status'],
                    'order_currency' => $order_data['currency'],
                    'order_total' => wc_format_decimal($order_data['total'], 2),
                    'payment_method' => $order_data['payment_method_title'],
                    'transaction_id' => $order_data['transaction_id'] ? $order_data['transaction_id'] : '',
                    'customer_name' => $last_name ? $first_name . ' ' . $last_name : $first_name,
                    'customer_email' => $order_data['billing']['email'],
                    'customer_phone' => $order_data['billing']['phone'],
                    'customer_country' => $order_data['billing']['country'],
                    'order_items' => $order_items,
                    'site_url' => home_url(),
                    'shipping_address' => $shipping_address,
                    'shipping_method' => $order->get_shipping_method(),
                    'sub_total' => wc_format_decimal($order->get_subtotal(), 2),
                    'total_discount' => wc_format_decimal($order->get_total_discount(), 2),
                    'total_shipping' => wc_format_decimal($order->get_total_shipping(), 2),
                    'total_tax' =>  wc_format_decimal($order->get_total_tax(), 2),
                    'refund_total' => wc_format_decimal($refund_amount, 2),
                    'shipment_details' => get_post_meta($order_id, '_trackfree_shipment_details', true),
                    'customer_note' => $order_notes,
                    'order_fee' => $order_fee,
                    'coupon_details' => $coupon_details
                );

                return array(
                    'status' => 'success',
                    'order_details' => $order_details
                );
            }
        }
    }

    add_action( 'rest_api_init', 'trackfree_updating_tracking' );

    function trackfree_updating_tracking() {
        register_rest_route( 'trackfree-wc/v1', 'order/(?P<order_id>\d+)', array(
            'methods' => 'POST',
            'callback' => 'update_trackfree_tracking_details',
            'permission_callback' => '__return_true'
        ));
    }

    function update_trackfree_tracking_details($data)
    {
        $order_id = $data['order_id'];
        $request_api_key = $data['api_key'];
        $tracking_number = $data['tracking_number'];
        $tracking_status = $data['tracking_status'];
        $estimated_delivery = $data['estimated_delivery'];
        $delivered_date = $data['delivered_date'];

        $shipment_details = get_post_meta($order_id, '_trackfree_shipment_details', true);
        $trackfree_account_api_key = get_option('trackfree_account_api_key');

        if ($trackfree_account_api_key == $request_api_key) {
            if ($shipment_details) {
                $track_details = array();
                foreach ($shipment_details as $key => $shipment_values) {
                    if ($shipment_values['tracking_num'] == $tracking_number) {
                        $track_details[$key] = array(
                            'tracking_num' => $shipment_values['tracking_num'],
                            'courier_name' => $shipment_values['courier_name'],
                            'status' => $tracking_status ? $tracking_status : $shipment_values['status'],
                            'estimated_delivery' => $estimated_delivery,
                            'delivered_date' => $delivered_date,
                            'fulfilment_items' => $shipment_values['fulfilment_items']
                        );
                    } else {
                        $track_details[$key] = array(
                            'tracking_num' => $shipment_values['tracking_num'],
                            'courier_name' => $shipment_values['courier_name'],
                            'status' => $shipment_values['status'],
                            'estimated_delivery' => $shipment_values['estimated_delivery'],
                            'delivered_date' => $shipment_values['delivered_date'],
                            'fulfilment_items' => $shipment_values['fulfilment_items']
                        );
                    }
                }

                update_post_meta($order_id, '_trackfree_shipment_details', wc_clean($track_details));

                if (get_option('trackfree_auto_order_status_update') == 1) {
                    trackfree_update_order_status($track_details, $order_id);
                }
            }
        }
    }

    function trackfree_activation()
    {
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        if ($trackfree_account_api_key) {
            wp_remote_post(trackfree_url() . '/api/wc_plugin_activation?key=' . $trackfree_account_api_key,
                array(
                    'sslverify' => false,
                    'timeout' => 15
                )
            );
        }
    }

    function trackfree_uninstall()
    {
        wp_remote_post(trackfree_url() . '/api/wc_plugin_uninstall?key=' . get_option('trackfree_account_api_key'),
            array(
                'sslverify' => false,
                'timeout' => 15
            )
        );
        delete_option('trackfree_account_api_key');
        delete_option('trackfree_account_verify');
        delete_option('trackfree_preferred_couriers');
        delete_option('trackfree_tracking_domain');
        delete_option('trackfree_shipment_status_in_orders');
        delete_option('trackfree_shipment_details_in_order_details');
        delete_option('trackfree_auto_order_status_update');
    }

    function trackfree_load_plugin_textdomain() {
        load_plugin_textdomain( 'trackfree-woocommerce-tracking', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    add_action( 'plugins_loaded', 'trackfree_load_plugin_textdomain' );

    include_once('settings.php');
    include_once('includes/trackfree-home.php');
    include_once('includes/trackfree-tracking-page.php');
    include_once('includes/trackfree-shipments.php');
    include_once('includes/tf-order-details-page.php');
    include_once('includes/trackfree-shipment-overview.php');
    include_once('includes/trackfree-shipment-details.php');
    include_once('includes/trackfree-wc-order-summary.php');
    include_once('includes/tf-tracking-details.php');

    if ((isset($_GET['page']) && in_array($_GET['page'],
    [
        'trackfree-getting-started',
        'trackfree'
    ]))) {
        add_filter('admin_footer_text', 'admin_footer_text_action', 1);
        function admin_footer_text_action()
        {
            return 'Thank you for using TrackFree!';
        }
    }

    add_filter('admin_body_class', 'trackfree_admin_body_classes');

    function trackfree_admin_body_classes($classes)
    {
        if ((isset($_GET['page']) && in_array($_GET['page'],
        [
            'trackfree-getting-started',
            'trackfree'
        ]))) {
            $classes .= ' trackfree-admin-body trackfree-admin-wrap';
        }
        return $classes;
    }

    add_action('admin_menu', 'trackfree_admin_menus');

    function trackfree_admin_menus()
    {
        $trackfree_icon = '<svg width="83" height="75" viewBox="0 0 83 75" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M42.3221 6.00889C39.0566 5.93791 35.8977 6.29284 32.8807 7.03821L32.8686 7.07354C18.1216 10.7281 6.98279 23.6116 6.06174 39.4193C6.03844 39.7951 6.02205 40.1713 6.01357 40.5475L6.01179 40.6154L6.01049 40.6796C5.85557 46.9769 7.42037 53.2094 10.4699 58.7015C10.6582 59.0409 10.8522 59.3775 11.0519 59.711C13.7317 64.1478 18.2838 66.4194 22.8314 66.4726C22.9735 66.4743 23.1156 66.4738 23.2577 66.4711C27.3129 66.395 31.3179 64.5519 33.9402 60.9042C29.5982 53.5706 24.6775 44.1526 24.6775 39.1365C24.6775 30.0502 32.0652 22.6584 41.1451 22.6584L41.1452 22.6585C50.2256 22.6585 57.6127 30.0508 57.6105 39.1343C57.6105 44.0346 52.9118 53.1389 48.646 60.395C54.0089 68.7618 66.4242 68.5458 71.6753 60.1015C73.6431 56.9243 75.09 53.4631 75.9682 49.8545L76.0057 49.8437C76.6446 47.2172 76.964 44.5197 76.9998 41.7866C77.0173 36.9802 76.0752 32.382 74.3534 28.1781C69.1458 15.4629 56.8064 6.35566 42.3221 6.00889ZM33.7422 39.1359C33.7422 43.2197 37.0641 46.5436 41.1451 46.5436C45.2285 46.5436 48.5502 43.22 48.548 39.1342C48.548 35.0504 45.2283 31.7266 41.1451 31.7266C37.0639 31.7266 33.7422 35.0501 33.7422 39.1359Z" fill="url(#paint0_linear)"/><defs><linearGradient id="paint0_linear" x1="35.0196" y1="62.6697" x2="3.94619" y2="36.4729" gradientUnits="userSpaceOnUse"><stop stop-color="#EDF3FB"/><stop offset="1" stop-color="#EDF3FB"/></linearGradient></defs></svg>';

        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $trackfree_account_verify  = get_option('trackfree_account_verify');
        if (($trackfree_account_api_key) && ($trackfree_account_verify == 1)) {
            add_menu_page(
                'TrackFree',
                'TrackFree',
                'manage_options',
                'trackfree',
                'trackfree_admin_render_page',
                'data:image/svg+xml;base64,' . base64_encode($trackfree_icon),
                56
            );

            add_submenu_page('trackfree', 'Home - TrackFree', 'Home', 'manage_options', 'trackfree#/home', 'trackfree_admin_render_page');
            add_submenu_page('trackfree', 'Tracking Page - TrackFree', 'Tracking Page', 'manage_options', 'trackfree#/tracking-page', 'trackfree_admin_render_page');
            add_submenu_page('trackfree', 'Shipments - TrackFree', 'Shipments', 'manage_options', 'trackfree#/shipments', 'trackfree_admin_render_page');
            add_submenu_page('trackfree', 'Settings - TrackFree', 'Settings', 'manage_options', 'trackfree#/settings', 'trackfree_admin_render_page');
            add_submenu_page('trackfree', 'Analytics - TrackFree', 'Analytics', 'manage_options', 'trackfree#/analytics', 'trackfree_admin_render_page');
            add_submenu_page('trackfree', 'Account - TrackFree', 'Billing', 'manage_options', 'trackfree#/account', 'trackfree_admin_render_page');

            remove_submenu_page('trackfree', 'trackfree');
        } else {
            add_menu_page('TrackFree', 'TrackFree', 'manage_options', 'trackfree-getting-started', 'trackfree_settings_page', 'data:image/svg+xml;base64,' . base64_encode($trackfree_icon), 56);
        }
    }

    function trackfree_admin_render_page() {
        echo '<div id="root"></div>';
    }

    function trackfree_get_category_by_id($category_id) {
        $term = get_term_by('id', $category_id, 'product_cat', 'ARRAY_A' );
        return $term['name'];
    }

    add_action('wp_ajax_tfree_update_shipment', 'tfree_update_shipment_action');
    function tfree_update_shipment_action()
    {
        $user_id = get_current_user_id();
        if (current_user_can('edit_user', $user_id)) {
            $trackfree_account_api_key = get_option('trackfree_account_api_key');
            $ord_id = $_POST['ord_id'];
            $shipment_id = $_POST['shipment_id'];
            $shipment_details = get_post_meta($ord_id, '_trackfree_shipment_details', true);
            $shipment_values = $shipment_details[$shipment_id];
            $is_tracking_modified = 0;
            $existing_tracking_num = $shipment_values['tracking_num'];
            $existing_courier_name = $shipment_values['courier_name'];
            $tracking_num = $_POST['trackfree_tracking_number'];
            $courier_name = $_POST['trackfree_courier_name'];

            if ($existing_tracking_num != $tracking_num || $existing_courier_name != $courier_name) {
                $is_tracking_modified = 1;
            }

            $fulfilment_items = $shipment_values['fulfilment_items'];
            $updated_fulfilment_items = $_POST['fulfilment_items'];
            foreach($fulfilment_items as $key => $item) {
                $qty = intval($updated_fulfilment_items[$key]['quantity']);
                $total_qty = intval($updated_fulfilment_items[$key]['total_quantity']);
                if ($qty < 0) {
                    $qty = 0;
                } else if ($qty > $total_qty) {
                    $qty = $total_qty;
                }
                $fulfilment_items[$key]['quantity'] = $qty;
            }

            if ($_POST['trackfree_order_status']) {
                $order = wc_get_order($ord_id);
                $order->update_status(str_replace('wc-', '', $_POST['trackfree_order_status']));
            }

            $track_request = array(
                'key' => $trackfree_account_api_key,
                'order_id' => $ord_id,
                'shipment_id' => $shipment_id,
                'fulfilment_items' => $shipment_details,
                'is_tracking_modified' => $is_tracking_modified,
                'tracking_num' => $tracking_num,
                'courier_name' => $courier_name,
                'existing_tracking_num' => $existing_tracking_num,
                'existing_courier_name' => $existing_courier_name
            );

            $response_data = wp_remote_get(trackfree_url() . '/api/wc_update_tracking', array(
                'sslverify' => false,
                'timeout' => 15,
                'body' => $track_request
            ));
            if ($is_tracking_modified == 1) {
                $track_data = json_decode(wp_remote_retrieve_body($response_data), true);
                if ($track_data['status'] == 'success') {
                    $tfCourierName = sanitize_text_field($_POST['trackfree_courier_name']);
                    if (!$tfCourierName) {
                        $tfCourierName = $track_data['courier'];
                    }
                    $shipment_details[$shipment_id]['tracking_num'] = $tracking_num;
                    $shipment_details[$shipment_id]['courier_name'] = $tfCourierName;
                    $shipment_details[$shipment_id]['status'] = '';
                    $shipment_details[$shipment_id]['estimated_delivery'] = '';
                    $shipment_details[$shipment_id]['delivered_date'] = '';
                    $shipment_details[$shipment_id]['fulfilment_items'] = $fulfilment_items;
                    update_post_meta($ord_id, '_trackfree_shipment_details', wc_clean($shipment_details));
                    if (get_option('trackfree_auto_order_status_update') == 1) {
                        trackfree_update_order_status($shipment_details, $ord_id);
                    }
                    echo 'success';
                } else {
                    if ($track_data['message'] == 'already_exist') {
                        _e('This tracking number already exists', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'limit_exceed') {
                        _e('You can add only up to 3 shipments', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'invalid_user') {
                        _e('Invalid user', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'credit_exceed') {
                        _e('Your credit limit is over. Please upgrade', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'monthly_credit_exceed') {
                        $upgrade_url = admin_url('admin.php?page=trackfree#/account');
                        printf(
                            __('You have exceeded your monthly credit usage. Upgrade to a higher plan to continue using TrackFree. You can upgrade <a href="%s" target="_blank">here</a>.', 'trackfree-woocommerce-tracking'),
                            esc_url($upgrade_url)
                        );
                    } else if ($track_data['message'] == 'invalid_tracking') {
                        _e('Invalid tracking number', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'courier_not_supported') {
                        _e('Courier not supported', 'trackfree-woocommerce-tracking');
                    } else {
                        echo $track_data['message'];
                    }
                }
            } else {
                $shipment_details[$shipment_id]['fulfilment_items'] = $fulfilment_items;
                update_post_meta($ord_id, '_trackfree_shipment_details', wc_clean($shipment_details));
                if (get_option('trackfree_auto_order_status_update') == 1) {
                    trackfree_update_order_status($shipment_details, $ord_id);
                }
                echo 'success';
            }
        }
        wp_die();
    }

    add_action('wp_ajax_add_new_shipment', 'add_new_shipment_action');
    function add_new_shipment_action()
    {
        $user_id = get_current_user_id();
        if (current_user_can('edit_user', $user_id)) {
            $fulfil_items = $_POST['fulfilment_items'];
            $fulfilment_items = [];
            foreach ($fulfil_items as $key => $item) {
                $fulfilmentItemsJson = stripslashes($fulfil_items[$key]['order_items']);
                $fulfillment = json_decode($fulfilmentItemsJson, true);
                $availableQty = $fulfillment['quantity'];
                $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
                if ($quantity < 0) {
                    $quantity = 0;
                } else if ($quantity > $availableQty) {
                    $quantity = $availableQty;
                }
                $fulfil_items[$key]['quantity'] = $quantity;
                $fulfilment_items[] = [
                    'id' => isset($item['id']) ? sanitize_text_field($item['id']) : '',
                    'variation_id' => isset($item['variation_id']) ? sanitize_text_field($item['variation_id']) : '',
                    'quantity' => $quantity
                ];
            }

            $plugin_data = get_plugin_data(dirname(__FILE__) . '/trackfree-woocommerce-tracking.php');
            $plugin_version = $plugin_data['Version'];

            $trackfree_account_api_key = get_option('trackfree_account_api_key');
            $ord_id = $_POST['ord_id'];
            $trackfree_courier_name = sanitize_text_field($_POST['trackfree_courier_name']);
            $trackfree_tracking_number = sanitize_text_field(preg_replace('/\s+|[^a-zA-Z0-9\-]/', '', $_POST['trackfree_tracking_number']));
            if ($trackfree_tracking_number) {
                $order = wc_get_order($ord_id);
                $order_data = $order->get_data();
                $first_name = $order_data['shipping']['first_name'] ? $order_data['shipping']['first_name'] : $order_data['billing']['first_name'];
                $last_name = $order_data['shipping']['last_name'] ? $order_data['shipping']['last_name'] : $order_data['billing']['last_name'];

                $order_items  = array();
                $exclude_item = array();
                $categories = array();

                foreach ($order->get_items() as $item_key => $item_values) {
                    $item_data = $item_values->get_data();
                    $product_data = wc_get_product($item_data['product_id']);
                    if (!empty($product_data)) {
                        $order_items[]  = array(
                            'product_id' => $item_data['product_id'],
                            'name' => $product_data->get_name(),
                            'price' => $product_data->get_price(),
                            'sku' => $product_data->get_sku(),
                            'quantity' => $item_data['quantity'],
                            'slug' => $product_data->get_slug(),
                            'image' => get_the_post_thumbnail_url($product_data->get_id(), 'full')
                        );

                        $exclude_item[] = $item_data['product_id'];

                        if ($product_data->category_ids) {
                            foreach ($product_data->category_ids as $category) {
                                $categories[] = trackfree_get_category_by_id($category);
                            }
                        }
                    }
                }

                //Get products by category
                $args = array(
                    'status' => 'publish',
                    'orderby' => 'rand',
                    'limit' => 12,
                    'return' => 'ids',
                    'exclude' => $exclude_item,
                );

                if ($categories) {
                    $args['category'] = $categories;
                }

                $storeProducts = wc_get_products($args);

                //Get products without category
                if (sizeof($storeProducts) < 4) {
                    $args = array(
                        'status' => 'publish',
                        'orderby' => 'rand',
                        'limit' => 12,
                        'return' => 'ids',
                        'exclude' => $exclude_item,
                    );
                    $storeProducts = wc_get_products($args);
                }

                $recommendProducts = array();
                foreach ($storeProducts as $product_id) {
                    $product = wc_get_product($product_id);
                    if (!empty($product)) {
                        $image_url = get_the_post_thumbnail_url($product->get_id(), 'full');

                        if ($image_url) {
                            $recommendProducts[] = array(
                                'product_id' => $product->get_id(),
                                'name' => $product->get_name(),
                                'slug' => $product->get_slug(),
                                'price' => $product->get_price(),
                                'sku' => $product->get_sku(),
                                'image' => $image_url,
                            );

                            if (count($recommendProducts) === 4) {
                                break;
                            }
                        }
                    }
                }

                $track_request = array(
                    'key' => $trackfree_account_api_key,
                    'tracking_num' => $trackfree_tracking_number,
                    'courier_name' => $trackfree_courier_name,
                    'order_id' => $order_data['id'],
                    'order_number' => $order_data['number'],
                    'customer_email' => $order_data['billing']['email'],
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'customer_phone' => $order_data['billing']['phone'],
                    'customer_country' => $order_data['billing']['country'],
                    'order_currency' => $order_data['currency'],
                    'order_total' => $order_data['total'],
                    'order_created_at' => $order_data['date_created'] ? $order_data['date_created']->date('Y-m-d H:i:s') : '',
                    'shipping_address_1' => $order_data['shipping']['address_1'],
                    'shipping_address_2' => $order_data['shipping']['address_2'],
                    'shipping_city' => $order_data['shipping']['city'],
                    'shipping_state' => $order_data['shipping']['state'],
                    'shipping_postcode' => $order_data['shipping']['postcode'],
                    'shipping_country' => $order_data['shipping']['country'],
                    'order_items' => $order_items,
                    'site_url' => home_url(),
                    'recommendProducts' => $recommendProducts,
                    'plugin_version' => $plugin_version,
                    'shipment_details' => get_post_meta($ord_id, '_trackfree_shipment_details', true),
                    'fulfilment_items' => $fulfil_items
                );

                $response_data = wp_remote_get(trackfree_url() . '/api/wc_add_new_track', array(
                    'sslverify' => false,
                    'timeout' => 15,
                    'body' => $track_request
                ));
                $track_data = json_decode(wp_remote_retrieve_body($response_data), true);
                if ($track_data['status'] == 'success') {
                    $tfCourierName = sanitize_text_field($_POST['trackfree_courier_name']);
                    if (!$tfCourierName) {
                        $tfCourierName = $track_data['courier'];
                    }
                    $new_shipment[] = array(
                        'tracking_num' => $trackfree_tracking_number,
                        'courier_name' => $tfCourierName,
                        'status' => '',
                        'estimated_delivery' => '',
                        'delivered_date' => '',
                        'fulfilment_items' => $fulfilment_items
                    );
                    $existing_shipments = get_post_meta($ord_id, '_trackfree_shipment_details', true);
                    if ($existing_shipments) {
                        $shipments = array_merge($existing_shipments, $new_shipment);
                        update_post_meta($ord_id, '_trackfree_shipment_details', wc_clean($shipments));
                        if (get_option('trackfree_auto_order_status_update') == 1) {
                            trackfree_update_order_status($shipments, $ord_id);
                        }
                    } else {
                        update_post_meta($ord_id, '_trackfree_shipment_details', wc_clean($new_shipment));
                        if (get_option('trackfree_auto_order_status_update') == 1) {
                            trackfree_update_order_status($new_shipment, $ord_id);
                        }
                    }

                    if ($_POST['trackfree_order_status']) {
                        $order->update_status(str_replace('wc-', '', $_POST['trackfree_order_status']));
                    }
                    echo 'success';
                } else {
                    if ($track_data['message'] == 'already_exist') {
                        _e('This tracking number already exists', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'limit_exceed') {
                        _e('You can add only up to 3 shipments', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'invalid_user') {
                        _e('Invalid user', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'credit_exceed') {
                        _e('Your credit limit is over. Please upgrade', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'monthly_credit_exceed') {
                        $upgrade_url = admin_url('admin.php?page=trackfree#/account');
                        printf(
                            __('You have exceeded your monthly credit usage. Upgrade to a higher plan to continue using TrackFree. You can upgrade <a href="%s" target="_blank">here</a>.', 'trackfree-woocommerce-tracking'),
                            esc_url($upgrade_url)
                        );
                    } else if ($track_data['message'] == 'invalid_tracking') {
                        _e('Invalid tracking number', 'trackfree-woocommerce-tracking');
                    } else if ($track_data['message'] == 'courier_not_supported') {
                        _e('Courier not supported', 'trackfree-woocommerce-tracking');
                    } else {
                        echo $track_data['message'];
                    }
                }
            }
        }
        wp_die();
    }

    add_action('add_meta_boxes', 'trackfree_meta_box_add');

    //Show shipment status in order list page
    if (get_option('trackfree_shipment_status_in_orders') == 1) {
        //New version S
        add_filter('manage_woocommerce_page_wc-orders_columns', 'trackfree_wc_order_show_custom_column');

        function trackfree_wc_order_show_custom_column($columns)
        {
            $new_columns = array();
            foreach ($columns as $key => $column) {
                $new_columns[$key] = $column;
                if ($key == 'order_status') {
                    $new_columns['shipment_status'] = __('Shipment Status', 'trackfree-woocommerce-tracking');
                }
            }
            return $new_columns;
        }

        //New version E
        add_filter('manage_edit-shop_order_columns', 'show_shipment_status', 20);
        function show_shipment_status($columns)
        {
            $new_columns = array();
            foreach ($columns as $key => $column) {
                $new_columns[$key] = $column;
                if ($key == 'order_status') {
                    $new_columns['shipment_status'] = __('Shipment Status', 'trackfree-woocommerce-tracking');
                }
            }
            return $new_columns;
        }
    }

    if (get_option('trackfree_shipment_status_in_orders') == 1) {
        //New version S
        add_action('manage_woocommerce_page_wc-orders_custom_column', 'trackfree_wc_order_list_custom_column', 10, 2);

        function trackfree_wc_order_list_custom_column($column, $order) {
            if ('shipment_status' === $column) {
                $post_id = $order->get_id();
                $shipment_details = get_post_meta($post_id, '_trackfree_shipment_details', true);
                $i = 0;
                if ($shipment_details) {
                    if (checkShipmentStatus($shipment_details)) { ?>
                        <a href="javascript:void(0);" id="<?php echo $post_id; ?>" class="js-open-modal" data-modal-id="popup" title="Click to show current status">
                            <img src="https://tfree.sfo2.cdn.digitaloceanspaces.com/default/tf-wp-shipment-preview.png" alt="Shipment Status"/>
                        </a>
                        <?php
                    } else {
                        foreach ($shipment_details as $shipment_values) {
                            if ($i == 0) {
                                echo '<span style="color: #5b841b;">' . $shipment_values['status'] . '</span><br>' . $shipment_values['delivered_date'];
                            }
                            $i++;
                        }
                    } ?>
                    <div id="popup" class="trackfree-modal-box">
                        <header>
                            <a href="javascript:void(0);" class="trackfree-modal-close trackfree-close">X</a>
                            <h3><?php _e('Shipment Status', 'trackfree-woocommerce-tracking'); ?></h3>
                        </header>
                        <div class="modal-body">
                            <div id="trackfree-shipment-content">
                            </div>
                        </div>
                        <footer>
                            <a href="javascript:void(0);" class="trackfree-modal-close"><?php _e('Close', 'trackfree-woocommerce-tracking'); ?>
                            </a>
                        </footer>
                    </div>
                    <?php
                }
            }
        }
        //New version E

        add_action('manage_shop_order_posts_custom_column', 'orders_list_shipment_action', 20, 2);
        function orders_list_shipment_action($column, $post_id)
        {
            if ('shipment_status' === $column) {
                $shipment_details = get_post_meta($post_id, '_trackfree_shipment_details', true);
                $i = 0;
                if ($shipment_details) {
                    if (checkShipmentStatus($shipment_details)) { ?>
                        <a href="javascript:void(0);" id="<?php echo $post_id; ?>" class="js-open-modal" data-modal-id="popup" title="Click to show current status">
                            <img src="https://tfree.sfo2.cdn.digitaloceanspaces.com/default/tf-wp-shipment-preview.png" alt="Shipment Status"/>
                        </a>
                        <?php
                    } else {
                        foreach ($shipment_details as $shipment_values) {
                            if ($i == 0) {
                                echo '<span style="color: #5b841b;">' . $shipment_values['status'] . '</span><br>' . $shipment_values['delivered_date'];
                            }
                            $i++;
                        }
                    } ?>
                    <div id="popup" class="trackfree-modal-box">
                        <header>
                            <a href="javascript:void(0);" class="trackfree-modal-close trackfree-close">X</a>
                            <h3><?php _e('Shipment Status', 'trackfree-woocommerce-tracking'); ?></h3>
                        </header>
                        <div class="modal-body">
                            <div id="trackfree-shipment-content">
                            </div>
                        </div>
                        <footer>
                            <a href="javascript:void(0);" class="trackfree-modal-close"><?php _e('Close', 'trackfree-woocommerce-tracking'); ?>
                            </a>
                        </footer>
                    </div>
                    <?php
                }
            }
        }
    }

    function checkShipmentStatus($array)
    {
        foreach ($array as $val) {
            if ($val['status'] != 'Delivered') {
                return 1;
            }
        }
        return 0;
    }

    add_action('wp_ajax_tracking_delete_action', 'tracking_delete_action');
    function tracking_delete_action()
    {
        $user_id = get_current_user_id();
        if (current_user_can('edit_user', $user_id)) {
            $order_id = $_POST['order_id'];
            $track_id = $_POST['track_id'];
            $shipment_details = get_post_meta($order_id, '_trackfree_shipment_details', true);
            $trackfree_account_api_key = get_option('trackfree_account_api_key');
            $track_data = array(
                'tracking_num' => $shipment_details[$track_id]['tracking_num']
            );
            wp_remote_get(trackfree_url() . '/api/wc_delete_tracking?key=' . $trackfree_account_api_key, array(
                'sslverify' => false,
                'timeout' => 15,
                'body' => $track_data
            ));
            unset($shipment_details[$track_id]);
            update_post_meta($order_id, '_trackfree_shipment_details', $shipment_details);
            $get_shipment_details = get_post_meta($order_id, '_trackfree_shipment_details', true);
            echo $total_shipments = $get_shipment_details ? sizeof($get_shipment_details) : 0;
        }
        wp_die();
    }
}
