<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$track_by_order_email = 1;
$track_by_tk_num = 1;
$container_width = '1200px';
$width_type = 'px';

$cont_order_number = 'Order Number';
$cont_tracking_number = 'Tracking Number';
$cont_email = 'Email';
$cont_track = 'Track';

$text_above = '';
$text_below = '';

$custom_css = '';
$custom_html_above = '';
$custom_html_below = '';

$show_trackfree_branding = 1;

$tf_dis_opt = get_option('trackfree_display_options');
if ($tf_dis_opt) {
    $show_trackfree_branding = isset($tf_dis_opt['show_trackfree_branding']) ? $tf_dis_opt['show_trackfree_branding'] : 1;
    $track_by_order_email = isset($tf_dis_opt['by_ord_num_email']) ? $tf_dis_opt['by_ord_num_email'] : 1;
    $track_by_tk_num = isset($tf_dis_opt['by_tk_num']) ? $tf_dis_opt['by_tk_num'] : 1;

    if (isset($tf_dis_opt['width_type'])) {
        $width_type = $tf_dis_opt['width_type'] == 'pixel' ? 'px' : '%';
    }

    $container_width = isset($tf_dis_opt['cont_width']) ? $tf_dis_opt['cont_width'] : '1200' . '' . $width_type;
}

$tf_tns_str = get_option('trackfree_trans_strings');

if ($tf_tns_str) {
    $cont_order_number = isset($tf_tns_str['order_number']) ? $tf_tns_str['order_number'] : 'Order Number';
    $cont_tracking_number = isset($tf_tns_str['tracking_number']) ? $tf_tns_str['tracking_number'] : 'Tracking Number';
    $cont_email = isset($tf_tns_str['email']) ? $tf_tns_str['email'] : 'Email';
    $cont_track = isset($tf_tns_str['track']) ? $tf_tns_str['track'] : 'Track';
}

$tf_add_txt = get_option('trackfree_additional_texts');
if ($tf_add_txt) {
    $text_above = isset($tf_add_txt['text_above']) ? $tf_add_txt['text_above'] : '';
    $text_below = isset($tf_add_txt['text_below']) ? $tf_add_txt['text_below'] : '';
}

$tf_cus_csh = get_option('trackfree_custom_css_and_html');
if ($tf_cus_csh) {
    $custom_css = isset($tf_cus_csh['custom_css']) ? $tf_cus_csh['custom_css'] : '';
    $custom_html_above = isset($tf_cus_csh['custom_html_above']) ? $tf_cus_csh['custom_html_above'] : '';
    $custom_html_below = isset($tf_cus_csh['custom_html_below']) ? $tf_cus_csh['custom_html_below'] : '';
}

if ($custom_css) {
    ?>
    <style>
    <?php echo $custom_css; ?>
    </style>
    <?php
}
?>
<div class="trackfree-mn-ct" <?php if ($width_type == 'px') { ?>style="max-width: <?php echo $container_width ;?>"<?php } else { ?>style="width: <?php echo $container_width ;?>"<?php } ?>>
    <?php echo $custom_html_above ? '<p>' . stripslashes($custom_html_above) . '</p>' : ''; ?>
    <?php echo $text_above ? '<p>' . $text_above . '</p>' : ''; ?>
    <div class="trackfree-mi-cn">
        <?php if ($track_by_order_email == 1 && $track_by_tk_num == 1) { ?>
            <div class="trackfree-ct-tb">
                <div id="tf_od_tb" class="trackfree-tb-cn trackfree-sl-tb">
                    <?php echo $cont_order_number; ?>
                </div>
                <div id="tf_tn_tb" class="trackfree-tb-cn">
                    <?php echo $cont_tracking_number; ?>
                </div>
            </div>
        <?php } ?>
        <div class="trackfree-tk-ct">
            <?php if ($track_by_order_email == 1) { ?>
                <div class="trackfree-tf-cl" id="tf_od_ct">
                    <form method="get">
                        <div>
                            <div>
                                <div>
                                    <?php echo $cont_order_number; ?>
                                </div>
                                <div>
                                    <input type="text" value="" name="tfree_order_number" id="tfree_order_number" />
                                </div>
                            </div>
                            <div style="margin-top: 16px;">
                                <div>
                                    <?php echo $cont_email; ?>
                                </div>
                                <div>
                                    <input type="email" value="" name="tfree_email" id="tfree_email" />
                                </div>
                            </div>
                            <div style="margin-top: 16px;">
                                <button type="button" name="tfree_order_track" id="tfree_order_track" class="button trackfree-tk-bn"><?php echo $cont_track ?></button>
                            </div>
                            <?php if ($show_trackfree_branding == 1) {
                                ?>
                                <div style="margin-top: 8px;">
                                    <a href="https://trackfree.io/?src=wp-track-page" class="trackfree-br-sl" target="_blank">Powered by TrackFree</a>
                                </div>
                                <?php
                            } ?>
                        </div>
                    </form>
                </div>
            <?php }
            if ($track_by_tk_num == 1) { ?>
                <div class="trackfree-tf-cl" id="tf_tk_ct" <?php if ($track_by_order_email == 1 && $track_by_tk_num == 1) { ?> style="display: none;" <?php } ?>>
                    <form method="get">
                        <div>
                            <div>
                                <div>
                                    <?php echo $cont_tracking_number ?>
                                </div>
                                <div>
                                    <input type="text" name="tfree_tracking_number" id="tfree_tracking_number" />
                                </div>
                            </div>
                            <div style="margin-top: 16px;">
                                <button type="button" name="tfree_shipment_track" id="tfree_shipment_track" class="button trackfree-tk-bn"><?php echo $cont_track ?></button>
                            </div>
                            <?php if ($show_trackfree_branding == 1) {
                                ?>
                                <div style="margin-top: 8px;">
                                    <a href="https://trackfree.io/?src=wp-track-page" class="trackfree-br-sl" target="_blank">Powered by TrackFree</a>
                                </div>
                                <?php
                            } ?>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php echo $text_below ? '<p>' . $text_below . '</p>' : ''; ?>
    <div>
        <div id="track_data" style="padding: 16px 0">

        </div>
    </div>
    <?php echo $custom_html_below ? '<p>' . stripslashes($custom_html_below) . '</p>' : ''; ?>
</div>
