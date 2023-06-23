<?php

namespace Rtcl\Shortcodes;


use Rtcl\Controllers\Shortcodes;
use Rtcl\Helpers\Functions;

class Checkout
{

    /**
     * Get the shortcode content.
     *
     * @param array $atts Shortcode attributes.
     *
     * @return string
     */
    public static function get($atts) {
        return Shortcodes::shortcode_wrapper(array(__CLASS__, 'output'), $atts);
    }


    /**
     * Output the shortcode.
     *
     * @param array $atts Shortcode attributes.
     */
    public static function output($atts) {
        global $wp;

        if (!is_user_logged_in()) {
            $message = apply_filters('rtcl_checkout_message', '');

            if (!empty($message)) {
                Functions::add_notice($message);
            }

            Functions::add_notice(__("Need to login to access this page", 'classified-listing'), 'error');

            Functions::login_form();
        } else {
            // Start output buffer since the html may need discarding for BW compatibility
            ob_start();

            Functions::get_template('checkout/checkout');

            // Send output buffer
            ob_end_flush();
        }
    }

    public static function checkout_form($type, $value) {
        Functions::get_template('checkout/form', compact('type', 'value'));
    }

    public static function payment_receipt($order_id) {
        if ($order_id && $order = rtcl()->factory->get_order($order_id)) {
            Functions::get_template("checkout/payment-receipt", compact('order'));
        } else {
            Functions::add_notice(esc_html__("Given Payment Id is not a valid payment.", "classified-listing"), "error");
            Functions::get_template("checkout/error");
        }
    }
}