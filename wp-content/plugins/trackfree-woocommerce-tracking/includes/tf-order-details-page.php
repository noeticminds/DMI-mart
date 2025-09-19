<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function trackfree_meta_box_add()
{
    add_meta_box('trackfree-meta-box', 'TrackFree', 'trackfree_order_meta_callback', get_order_admin_screen(), 'side', 'high');
}

if (!function_exists('get_order_admin_screen')) {
    function get_order_admin_screen() {
        if (!wc_order_util_method_exists('get_order_admin_screen')) {
            return 'shop_order';
        }
        return call_user_func_array(array('Automattic\WooCommerce\Utilities\OrderUtil', 'get_order_admin_screen'), array());
    }
}

if (!function_exists( 'wc_order_util_method_exists')) {
    function wc_order_util_method_exists($method_name) {
        return class_exists('Automattic\WooCommerce\Utilities\OrderUtil') && method_exists('Automattic\WooCommerce\Utilities\OrderUtil', $method_name);
    }
}

function trackfree_order_meta_callback()
{
    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (!$order_id) {
        $order_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
    }

    $trackfree_account_api_key = get_option('trackfree_account_api_key');
    $trackfree_account_verify = get_option('trackfree_account_verify');

    if (($trackfree_account_api_key) && ($trackfree_account_verify)) {
        //Delete old trackfree options
        if (get_option('trackfree_option_name')) {
            $old_courier_data = get_option('trackfree_option_name');
            if ($old_courier_data['couriers']) {
                $courier_request = array(
                    'key' => $trackfree_account_api_key,
                    'courier_data' => $old_courier_data['couriers']
                );
                $response_data = wp_remote_get(trackfree_url() . '/api/wc_get_couriers',array(
                    'sslverify' => false,
                    'timeout' => 15,
                    'body' => $courier_request
                    )
                );
                $courier_data = wp_remote_retrieve_body( $response_data );
                $updated_couriers = sanitize_text_field($courier_data);
                add_option('trackfree_preferred_couriers');
                update_option('trackfree_preferred_couriers', $updated_couriers);
                delete_option('trackfree_option_name');
            }
        }

        $trackfree_courier_name = '';
        $trackfree_tracking_number = '';
        $shipment_details = get_post_meta($order_id, '_trackfree_shipment_details', true);
        $total_shipments = $shipment_details ? sizeof($shipment_details) : 0;
        ?>
        <div>
            <?php
            if ($shipment_details) {
                $couriers_json = file_get_contents( plugins_url('/trackfree-woocommerce-tracking/assets/js/trackfree_couriers.json'));
                $courier_list = json_decode($couriers_json, true);
                $i = 1;
                foreach ($shipment_details as $key => $shipment_values) {
                    $trackfree_courier_name = $shipment_values['courier_name'];
                    $trackfree_tracking_number = $shipment_values['tracking_num'];

                    $courier_logo = '';
                    if (is_array($courier_list)) {
                        $courier_key = array_search($trackfree_courier_name, array_column($courier_list, 'name'));
                        if ($courier_key) {
                            $courier_logo = $courier_list[$courier_key]['logo'];
                        }
                    }

                    if ($trackfree_tracking_number) { ?>
                        <div id="track_details_<?php echo $key;?>">
                            <div class="tfree-my-16">
                                <div class="tfree-fl-bx tfree-justify-between">
                                    <div>
                                        <?php _e('Shipment', 'trackfree-woocommerce-tracking'); ?> <?php echo $i;?>
                                    </div>
                                    <div class="tfree-track-actions">
                                        <a href="javascript:void(0);" class="trackfree_edit_tracking" id="<?php echo $key;?>">
                                            <?php _e('Edit', 'trackfree-woocommerce-tracking');?>
                                        </a>
                                        <a href="javascript:void(0);" class="trackfree_delete_tracking" style="padding-left: 4px;" id="<?php echo $key;?>">
                                            <?php _e('Delete', 'trackfree-woocommerce-tracking');?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <table width="100%">
                                    <tr>
                                        <td>
                                            <?php if ($courier_logo) { ?>
                                                <img src="//tfree.sfo2.digitaloceanspaces.com/courier-logo/<?php echo $courier_logo;?>" alt="<?php echo $trackfree_courier_name;?>" width="36" style="opacity: 0.4;"/>
                                                <?php
                                            } else {
                                                echo $trackfree_courier_name;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo trackfree_track_page_url($trackfree_tracking_number);?>" target="_blank" class="tfree-tn-ct"><?php echo $trackfree_tracking_number; ?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="tfree-py-8">
                                            <a href="javascript:void(0);" class="trackfree_show_shipment_detail" id="<?php echo $key;?>"><?php _e('View shipment details', 'trackfree-woocommerce-tracking');?>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                                <div class="tf-shipment-content" id="tf_ship_detail_<?php echo $key;?>">

                                </div>
                            </div>
                            <?php
                            if ($i != 3) {
                                echo '<hr>';
                            }
                            ?>
                        </div>
                        <input type="hidden" id="order_id" value="<?php echo $order_id; ?>"/>
                        <?php
                    }
                    $i++;
                }
            }

            if ($total_shipments < 3) {
                ?>
                <div class="tfree-my-16">
                    <input type="button" class="button button-primary tf-show-add-track" id="<?php echo $order_id;?>" value="<?php _e('Add tracking number', 'trackfree-woocommerce-tracking');?>" style="width: 100%; height: 36px;">
                </div>
                <?php
            } ?>
            <input type="hidden" id="redirect_url" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>"/>
            <div id="tfree-add-track-modal" class="tfree-cm-modal">
                <div class="tfree-cm-modal-content">
                    <div class="tfree-cm-modal-header">
                        <div class="tfree-cm-modal-heading">#<?php echo $order_id;?> <?php _e('Add tracking', 'trackfree-woocommerce-tracking');?></div>
                        <span class="tfree-cm-modal-close">&times;</span>
                    </div>
                    <div class="tfree-cm-modal-body">
                        <div id="tfree-add-ship-cont"></div>
                    </div>
                    <div class="tfree-cm-modal-footer">
                        <input type="button" id="trackfree_btn_add_tracking" class="button button-primary" value="<?php _e('Add', 'trackfree-woocommerce-tracking');?>">
                    </div>
                </div>
            </div>
            <div id="tfree-edit-track-modal" class="tfree-cm-modal">
                <div class="tfree-cm-modal-content">
                    <div class="tfree-cm-modal-header">
                        <div class="tfree-cm-modal-heading">#<?php echo $order_id;?> <?php _e('Edit tracking', 'trackfree-woocommerce-tracking');?></div>
                        <span class="tfree-cm-modal-close">&times;</span>
                    </div>
                    <div class="tfree-cm-modal-body">
                        <div id="tfree-edit-ship-cont">

                        </div>
                    </div>
                    <div class="tfree-cm-modal-footer">
                        <input type="button" id="trackfree_btn_update_tracking" class="button button-primary" value="<?php _e('Save', 'trackfree-woocommerce-tracking');?>">
                    </div>
                </div>
            </div>
        </div>
        <?php
    } else {
        echo '<a href="' . admin_url('options-general.php?page=trackfree-getting-started') . '">' . __('Connect with TrackFree', 'trackfree-woocommerce-tracking') . '</a>';
    }
}

add_action('wp_ajax_show_shipment_detail_action', 'show_shipment_detail_action');

function show_shipment_detail_action()
{
    $user_id = get_current_user_id();
    if (current_user_can('edit_user', $user_id)) {
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $trackfree_account_verify = get_option('trackfree_account_verify');

        if (($trackfree_account_api_key) && ($trackfree_account_verify)) {
            $shipment_details = get_post_meta($_POST['order_id'], '_trackfree_shipment_details', true);
            $key = $_POST['shipment_id'];
            $trackfree_courier_name = $shipment_details[$key]['courier_name'];
            $trackfree_tracking_number = $shipment_details[$key]['tracking_num'];
            $fulfilment_items = isset($shipment_details[$key]['fulfilment_items']) ? $shipment_details[$key]['fulfilment_items'] : [];
           if ($trackfree_tracking_number) {
                $track_request = array(
                    'key' => $trackfree_account_api_key,
                    'tracking_num' => $trackfree_tracking_number,
                    'courier_name' => $trackfree_courier_name,
                    'request_type' => 'details'
                );

                $response_data = wp_remote_get(trackfree_url() . '/api/wc_track_data', array(
                    'sslverify' => false,
                    'timeout' => 15,
                    'body' => $track_request
                    )
                );
                $track_data = json_decode( wp_remote_retrieve_body( $response_data ), true );
                if ($track_data['response'] == 'success') {
                    $shipment_details[$key] = array(
                        'tracking_num' => $track_data['trackingNum'],
                        'courier_name' => $track_data['courierName'],
                        'status' => $track_data['status'],
                        'estimated_delivery' => $track_data['estimateDeliveryDate'],
                        'delivered_date' => $track_data['deliveredDate'],
                        'fulfilment_items' => $fulfilment_items
                    );
                    update_post_meta($_POST['order_id'], '_trackfree_shipment_details', wc_clean($shipment_details));

                    if (get_option('trackfree_auto_order_status_update') == 1) {
                        trackfree_update_order_status($shipment_details, $_POST['order_id']);
                    }
                    ?>
                    <div id="track_details_<?php echo $key;?>">
                        <div style="padding-top:20px">
                            <table style="width:100%">
                                <tr>
                                    <?php if ($track_data['feedback']) { ?>
                                        <td align="center" style="width: 50%; border-right: solid 1px #ddd;">
                                            <span class="trackfree-status"><?php _e('STATUS', 'trackfree-woocommerce-tracking');?></span>
                                            <br/>
                                            <span class="tf-status">
                                                <?php echo $track_data['status']; ?>
                                            </span>
                                        </td>
                                        <td align="center" style="width: 50%">
                                            <span class="trackfree-status"><?php _e('FEEDBACK', 'trackfree-woocommerce-tracking');?></span>
                                            <br/>
                                            <?php
                                            if ($track_data['feedback'] == 1) { ?>
                                                <span class="trackfree-feedback"><?php _e('Satisfied', 'trackfree-woocommerce-tracking');?></span>
                                                <?php
                                            } else if ($track_data['feedback'] == 2) { ?>
                                                <span class="trackfree-feedback"><?php _e('Not satisfied', 'trackfree-woocommerce-tracking');?></span>
                                                <?php
                                            } else { ?>
                                                <span class="trackfree-feedback">N/A</span>
                                                <?php
                                            } ?>
                                        </td>
                                    <?php } else { ?>
                                        <td align="center">
                                            <span class="trackfree-status"><?php _e('STATUS', 'trackfree-woocommerce-tracking');?></span>
                                            <br/>
                                            <span class="tf-status">
                                                <?php echo $track_data['status']; ?>
                                            </span>
                                        </td>
                                    <?php } ?>
                                </tr>
                            </table>
                        </div>
                        <?php
                        if ($track_data['status'] == 'Delivered') {
                            if ($track_data['deliveredDate']) { ?>
                                <div class="trackfree-delivered">
                                    <div class="trackfree-scheduled-delivery-o"><?php _e('Delivered on', 'trackfree-woocommerce-tracking');?> <?php echo $track_data['deliveredDate'];?></div>
                                </div>
                                <?php
                            }
                        } else if ($track_data['status'] == 'Exception') { ?>
                            <div class="trackfree-exception">
                                <div class="trackfree-scheduled-delivery-o"><?php echo 'Exception';?></div>
                            </div>
                            <?php
                        } else { ?>
                            <div class="trackfree-scheduled">
                                <div class="trackfree-scheduled-delivery-o">
                                    <?php
                                    if ($track_data['estimateDeliveryDate']) {
                                        _e('Estimated delivery on', 'trackfree-woocommerce-tracking'); ?> <?php
                                        echo $track_data['estimateDeliveryDate'];
                                    } else {
                                        _e('No estimated delivery date', 'trackfree-woocommerce-tracking');
                                    } ?>
                                </div>
                            </div>
                            <?php
                        } ?>

                        <?php if (get_option('trackfree_shipment_details_in_order_details') == 1) { ?>
                            <div class="trackfree-nv-tb" id="tf_ship_carrier_details" style="margin-top: 16px;">
                                <span class="trackfree_ord_det_menu_item trackfree-sh-ct active" id="shipping-link_<?php echo $key;?>"><?php _e('Shipping Activity', 'trackfree-woocommerce-tracking');?></span>
                                <span class="trackfree_ord_det_menu_item trackfree-sh-ct" id="carrier-link_<?php echo $key;?>"><?php _e('Contact Carrier', 'trackfree-woocommerce-tracking');?></span>
                            </div>
                            <div style="clear: both;"></div>
                            <div class="trackfree-tab-content tfree-mt-8 trackfree_active" id="shipping-detail_<?php echo $key;?>">
                                <?php
                                echo show_shipment_details($track_data['trackDetails']['trackValues'], $key, 'tf', 'overflow-y: auto;'); ?>
                            </div>
                            <div class="trackfree-tab-content" id="carrier-detail_<?php echo $key;?>">
                                <table class="trackfree-table tfree-mt-16" style="padding: 10px 0;">
                                    <?php
                                    if ($track_data['carrierContact']['phoneNumber']) { ?>
                                        <tr style="border: none;">
                                            <td>
                                                <?php echo $track_data['carrierContact']['phoneNumber'];?>
                                            </td>
                                        </tr>
                                        <?php
                                    } if ($track_data['carrierContact']['website']) { ?>
                                        <tr style="border: none;">
                                            <td>
                                                <a href="<?php echo $track_data['carrierContact']['trackUrl'];?>" target="_blank"><?php echo $track_data['carrierContact']['website'];?></a>
                                            </td>
                                        </tr>
                                        <?php
                                    } ?>
                                </table>
                            </div>
                            <?php
                        } ?>
                    </div>
                    <?php
                } else {
                    if ($track_data['message'] == 'tracking_not_exist') {
                        $shipment_details[$key] = array(
                            'tracking_num' => $trackfree_tracking_number,
                            'courier_name' => $trackfree_courier_name,
                            'status' => '',
                            'estimated_delivery' => '',
                            'delivered_date' => '',
                            'fulfilment_items' => isset($shipment_details[$key]['fulfilment_items']) ? $shipment_details[$key]['fulfilment_items'] : []
                        );
                        update_post_meta($_POST['order_id'], '_trackfree_shipment_details', wc_clean($shipment_details));
                        ?>
                        <div id="track_details_<?php echo $key;?>">
                            <p>
                                <?php _e('This tracking does not exist', 'trackfree-woocommerce-tracking'); ?>
                            </p>
                        </div>
                        <?php
                    } else if ($track_data['message'] == 'user_not_exist') {
                        update_option('trackfree_account_api_key', '');
                        update_option('trackfree_account_verify', 0);
                    }
                }
            }
        }
    }
    wp_die();
}

add_action( 'wp_ajax_tfree_add_shipment', 'tfree_add_shipment_action' );

function tfree_add_shipment_action()
{
    $user_id = get_current_user_id();
    if (current_user_can('edit_user', $user_id)) {
        $order_id = $_POST['order_id'];
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $trackfree_account_verify = get_option('trackfree_account_verify');
        if (($trackfree_account_api_key) && ($trackfree_account_verify)) {
            $couriers = array();
            $preferred_couriers = get_option('trackfree_preferred_couriers');
            if ($preferred_couriers) {
                $couriers = explode(',', $preferred_couriers);
            }
            $order = wc_get_order($order_id);
            $order_items = array();

            $shipment_details = get_post_meta($order_id, '_trackfree_shipment_details', true);
            if ($shipment_details) {
                $existing_fulfilments = [];
                foreach ($shipment_details as $data) {
                    if (isset($data['fulfilment_items'])) {
                        foreach ($data['fulfilment_items'] as $item) {
                            $id = $item['variation_id'] ? $item['id'] . '-' . $item['variation_id'] :  $item['id'];
                            $quantity = $item['quantity'];
                            if (isset($existing_fulfilments[$id])) {
                                $existing_fulfilments[$id] += $quantity;
                            } else {
                                $existing_fulfilments[$id] = $quantity;
                            }
                        }
                    }
                }
            }

            if ($order_id) {
                foreach ($order->get_items() as $item_key => $item_values) {
                    $item_data = $item_values->get_data();
                    $product_data = wc_get_product($item_data['product_id']);

                    if (!empty($product_data)) {
                        $variation_id = '';
                        $variation_image_url = '';
                        if ($item_data['variation_id']) {
                            $variation_id = $item_data['variation_id'];
                            $product = new WC_Product_Variable($item_data['product_id']);
                            $variations = $product->get_available_variations();
                            foreach ($variations as $variation) {
                                if ($variation['variation_id'] == $item_data['variation_id']) {
                                    $variation_image_url = $variation['image']['url'];
                                }
                            }
                        }

                        $quantity = $item_data['quantity'];
                        $p_id = $variation_id ? $item_data['product_id'] . '-' . $variation_id : $item_data['product_id'];
                        if (isset($existing_fulfilments[$p_id])) {
                            $quantity = $item_data['quantity'] - $existing_fulfilments[$p_id];
                            $quantity = $quantity > 0 ? $quantity : 0;
                        }

                        if ($quantity > 0) {
                            $order_items[]  = array(
                                'product_id' => $item_data['product_id'],
                                'variation_id' => $variation_id,
                                'name' => $item_data['name'],
                                'sku' => $product_data->get_sku(),
                                'quantity' => $quantity,
                                'image' => $variation_image_url ? $variation_image_url : get_the_post_thumbnail_url($product_data->get_id(), 'full')
                            );
                        }
                    }
                }
            }
            ?>
            <div>
                <div class="trackfree-ln-sp tfree-pb-16">
                    <input type="hidden" value="<?php echo sizeof($order_items); ?>" id="total_order_items" />
                    <?php
                    if (sizeof($order_items) > 0) {
                        foreach ($order_items as $key => $item) {
                            $order_items_json = json_encode($item);
                            ?>
                            <table class="tfree-prd-items">
                                <tbody>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $item['image'];?>" width="40" height="40" />
                                        </td>
                                        <td class="item-name">
                                            <?php echo $item['name']; ?>
                                            <input type="hidden" value="<?php echo esc_attr($item['product_id']); ?>" name="_tfree_product_id_<?php echo $key; ?>" class="tfree-product-id" />
                                            <input type="hidden" value="<?php echo esc_attr($item['variation_id']); ?>" name="_tfree_variation_id_<?php echo $key; ?>" class="tfree-variation-id" />
                                            <input type="hidden" value="<?php echo esc_attr($order_items_json); ?>" name="_tfree_order_items_<?php echo $key; ?>" class="tfree-order-items" />
                                        </td>
                                        <td>
                                            <div class="tfree-qty-wrapper">
                                                <input type="number" id="tfree_quantity_<?php echo $key; ?>" class="tfree-product-qty qty text" min="0" max="<?php echo esc_attr($item['quantity']); ?>" name="_tfree_product_quantity_<?php echo $key; ?>" value="<?php echo esc_attr( $item['quantity']); ?>" size="4" pattern="[0-9]*" inputmode="numeric" />
                                                <span class="tfree-qty-suffix"> of <?php echo esc_attr($item['quantity']); ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                        }
                    } else {
                        echo '<div class="tfree-empty-fulfil">' . __('All items have been fulfilled', 'trackfree-woocommerce-tracking') . '</div>';
                    }
                    ?>
                    <div class="tfree-empty-line-error" style="display: none;">
                        <div class="tfree-mt-16 tfree-error-msg">
                            <?php _e('Empty line items', 'trackfree-woocommerce-tracking'); ?>
                        </div>
                    </div>
                </div>
                <div class="tfree-my-16">
                    <label>
                        <div class="tfree-my-4">
                            <?php _e('Tracking number', 'trackfree-woocommerce-tracking');?>
                        </div>
                        <input type="text" id="trackfree_tracking_number" name="_trackfree_tracking_number" class="form-field-wide" style="width: 100%" autocomplete="off">
                        <input type="hidden" id="ord_id" value="<?php echo $order_id; ?>"/>
                    </label>
                </div>
                <div class="tfree-fl-bx tfree-my-16 tfree-gap-4">
                    <div class="tfree-fl-gw">
                        <label>
                            <div class="tfree-my-4">
                                <?php _e('Courier', 'trackfree-woocommerce-tracking');?>:
                            </div>
                            <select name="_trackfree_courier_name" id="trackfree_courier_name" class="form-field-wide tfree-select-box" style="width: 100%;">
                                <option value=""><?php _e('Automatic Matching', 'trackfree-woocommerce-tracking');?></option>
                                <?php if (sizeof($couriers) > 0) { ?>
                                    <option disabled style="color: #999;"><?php _e('Preferred couriers', 'trackfree-woocommerce-tracking');?></option>
                                    <?php
                                } ?>
                                <?php
                                foreach ($couriers as $courier) { ?>
                                    <option value="<?php echo $courier;?>"><?php echo $courier; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </label>
                    </div>
                    <div class="tfree-fl-gw">
                        <label>
                            <div class="tfree-my-4">
                                <?php _e('Change order status as', 'trackfree-woocommerce-tracking');?> (<?php _e('Optional', 'trackfree-woocommerce-tracking');?>)
                            </div>
                            <select name="_trackfree_order_status" id="trackfree_order_status" class="form-field-wide"  style="width: 100%">
                                <option value="">— <?php _e('Select', 'trackfree-woocommerce-tracking');?> —</option>
                                <?php
                                $order_statuses = wc_get_order_statuses();
                                foreach ($order_statuses as $key => $status) { ?>
                                    <option value="<?php echo $key;?>"><?php echo $status; ?></option>
                                    <?php
                                } ?>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="tfree-invalid-shipment-error tfree-error-notification" style="display: none;">
                    <p>Please enter a valid tracking number</p>
                </div>
                <div class="tfree-add-shipment-error tfree-warning-notification" style="display: none;">
                    <p class="tfree-add-shipment-data">
                    </p>
                </div>
            </div>
            <?php
        }
    }
    wp_die();
}

add_action( 'wp_ajax_tfree_edit_shipment', 'tfree_edit_shipment_action' );

function tfree_edit_shipment_action()
{
    $user_id = get_current_user_id();
    if (current_user_can('edit_user', $user_id)) {
        $order_id = $_POST['order_id'];
        $shipment_key = $_POST['shipment_id'];
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $trackfree_account_verify = get_option('trackfree_account_verify');
        if (($trackfree_account_api_key) && ($trackfree_account_verify)) {
            $trackfree_courier_name = '';
            $trackfree_tracking_number = '';
            $shipment_details = get_post_meta($order_id, '_trackfree_shipment_details', true);
            $shipment_values = $shipment_details[$shipment_key];
            $order = wc_get_order($order_id);
            $get_order_items = [];
            foreach ($order->get_items() as $item_key => $item_values) {
                $item_data = $item_values->get_data();
                $product_id = $item_data['variation_id'] ? $item_data['product_id'] . '-' . $item_data['variation_id'] : $item_data['product_id'];
                $get_order_items[$product_id] = [
                    'product_id' => $item_data['product_id'],
                    'variation_id' => isset($item_data['variation_id']) ? $item_data['variation_id'] : '',
                    'name' => $item_data['name'],
                    'quantity' => $item_data['quantity']
                ];
            }
        }
        ?>
        <div>
            <?php
            if (sizeof($shipment_values['fulfilment_items']) > 0) {
                foreach ($shipment_values['fulfilment_items'] as $key => $item) {
                    $item_id = $item['variation_id'] ? $item['id'] . '-' . $item['variation_id'] : $item['id'];
                    if (isset($get_order_items[$item_id])) {
                        $order_item_data = $get_order_items[$item_id];
                        $product_data = wc_get_product($item['id']);
                        if (!empty($product_data)) {
                            $variation_id = '';
                            $variation_image_url = '';
                            if ($item['variation_id']) {
                                $variation_id = $item['variation_id'];
                                $product = new WC_Product_Variable($item['id']);
                                $variations = $product->get_available_variations();
                                foreach ($variations as $variation) {
                                    if ($variation['variation_id'] == $item['variation_id']) {
                                        $variation_image_url = $variation['image']['url'];
                                    }
                                }
                            }

                            $quantity = $order_item_data['quantity'];
                            $image_url = $variation_image_url ? $variation_image_url : get_the_post_thumbnail_url($product_data->get_id(), 'full');

                            $existing_quantity = 0;
                            foreach ($shipment_details as $data) {
                                if (isset($data['fulfilment_items'])) {
                                    foreach ($data['fulfilment_items'] as $fl_item) {
                                        $id = $fl_item['variation_id'] ? $fl_item['id'] . '-' . $fl_item['variation_id'] :  $fl_item['id'];
                                        if ($item_id == $id) {
                                            $existing_quantity += $fl_item['quantity'];
                                        }
                                    }
                                }
                            }
                            $existing_quantity = $existing_quantity - $item['quantity'];
                            $total_quantity = $quantity - $existing_quantity;
                            ?>
                            <table class="tfree-edit-prd-items tfree-prd-items">
                                <tbody>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $image_url;?>" alt="<?php echo $order_item_data['name']; ?>" width="40" height="40" />
                                        </td>
                                        <td class="item-name">
                                            <?php echo $order_item_data['name']; ?>
                                        </td>
                                        <td>
                                            <div class="tfree-qty-wrapper">
                                                <input type="number" id="tfree_edit_quantity_<?php echo $key; ?>" class="tfree-edit-product-qty qty text" min="0" max="<?php echo esc_attr($total_quantity); ?>" name="_tfree_edit_product_quantity_<?php echo $key; ?>" value="<?php echo esc_attr( $item['quantity']); ?>" size="4" pattern="[0-9]*" inputmode="numeric" />
                                                <span class="tfree-qty-suffix"> of <?php echo esc_attr($total_quantity); ?></span>
                                                <input type="hidden" id="total_quantity" class="edit-total-qty" value="<?php echo $total_quantity; ?>"/>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                        }
                    }
                }
            }
            ?>
        </div>
        <div class="tfree-empty-line-error" style="display: none;">
            <div class="tfree-mt-16 tfree-error-msg">
                <?php _e('Empty line items', 'trackfree-woocommerce-tracking'); ?>
            </div>
        </div>
        <div class="tfree-my-16">
            <label>
                <div class="tfree-my-4">
                    <?php _e('Tracking number', 'trackfree-woocommerce-tracking');?>
                </div>
                <input type="text" id="tfree_edit_track_num" name="_tfree_edit_track_num" class="form-field-wide" style="width: 100%" autocomplete="off" value="<?php echo $shipment_values['tracking_num'];?>">
                <input type="hidden" id="edit_ord_id" value="<?php echo $order_id; ?>"/>
                <input type="hidden" id="edit_shipment_id" value="<?php echo $shipment_key; ?>"/>
            </label>
        </div>
        <div class="tfree-fl-bx tfree-my-16 tfree-gap-4">
            <div class="tfree-fl-gw">
                <label>
                    <div class="tfree-my-4">
                        <?php _e('Courier', 'trackfree-woocommerce-tracking');?>:
                    </div>
                    <select name="_tfree_edit_courier_name" id="tfree_edit_courier_name" class="form-field-wide tfree-select-box" style="width: 100%;">
                        <option value=""><?php _e('Automatic Matching', 'trackfree-woocommerce-tracking');?></option>
                        <?php
                        $couriers = array();
                        $preferred_couriers = get_option('trackfree_preferred_couriers');
                        if ($preferred_couriers) {
                            $couriers = explode(',', $preferred_couriers);
                        }
                        if (sizeof($couriers) > 0) { ?>
                            <option disabled style="color: #999;"><?php _e('Preferred couriers', 'trackfree-woocommerce-tracking');?></option>
                            <?php
                        } ?>
                        <?php
                        foreach ($couriers as $courier) { ?>
                            <option value="<?php echo $courier;?>" <?php if ($shipment_values['courier_name'] == $courier) { ?> selected <?php } ?>><?php echo $courier; ?></option>
                            <?php
                        } ?>
                    </select>
                </label>
            </div>
            <div class="tfree-fl-gw">
                <label>
                    <div class="tfree-my-4">
                        <?php _e('Change order status as', 'trackfree-woocommerce-tracking');?> (<?php _e('Optional', 'trackfree-woocommerce-tracking');?>)
                    </div>
                    <select name="_trackfree_edit_order_status" id="trackfree_edit_order_status" class="form-field-wide"  style="width: 100%">
                        <option value="">— <?php _e('Select', 'trackfree-woocommerce-tracking');?> —</option>
                        <?php
                        $order_statuses = wc_get_order_statuses();
                        foreach ($order_statuses as $key => $status) { ?>
                            <option value="<?php echo $key;?>"><?php echo $status; ?></option>
                            <?php
                        } ?>
                    </select>
                </label>
            </div>
        </div>
        <div class="tfree-invalid-shipment-error tfree-error-notification" style="display: none;">
            <p>Please enter a valid tracking number</p>
        </div>
        <div class="tfree-add-shipment-error tfree-warning-notification" style="display: none;">
            <p class="tfree-add-shipment-data">
            </p>
        </div>
        <?php
    }
    wp_die();
}
