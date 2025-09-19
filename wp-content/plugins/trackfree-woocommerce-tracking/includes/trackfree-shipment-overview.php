<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_get_shipment_action', 'get_shipment_action' );

function get_shipment_action()
{
    $user_id = get_current_user_id();
    if (current_user_can('edit_user', $user_id)) {
        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        $trackfree_account_verify = get_option('trackfree_account_verify');
        if (($trackfree_account_api_key) && ($trackfree_account_verify)) {
            $trackfree_courier_name = '';
            $trackfree_tracking_number = '';
            $shipment_details = get_post_meta($_POST['order_id'], '_trackfree_shipment_details', true);
            $total_shipments = $shipment_details ? sizeof($shipment_details) : 0;
            if ($shipment_details) {
                $update_shipment_details = array();
                if ($total_shipments > 1) { ?>
                    <div class="media-router" style="margin-top:10px;">
                        <?php
                        for ($i = 1; $i <= $total_shipments; $i++) {
                            ?>
                            <a href="javascript:void(0);" style="font-size:12px;" class="trackfree_shipement_menu_item media-menu-item <?php if ($i == 1) { ?> active <?php } ?>" id="shipment-item-link_<?php echo $i;?>">Shipememt <?php echo $i ?></a>
                            <?php
                            }
                        ?>
                    </div>
                    <div style="clear: both;"></div>
                    <?php
                     $i = 1;
                     foreach ($shipment_details as $shipment_values) {
                         ?>
                        <div class="trackfree-shipment-content trackfree-tab-content <?php if ($i==1) { ?> trackfree_active <?php } ?>" id="shipment-detail_<?php echo $i;?>">
                        <?php
                        $trackfree_courier_name = $shipment_values['courier_name'];
                        $trackfree_tracking_number = $shipment_values['tracking_num'];
                       if ($trackfree_tracking_number) {
                            $track_request = array(
                                'key' => $trackfree_account_api_key,
                                'tracking_num' => $trackfree_tracking_number,
                                'courier_name' => $trackfree_courier_name,
                                'request_type' => 'list'
                            );

                            $response_data = wp_remote_get(trackfree_url() . '/api/wc_track_data',array(
                                'sslverify' => false,
                                'timeout' => 15,
                                'body' => $track_request
                                )
                            );
                            $track_data = json_decode( wp_remote_retrieve_body( $response_data ), true );
                            if ($track_data['response'] == 'success') {
                                $update_shipment_details[] = array(
                                    'tracking_num' => $track_data['trackingNum'],
                                    'courier_name' => $track_data['courierName'],
                                    'status' => $track_data['status'],
                                    'estimated_delivery' => $track_data['estimateDeliveryDate'],
                                    'delivered_date' => $track_data['deliveredDate']
                                );
                                ?>
                                <div id="track_details_<?php echo $i;?>">
                                  <?php echo shipment_preview_template($track_data); ?>
                                </div>

                                <?php
                                echo show_shipment_details($track_data['trackDetails']['trackValues'], $i, 'mb');
                            } else { ?>
                                <p><?php echo $trackfree_tracking_number; ?></p>
                                <p><?php echo $trackfree_courier_name; ?></p>
                                <p>
                                    <?php _e('This tracking does not exist', 'trackfree-woocommerce-tracking'); ?>
                                </p>
                                <?php
                                $update_shipment_details[] = array(
                                    'tracking_num' => $trackfree_tracking_number,
                                    'courier_name' => $trackfree_courier_name,
                                    'status' => '',
                                    'estimated_delivery' => '',
                                    'delivered_date' => ''
                                );
                            }
                        } ?>
                        </div>
                        <?php
                        $i++;
                    }?>
                    <?php
                } else {
                    $trackfree_courier_name = $shipment_details[0]['courier_name'];
                    $trackfree_tracking_number = $shipment_details[0]['tracking_num'];
                    $fulfilment_items = isset($shipment_details[0]['fulfilment_items']) ? $shipment_details[0]['fulfilment_items'] : [];
                    if ($trackfree_tracking_number) {
                        ?>
                        <?php
                        $track_request = array(
                            'key' => $trackfree_account_api_key,
                            'tracking_num' => $trackfree_tracking_number,
                            'courier_name' => $trackfree_courier_name,
                            'request_type' => 'list'
                        );

                        $response_data = wp_remote_get(trackfree_url() . '/api/wc_track_data',array(
                            'sslverify' => false,
                            'timeout' => 15,
                            'body' => $track_request
                            )
                        );
                        $track_data = json_decode( wp_remote_retrieve_body( $response_data ), true );
                        if ($track_data['response'] == 'success') {
                            $update_shipment_details[] = array(
                                'tracking_num' => $track_data['trackingNum'],
                                'courier_name' => $track_data['courierName'],
                                'status' => $track_data['status'],
                                'estimated_delivery' => $track_data['estimateDeliveryDate'],
                                'delivered_date' => $track_data['deliveredDate']
                            );
                            echo shipment_preview_template($track_data);

                            echo show_shipment_details($track_data['trackDetails']['trackValues'], 1, 'mb');
                            ?>
                            <?php
                        } else {
                            ?>
                            <p>
                                <?php echo $trackfree_tracking_number; ?>
                            </p>
                            <p>
                                <?php echo $trackfree_courier_name; ?>
                            </p>
                            <p>
                                <?php _e('This tracking does not exist', 'trackfree-woocommerce-tracking'); ?>
                            </p>
                            <?php
                            $update_shipment_details[] = array(
                                'tracking_num' => $trackfree_tracking_number,
                                'courier_name' => $trackfree_courier_name,
                                'status' => '',
                                'estimated_delivery' => '',
                                'delivered_date' => '',
                                'fulfilment_items' => $fulfilment_items
                            );
                        }
                    }
                }
                update_post_meta($_POST['order_id'], '_trackfree_shipment_details', wc_clean($update_shipment_details));
                if (get_option('trackfree_auto_order_status_update') == 1) {
                    trackfree_update_order_status($update_shipment_details, $_POST['order_id']);
                }
            }
        }
    }
    wp_die();
}

function shipment_preview_template($track_data)
{
    ?>
    <div>
        <img src="<?php echo $track_data['courierLogo'];?>" alt="<?php echo $track_data['courierName'];?>" width="36" style="opacity: 0.4;"/>
        <br/>
        <?php echo $track_data['trackingNum']; ?>
    </div>
    <div>
        <a href="<?php echo trackfree_track_page_url($track_data['trackingNum']);?>" target="_new"><?php _e('View tracking', 'trackfree-woocommerce-tracking');?></div>
    </p>
    <?php
}
