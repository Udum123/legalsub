<?php

namespace Rtcl\Models;

use Rtcl\Helpers\Functions;
use Rtcl\Traits\SingletonTrait;

class Checkout {
	use SingletonTrait;

	/**
	 * Checkout fields are stored here.
	 *
	 * @var array|null
	 */
	protected $fields = null;


	/**
	 * Get an array of checkout fields.
	 *
	 * @param string $fieldset to get.
	 *
	 * @return array
	 */
	function get_checkout_fields( $fieldset = '' ) {
		if ( ! is_null( $this->fields ) ) {
			return $fieldset ? $this->fields[ $fieldset ] : $this->fields;
		}
		$this->fields = [
			'billing' => rtcl()->countries->get_address_fields()
		];
		$this->fields = apply_filters( 'rtcl_checkout_fields', $this->fields );
		foreach ( $this->fields as $field_type => $fields ) {
			// Sort each of the checkout field sections based on priority.
			uasort( $this->fields[ $field_type ], [ Functions::class, 'checkout_fields_uasort_comparison' ] );

			// Add accessibility labels to fields that have placeholders.
			foreach ( $fields as $single_field_type => $field ) {
				if ( empty( $field['label'] ) && ! empty( $field['placeholder'] ) ) {
					$this->fields[ $field_type ][ $single_field_type ]['label']       = $field['placeholder'];
					$this->fields[ $field_type ][ $single_field_type ]['label_class'] = [ 'screen-reader-text' ];
				}
			}
		}

		return $fieldset ? $this->fields[ $fieldset ] : $this->fields;
	}


	/**
	 * Gets the value either from POST, or from the customer object. Sets the default values in checkout fields.
	 *
	 * @param string $input Name of the input we want to grab data for. e.g. billing_country.
	 *
	 * @return string The default value.
	 */
	public function get_value( $input ) {
		// If the form was posted, get the posted value. This will only tend to happen when JavaScript is disabled client side.
		if ( ! empty( $_POST[ $input ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return Functions::clean( wp_unslash( $_POST[ $input ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Allow 3rd parties to short circuit the logic and return their own default value.
		$value = apply_filters( 'rtcl_checkout_get_value', null, $input );

		if ( ! is_null( $value ) ) {
			return $value;
		}

		if ( is_user_logged_in() ) {
			$user  = get_userdata( get_current_user_id() );
			$value = get_user_meta( $user->ID, '_' . $input, true );
			if ( ! $value ) {
				if ( 'billing_first_name' === $input ) {
					$value = $user->first_name;
				} elseif ( 'billing_last_name' === $input ) {
					$value = $user->last_name;
				} else if ( 'billing_email' === $input ) {
					$value = $user->user_email;
				} else if ( 'billing_phone' === $input ) {
					$value = get_post_meta( $user->ID, '_rtcl_phone', true );
				} elseif ( ! empty( $user->$input ) ) {
					$value = $user->$input;
				}
			}
		}

		if ( '' === $value ) {
			if ( in_array( $input, [ 'billing_country', 'billing_state' ] ) ) {
				$baseLocation = Functions::get_base_location();
				if ( 'billing_country' === $input && ! empty( $baseLocation['country'] ) ) {
					$value = $baseLocation['country'];
				} elseif ( 'billing_state' === $input && ! empty( $baseLocation['state'] ) ) {
					$value = $baseLocation['state'];
				}
			}
		}
		if ( '' === $value ) {
			$value = null;
		}

		return apply_filters( 'rtcl_default_checkout_' . $input, $value, $input );
	}
}