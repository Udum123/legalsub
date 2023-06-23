<?php

namespace Rtcl\Models;

use Rtcl\Helpers\Link;
use WP_Error;

abstract class PaymentGateway extends SettingsAPI {

	/**
	 * Set if the place order button should be renamed on selection.
	 *
	 * @var string
	 */
	public $order_button_text;

	/**
	 * yes or no based on whether the method is enabled.
	 *
	 * @var string
	 */
	public $enabled = 'yes';

	/**
	 * Payment method title for the frontend.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Payment method title for the frontend.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Payment method title for the frontend.
	 *
	 * @var string
	 */
	public $option = "payment_";

	/**
	 * Payment method description for the frontend.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Chosen payment method id.
	 *
	 * @var bool
	 */
	public $chosen;

	/**
	 * Gateway title.
	 *
	 * @var string
	 */
	public $method_title = '';

	/**
	 * Gateway description.
	 *
	 * @var string
	 */
	public $method_description = '';

	/**
	 * True if the gateway shows fields on the checkout.
	 *
	 * @var bool
	 */
	public $has_fields;

	/**
	 * Countries this gateway is allowed for.
	 *
	 * @var array
	 */
	public $countries;

	/**
	 * Available for all counties or specific.
	 *
	 * @var string
	 */
	public $availability;

	/**
	 * Icon for the gateway.
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * Supported features such as 'default_credit_card_form', 'refunds'.
	 *
	 * @var array
	 */
	public $supports = [ 'listings' ];

	/**
	 * Maximum transaction amount, zero does not define a maximum.
	 *
	 * @var int
	 */
	public $max_amount = 0;

	/**
	 * Optional URL to view a transaction.
	 *
	 * @var string
	 */
	public $view_transaction_url = '';

	/**
	 * Optional label to show for "new payment method" in the payment
	 * method/token selection radio selection.
	 *
	 * @var string
	 */
	public $new_method_label = '';

	/**
	 * Contains a users saved tokens for this gateway.
	 *
	 * @var array
	 */
	protected $tokens = [];

	/**
	 * Returns a users saved tokens for this gateway.
	 *
	 * @return array
	 * @since 2.6.0
	 */
	public function get_tokens() {
		if ( sizeof( $this->tokens ) > 0 ) {
			return $this->tokens;
		}

		if ( is_user_logged_in() && $this->supports( 'tokenization' ) ) {
			$tokens       = ''; // TODO : need to generate here
			$customer_id  = get_current_user_id();
			$gateway_id   = $this->id;
			$this->tokens = apply_filters( 'rtcl_get_customer_payment_tokens', $tokens, $customer_id, $gateway_id );
		}

		return $this->tokens;
	}

	/**
	 * Return the title for admin screens.
	 *
	 * @return string
	 */
	public function get_method_title() {
		return apply_filters( 'rtcl_gateway_method_title', $this->method_title, $this );
	}

	/**
	 * Return the description for admin screens.
	 *
	 * @return string
	 */
	public function get_method_description() {
		return apply_filters( 'rtcl_gateway_method_description', $this->method_description, $this );
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function getToken( $data ) {
		return '';
	}

	/**
	 * Output the gateway settings screen.
	 */
	public function admin_options() {
		echo '<h2>' . esc_html( $this->get_method_title() ) . '</h2>';
		echo wp_kses_post( wpautop( $this->get_method_description() ) );
		parent::admin_options();
	}

	/**
	 * Init settings for gateways.
	 */
	public function init_settings() {
		parent::init_settings();
		$this->enabled = ! empty( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'] ? 'yes' : 'no';
	}

	/**
	 * Get the return url (thank you page).
	 *
	 * @param Payment $Payment
	 *
	 * @return string
	 */
	public function get_return_url( $Payment ) {

		$return_url = Link::get_checkout_endpoint_url( "payment-receipt", $Payment->get_id() );

		return apply_filters( 'rtcl_payment_get_return_url', $return_url, $Payment );
	}

	/**
	 * Get a link to the transaction on the 3rd party gateway size (if applicable).
	 *
	 * @param Payment $order the order object.
	 *
	 * @return string transaction URL, or empty string.
	 */
	public function get_transaction_url( $order ) {

		$return_url     = '';
		$transaction_id = $order->get_transaction_id();

		if ( ! empty( $this->view_transaction_url ) && ! empty( $transaction_id ) ) {
			$return_url = sprintf( $this->view_transaction_url, $transaction_id );
		}

		return apply_filters( 'rtcl_payment_get_transaction_url', $return_url, $order, $this );
	}

	/**
	 * Get the order total in checkout and pay_for_order.
	 *
	 * @return float
	 */
	protected function get_order_total() {

	}

	/**
	 * Check if the gateway is available for use.
	 *
	 * @return bool
	 */
	public function is_available() {
		return ( 'yes' === $this->enabled );
	}

	/**
	 * Check if the gateway has fields on the checkout.
	 *
	 * @return bool
	 */
	public function has_fields() {
		return $this->has_fields;
	}

	/**
	 * Return the gateway's title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'rtcl_gateway_title', $this->title, $this->id );
	}

	/**
	 * Return the gateway's description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'rtcl_gateway_description', $this->description, $this->id );
	}

	/**
	 * Return the gateway's icon.
	 *
	 * @return string
	 */
	public function get_icon_url() {
		return $this->icon;
	}

	/**
	 * Return the gateway's icon.
	 *
	 * @return string
	 */
	public function get_icon() {

		$icon_url = $this->get_icon_url();
		$icon     = $icon_url ? '<img src="' . esc_url( $icon_url ) . '" alt="' . esc_attr( $this->get_title() ) . '" />' : '';

		return apply_filters( 'rtcl_gateway_icon', $icon, $this->id );
	}

	/**
	 * Set as current gateway.
	 *
	 * Set this as the current gateway.
	 */
	public function set_current() {
		$this->chosen = true;
	}


	public function check_callback_response() {

		return false;

	}

	/**
	 * Process Payment.
	 *
	 * Process the payment. Override this in your gateway. When implemented, this should.
	 * return the success and redirect in an array. e.g:
	 *
	 *        return array(
	 *            'result'   => 'success',
	 *            'redirect' => $this->get_return_url( $order )
	 *        );
	 *
	 * @param Payment $order
	 * @param array   $data
	 *
	 * @return array
	 */
	public function process_payment( $order, $data = [] ) {
		return [];
	}

	/**
	 * Process refund.
	 *
	 * If the gateway declares 'refunds' support, this will allow it to refund.
	 * a passed in amount.
	 *
	 * @param int    $order_id
	 * @param float  $amount
	 * @param string $reason
	 *
	 * @return boolean True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		return false;
	}

	/**
	 * Validate frontend fields.
	 *
	 * Validate payment fields on the frontend.
	 *
	 * @return bool
	 */
	public function validate_fields() {
		return true;
	}

	/**
	 * If There are no payment fields show the description if set.
	 * Override this in your gateway if you have some.
	 */
	public function payment_fields() {
		$html = null;
		if ( $description = $this->get_description() ) {
			$html .= wpautop( wptexturize( $description ) );
		}

		return $html;
	}

	/**
	 * Check if a gateway supports a given feature.
	 *
	 * Gateways should override this to declare support (or lack of support) for a feature.
	 * For backward compatibility, gateways support 'products' by default, but nothing else.
	 *
	 * @param string $feature string The name of a feature to test support for.
	 *
	 * @return bool True if the gateway supports the feature, false otherwise.
	 * @since 1.5.7
	 */
	public function supports( $feature ) {
		return apply_filters( 'rtcl_payment_gateway_supports', in_array( $feature, $this->supports ), $feature, $this );
	}


	/**
	 * @return array
	 */
	public function rest_api_data() {
		return [
			'id'          => $this->id,
			'title'       => strip_tags( $this->get_title() ),
			'icon'        => $this->get_icon_url(),
			'description' => strip_tags( $this->get_description() )
		];
	}

	/**
	 * @param string $id
	 *
	 * @return string
	 */
	public function getApiRequestUrl( $id ) {
		if ( empty( $id ) ) {
			return '';
		}

		return add_query_arg( 'rtcl-api', $id, trailingslashit( get_home_url() ) );
	}


	/**
	 * @return bool|WP_Error
	 */
	public function cancelSubscription( $subscription ) {
		return null;
	}

}
