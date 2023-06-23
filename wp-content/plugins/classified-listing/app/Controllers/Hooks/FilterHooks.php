<?php

namespace Rtcl\Controllers\Hooks;

use Elementor\Controls_Manager;
use Rtcl\Helpers\Functions;
use WP_Error;
use WP_User;

class FilterHooks {
	public static function init() {
		self::applyHook();
		add_filter( 'embed_oembed_html', [ __CLASS__, 'wrap_embed_with_div' ], 10 );

		add_filter( 'rtcl_process_registration_errors', [ __CLASS__, 'password_validation' ], 10, 4 );
		add_filter( 'rtcl_validate_password_reset', [ __CLASS__, 'validate_password_reset_password' ], 10, 3 );
		add_filter( 'rtcl_my_account_validate_password_reset', [ __CLASS__, 'my_account_reset_password' ], 10, 2 );
		add_filter( 'rtcl_ajaxurl_current_lang', [ __CLASS__, 'wpml_ajaxurl_current_lang' ] );
		add_filter( 'rtcl_get_page_id', [ __CLASS__, 'wpml_get_page_object_id' ] );
		add_filter( 'rtcl_i18_get_post_id', [ __CLASS__, 'wpml_get_post_object_id' ] );
		add_filter( 'rtcl_transient_lang_prefix', [ __CLASS__, 'wpml_transient_lang_prefix' ] );
		add_filter( 'rtcl_single_listing_data_options', [ __CLASS__, 'add_map_options_data' ] );
		add_filter( 'wp_get_attachment_image_attributes', [ __CLASS__, 'add_title_attr_img' ], 10, 2 );
		add_filter( 'rtcl_registration_phone_validation', [ __CLASS__, 'required_phone_validation_at_registration' ] );
		if ( Functions::is_registration_page_separate() ) {
			add_filter( 'rtcl_advanced_settings_options', [ __CLASS__, 'add_registration_endpoint_options' ] );
			add_filter( 'rtcl_my_account_endpoint', [ __CLASS__, 'add_registration_end_points' ], 20 );
		}
	}

	public static function add_title_attr_img( $attr, $attachment = null ) {
		if ( ! empty( $attachment ) ) {
			$attr['title'] = esc_attr( wp_get_attachment_caption( $attachment->ID ) );
		}

		return $attr;
	}

	/**
	 * @param boolean $validation
	 * @param string  $source
	 *
	 * @return false
	 */
	public static function required_phone_validation_at_registration() {

		if ( Functions::get_option_item( 'rtcl_account_settings', 'disable_phone_at_registration', false, 'checkbox' ) ) {
			return false;
		}

		return Functions::get_option_item( 'rtcl_account_settings', 'required_phone_at_registration', false, 'checkbox' );
	}

	public static function remove_registration_name_validation() {
		return false;
	}

	public static function add_map_options_data( $options ) {
		global $listing, $rtcl_has_map_data;
		if ( $rtcl_has_map_data && $listing ) {
			$options = wp_parse_args( Functions::get_map_data( $listing, 'content' ), $options );
		}

		return $options;
	}

	public static function wpml_ajaxurl_current_lang( $current_lang ) {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return $current_lang;
		}

		return apply_filters( 'wpml_current_language', null );
	}

	public static function wpml_transient_lang_prefix( $prefix ) {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return $prefix;
		}
		global $sitepress;

		return '_' . $sitepress->get_current_language();
	}

	public static function wpml_get_page_object_id( $page_id ) {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return $page_id;
		}

		return apply_filters( 'wpml_object_id', absint( $page_id ), 'page', true );
	}

	public static function wpml_get_post_object_id( $post_id ) {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return $post_id;
		}

		return apply_filters( 'wpml_object_id', absint( $post_id ), 'post', true );
	}


	/**
	 * @param WP_Error $errors
	 * @param string   $password
	 *
	 * @return WP_Error
	 */
	public static function my_account_reset_password( $errors, $password ) {
		self::min_password_validation_message( $errors, $password );

		return $errors;
	}

	/**
	 * @param WP_Error $errors
	 * @param WP_User  $user
	 * @param array    $posted_fields
	 *
	 * @return WP_Error
	 */
	public static function validate_password_reset_password( $errors, $user, $posted_fields ) {
		$password = trim( $posted_fields['password_1'] );
		self::min_password_validation_message( $errors, $password );

		return $errors;
	}

	/**
	 * @param WP_Error $errors
	 * @param string   $email
	 * @param string   $username
	 * @param string   $password
	 */
	public static function password_validation( $errors, $email, $username, $password ) {
		self::min_password_validation_message( $errors, $password );

		return $errors;
	}

	/**
	 * @param WP_Error $errors
	 * @param string   $password
	 */
	private static function min_password_validation_message( &$errors, $password ) {
		$length = Functions::password_min_length();
		if ( $length && strlen( $password ) < $length ) {
			$errors->add( 'rtcl_min_pass_length', sprintf( esc_html__( "Your password must be at least %d characters long.", "classified-listing" ), Functions::password_min_length() ) );
		}
	}

	public static function wrap_embed_with_div( $html ) {
		return "<div class=\"responsive-container\">" . $html . "</div>";
	}


	private static function applyHook() {
		/**
		 * Short Description (excerpt).
		 */
		if ( function_exists( 'do_blocks' ) ) {
			add_filter( 'rtcl_short_description', 'do_blocks', 9 );
		}
		add_filter( 'rtcl_short_description', 'wptexturize' );
		add_filter( 'rtcl_short_description', 'convert_smilies' );
		add_filter( 'rtcl_short_description', 'convert_chars' );
		add_filter( 'rtcl_short_description', 'wpautop' );
		add_filter( 'rtcl_short_description', 'shortcode_unautop' );
		add_filter( 'rtcl_short_description', 'prepend_attachment' );
		add_filter( 'rtcl_short_description', 'do_shortcode', 11 ); // After wpautop().
		add_filter( 'rtcl_short_description', [ Functions::class, 'format_product_short_description' ], 9999999 );
		add_filter( 'rtcl_short_description', [ Functions::class, 'do_oembeds' ] );
		add_filter( 'rtcl_short_description', [ $GLOBALS['wp_embed'], 'run_shortcode' ], 8 ); // Before wpautop().
	}

	public static function add_registration_endpoint_options( $options ) {

		$position = array_search( 'myaccount_edit_account_endpoint', array_keys( $options ) );

		if ( $position > - 1 ) {
			$newOptions = [
				'myaccount_registration_endpoint' => [
					'title'   => esc_html__( 'Registration', 'classified-listing' ),
					'type'    => 'text',
					'default' => 'registration'
				]
			];
			Functions::array_insert( $options, $position, $newOptions );
		}

		return $options;
	}

	public static function add_registration_end_points( $endpoints ) {
		$endpoints['registration'] = Functions::get_option_item( 'rtcl_advanced_settings', 'myaccount_registration_endpoint', 'registration' );

		return $endpoints;
	}

}