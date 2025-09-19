<?php
if (!defined('ABSPATH')) exit;

// Register the WooCommerce Order Test Gateway
function wpfi_add_woo_order_test_gateway($methods) {
    $methods[] = 'WC_Woo_Order_Test_Gateway';
    return $methods;
}
add_filter('woocommerce_payment_gateways', 'wpfi_add_woo_order_test_gateway');

// Define the WooCommerce Order Test Gateway class
add_action('plugins_loaded', 'wpfi_init_woo_order_test_gateway_class');
function wpfi_init_woo_order_test_gateway_class() {
    class WC_Woo_Order_Test_Gateway extends WC_Payment_Gateway {

        public function admin_options() {
            ?>
            <style>.form-table th {width: 240px !important;</style>
            <h2><?php echo esc_html($this->get_method_title()); ?></h2>
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="flex: 2;">
                    <p><?php echo wp_kses_post($this->get_method_description()); ?></p>
                    <table class="form-table">
                        <?php $this->generate_settings_html(); ?>
<div id="woo-test-banner-preview" style="margin-top: 10px;">
    <div style="background-color: <?php echo esc_attr($this->display_background_color); ?>; padding: 0px 10px 0px 10px; display: flex; justify-content: space-between; align-items: center;">
        <img src="<?php echo esc_url(plugins_url('/assets/woo-circel.png', __FILE__)); ?>" style="max-width: 40px;">
        <div style="flex-grow: 1; text-align: center; color: <?php echo esc_attr($this->display_text_color); ?>;">
            <p id="woo-test-preview-message"><?php echo esc_html($this->custom_message); ?></p>
        </div>
        <a href="#" id="woo-test-preview-button" style="
            background-color: <?php echo esc_attr($this->button_bg_color); ?>;
            color: <?php echo esc_attr($this->button_text_color); ?>;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        ">SETTINGS</a>
    </div>
</div>
                    </table>
                </div>
                <div class="woo-test-sidebar" style="flex: 1; text-align: center;background-image: url('<?php echo esc_url(plugins_url('/assets/box-bg.png', __FILE__)); ?>'); background-repeat: no-repeat; background-position: center center; background-size: cover;">
                    <a href="https://www.wpfixit.com" target="_blank" class="wpfi-woo-test-hover-raise">
                        <img src="<?php echo esc_url(plugins_url('/assets/n-desktop.webp', __FILE__)); ?>"  
                            alt="WP Fix It - WordPress Experts" 
                            style="margin: 0 auto; max-width: 150px; height: auto;margin-top: 10px;" 
                            loading="lazy" 
                            decoding="async"
                        />
                    </a>
                    <p style="color:#fff; font-size:16px;text-align:center">This plugin is brought to you by WP Fix It.<br>Experts in instant WordPress support!</p>
                    <a href="https://www.wpfixit.com/save/20-off/?ref=1" target="_blank" class="wpfi-woo-test-hover-raise">
                        <img src="<?php echo esc_url(plugins_url('/assets/save.png', __FILE__)); ?>"  
                            alt="WP Fix It - WordPress Experts" 
                            style="margin: 0 auto; display: block; border-radius: 12px; width: 325px;"
                            title="20% Off WP Fix It's Services" 
                            loading="lazy" 
                            decoding="async"
                        />
                    </a>
                </div>
            </div>
            <?php
        }

        public $custom_message;

        public function __construct() {
            $this->id                 = 'wpfi_woo_order_test';
            $this->has_fields         = false;
            $this->method_title       = esc_html__('WooCommerce Order Test', 'woo-order-test');

            if (isset($_GET['section']) && $_GET['section'] === 'wpfi_woo_order_test') {
                $this->method_description = esc_html__('A test gateway for admins to bypass payment methods to test that the checkout is working properly. Created and managed by WP Fix It - WordPress Experts.', 'woo-order-test');
            } else {
                $this->method_description = wp_kses_post(__('A test gateway for admins to bypass payment methods.', 'woo-order-test'));
            }

            $this->init_form_fields();
            $this->init_settings();

            $this->enabled                   = $this->get_option('enabled');
            $this->custom_message            = $this->get_option('custom_message', '');
            $this->display_background_color = $this->get_option('display_background_color', '#efe');
            $this->display_text_color       = $this->get_option('display_text_color', '#000');
            $this->button_bg_color          = $this->get_option('button_bg_color', '#d16aff');
            $this->button_text_color        = $this->get_option('button_text_color', '#ffffff');
            $this->button_hover_bg_color    = $this->get_option('button_hover_bg_color', '#00D78B');
            $this->button_hover_text_color  = $this->get_option('button_hover_text_color', '#ffffff');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('wp_body_open', array($this, 'display_custom_above_header_notice'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'   => esc_html__('Enable/Disable', 'woo-order-test'),
                    'type'    => 'checkbox',
                    'label'   => esc_html__('Enable WooCommerce Order Test Gateway', 'woo-order-test'),
                    'default' => 'no',
                ),
                'custom_message' => array(
                    'title'       => esc_html__('Custom Testing Message', 'woo-order-test'),
                    'type'        => 'text',
                    'description' => esc_html__('Displayed during checkout for admin users.', 'woo-order-test'),
                    'default'     => 'Payment gateways are disabled for testing purposes.',
                    'desc_tip'    => true,
                    'css'         => 'width: 100%; max-width: 600px;',
                ),
                'display_background_color' => array(
                    'title' => esc_html__('Banner Background Color', 'woo-order-test'),
                    'type'  => 'color',
                    'css'   => 'width:77px;',
                    'default' => '#efe',
                ),
                'display_text_color' => array(
                    'title' => esc_html__('Banner Text Color', 'woo-order-test'),
                    'type'  => 'color',
                    'css'   => 'width:77px;',
                    'default' => '#000',
                ),
                'button_bg_color' => array(
                    'title' => esc_html__('Button Background Color', 'woo-order-test'),
                    'type'  => 'color',
                    'css'   => 'width:77px;',
                    'default' => '#d16aff',
                ),
                'button_text_color' => array(
                    'title' => esc_html__('Button Text Color', 'woo-order-test'),
                    'type'  => 'color',
                    'css'   => 'width:77px;',
                    'default' => '#ffffff',
                ),
                'button_hover_bg_color' => array(
                    'title' => esc_html__('Button Hover Background Color', 'woo-order-test'),
                    'type'  => 'color',
                    'css'   => 'width:77px;',
                    'default' => '#00D78B',
                ),
                'button_hover_text_color' => array(
                    'title' => esc_html__('Button Hover Text Color', 'woo-order-test'),
                    'type'  => 'color',
                    'css'   => 'width:77px;',
                    'default' => '#ffffff',
                ),
            );
        }

        public function process_payment($order_id) {
            $order = wc_get_order($order_id);
            if (current_user_can('administrator')) {
                $order->payment_complete();
                $order->add_order_note(__('Order test completed by admin user.', 'woo-order-test'));
                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url($order),
                );
            } else {
                wc_add_notice(__('This payment method is only available for admin users.', 'woo-order-test'), 'error');
                return;
            }
        }

        public function display_custom_above_header_notice() {
            if (is_checkout() && $this->enabled === 'yes' && wpfi_is_payment_bypass_enabled_for_admin()) {
                if (!empty($this->custom_message)) {
                    $bg_color   = esc_attr($this->display_background_color);
                    $text_color = esc_attr($this->display_text_color);
                    $btn_bg     = esc_attr($this->button_bg_color);
                    $btn_txt    = esc_attr($this->button_text_color);
                    $btn_hover_bg  = esc_attr($this->button_hover_bg_color);
                    $btn_hover_txt = esc_attr($this->button_hover_text_color);
                    echo '<div class="woocommerce-notices-wrapper" style="position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;">';
                    echo '<div style="background-color: ' . $bg_color . '; border-bottom: 1px solid #ccc; padding: 30px 0px 0px 0px; margin-bottom: 0; display: flex; justify-content: space-between; align-items: center;">';
                    echo '<div style="flex: 0 0 auto;"><img src="' . esc_url(plugins_url('/assets/woo-circel.png', __FILE__)) . '" alt="WooCommerce" style="max-width: 40px;padding: 10px 0px 0px 10px;"></div>';
                    echo '<div style="flex-grow: 1; text-align: center; color: ' . $text_color . ';"><p>' . esc_html($this->custom_message) . '</p></div>';
                    echo '<div style="flex: 0 0 auto;"><a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=wpfi_woo_order_test')) . '" class="woo_order_test_button">SETTINGS</a></div>';
                    echo '</div></div>';
                    echo '<style>
                        .woo_order_test_button {
                            background-color: ' . $btn_bg . ';
                            color: ' . $btn_txt . ';
                            padding: 10px 20px;
                            text-decoration: none;
                            border-radius: 5px;
                            font-size: 14px;
                            font-weight: bold;
                            margin-right:23px;
                            transition: all 0.3s ease;
                            display: inline-block;
                        }
                        .woo_order_test_button:hover {
                            background-color: ' . $btn_hover_bg . ';
                            color: ' . $btn_hover_txt . ';
                        }
                    </style>';
                }
            }
        }
    }
}

function wpfi_is_payment_bypass_enabled_for_admin() {
    return current_user_can('administrator') && 'yes' === get_option('admin_payment_bypass_enabled', 'no');
}
function wpfi_admin_auto_complete_order($order_id) {
    if (!$order_id) return;
    if (current_user_can('administrator')) {
        $order = wc_get_order($order_id);
        if ($order) {
            $order->update_status('completed');
        }
    }
}
add_action('woocommerce_thankyou', 'wpfi_admin_auto_complete_order');

function wpfi_is_test_gateway_enabled() {
    $gateway_settings = get_option('woocommerce_wpfi_woo_order_test_settings');
    return isset($gateway_settings['enabled']) && $gateway_settings['enabled'] === 'yes';
}

function wpfi_admin_cart_needs_payment($needs_payment) {
    if (current_user_can('administrator') && wpfi_is_test_gateway_enabled() && wpfi_is_payment_bypass_enabled_for_admin()) {
        return false;
    }
    return $needs_payment;
}
add_filter('woocommerce_cart_needs_payment', 'wpfi_admin_cart_needs_payment');

function wpfi_admin_disable_payment_gateways($available_gateways) {
    if (is_checkout() && wpfi_is_test_gateway_enabled() && wpfi_is_payment_bypass_enabled_for_admin()) {
        return array();
    }
    return $available_gateways;
}
add_filter('woocommerce_available_payment_gateways', 'wpfi_admin_disable_payment_gateways');

function wpfi_admin_skip_payment_method_validation($data, $errors) {
    if (wpfi_is_payment_bypass_enabled_for_admin()) {
        $errors->remove('no_payment_method');
    }
}
add_action('woocommerce_after_checkout_validation', 'wpfi_admin_skip_payment_method_validation', 10, 2);

function wpfi_admin_order_needs_payment($needs_payment, $order) {
    if (wpfi_is_payment_bypass_enabled_for_admin()) {
        return false;
    }
    return $needs_payment;
}
add_filter('woocommerce_order_needs_payment', 'wpfi_admin_order_needs_payment', 10, 2);

function wpfi_modify_gateway_title($title, $gateway_id) {
    if ($gateway_id === 'wpfi_woo_order_test') {
        return esc_html__('WooCommerce Order Test', 'woo-order-test');
    }
    return $title;
}

add_filter('wp_mail', 'wpfi_disable_gateway_activation_email', 10, 1);
function wpfi_disable_gateway_activation_email($args) {
    if (isset($args['subject']) && strpos($args['subject'], 'Payment gateway') !== false && wpfi_is_test_gateway_enabled()) {
        $args['to'] = [];
    }
    return $args;
}
