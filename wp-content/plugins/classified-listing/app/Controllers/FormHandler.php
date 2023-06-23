<?php

namespace Rtcl\Controllers;


use Exception;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Shortcodes\MyAccount;

class FormHandler {

	public static function init() {
		add_action( 'template_redirect', [ __CLASS__, 'redirect_reset_password_link' ] );
		add_action( 'wp_loaded', [ __CLASS__, 'process_checkout' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'process_login' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'process_registration' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'process_lost_password' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'process_reset_password' ], 20 );
		add_action( 'wp_loaded', [ __CLASS__, 'cancel_payment' ], 20 );
	}


	/**
	 * Cancel a pending order.
	 */
	public static function cancel_payment() {
		if (
			isset( $_GET['cancel_payment'] ) &&
			isset( $_GET['order_key'] ) &&
			isset( $_GET['payment_id'] ) &&
			( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'rtcl-cancel_payment' ) )
		) {
			Functions::nocache_headers();
			if ( empty( rtcl()->session ) ) {
				rtcl()->initialize_session();
			}
			$order_key = wp_unslash( $_GET['order_key'] );
			$order_id  = absint( $_GET['payment_id'] );
			$order     = rtcl()->factory->get_order( $order_id );
			$redirect  = isset( $_GET['redirect'] ) ? wp_unslash( $_GET['redirect'] ) : '';

			if ( $order && get_current_user_id() === $order->get_customer_id() ) {
				$order_can_cancel = $order->has_status( apply_filters( 'rtcl_valid_order_statuses_for_cancel', [
					'rtcl-created',
					'rtcl-pending',
					'rtcl-failed'
				], $order ) );
				if ( $order_can_cancel && hash_equals( $order->get_order_key(), $order_key ) ) {

					// Cancel the order + restore stock.
					rtcl()->session->set( 'order_awaiting_payment', false );
					$order->update_status( 'cancelled', esc_html__( 'Order cancelled by customer.', 'classified-listing' ) );

					Functions::add_notice( apply_filters( 'rtcl_payment_cancelled_notice', esc_html__( 'Your order was cancelled.', 'classified-listing' ) ), apply_filters( 'rtcl_payment_cancelled_notice_type', 'notice' ) );

					do_action( 'rtcl_cancelled_order', $order );

				} elseif ( ! $order_can_cancel ) {
					Functions::add_notice( esc_html__( 'Your order can no longer be cancelled. Please contact us if you need assistance.', 'classified-listing' ), 'error' );
				} else {
					Functions::add_notice( esc_html__( 'Invalid order.', 'classified-listing' ), 'error' );
				}
			} else {
				Functions::add_notice( esc_html__( 'Invalid order.', 'classified-listing' ), 'error' );
			}

			if ( $redirect ) {
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}

	/**
	 * @throws \Exception
	 */
	static function process_checkout() {
		if ( wp_doing_ajax() ) {
			return false;
		}
		if ( isset( $_POST['rtcl-checkout'] ) && isset( $_POST['rtcl_checkout_nonce'] ) && wp_verify_nonce( $_POST['rtcl_checkout_nonce'], 'rtcl_checkout' ) ) {

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
			$errors = new \WP_Error();
			do_action( 'rtcl_checkout_data', $checkout_data, $pricing, $gateway, $_REQUEST, $errors );
			$errors = apply_filters( 'rtcl_checkout_validation_errors', $errors, $checkout_data, $pricing, $gateway, $_REQUEST );
			if ( is_wp_error( $errors ) && $errors->has_errors() ) {
				Functions::add_notice( $errors->get_error_message(), 'error' );

				return false;
			}

			rtcl()->session->set( 'order_awaiting_payment', '' );
			if ( $pricing->getPrice() > 0 ) {
				try {
					$cart = rtcl()->cart;
					$cart->empty_cart();
					if ( $cart_id = $cart->add_to_cart( $pricing->getId(), 1, $checkout_data ) ) {
						do_action( "rtcl_process_checkout_handler", $pricing, $cart_id, $checkout_data );
					}

				} catch ( Exception $e ) {
					if ( $e->getMessage() ) {
						Functions::add_notice( $e->getMessage(), 'error' );
					}

					return false;
				}
			} else {
				$gateway          = Functions::get_payment_gateway( 'offline' );
				$new_payment_args = [
					'post_title'  => __( 'Order on', 'classified-listing' ) . ' ' . current_time( "l jS F Y h:i:s A" ),
					'post_status' => 'rtcl-created',
					'post_parent' => '0',
					'ping_status' => 'closed',
					'post_author' => 1,
					'post_type'   => rtcl()->post_type_payment,
					'meta_input'  => [
						'customer_id'           => get_current_user_id(),
						'customer_ip_address'   => Functions::get_ip_address(),
						'_order_key'            => apply_filters( 'rtcl_generate_order_key', uniqid( 'rtcl_oder_' ) ),
						'_pricing_id'           => $pricing->getId(),
						'amount'                => $pricing->getPrice(),
						'_payment_method'       => $gateway->id,
						'_payment_method_title' => $gateway->method_title,
					]
				];
				$order_id         = wp_insert_post( apply_filters( 'rtcl_checkout_process_new_order_args', $new_payment_args, $pricing, $gateway, $checkout_data ) );
				if ( $order_id ) {
					$payment_process_data = [];
					$order                = rtcl()->factory->get_order( $order_id );
					$order->payment_complete( wp_generate_password( 12, true ) );
					$redirect_url = Link::get_payment_receipt_page_link( $order_id );
					Functions::add_notice( esc_html__( "Payment successfully made.", "classified-listing" ) );
					do_action( 'rtcl_checkout_process_success', $order, $payment_process_data );
					wp_redirect( $redirect_url );
					exit();
				}
			}
		}


		return true;
	}

	/**
	 * Remove key and login from query string, set cookie, and redirect to account page to show the form.
	 */
	public static function redirect_reset_password_link() {
		if ( Functions::is_account_page() && ! empty( $_GET['key'] ) && ! empty( $_GET['login'] ) ) {
			// If available, get $user_id from query string parameter for fallback purposes.
			$user    = get_user_by( 'login', sanitize_user( wp_unslash( $_GET['login'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$user_id = $user ? $user->ID : 0;

			// If the reset token is not for the current user, ignore the reset request (don't redirect).
			$logged_in_user_id = get_current_user_id();
			if ( $logged_in_user_id && $logged_in_user_id !== $user_id ) {
				Functions::add_notice( __( 'This password reset key is for a different user account. Please log out and try again.', 'classified-listing' ), 'error' );

				return;
			}

			$value = sprintf( '%d:%s', $user_id, wp_unslash( $_GET['key'] ) ); // phpcs:ignore
			MyAccount::set_reset_password_cookie( $value );

			wp_safe_redirect( add_query_arg( 'show-reset-form', 'true', Link::get_my_account_page_link( 'lost-password' ) ) );
			exit;
		}
	}

	/**
	 * @return false
	 */
	public static function process_login() {
		if ( wp_doing_ajax() ) {
			return false;
		}
		$nonce_value = Functions::get_var( $_REQUEST['rtcl-login-nonce'] );

		if ( isset( $_POST['rtcl-login'], $_POST['username'], $_POST['password'] ) && wp_verify_nonce( $nonce_value, 'rtcl-login' ) ) {
			try {
				if ( ! Functions::is_human( 'login' ) ) {
					throw new \Exception( '<strong>' . __( 'Error:', 'classified-listing' ) . '</strong> ' . __( 'Invalid Captcha: Please try again.', 'classified-listing' ) );
				}

				$creds            = [
					'user_login'    => trim( wp_unslash( $_POST['username'] ) ),
					'user_password' => $_POST['password'],
					'remember'      => isset( $_POST['rememberme'] ),
				];
				$validation_error = new \WP_Error();
				$validation_error = apply_filters( 'rtcl_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new \Exception( '<strong>' . __( 'Error:', 'classified-listing' ) . '</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $creds['user_login'] ) ) {
					throw new \Exception( '<strong>' . __( 'Error:', 'classified-listing' ) . '</strong> ' . __( 'Username is required.', 'classified-listing' ) );
				}

				// On multisite, ensure user exists on current site, if not add them before allowing login.
				if ( is_multisite() ) {
					$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

					if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
						add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
					}
				}

				// Perform the login
				$user = wp_signon( apply_filters( 'rtcl_login_credentials', $creds ), is_ssl() );

				if ( is_wp_error( $user ) ) {
					$message = $user->get_error_message();
					$message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', $message );
					throw new \Exception( $message );
				} else {

					if ( ! empty( $_REQUEST['redirect_to'] ) ) {
						$redirect = wp_unslash( $_REQUEST['redirect_to'] );
					} elseif ( Functions::get_raw_referer() ) {
						$redirect = Functions::get_raw_referer();
					} else {
						$redirect = Link::get_my_account_page_link();
					}

					wp_redirect( wp_validate_redirect( apply_filters( 'rtcl_login_redirect_to', $redirect, $user ), Link::get_my_account_page_link() ) );
					exit;
				}
			} catch ( \Exception $e ) {
				Functions::add_notice( apply_filters( 'login_errors', $e->getMessage() ), 'error' );
				do_action( 'rtcl_login_failed', $e->getMessage(), $_POST );
			}
		}
	}

	/**
	 * @return false
	 */
	public static function process_registration() {
		if ( wp_doing_ajax() ) {
			return false;
		}

		$nonce_value = isset( $_POST['rtcl-register-nonce'] ) ? $_POST['rtcl-register-nonce'] : null;

		if ( ! empty( $_POST['rtcl-register'] ) && wp_verify_nonce( $nonce_value, 'rtcl-register' ) ) {

			if ( ! Functions::is_registration_enabled() ) {
				Functions::add_notice( esc_html__( "User registration is disabled", "classified-listing" ), 'error' );

				return false;
			}
			$username         = isset( $_POST['username'] ) ? trim( $_POST['username'] ) : '';
			$password         = isset( $_POST['password'] ) ? trim( $_POST['password'] ) : '';
			$confirm_password = isset( $_POST['pass2'] ) ? trim( $_POST['pass2'] ) : '';
			$email            = isset( $_POST['email'] ) ? $_POST['email'] : '';
			$args             = [];
			if ( ! empty( $_POST['first_name'] ) ) {
				$args['first_name'] = $_POST['first_name'];
			}
			if ( ! empty( $_POST['last_name'] ) ) {
				$args['last_name'] = $_POST['last_name'];
			}
			if ( ! empty( $_POST['phone'] ) ) {
				$args['phone'] = $_POST['phone'];
			}

			try {

				$validation_error = new \WP_Error();
				$validation_error = apply_filters( 'rtcl_process_registration_errors', $validation_error, $email, $username, $password, $_POST );

				if ( empty( $confirm_password ) || $password != $confirm_password ) {
					// Passwords don't match
					$validation_error->add( 'rtcl_my_account_password_not_matched', esc_html__( "The two passwords you entered don't match.", 'classified-listing' ) );
				}

				if ( $validation_error->get_error_code() ) {
					throw new \Exception( $validation_error->get_error_message() );
				}

				/* 
				//TODO: check it later
				if ( Functions::get_option_item('rtcl_misc_settings', 'recaptcha_forms', 'registration', 'multi_checkbox') && !Functions::is_human('registration') ) { 
					throw new \Exception('<strong>' . __('Error:', 'classified-listing') . '</strong> ' . __('Invalid Captcha: Please try again.', 'classified-listing'));
				}  */

				$new_user_id = Functions::create_new_user( sanitize_email( $email ), Functions::clean( $username ), $password, $args );

				if ( is_wp_error( $new_user_id ) ) {
					throw new \Exception( $new_user_id->get_error_message() );
				}

				if ( ! empty( $_REQUEST['redirect_to'] ) ) {
					$redirect = $_REQUEST['redirect_to'];
				} elseif ( Functions::get_raw_referer() ) {
					$redirect = Functions::get_raw_referer();
				} else {
					$redirect = Link::get_page_permalink( 'myaccount' );
				}

				if ( ! apply_filters( 'rtcl_registration_need_auth_new_user', false, $new_user_id ) ) {
					Functions::set_customer_auth_cookie( $new_user_id );
					Functions::add_notice( esc_html__( "You have successfully registered.", 'classified-listing' ) );
				} else {
					Functions::add_notice( esc_html__( 'You have successfully registered on our website, Please check your email and click on the link, we sent a verification mail to verify your email address.', 'classified-listing' ) );
				}

				wp_redirect( wp_validate_redirect( apply_filters( 'rtcl_registration_redirect', $redirect ), Link::get_page_permalink( 'myaccount' ) ) );
				exit;

			} catch ( \Exception $e ) {
				Functions::add_notice( '<strong>' . esc_html__( 'Error:', 'classified-listing' ) . '</strong> ' . $e->getMessage(), 'error' );
			}
		}
	}


	/**
	 * Handle lost password form.
	 */
	public static function process_lost_password() {
		$nonce_value = Functions::get_var( $_REQUEST['rtcl-lost-password-nonce'] );
		if ( isset( $_POST['rtcl-lost-password'] ) && isset( $_POST['user_login'] ) && $nonce_value && wp_verify_nonce( $nonce_value, 'rtcl-lost-password' ) ) {
			$success = MyAccount::retrieve_password();

			// If successful, redirect to my account with query arg set.
			if ( $success ) {
				wp_redirect( add_query_arg( 'reset-link-sent', 'true', Link::get_account_endpoint_url( 'lost-password' ) ) );
				exit;
			}
		}
	}

	/**
	 * Handle reset password form.
	 */
	public static function process_reset_password() {
		$posted_fields = [
			'rtcl-reset-password',
			'password_1',
			'password_2',
			'reset_key',
			'reset_login',
			'_wpnonce'
		];
		foreach ( $posted_fields as $field ) {
			if ( ! isset( $_POST[ $field ] ) ) {
				return;
			}
			$posted_fields[ $field ] = $_POST[ $field ];
		}

		if ( ! wp_verify_nonce( $posted_fields['_wpnonce'], 'reset_password' ) ) {
			return;
		}

		$user = MyAccount::check_password_reset_key( $posted_fields['reset_key'], $posted_fields['reset_login'] );

		if ( $user instanceof \WP_User ) {
			if ( empty( $posted_fields['password_1'] ) ) {
				Functions::add_notice( __( 'Please enter your password.', 'classified-listing' ), 'error' );
			}

			if ( $posted_fields['password_1'] !== $posted_fields['password_2'] ) {
				Functions::add_notice( __( 'Passwords do not match.', 'classified-listing' ), 'error' );
			}

			$errors = new \WP_Error();

			do_action( 'validate_password_reset', $errors, $user );
			do_action( 'rtcl_validate_password_reset', $errors, $user, $posted_fields );

			Functions::add_wp_error_notices( $errors );

			if ( 0 === Functions::notice_count( 'error' ) ) {
				MyAccount::reset_password( $user, $posted_fields['password_1'] );

				do_action( 'rtcl_reset_password', $user );

				wp_redirect( add_query_arg( 'password-reset', 'true', Link::get_page_permalink( 'myaccount' ) ) );
				exit;
			}
		}
	}


}
