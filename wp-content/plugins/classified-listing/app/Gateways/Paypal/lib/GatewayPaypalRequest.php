<?php

namespace Rtcl\Gateways\Paypal\lib;

use Rtcl\Gateways\Paypal\GatewayPaypal;
use Rtcl\Helpers\Functions;
use Rtcl\Log\Logger;
use Rtcl\Models\Payment;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Generates requests to send to PayPal.
 */
class GatewayPaypalRequest
{

	/**
	 * Stores line items to send to PayPal.
	 *
	 * @var array
	 */
	protected $line_items = array();

	/**
	 * Pointer to gateway making the request.
	 *
	 * @var
	 */
	protected $gateway;

	/**
	 * Endpoint for requests from PayPal.
	 *
	 * @var string
	 */
	protected $notify_url;

	/**
	 * Constructor.
	 *
	 * @param GatewayPaypal $gateway
	 */
	public function __construct($gateway) {
		$this->gateway = $gateway;
		$this->notify_url = rtcl()->api_request_url('rtcl_gateway_paypal');
	}

	/**
	 * Get the PayPal request URL for an order.
	 *
	 * @param Payment $Payment
	 * @param bool    $sandbox
	 *
	 * @return string
	 */
	public function get_request_url($Payment, $sandbox = false) {
		$paypal_args = http_build_query($this->get_paypal_args($Payment));

		// Test Log
		$logger = new Logger();
		$logger->log("info", $paypal_args);
		if ($sandbox) {
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&' . $paypal_args;
		} else {
			return 'https://www.paypal.com/cgi-bin/webscr?' . $paypal_args;
		}
	}

	/**
	 * Limit length of an arg.
	 *
	 * @param string  $string
	 * @param integer $limit
	 *
	 * @return string
	 */
	protected function limit_length($string, $limit = 127) {
		if (strlen($string) > $limit) {
			$string = substr($string, 0, $limit - 3) . '...';
		}

		return $string;
	}

	/**
	 * Get PayPal Args for passing to PP.
	 *
	 * @param Payment $payment
	 *
	 * @return array
	 */
	protected function get_paypal_args($payment) {

		return apply_filters('rtcl_paypal_args', array_merge(
			array(
				'cmd'           => '_cart',
				'business'      => $this->gateway->get_option('email'),
				'no_note'       => 1,
				'currency_code' => Functions::get_order_currency(),
				'charset'       => 'utf-8',
				'rm'            => is_ssl() ? 2 : 1,
				'upload'        => 1,
				'return'        => esc_url_raw(add_query_arg(['utm_nooverride' => '1', 'rtcl_return' => 'success'], $this->gateway->get_return_url($payment))),
				'cancel_return' => esc_url_raw($payment->get_cancel_payment_url_raw()),
				'page_style'    => $this->gateway->get_option('page_style'),
				'image_url'     => esc_url_raw($this->gateway->get_option('image_url')),
				'paymentaction' => $this->gateway->get_option('paymentaction'),
				'bn'            => 'Rtcl_Cart',
//				'invoice'       => $this->limit_length( $this->gateway->get_option( 'invoice_prefix' ) . $payment->get_order_number(), 127 ),
				'custom'        => json_encode(array(
					'order_id'  => $payment->get_id(),
					'order_key' => $payment->get_order_key()
				)),
				'notify_url'    => $this->limit_length($this->notify_url, 255),
			),
			$this->get_item_args($payment)
		), $payment);
	}

	/**
	 * @param Payment $payment
	 *
	 * @return array
	 */
	protected function get_item_args($payment) {
		$item = array();
		$item['item_name_1'] = $this->limit_length($payment->get_listing_title());
		$item['amount_1'] = $payment->get_total();

		return apply_filters('rtcl_paypal_item_info', $item, $payment);
	}


	protected function get_phone_number_args($order) {
		if (in_array($order->get_billing_country(), array('US', 'CA'))) {
			$phone_number = str_replace(array('(', '-', ' ', ')', '.'), '', $order->get_billing_phone());
			$phone_number = ltrim($phone_number, '+1');
			$phone_args = array(
				'night_phone_a' => substr($phone_number, 0, 3),
				'night_phone_b' => substr($phone_number, 3, 3),
				'night_phone_c' => substr($phone_number, 6, 4),
			);
		} else {
			$phone_args = array(
				'night_phone_b' => $order->get_billing_phone(),
			);
		}

		return $phone_args;
	}

	/**
	 * Return all line items.
	 */
	protected function get_line_items() {
		return $this->line_items;
	}

	/**
	 * Remove all line items.
	 */
	protected function delete_line_items() {
		$this->line_items = array();
	}

	/**
	 * Get the state to send to paypal.
	 *
	 * @param string $cc
	 * @param string $state
	 *
	 * @return string
	 */
	protected function get_paypal_state($cc, $state) {
		if ('US' === $cc) {
			return $state;
		}

		$states = rtcl()->countries->get_states($cc);

		if (isset($states[$state])) {
			return $states[$state];
		}

		return $state;
	}

	/**
	 * Check if currency has decimals.
	 *
	 * @param string $currency
	 *
	 * @return bool
	 */
	protected function currency_has_decimals($currency) {
		if (in_array($currency, array('HUF', 'JPY', 'TWD'))) {
			return false;
		}

		return true;
	}

	/**
	 * Round prices.
	 *
	 * @param double  $price
	 * @param Payment $order
	 *
	 * @return double
	 */
	protected function round($price, $order) {
		$precision = 2;

		if (!$this->currency_has_decimals($order->get_currency())) {
			$precision = 0;
		}

		return round($price, $precision);
	}

	/**
	 * Format prices.
	 *
	 * @param float|int $price
	 * @param Payment   $order
	 *
	 * @return string
	 */
	protected function number_format($price, $order) {
		$decimals = 2;

		if (!$this->currency_has_decimals($order->get_currency())) {
			$decimals = 0;
		}

		return number_format($price, $decimals, '.', '');
	}
}
