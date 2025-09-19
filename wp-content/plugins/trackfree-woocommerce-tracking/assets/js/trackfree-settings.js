jQuery(document).ready(function($) {
    $('#tfree_live_chat, #tfree_support_widget').click(function() {
        FreshworksWidget('show', 'launcher');
        FreshworksWidget('open');
        FreshworksWidget('identify', 'ticketForm', {
          name: ajax_object.store_name,
          email: ajax_object.admin_email,
        });
    });

    function updateActiveSubmenu() {
        const currentHash = window.location.hash || '#/home';
        const submenuItems = $('#toplevel_page_trackfree .wp-submenu li a');
        submenuItems.removeClass('tfree-current');
        submenuItems.each(function() {
            const href = $(this).attr('href');
            if (href.endsWith(currentHash)) {
                $(this).addClass('tfree-current');
            }
        });
    }

    updateActiveSubmenu();

    $(window).on('hashchange', function() {
        updateActiveSubmenu();
    });

    $(document).on('click', '.tfree_nav_shipments', function() {
        updateActiveSubmenu();
    });

    var modal = $('#tfree-add-track-modal');
    $(document).on('click', '.tf-show-add-track', function() {
        var id = this.id;
        $.ajax({
            type: 'POST',
            data: {
                action: 'tfree_add_shipment',
                order_id: id
            },
            url: ajax_object.ajax_url,
            success: function(data) {
                modal.show();
                $('#tfree-add-ship-cont').html(data);
                var total_order_items = $('#total_order_items').val();
                if (total_order_items == 0) {
                    $('#trackfree_btn_add_tracking').attr('disabled', true);
                } else {
                    $('#trackfree_btn_add_tracking').attr('disabled', false);
                }
            }
        });
    });

    var modal2 = $('#tfree-edit-track-modal');
    $(document).on('click', '.trackfree_edit_tracking', function() {
        modal2.show();
        var id = this.id;
        var order_id = $('#order_id').val();
        $.ajax({
            type: 'POST',
            data: {
                action: 'tfree_edit_shipment',
                shipment_id: id,
                order_id: order_id
            },
            url: ajax_object.ajax_url,
            success: function(data) {
                $('#tfree-edit-ship-cont').html(data);
            }
        });
    });

    $('.tfree-cm-modal-close').on('click', function() {
        modal.hide();
        modal2.hide();
        $('.tfree-empty-line-error').hide();
    });

    $(window).on('click', function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
        if ($(event.target).is(modal2)) {
            modal2.hide();
        }
    });

    $(document).on('change', '.tfree-product-qty', function() {
        $('.tfree-empty-line-error').hide();
    });

    $(document).on('change', '.tfree-edit-product-qty', function() {
        $('.tfree-empty-line-error').hide();
    });

    $('#trackfree_btn_add_tracking').click(function() {
        $('.tfree-add-shipment-error').hide();
        $('.tfree-invalid-shipment-error').hide();
        let products = [];
        let totalQty = 0;
        $('.tfree-prd-items tbody tr').each(function() {
            let product = {};
            product.id = $(this).find('.tfree-product-id').val();
            product.variation_id = $(this).find('.tfree-variation-id').val();
            product.quantity = $(this).find('.tfree-product-qty').val();
            product.order_items = $(this).find('.tfree-order-items').val();
            products.push(product);
            totalQty += Number(product.quantity);
        });

        var trackfree_tracking_number = $('#trackfree_tracking_number').val();
        var trackfree_courier_name = $("select#trackfree_courier_name option").filter(":selected").val();
        var trackfree_order_status = $("select#trackfree_order_status option").filter(":selected").val();
        var ord_id = $('#ord_id').val();
        var redirect_url = $('#redirect_url').val();
        if (trackfree_tracking_number) {
            if (trackfree_tracking_number.length > 40) {
                $('.tfree-invalid-shipment-error').show();
                return false;
            }

            if (totalQty == 0) {
                $('.tfree-empty-line-error').show();
                return false;
            }

            $.ajax({
                type: 'POST',
                data: {
                    action: 'add_new_shipment',
                    ord_id: ord_id,
                    trackfree_tracking_number: trackfree_tracking_number,
                    trackfree_courier_name: trackfree_courier_name,
                    trackfree_order_status: trackfree_order_status,
                    fulfilment_items: products
                },
                url: ajax_object.ajax_url,
                success: function(data) {
                    if (data != 'success') {
                        $('.tfree-add-shipment-error').show();
                        $('.tfree-add-shipment-data').html(data);
                    } else {
                        window.location.href=redirect_url;
                    }
                }
            });
        }
    });

    $('#trackfree_btn_update_tracking').click(function() {
        $('.tfree-invalid-shipment-error').hide();
        $('.tfree-add-shipment-error').hide();
        let products = [];
        let totalQty = 0;
        $('.tfree-edit-prd-items tbody tr').each(function() {
            let product = {};
            product.quantity = $(this).find('.tfree-edit-product-qty').val();
            product.total_quantity = $(this).find('.edit-total-qty').val();
            products.push(product);
            totalQty += Number(product.quantity);
        });

        var trackfree_tracking_number = $('#tfree_edit_track_num').val();
        var trackfree_courier_name = $("select#tfree_edit_courier_name option").filter(":selected").val();
        var trackfree_order_status = $("select#trackfree_edit_order_status option").filter(":selected").val();
        var ord_id = $('#edit_ord_id').val();
        var shipment_id = $('#edit_shipment_id').val();

        if (trackfree_tracking_number) {
            if (trackfree_tracking_number.length > 40) {
                $('.tfree-invalid-shipment-error').show();
                return false;
            }

            if (totalQty == 0) {
                $('.tfree-empty-line-error').show();
                return false;
            }

            $.ajax({
                type: 'POST',
                data: {
                    action: 'tfree_update_shipment',
                    ord_id: ord_id,
                    shipment_id: shipment_id,
                    trackfree_tracking_number: trackfree_tracking_number,
                    trackfree_courier_name: trackfree_courier_name,
                    trackfree_order_status: trackfree_order_status,
                    fulfilment_items: products
                },
                url: ajax_object.ajax_url,
                success: function(data) {
                    if (data != 'success') {
                        $('.tfree-add-shipment-error').show();
                        $('.tfree-add-shipment-data').html(data);
                    } else {
                        window.location.reload()
                    }
                }
            });
        }
    });
});

jQuery(function () {
    var appendthis =  ("<div class='modal-overlay trackfree-modal-close'></div>");

    jQuery('a[data-tf-modal-id]').click(function(e) {
        e.preventDefault();
        jQuery("body").append(appendthis);
        jQuery(".trackfree-modal-overlay").fadeTo(500, 0.7);
        var modalBox = jQuery(this).attr('data-tf-modal-id');
        jQuery('#'+modalBox).fadeIn(jQuery(this).data());
    });

    jQuery('a[data-modal-id]').click(function(e) {
        jQuery('#trackfree-shipment-content').html('<img src="https://tfree.sfo2.cdn.digitaloceanspaces.com/default/tf-wp-preloader.gif" alt="Please wait...">');
        var id = this.id;
        e.preventDefault();
        jQuery("body").append(appendthis);
        jQuery(".trackfree-modal-overlay").fadeTo(500, 0.7);
        var modalBox = jQuery(this).attr('data-modal-id');
        jQuery('#'+modalBox).fadeIn(jQuery(this).data());
        jQuery.ajax({
            type: 'POST',
            data: {
                action: 'get_shipment_action',
                order_id: id
            },
            url: ajax_object.ajax_url,
            success: function(data) {
                jQuery('#trackfree-shipment-content').html(data);
            }
        });
    });

    jQuery(".trackfree-modal-close, .trackfree-modal-overlay").click(function() {
        jQuery(".trackfree-modal-box, .trackfree-modal-overlay").fadeOut(500, function() {
            jQuery(".trackfree-modal-overlay").remove();
        });
    });

    jQuery(window).resize(function() {
        jQuery(".trackfree-modal-box").css({
            top: (jQuery(window).height() - jQuery(".trackfree-modal-box").outerHeight()) / 2,
            left: (jQuery(window).width() - jQuery(".trackfree-modal-box").outerWidth()) / 2
        });
    });

    jQuery(window).resize();

    jQuery('.trackfree_delete_tracking').click(function() {
        var cfm = confirm('Are you sure you want to delete?');
        if (cfm) {
            var id = this.id;
            var order_id = jQuery('#order_id').val();
            jQuery.ajax({
                type: 'POST',
                data: {
                    action: 'tracking_delete_action',
                    track_id: id,
                    order_id: order_id
                },
                url: ajax_object.ajax_url,
                success: function(data) {
                    location.reload();
                }
            });
        }
    });

    jQuery('.trackfree_show_shipment_detail').click(function() {
        jQuery("#tf_ship_detail_" + this.id).html('<img src="https://tfree.sfo2.cdn.digitaloceanspaces.com/default/tf-wp-preloader.gif" alt="Please wait...">');
        var shipment_id = this.id;
        var order_id = jQuery('#order_id').val();
        jQuery.ajax({
            type: 'POST',
            data: {
                action: 'show_shipment_detail_action',
                shipment_id: shipment_id,
                order_id: order_id
            },
            url: ajax_object.ajax_url,
            success: function(data) {
                jQuery("#tf_ship_detail_" + shipment_id).html(data);
            }
        });
    });
});

jQuery(document).on('click', '.trackfree_ord_det_menu_item', function() {
    var id  = this.id.split('_');
    if (id[0] == 'shipping-link')
    {
        jQuery('#carrier-link_' + id[1]).removeClass('active');
        jQuery('#carrier-detail_' + id[1]).removeClass('trackfree_active');
        jQuery('#shipping-detail_' + id[1]).addClass('trackfree_active');
    }
    if (id[0] == 'carrier-link')
    {
        jQuery('#shipping-link_' + id[1]).removeClass('active');
        jQuery('#shipping-detail_' + id[1]).removeClass('trackfree_active');
        jQuery('#carrier-detail_' + id[1]).addClass('trackfree_active');
    }
    jQuery(this).addClass('active');
});

jQuery(document).on('click', '.trackfree_shipement_menu_item', function() {
    var id = this.id;
    var new_id = id.replace('shipment-item-link_', 'shipment-detail_');
    jQuery('.trackfree_shipement_menu_item').removeClass('active');
    jQuery(this).addClass('active');
    jQuery('.trackfree-shipment-content').removeClass('trackfree_active');
    jQuery('#' + new_id).addClass('trackfree_active');
});

jQuery(document).on('click', '.tf-btn-show-more', function() {
    var id  = this.id.split('_');
    jQuery('#tf-show-more-data_' + id[1]).show();
    jQuery(this).hide();
    jQuery('#tf-btn-hide-more_' + id[1]).show();
});

jQuery(document).on('click', '.tf-btn-hide-more', function() {
    var id  = this.id.split('_');
    jQuery('#tf-show-more-data_' + id[1]).hide();
    jQuery(this).hide();
    jQuery('#tf-btn-show-more_' + id[1]).show();
});
