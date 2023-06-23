<?php

namespace Rtcl\Gateways\Paypal;


use Rtcl\Gateways\Paypal\lib\GatewayPaypalRequest;
use Rtcl\Gateways\Paypal\lib\PayPalApiHandler;
use Rtcl\Helpers\Functions;
use Rtcl\Log\Logger;
use Rtcl\Models\Payment;
use Rtcl\Models\PaymentGateway;

class GatewayPaypal extends PaymentGateway
{

	protected $ipn_notification;
	protected $testmode;
	protected $email;
	protected $receiver_email;


	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id = 'paypal';
		$this->option = $this->option . $this->id;
		$this->order_button_text = __('Proceed to PayPal', 'classified-listing');
		$this->method_title = __('PayPal', 'classified-listing');
		$this->method_description = __('PayPal Standard sends customers to PayPal to enter their payment information. PayPal IPN requires fsockopen/cURL support to update order statuses after payment. Check the <a href="%s">system status</a> page for more details.',
			'classified-listing');

		// Load the settings.
		$this->init_form_fields();

		$this->init_settings();

		// Define user set variables.
		$this->enable = $this->get_option('enable');
		$this->title = $this->get_option('title');
		$this->description = $this->get_option('description');
		$this->testmode = 'yes' === $this->get_option('testmode', 'no');
		$this->email = $this->get_option('email');
		$this->ipn_notification = 'yes' === $this->get_option('ipn_notification', 'no');
		$this->receiver_email = $this->get_option('receiver_email', $this->email);

		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
		add_action('rtcl_api_rtcl_gateway_paypal', array($this, 'check_callback_response'));
		add_action('rtcl-on-hold_to_rtcl-processing', array($this, 'capture_payment'));
		add_action('rtcl-on-hold_to_rtcl-completed', array($this, 'capture_payment'));
	}


	/**
	 * Return the gateway's icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		$icon = $this->get_icon_image();
		if (is_array($icon) && !empty($icon)) {
			return $icon[0];
		}
		return $icon;
	}

	/**
	 * Get gateway icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		$icon_html = '';
		$icon = (array)$this->get_icon_image();

		foreach ($icon as $i) {
			$icon_html .= '<img src="' . esc_attr($i) . '" alt="' . esc_attr__('PayPal acceptance mark',
					'classified-listing') . '" />';
		}

		$icon_html .= sprintf('<a href="%1$s" class="about_paypal" onclick="javascript:window.open(\'%1$s\',\'WIPaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700\'); return false;">' . esc_attr__('What is PayPal?',
				'classified-listing') . '</a>', esc_url($this->get_icon_link_url()));

		return apply_filters('rtcl_gateway_icon', $icon_html, $this->id);
	}

	/**
	 * Get the link for an icon based on country.
	 *
	 * @param string $country
	 *
	 * @return string
	 */
	protected function get_icon_link_url($country = 'US') {
		$url = 'https://www.paypal.com/' . strtolower($country);
		$home_counties = array('BE', 'CZ', 'DK', 'HU', 'IT', 'JP', 'NL', 'NO', 'ES', 'SE', 'TR', 'IN');
		$countries = array(
			'DZ',
			'AU',
			'BH',
			'BQ',
			'BW',
			'CA',
			'CN',
			'CW',
			'FI',
			'FR',
			'DE',
			'GR',
			'HK',
			'ID',
			'JO',
			'KE',
			'KW',
			'LU',
			'MY',
			'MA',
			'OM',
			'PH',
			'PL',
			'PT',
			'QA',
			'IE',
			'RU',
			'BL',
			'SX',
			'MF',
			'SA',
			'SG',
			'SK',
			'KR',
			'SS',
			'TW',
			'TH',
			'AE',
			'GB',
			'US',
			'VN'
		);

		if (in_array($country, $home_counties)) {
			return $url . '/webapps/mpp/home';
		} elseif (in_array($country, $countries)) {
			return $url . '/webapps/mpp/paypal-popup';
		} else {
			return $url . '/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside';
		}
	}

	/**
	 * Get PayPal images for a country.
	 *
	 * @param string $country Country code.
	 *
	 * @return array|string of image URLs
	 */
	protected function get_icon_image($country = null) {
		switch ($country) {
			case 'US' :
			case 'NZ' :
			case 'CZ' :
			case 'HU' :
			case 'IN':
			case 'MY' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TR' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_odeme_secenekleri.jpg';
				break;
			case 'GB' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/Logo/AM_mc_vs_ms_ae_UK.png';
				break;
			case 'MX' :
				$icon = array(
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_visa_mastercard_amex.png',
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_debit_card_275x60.gif',
				);
				break;
			case 'FR' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg';
				break;
			case 'AU' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_AU/mktg/logo/Solutions-graphics-1-184x80.jpg';
				break;
			case 'DK' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_PayPal_betalingsmuligheder_dk.jpg';
				break;
			case 'RU' :
				$icon = 'https://www.paypalobjects.com/webstatic/ru_RU/mktg/business/pages/logo-center/AM_mc_vs_dc_ae.jpg';
				break;
			case 'NO' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/banner_pl_just_pp_319x110.jpg';
				break;
			case 'CA' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_CA/mktg/logo-image/AM_mc_vs_dc_ae.jpg';
				break;
			case 'HK' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_HK/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'SG' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_SG/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TW' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_TW/mktg/logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TH' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_TH/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'JP' :
				$icon = 'https://www.paypal.com/ja_JP/JP/i/bnr/horizontal_solution_4_jcb.gif';
				break;
			default :
				$icon = plugins_url('assets/images/paypal.png', __FILE__);
				break;
		}

		return apply_filters('rtcl_paypal_icon', $icon);
	}

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 *
	 * @return bool
	 */
	public function is_valid_for_use() {
		return in_array(Functions::get_order_currency(),
			apply_filters(
				'rtcl_paypal_supported_currencies',
				[
					'AUD',
					'BRL',
					'CAD',
					'MXN',
					'NZD',
					'HKD',
					'SGD',
					'USD',
					'EUR',
					'JPY',
					'TRY',
					'NOK',
					'CZK',
					'DKK',
					'HUF',
					'ILS',
					'MYR',
					'PHP',
					'PLN',
					'SEK',
					'CHF',
					'TWD',
					'THB',
					'GBP',
					'RMB',
					'RUB',
					'INR'
				]),
			true
		);
	}

	/**
	 * Admin Panel Options.
	 * - Options for bits like 'title' and availability on a country-by-country basis.
	 *
	 * @since 1.0.0
	 */
	public function process_admin_options() {
		if ($this->is_valid_for_use()) {
			parent::process_admin_options();
		} else {
			?>
			<div class="inline error"><p><strong><?php _e('Gateway disabled',
							'classified-listing'); ?></strong>: <?php _e('PayPal does not support your store currency.',
						'classified-listing'); ?></p></div>
			<?php
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'               => array(
				'title' => esc_html__('Enable/Disable', 'classified-listing'),
				'type'  => 'checkbox',
				'label' => esc_html__('Enable PayPal Standard', 'classified-listing'),
			),
			'title'                 => array(
				'title'       => esc_html__('Title', 'classified-listing'),
				'type'        => 'text',
				'default'     => 'Paypal',
				'description' => esc_html__('This controls the title which the user sees during checkout.', 'classified-listing'),
			),
			'description'           => array(
				'title'       => esc_html__('Description', 'classified-listing'),
				'type'        => 'text',
				'description' => esc_html__('This controls the description which the user sees during checkout.',
					'classified-listing'),
				'default'     => esc_html__("Pay via PayPal; you can pay with your credit card if you don't have a PayPal account.",
					'classified-listing'),
			),
			'email'                 => array(
				'title'       => esc_html__('PayPal email', 'classified-listing'),
				'type'        => 'email',
				'description' => esc_html__('Please enter your PayPal email address; this is needed in order to take payment.',
					'classified-listing'),
				'default'     => get_option('admin_email'),
				'placeholder' => 'you@youremail.com',
			),
			'advanced'              => array(
				'title'       => esc_html__('Advanced options', 'classified-listing'),
				'type'        => 'title',
				'description' => '',
			),
			'testmode'              => array(
				'title'       => esc_html__('PayPal sandbox', 'classified-listing'),
				'type'        => 'checkbox',
				'label'       => esc_html__('Enable PayPal sandbox', 'classified-listing'),
				'default'     => 'no',
				'description' => sprintf(__('PayPal sandbox can be used to test payments. Sign up for a <a href="%s">developer account</a>.', 'classified-listing'), 'https://developer.paypal.com/'),
			),
			'ipn_notification'      => array(
				'title'       => esc_html__('IPN Email Notifications', 'classified-listing'),
				'type'        => 'checkbox',
				'label'       => esc_html__('Enable IPN email notifications', 'classified-listing'),
				'default'     => 'yes',
				'description' => esc_html__('Send notifications when an IPN is received from PayPal indicating refunds, chargebacks and cancellations.', 'classified-listing'),
			),
			'receiver_email'        => array(
				'title'       => esc_html__('Receiver email', 'classified-listing'),
				'type'        => 'email',
				'description' => esc_html__('If your main PayPal email differs from the PayPal email entered above, input your main receiver email for your PayPal account here. This is used to validate IPN requests.', 'classified-listing'),
				'default'     => '',
				'placeholder' => 'you@youremail.com',
			),
			//			'identity_token'   => array(
			//				'title'       => esc_html__( 'PayPal identity token', 'classified-listing' ),
			//				'type'        => 'text',
			//				'description' => esc_html__( 'Optionally enable "Payment Data Transfer" (Profile > Profile and Settings > My Selling Tools > Website Preferences) and then copy your identity token here. This will allow payments to be verified without the need for PayPal IPN.', 'classified-listing' ),
			//				'default'     => '',
			//				'placeholder' => '',
			//			),
			'paymentaction'         => array(
				'title'       => esc_html__('Payment action', 'classified-listing'),
				'type'        => 'select',
				'class'       => 'rtcl-select2',
				'description' => esc_html__('Choose whether you wish to capture funds immediately or authorize payment only.', 'classified-listing'),
				'default'     => 'sale',
				'options'     => array(
					'sale'          => esc_html__('Capture', 'classified-listing'),
					'authorization' => esc_html__('Authorize', 'classified-listing'),
				),
			),
			'page_style'            => array(
				'title'       => esc_html__('Page style', 'classified-listing'),
				'type'        => 'text',
				'description' => esc_html__('Optionally enter the name of the page style you wish to use. These are defined within your PayPal account. This affects classic PayPal checkout screens.', 'classified-listing'),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
			'image_url'             => array(
				'title'       => esc_html__('Image url', 'classified-listing'),
				'type'        => 'text',
				'description' => wp_kses(
					__('Optionally enter the URL to a <span style="color:red">150x50px (Not more than this)</span> image displayed as your logo in the upper left corner of the PayPal checkout pages.', 'classified-listing'), [
					'span' => [
						'style' => []
					]
				]),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
			'api_details'           => array(
				'title'       => esc_html__('API credentials', 'classified-listing'),
				'type'        => 'title',
				'description' => sprintf(__('Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your <a href="%s">PayPal API Credentials</a>.', 'classified-listing'), 'https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#creating-an-api-signature'),
			),
			'api_username'          => array(
				'title'       => esc_html__('Live API username', 'classified-listing'),
				'type'        => 'text',
				'description' => esc_html__('Get your API credentials from PayPal.', 'classified-listing'),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
			'api_password'          => array(
				'title'       => esc_html__('Live API password', 'classified-listing'),
				'type'        => 'password',
				'description' => esc_html__('Get your API credentials from PayPal.', 'classified-listing'),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
			'api_signature'         => array(
				'title'       => esc_html__('Live API signature', 'classified-listing'),
				'type'        => 'text',
				'description' => esc_html__('Get your API credentials from PayPal.', 'classified-listing'),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
			'sandbox_api_username'  => array(
				'title'       => esc_html__('Sandbox API username', 'classified-listing'),
				'type'        => 'text',
				'description' => esc_html__('Get your API credentials from PayPal.', 'classified-listing'),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
			'sandbox_api_password'  => array(
				'title'       => esc_html__('Sandbox API password', 'classified-listing'),
				'type'        => 'password',
				'description' => esc_html__('Get your API credentials from PayPal.', 'classified-listing'),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
			'sandbox_api_signature' => array(
				'title'       => esc_html__('Sandbox API signature', 'classified-listing'),
				'type'        => 'text',
				'description' => esc_html__('Get your API credentials from PayPal.', 'classified-listing'),
				'default'     => '',
				'placeholder' => esc_html__('Optional', 'classified-listing'),
			),
		);
	}

	/**
	 * Get the transaction URL.
	 *
	 * @param Payment $order
	 *
	 * @return string
	 */
	public function get_transaction_url($order) {
		if ($this->testmode) {
			$this->view_transaction_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		} else {
			$this->view_transaction_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		}

		return parent::get_transaction_url($order);
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param Payment $order
	 * @param array   $data
	 *
	 * @return array
	 */
	public function process_payment($order, $data = []) {

		if (!$order instanceof Payment) {
			return [
				'result'   => 'error',
				'message'  => esc_html__('Payment not found', 'classified-listing'),
				'redirect' => null,
			];
		}
		$paypal_request = new GatewayPaypalRequest($this);

		return array(
			'result'   => 'success',
			'redirect' => $paypal_request->get_request_url($order, $this->testmode),
		);
	}


	public function check_callback_response() {

		if (!empty($_POST) && $this->validate_ipn()) {
			$posted = wp_unslash($_POST);

			if (!empty($posted['custom']) && ($payment = $this->get_paypal_order($posted['custom']))) {
				$log = new Logger();
				// Lowercase returned variables.
				$posted['payment_status'] = strtolower($posted['payment_status']);
				$log->info('Found order #' . $payment->get_id());
				$log->info('Payment status: ' . $posted['payment_status']);
				$log->info('ipnpb Response', $posted);
				if (method_exists($this, 'payment_status_' . $posted['payment_status'])) {
					call_user_func(array($this, 'payment_status_' . $posted['payment_status']), $payment, $posted);
				}
			}

			exit;
		}
		wp_die('PayPal IPN Request Failure', 'PayPal IPN', array('response' => 500));
	}

	public function validate_ipn() {
		$log = new Logger();
		// Get received values from post data
		$validate_ipn = wp_unslash($_POST);
		$validate_ipn['cmd'] = '_notify-validate';
		// Send back post vars to paypal
		$params = array(
			'body'        => $validate_ipn,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'Rtcl/' . RTCL_VERSION
		);

		// Post back to get a response.
		$response = wp_safe_remote_post($this->testmode ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr' : 'https://ipnpb.paypal.com/cgi-bin/webscr', $params);

		// Check to see if the request was valid.
		if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr($response['body'], 'VERIFIED')) {
			return true;
		}
		if (is_wp_error($response)) {
			$log->error('Error response: ' . $response->get_error_message());
		}

		return false;
	}

	protected function get_paypal_order($raw_custom) {
		$log = new Logger();
		// We have the data in the correct format, so get the order.
		if (($custom = json_decode($raw_custom)) && is_object($custom)) {
			$order_id = $custom->order_id;
			$order_key = $custom->order_key;

			// Nothing was found.
		} else {

			$log->error('Order ID and key were not found in "custom".');

			return false;
		}

		$order = rtcl()->factory->get_order($order_id);

		if (!$order || $order->get_order_key() !== $order_key) {
			$log->error('Order Keys do not match.');

			return false;
		}

		return $order;
	}

	/**
	 * @param Payment $payment
	 * @param         $posted
	 *
	 * @throws \Exception
	 */
	protected function payment_status_completed($payment, $posted) {
		$log = new Logger();
		if ($payment->has_status(array('rtcl-processing', 'rtcl-completed'))) {
			$message = sprintf(__('Aborting, Order #%d is already complete.', 'classified-listing'), $payment->get_id());
			$log->info($message, $posted);
			$payment->add_note($message);
			exit;
		}

		$this->validate_transaction_type($posted['txn_type']);
		$this->validate_currency($payment, $posted['mc_currency']);
		$this->validate_amount($payment, $posted['mc_gross']);
		$this->validate_receiver_email($payment, $posted['receiver_email']);
		$this->save_paypal_meta_data($payment, $posted);

		if ('completed' === $posted['payment_status'] || 'pending' === $posted['payment_status']) {
			if ($payment->has_status('cancelled')) {
				$this->payment_status_paid_cancelled_payment($payment, $posted);
			}
			$log->info('Completed, Order #' . $payment->get_id() . ' is Authorization.');
			$this->payment_complete($payment, !empty($posted['txn_id']) ? Functions::clean($posted['txn_id']) : esc_html__('IPN payment completed', 'classified-listing'));
		} else {
			if ('authorization' === $posted['pending_reason']) {
				$log->info('Aborting, Order #' . $payment->get_id() . ' is Authorization.');
				$this->payment_on_hold($payment, esc_html__('Payment authorized. Change payment status to processing or complete to capture funds.', 'classified-listing'));
			} else {
				$log->info('Aborting, Order #' . $payment->get_id() . ' is else Authorization.');
				$this->payment_on_hold($payment, sprintf(__('Payment pending (%s).', 'classified-listing'), $posted['pending_reason']));
			}
		}
	}

	/**
	 * @param Payment $payment Payment Object
	 * @param array   $posted  Posted Data
	 *
	 * @throws \Exception
	 */
	protected function payment_status_pending($payment, $posted) {
		$this->payment_status_completed($payment, $posted);
	}

	/**
	 * Handle a failed payment.
	 *
	 * @param Payment $payment Payment Object
	 * @param array   $posted  Posted Data
	 */
	protected function payment_status_failed($payment, $posted) {
		$payment->update_status('failed');
		$payment->add_note(sprintf(__('Payment %s via IPN.', 'classified-listing'), Functions::clean($posted['payment_status'])));
	}


	/**
	 * Handle a failed payment.
	 *
	 * @param Payment $payment Payment Object
	 * @param array   $posted  Posted Data
	 */
	protected function payment_status_denied($payment, $posted) {
		$this->payment_status_failed($payment, $posted);
	}

	/**
	 * Handle a failed payment.
	 *
	 * @param Payment $payment Payment Object
	 * @param array   $posted  Posted Data
	 */
	protected function payment_status_expired($payment, $posted) {
		$this->payment_status_failed($payment, $posted);
	}

	/**
	 * Handle a failed payment.
	 *
	 * @param Payment $payment Payment Object
	 * @param array   $posted  Posted Data
	 */
	protected function payment_status_voided($payment, $posted) {
		$this->payment_status_failed($payment, $posted);
	}


	/**
	 * @param       $payment Payment Payment Object
	 * @param array $posted  Posted Data
	 *
	 * @throws \Exception
	 */
	protected function payment_status_paid_cancelled_payment($payment, $posted) {
		$this->send_ipn_email_notification(
		/* translators: %s: order link. */
			sprintf(__('Payment for cancelled order %s received', 'classified-listing'), '<a class="link" href="' . esc_url($payment->get_edit_order_url()) . '">' . $payment->get_order_number() . '</a>'),
			/* translators: %s: order ID. */
			sprintf(__('Order #%s has been marked paid by PayPal IPN, but was previously cancelled. Admin handling required.', 'classified-listing'), $payment->get_order_number())
		);
	}

	/**
	 * @param Payment $payment Payment Object
	 * @param string  $reason  Posted Data
	 */
	protected function payment_on_hold($payment, $reason) {
		$payment->update_status('on-hold');
		$payment->add_note($reason);
	}


	/**
	 * Complete order, add transaction ID and note.
	 *
	 * @param Payment $payment Payment object.
	 * @param string  $txn_id  Transaction ID.
	 * @param string  $note    Payment note.
	 */
	protected function payment_complete($payment, $txn_id = '', $note = '') {
		if (!$payment->has_status(array('processing', 'completed'))) {
			$payment->payment_complete($txn_id);
			$payment->add_note($note);
		}
	}

	/**
	 * Check for a valid transaction type.
	 *
	 * @param string $txn_type Transaction type.
	 */
	protected function validate_transaction_type($txn_type) {
		$accepted_types = array(
			'cart',
			'instant',
			'express_checkout',
			'web_accept',
			'masspay',
			'send_money',
			'paypal_here'
		);

		if (!in_array(strtolower($txn_type), $accepted_types, true)) {
			$log = new Logger();
			$log->info('Aborting, Invalid type:' . $txn_type);
			exit;
		}
	}

	/**
	 * Check currency from IPN matches the order.
	 *
	 * @param Payment $order    Order object.
	 * @param string  $currency Currency code.
	 */
	protected function validate_currency($order, $currency) {
		if (Functions::get_order_currency() !== $currency) {
			$log = new Logger();
			$log->info('Payment error: Currencies do not match (sent "' . Functions::get_order_currency() . '" | returned "' . $currency . '")');

			/* translators: %s: currency code. */
			$order->update_status('on-hold');
			$order->add_note(sprintf(__('Validation error: PayPal currencies do not match (code %s).', 'classified-listing'), $currency));
			exit;
		}
	}

	/**
	 * Check payment amount from IPN matches the order.
	 *
	 * @param Payment $order  Order object.
	 * @param int     $amount Amount to validate.
	 */
	protected function validate_amount($order, $amount) {
		if (number_format($order->get_total(), 2, '.', '') !== number_format($amount, 2, '.', '')) {
			$log = new Logger();
			$log::info('Payment error: Amounts do not match (gross ' . $amount . ')');

			/* translators: %s: Amount. */
			$order->update_status('on-hold');
			$order->add_note(sprintf(__('Validation error: PayPal amounts do not match (gross %s).', 'classified-listing'), $amount));
			exit;
		}
	}

	/**
	 * Check receiver email from PayPal. If the receiver email in the IPN is different than what is stored in.
	 * Classified-listing -> Settings -> Payment -> PayPal, it will log an error about it.
	 *
	 * @param Payment $order          Order object.
	 * @param string  $receiver_email Email to validate.
	 */
	protected function validate_receiver_email($order, $receiver_email) {
		if (strcasecmp(trim($receiver_email), trim($this->receiver_email)) !== 0) {
			$log = new Logger();
			$log->info("IPN Response is for another account: {$receiver_email}. Your email is {$this->receiver_email}");

			/* translators: %s: email address . */
			$order->update_status('on-hold');
			$order->add_note(sprintf(__('Validation error: PayPal IPN response from a different email address (%s).', 'classified-listing'), $receiver_email));
			exit;
		}
	}

	/**
	 * Save important data from the IPN to the order.
	 *
	 * @param Payment $order  Order object.
	 * @param array   $posted Posted data.
	 */
	protected function save_paypal_meta_data($order, $posted) {
		if (!empty($posted['payer_email'])) {
			update_post_meta($order->get_id(), 'payer_paypal_address', Functions::clean($posted['payer_email']));
		}
		if (!empty($posted['first_name'])) {
			update_post_meta($order->get_id(), 'payer_first_name', Functions::clean($posted['first_name']));
		}
		if (!empty($posted['last_name'])) {
			update_post_meta($order->get_id(), 'payer_last_name', Functions::clean($posted['last_name']));
		}
		if (!empty($posted['payment_type'])) {
			update_post_meta($order->get_id(), '_paypal_payment_type', Functions::clean($posted['payment_type']));
		}
		if (!empty($posted['txn_id'])) {
			update_post_meta($order->get_id(), 'transaction_id', Functions::clean($posted['txn_id']));
		}
		if (!empty($posted['payment_status'])) {
			update_post_meta($order->get_id(), '_paypal_status', Functions::clean($posted['payment_status']));
		}
	}

	/**
	 * Capture payment when the order is changed from on-hold to complete or processing
	 *
	 * @param int $order_id
	 */
	public function capture_payment($order_id) {
		$order = rtcl()->factory->get_order($order_id);

		if ('paypal' === $order->get_payment_method() && 'pending' === get_post_meta($order->get_id(),
				'_paypal_status', true) && $order->get_transaction_id()) {

			PayPalApiHandler::$api_username = $this->testmode ? $this->get_option('sandbox_api_username') : $this->get_option('api_username');
			PayPalApiHandler::$api_password = $this->testmode ? $this->get_option('sandbox_api_password') : $this->get_option('api_password');
			PayPalApiHandler::$api_signature = $this->testmode ? $this->get_option('sandbox_api_signature') : $this->get_option('api_signature');
			PayPalApiHandler::$sandbox = $this->testmode;

			$result = PayPalApiHandler::do_capture($order);

			$log = new Logger();
			if (is_wp_error($result)) {
				$log->info('Capture Failed: ' . $result->get_error_message(), 'error');
				$order->add_note(sprintf(__('Payment could not captured: %s', 'classified-listing'),
					$result->get_error_message()));

				return;
			}

			$log->info('Capture Result: ', $result);

			if (!empty($result->PAYMENTSTATUS)) {
				switch ($result->PAYMENTSTATUS) {
					case 'Completed' :
						$order->add_note(sprintf(__('Payment of %1$s was captured - Auth ID: %2$s, Transaction ID: %3$s',
							'classified-listing'), $result->AMT, $result->AUTHORIZATIONID, $result->TRANSACTIONID));
						update_post_meta($order->get_id(), '_paypal_status', $result->PAYMENTSTATUS);
						update_post_meta($order->get_id(), '_transaction_id', $result->TRANSACTIONID);
						break;
					default :
						$order->add_note(sprintf(__('Payment could not captured - Auth ID: %1$s, Status: %2$s',
							'classified-listing'), $result->AUTHORIZATIONID, $result->PAYMENTSTATUS));
						break;
				}
			}
		}
	}

	/**
	 * Load admin scripts.
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts() {
		$screen = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if (!isset($_GET['tab']) || !isset($_GET['section']) || $_GET['tab'] !== 'payment' || $_GET['section'] !== $this->id || 'rtcl_listing_page_rtcl_settings' !== $screen_id) {
			return;
		}

		wp_enqueue_script('rtcl_paypal_admin', plugins_url('assets/js/paypal-admin.js', __FILE__), '', RTCL_VERSION, true);
	}


	/**
	 * Send a notification to the user handling orders.
	 *
	 * @param string $subject Email subject.
	 * @param string $message Email message.
	 *
	 * @throws \Exception
	 */
	protected function send_ipn_email_notification($subject, $message) {
		$new_order_settings = get_option('rtcl_new_order_settings', array());
		$mailer = rtcl()->mailer();
		$message = $mailer->wrap_message($subject, $message);

		if (!$this->ipn_notification) {
			return;
		}

		$mailer->send(!empty($new_order_settings['recipient']) ? $new_order_settings['recipient'] : get_option('admin_email'), strip_tags($subject), $message);
	}
}