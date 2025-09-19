<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('wp_ajax_tfree_get_all_carriers', 'tfree_get_all_carriers');
    add_action('wp_ajax_nopriv_tfree_get_all_carriers', 'tfree_get_all_carriers');

    function tfree_get_all_carriers()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $json_data = file_get_contents( plugins_url('/trackfree-woocommerce-tracking/assets/js/trackfree_couriers.json'));
        $all_options = json_decode($json_data, true);
        $carriers = [];
        $preferred_courier_list = [];
        $preferred_couriers = get_option('trackfree_preferred_couriers');
        if ($preferred_couriers) {
            $preferred_courier_list = explode(',', $preferred_couriers);
        }
        foreach ($all_options as $option) {
            if (!in_array($option['name'], $preferred_courier_list)) {
                $carriers[] = $option['name'];
            }
        }
        $response = array(
            'status' => 'success',
            'carriers' => $carriers
        );
        wp_send_json_success($response);
        wp_die();
    }

    add_action('wp_ajax_tfree_carrier_update', 'tfree_carrier_update');
    add_action('wp_ajax_nopriv_tfree_carrier_update', 'tfree_carrier_update');

    function tfree_carrier_update()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $trackfree_options = '';

        $tf_post_data = file_get_contents('php://input');
        $request_data = json_decode($tf_post_data, true);

        if ($request_data['trackfree_preferred_couriers']) {
            $trackfree_options = sanitize_text_field($request_data['trackfree_preferred_couriers']);
        }
        add_option('trackfree_preferred_couriers');
        update_option('trackfree_preferred_couriers', $trackfree_options);

        $trackfree_account_api_key = get_option('trackfree_account_api_key');
        wp_remote_post(trackfree_url() . '/api/wc_update_couriers', array(
            'sslverify' => false,
            'timeout' => 15,
            'body' => array(
                'key' => $trackfree_account_api_key,
                'couriers' => $request_data['trackfree_preferred_couriers']
            )
        ));
        $response = array(
            'status' => 'success',
            'message' =>  __('Preferred couriers updated successfully', 'trackfree-woocommerce-tracking')
        );
        wp_send_json_success($response);
        wp_die();
    }

    add_action('wp_ajax_tfree_get_tfree_settings', 'tfree_get_tfree_settings');
    add_action('wp_ajax_nopriv_tfree_get_tfree_settings', 'tfree_get_tfree_settings');

    function tfree_get_tfree_settings()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $plugin_data = get_plugin_data(dirname(__FILE__) . '/trackfree-woocommerce-tracking.php');
        $plugin_version = $plugin_data['Version'];

        $response_data = array(
            'show_shipment_status_order_list' => get_option('trackfree_shipment_status_in_orders'),
            'show_shipment_details_order_details' => get_option('trackfree_shipment_details_in_order_details'),
            'auto_order_status_change' => get_option('trackfree_auto_order_status_update'),
            'plugin_version' => $plugin_version
        );

        $response = array(
            'status' => 'success',
            'settings' => $response_data
        );
        wp_send_json_success($response);
        wp_die();
    }

    add_action('wp_ajax_tfree_update_general_settings', 'tfree_update_general_settings');
    add_action('wp_ajax_nopriv_tfree_update_general_settings', 'tfree_update_general_settings');

    function tfree_update_general_settings()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $message_type = '';
        $message = '';

        $trackfree_account_api_key = get_option('trackfree_account_api_key');

        $tf_post_data = file_get_contents('php://input');
        $request_data = json_decode($tf_post_data, true);

        $general_setting_data = array(
            'trackfree_storename' => $request_data['trackfree_storename'],
            'trackfree_email' => $request_data['trackfree_email'],
            'delivered_mail_to_customer' => $request_data['delivered_mail_to_customer'],
            'show_shipment_status_order_list' =>  $request_data['show_shipment_status_order_list'],
            'show_shipment_details_order_details' =>  $request_data['show_shipment_details_order_details'],
            'auto_order_status_change' => $request_data['auto_order_status_change']
        );
        $response = wp_remote_get(trackfree_url() . '/api/wc_general_settings?key=' . $trackfree_account_api_key,
            array(
                'sslverify' => false,
                'timeout' => 15,
                'body' => $general_setting_data
            )
        );
        $response_data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ($response_data['status'] == 'success') {
            add_option('trackfree_shipment_status_in_orders');
            update_option('trackfree_shipment_status_in_orders', $request_data['show_shipment_status_order_list']);

            add_option('trackfree_shipment_details_in_order_details');
            update_option('trackfree_shipment_details_in_order_details', $request_data['show_shipment_details_order_details']);

            add_option('trackfree_auto_order_status_update');
            update_option('trackfree_auto_order_status_update', $request_data['auto_order_status_change']);

            $message_type = 'success';
            $message = __('General settings updated successfully', 'trackfree-woocommerce-tracking');
            if (isset($response_data['verify_message'])) {
                $message_type = 'warning';
                $message = __('The new address will not be active until confirmed.', 'trackfree-woocommerce-tracking');
            }
        } else if ($response_data['status'] == 'error') {
            switch ($response_data['error_type']) {
                case "user_not_exist":
                    $message_type = 'error';
                    $message = __('Invalid authentication', 'trackfree-woocommerce-tracking');
                    break;
                case "invalid_email":
                    $message_type = 'error';
                    $message = sprintf(esc_html__('%1$s is not a valid email address', 'trackfree-woocommerce-tracking'), $request_data['trackfree_email']);
                    break;
                case "email_exists":
                    $message_type = 'error';
                    $message = __('Account already exists with this email address', 'trackfree-woocommerce-tracking');
                    break;
                case "all_fields_required":
                    $message_type = 'error';
                    $message = __('All fields required', 'trackfree-woocommerce-tracking');
                    break;
                case "invalid_request":
                   $message_type = 'error';
                   $message = __('Invalid request', 'trackfree-woocommerce-tracking');
                   break;
            }
        }

        $response = array(
            'status' => 'success',
            'message_type' => $message_type,
            'message' => $message
        );

        wp_send_json_success($response);
        wp_die();
    }

    add_action('wp_ajax_tfree_send_test_email', 'tfree_send_test_email');
    add_action('wp_ajax_nopriv_tfree_send_test_email', 'tfree_send_test_email');

    function tfree_send_test_email()
    {
        if (!isset($_SERVER['HTTP_X_WP_NONCE']) || !wp_verify_nonce($_SERVER['HTTP_X_WP_NONCE'], 'tfree_nonce')) {
            wp_send_json_error(array('status' => 'error'));
            wp_die();
        }

        $message_type = '';
        $message = '';

        $trackfree_account_api_key = get_option('trackfree_account_api_key');

        $tf_post_data = file_get_contents('php://input');
        $request_data = json_decode($tf_post_data, true);

        $shipment_email_data = array(
            'trackfree_send_mail' => $request_data['trackfree_send_mail']
        );

        $response = wp_remote_get(trackfree_url() . '/api/wc_shipment_test_mail?key=' . $trackfree_account_api_key,
            array(
                'sslverify' => false,
                'timeout' => 15,
                'body' => $shipment_email_data
            )
        );
        $response_data = json_decode( wp_remote_retrieve_body( $response ), true );
        $message_type = '';
        $message = '';
        if ($response_data['status'] == 'success') {
            $message_type = 'success';
            $message =  __('Shipment test mail sent successfully', 'trackfree-woocommerce-tracking');
        } else if ($response_data['status'] == 'error') {
            if ($response_data['message'] == 'user_not_exist') {
                $message_type = 'error';
                $message = __('Invalid authentication', 'trackfree-woocommerce-tracking');
            } else if ($response_data['error_type'] == 'invalid_request') {
                $message_type = 'error';
                $message = __('Invalid request', 'trackfree-woocommerce-tracking');
            } else if ($response_data['error_type'] == 'no_tracking') {
                $message_type = 'error';
                $message = __('Sorry! Shipment test mail cannot be sent because no tracking number is available.', 'trackfree-woocommerce-tracking');
            }
        }

        $response = array(
            'status' => 'success',
            'message_type' => $message_type,
            'message' => $message
        );

        wp_send_json_success($response);
        wp_die();
    }

    function trackfree_error_notice($errors)
    {
        $trackfree_error = '';
        if (isset($errors)) {
            foreach($errors as $error) {
                if (isset($error['empty_name'])) {
                    $trackfree_error .= '<p>' . __( $error['empty_name'][0]) . '</p>';
                }
                if (isset($error['empty_storename'])) {
                    $trackfree_error .= '<p>' . __( $error['empty_storename'][0]) . '</p>';
                }
                if (isset($error['invalid_email'])) {
                    $trackfree_error .= '<p>' . __($error['invalid_email'][0]) .'</p>';
                }
            }
            if ($trackfree_error) {
                echo '<div class="error notice"> ' . $trackfree_error . '</div>';
                return 1;
            }
            return 0;
        }
    }

    function trackfree_settings_page()
    {
        $user_id = get_current_user_id();
        if (current_user_can('edit_user', $user_id)) {
            $trackfree_account_api_key = get_option('trackfree_account_api_key');
            $trackfree_account_verify = get_option('trackfree_account_verify');

            if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'TrackFree')) {
                if (isset($_POST['trackfree'])) {
                    trackfree_action();
                }
            }
            $appVerifyContent = '';
            $nonce = wp_create_nonce( 'TrackFree' );
            if ($trackfree_account_api_key == '') {
                include ('getting-started.php');
            }
            if (($trackfree_account_api_key) && ($trackfree_account_verify == 0)) {
                $appVerifyContent = '<div class="trackfree-error-container">' . __('Your app is not verified. Click verify link and activate your app in your mail. Please refresh this page if you have already verified.', 'trackfree-woocommerce-tracking') . '</div>';
                include ('getting-started.php');
            }

            if (($trackfree_account_verify == 0) && ($trackfree_account_api_key)) {
                trackfree_verification_status();
            }

            if (($trackfree_account_api_key) && ($trackfree_account_verify)) {
                trackfree_account_info_action();
            }
        }
    }

    function trackfree_account_info_action()
    {
        $user_id = get_current_user_id();
        if (current_user_can('edit_user', $user_id)) {
            $errors = new WP_Error;
            $nonce = wp_create_nonce( 'TrackFree' );
            $trackfree_account_api_key = get_option('trackfree_account_api_key');
            $response = wp_remote_get(trackfree_url() . '/api/wc_get_account_info?key=' . $trackfree_account_api_key,
                array(
                    'sslverify' => false,
                    'timeout' => 15
                )
            );

            $response_data = json_decode( wp_remote_retrieve_body( $response ), true );
            if ($response_data && $response_data['status'] == 'success') {
                add_option('trackfree_preferred_couriers');
                update_option('trackfree_preferred_couriers', $response_data['user_couriers']);

                if ($response_data['wp_version'] != get_bloginfo( 'version' )) {
                    wp_remote_post(trackfree_url() . '/api/wc_update_versions', array(
                        'sslverify' => false,
                        'timeout' => 15,
                        'body' => array(
                            'key' => $trackfree_account_api_key,
                            'type' => 'wordpress',
                            'version' => get_bloginfo( 'version' )
                        )
                    ));
                }

                if ($response_data['wc_version'] != WC()->version) {
                    wp_remote_post(trackfree_url() . '/api/wc_update_versions', array(
                        'sslverify' => false,
                        'timeout' => 15,
                        'body' => array(
                            'key' => $trackfree_account_api_key,
                            'type' => 'woocommerce',
                            'version' => WC()->version
                        )
                    ));
                }

                add_option('trackfree_tracking_domain');
                update_option('trackfree_tracking_domain', $response_data['domain_name']);
                ?>
                <div id="root"></div>
                <?php
            } else {
                if (($response_data && $response_data['status'] == 'error') && ($response_data['message'] == 'user_not_exist')) {
                    update_option('trackfree_account_api_key', '');
                    update_option('trackfree_account_verify', 0);
                    header('Location: ' . admin_url('admin.php?page=trackfree-getting-started'));
                } else {
                    $appVerifyContent = '<div class="trackfree-error-container">' . __('This account not verified or locked. Please contact TrackFree support team.', 'trackfree-woocommerce-tracking') . '</div>';
                    include ('getting-started.php');
                }
            }
        }
    }

    function trackfree_action()
    {
        $user_id = get_current_user_id();
        if (current_user_can('edit_user', $user_id)) {
            $user_info = get_userdata($user_id);
            $errors = new WP_Error;
            $user_email = sanitize_text_field($_POST['trackfree_account_email']);
            if ((empty( $user_email )) || ! (is_email( $user_email))) {
                $errors->add('invalid_email', __("<strong>ERROR</strong>: The email address is not correct.", 'trackfree-woocommerce-tracking'));
            }
            if (trackfree_error_notice($errors) == 0) {
                $custom_logo_id = get_theme_mod( 'custom_logo' );
                $store_logo_url = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                $plugin_data = get_plugin_data(dirname(__FILE__) . '/trackfree-woocommerce-tracking.php');
                $plugin_version = $plugin_data['Version'];
                $user_data = array(
                    'full_name' => $user_info->nickname,
                    'store_url' => get_option('siteurl'),
                    'store_name' => get_option('blogname'),
                    'address_1' => get_option('woocommerce_store_address'),
                    'address_2' => get_option('woocommerce_store_address_2'),
                    'city' => get_option('woocommerce_store_city'),
                    'country' => get_option('woocommerce_default_country'),
                    'post_code' => get_option('woocommerce_store_postcode'),
                    'gmt_offset' => get_option('gmt_offset'),
                    'timezone_string' => get_option('timezone_string'),
                    'store_icon_url' => get_site_icon_url(),
                    'store_logo_url' => $store_logo_url ? $store_logo_url[0] : '',
                    'plugin_version' => $plugin_version,
                    'currency' => get_option('woocommerce_currency'),
                    'locale' => get_locale(),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'wp_version' => get_bloginfo( 'version' ),
                    'wc_version' => WC()->version
                );
                $response = wp_remote_get(trackfree_url() . '/api/wc_create_user?email=' . $user_email,
                    array(
                        'sslverify' => false,
                        'timeout' => 15,
                        'body' => $user_data
                    )
                );
                $response_data = json_decode( wp_remote_retrieve_body( $response ), true );
                if ($response_data) {
                    if ($response_data['status'] == 'success') {
                        add_option('trackfree_account_api_key');
                        update_option('trackfree_account_api_key', $response_data['api_key']);
                        add_option('trackfree_account_verify');
                        update_option('trackfree_account_verify', 1);
                        add_option('trackfree_shipment_status_in_orders');
                        update_option('trackfree_shipment_status_in_orders', 0);
                        add_option('trackfree_shipment_details_in_order_details');
                        update_option('trackfree_shipment_details_in_order_details', 0);
                        add_option('trackfree_auto_order_status_update');
                        update_option('trackfree_auto_order_status_update', 0);
                        header('Location: ' . admin_url('admin.php?page=trackfree'));
                    } else if ($response_data['status'] == 'app_verify_mail') {
                        add_option('trackfree_account_api_key');
                        update_option('trackfree_account_api_key', $response_data['api_key']);
                        add_option('trackfree_account_verify');
                        update_option('trackfree_account_verify', 0);
                        add_option('trackfree_shipment_status_in_orders');
                        update_option('trackfree_shipment_status_in_orders', 0);
                        add_option('trackfree_shipment_details_in_order_details');
                        update_option('trackfree_shipment_details_in_order_details', 0);
                        add_option('trackfree_auto_order_status_update');
                        update_option('trackfree_auto_order_status_update', 0);
                        header('Location: ' . admin_url('admin.php?page=trackfree-getting-started'));
                    } else if ($response_data['status'] == 'app_already_exist') {
                        echo '<div class="trackfree-error-container">' . __('App already installed. Please contact TrackFree support team.', 'trackfree-woocommerce-tracking') . '</div>';
                        exit;
                    } else if ($response_data['status'] == 'account_inactive') {
                        echo '<div class="trackfree-error-container">'. __('Something went wrong. Please contact TrackFree support team.', 'trackfree-woocommerce-tracking') . '</div>';
                        exit;
                    } else if ($response_data['status'] == 'error') {
                        echo '<div class="trackfree-error-container">'. $response_data['message'] . '</div>';
                        exit;
                    }
                }

                if (isset($response_data['couriers'])) {
                    $trackfree_options = sanitize_text_field($response_data['couriers']);
                    add_option('trackfree_preferred_couriers');
                    update_option('trackfree_preferred_couriers', $trackfree_options);
                }
            }
        }
    }

    function trackfree_verification_status()
    {
        $user_id = get_current_user_id();
        if (current_user_can('edit_user', $user_id)) {
            $trackfree_account_api_key = get_option('trackfree_account_api_key');
            $response = wp_remote_get(trackfree_url() . '/api/wc_verify_status?key=' . $trackfree_account_api_key,
                array(
                    'sslverify' => false,
                    'timeout' => 15
                )
            );
            $response_data = $response['body'];
            if ($response_data == 'success') {
                update_option('trackfree_account_verify', 1);
                header('Location: ' . admin_url('admin.php?page=trackfree'));
            } else if ($response_data == 'expired') {
                add_option('trackfree_account_api_key');
                update_option('trackfree_account_api_key', '');
                add_option('trackfree_account_verify');
                update_option('trackfree_account_verify', 0);
                header('Location: ' . admin_url('admin.php?page=trackfree-getting-started'));
            }
        }
    }
}
