<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function show_multi_tracking_details($order_id, $trackings, $tracking_settings, $products = [])
{
    ?>
    <div style="margin-top: 32px">
        <div style="margin: 32px;">
            <div style="text-align: center">
                <h1>Order #<?php echo $order_id;?></h1>
            </div>
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 64px;">
                <?php
                $count = sizeof($trackings);
                for ($i = 1; $i <= $count; $i++) { ?>
                    <h2 style="padding-right: 16px" class="trackfree_od_tl" id="tfodls_<?php echo $i?>"><?php echo $tracking_settings['cont_shipment'] . ' ' . $i;?><?php echo $i != $count ? ',' : '';?></h2>
                    <?php
                } ?>
            </div>
            <?php
            $i = 1;
            foreach ($trackings as $d) {
                $data = $d['data'];
                ?>
                <div class="trackfree_sf_dt" id="tfodts_<?php echo $i;?>">
                    <?php show_tracking_progress($data, $tracking_settings['pg_bar_color'], $tracking_settings);?>
                    <div style="margin: 64px 0;">
                        <div>
                            <div class="trackfree-sd-tt"><?php echo $tracking_settings['cont_status']; ?>: <?php echo $data['status'];?></div>
                        </div>
                        <div style="display: flex; justify-content: left;">
                            <div>
                                <?php if ($data['shipmentDetails']['status']) { ?>
                                    <div style="margin-bottom: 32px;">
                                        <div class="trackfree-sd-tl">
                                            <?php echo $data['shipmentDetails']['status'];?>
                                        </div>
                                        <div>
                                            <?php echo $data['shipmentDetails']['days'] ? $data['shipmentDetails']['days'] : $data['shipmentDetails']['date'];?>
                                        </div>
                                    </div>
                                <?php }
                                if ($tracking_settings['show_cr_nm'] == 1) { ?>
                                    <div>
                                        <div class="trackfree-sd-tl">
                                            <?php echo $tracking_settings['cont_carrier']; ?>
                                        </div>
                                        <div style="display: flex; align-items: center;">
                                            <div style="margin-right: 16px;">
                                                <img src="<?php echo $data['courierLogo'];?>" width="72px" height="72px" />
                                            </div>
                                            <div>
                                                <a href="<?php echo $data['carrierContact']['trackUrl'];?>" target="_blank" style="color: #2c2d33;"><?php echo $data['courierCaption'];?></a>
                                                <div style="margin-top: 6px;">
                                                    <a href="tel:<?php echo $data['carrierContact']['phoneNumber'];?>" style="color: #2c2d33;"><?php echo $data['carrierContact']['phoneNumber'];?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                                if ($tracking_settings['show_track_num'] == 1) { ?>
                                    <div style="margin-top: 32px;">
                                        <div class="trackfree-sd-tl">
                                            <?php echo $tracking_settings['cont_tracking_number']; ?>
                                        </div>
                                        <div>
                                            <?php echo $data['trackingNum'];?>
                                        </div>
                                    </div>
                                    <?php
                                }
                                if ($tracking_settings['show_product_info'] == 1 && isset($data['fulfillItems'])) {
                                   if (sizeof($data['fulfillItems']) > 0) {
                                       ?>
                                       <div style="margin-top: 32px;">
                                           <div class="trackfree-sd-tl">
                                               <?php echo $tracking_settings['cont_product']; ?>
                                           </div>
                                           <div>
                                               <?php
                                               foreach ($data['fulfillItems'] as $item) {
                                                   ?>
                                                   <div class="tfree-st-fl">
                                                       <div>
                                                           <img src="<?php echo $item['image'] ?>" alt="<?php echo $item['name'];?>" width="48" height="48" />
                                                       </div>
                                                       <div class="tfree-st-pn">
                                                           <?php echo 'x' . $item['quantity'] . ' ' . $item['name'];?>
                                                       </div>
                                                   </div>
                                                   <?php
                                               }
                                               ?>
                                           </div>
                                       </div>
                                       <?php
                                   }
                               } ?>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                    <?php
                    if ($tracking_settings['show_track_info'] == 1) {
                        show_tracking_timeline($data['trackDetails'], $tracking_settings['date_format'], $tracking_settings['time_format'], $data['courierCaption']);
                    }
                    ?>
                </div>
                <?php
                $i++;
            }
            ?>
            <?php if ($tracking_settings['show_rec_prds'] == 1 && sizeof($products) > 0) {
                show_rec_prds($products, $tracking_settings['cont_may_like']);
            } ?>
        </div>
    </div>
    <?php
}

function show_tracking_details($data, $tracking_settings, $products = [])
{
    ?>
    <div style="margin-top: 32px">
        <?php if ($data['orderId']) {?>
          <div style="margin: 64px; text-align: center">
              <h1><?php echo $tracking_settings['cont_order'];?> #<?php echo $data['orderId'];?></h1>
          </div>
        <?php } ?>
        <?php show_tracking_progress($data, $tracking_settings['pg_bar_color'], $tracking_settings);
        if ($data['trackingNum']) { ?>
            <div style="margin: 64px 0;">
                <div>
                    <div class="trackfree-sd-tt"><?php echo $tracking_settings['cont_status']; ?>: <?php echo $data['status'];?></div>
                </div>
                <div style="display: flex;">
                    <?php if ($tracking_settings['show_map'] == 1 && $data['currentLat'] && $data['currentLng']) { ?>
                    <div class="trackfree-cl-5" style="margin: 16px 0;">
                        <div id="trackfree_map"></div>
                    </div>
                    <?php } ?>
                    <div class="trackfree-cl-5 <?php if ($data['shipmentDetails']['status']) { ?>trackfree-sp-dt<?php } ?>">
                        <?php if ($data['shipmentDetails']['status']) { ?>
                            <div style="margin-bottom: 32px;">
                                <div class="trackfree-sd-tl">
                                    <?php echo $data['shipmentDetails']['status'];?>
                                </div>
                                <div>
                                    <?php echo $data['shipmentDetails']['days'] ? $data['shipmentDetails']['days'] : $data['shipmentDetails']['date'];?>
                                </div>
                            </div>
                            <?php
                        }
                        if ($tracking_settings['show_cr_nm'] == 1) { ?>
                            <div>
                                <div class="trackfree-sd-tl">
                                    <?php echo $tracking_settings['cont_carrier']; ?>
                                </div>
                                <div style="display: flex; align-items: center;">
                                    <div style="margin-right: 16px;">
                                        <img src="<?php echo $data['courierLogo'];?>" width="72px" height="72px" />
                                    </div>
                                    <div>
                                        <a href="<?php echo $data['carrierContact']['trackUrl'];?>" target="_blank" style="color: #2c2d33;"><?php echo $data['courierCaption'];?></a>
                                        <div style="margin-top: 6px;">
                                            <a href="tel:<?php echo $data['carrierContact']['phoneNumber'];?>" style="color: #2c2d33;"><?php echo $data['carrierContact']['phoneNumber'];?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        if ($tracking_settings['show_track_num'] == 1) { ?>
                        <div style="margin-top: 32px;">
                            <div class="trackfree-sd-tl">
                                <?php echo $tracking_settings['cont_tracking_number']; ?>
                            </div>
                            <div>
                                <?php echo $data['trackingNum'];?>
                            </div>
                        </div>
                        <?php }
                        if ($tracking_settings['show_product_info'] == 1 && isset($data['fulfillItems'])) {
                            if (sizeof($data['fulfillItems']) > 0) {
                                ?>
                                <div style="margin-top: 32px;">
                                    <div class="trackfree-sd-tl">
                                        <?php echo $tracking_settings['cont_product']; ?>
                                    </div>
                                    <div>
                                        <?php
                                        foreach ($data['fulfillItems'] as $item) {
                                            ?>
                                            <div class="tfree-st-fl">
                                                <div>
                                                    <img src="<?php echo $item['image'] ?>" alt="<?php echo $item['name'];?>" width="48" height="48" />
                                                </div>
                                                <div class="tfree-st-pn">
                                                    <?php echo 'x' . $item['quantity'] . ' ' . $item['name'];?>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                        } ?>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div>
            <?php
        }
        if ($tracking_settings['show_track_info'] == 1) {
            show_tracking_timeline($data['trackDetails'], $tracking_settings['date_format'], $tracking_settings['time_format'], $data['courierCaption']);
        }
        if ($tracking_settings['show_rec_prds'] == 1 && sizeof($products) > 0) {
            show_rec_prds($products, $tracking_settings['cont_may_like']);
        } ?>
    </div>
    <?php if ($tracking_settings['show_map'] == 1 && $data['currentLat'] && $data['currentLng']) {
        show_map($data['currentLat'], $data['currentLng'], $data['currentLocation']);
    }
}

function show_tracking_progress($data, $color, $tracking_settings)
{ ?>
    <style>
    .trackfree-pg-id>li.trackfree-pg-ct .trackfree-pg-bl, .trackfree-pg-id>li.trackfree-pg-ct .trackfree-pg-bl:after, .trackfree-pg-id>li.trackfree-pg-ct .trackfree-pg-bl:before {
        background-color: <?php echo $color; ?>;
    }
    .trackfree-pg-dn::after {
      content: "";
      position: absolute;
      z-index: 2;
      right: 0;
      top: 0;
      transform: translateX(50%);
      width: 24px;
      height: 24px;
      background-color: <?php echo $color; ?>;
      border-radius: 50%;
    }
    .trackfree-pg-sn::before {
      content: "";
      position: absolute;
      z-index: 1;
      left: -2px;
      height: 100%;
      border-left: 5px <?php echo $color;?> solid;
    }
    </style>
    <div class="trackfree-pg-hr">
        <ul class="trackfree-pg-id">
            <li class="trackfree-pg-ct">
                <span class="trackfree-pg-bl"></span>
                <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_ordered']; ?></span>
                <div class="trackfree-pg-dt"><?php echo $data['orderDate'];?></div>
            </li>
            <li <?php if ($data['shipmentDate']) { ?> class="trackfree-pg-ct" <?php } ?>>
                <span class="trackfree-pg-bl"></span>
                <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_order_ready']; ?></span>
                <div class="trackfree-pg-dt"><?php echo $data['shipmentDate'];?></div>
            </li>
            <li <?php if ($data['currentStatus'] > 1) { ?> class="trackfree-pg-ct" <?php } ?>>
                <span class="trackfree-pg-bl"></span>
                <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_in_transit']; ?></span>
                <div class="trackfree-pg-dt"><?php echo $data['transitDate'];?></div>
            </li>
            <li <?php if ($data['currentStatus'] > 2) { ?> class="trackfree-pg-ct" <?php } ?>>
                <span class="trackfree-pg-bl"></span>
                <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_out_for_delivery']; ?></span>
                <div class="trackfree-pg-dt"><?php echo $data['pickupDate'];?></div>
            </li>
            <li  <?php if ($data['currentStatus'] > 3) { ?> class="trackfree-pg-ct" <?php } ?>>
                <span class="trackfree-pg-bl"></span>
                <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_delivered']; ?></span>
                <div class="trackfree-pg-dt"><?php echo $data['deliveryDate'];?></div>
            </li>
        </ul>
    </div>

    <div class="trackfree-pg-vr">
      <ul class="trackfree-sp-ev">
        <li>
            <span class="trackfree-pg-dn">
            </span>
            <span class="trackfree-pg-sn">
             <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_ordered']; ?></span>
             <div class="trackfree-pg-dt"><?php echo $data['orderDate'];?></div>
            </span>
        </li>
        <li>
            <span class=<?php echo $data['shipmentDate'] ? "trackfree-pg-dn" : "trackfree-pg-tm";?>>
            </span>
            <span class=<?php echo $data['shipmentDate'] ? "trackfree-pg-sn" : "trackfree-sp-sn";?>>
             <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_order_ready']; ?></span>
             <div class="trackfree-pg-dt"><?php echo $data['shipmentDate'];?></div>
            </span>
        </li>
        <li>
            <span class=<?php echo $data['currentStatus'] > 1 ? "trackfree-pg-dn" : "trackfree-pg-tm";?>>
            </span>
            <span class=<?php echo $data['currentStatus'] > 2 ? "trackfree-pg-sn" : "trackfree-sp-sn";?>>
             <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_in_transit']; ?></span>
             <div class="trackfree-pg-dt"><?php echo $data['transitDate'];?></div>
            </span>
        </li>
        <li>
            <span class=<?php echo $data['currentStatus'] > 2 ? "trackfree-pg-dn" : "trackfree-pg-tm";?>>
            </span>
            <span class=<?php echo $data['currentStatus'] > 3 ? "trackfree-pg-sn" : "trackfree-sp-sn";?>>
            <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_out_for_delivery']; ?></span>
             <div class="trackfree-pg-dt"><?php echo $data['pickupDate'];?></div>
            </span>
        </li>
        <li>
            <span class=<?php echo $data['currentStatus'] > 3 ? "trackfree-pg-dn" : "trackfree-pg-tm";?>>
            </span>
            <span class="trackfree-sp-sf">
            <span class="trackfree-pr-tx"><?php echo $tracking_settings['cont_delivered']; ?></span>
            <div class="trackfree-pg-dt"><?php echo $data['deliveryDate'];?></div>
            </span>
        </li>
      </ul>
    </div>
    <?php
}

function show_tracking_timeline($trackDetails, $date_format, $time_format, $courier_caption)
{ ?>
    <div style="margin-top: 32px">
      <ul class="trackfree-sp-ev">
        <?php $length = sizeof($trackDetails);
        $i = 0;
        $df = $tf = array(
            1 => 'M d, Y',
            2 => 'M d',
            3 => 'M jS, Y',
            4 => 'd M Y',
            5 => 'd-M-Y',
            6 => 'm/d/Y',
            7 => 'd/m/Y',
        );

        $datefm = $df[$date_format];

        $tf = array(
            1 => 'h:i a',
            2 => 'H:i',
        );
        $timefm = $tf[$time_format];

        $total_trans = 0;
        $tf_org_txt = [];
        $tf_rep_txt = [];
        $tf_tns_stn = get_option('trackfree_translate_strings');
        if ($tf_tns_stn) {
            $total_trans = sizeof ($tf_tns_stn['original_text']);
            $tf_org_txt = $tf_tns_stn['original_text'];
            $tf_rep_txt = $tf_tns_stn['replace_text'];
        }

        foreach ($trackDetails as $track) {
            $desc = $track['statusDescription'];
            $location = $track['city'];
            if ($total_trans > 0) {
                $desc = str_replace($tf_org_txt, $tf_rep_txt, $track['statusDescription']);
                $location = str_replace($tf_org_txt, $tf_rep_txt, $track['city']);
            }
            $track_date_time = $track['dateTime'] ? date($datefm . ' ' . $timefm, strtotime($track['dateTime'])) : '';
            if ($courier_caption == 'Shree Maruti Courier') {
                $track_date_time = $track['dateTime'] ? date($datefm, strtotime($track['dateTime'])) : '';
            }
            ?>
            <li>
            <span class="trackfree-sp-tm">
            </span>
            <span class=<?php echo $i + 1 === $length ? "trackfree-sp-sf" : "trackfree-sp-sn";?>>
              <span class="trackfree-st-at"><?php echo ucfirst(trim($desc));?></span>
              <span class="trackfree-st-at"><?php echo ucfirst(trim($location));?></span>
              <span class="trackfree-st-tm"><?php echo $track_date_time; ?></span>
            </span>
            </li>
            <?php $i++;?>
        <?php } ?>
      </ul>
    </div>
    <?php
}

function show_rec_prds($products, $title)
{ ?>
    <h2 style="text-align: center; margin: 32px 0;"><?php echo $title; ?></h2>
    <div class="trackfree-rp-cn">
        <?php foreach ($products as $product) { ?>
        <div class="trackfree-rp-ct" onclick="window.open('<?php echo $product['slug'];?>')">
            <img class="trackfree_prd_img" src="<?php echo $product['image'];?>" />
            <div class="trackfree-rp-pd">
                <?php echo $product['name'];?>
            </div>
            <div class="trackfree-rp-pc">
                <?php echo wc_price($product['price']);?>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php
}

function show_map($lat, $lng, $loc)
{ ?>
    <script>
    var map = L.map('trackfree_map').setView([<?php echo $lat;?>, <?php echo $lng;?>], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    L.marker([<?php echo $lat;?>, <?php echo $lng;?>]).addTo(map)
        .bindPopup('<b>Current location</b><br> <?php echo $loc;?>')
        .openPopup();
    </script>
    <?php
}

function get_recommend_products($order_id)
{
    $recommend_products = array();
    $exclude_item = array();
    $categories = array();

    $order = wc_get_order($order_id);
    if ($order) {
        foreach ($order->get_items() as $item_key => $item_values) {
            $item_data = $item_values->get_data();
            $product_data = wc_get_product($item_data['product_id']);
            if (!empty($product_data)) {
                $exclude_item[] = $item_data['product_id'];

                if ($product_data->category_ids) {
                    foreach ($product_data->category_ids as $category) {
                        $categories[] = trackfree_get_category_by_id($category);
                    }
                }
            }
        }
    }

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

    $store_products = wc_get_products($args);

    if (sizeof($store_products) < 4) {
        $args = array(
            'status' => 'publish',
            'orderby' => 'rand',
            'limit' => 12,
            'return' => 'ids',
            'exclude' => $exclude_item,
        );
        $store_products = wc_get_products($args);
    }

    foreach ($store_products as $product_id) {
        $product = wc_get_product($product_id);
        if (!empty($product)) {
            $image_url = get_the_post_thumbnail_url($product->get_id(), 'medium');

            if ($image_url) {
                $recommend_products[] = array(
                    'product_id' => $product->get_id(),
                    'name' => $product->get_name(),
                    'slug' => get_permalink($product_id),
                    'price' => $product->get_price(),
                    'sku' => $product->get_sku(),
                    'image' => $image_url,
                );

                if (count($recommend_products) === 4) {
                    break;
                }
            }
        }
    }

    return $recommend_products;
}

function show_trackfree_order_details($data, $tracking_settings, $products = [])
{
    ?>
    <div>
        <div style="margin: 64px 0 32px;">
            <div>
                Order #<strong><?php echo $data['id'];?></strong> was placed on <strong><?php echo date('F d, Y', strtotime($data['date_created']));?></strong> and is currently <strong><?php echo ucfirst($data['status']);?></strong>.
            </div>
        </div>
        <div>
            <h3>Order details</h3>
            <table>
                <tbody>
                    <?php
                    $subtotal = 0;
                    foreach ($data['line_items'] as $item) {
                        $subtotal += $item['total'];
                        ?>
                        <tr>
                            <td>
                                <?php echo $item['name'];?> <strong>x <?php echo $item['quantity'];?></strong>
                            </td>
                            <td style="text-align: right;">
                                <?php echo wc_price($item['total']);?>
                            </td>
                        </tr>
                        <?php
                    } ?>
                    <tr>
                        <td>
                            <strong>Subtotal</strong>
                        </td>
                        <td style="text-align: right;">
                            <?php echo wc_price($subtotal);?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Shipping</strong>
                        </td>
                        <td style="text-align: right;">
                            <?php echo $data['shipping_total'] ? wc_price($data['shipping_total']) : 'Free shipping';?>
                        </td>
                    </tr>
                    <?php if ($data['total_tax'] > 0) { ?>
                        <tr>
                            <td>
                                <strong>Subtotal</strong>
                            </td>
                            <td style="text-align: right;">
                                <?php echo wc_price($data['total_tax']);?>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ($data['discount_total'] > 0) { ?>
                        <tr>
                            <td>
                                <strong>Discount</strong>
                            </td>
                            <td style="text-align: right;">
                                <?php echo wc_price($data['discount_total']);?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>
                            <strong>Total</strong>
                        </td>
                        <td style="text-align: right;">
                            <?php echo $data['total'];?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
            <div class="trackfree-cl-6">
                <h3>Billing address</h3>
                <p>
                    <?php $billing_address = $data['billing']['first_name'] . ' ' . $data['billing']['last_name'] . '<br />';
                    $billing_address .= $data['billing']['address_1'] . '<br />';
                    if ($data['billing']['address_2']) {
                        $billing_address .= $data['billing']['address_2'] . '<br />';
                    }
                    $billing_address .= $data['billing']['city'] . ' ' .  $data['billing']['postcode'] . '<br />' . $data['billing']['state'];
                    $billing_address .= '<br /><span class="woocommerce-customer-details--phone">' . $data['billing']['phone'] . '</span>';
                    $billing_address .= '<br /><span class="woocommerce-customer-details--email">' . $data['billing']['email'] . '</span>';
                    echo $billing_address;?>
                </p>
            </div>
            <div class="trackfree-cl-6">
                <h3>Shipping address</h3>
                <p>
                    <?php $shipping_address = $data['shipping']['first_name'] . ' ' . $data['shipping']['last_name'] . '<br />';
                    $shipping_address .= $data['shipping']['address_1'] . '<br />';
                    if ($data['shipping']['address_2']) {
                        $shipping_address .= $data['shipping']['address_2'] . '<br />';
                    }
                    $shipping_address .= $data['shipping']['city'] . ' ' .  $data['shipping']['postcode'] . '<br />' . $data['shipping']['state'];
                    echo $shipping_address;?>
                </p>
            </div>
        </div>
        <div style="clear: both;"></div>
        <?php if ($tracking_settings['show_rec_prds'] == 1 && sizeof($products) > 0) {
            show_rec_prds($products, $tracking_settings['cont_may_like']);
        } ?>
    </div>
    <?php
}
?>
