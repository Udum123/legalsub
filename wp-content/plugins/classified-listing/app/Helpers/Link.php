<?php

namespace Rtcl\Helpers;

class Link {

	protected static $api_request = [
		'receive-payment'
	];

	public static function get_listing_page_id() {
		$page_settings = Functions::get_page_ids();
		$page_id       = isset( $page_settings['listings'] ) ? absint( $page_settings['listings'] ) : 0;

		return apply_filters( 'wpml_object_id', $page_id, 'post' );
	}

	public static function get_listing_page_slug( $page_id = 0 ) {
		$page_id = $page_id ? $page_id : self::get_listing_page_id();
		$page_id = apply_filters( 'wpml_object_id', $page_id, 'post' );
		$slug    = 'listings';
		if ( $page_id && $page = get_post( $page_id ) ) {
			$slug = $page->post_name;
		}

		return $slug;
	}

	public static function is_front_page( $page_id = 0 ) {
		$page_id = $page_id ? $page_id : self::get_listing_page_id();
		$page_id = apply_filters( 'wpml_object_id', $page_id, 'post' );

		return absint( $page_id ) === absint( get_option( 'page_on_front', 0 ) );
	}

	public static function logout_url( $redirect = '' ) {
		$logout_endpoint = Functions::get_option_item( 'rtcl_advanced_settings', "myaccount_logout_endpoint" );
		$redirect        = $redirect ? $redirect : self::get_page_permalink( 'myaccount' );

		if ( $logout_endpoint ) {
			return wp_nonce_url( self::get_endpoint_url( 'logout', '', $redirect ), 'logout' );
		} else {
			return wp_logout_url( $redirect );
		}
	}

	public static function get_page_permalink( $page ) {
		$page_id   = Functions::get_page_id( $page );
		$permalink = 0 < $page_id ? get_permalink( $page_id ) : get_home_url();

		return apply_filters( 'rtcl_get_' . $page . '_page_permalink', $permalink );
	}

	public static function lostpassword_url( $default_url = '' ) {
		// Avoid loading too early.
		if ( ! did_action( 'init' ) ) {
			return $default_url;
		}

		// Don't redirect to the rtcl endpoint on global network admin lost passwords.
		if ( is_multisite() && isset( $_GET['redirect_to'] ) && false !== strpos( wp_unslash( $_GET['redirect_to'] ), network_admin_url() ) ) { // WPCS: input var ok, sanitization ok.
			return $default_url;
		}

		$account_page_url       = self::get_page_permalink( 'myaccount' );
		$account_page_exists    = Functions::get_page_id( 'myaccount' ) > 0;
		$lost_password_endpoint = Functions::get_option_item( 'rtcl_advanced_settings', 'myaccount_lost_password_endpoint' );

		if ( $account_page_exists && ! empty( $lost_password_endpoint ) ) {
			return Link::get_endpoint_url( $lost_password_endpoint, '', $account_page_url );
		} else {
			return $default_url;
		}
	}

	/**
	 * @param $endpoint
	 *
	 * @return mixed
	 */
	public static function get_account_endpoint_url( $endpoint = false ) {
		if ( ! $endpoint || 'dashboard' === $endpoint ) {
			$url = self::get_page_permalink( 'myaccount' );
		} else if ( 'logout' === $endpoint ) {
			$url = self::logout_url();
		} else {
			$url = self::get_endpoint_url( $endpoint, '', self::get_page_permalink( 'myaccount' ) );
		}

		// Hooks BuddyPress Support .
		return apply_filters( 'rtcl_get_account_endpoint_url', $url, $endpoint );
	}

	/**
	 * @param      $endpoint
	 * @param null $value
	 *
	 * @return mixed
	 */
	public static function get_checkout_endpoint_url( $endpoint, $value = null ) {
		return self::get_endpoint_url( $endpoint, $value, self::get_page_permalink( 'checkout' ) );
	}

	/**
	 * @param        $endpoint
	 * @param string $value
	 * @param string $permalink
	 *
	 * @return mixed
	 */
	public static function get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
		if ( ! $permalink ) {
			$permalink = get_permalink();
		}

		// Map endpoint to options.
		$query_vars = rtcl()->query->get_query_vars();
		$endpoint   = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;
		if ( get_option( 'permalink_structure' ) ) {
			if ( strstr( $permalink, '?' ) ) {
				$query_string = '?' . wp_parse_url( $permalink, PHP_URL_QUERY );
				$permalink    = current( explode( '?', $permalink ) );
			} else {
				$query_string = '';
			}
			$url = trailingslashit( $permalink );
			if ( $endpoint ) {
				$url .= trailingslashit( $endpoint );
			}

			if ( $value ) {
				$url .= trailingslashit( $value );
			}

			$url .= $query_string;
		} else {
			$url = add_query_arg( $endpoint, $value, $permalink );
		}

		return apply_filters( 'rtcl_get_endpoint_url', $url, $endpoint, $value, $permalink );
	}

	protected static $default_request = 'receive-payment';

	static function get_listings_page_link( $loc_cat = false, $list = [] ) {
		$page_settings = Functions::get_page_ids();

		$link = '/';

		if ( $page_settings['listings'] > 0 ) {
			$link = get_permalink( $page_settings['listings'] );
		}
		if ( $loc_cat && ! empty( $page_settings['listings'] ) && $page_settings['listings'] > 0 ) {
			$location = $category = null;
			if ( $loc_slug = get_query_var( 'rtcl_location' ) ) {
				$location = get_term_by( 'slug', $loc_slug, rtcl()->location );
			}
			if ( $cat_slug = get_query_var( 'rtcl_category' ) ) {
				$category = get_term_by( 'slug', $cat_slug, rtcl()->category );
			}
			if ( '' != get_option( 'permalink_structure' ) ) {
				if ( $location && $category ) {
					$link = user_trailingslashit( trailingslashit( $link ) . $location->slug . '/' . $category->slug );
				} else if ( $location && ! $category ) {
					$link = user_trailingslashit( trailingslashit( $link ) . $location->slug );
				} else if ( ! $location && $category ) {
					$category_slug = self::get_category_slug();
					$link          = user_trailingslashit( trailingslashit( $link ) . $category_slug . "/" . $category->slug );
				}
			} else {
				if ( $location && $category ) {
					$link = add_query_arg( [
						'rtcl_location' => $location->slug,
						'rtcl_category' => $category->slug
					], $link );
				} else if ( $location && ! $category ) {
					$link = add_query_arg( [
						'rtcl_location' => $location->slug
					], $link );
				} else if ( ! $location && $category ) {
					$link = add_query_arg( [
						'rtcl_category' => $category->slug
					], $link );
				}

			}

			return $link;
		}

		return $link;
	}

	public static function get_location_page_link( $term, $loc_cat = false ) {
		return get_term_link( $term );
	}

	public static function get_category_page_link( $term, $loc_cat = false ) {
		return get_term_link( $term );
	}

	public static function get_listings_page_link_at_ajax_call( $location = null, $category = null ) {

		$page_id = self::get_listing_page_id();
		$link    = home_url();
		if ( $page_id ) {
			$params = [];
			$link   = get_permalink( $page_id );
			global $sitepress;
			if ( method_exists( $sitepress, 'get_current_language' ) && $sitepress->get_current_language() != $sitepress->get_default_language() ) {
				$link = apply_filters( 'wpml_permalink', $link, $sitepress->get_current_language() );
			}

			if ( self::is_front_page( $page_id ) ) {
				$params[] = self::get_listing_page_slug();
			}

			if ( '' != get_option( 'permalink_structure' ) ) {
				if ( $location && $category ) {
					$params[] = $location->slug;
					$params[] = $category->slug;
				} else if ( $location && ! $category ) {
					$params[] = $location->slug;
				} else if ( ! $location && $category ) {
					$category_slug            = self::get_category_slug();
					$params[ $category_slug ] = $category->slug;
				}
				$link = self::get_wpml_formatted_url( $link, $params );
			} else {
				if ( $location && $category ) {
					$link = add_query_arg( [
						'rtcl_location' => $location->slug,
						'rtcl_category' => $category->slug
					], $link );
				} else if ( $location && ! $category ) {
					$link = add_query_arg( [
						'rtcl_location' => $location->slug
					], $link );
				} else if ( ! $location && $category ) {
					$link = add_query_arg( [
						'rtcl_category' => $category->slug
					], $link );
				}
			}
		}

		return apply_filters( 'rtcl_get_listings_page_link_at_ajax_call', $link );
	}

	public static function get_current_url() {

		$current_url = ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" ) ? "https://" : "http://";
		$current_url .= $_SERVER["SERVER_NAME"];
		if ( $_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443" ) {
			$current_url .= ":" . $_SERVER["SERVER_PORT"];
		}
		$current_url .= $_SERVER["REQUEST_URI"];

		return $current_url;

	}

	public static function get_listing_form_page_link() {

		$page_settings = Functions::get_page_ids();

		$link = home_url();

		if ( ! empty( $page_settings['listing_form'] ) && $page_settings['listing_form'] > 0 ) {
			$link = get_permalink( $page_settings['listing_form'] );
		}

		return $link;
	}

	public static function get_listing_edit_page_link( $listing_id ) {

		$page_settings = Functions::get_page_ids();

		$link = home_url();

		if ( ! empty( $page_settings['listing_form'] ) && $page_settings['listing_form'] > 0 ) {
			$link = get_permalink( $page_settings['listing_form'] );

			if ( '' != get_option( 'permalink_structure' ) ) {
				$link = user_trailingslashit( trailingslashit( $link ) . 'edit/' . $listing_id );
			} else {
				$link = add_query_arg( [ 'rtcl_action' => 'edit', 'rtcl_listing' => $listing_id ], $link );
			}
		}

		return $link;

	}

	public static function get_user_page_link() {

	}


	public static function get_my_account_page_link( $action = null ) {
		return self::get_account_endpoint_url( $action );
	}

	public static function get_registration_page_link() {
		$endpoint = Functions::get_option_item( 'rtcl_advanced_settings', 'myaccount_registration_endpoint', 'registration' );

		return self::get_account_endpoint_url( $endpoint );
	}

	/**
	 * @param $payment_id
	 *
	 * @return mixed|void
	 */
	public static function get_payment_receipt_page_link( $payment_id ) {

		return self::get_checkout_endpoint_url( "payment-receipt", $payment_id );

	}

	/**
	 * @param $listing_id
	 *
	 * @return mixed|void
	 */
	static function get_listing_promote_page_link( $listing_id ) {

		return self::get_checkout_endpoint_url( "submission", $listing_id );

	}

	/**
	 * @param        $gateway
	 * @param string $request
	 * @param null $ssl
	 *
	 * @param string $version
	 *
	 * @return string
	 */
	static function payment_api_request_url( $gateway, $request = 'receive-payment', $ssl = null, $version = '' ) {

		$request = sanitize_title( strtolower( $request ) );
		if ( ! in_array( $request, self::$api_request ) ) {
			$request = self::$default_request;
		}
		$path = rtcl()->get_api_prefix( $version ) . "/" . $request . "/?gateway_id=" . $gateway->id;
		global $wp_rewrite;
		if ( ! is_object( $wp_rewrite ) ) {
			$wp_rewrite = new \wp_rewrite();
		}
		$url = get_rest_url( null, $path );

		return apply_filters( 'rtcl_api_request_url', $url, $gateway, $request, $ssl );
	}

	/**
	 * @param string $api_version
	 *
	 * @return mixed|void
	 */
	static function get_rest_api_url( $api_version = '' ) {
		$path = rtcl()->get_api_prefix( $api_version );
		global $wp_rewrite;
		if ( ! is_object( $wp_rewrite ) ) {
			$wp_rewrite = new \wp_rewrite();
		}
		$url = get_rest_url( null, $path, 'rest' );

		return apply_filters( 'rtcl_rest_api_url', $url );
	}


	/**
	 * @param       $url
	 * @param array $params
	 *
	 * @return array
	 */
	static function get_wpml_formatted_url( $url, $params ) {

		$original_url = $old_url = null;
		if ( ! empty( $params ) ) {
			$queries = self::get_url_query( $url );
			if ( ! empty( $queries ) ) {
				$url = $original_url = current( explode( '?', $url ) );
			}
			foreach ( $params as $key => $param ) {
				if ( ! is_numeric( $key ) ) {
					$url = user_trailingslashit( trailingslashit( $url ) . $key . '/' . $param );
				} else {
					$url = user_trailingslashit( trailingslashit( $url ) . $param );
				}
			}
			if ( ! empty( $queries ) ) {
				$url = add_query_arg( $queries, $url );
			}
		}

		return apply_filters( 'rtcl_get_wpml_formatted_url', $url, $old_url, $original_url, $params );
	}

	/**
	 * @param $url
	 *
	 * @return array
	 */
	static function get_url_query( $url ) {
		$params = [];

		$parse_url = parse_url( $url, PHP_URL_QUERY );
		if ( $parse_url ) {
			parse_str( $parse_url, $params );
		}

		return apply_filters( 'rtcl_get_url_query', $params );
	}


	static function get_regular_submission_end_point( $post_id ) {
		$end_point       = null;
		$pricing_options = Functions::get_regular_pricing_options();
		if ( Functions::get_option_item( 'rtcl_payment_settings', 'payment', false, 'checkbox' ) && ! empty( $pricing_options ) ) {
			$end_point = Link::get_checkout_endpoint_url( "submission", $post_id );
		}

		return apply_filters( 'rtcl_get_regular_submission_end_point', $end_point, $post_id );
	}

	/**
	 * @return string  Link category Slug
	 */
	public static function get_category_slug() {
		$category_slug = apply_filters( 'rtcl_link_category_slug', 'category' );

		return $category_slug ? $category_slug : 'category';
	}

}
