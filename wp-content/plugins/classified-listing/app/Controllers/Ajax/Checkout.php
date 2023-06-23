<?php

namespace Rtcl\Controllers\Ajax;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Traits\SingletonTrait;
use WP_Error;

/**
 * Class Checkout
 *
 * @package Rtcl\Controllers\Ajax
 */
class Checkout {

	use SingletonTrait;

	function __construct() {
		add_action( 'wp_ajax_rtcl_ajax_checkout_action', [ $this, 'rtcl_ajax_checkout_action' ] );
	}

	function rtcl_ajax_checkout_action() {
		Functions::clear_notices();
		$success              = false;
		$redirect_url         = $gateway_id = null;
		$payment_process_data = [];

		if ( isset( $_POST['rtcl_checkout_nonce'] ) && wp_verify_nonce( $_POST['rtcl_checkout_nonce'], 'rtcl_checkout' ) ) {
			$pricing_id     = isset( $_REQUEST['pricing_id'] ) ? absint( $_REQUEST['pricing_id'] ) : 0;
			$payment_method = isset( $_REQUEST['payment_method'] ) ? sanitize_key( $_POST['payment_method'] ) : '';
			$checkout_data  = apply_filters( 'rtcl_checkout_process_data', wp_parse_args( $_REQUEST, [
				'type'           => '',
				'listing_id'     => 0,
				'pricing_id'     => $pricing_id,
				'payment_method' => $payment_method
			] ) );
			$pricing        = rtcl()->factory->get_pricing( $checkout_data['pricing_id'] );
			$gateway        = Functions::get_payment_gateway( $checkout_data['payment_method'] );
			// Use WP_Error to handle checkout errors.
			$errors = new WP_Error();
			do_action( 'rtcl_checkout_data', $checkout_data, $pricing, $gateway, $_REQUEST, $errors );
			$errors = apply_filters( 'rtcl_checkout_validation_errors', $errors, $checkout_data, $pricing, $gateway, $_REQUEST );

			if ( is_wp_error( $errors ) && $errors->has_errors() ) {
				Functions::add_notice( $errors->get_error_message(), 'error' );
			} else {
				$current_user = wp_get_current_user();
				$metaInputs   = [
					'customer_id'           => $current_user->ID,
					'customer_ip_address'   => Functions::get_ip_address(),
					'_order_key'            => apply_filters( 'rtcl_generate_order_key', uniqid( 'rtcl_oder_' ) ),
					'_pricing_id'           => $pricing->getId(),
					'amount'                => $pricing->getPrice(),
					'_payment_method'       => $gateway->id,
					'_payment_method_title' => $gateway->method_title,
					'_order_currency'       => Functions::get_order_currency(),
					'_billing_email'        => $current_user ? $current_user->user_email : null
				];
				if ( $current_user->first_name ) {
					$metaInputs['_billing_first_name'] = $current_user->first_name;
				}
				if ( $current_user->last_name ) {
					$metaInputs['_billing_last_name'] = $current_user->last_name;
				}
				if ( ! Functions::is_billing_address_disabled() ) {
					$checkout      = rtcl()->checkout();
					$billingFields = $checkout->get_checkout_fields( 'billing' );
					if ( ! empty( $billingFields ) ) {
						foreach ( $billingFields as $_key => $field ) {
							if ( $_value = $checkout->get_value( $_key ) ) {
								if ( 'billing_email' === $_key ) {
									if ( is_email( $_value ) ) {
										$metaInputs[ '_' . $_key ] = $_value;
										update_user_meta( $current_user->ID, '_' . $_key, $_value );
									}
								} else {
									$metaInputs[ '_' . $_key ] = $_value;
									update_user_meta( $current_user->ID, '_' . $_key, $_value );
								}
							}
						}
					}
				}
				$newOrderArgs = [
					'post_title'  => esc_html__( 'Order on', 'classified-listing' ) . ' ' . current_time( "l jS F Y h:i:s A" ),
					'post_status' => 'rtcl-created',
					'post_parent' => '0',
					'ping_status' => 'closed',
					'post_author' => 1,
					'post_type'   => rtcl()->post_type_payment,
					'meta_input'  => $metaInputs
				];

				$order_id = wp_insert_post( apply_filters( 'rtcl_checkout_process_new_order_args', $newOrderArgs, $pricing, $gateway, $checkout_data ) );

				if ( $order_id ) {
					$order = rtcl()->factory->get_order( $order_id );
					$order->set_order_key();
					do_action( 'rtcl_checkout_process_new_payment_created', $order_id, $order );
					// process payment
					if ( $order->get_total() > 0 ) {

						$payment_process_data = $gateway->process_payment( $order );
						$payment_process_data = apply_filters( 'rtcl_checkout_process_payment_result', $payment_process_data, $order );
						$redirect_url         = ! empty( $payment_process_data['redirect'] ) ? $payment_process_data['redirect'] : null;
						// Redirect to success/confirmation/payment page
						if ( isset( $payment_process_data['result'] ) && 'success' === $payment_process_data['result'] ) {
							$success = true;
							do_action( 'rtcl_checkout_process_success', $order, $payment_process_data );
						} else {
							wp_delete_post( $order->get_id(), true );
							if ( ! empty( $payment_process_data['message'] ) ) {
								Functions::add_notice( $payment_process_data['message'], 'error' );
							}
							do_action( 'rtcl_checkout_process_error', $order, $payment_process_data );
						}

					} else {
						$success = true;
						$gateway = Functions::get_payment_gateway( 'offline' );
						update_post_meta( $order->get_id(), '_payment_method', $gateway->id );
						update_post_meta( $order->get_id(), '_payment_method_title', $gateway->method_title );
						$order->payment_complete( wp_generate_password( 12, true ) );
						$redirect_url = Link::get_payment_receipt_page_link( $order_id );
						Functions::add_notice( esc_html__( "Payment successfully made.", "classified-listing" ) );
						do_action( 'rtcl_checkout_process_success', $order, $payment_process_data );
					}
				} else {
					Functions::add_notice( esc_html__( "Error to create payment.", "classified-listing" ), 'error' );
				}
			}

		} else {
			Functions::add_notice( esc_html__( "Session error", "classified-listing" ), 'error' );
		}

		$error_message   = Functions::get_notices( 'error' );
		$success_message = Functions::get_notices( 'success' );
		Functions::clear_notices();
		$res_data = wp_parse_args( $payment_process_data, [
			'error_message'   => $error_message,
			'success_message' => $success_message,
			'success'         => $success,
			'redirect_url'    => $redirect_url,
			'gateway_id'      => $gateway_id
		] );
		wp_send_json( apply_filters( 'rtcl_checkout_process_ajax_response_args', $res_data ) );

	}

}