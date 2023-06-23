<?php

namespace Rtcl\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Resources\Options;
use WP_Error;

class ActionHooks {
	public static function init() {
		add_action( 'rtcl_checkout_process_success', [ __CLASS__, 'checkout_process_mail' ] );

		add_action( 'ajax_query_attachments_args', [ __CLASS__, 'remove_ajax_query_attachments_args' ] );
		add_action( 'load-upload.php', [ __CLASS__, 'remove_attachments_load_media' ] );

		add_action( 'rtcl_set_local', [ __CLASS__, 'wpml_set_local_ajax' ] );

		add_action( 'rtcl_listing_query', [ __CLASS__, 'add_geo_query' ], 10, 2 );
		add_action( 'rtcl_listing_form_template_contact_end', [ __CLASS__, 'map_input_content' ] );
		add_action( 'rtcl_listing_seller_contact_form_validation', [ __CLASS__, 'seller_form_validation' ], 10, 2 );
		add_action( 'rtcl_listing_report_abuse_form_validation', [ __CLASS__, 'report_abuse_form_validation' ], 10, 2 );
	}

	/**
	 * @param WP_Error $error
	 * @param array $data
	 */
	public static function report_abuse_form_validation( $error, $data ) {
		if ( empty( $data['post_id'] ) || ! is_object( get_post( $data['post_id'] ) ) || empty( $data['message'] ) ) {
			$error->add( 'rtcl_field_required', esc_html__( 'Need to fill all the required field.', 'classified-listing' ) );
		}
	}

	/**
	 * @param WP_Error $error
	 * @param array $data
	 */
	public static function seller_form_validation( $error, $data ) {
		if ( empty( $data['post_id'] ) || ! is_object( get_post( $data['post_id'] ) ) || empty( $data['name'] ) || empty( $data['email'] ) || empty( $data['message'] ) ) {
			$error->add( 'rtcl_field_required', esc_html__( 'Need to fill all the required field.', 'classified-listing' ) );
		}
	}

	public static function add_geo_query( $query, $qObj ) {
		$distance       = ! empty( $_GET['distance'] ) ? absint( $_GET['distance'] ) : 0;
		$rtcl_geo_query = $query->get( 'rtcl_geo_query', [] );

		if ( $distance ) {
			$current_user_id = get_current_user_id();
			$lat             = ! empty( $_GET['center_lat'] ) ? trim( $_GET['center_lat'] ) : get_user_meta( $current_user_id, '_rtcl_latitude', true );
			$lan             = ! empty( $_GET['center_lng'] ) ? trim( $_GET['center_lng'] ) : get_user_meta( $current_user_id, '_rtcl_longitude', true );

			if ( $lat && $lan ) {
				$rs_data        = Options::radius_search_options();
				$rtcl_geo_query = [
					'lat_field' => 'latitude',
					'lng_field' => 'longitude',
					'latitude'  => $lat,
					'longitude' => $lan,
					'distance'  => $distance,
					'units'     => $rs_data["units"]
				];
			}
		}
		$geo_query = array_filter( apply_filters( 'rtcl_listing_query_geo_query', $rtcl_geo_query, $qObj ) );
		$query->set( 'rtcl_geo_query', $geo_query );
	}


	static function map_input_content( $post_id ) {
		if ( Functions::has_map() ) {
			$user_id     = get_current_user_id();
			$latitude    = $post_id ? get_post_meta( $post_id, 'latitude', true ) : get_user_meta( $user_id, '_rtcl_latitude', true );
			$longitude   = $post_id ? get_post_meta( $post_id, 'longitude', true ) : get_user_meta( $user_id, '_rtcl_longitude', true );
			$address     = $post_id ? get_post_meta( $post_id, 'address', true ) : get_user_meta( $user_id, '_rtcl_address', true );
			$geo_address = $post_id ? get_post_meta( $post_id, '_rtcl_geo_address', true ) : get_user_meta( $user_id, '_rtcl_geo_address', true );
			?>
            <div class="rtcl-map-wrap">
                <div class="rtcl-map" data-type="input">
                    <div class="marker" data-latitude="<?php echo esc_attr( $latitude ); ?>"
                         data-longitude="<?php echo esc_attr( $longitude ); ?>"><?php echo 'geo' === Functions::location_type() ? esc_attr( $geo_address ) : esc_html( $address ); ?></div>
                </div>
                <div class="rtcl-form-check">
                    <input class="rtcl-form-check-input" id="rtcl-hide-map"
                           type="checkbox" name="hide_map"
                           value="1" <?php checked( Functions::hide_map( $post_id ), 1 ); ?>>
                    <label class="rtcl-form-check-label"
                           for="rtcl-hide-map"><?php esc_html_e( "Don't show the Map", "classified-listing" ) ?></label>
                </div>
            </div>
            <!-- Map Hidden field-->
            <input type="hidden" name="latitude" value="<?php echo esc_attr( $latitude ); ?>" id="rtcl-latitude"/>
            <input type="hidden" name="longitude" value="<?php echo esc_attr( $longitude ); ?>" id="rtcl-longitude"/>
			<?php
		}
	}

	public static function wpml_set_local_ajax( $current_lang ) {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX || ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return;
		}
		global $sitepress;
		$current_lang = $current_lang ? $current_lang : ( isset( $_GET['lang'] ) ? $_GET['lang'] : '' );
		if ( $sitepress && method_exists( $sitepress, 'switch_lang' ) && $current_lang !== $sitepress->get_default_language() ) {
			$sitepress->switch_lang( $current_lang, true ); // Alternative do_action( 'wpml_switch_language', $_GET['lang'] );
		}
	}

	/**
	 * @param Payment $payment
	 */
	public static function checkout_process_mail( $payment ) {
		if ( $payment && $payment->exists() ) {
			if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_admin', 'order_created', 'multi_checkbox' ) ) {
				rtcl()->mailer()->emails['Order_Created_Email_To_Admin']->trigger( $payment->get_id(), $payment );
			}

			if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_users', 'order_created', 'multi_checkbox' ) ) {
				rtcl()->mailer()->emails['Order_Created_Email_To_Customer']->trigger( $payment->get_id(), $payment );
			}
		}
	}

	public static function remove_attachments_load_media() {
		add_action( 'pre_get_posts', [ __CLASS__, 'hide_media' ], 10, 1 );
	}

	/**
	 * @param $query \WP_Query
	 *
	 * @return mixed
	 */
	public static function remove_ajax_query_attachments_args( $query ) {
		if ( $query['post_type'] == 'attachment' ) {
			if ( ! empty( $excluded_ids = Functions::all_ids_for_remove_attachment() ) ) {
				$query['post_parent__not_in'] = $excluded_ids;
			}
		}

		return $query;
	}

	/**
	 * @param $query \WP_Query
	 *
	 * @return mixed
	 */
	public static function hide_media( $query ) {
		global $pagenow;

		// there is no need to check for update.php as we are already hooking to it, but anyway
		if ( 'upload.php' == $pagenow && is_admin() && $query->is_main_query() ) {
			if ( ! empty( $excluded_ids = Functions::all_ids_for_remove_attachment() ) ) {
				$query->set( 'post_parent__not_in', $excluded_ids );
			}
		}

		return $query;
	}
}
