<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tf_wc_order_notes($order_id) {
    $trackfree_account_api_key = get_option('trackfree_account_api_key');
    $trackfree_account_verify  = get_option('trackfree_account_verify');

    $tracking_number = '';
    $carrier_name = '';
    if (($trackfree_account_api_key) && ($trackfree_account_verify == 1)) {
        $order_notes = wc_get_order_notes([
            'order_id' => $order_id
        ]);
        foreach ($order_notes as $order_note) {
            if (substr($order_note->content, 0, 15 ) == "SHIPMENT NUMBER" || substr($order_note->customer_note, 0, 15 ) == "SHIPMENT NUMBER") {
                $note = $order_note->content ? $order_note->content : $order_note->customer_note;

                $shipment_data = explode('|', $note);
                if (isset($shipment_data[1])) {
                    $shipper = explode(':', $shipment_data[1]);
                    if (isset($shipper[1])) {
                        $carrier_name = trim($shipper[1]);
                    }
                }

                if (isset($shipment_data[2])) {
                    $tracking_info = explode("/", strip_tags($shipment_data[2]));
                    $tracking_num = end($tracking_info);
                    if (isset($tracking_num)) {
                        $tracking_number = trim($tracking_num);
                    }
                    $tracking_number = str_replace(array('#TRACKING', 'TRACKING'), '', $tracking_number);
                }

                if ($tracking_number && $carrier_name) {
                    $is_existing_tracking = 0;
                    $shipment_details = get_post_meta($order_id, '_trackfree_shipment_details', true);

                    if ($shipment_details) {
                        foreach ($shipment_details as $ship_detail) {
                            if ($ship_detail['tracking_num'] == $tracking_number) {
                                $is_existing_tracking = 1;
                            }
                        }
                    }

                    if ($is_existing_tracking == 0) {
                        $trackfree_courier_name = $carrier_name;
                        $trackfree_tracking_number = preg_replace('/\s+|[^a-zA-Z0-9\-]/', '', $tracking_number);
                        if ($trackfree_tracking_number) {
                            $order = wc_get_order($order_id);
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
                                }
                            }

                            // Get products by category
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
                                'plugin_version' => '',
                                'shipment_details' => get_post_meta($order_id, '_trackfree_shipment_details', true),
                                'order_notes_tracking' => 1
                            );

                            $response_data = wp_remote_get(trackfree_url() . '/api/wc_add_new_track', array(
                                'sslverify' => false,
                                'timeout' => 15,
                                'body' => $track_request
                            ));
                            $track_data = json_decode(wp_remote_retrieve_body($response_data), true);
                            if ($track_data['status'] == 'success') {
                                $new_shipment[] = array(
                                    'tracking_num' => $trackfree_tracking_number,
                                    'courier_name' => $trackfree_courier_name,
                                    'status' => '',
                                    'estimated_delivery' => '',
                                    'delivered_date' => '',
                                    'fulfilment_items' => []
                                );
                                $existing_shipments = get_post_meta($order_id, '_trackfree_shipment_details', true);
                                if ($existing_shipments) {
                                    $shipments = array_merge($existing_shipments, $new_shipment);
                                    update_post_meta($order_id, '_trackfree_shipment_details', wc_clean($shipments));
                                    if (get_option('trackfree_auto_order_status_update') == 1) {
                                        trackfree_update_order_status($shipments, $order_id);
                                    }
                                } else {
                                    update_post_meta($order_id, '_trackfree_shipment_details', wc_clean($new_shipment));
                                    if (get_option('trackfree_auto_order_status_update') == 1) {
                                        trackfree_update_order_status($new_shipment, $order_id);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function tf_wc_order_summary($order_id) {
    $user_id = get_current_user_id();
    if (current_user_can('edit_user', $user_id)) {
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $trackfree_account_verify  = get_option('trackfree_account_verify');

        if (($trackfree_account_api_key) && ($trackfree_account_verify == 1)) {
            $order = wc_get_order($order_id);
            $order_data = $order->get_data();

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

            $order_items  = array();

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

            wp_remote_post(trackfree_url() . '/api/wc_order_summary', array(
                'sslverify' => false,
                'timeout' => 15,
                'body' => $order_details
            ));
        }
    }
}

add_action('woocommerce_checkout_order_processed', 'trackfree_wc_new_order', 10, 1);

function trackfree_wc_new_order($order_id) {
    tf_wc_order_summary($order_id);
}

add_action('woocommerce_order_status_changed', 'trackfree_wc_update_order', 10, 1);

function trackfree_wc_update_order($order_id) {
    tf_wc_order_summary($order_id);
    tf_wc_order_notes($order_id);
}

add_action( 'woocommerce_order_note_added', 'trackfree_after_order_note_added', 10, 2 );
function trackfree_after_order_note_added ($comment_id, $order) {
    $order_id = $order->get_id();
    if ($order_id) {
        tf_wc_order_notes($order_id);
    }
}
?>
