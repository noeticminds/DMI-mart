<?php
/**
 * Formatting API
 * 
 * WooSingle product pages
 * 	update variable values - call to action, prefilled...
 * 
 * @since 3.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Variables.. woocommerce single product pages..
 * 
 * Replaces placeholders in a given string with WooCommerce product details on single product pages.
 *
 * This function retrieves product details (name, price, regular price, and SKU) 
 * and replaces corresponding placeholders within the `$value` variable.
 *
 * Supported placeholders:
 * - `{product}` → Product Name
 * - `{{price}}` or `{price}` → Product Price (formatted if `wc_price` exists)
 * - `{regular_price}` → Product's Regular Price (before discount)
 * - `{sku}` → Product SKU (Stock Keeping Unit)
 * - `{price} → Product Price (unformatted).
 * - `{sku}` → Product SKU (Stock Keeping Unit)
 * 
 * @uses
 * 
 * @since 3.4
 * @param string $value		input value to convert variables on product page
 *
 */
if ( ! function_exists('ht_ctc_woo_single_product_page_variables') ) {

    function ht_ctc_woo_single_product_page_variables( $value ) {

        // if woocommerce single product page
        if ( function_exists( 'is_product' ) && function_exists( 'wc_get_product' )) {
            if ( is_product() ) {

                $product = wc_get_product();

                $name = $product->get_name();
                // $title = $product->get_title();
                $price = $product->get_price();
                $regular_price = $product->get_regular_price();
                $sku = $product->get_sku();
                $price_formatted = '';
                
                 // Ensure price is not empty or null to prevent displaying "0.00". If wc_price() is used, it may return a default "0.00" when no price is set.
                if ( $price !== '' && $price !== null ) { 
                    if ( function_exists( 'wc_price' ) ) {
                        /**
                         * get thousand separator, decimal separator, currency symbol
                         * 
                         * wc_price() returns the formatted price with HTML tags.
                         * Use strip_tags() to remove HTML and html_entity_decode() to display currency symbols correctly.
                         */
                        $price_formatted = html_entity_decode( strip_tags( wc_price( $price ) ) );
                    } else {
                        $price_formatted = $price; // Use raw price if wc_price() is unavailable.
                    }
                } else {
                    $price_formatted = ''; // Keep output blank if no price is set.
                }

                // variables works in default pre_filled also for woo pages.
                $value = str_replace( array('{product}', '{{price}}', '{price}', '{regular_price}', '{sku}' ),  array( $name, $price_formatted, $price, $regular_price, $sku ), $value );
            }
        }

        return $value;
    }
}