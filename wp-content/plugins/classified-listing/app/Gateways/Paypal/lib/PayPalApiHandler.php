<?php

namespace Rtcl\Gateways\Paypal\lib;

use Rtcl\Helpers\Functions;
use Rtcl\Log\Logger;
use Rtcl\Models\Payment;

/**
 * Handles Refunds and other API requests such as capture.
 * @since 1.0.0
 */
class PayPalApiHandler {

	/** @var string API Username */
	public static $api_username;

	/** @var string API Password */
	public static $api_password;

	/** @var string API Signature */
	public static $api_signature;

	/** @var bool Sandbox */
	public static $sandbox = false;

	/**
	 * Get capture request args.
	 * See https://developer.paypal.com/docs/classic/api/merchant/DoCapture_API_Operation_NVP/.
	 *
	 * @param  Payment $order
	 * @param  float $amount
	 *
	 * @return array
	 */
	public static function get_capture_request( $order, $amount = null ) {
		$request = array(
			'VERSION'         => '84.0',
			'SIGNATURE'       => self::$api_signature,
			'USER'            => self::$api_username,
			'PWD'             => self::$api_password,
			'METHOD'          => 'DoCapture',
			'AUTHORIZATIONID' => $order->get_transaction_id(),
			'AMT'             => number_format( is_null( $amount ) ? $order->get_total() : $amount, 2, '.', '' ),
			'CURRENCYCODE'    => Functions::get_order_currency( ),
			'COMPLETETYPE'    => 'Complete',
		);

		return apply_filters( 'rtcl_paypal_capture_request', $request, $order, $amount );
	}

	/**
	 * Get refund request args.
	 *
	 * @param  Payment $order
	 * @param  float $amount
	 * @param  string $reason
	 *
	 * @return array
	 */
	public static function get_refund_request( $order, $amount = null, $reason = '' ) {
		$request = array(
			'VERSION'       => '84.0',
			'SIGNATURE'     => self::$api_signature,
			'USER'          => self::$api_username,
			'PWD'           => self::$api_password,
			'METHOD'        => 'RefundTransaction',
			'TRANSACTIONID' => $order->get_transaction_id(),
			'NOTE'          => html_entity_decode( Functions::trim_string( $reason, 255 ), ENT_NOQUOTES, 'UTF-8' ),
			'REFUNDTYPE'    => 'Full',
		);
		if ( ! is_null( $amount ) ) {
			$request['AMT']          = number_format( $amount, 2, '.', '' );
			$request['CURRENCYCODE'] = Functions::get_order_currency();
			$request['REFUNDTYPE']   = 'Partial';
		}

		return apply_filters( 'rtcl_paypal_refund_request', $request, $order, $amount, $reason );
	}

	/**
	 * Capture an authorization.
	 *
	 * @param  Payment $order
	 * @param  float $amount
	 *
	 * @return object Either an object of name value pairs for a success, or a WP_ERROR object.
	 */
	public static function do_capture( $order, $amount = null ) {

		$raw_response = wp_safe_remote_post(
			self::$sandbox ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp',
			array(
				'method'      => 'POST',
				'body'        => self::get_capture_request( $order, $amount ),
				'timeout'     => 70,
				'user-agent'  => 'Rtcl/' . RTCL_VERSION,
				'httpversion' => '1.1',
			)
		);
		$log = new Logger();
		$log->info( 'DoCapture Response: ', $raw_response );

		if ( empty( $raw_response['body'] ) ) {
			return new \WP_Error( 'paypal-api', 'Empty Response' );
		} elseif ( is_wp_error( $raw_response ) ) {
			return $raw_response;
		}

		parse_str( $raw_response['body'], $response );

		return (object) $response;
	}

	/**
	 * Refund an order via PayPal.
	 *
	 * @param  Payment $order
	 * @param  float $amount
	 * @param  string $reason
	 *
	 * @return object Either an object of name value pairs for a success, or a WP_ERROR object.
	 */
	public static function refund_transaction( $order, $amount = null, $reason = '' ) {
		$raw_response = wp_safe_remote_post(
			self::$sandbox ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp',
			array(
				'method'      => 'POST',
				'body'        => self::get_refund_request( $order, $amount, $reason ),
				'timeout'     => 70,
				'user-agent'  => 'Rtcl/' . RTCL_VERSION,
				'httpversion' => '1.1',
			)
		);
		$log = new Logger();
		$log->info( 'Refund Response: ', $raw_response);

		if ( empty( $raw_response['body'] ) ) {
			return new \WP_Error( 'paypal-api', 'Empty Response' );
		} elseif ( is_wp_error( $raw_response ) ) {
			return $raw_response;
		}

		parse_str( $raw_response['body'], $response );

		return (object) $response;
	}

}