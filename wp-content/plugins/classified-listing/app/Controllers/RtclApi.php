<?php

namespace Rtcl\Controllers;

use Rtcl\Helpers\Functions;
use WP_REST_Request;

/**
 * RTCL-API endpoint handler.
 *
 * This handles API related functionality in ClassifiedListing.
 * - rtcl-api endpoint - Commonly used by Payment gateways for callbacks.
 *
 * @package ClassifiedListing\RestApi
 * @since   2.0.6.3
 */
class RtclApi
{
	public function init() {
		add_action('init', [$this, 'add_endpoint'], 0);
		add_filter('query_vars', [$this, 'add_query_vars'], 0);
		add_action('parse_request', [$this, 'handle_api_requests'], 0);

		//add_action('rest_api_init', [$this, 'payment_receipt_restapi']); deprecated
	}


	/**
	 * RTCL API for payment gateway IPNs, etc.
	 *
	 * @since 2.0
	 */
	public static function add_endpoint() {
		add_rewrite_endpoint('rtcl-api', EP_ALL);
	}


	/**
	 * Add new query vars.
	 *
	 * @param array $vars Query vars.
	 *
	 * @return string[]
	 */
	public function add_query_vars($vars) {
		$vars[] = 'rtcl-api';
		return $vars;
	}

	/**
	 * API request - Trigger any API requests.
	 *
	 */
	public function handle_api_requests() {
		global $wp;

		if (!empty($_GET['rtcl-api'])) {
			$wp->query_vars['rtcl-api'] = sanitize_key(wp_unslash($_GET['rtcl-api']));
		}

		// rtcl-api endpoint requests.
		if (!empty($wp->query_vars['rtcl-api'])) {

			// Buffer, we won't want any output here.
			ob_start();

			// No cache headers.
			Functions::nocache_headers();

			// Clean the API request.
			$api_request = strtolower(Functions::clean($wp->query_vars['rtcl-api']));

			// Make sure gateways are available for request.
			rtcl()->payment_gateways();

			// Trigger generic action before request hook.
			do_action('rtcl_api_request', $api_request);

			// Is there actually something hooked into this API request? If not trigger 400 - Bad request.
			status_header(has_action('rtcl_api_' . $api_request) ? 200 : 400);

			// Trigger an action which plugins can hook into to fulfill the request.
			do_action('rtcl_api_' . $api_request);

			// Done, clear buffer and exit.
			ob_end_clean();
			die('-1');
		}
	}

	public function payment_receipt_restapi() {
		register_rest_route('rtcl/v1', '/receive-payment/', [
			'methods'             => 'POST',
			'callback'            => [$this, 'payment_receipt_api_callback'],
			'permission_callback' => '__return_true'
		]);
	}

	public function payment_receipt_api_callback(WP_REST_Request $request) {
		// Clean the API request.
		$gateway_id = strtolower(Functions::clean($request->get_param('gateway_id')));
		if ($gateway_id && $gateway = Functions::get_payment_gateway($gateway_id)) {
			$gateway->check_callback_response();
		}
	}
}