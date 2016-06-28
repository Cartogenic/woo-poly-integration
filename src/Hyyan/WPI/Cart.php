<?php

/**
 * This file is part of the hyyan/woo-poly-integration plugin.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hyyan\WPI;

use Hyyan\WPI\Product\Variation;

/**
 * Cart
 *
 * Handle cart translation
 *
 * @author Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 */
class Cart {

    const ADD_TO_CART_HANDLER_VARIABLE = 'wpi_variable';

    /**
     * Construct object
     */
    public function __construct() {
        // handle add to cart
        add_filter( 'woocommerce_add_to_cart_product_id', array( $this, 'add_to_cart' ), 10, 1 );

        // handle cart translation
        add_filter( 'woocommerce_cart_item_product', array( $this, 'translate_cart' ), 10, 2 );
        add_filter( 'woocommerce_get_item_data', array( $this, 'translate_cart_item_data' ), 10, 2 );

        // handle the update of cart widget when language is switched
        add_action( 'wp_enqueue_scripts', array( $this, 'replaceCartFragmentsScript' ), 100 );

        // Costum 'add to cart' handler for variable products
        add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'set_add_to_cart_handler' ), 10, 2 );
        add_action( 'woocommerce_add_to_cart_handler_' . self::ADD_TO_CART_HANDLER_VARIABLE, array( $this, 'add_to_cart_handler_variable' ), 10, 1 );

    }

    /**
     * Add to cart
     *
     * The function will make sure that products won't be duplicated for each
     * language
     *
     * @param integer $ID the current product ID
     *
     * @return integer the final product ID
     */
    public function add_to_cart( $ID ) {

        $result = $ID;

        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );
        error_log( 'prod_id to add to cart: ' . print_r( $ID, true ) );

        // get the product translations
        $IDS = Utilities::getProductTranslationsArrayByID( $ID );

        error_log( 'translation ids of prod_id: ' . print_r( $IDS, true ) );
        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );

        // check if any of product's translation is already in cart
        foreach ( WC()->cart->get_cart() as $values ) {

            error_log( 'prod id in the cart: ' . print_r( $values['product_id'], true ) );
            error_log( '          w/ var id: ' . print_r( $values['variation_id'], true ) );

            $product = $values['data'];

            if ( in_array( $product->id, $IDS ) ) {

                error_log( 'translation found in the cart: ' . print_r( $values['product_id'], true ) . ' is translation of ' .  print_r( $ID, true ) );
                error_log( 'return the translation instead: ' . print_r( $values['product_id'], true ) );
                error_log( print_r( '', true ) );

                $result = $product->id;
                break;
            }
        }

        return $result;
    }

    /**
     * Translate displayed products in cart
     *
     * @param \WC_Product|\WC_Product_Variation $cartItemData
     * @param array       $cartItem
     *
     * @return \WC_Product|\WC_Product_Variation
     */
    public function translate_cart( $cart_item_data, $cart_item ) {

        $cart_product_id   = $cart_item['product_id'];
        $cart_variation_id = $cart_item['variation_id'];

        // By default, returns the same input
        $cart_item_data_translation = $cart_item_data;

        switch ( $cart_item_data->product_type ) {
            case 'variation':

                // Get product translation in current language
                $product_translation = Utilities::getProductTranslationByID( $cart_product_id );

                if ( $product_translation && $product_translation->id != $cart_product_id ) {

                    // Found a translation:
                    // 1. Get duplication key metadata value from the variation in the cart
                    $meta = get_post_meta( $cart_variation_id, Variation::DUPLICATE_KEY, true );

                    // 2. Get posts (variations) with duplication metadata value
                    // for the product translation
                    if ( $meta ) {
                        $variation_post = get_posts( array(
                            'meta_key'    => Variation::DUPLICATE_KEY,
                            'meta_value'  => $meta,
                            'post_type'   => 'product_variation',
                            'post_parent' => $product_translation->id
                        ) );
                    }

                    // 3. Get variation translation
                    if ( isset( $variation_post ) && $variation_post && count( $variation_post ) == 1 ) {
                        $cart_item_data_translation = wc_get_product( $variation_post[0]->ID );
                    }

                }
                break;

            case 'simple':
            default:
                $cart_item_data_translation = Utilities::getProductTranslationByID( $cart_product_id );
                break;
        }

        return $cart_item_data_translation;
    }

    public function translate_cart_item_data( $item_data, $cart_item ) {

        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );
        error_log( 'item data: ' . print_r( $item_data, true ) );

        $item_data_translation = array();

        foreach ( $item_data as $data ) {

            $term_id = term_exists( $data['value'] );

            if ( $term_id !== 0 && $term_id !== null ) {

                // Product attribute is a taxonomy term - check if Polylang has a translation
                $term_lang = pll_get_term_language( $term_id );
                $term_id_translation = pll_get_term( $term_id );

                if ( $term_id_translation == $term_id ) {
                    // Already showing the attribute (term) in the correct language
                    $item_data_translation[] = $data;

                } else {
                    // Get term translation from id
                    $term_translation = get_term( $term_id_translation );

                    $error = get_class( $term_translation ) == 'WP_Error';

                    $item_data_translation[] = array( 'key' => $data['key'], 'value' => ! $error ? $term_translation->name : $data['value'] ); // On error return same
                }

                error_log( 'term exists: yes' );
                error_log( 'term id found: ' . print_r( $term_id, true ) );
                error_log( 'term found: ' . print_r( get_term( $term_id ), true ) );
                error_log( 'term lang: ' . print_r( $term_lang, true ) );
                error_log( 'term id translation: ' . print_r( $term_id_translation, true ) );
                error_log( print_r( '', true ) );

            } else {

                // Product attribute is post metadata and not translatable - return same
                $item_data_translation[] = $data;

            }

        }

        return ! empty( $item_data_translation ) ? $item_data_translation : $item_data;
    }

    /**
     * Replace woo fragments script
     *
     * To update cart widget when language is switched
     */
    public function replaceCartFragmentsScript() {

        /* remove the orginal wc-cart-fragments.js and register ours */
        wp_deregister_script( 'wc-cart-fragments' );
        wp_enqueue_script( 'wc-cart-fragments'
                , plugins_url( 'public/js/Cart.js', Hyyan_WPI_DIR )
                , array( 'jquery', 'jquery-cookie' )
                , Plugin::getVersion()
                , true
        );
    }

    /**
     * Set custom add to cart handler
     *
     * @param string    $product_type   Product type of the product being added to cart
     * @param (mixed)   $product        Product object of the product being added to cart
     *
     * @return string   Costum add to cart handler
     */
    public function set_add_to_cart_handler( $product_type, $product ) {
        return 'variable' === $product_type ? self::ADD_TO_CART_HANDLER_VARIABLE : $product_type;
    }

    /**
     * Custom add to cart handler for variable products
     *
     * Based on function add_to_cart_handler_variable( $product_id ) from
     * <install_dir>/wp-content/plugins/woocommerce/includes/class-wc-form-handler.php
     * but using $url as argument.Therefore we use the initial bits from
     * add_to_cart_action( $url ).
     *
     * @param string    $url   Add to cart url (e.g. https://www.yourdomain.com/?add-to-cart=123&quantity=1&variation_id=117&attribute_size=Small&attribute_color=Black )
     */
    public function add_to_cart_handler_variable( $url ) {

        // From add_to_cart_action( $url )
        if ( empty( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) {
            return;
        }

        $product_id          = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );
        $was_added_to_cart   = false;
        $adding_to_cart      = wc_get_product( $product_id );

        if ( ! $adding_to_cart ) {
                return;
        }

        // From add_to_cart_handler_variable( $product_id )
        $variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
        $quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
        $missing_attributes = array();
        $variations         = array();
        $attributes         = $adding_to_cart->get_attributes();


        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );
        error_log( print_r( '', true ) );
        error_log( 'real product adding to cart: ' . print_r( absint( $_REQUEST['add-to-cart'] ), true ) );
        error_log( 'product adding to cart: ' . print_r( $product_id, true ) );
        error_log( 'variation adding to cart: ' . print_r( $variation_id, true ) );
        error_log( 'variation attributes: ' . print_r( $attributes, true ) );


        // If no variation ID is set, attempt to get a variation ID from posted attributes.
        if ( empty( $variation_id ) ) {
            $variation_id = $adding_to_cart->get_matching_variation( wp_unslash( $_POST ) );
        }

        $variation = wc_get_product( $variation_id );

        // Verify all attributes
        foreach ( $attributes as $attribute ) {
            if ( ! $attribute['is_variation'] ) {
                    continue;
            }

            $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

            if ( isset( $_REQUEST[ $taxonomy ] ) ) {

                // Get value from post data
                if ( $attribute['is_taxonomy'] ) {
                    // Don't use wc_clean as it destroys sanitized characters
                    $value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
                } else {
                    $value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
                }

                // Get valid value from variation
                $valid_value = isset( $variation->variation_data[ $taxonomy ] ) ? $variation->variation_data[ $taxonomy ] : '';

                // Allow if valid
                if ( '' === $valid_value || $valid_value === $value ) {
                    $variations[ $taxonomy ] = $value;
                    continue;
                }

            } else {
                $missing_attributes[] = wc_attribute_label( $attribute['name'] );
            }
        }

        if ( ! empty( $missing_attributes ) ) {
            wc_add_notice( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ), 'error' );
        } elseif ( empty( $variation_id ) ) {
            wc_add_notice( __( 'Please choose product options&hellip;', 'woocommerce' ), 'error' );
        } else {
            // Add to cart validation
            $passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

            if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) !== false ) {
                wc_add_to_cart_message( array( $product_id => $quantity ), true );
                return true;
            }
        }
        return false;
    }

}
