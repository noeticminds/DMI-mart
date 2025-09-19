<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function show_shipment_details($track_values, $row, $type, $style = '')
{
    $track_details_length = $track_values ? sizeof($track_values) : 0;
    if ($track_details_length > 0) {
        if ($track_details_length > 3) {
            $track_details_data_1 = array_slice($track_values, 0, 3);
            $track_details_data_2 = array_slice($track_values, 3, $track_details_length);
            ?>
            <div class="shipment-container" id="shipment_container_<?php echo $row;?>" style="<?php echo $style;?>">
                <table class="trackfree-table" style="padding:10px 0;">
                    <?php
                    foreach ($track_details_data_1 as $track_detail_data_1) { ?>
                        <tr>
                            <td class="trackfree-shipment-date" style="width:30%">
                                <span><?php echo $track_detail_data_1['date'] . '<br />' . $track_detail_data_1['time']; ?></span>
                            </td>
                            <td>
                                <span class="trackfree-shipment-description"><?php echo $track_detail_data_1['statusDescription']; ?></span>
                                <br>
                                <span class="trackfree-shipment-description trackfree-shipment-address"><?php echo $track_detail_data_1['location']; ?></span>
                            </td>
                        </tr>
                        <?php
                    } ?>
                </table>
                <a href="javascript:void(0);"><input type="button" class="<?php echo $type;?>-btn-show-more button action" id="<?php echo $type;?>-btn-show-more_<?php echo $row;?>" value="<?php _e('See all shipping activity', 'trackfree-woocommerce-tracking');?>" style="margin: 16px 0 8px;"/></a>
                <div class="trackfree-show-more-container" id="<?php echo $type;?>-show-more-data_<?php echo $row;?>">
                    <table class="trackfree-table" style="width: 100%">
                        <?php
                        foreach ($track_details_data_2 as $track_detail_data_2) { ?>
                            <tr>
                                <td class="trackfree-shipment-date" style="width:30%">
                                    <span><?php echo $track_detail_data_2['date'] . '<br />' . $track_detail_data_2['time']; ?></span>
                                </td>
                                <td>
                                    <span class="trackfree-shipment-description"><?php echo $track_detail_data_2['statusDescription']; ?></span>
                                    <br>
                                    <span class="trackfree-shipment-description trackfree-shipment-address"><?php echo $track_detail_data_2['location']; ?></span>
                                </td>
                            </tr>
                            <?php
                        } ?>
                    </table>
                </div>
            </div>
            <a href="javascript:void(0);"><input type="button" class="<?php echo $type;?>-btn-hide-more button action" id="<?php echo $type;?>-btn-hide-more_<?php echo $row;?>" value="<?php _e('Hide all shipping activity', 'trackfree-woocommerce-tracking');?>" style="display: none; margin: 10px;"/></a>
            <?php
        } else { ?>
            <table class="trackfree-table" style="width: 100%; padding:10px 0;">
                <?php
                foreach ($track_values as $track_detail_data) { ?>
                    <tr>
                        <td class="trackfree-shipment-date" style="width:30%">
                            <span><?php echo $track_detail_data['date'] . '<br />' . $track_detail_data['time']; ?></span>
                        </td>
                        <td>
                            <span class="trackfree-shipment-description"><?php echo $track_detail_data['statusDescription']; ?></span>
                            <br>
                            <span class="trackfree-shipment-description trackfree-shipment-address"><?php echo $track_detail_data['location']; ?></span>
                        </td>
                    </tr>
                    <?php
                } ?>
            </table>
            <?php
        }
    } else {
        ?>
        <table style="width: 100%;">
            <tr>
                <td class="tfree-py-16"><?php _e('No track details available', 'trackfree-woocommerce-tracking');?></td>
            </tr>
        </table>
        <?php
    }
}
