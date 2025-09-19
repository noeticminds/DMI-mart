<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('wp_ajax_tfree_get_tracking_page_data', 'tfree_get_tracking_page_data');
    add_action('wp_ajax_nopriv_tfree_get_tracking_page_data', 'tfree_get_tracking_page_data');

    function tfree_get_tracking_page_data()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

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
        $show_trackfree_branding = 1;

        $track_order_status = "Track Order Status";
        $order_number = 'Order Number';
        $tracking_number = 'Tracking Number';
        $email = 'Email';
        $track = 'Track';
        $order = 'Order';
        $shipment = 'Shipment';
        $status = 'Status';
        $carrier = 'Carrier';
        $product = 'Product';
        $ordered = 'Ordered';
        $order_ready = 'Order Ready';
        $in_transit = 'In Transit';
        $out_for_delivery = 'Out for Delivery';
        $delivered = 'Delivered';
        $may_like = 'You may also like...';
        $order_not_found = 'Order Not Found';

        $date_format = 1;
        $time_format = 1;

        $text_above = '';
        $text_below = '';

        $custom_css = '';
        $custom_html_above= '';
        $custom_html_below = '';

        $tf_dis_opt = get_option('trackfree_display_options');
        if ($tf_dis_opt) {
            $cont_width = isset($tf_dis_opt['cont_width']) ? $tf_dis_opt['cont_width'] : 1200;
            $width_type = isset($tf_dis_opt['width_type']) ? $tf_dis_opt['width_type'] : 'pixel';
            $pg_bar_color = isset($tf_dis_opt['pg_bar_color']) ? $tf_dis_opt['pg_bar_color'] : '#017501';
            $by_ord_num_email = isset($tf_dis_opt['by_ord_num_email']) ? $tf_dis_opt['by_ord_num_email'] : 1;
            $by_tk_num = isset($tf_dis_opt['by_tk_num']) ? $tf_dis_opt['by_tk_num'] : 1;
            $show_cr_nm = isset($tf_dis_opt['show_cr_nm']) ? $tf_dis_opt['show_cr_nm'] : 1;
            $show_track_num = isset($tf_dis_opt['show_track_num']) ? $tf_dis_opt['show_track_num'] : 1;
            $show_track_info = isset($tf_dis_opt['show_track_info']) ? $tf_dis_opt['show_track_info'] : 1;
            $show_product_info = isset($tf_dis_opt['show_product_info']) ? $tf_dis_opt['show_product_info'] : 1;
            $show_map = isset($tf_dis_opt['show_map']) ? $tf_dis_opt['show_map'] : 1;
            $show_rec_prds = isset($tf_dis_opt['show_rec_prds']) ? $tf_dis_opt['show_rec_prds'] : 1;
            $show_trackfree_branding = isset($tf_dis_opt['show_trackfree_branding']) ? $tf_dis_opt['show_trackfree_branding'] : 1;
        }

        $tf_tns_str = get_option('trackfree_trans_strings');
        if ($tf_tns_str) {
            $track_order_status = isset($tf_tns_str['track_order_status']) ? $tf_tns_str['track_order_status'] : 'Track Order Status';
            $order_number = isset($tf_tns_str['order_number']) ? $tf_tns_str['order_number'] : 'Order Number';
            $tracking_number = isset($tf_tns_str['tracking_number']) ? $tf_tns_str['tracking_number'] : 'Tracking Number';
            $email = isset($tf_tns_str['email']) ? $tf_tns_str['email'] : 'Email';
            $track = isset($tf_tns_str['track']) ? $tf_tns_str['track'] : 'Track';
            $order = isset($tf_tns_str['order']) ? $tf_tns_str['order'] : 'Order';
            $shipment = isset($tf_tns_str['shipment']) ? $tf_tns_str['shipment'] : 'Shipment';
            $status = isset($tf_tns_str['status']) ? $tf_tns_str['status'] : 'Status';
            $carrier = isset($tf_tns_str['carrier']) ? $tf_tns_str['carrier'] : 'Carrier';
            $product = isset($tf_tns_str['product']) ? $tf_tns_str['product'] : 'Product';
            $ordered = isset($tf_tns_str['ordered']) ? $tf_tns_str['ordered'] : 'Ordered';
            $order_ready = isset($tf_tns_str['order_ready']) ? $tf_tns_str['order_ready'] : 'Order Ready';
            $in_transit = isset($tf_tns_str['in_transit']) ? $tf_tns_str['in_transit'] : 'In Transit';
            $out_for_delivery = isset($tf_tns_str['out_for_delivery']) ? $tf_tns_str['out_for_delivery'] : 'Out for Delivery';
            $delivered = isset($tf_tns_str['delivered']) ? $tf_tns_str['delivered'] : 'Delivered';
            $may_like = isset($tf_tns_str['may_like']) ? $tf_tns_str['may_like'] : 'You may also like...';
            $order_not_found = isset($tf_tns_str['order_not_found']) ? $tf_tns_str['order_not_found'] : 'Order Not Found';
        }

        $tf_dtm_fms = get_option('trackfree_date_time_formats');

        if ($tf_dtm_fms) {
            $date_format = isset($tf_dtm_fms['date_format']) ? $tf_dtm_fms['date_format'] : 1;
            $time_format = isset($tf_dtm_fms['time_format']) ? $tf_dtm_fms['time_format'] : 1;
        }

        $total_trans = 0;
        $original_texts = [];
        $replace_texts = [];
        $tf_tns_stn = get_option('trackfree_translate_strings');
        if ($tf_tns_stn) {
            $total_trans = sizeof ($tf_tns_stn['original_text']);
            $original_texts = $tf_tns_stn['original_text'];
            $replace_texts = $tf_tns_stn['replace_text'];
        }

        $tf_add_txt = get_option('trackfree_additional_texts');
        if ($tf_add_txt) {
            $text_above = isset($tf_add_txt['text_above']) ? $tf_add_txt['text_above'] : '';
            $text_below = isset($tf_add_txt['text_below']) ? $tf_add_txt['text_below'] : '';
        }

        $tf_css_htm = get_option('trackfree_custom_css_and_html');
        if ($tf_css_htm) {
            $custom_css = isset($tf_css_htm['custom_css']) ? $tf_css_htm['custom_css'] : '';
            $custom_html_above= isset($tf_css_htm['custom_html_above']) ? $tf_css_htm['custom_html_above'] : '';
            $custom_html_below = isset($tf_css_htm['custom_html_below']) ? $tf_css_htm['custom_html_below'] : '';
        }

        $track_page_data = [
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
            'show_trackfree_branding' => $show_trackfree_branding,
            'track_order_status' => $track_order_status,
            'order_number' => $order_number,
            'tracking_number' => $tracking_number,
            'email' => $email,
            'track' => $track,
            'order' => $order,
            'shipment' => $shipment,
            'status' => $status,
            'carrier' => $carrier,
            'product' => $product,
            'ordered' => $ordered,
            'order_ready' => $order_ready,
            'in_transit' => $in_transit,
            'out_for_delivery' => $out_for_delivery,
            'delivered' => $delivered,
            'may_like' => $may_like,
            'order_not_found' => $order_not_found,
            'date_format' => $date_format,
            'time_format' => $time_format,
            'total_trans' => $total_trans,
            'original_texts' => $original_texts,
            'replace_texts' => $replace_texts,
            'text_above' => $text_above,
            'text_below' => $text_below,
            'custom_css' => $custom_css,
            'custom_html_above' => $custom_html_above,
            'custom_html_below' => $custom_html_below,
        ];

        $date_formats = [
            ["id" => 1, "label" => date('M d, Y', strtotime('now'))],
            ["id" => 2, "label" => date('M d', strtotime('now'))],
            ["id" => 3, "label" => date('M jS, Y', strtotime('now'))],
            ["id" => 4, "label" => date('d M Y', strtotime('now'))],
            ["id" => 5, "label" => date('d-M-Y', strtotime('now'))],
            ["id" => 6, "label" => date('m/d/Y', strtotime('now')) . ' (m/d/yyyy)'],
            ["id" => 7, "label" => date('d/m/Y', strtotime('now')) . ' (d/m/yyyy)'],
        ];

        $time_formats = [
            ["id" => 1, "label" => date('h:i a', strtotime('now'))],
            ["id" => 2, "label" => "24-hour time"],
        ];

        $response = [
            'status' => 'success',
            'track_page_data' => $track_page_data,
            'date_formats' => $date_formats,
            'time_formats' => $time_formats,
        ];
        wp_send_json_success($response);
        wp_die();
    }
    add_action('wp_ajax_tfree_post_tracking_page_data', 'tfree_post_tracking_page_data');
    add_action('wp_ajax_nopriv_tfree_post_tracking_page_data', 'tfree_post_tracking_page_data');

    function tfree_post_tracking_page_data()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $tf_post_data = file_get_contents('php://input');
        $request_data = json_decode($tf_post_data, true);

        if ($request_data['post_type'] == 'display_options') {
            $display_options_data = array(
                'cont_width' => sanitize_text_field($request_data['cont_width']),
                'width_type' => $request_data['width_type'],
                'pg_bar_color' => sanitize_text_field($request_data['pg_bar_color']),
                'by_ord_num_email' => $request_data['by_ord_num_email'] ? 1 : 0,
                'by_tk_num' => $request_data['by_tk_num'] ? 1 : 0,
                'show_cr_nm' => $request_data['show_cr_nm'] ? 1 : 0,
                'show_track_num' => $request_data['show_track_num'] ? 1 : 0,
                'show_track_info' => $request_data['show_track_info'] ? 1 : 0,
                'show_product_info' => $request_data['show_product_info'] ? 1 : 0,
                'show_map' => $request_data['show_map'] ? 1 : 0,
                'show_rec_prds' =>$request_data['show_rec_prds'] ? 1 : 0,
                'show_trackfree_branding' => $request_data['show_trackfree_branding'] ? 1 : 0,
            );
            update_option('trackfree_display_options', $display_options_data);
        } else if ($request_data['post_type'] == 'trans_strings') {
            $track_page_id = get_option('trackfree_track_page_id');
            $page_info = get_post($track_page_id);
            if (!empty($page_info)) {
                $tf_page = [
                    'ID' =>  $page_info->ID,
                    'post_title'   => sanitize_text_field($request_data['track_order_status']),
                    'post_name'    => $page_info->post_name,
                    'post_content' => $page_info->post_content,
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_author'  => 1,
                ];
                wp_update_post($tf_page);
            }

            $trans_strings_data = array(
                'track_order_status' => sanitize_text_field($request_data['track_order_status']),
                'order_number' => sanitize_text_field($request_data['order_number']),
                'tracking_number' => sanitize_text_field($request_data['tracking_number']),
                'email' => sanitize_text_field($request_data['email']),
                'track' => sanitize_text_field($request_data['track']),
                'order' => sanitize_text_field($request_data['order']),
                'shipment' => sanitize_text_field($request_data['shipment']),
                'status' => sanitize_text_field($request_data['status']),
                'carrier' => sanitize_text_field($request_data['carrier']),
                'product' => sanitize_text_field($request_data['product']),
                'ordered' => sanitize_text_field($request_data['ordered']),
                'order_ready' => sanitize_text_field($request_data['order_ready']),
                'in_transit' => sanitize_text_field($request_data['in_transit']),
                'out_for_delivery' => sanitize_text_field($request_data['out_for_delivery']),
                'delivered' => sanitize_text_field($request_data['delivered']),
                'may_like' => sanitize_text_field($request_data['may_like']),
                'order_not_found' => sanitize_text_field($request_data['order_not_found']),
            );
            update_option('trackfree_trans_strings', $trans_strings_data);
        } else if ($request_data['post_type'] == 'date_time_formats') {
            $date_format_data = array(
                'date_format' => $request_data['date_format'],
                'time_format' => $request_data['time_format']
            );
            update_option('trackfree_date_time_formats', $date_format_data);
        } else if ($request_data['post_type'] == 'manual_translations') {
            $manual_trans_data = array(
                'original_text' => $request_data['original_text'],
                'replace_text' => $request_data['replace_text']
            );
            update_option('trackfree_translate_strings', $manual_trans_data);
        } else if ($request_data['post_type'] == 'additional_texts') {
            $additional_text_data = array(
                'text_above' => sanitize_text_field($request_data['text_above']),
                'text_below' => sanitize_text_field($request_data['text_below'])
            );
            update_option('trackfree_additional_texts', $additional_text_data);
        } else if ($request_data['post_type'] == 'custom_css_and_html') {
            $custom_css_html_data = array(
                'custom_css' => $request_data['tfree_custom_css'],
                'custom_html_above' => stripslashes($request_data['custom_html_above']),
                'custom_html_below' => stripslashes($request_data['custom_html_below']),
            );
            update_option('trackfree_custom_css_and_html', $custom_css_html_data);
        }

        $response = [
            'status' => 'success'
        ];
        wp_send_json_success($response);
        wp_die();
    }
}
?>
