jQuery(document).on('click', '.trackfree_od_tl', function() {
    jQuery('.trackfree_sf_dt').hide();
    var id = this.id.split('_');
    jQuery('#tfodts_' + id[1]).show();
    jQuery('.trackfree_od_tl').removeClass('trackfree_od_tl trackfree_od_active').addClass('trackfree_od_tl');
    jQuery('#tfodls_' + id[1]).removeClass('trackfree_od_tl').addClass('trackfree_od_tl trackfree_od_active');
});

jQuery(document).on('click', '#tf_od_tb', function() {
    jQuery(this).removeClass('trackfree-tb-cn').addClass('trackfree-tb-cn trackfree-sl-tb');
    jQuery('#tf_tn_tb').removeClass('trackfree-tb-cn trackfree-sl-tb').addClass('trackfree-tb-cn');
    jQuery('#tf_od_ct').show();
    jQuery('#tf_tk_ct').hide();
});

jQuery(document).on('click', '#tf_tn_tb', function() {
    jQuery(this).removeClass('trackfree-tb-cn').addClass('trackfree-tb-cn trackfree-sl-tb');
    jQuery('#tf_od_tb').removeClass('trackfree-tb-cn trackfree-sl-tb').addClass('trackfree-tb-cn');
    jQuery('#tf_od_ct').hide();
    jQuery('#tf_tk_ct').show();
});

jQuery(function () {
    jQuery.urlParam = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.search);
        return (results !== null) ? results[1] || 0 : false;
    }

    if (jQuery.urlParam('tracking_num') !== false) {
        jQuery('#tf_tn_tb').removeClass('trackfree-tb-cn').addClass('trackfree-tb-cn trackfree-sl-tb');
        jQuery('#tf_od_tb').removeClass('trackfree-tb-cn trackfree-sl-tb').addClass('trackfree-tb-cn');
        jQuery('#tf_od_ct').hide();
        jQuery('#tf_tk_ct').show();

        jQuery('#tfree_tracking_number').val(jQuery.urlParam('tracking_num'));
        jQuery('#track_data').html('<div class="trackfree_loader">Loading...</div>');
        var tfree_tracking_number = jQuery.urlParam('tracking_num');
        jQuery.ajax({
            type: 'POST',
            data: {
                action: 'get_shipment_data',
                tfree_tracking_number: tfree_tracking_number,
                preview: jQuery.urlParam('preview')
            },
            url: ajax_object.ajax_url,
            success: function(data) {
                jQuery('#track_data').html(data);
                jQuery('html,body').animate({
                scrollTop: jQuery("#track_data").offset().top},
                'slow');
            },
            error: function(response) {
                jQuery('#track_data').html('');
            }
        });
    }

    jQuery('#tfree_shipment_track').click(function() {
        var tfree_tracking_number = jQuery('#tfree_tracking_number').val();
        if (tfree_tracking_number) {
            jQuery('#track_data').html('<div class="trackfree_loader">Loading...</div>');
            jQuery.ajax({
                type: 'POST',
                data: {
                    action: 'get_shipment_data',
                    tfree_tracking_number: tfree_tracking_number,
                    preview: jQuery.urlParam('preview')
                },
                url: ajax_object.ajax_url,
                success: function(data) {
                    jQuery('#track_data').html(data);
                    jQuery('html,body').animate({
                    scrollTop: jQuery("#track_data").offset().top},
                    'slow');
                },
                error: function(response) {
                    jQuery('#track_data').html('');
                }
            });
        }
    });

    jQuery('#tfree_order_track').click(function() {
        var tfree_order_number = jQuery('#tfree_order_number').val();
        var tfree_email = jQuery('#tfree_email').val();
        if (tfree_order_number && tfree_email) {
            jQuery('#track_data').html('<div class="trackfree_loader">Loading...</div>');
            jQuery.ajax({
                type: 'POST',
                data: {
                    action: 'get_shipment_data',
                    tfree_order_number: tfree_order_number,
                    tfree_email: tfree_email
                },
                url: ajax_object.ajax_url,
                success: function(data) {
                    jQuery('#track_data').html(data);
                    jQuery('.trackfree_sf_dt').hide();
                    jQuery('#tfodts_1').show();
                    jQuery('.trackfree_sf_dt').removeClass('active');
                    jQuery('#tfodls_1').addClass('trackfree_sf_dt trackfree_od_active').removeClass('trackfree_sf_dt');
                    jQuery('html,body').animate({
                    scrollTop: jQuery("#track_data").offset().top},
                    'slow');
                },
                error: function(response) {
                    jQuery('#track_data').html('');
                }
            });
        }
    });
});
