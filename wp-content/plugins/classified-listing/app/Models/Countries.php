<?php

namespace Rtcl\Models;

use Rtcl\Helpers\Functions;

defined( 'ABSPATH' ) || exit;

class Countries {
	/**
	 * Locales list.
	 *
	 * @var array
	 */
	public $locale = [];

	/**
	 * Locales list.
	 *
	 * @var array
	 */
	// Todo: change later
	//public $countries = false;

	/**
	 * Locales list.
	 *
	 * @var array
	 */
	// Todo: change later
	//public $states = false;

	/**
	 * List of address formats for locales.
	 *
	 * @var array
	 */
	public $address_formats = [];

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key Key.
	 *
	 * @return array|false|void
	 */
	public function __get( $key ) {
		if ( 'countries' === $key ) {
			return $this->get_countries();
		} elseif ( 'states' === $key ) {
			return $this->get_states();
		}
	}

	/**
	 * Get all countries.
	 *
	 * @return array
	 */
	public function get_countries() {
		if ( empty( $this->countries ) ) {
			$this->countries = apply_filters( 'rtcl_countries', include rtcl()->plugin_path() . '/i18n/countries.php' );
			if ( apply_filters( 'rtcl_sort_countries', true ) ) {
				Functions::asort_by_locale( $this->countries );
			}
		}

		return $this->countries;
	}

	/**
	 * Check if a given code represents a valid ISO 3166-1 alpha-2 code for a country known to us.
	 *
	 * @param string $country_code The country code to check as a ISO 3166-1 alpha-2 code.
	 *
	 * @return bool True if the country is known to us, false otherwise.
	 *
	 * @since 5.1.0
	 */
	public function country_exists( $country_code ) {
		return isset( $this->get_countries()[ $country_code ] );
	}

	/**
	 * Get all continents.
	 *
	 * @return array
	 */
	public function get_continents() {
		if ( empty( $this->continents ) ) {
			$this->continents = apply_filters( 'rtcl_continents', include rtcl()->plugin_path() . '/i18n/continents.php' );
		}

		return $this->continents;
	}

	/**
	 * Get continent code for a country code.
	 *
	 * @param string $cc Country code.
	 *
	 * @return string
	 *
	 * @since 2.6.0
	 */
	public function get_continent_code_for_country( $cc ) {
		$cc                 = trim( strtoupper( $cc ) );
		$continents         = $this->get_continents();
		$continents_and_ccs = wp_list_pluck( $continents, 'countries' );
		foreach ( $continents_and_ccs as $continent_code => $countries ) {
			if ( in_array( $cc, $countries, true ) ) {
				return $continent_code;
			}
		}

		return '';
	}

	/**
	 * Get calling code for a country code.
	 *
	 * @param string $cc Country code.
	 *
	 * @return string|array Some countries have multiple. The code will be stripped of - and spaces and always be prefixed with +.
	 *
	 * @since 3.6.0
	 */
	public function get_country_calling_code( $cc ) {
		$codes = wp_cache_get( 'calling-codes', 'countries' );

		if ( ! $codes ) {
			$codes = include rtcl()->plugin_path() . '/i18n/phone.php';
			wp_cache_set( 'calling-codes', $codes, 'countries' );
		}

		$calling_code = isset( $codes[ $cc ] ) ? $codes[ $cc ] : '';

		if ( is_array( $calling_code ) ) {
			$calling_code = $calling_code[0];
		}

		return $calling_code;
	}

	/**
	 * Get continents that the store ships to.
	 *
	 * @return array
	 *
	 * @since 3.6.0
	 */
	public function get_shipping_continents() {
		$continents             = $this->get_continents();
		$shipping_countries     = $this->get_shipping_countries();
		$shipping_country_codes = array_keys( $shipping_countries );
		$shipping_continents    = [];

		foreach ( $continents as $continent_code => $continent ) {
			if ( count( array_intersect( $continent['countries'], $shipping_country_codes ) ) ) {
				$shipping_continents[ $continent_code ] = $continent;
			}
		}

		return $shipping_continents;
	}

	/**
	 * Load the states.
	 *
	 * @deprecated 3.6.0 This method was used to load state files, but is no longer needed. @see get_states().
	 */
	public function load_country_states() {
		global $states;

		$states       = include rtcl()->plugin_path() . '/i18n/states.php';
		$this->states = apply_filters( 'rtcl_states', $states );
	}

	/**
	 * Get the states for a country.
	 *
	 * @param string $cc Country code.
	 *
	 * @return false|array of states
	 */
	public function get_states( $cc = null ) {
		if ( ! isset( $this->states ) ) {
			$this->states = apply_filters( 'rtcl_states', include rtcl()->plugin_path() . '/i18n/states.php' );
		}

		if ( ! is_null( $cc ) ) {
			return isset( $this->states[ $cc ] ) ? $this->states[ $cc ] : false;
		} else {
			return $this->states;
		}
	}

	/**
	 * Get the base address (first line) for the store.
	 *
	 * @return string
	 *
	 * @since 3.1.1
	 */
	public function get_base_address() {
		$base_address = get_option( 'rtcl_store_address', '' );

		return apply_filters( 'rtcl_countries_base_address', $base_address );
	}

	/**
	 * Get the base address (second line) for the store.
	 *
	 * @return string
	 *
	 * @since 3.1.1
	 */
	public function get_base_address_2() {
		$base_address_2 = get_option( 'rtcl_store_address_2', '' );

		return apply_filters( 'rtcl_countries_base_address_2', $base_address_2 );
	}

	/**
	 * Get the base country for the store.
	 *
	 * @return string
	 */
	public function get_base_country() {
		$default = Functions::get_base_location();

		return apply_filters( 'rtcl_countries_base_country', $default['country'] );
	}

	/**
	 * Get the base state for the store.
	 *
	 * @return string
	 */
	public function get_base_state() {
		$default = Functions::get_base_location();

		return apply_filters( 'rtcl_countries_base_state', $default['state'] );
	}

	/**
	 * Get the base city for the store.
	 *
	 * @return string
	 *
	 * @version 3.1.1
	 */
	public function get_base_city() {
		$base_city = get_option( 'rtcl_store_city', '' );

		return apply_filters( 'rtcl_countries_base_city', $base_city );
	}

	/**
	 * Get the base postcode for the store.
	 *
	 * @return string
	 *
	 * @since 3.1.1
	 */
	public function get_base_postcode() {
		$base_postcode = get_option( 'rtcl_store_postcode', '' );

		return apply_filters( 'rtcl_countries_base_postcode', $base_postcode );
	}

	/**
	 * @return mixed|null
	 */
	public function get_all_countries() {
		return apply_filters( 'rtcl_countries_all_countries', $this->countries );
	}

	/**
	 * Get countries that the store sells to.
	 *
	 * @return array
	 */
	public function get_allowed_countries() {
		if ( 'all' === apply_filters( 'rtcl_allowed_countries', 'all' ) ) {
			return apply_filters( 'rtcl_countries_allowed_countries', $this->countries );
		}

		if ( 'all_except' === apply_filters( 'rtcl_allowed_countries', 'all' ) ) {
			$except_countries = apply_filters( 'rtcl_all_except_countries', [] );

			if ( ! $except_countries ) {
				return $this->countries;
			} else {
				$all_except_countries = $this->countries;
				foreach ( $except_countries as $country ) {
					unset( $all_except_countries[ $country ] );
				}

				return apply_filters( 'rtcl_countries_allowed_countries', $all_except_countries );
			}
		}

		$countries = [];

		$raw_countries = get_option( 'rtcl_specific_allowed_countries', [] );

		if ( $raw_countries ) {
			foreach ( $raw_countries as $country ) {
				$countries[ $country ] = $this->countries[ $country ];
			}
		}

		return apply_filters( 'rtcl_countries_allowed_countries', $countries );
	}

	/**
	 * Get countries that the store ships to.
	 *
	 * @return array
	 */
	public function get_shipping_countries() {
		if ( '' === get_option( 'rtcl_ship_to_countries' ) ) {
			return $this->get_allowed_countries();
		}

		if ( 'all' === get_option( 'rtcl_ship_to_countries' ) ) {
			return $this->countries;
		}

		$countries = [];

		$raw_countries = get_option( 'rtcl_specific_ship_to_countries' );

		if ( $raw_countries ) {
			foreach ( $raw_countries as $country ) {
				$countries[ $country ] = $this->countries[ $country ];
			}
		}

		return apply_filters( 'rtcl_countries_shipping_countries', $countries );
	}

	/**
	 * Get allowed country states.
	 *
	 * @return array
	 */
	public function get_allowed_country_states() {
		if ( get_option( 'rtcl_allowed_countries' ) !== 'specific' ) {
			return $this->states;
		}

		$states = [];

		$raw_countries = get_option( 'rtcl_specific_allowed_countries' );

		if ( $raw_countries ) {
			foreach ( $raw_countries as $country ) {
				if ( isset( $this->states[ $country ] ) ) {
					$states[ $country ] = $this->states[ $country ];
				}
			}
		}

		return apply_filters( 'rtcl_countries_allowed_country_states', $states );
	}

	/**
	 * Get shipping country states.
	 *
	 * @return array
	 */
	public function get_shipping_country_states() {
		if ( get_option( 'rtcl_ship_to_countries' ) === '' ) {
			return $this->get_allowed_country_states();
		}

		if ( get_option( 'rtcl_ship_to_countries' ) !== 'specific' ) {
			return $this->states;
		}

		$states = [];

		$raw_countries = get_option( 'rtcl_specific_ship_to_countries' );

		if ( $raw_countries ) {
			foreach ( $raw_countries as $country ) {
				if ( ! empty( $this->states[ $country ] ) ) {
					$states[ $country ] = $this->states[ $country ];
				}
			}
		}

		return apply_filters( 'rtcl_countries_shipping_country_states', $states );
	}

	/**
	 * Gets an array of countries in the EU.
	 *
	 * @param string $type Type of countries to retrieve. Blank for EU member countries. eu_vat for EU VAT countries.
	 *
	 * @return string[]
	 */
	public function get_european_union_countries( $type = '' ) {
		$countries = [
			'AT',
			'BE',
			'BG',
			'CY',
			'CZ',
			'DE',
			'DK',
			'EE',
			'ES',
			'FI',
			'FR',
			'GR',
			'HR',
			'HU',
			'IE',
			'IT',
			'LT',
			'LU',
			'LV',
			'MT',
			'NL',
			'PL',
			'PT',
			'RO',
			'SE',
			'SI',
			'SK'
		];

		if ( 'eu_vat' === $type ) {
			$countries[] = 'MC';
		}

		return apply_filters( 'rtcl_european_union_countries', $countries, $type );
	}

	/**
	 * Gets an array of Non-EU countries that use VAT as the Local name for their taxes based on this list - https://en.wikipedia.org/wiki/Value-added_tax#Non-European_Union_countries.
	 *
	 * @return string[]
	 *
	 * @since      3.9.0
	 * @deprecated 4.0.0
	 */
	public function countries_using_vat() {
		wc_deprecated_function( 'countries_using_vat', '4.0', 'WC_Countries::get_vat_countries' );
		$countries = [
			'AE',
			'AL',
			'AR',
			'AZ',
			'BB',
			'BH',
			'BO',
			'BS',
			'BY',
			'CL',
			'CO',
			'EC',
			'EG',
			'ET',
			'FJ',
			'GH',
			'GM',
			'GT',
			'IL',
			'IN',
			'IR',
			'KN',
			'KR',
			'KZ',
			'LK',
			'MD',
			'ME',
			'MK',
			'MN',
			'MU',
			'MX',
			'NA',
			'NG',
			'NP',
			'PS',
			'PY',
			'RS',
			'RU',
			'RW',
			'SA',
			'SV',
			'TH',
			'TR',
			'UA',
			'UY',
			'UZ',
			'VE',
			'VN',
			'ZA'
		];

		return apply_filters( 'rtcl_countries_using_vat', $countries );
	}

	/**
	 * Gets an array of countries using VAT.
	 *
	 * @return string[] of country codes.
	 *
	 * @since 4.0.0
	 */
	public function get_vat_countries() {
		$eu_countries  = $this->get_european_union_countries();
		$vat_countries = [
			'AE',
			'AL',
			'AR',
			'AZ',
			'BB',
			'BH',
			'BO',
			'BS',
			'BY',
			'CL',
			'CO',
			'EC',
			'EG',
			'ET',
			'FJ',
			'GB',
			'GH',
			'GM',
			'GT',
			'IL',
			'IM',
			'IN',
			'IR',
			'KN',
			'KR',
			'KZ',
			'LK',
			'MC',
			'MD',
			'ME',
			'MK',
			'MN',
			'MU',
			'MX',
			'NA',
			'NG',
			'NO',
			'NP',
			'PS',
			'PY',
			'RS',
			'RU',
			'RW',
			'SA',
			'SV',
			'TH',
			'TR',
			'UA',
			'UY',
			'UZ',
			'VE',
			'VN',
			'ZA'
		];

		return apply_filters( 'rtcl_vat_countries', array_merge( $eu_countries, $vat_countries ) );
	}

	/**
	 * Gets the correct string for shipping - either 'to the' or 'to'.
	 *
	 * @param string $country_code Country code.
	 *
	 * @return string
	 */
	public function shipping_to_prefix( $country_code = '' ) {
		$country_code = $country_code ? $country_code : WC()->customer->get_shipping_country();
		$countries    = [ 'AE', 'CZ', 'DO', 'GB', 'NL', 'PH', 'US', 'USAF' ];
		$return       = in_array( $country_code, $countries, true ) ? __( 'to the', 'classified-listing' ) : __( 'to', 'classified-listing' );

		return apply_filters( 'rtcl_countries_shipping_to_prefix', $return, $country_code );
	}

	/**
	 * Prefix certain countries with 'the'.
	 *
	 * @param string $country_code Country code.
	 *
	 * @return string
	 */
	public function estimated_for_prefix( $country_code = '' ) {
		$country_code = $country_code ? $country_code : $this->get_base_country();
		$countries    = [ 'AE', 'CZ', 'DO', 'GB', 'NL', 'PH', 'US', 'USAF' ];
		$return       = in_array( $country_code, $countries, true ) ? __( 'the', 'classified-listing' ) . ' ' : '';

		return apply_filters( 'rtcl_countries_estimated_for_prefix', $return, $country_code );
	}

	/**
	 * Correctly name tax in some countries VAT on the frontend.
	 *
	 * @return string
	 */
	public function tax_or_vat() {
		$return = in_array( $this->get_base_country(), $this->get_vat_countries(), true ) ? __( 'VAT', 'classified-listing' ) : __( 'Tax', 'classified-listing' );

		return apply_filters( 'rtcl_countries_tax_or_vat', $return );
	}

	/**
	 * Include the Inc Tax label.
	 *
	 * @return string
	 */
	public function inc_tax_or_vat() {
		$return = in_array( $this->get_base_country(), $this->get_vat_countries(), true ) ? __( '(incl. VAT)', 'classified-listing' ) : __( '(incl. tax)', 'classified-listing' );

		return apply_filters( 'rtcl_countries_inc_tax_or_vat', $return );
	}

	/**
	 * Include the Ex Tax label.
	 *
	 * @return string
	 */
	public function ex_tax_or_vat() {
		$return = in_array( $this->get_base_country(), $this->get_vat_countries(), true ) ? __( '(ex. VAT)', 'classified-listing' ) : __( '(ex. tax)', 'classified-listing' );

		return apply_filters( 'rtcl_countries_ex_tax_or_vat', $return );
	}

	/**
	 * Outputs the list of countries and states for use in dropdown boxes.
	 *
	 * @param string $selected_country Selected country.
	 * @param string $selected_state   Selected state.
	 * @param bool   $escape           If we should escape HTML.
	 */
	public function country_dropdown_options( $selected_country = '', $selected_state = '', $escape = false ) {
		if ( $this->countries ) {
			foreach ( $this->countries as $key => $value ) {
				$states = $this->get_states( $key );
				if ( $states ) {
					echo '<optgroup label="' . esc_attr( $value ) . '">';
					foreach ( $states as $state_key => $state_value ) {
						echo '<option value="' . esc_attr( $key ) . ':' . esc_attr( $state_key ) . '"';

						if ( $selected_country === $key && $selected_state === $state_key ) {
							echo ' selected="selected"';
						}

						echo '>' . esc_html( $value ) . ' &mdash; ' . ( $escape ? esc_html( $state_value ) : $state_value ) . '</option>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					echo '</optgroup>';
				} else {
					echo '<option';
					if ( $selected_country === $key && '*' === $selected_state ) {
						echo ' selected="selected"';
					}
					echo ' value="' . esc_attr( $key ) . '">' . ( $escape ? esc_html( $value ) : $value ) . '</option>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}

	/**
	 * Get country address formats.
	 *
	 * These define how addresses are formatted for display in various countries.
	 *
	 * @return array
	 */
	public function get_address_formats() {
		if ( empty( $this->address_formats ) ) {
			$this->address_formats = apply_filters(
				'rtcl_localisation_address_formats',
				[
					'default' => "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}",
					'AT'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'AU'      => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
					'BE'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'CA'      => "{company}\n{name}\n{address_1}\n{address_2}\n{city} {state_code} {postcode}\n{country}",
					'CH'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'CL'      => "{company}\n{name}\n{address_1}\n{address_2}\n{state}\n{postcode} {city}\n{country}",
					'CN'      => "{country} {postcode}\n{state}, {city}, {address_2}, {address_1}\n{company}\n{name}",
					'CZ'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'DE'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'DK'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'EE'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'ES'      => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city}\n{state}\n{country}",
					'FI'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'FR'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city_upper}\n{country}",
					'HK'      => "{company}\n{first_name} {last_name_upper}\n{address_1}\n{address_2}\n{city_upper}\n{state_upper}\n{country}",
					'HU'      => "{last_name} {first_name}\n{company}\n{city}\n{address_1}\n{address_2}\n{postcode}\n{country}",
					'IN'      => "{company}\n{name}\n{address_1}\n{address_2}\n{city} {postcode}\n{state}, {country}",
					'IS'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'IT'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode}\n{city}\n{state_upper}\n{country}",
					'JM'      => "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode_upper}\n{country}",
					'JP'      => "{postcode}\n{state} {city} {address_1}\n{address_2}\n{company}\n{last_name} {first_name}\n{country}",
					'LI'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'NL'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'NO'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'NZ'      => "{name}\n{company}\n{address_1}\n{address_2}\n{city} {postcode}\n{country}",
					'PL'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'PR'      => "{company}\n{name}\n{address_1} {address_2}\n{city} \n{country} {postcode}",
					'PT'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'RS'      => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'SE'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'SI'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'SK'      => "{company}\n{name}\n{address_1}\n{address_2}\n{postcode} {city}\n{country}",
					'TR'      => "{name}\n{company}\n{address_1}\n{address_2}\n{postcode} {city} {state}\n{country}",
					'TW'      => "{company}\n{last_name} {first_name}\n{address_1}\n{address_2}\n{state}, {city} {postcode}\n{country}",
					'UG'      => "{name}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}, {country}",
					'US'      => "{name}\n{company}\n{address_1}\n{address_2}\n{city}, {state_code} {postcode}\n{country}",
					'VN'      => "{name}\n{company}\n{address_1}\n{city}\n{country}",
				]
			);
		}

		return $this->address_formats;
	}

	/**
	 * Get country address format.
	 *
	 * @param array  $args      Arguments.
	 * @param string $separator How to separate address lines. @since 3.5.0.
	 *
	 * @return string
	 */
	public function get_formatted_address( $args = [], $separator = '<br/>' ) {
		$default_args = [
			'first_name' => '',
			'last_name'  => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
		];

		$args    = array_map( 'trim', wp_parse_args( $args, $default_args ) );
		$state   = $args['state'];
		$country = $args['country'];

		// Get all formats.
		$formats = $this->get_address_formats();

		// Get format for the address' country.
		$format = ( $country && isset( $formats[ $country ] ) ) ? $formats[ $country ] : $formats['default'];

		// Handle full country name.
		$full_country = ( isset( $this->countries[ $country ] ) ) ? $this->countries[ $country ] : $country;

		// Country is not needed if the same as base.
		if ( $country === $this->get_base_country() && ! apply_filters( 'rtcl_formatted_address_force_country_display', false ) ) {
			$format = str_replace( '{country}', '', $format );
		}

		// Handle full state name.
		$full_state = ( $country && $state && isset( $this->states[ $country ][ $state ] ) ) ? $this->states[ $country ][ $state ] : $state;

		// Substitute address parts into the string.
		$replace = array_map(
			'esc_html',
			apply_filters(
				'rtcl_formatted_address_replacements',
				[
					'{first_name}'       => $args['first_name'],
					'{last_name}'        => $args['last_name'],
					'{name}'             => sprintf(
					/* translators: 1: first name 2: last name */
						_x( '%1$s %2$s', 'full name', 'classified-listing' ),
						$args['first_name'],
						$args['last_name']
					),
					'{company}'          => $args['company'],
					'{address_1}'        => $args['address_1'],
					'{address_2}'        => $args['address_2'],
					'{city}'             => $args['city'],
					'{state}'            => $full_state,
					'{postcode}'         => $args['postcode'],
					'{country}'          => $full_country,
					'{first_name_upper}' => Functions::strtoupper( $args['first_name'] ),
					'{last_name_upper}'  => Functions::strtoupper( $args['last_name'] ),
					'{name_upper}'       => Functions::strtoupper(
						sprintf(
						/* translators: 1: first name 2: last name */
							_x( '%1$s %2$s', 'full name', 'classified-listing' ),
							$args['first_name'],
							$args['last_name']
						)
					),
					'{company_upper}'    => Functions::strtoupper( $args['company'] ),
					'{address_1_upper}'  => Functions::strtoupper( $args['address_1'] ),
					'{address_2_upper}'  => Functions::strtoupper( $args['address_2'] ),
					'{city_upper}'       => Functions::strtoupper( $args['city'] ),
					'{state_upper}'      => Functions::strtoupper( $full_state ),
					'{state_code}'       => Functions::strtoupper( $state ),
					'{postcode_upper}'   => Functions::strtoupper( $args['postcode'] ),
					'{country_upper}'    => Functions::strtoupper( $full_country ),
				],
				$args
			)
		);

		$formatted_address = str_replace( array_keys( $replace ), $replace, $format );

		// Clean up white space.
		$formatted_address = preg_replace( '/  +/', ' ', trim( $formatted_address ) );
		$formatted_address = preg_replace( '/\n\n+/', "\n", $formatted_address );

		// Break newlines apart and remove empty lines/trim commas and white space.
		$formatted_address = array_filter( array_map( [
			$this,
			'trim_formatted_address_line'
		], explode( "\n", $formatted_address ) ) );

		// Add html breaks.
		$formatted_address = implode( $separator, $formatted_address );

		// We're done!
		return $formatted_address;
	}

	/**
	 * Trim white space and commas off a line.
	 *
	 * @param string $line Line.
	 *
	 * @return string
	 */
	private function trim_formatted_address_line( $line ) {
		return trim( $line, ', ' );
	}

	/**
	 * Returns the fields we show by default. This can be filtered later on.
	 *
	 * @return array
	 */
	public function get_default_address_fields() {
		$address_2_label = __( 'Apartment, suite, unit, etc.', 'classified-listing' );

		// If necessary, append '(optional)' to the placeholder: we don't need to worry about the
		// label, though, as rtcl_form_field() takes care of that.
		if ( 'optional' === apply_filters( 'rtcl_checkout_address_2_field', 'optional' ) ) {
			$address_2_placeholder = __( 'Apartment, suite, unit, etc. (optional)', 'classified-listing' );
		} else {
			$address_2_placeholder = $address_2_label;
		}

		$fields = [
			'first_name' => [
				'label'        => __( 'First name', 'classified-listing' ),
				'required'     => true,
				'class'        => [ 'form-row-first half' ],
				'autocomplete' => 'given-name',
				'priority'     => 10,
			],
			'last_name'  => [
				'label'        => __( 'Last name', 'classified-listing' ),
				'required'     => true,
				'class'        => [ 'form-row-last half' ],
				'autocomplete' => 'family-name',
				'priority'     => 20,
			],
			'company'    => [
				'label'        => __( 'Company name', 'classified-listing' ),
				'class'        => [ 'form-row-wide' ],
				'autocomplete' => 'organization',
				'priority'     => 30,
				'required'     => 'required' === apply_filters( 'rtcl_checkout_company_field', 'optional' ),
			],
			'country'    => [
				'type'         => 'country',
				'label'        => __( 'Country / Region', 'classified-listing' ),
				'required'     => true,
				'class'        => [ 'form-row-wide', 'address-field', 'update_totals_on_change' ],
				'autocomplete' => 'country',
				'priority'     => 40,
			],
			'address_1'  => [
				'label'        => __( 'Street address', 'classified-listing' ),
				/* translators: use local order of street name and house number. */
				'placeholder'  => esc_attr__( 'House number and street name', 'classified-listing' ),
				'required'     => true,
				'class'        => [ 'form-row-wide', 'address-field' ],
				'autocomplete' => 'address-line1',
				'priority'     => 50,
			],
			'address_2'  => [
				'label'        => $address_2_label,
				'label_class'  => [ 'screen-reader-text' ],
				'placeholder'  => esc_attr( $address_2_placeholder ),
				'class'        => [ 'form-row-wide', 'address-field' ],
				'autocomplete' => 'address-line2',
				'priority'     => 60,
				'required'     => 'required' === apply_filters( 'rtcl_checkout_address_2_field', 'optional' ),
			],
			'city'       => [
				'label'        => __( 'Town / City', 'classified-listing' ),
				'required'     => true,
				'class'        => [ 'form-row-wide', 'address-field' ],
				'autocomplete' => 'address-level2',
				'priority'     => 70,
			],
			'state'      => [
				'type'         => 'state',
				'label'        => __( 'State / County', 'classified-listing' ),
				'required'     => true,
				'class'        => [ 'form-row-wide', 'address-field' ],
				'input_class'  => [ 'form-control' ],
				'validate'     => [ 'state' ],
				'autocomplete' => 'address-level1',
				'priority'     => 80,
			],
			'postcode'   => [
				'label'        => __( 'Postcode / ZIP', 'classified-listing' ),
				'required'     => true,
				'class'        => [ 'form-row-wide', 'address-field' ],
				'validate'     => [ 'postcode' ],
				'autocomplete' => 'postal-code',
				'priority'     => 90,
			],
		];

		if ( 'hidden' === apply_filters( 'rtcl_checkout_company_field', 'optional' ) ) {
			unset( $fields['company'] );
		}

		if ( 'hidden' === apply_filters( 'rtcl_checkout_address_2_field', 'optional' ) ) {
			unset( $fields['address_2'] );
		}

		$default_address_fields = apply_filters( 'rtcl_default_address_fields', $fields );
		// Sort each of the fields based on priority.
		uasort( $default_address_fields, [ Functions::class, 'checkout_fields_uasort_comparison' ] );

		return $default_address_fields;
	}

	/**
	 * Get JS selectors for fields which are shown/hidden depending on the locale.
	 *
	 * @return array
	 */
	public function get_country_locale_field_selectors() {
		$locale_fields = [
			'address_1' => '#billing_address_1_field, #shipping_address_1_field',
			'address_2' => '#billing_address_2_field, #shipping_address_2_field',
			'state'     => '#billing_state_field, #shipping_state_field, #calc_shipping_state_field',
			'postcode'  => '#billing_postcode_field, #shipping_postcode_field, #calc_shipping_postcode_field',
			'city'      => '#billing_city_field, #shipping_city_field, #calc_shipping_city_field',
		];

		return apply_filters( 'rtcl_country_locale_field_selectors', $locale_fields );
	}

	/**
	 * Get country locale settings.
	 *
	 * These locales override the default country selections after a country is chosen.
	 *
	 * @return array
	 */
	public function get_country_locale() {
		if ( empty( $this->locale ) ) {
			$this->locale = apply_filters(
				'rtcl_get_country_locale',
				[
					'AE' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'required' => false,
						],
					],
					'AF' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'AL' => [
						'state' => [
							'label' => __( 'County', 'classified-listing' ),
						],
					],
					'AO' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'AT' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'AU' => [
						'city'     => [
							'label' => __( 'Suburb', 'classified-listing' ),
						],
						'postcode' => [
							'label' => __( 'Postcode', 'classified-listing' ),
						],
						'state'    => [
							'label' => __( 'State', 'classified-listing' ),
						],
					],
					'AX' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'BA' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'label'    => __( 'Canton', 'classified-listing' ),
							'required' => false,
							'hidden'   => true,
						],
					],
					'BD' => [
						'postcode' => [
							'required' => false,
						],
						'state'    => [
							'label' => __( 'District', 'classified-listing' ),
						],
					],
					'BE' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'BH' => [
						'postcode' => [
							'required' => false,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'BI' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'BO' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'BS' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'CA' => [
						'postcode' => [
							'label' => __( 'Postal code', 'classified-listing' ),
						],
						'state'    => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'CH' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'label'    => __( 'Canton', 'classified-listing' ),
							'required' => false,
						],
					],
					'CL' => [
						'city'     => [
							'required' => true,
						],
						'postcode' => [
							'required' => false,
						],
						'state'    => [
							'label' => __( 'Region', 'classified-listing' ),
						],
					],
					'CN' => [
						'state' => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'CO' => [
						'postcode' => [
							'required' => false,
						],
						'state'    => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'CR' => [
						'state' => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'CW' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'required' => false,
						],
					],
					'CZ' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'DE' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'DK' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'DO' => [
						'state' => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'EC' => [
						'state' => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'EE' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'FI' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'FR' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'GH' => [
						'postcode' => [
							'required' => false,
						],
						'state'    => [
							'label' => __( 'Region', 'classified-listing' ),
						],
					],
					'GP' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'GF' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'GR' => [
						'state' => [
							'required' => false,
						],
					],
					'GT' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'HK' => [
						'postcode' => [
							'required' => false,
						],
						'city'     => [
							'label' => __( 'Town / District', 'classified-listing' ),
						],
						'state'    => [
							'label' => __( 'Region', 'classified-listing' ),
						],
					],
					'HN' => [
						'state' => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'HU' => [
						'last_name'  => [
							'class'    => [ 'form-row-first half' ],
							'priority' => 10,
						],
						'first_name' => [
							'class'    => [ 'form-row-last half' ],
							'priority' => 20,
						],
						'postcode'   => [
							'class'    => [ 'form-row-first half', 'address-field' ],
							'priority' => 65,
						],
						'city'       => [
							'class' => [ 'form-row-last half', 'address-field' ],
						],
						'address_1'  => [
							'priority' => 71,
						],
						'address_2'  => [
							'priority' => 72,
						],
						'state'      => [
							'label' => __( 'County', 'classified-listing' ),
						],
					],
					'ID' => [
						'state' => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'IE' => [
						'postcode' => [
							'required' => false,
							'label'    => __( 'Eircode', 'classified-listing' ),
						],
						'state'    => [
							'label' => __( 'County', 'classified-listing' ),
						],
					],
					'IS' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'IL' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'IM' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'IN' => [
						'postcode' => [
							'label' => __( 'PIN', 'classified-listing' ),
						],
						'state'    => [
							'label' => __( 'State', 'classified-listing' ),
						],
					],
					'IT' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => true,
							'label'    => __( 'Province', 'classified-listing' ),
						],
					],
					'JM' => [
						'city'     => [
							'label' => __( 'Town / City / Post Office', 'classified-listing' ),
						],
						'postcode' => [
							'required' => false,
							'label'    => __( 'Postal Code', 'classified-listing' ),
						],
						'state'    => [
							'required' => true,
							'label'    => __( 'Parish', 'classified-listing' ),
						],
					],
					'JP' => [
						'last_name'  => [
							'class'    => [ 'form-row-first half' ],
							'priority' => 10,
						],
						'first_name' => [
							'class'    => [ 'form-row-last half' ],
							'priority' => 20,
						],
						'postcode'   => [
							'class'    => [ 'form-row-first half', 'address-field' ],
							'priority' => 65,
						],
						'state'      => [
							'label'    => __( 'Prefecture', 'classified-listing' ),
							'class'    => [ 'form-row-last half', 'address-field' ],
							'priority' => 66,
						],
						'city'       => [
							'priority' => 67,
						],
						'address_1'  => [
							'priority' => 68,
						],
						'address_2'  => [
							'priority' => 69,
						],
					],
					'KR' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'KW' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'LV' => [
						'state' => [
							'label'    => __( 'Municipality', 'classified-listing' ),
							'required' => false,
						],
					],
					'LB' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'MQ' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'MT' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'MZ' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'NI' => [
						'state' => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'NL' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'NG' => [
						'postcode' => [
							'label'    => __( 'Postcode', 'classified-listing' ),
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'label' => __( 'State', 'classified-listing' ),
						],
					],
					'NZ' => [
						'postcode' => [
							'label' => __( 'Postcode', 'classified-listing' ),
						],
						'state'    => [
							'required' => false,
							'label'    => __( 'Region', 'classified-listing' ),
						],
					],
					'NO' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'NP' => [
						'state'    => [
							'label' => __( 'State / Zone', 'classified-listing' ),
						],
						'postcode' => [
							'required' => false,
						],
					],
					'PA' => [
						'state' => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'PL' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'PR' => [
						'city'  => [
							'label' => __( 'Municipality', 'classified-listing' ),
						],
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'PT' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'PY' => [
						'state' => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'RE' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'RO' => [
						'state' => [
							'label'    => __( 'County', 'classified-listing' ),
							'required' => true,
						],
					],
					'RS' => [
						'city'     => [
							'required' => true,
						],
						'postcode' => [
							'required' => true,
						],
						'state'    => [
							'label'    => __( 'District', 'classified-listing' ),
							'required' => false,
						],
					],
					'SG' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
						'city'  => [
							'required' => false,
						],
					],
					'SK' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'SI' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'SR' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'SV' => [
						'state' => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'ES' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'LI' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'label'    => __( 'Municipality', 'classified-listing' ),
							'required' => false,
						],
					],
					'LK' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'LU' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'MD' => [
						'state' => [
							'label' => __( 'Municipality / District', 'classified-listing' ),
						],
					],
					'SE' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'TR' => [
						'postcode' => [
							'priority' => 65,
						],
						'state'    => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'UG' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'city'     => [
							'label'    => __( 'Town / Village', 'classified-listing' ),
							'required' => true,
						],
						'state'    => [
							'label'    => __( 'District', 'classified-listing' ),
							'required' => true,
						],
					],
					'US' => [
						'postcode' => [
							'label' => __( 'ZIP Code', 'classified-listing' ),
						],
						'state'    => [
							'label' => __( 'State', 'classified-listing' ),
						],
					],
					'UY' => [
						'state' => [
							'label' => __( 'Department', 'classified-listing' ),
						],
					],
					'GB' => [
						'postcode' => [
							'label' => __( 'Postcode', 'classified-listing' ),
						],
						'state'    => [
							'label'    => __( 'County', 'classified-listing' ),
							'required' => false,
						],
					],
					'ST' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
						'state'    => [
							'label' => __( 'District', 'classified-listing' ),
						],
					],
					'VN' => [
						'state'     => [
							'required' => false,
							'hidden'   => true,
						],
						'postcode'  => [
							'priority' => 65,
							'required' => false,
							'hidden'   => false,
						],
						'address_2' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'WS' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'YT' => [
						'state' => [
							'required' => false,
							'hidden'   => true,
						],
					],
					'ZA' => [
						'state' => [
							'label' => __( 'Province', 'classified-listing' ),
						],
					],
					'ZW' => [
						'postcode' => [
							'required' => false,
							'hidden'   => true,
						],
					],
				]
			);

			$this->locale = array_intersect_key( $this->locale, array_merge( $this->get_allowed_countries(), $this->get_shipping_countries() ) );

			// Default Locale Can be filtered to override fields in get_address_fields(). Countries with no specific locale will use default.
			$this->locale['default'] = apply_filters( 'rtcl_get_country_locale_default', $this->get_default_address_fields() );

			// Filter default AND shop base locales to allow overides via a single function. These will be used when changing countries on the checkout.
			if ( ! isset( $this->locale[ $this->get_base_country() ] ) ) {
				$this->locale[ $this->get_base_country() ] = $this->locale['default'];
			}

			$this->locale['default']                   = apply_filters( 'rtcl_get_country_locale_base', $this->locale['default'] );
			$this->locale[ $this->get_base_country() ] = apply_filters( 'rtcl_get_country_locale_base', $this->locale[ $this->get_base_country() ] );
		}

		return $this->locale;
	}

	/**
	 * Apply locale and get address fields.
	 *
	 * @param mixed  $country Country.
	 * @param string $type    Address type, defaults to 'billing_'.
	 *
	 * @return array
	 */
	public function get_address_fields( $country = '', $type = 'billing_' ) {
		if ( ! $country ) {
			$country = $this->get_base_country();
		}

		$fields = $this->get_default_address_fields();
		$locale = $this->get_country_locale();

		if ( isset( $locale[ $country ] ) ) {
			$fields = Functions::array_overlay( $fields, $locale[ $country ] );
		}

		// Prepend field keys.
		$address_fields = [];

		foreach ( $fields as $key => $value ) {
			if ( 'state' === $key ) {
				$value['country_field'] = $type . 'country';
				$value['country']       = $country;
			}
			$address_fields[ $type . $key ] = $value;
		}

		// Add email and phone fields.
		if ( 'billing_' === $type ) {
			if ( apply_filters( 'rtcl_checkout_phone_field', true ) ) {
				$address_fields['billing_phone'] = [
					'label'        => __( 'Phone', 'classified-listing' ),
					'required'     => 'required' === get_option( 'rtcl_checkout_phone_field', 'required' ),
					'type'         => 'tel',
					'class'        => [ 'form-row-wide' ],
					'validate'     => [ 'phone' ],
					'autocomplete' => 'tel',
					'priority'     => 100,
				];
			}
			$address_fields['billing_email'] = [
				'label'        => __( 'Email address', 'classified-listing' ),
				'required'     => true,
				'type'         => 'email',
				'class'        => [ 'form-row-wide' ],
				'validate'     => [ 'email' ],
				'autocomplete' => apply_filters( 'rtcl_registration_generate_username', false, null ) ? 'email' : 'email username',
				'priority'     => 110,
			];
		}

		/**
		 * Important note on this filter: Changes to address fields can and will be overridden by
		 * the rtcl_default_address_fields. The locales/default locales apply on top based
		 * on country selection. If you want to change things like the required status of an
		 * address field, filter rtcl_default_address_fields instead.
		 */
		$address_fields = apply_filters( 'rtcl_' . $type . 'fields', $address_fields, $country );
		// Sort each of the fields based on priority.
		uasort( $address_fields, [ Functions::class, 'checkout_fields_uasort_comparison' ] );

		return $address_fields;
	}
}
