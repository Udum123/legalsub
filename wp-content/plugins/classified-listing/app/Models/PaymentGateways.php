<?php

namespace Rtcl\Models;

use Rtcl\Gateways\Offline\GatewayOffline;
use Rtcl\Gateways\Paypal\GatewayPaypal;
use Rtcl\Helpers\Functions;

class PaymentGateways
{

	/** @var array Array of payment gateway classes. */
	public $payment_gateways = [];

	/**
	 * @var PaymentGateways The single instance of the class
	 * @since 2.1
	 */
	protected static $_instance = null;

	/**
	 * Main PaymentGateways Instance.
	 *
	 * Ensures only one instance of PaymentGateways is loaded or can be loaded.
	 *
	 * @return PaymentGateways Main instance
	 * @since 1.0.1
	 * @static
	 */
	public static function instance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		Functions::doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'classified-listing'), '1.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		Functions::doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'classified-listing'), '1.0');
	}

	/**
	 * Initialize payment gateways.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load gateways and hook in functions.
	 */
	public function init() {
		$load_gateways = array(
			GatewayOffline::class,
			GatewayPaypal::class,
		);


		// Filter
		$load_gateways = apply_filters('rtcl_load_payment_gateways', $load_gateways);

		// TODO: Get sort order option

		// Load gateways in order
		foreach ($load_gateways as $gateway) {
			$load_gateway = is_string($gateway) ? new $gateway() : $gateway;
			$this->payment_gateways[] = $load_gateway;
		}
	}

	/**
	 * Get gateways.
	 *
	 * @return array
	 */
	public function payment_gateways() {
		$_available_gateways = array();

		if (sizeof($this->payment_gateways) > 0) {
			foreach ($this->payment_gateways as $gateway) {
				$_available_gateways[$gateway->id] = $gateway;
			}
		}

		return $_available_gateways;
	}

	/**
	 * Get array of registered gateway ids
	 *
	 * @return array of strings
	 * @since 2.6.0
	 */
	public function get_payment_gateway_ids() {
		return wp_list_pluck($this->payment_gateways, 'id');
	}

}