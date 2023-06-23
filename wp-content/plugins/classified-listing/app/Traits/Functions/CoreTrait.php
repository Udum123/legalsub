<?php

namespace Rtcl\Traits\Functions;

use Collator;
use IntlException;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Geolocation;

trait CoreTrait {

	public static function verify_nonce() {
		$nonce     = isset( $_REQUEST[ rtcl()->nonceId ] ) ? $_REQUEST[ rtcl()->nonceId ] : null;
		$nonceText = rtcl()->nonceText;
		if ( wp_verify_nonce( $nonce, $nonceText ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Get the user's default location.
	 *
	 * Filtered, and set to base location or left blank. If cache-busting,
	 * this should only be used when 'location' is set in the querystring.
	 *
	 * @return array
	 * @since 2.0.15
	 */
	public static function get_user_default_location() {
		$set_default_location_to = get_option( 'rtcl_default_customer_address', 'base' );
		$default_location        = '';//=== $set_default_location_to ? '' : get_option('woocommerce_default_country', 'US:CA');
		$location                = [];//wc_format_country_state_string(apply_filters('rtcl_user_default_location', $default_location));
		$set_default_location_to = 'geolocation';
		// Geolocation takes priority if used and if geolocation is possible.
		if ( 'geolocation' === $set_default_location_to || 'geolocation_ajax' === $set_default_location_to ) {
			$ua = self::get_user_agent();

			// Exclude common bots from geolocation by user agent.
			if ( ! stristr( $ua, 'bot' ) && ! stristr( $ua, 'spider' ) && ! stristr( $ua, 'crawl' ) ) {
				$geolocation = Geolocation::geolocate_ip( '', true, false );
				if ( ! empty( $geolocation['country'] ) ) {
					$location = $geolocation;
				}
			}
		}

		return apply_filters( 'rtcl_user_default_location_array', $location );
	}

	/**
	 * Get user agent string.
	 *
	 * @return string
	 * @since  2.0.15
	 */
	public static function get_user_agent() {
		return isset( $_SERVER['HTTP_USER_AGENT'] ) ? Functions::clean( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : ''; // @codingStandardsIgnoreLine
	}


	static function checkout_fields_uasort_comparison( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return 0;
		}

		return self::uasort_comparison( $a['priority'], $b['priority'] );
	}

	static function uasort_comparison( $a, $b ) {
		if ( $a === $b ) {
			return 0;
		}

		return ( $a < $b ) ? - 1 : 1;
	}


	/**
	 * Sort array according to current locale rules and maintaining index association.
	 *
	 * @param array $data List of values to sort.
	 * @param string $locale Locale.
	 *
	 * @return array
	 */
	static function asort_by_locale( &$data, $locale = '' ) {
		// Use Collator if PHP Internationalization Functions (php-intl) is available.
		if ( class_exists( 'Collator' ) ) {
			try {
				$locale   = $locale ? $locale : get_locale();
				$collator = new Collator( $locale );
				$collator->asort( $data, Collator::SORT_STRING );

				return $data;
			} catch ( IntlException $e ) {
				error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					sprintf(
						'An unexpected error occurred while trying to use PHP Intl Collator class, it may be caused by an incorrect installation of PHP Intl and ICU, and could be fixed by reinstallaing PHP Intl, see more details about PHP Intl installation: %1$s. Error message: %2$s',
						'https://www.php.net/manual/en/intl.installation.php',
						$e->getMessage()
					)
				);
			}
		}

		$raw_data = $data;

		array_walk(
			$data,
			function ( &$value ) {
				$value = remove_accents( html_entity_decode( $value ) );
			}
		);

		uasort( $data, 'strcmp' );

		foreach ( $data as $key => $val ) {
			$data[ $key ] = $raw_data[ $key ];
		}

		return $data;
	}
}