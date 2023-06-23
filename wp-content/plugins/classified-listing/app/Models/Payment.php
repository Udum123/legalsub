<?php

namespace Rtcl\Models;


use mysql_xdevapi\Exception;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Log\Logger;
use Rtcl\Resources\Options;

class Payment {

	protected $id;
	protected $payment;
	protected $status;
	protected $created_date;

	/**
	 * @var PaymentGateway
	 */
	public $gateway;

	/**
	 * Payment Pricing Option
	 *
	 * @var Pricing Object
	 */
	public $pricing = null;

	protected $data = [
		// Abstract order props.
		'parent_id'           => 0,
		'amount'              => 0,
		'_applied'            => null,

		// Order props.
		'customer_id'         => 0,
		'listing_id'          => 0,
		'_order_key'          => '',
		'billing'             => [
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
		],
		'payment_method'      => '',
		'_payment_method'     => '',
		'payment_type'        => '',
		'payment_option_id'   => 0,
		'_pricing_id'         => 0,
		'transaction_id'      => '',
		'customer_ip_address' => '',
		'created_via'         => '',
		'_order_currency'     => '',
		'date_completed'      => null,
		'date_paid'           => null,
	];

	function __construct( $payment_id ) {
		$post = get_post( $payment_id );
		$this->setData( $post );
	}

	/**
	 * Course is exists if the post is not empty
	 *
	 * @return bool
	 */
	public function exists() {
		return $this->payment && rtcl()->post_type_payment === $this->payment->post_type;
	}


	/**
	 * @param \WP_Post $post
	 */
	private function setData( $post ) {
		$this->id           = $post->ID;
		$this->payment      = $post;
		$this->status       = $post->post_status;
		$this->created_date = $post->post_date;
		$this->pricing      = rtcl()->factory->get_pricing( $this->get_pricing_id() );
		$this->setGateWay();
	}

	private function setGateWay() {
		$this->gateway = Functions::get_payment_gateway( $this->get_payment_method() );
	}


	private function get_prop( $prop ) {

		if ( array_key_exists( $prop, $this->data ) ) {
			return get_post_meta( $this->get_id(), $prop, true );
		}

		return null;
	}

	protected function get_address_prop( $prop, $address = 'billing' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data[ $address ] ) ) {
			return get_post_meta( $this->get_id(), '_' . $address . '_' . $prop, true );
		}

		return $value;
	}


	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop    Name of prop to set.
	 * @param string $address Name of address to set. billing or shipping.
	 * @param mixed  $value   Value of the prop.
	 */
	protected function set_address_prop( $prop, $address, $value ) {
		if ( array_key_exists( $prop, $this->data[ $address ] ) ) {
			return update_post_meta( $this->id, '_' . $address . '_' . $prop, $value );
		}

		return false;
	}

	private function set_prop( $prop, $value = null ) {
		if ( array_key_exists( $prop, $this->data ) ) {
			return update_post_meta( $this->id, $prop, $value );
		}

		return false;
	}

	/**
	 * @param string $meta_key
	 * @param bool   $single
	 *
	 * @return mixed|string
	 */
	public function get_meta( $meta_key, $single = true, $default = null ) {
		if ( ! $meta_key ) {
			return '';
		}
		$value = get_post_meta( $this->get_id(), $meta_key, $single );

		return ! is_null( $value ) ? $value : $default;
	}

	/**
	 * @param string $meta_key
	 * @param mixed  $single
	 *
	 * @return mixed|string
	 */
	public function update_meta( $meta_key, $meta_value ) {
		if ( ! $meta_key ) {
			return '';
		}

		return update_post_meta( $this->get_id(), $meta_key, $meta_value );
	}

	/**
	 * @param int    $post_id    Post ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Optional. Metadata value. If provided,
	 *                           rows will only be removed that match the value.
	 *                           Must be serializable if non-scalar. Default empty.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_meta( $meta_key, $meta_value = '' ) {
		if ( ! $meta_key ) {
			return '';
		}

		return delete_post_meta( $this->get_id(), $meta_key, $meta_value );
	}

	public function is_applied() {
		return $this->get_prop( '_applied' ) ? true : false;
	}

	/**
	 * @return bool
	 */
	public function is_membership() {
		return "membership" == get_post_meta( $this->get_id(), 'payment_type', true );
	}

	public function set_applied() {
		$this->set_prop( '_applied', 1 );
	}

	public function get_customer_ip_address() {
		return $this->get_prop( 'customer_ip_address' );
	}

	public function get_transaction_id() {
		return $this->get_prop( 'transaction_id' );
	}

	/**
	 * @return mixed
	 */
	public function get_payment_method() {
		if ( $this->get_prop( '_payment_method' ) ) {
			return $this->get_prop( '_payment_method' );
		}

		// @deprecated
		return $this->get_prop( 'payment_method' );
	}

	public function get_payment_method_title() {

		$title = get_post_meta( $this->get_id(), '_payment_method_title', true );
		$title = $title ? $title : ( $this->gateway ? $this->gateway->method_title : '' );

		return apply_filters( 'rtcl_display_payment_method_title', $title, $this );
	}

	/**
	 * @return mixed
	 */
	public function get_pricing_id() {
		if ( $this->get_prop( '_pricing_id' ) ) {
			return $this->get_prop( '_pricing_id' );
		}

		// @deprecated
		return $this->get_prop( 'payment_option_id' );
	}

	public function get_date_paid() {
		return $this->get_prop( 'date_paid' );
	}

	public function get_created_date() {
		return $this->created_date;
	}

	public function set_date_paid( $date ) {
		$this->set_prop( 'date_paid', $date );
	}

	public function get_id() {
		return $this->id;
	}

	/**
	 * @return int|string
	 */
	public function get_wc_id() {
		$wc_id = absint( get_post_meta( $this->get_id(), '_woo_order_id', true ) );
		if ( ! $wc_id ) {
			return '';
		}

		return $wc_id;
	}

	/**
	 * Return WC order id if WC order , Otheårwise will return RTCL order id
	 *
	 * @return string
	 */
	public function get_maybe_id() {
		return $this->get_wc_id() ? 'wc-' . $this->get_wc_id() : $this->id;
	}

	public function get_order_key() {
		return $this->get_prop( '_order_key' );
	}

	/**
	 * Return Listing ID
	 *
	 * @return mixed|null
	 */
	public function get_listing_id() {
		return $this->get_prop( 'listing_id' );
	}

	public function get_customer_id() {
		return absint( $this->get_prop( 'customer_id' ) );
	}

	public function get_edit_order_url() {
		return apply_filters( 'rtcl_get_order_edit_url', get_admin_url( null, 'post.php?post=' . $this->get_id() . '&action=edit' ), $this );
	}

	/**
	 * Gets the order number for display (by default, order ID).
	 *
	 * @return string
	 */
	public function get_order_number() {
		return (string) apply_filters( 'rtcl_get_order_number', $this->get_id(), $this );
	}


	/**
	 * Set order_currency.
	 *
	 * @param string $value Value to set.
	 *
	 */
	public function set_currency( $value ) {
		if ( $value && ! in_array( $value, array_keys( Options::get_currency_list() ), true ) ) {
			Functions::add_notice( __( 'Invalid currency code', 'classified-listing' ), 'error', 'order_invalid_currency' );
		}

		$this->set_prop( '_order_currency', $value ? $value : Functions::get_order_currency() );
	}

	/**
	 * Gets order currency.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return string
	 */
	public function get_currency() {
		$currency = $this->get_prop( '_order_currency' );

		return $currency ? $currency : Functions::get_order_currency();
	}

	/**
	 * Gets the order number for display (by default, order ID).
	 *
	 * @return string
	 */
	public function get_maybe_order_number() {
		return $this->get_maybe_id() ?: $this->get_order_number();
	}

	public function get_customer_email() {
		$user_id   = $this->get_customer_id();
		$user_info = get_userdata( $user_id );

		return $user_info->user_email;
	}


	public function get_customer_full_name() {
		$user_id   = $this->get_customer_id();
		$user_info = get_userdata( $user_id );

		/* translators: 1: first name 2: last name */

		return sprintf( _x( '%1$s %2$s', 'full name', 'classified-listing' ), $user_info->first_name, $user_info->last_name );
	}

	public function get_billing_full_name() {
		return sprintf( _x( '%1$s %2$s', 'full name', 'classified-listing' ), $this->get_billing_first_name(), $this->get_billing_last_name() );
	}

	public function get_billing_first_name() {
		return $this->get_address_prop( 'first_name', 'billing' );
	}

	public function get_billing_last_name() {
		return $this->get_address_prop( 'last_name', 'billing' );
	}

	/**
	 * Get billing company.
	 *
	 * @return string
	 */
	public function get_billing_company() {
		return $this->get_address_prop( 'company', 'billing' );
	}

	/**
	 * Get billing address line 1.
	 *
	 * @return string
	 */
	public function get_billing_address_1() {
		return $this->get_address_prop( 'address_1', 'billing' );
	}

	/**
	 * Get billing address line 2.
	 *
	 * @return string
	 */
	public function get_billing_address_2() {
		return $this->get_address_prop( 'address_2', 'billing' );
	}

	/**
	 * Get billing city.
	 *
	 * @return string
	 */
	public function get_billing_city() {
		return $this->get_address_prop( 'city', 'billing' );
	}

	/**
	 * Get billing state.
	 *
	 * @return string
	 */
	public function get_billing_state() {
		return $this->get_address_prop( 'state', 'billing' );
	}

	/**
	 * Get billing postcode.
	 *
	 * @return string
	 */
	public function get_billing_postcode() {
		return $this->get_address_prop( 'postcode', 'billing' );
	}

	/**
	 * Get billing country.
	 *
	 * @return string
	 */
	public function get_billing_country() {
		return $this->get_address_prop( 'country', 'billing' );
	}

	/**
	 * Get billing email.
	 *
	 * @return string
	 */
	public function get_billing_email() {
		return $this->get_address_prop( 'email', 'billing' );
	}

	/**
	 * Get billing phone.
	 *
	 * @return string
	 */
	public function get_billing_phone() {
		return $this->get_address_prop( 'phone', 'billing' );
	}


	/**
	 * Set billing email.
	 *
	 * @param string $value Billing email.
	 */
	public function set_billing_email( $value ) {
		if ( $value && ! is_email( $value ) ) {
			throw new Exception( 'order_invalid_billing_email', __( 'Invalid billing email address', 'classified-listing' ) );
		}
		$this->set_address_prop( 'email', 'billing', sanitize_email( $value ) );
	}

	/**
	 * Set billing phone.
	 *
	 * @param string $value Billing phone.
	 *
	 */
	public function set_billing_phone( $value ) {
		$this->set_address_prop( 'phone', 'billing', $value );
	}

	/**
	 * Set billing first name.
	 *
	 * @param string $value Billing first name.
	 */
	public function set_billing_first_name( $value ) {
		$this->set_address_prop( 'first_name', 'billing', $value );
	}

	public function set_billing_last_name( $value ) {
		return $this->set_address_prop( 'last_name', 'billing', $value );
	}


	/**
	 * Set billing company.
	 *
	 * @return string
	 */
	public function set_billing_company( $value ) {
		return $this->set_address_prop( 'company', 'billing', $value );
	}

	/**
	 * Set billing address line 1.
	 *
	 * @param string $value Billing address line 1.
	 */
	public function set_billing_address_1( $value ) {
		$this->set_address_prop( 'address_1', 'billing', $value );
	}

	/**
	 * Set billing address line 2.
	 *
	 * @param string $value Billing address line 2.
	 */
	public function set_billing_address_2( $value ) {
		$this->set_address_prop( 'address_2', 'billing', $value );
	}

	/**
	 * Set billing city.
	 *
	 * @param string $value Billing city.
	 */
	public function set_billing_city( $value ) {
		$this->set_address_prop( 'city', 'billing', $value );
	}

	/**
	 * Set billing state.
	 *
	 * @param string $value Billing state.
	 */
	public function set_billing_state( $value ) {
		$this->set_address_prop( 'state', 'billing', $value );
	}

	/**
	 * Set billing postcode.
	 *
	 * @param string $value Billing postcode.
	 */
	public function set_billing_postcode( $value ) {
		$this->set_address_prop( 'postcode', 'billing', $value );
	}

	/**
	 * Set billing country.
	 *
	 * @param string $value Billing country.
	 */
	public function set_billing_country( $value ) {
		$this->set_address_prop( 'country', 'billing', $value );
	}

	public function has_status( $status ) {
		return is_array( $status ) && in_array( $this->get_status(), $status ) || $this->get_status() === $status;
	}

	public function needs_payment() {
		$valid_order_statuses = apply_filters( 'rtcl_valid_order_statuses_for_payment', [
			'rtcl-pending',
			'rtcl-failed'
		], $this );

		return apply_filters( 'rtcl_order_needs_payment', ( $this->has_status( $valid_order_statuses ) && $this->get_total() > 0 ), $this, $valid_order_statuses );
	}


	public function get_total() {
		return $this->get_prop( 'amount' );
	}

	public function set_total( $value ) {
		$this->set_prop( 'amount', Functions::get_payment_formatted_price( $value ) );
	}

	/**
	 * @return string
	 */
	public function get_listing_title() {
		return get_the_title( $this->get_listing_id() );
	}


	/**
	 * @return mixed|void
	 */
	public function get_status() {
		$status = $this->status;
		if ( empty( $this->status ) ) {
			$status = apply_filters( 'rtcl_default_order_status', 'rtcl-pending' );
		}

		return $status;
	}


	/**
	 * Before set It need to check a valid status
	 *
	 * @param $new_status
	 *
	 * @return array
	 */
	public function set_status( $new_status ) {
		$old_status  = $this->get_status();
		$new_status  = 'rtcl-' === substr( $new_status, 0, 5 ) ? $new_status : 'rtcl-' . $new_status;
		$status_list = array_keys( Options::get_payment_status_list() );
		if ( ! in_array( $new_status, $status_list ) ) {
			$new_status = 'rtcl-pending';
		}

		return [
			'from' => $old_status,
			'to'   => $new_status
		];
	}

	/**
	 * Updates status of order immediately. Order must exist.
	 *
	 * @param string $new_status Status to change the order to. No internal wc- prefix is required.
	 * @param bool   $manual
	 *
	 * @return bool
	 * @uses Payment::set_status()
	 */
	public function update_status( $new_status, $manual = false ) {
		try {
			if ( ! $this->get_id() ) {
				return false;
			}

			$this->status_transition( $new_status );
		} catch ( \Exception $e ) {
			$logger = new Logger();
			$logger->error( sprintf( 'Update status of order #%d failed!', $this->get_id() ), [
				'order' => $this,
				'error' => $e,
			] );

			return false;
		}

		return true;
	}

	/**
	 * Handle the status transition.
	 *
	 * @param $new_status
	 */
	protected function status_transition( $new_status ) {

		$result = $this->set_status( $new_status );
		if ( is_array( $result ) && ! empty( $result['from'] ) && $result['to'] && ( $result['from'] !== $result['to'] ) ) {
			wp_update_post( [
				'ID'                => $this->get_id(),
				'post_status'       => $result['to'],
				'post_modified'     => current_time( 'mysql' ),
				'post_modified_gmt' => current_time( 'mysql', 1 ),
			] );
		}

	}

	public function payment_complete( $transaction_id = '' ) {
		try {
			if ( ! $this->get_id() ) {
				return false;
			}

			if ( $this->has_status( [
				'rtcl-created',
				'rtcl-on-hold',
				'rtcl-pending',
				'rtcl-failed',
				'rtcl-cancelled'
			] ) ) {
				if ( ! empty( $transaction_id ) ) {
					$this->set_transaction_id( $transaction_id );
				}
				if ( ! $this->get_date_paid() ) {
					$this->set_date_paid( Functions::datetime() );
				}
				$this->update_status( 'rtcl-completed' );
			}
		} catch ( \Exception $e ) {
			return false;
		}

		return true;
	}

	public function set_transaction_id( $transaction_id ) {
		$this->set_prop( 'transaction_id', $transaction_id );
	}

	public function set_order_key() {
		if ( empty( $this->get_order_key() ) ) {
			$this->set_prop( '_order_key', '' . apply_filters( 'rtcl_generate_order_key', uniqid( 'rtcl_order_' ) ) );
		}
	}

	public function get_details() {
		ob_start();
		?>
		<table border="0" cellspacing="0" cellpadding="7" style="border:1px solid #CCC;">
			<tr style="background-color:#F0F0F0;">
				<th colspan="2"><?php echo get_the_title( $this->get_listing_id() ); ?> (<span
						class="listing-id"><?php _e( "ID#", 'classified-listing' );
						echo absint( $this->get_listing_id() ) ?></span>)
				</th>
			</tr>
			<tr>
				<td style="border-right:1px solid #CCC; border-bottom:1px solid #CCC; text-align:right;"><?php _e( 'Pricing ',
						'classified-listing' ); ?></td>
				<td><?php echo $this->pricing->getTitle(); ?></td>
			</tr>
			<tr>
				<td style="border-right:1px solid #CCC; border-bottom:1px solid #CCC; text-align:right;"><?php _e( 'Visible ',
						'classified-listing' ); ?></td>
				<td><?php
					printf( '<span>%s</span>', sprintf( _n( '%s Day', '%s Days', absint( $this->pricing->getVisible() ), 'classified-listing' ), number_format_i18n( absint( absint( $this->pricing->getVisible() ) ) ) ) );
					$promotions = Options::get_listing_promotions();
					foreach ( $promotions as $promo_id => $promotion ) {
						if ( $this->pricing->hasPromotion( $promo_id ) ) {
							echo '<span class="badge rtcl-badge-' . esc_attr( $promo_id ) . '">' . esc_html( $promotion ) . '</span>';
						}
					} ?></td>
			</tr>
			<tr>
				<td style="border-right:1px solid #CCC; border-bottom:1px solid #CCC; text-align:right;"><?php _e( 'Amount ',
						'classified-listing' ); ?></td>
				<td><?php echo Functions::get_payment_formatted_price_html( $this->pricing->getPrice() ); ?></td>
			</tr>
		</table>
		<?php
		return ob_get_clean();
	}

	function add_note( $note, $is_customer_note = 0, $added_by_user = false ) {
		if ( ! $this->get_id() ) {
			return 0;
		}

		if ( is_user_logged_in() && current_user_can( 'manage_rtcl_options', $this->get_id() ) && $added_by_user ) {
			$user                 = get_user_by( 'id', get_current_user_id() );
			$comment_author       = $user->display_name;
			$comment_author_email = $user->user_email;
		} else {
			$comment_author       = 'RtclListing';
			$comment_author_email = strtolower( $comment_author ) . '@';
			$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com'; // WPCS: input var ok.
			$comment_author_email = sanitize_email( $comment_author_email );
		}

		$commentData = apply_filters( 'rtcl_order_note_data',
			[
				'comment_post_ID'      => $this->get_id(),
				'comment_author'       => $comment_author,
				'comment_author_email' => $comment_author_email,
				'comment_author_url'   => '',
				'comment_content'      => $note,
				'comment_agent'        => 'RtclListing',
				'comment_type'         => 'rtcl_order_note',
				'comment_parent'       => 0,
				'comment_approved'     => 1,
			],
			[
				'oder_id'          => $this->get_id(),
				'is_customer_note' => $is_customer_note,
			]
		);

		$comment_id = wp_insert_comment( $commentData );

		if ( $is_customer_note ) {
			add_comment_meta( $comment_id, 'is_customer_note', 1 );

			do_action( 'rtcl_order_new_customer_note', [
				'order_id'      => $this->get_id(),
				'customer_note' => $commentData['comment_content'],
			] );
		}

		return $comment_id;
	}

	/**
	 * Generates a raw (unescaped) cancel-order URL for use by payment gateways.
	 *
	 * @param string $redirect Redirect URL.
	 *
	 * @return string The unescaped cancel-order URL.
	 */
	public function get_cancel_payment_url_raw( $redirect = '' ) {
		return apply_filters(
			'rtcl_get_cancel_payment_url_raw',
			add_query_arg(
				[
					'rtcl_return'    => 'cancel',
					'cancel_payment' => 'true',
					'order_key'      => $this->get_order_key(),
					'payment_id'     => $this->get_id(),
					'redirect'       => $redirect,
					'_wpnonce'       => wp_create_nonce( 'rtcl-cancel_payment' ),
				],
				$this->get_cancel_endpoint()
			)
		);
	}

	/**
	 * Helper method to return the cancel endpoint.
	 *
	 * @return string the cancel endpoint; either the cart page or the home page.
	 */
	public function get_cancel_endpoint() {
		$cancel_endpoint = Link::get_account_endpoint_url();
		if ( ! $cancel_endpoint ) {
			$cancel_endpoint = home_url();
		}

		if ( false === strpos( $cancel_endpoint, '?' ) ) {
			$cancel_endpoint = trailingslashit( $cancel_endpoint );
		}

		return $cancel_endpoint;
	}
}