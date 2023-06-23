<?php

namespace Rtcl\Controllers\Hooks;

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Helpers\Text;
use Rtcl\Models\Listing;
use Rtcl\Models\Payment;
use Rtcl\Resources\Options;
use Rtcl\Shortcodes\Checkout;
use Rtcl\Shortcodes\MyAccount;
use Rtcl\Widgets\Filter;

class TemplateHooks {
	public static function init() {
		add_filter( 'body_class', [ __CLASS__, 'body_class' ] );
		add_filter( 'post_class', [ __CLASS__, 'listing_post_class' ], 20, 3 );

		/**
		 * Listing form hook
		 */
		add_action( "rtcl_listing_form", [ __CLASS__, 'listing_category' ], 5 );
		add_action( "rtcl_listing_form", [ __CLASS__, 'listing_information' ], 10 );
		add_action( "rtcl_listing_form", [ __CLASS__, 'listing_gallery' ], 20 );
		add_action( "rtcl_listing_form", [ __CLASS__, 'listing_contact' ], 30 );
		add_action( "rtcl_listing_form", [ __CLASS__, 'listing_recaptcha' ], 90 );
		add_action( "rtcl_listing_form", [ __CLASS__, 'listing_terms_conditions' ], 100 );
		add_action( "rtcl_listing_form_end", [ __CLASS__, 'add_listing_form_hidden_field' ], 10 );
		add_action( "rtcl_listing_form_end", [ __CLASS__, 'add_wpml_support' ], 20 );
		add_action( "rtcl_listing_form_end", [ __CLASS__, 'listing_form_submit_button' ], 50 );


		add_action( "rtcl_widget_filter_form", [ __CLASS__, 'widget_filter_form_ad_type_item' ], 10 );
		add_action( "rtcl_widget_filter_form", [ __CLASS__, 'widget_filter_form_category_item' ], 20 );
		add_action( "rtcl_widget_filter_form", [ __CLASS__, 'widget_filter_form_location_item' ], 30 );
		add_action( "rtcl_widget_filter_form", [ __CLASS__, 'widget_filter_form_radius_item' ], 40, 2 );
		add_action( "rtcl_widget_filter_form", [ __CLASS__, 'widget_filter_form_price_item' ], 90 );
		add_action( "rtcl_widget_filter_form_end", [ __CLASS__, 'add_apply_filter_button' ], 10 );
		add_action( "rtcl_widget_filter_form_end", [ __CLASS__, 'add_wpml_support' ], 90 );
		add_action( "rtcl_widget_search_inline_form", [ __CLASS__, 'add_wpml_support' ] );
		add_action( "rtcl_widget_search_vertical_form", [ __CLASS__, 'add_wpml_support' ] );

		/**
		 * Listing thumbnail hook
		 */
		add_action( 'rtcl_after_listing_thumbnail', [ __CLASS__, 'loop_item_meta_buttons' ], 10 );

		/**
		 * Content Wrappers.
		 *
		 * @see output_content_wrapper()
		 * @see breadcrumb()
		 * @see output_content_wrapper_end()
		 */
		add_action( 'rtcl_before_main_content', [ __CLASS__, 'astra_sidebar' ], 5 );
		add_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 6 );
		add_action( 'rtcl_before_main_content', [ __CLASS__, 'output_main_wrapper_start' ], 8 );
		add_action( 'rtcl_before_main_content', [ __CLASS__, 'output_content_wrapper' ], 10 );
		add_action( 'rtcl_after_main_content', [ __CLASS__, 'output_content_wrapper_end' ], 10 );
		/**
		 *
		 * Sidebar.
		 *
		 * @see get_sidebar()
		 */
		add_action( 'rtcl_sidebar', [ __CLASS__, 'get_sidebar' ], 10 );
		add_action( 'rtcl_sidebar', [ __CLASS__, 'output_main_wrapper_end' ], 15 );

		add_action( 'rtcl_archive_description', [ __CLASS__, 'taxonomy_archive_description' ], 10 );
		add_action( 'rtcl_archive_description', [ __CLASS__, 'listing_archive_description' ], 10 );

		add_action( 'rtcl_before_listing_loop', [ __CLASS__, 'listing_actions' ], 20 );
		add_action( 'rtcl_listing_loop_action', [ __CLASS__, 'result_count' ], 10 );
		add_action( 'rtcl_listing_loop_action', [ __CLASS__, 'catalog_ordering' ], 20 );
		add_action( 'rtcl_no_listings_found', [ __CLASS__, 'no_listings_found' ] );
		add_action( 'rtcl_shortcode_listings_loop_no_results', [ __CLASS__, 'no_listings_found' ] );

		add_action( 'rtcl_listing_loop_item_start', [ __CLASS__, 'listing_thumbnail' ] );


		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_wrapper_start' ], 10 );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_listing_title' ], 20 );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_badges' ], 30 );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_meta' ], 50 );
		//add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_meta_buttons' ], 60 );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_excerpt' ], 70 );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'listing_price' ], 80 );
		add_action( 'rtcl_listing_loop_item', [ __CLASS__, 'loop_item_wrapper_end' ], 100 );

		add_action( 'rtcl_after_listing_loop', [ __CLASS__, 'pagination' ], 10 );

		/**
		 * Notice
		 */
		add_action( 'rtcl_before_listing_loop', [ __CLASS__, 'output_all_notices' ], 10 );

		add_action( 'rtcl_account_navigation', [ __CLASS__, 'account_navigation' ] );
		add_action( 'rtcl_account_content', [ __CLASS__, 'account_content' ] );
		add_action( 'rtcl_account_listings_endpoint', [ __CLASS__, 'account_listings_endpoint' ] );
		add_action( 'rtcl_account_favourites_endpoint', [ __CLASS__, 'account_favourites_endpoint' ] );
		add_action( 'rtcl_account_edit-account_endpoint', [ __CLASS__, 'account_edit_account_endpoint' ] );
		add_action( 'rtcl_account_rtcl_edit_account_endpoint', [ __CLASS__, 'account_edit_account_endpoint' ] );

		add_action( 'rtcl_account_payments_endpoint', [ __CLASS__, 'account_payments_endpoint' ] );

		add_action( 'rtcl_checkout_content', [ __CLASS__, 'checkout_content' ] );
		add_action( 'rtcl_checkout_submission_endpoint', [ __CLASS__, 'checkout_submission_endpoint' ], 10, 2 );
		add_action( 'rtcl_checkout_payment-receipt_endpoint', [
			__CLASS__,
			'checkout_payment_receipt_endpoint'
		], 10, 2 );

		add_action( 'rtcl_account_dashboard', [ __CLASS__, 'user_information' ] );

		add_action( 'rtcl_single_listing_content', [ __CLASS__, 'add_single_listing_title' ], 5 );
		add_action( 'rtcl_single_listing_content', [ __CLASS__, 'add_single_listing_meta' ], 10 );
		add_action( 'rtcl_single_listing_content', [ __CLASS__, 'add_single_listing_gallery' ], 30 );
		add_action( 'rtcl_single_listing_content_end', [ __CLASS__, 'single_listing_map_content' ] );

		add_action( 'rtcl_single_listing_review', [ __CLASS__, 'add_single_listing_review' ], 10 );
		add_action( 'rtcl_single_listing_sidebar', [ __CLASS__, 'add_single_listing_sidebar' ], 10 );
		add_action( 'rtcl_single_listing_inner_sidebar', [
			__CLASS__,
			'add_single_listing_inner_sidebar_custom_field'
		], 10 );
		add_action( 'rtcl_single_listing_inner_sidebar', [ __CLASS__, 'add_single_listing_inner_sidebar_action' ], 20 );

		if ( ! Functions::get_option_item( 'rtcl_account_settings', 'disable_name_phone_registration', false, 'checkbox' ) ) {
			add_action( 'rtcl_register_form_start', [ __CLASS__, 'add_name_fields_at_registration_form' ], 10 );
		} else {
			add_filter( 'rtcl_registration_name_validation', function () {
				return false;
			} );
		}

		if ( ! Functions::get_option_item( 'rtcl_account_settings', 'disable_phone_at_registration', false, 'checkbox' ) ) {
			add_action( 'rtcl_register_form_start', [ __CLASS__, 'add_phone_at_registration_form' ], 20 );
		}


		/**
		 * Check out form
		 */
		add_action( 'rtcl_before_checkout_form', [ __CLASS__, 'add_checkout_form_instruction' ], 10 );
		add_action( 'rtcl_checkout_form_start', [ __CLASS__, 'add_checkout_form_promotion_options' ], 10, 2 );
		add_action( 'rtcl_checkout_form', [ __CLASS__, 'add_checkout_billing_details' ], 10, 2 );
		add_action( 'rtcl_checkout_form', [ __CLASS__, 'add_checkout_payment_method' ], 20, 2 );
		add_action( 'rtcl_checkout_form', [ __CLASS__, 'checkout_terms_and_conditions' ], 50 );
		add_action( 'rtcl_checkout_form_submit_button', [ __CLASS__, 'checkout_form_submit_button' ], 10 );
		add_action( 'rtcl_checkout_form_end', [ __CLASS__, 'add_checkout_hidden_field' ], 50 );
		add_action( 'rtcl_checkout_form_end', [ __CLASS__, 'add_submission_checkout_hidden_field' ], 60, 2 );

		add_action( 'rtcl_checkout_terms_and_conditions', [ __CLASS__, 'checkout_privacy_policy_text' ], 20 );
		add_action( 'rtcl_checkout_terms_and_conditions', [
			__CLASS__,
			'checkout_terms_and_conditions_page_content'
		], 30 );

		/**
		 * Misc Hooks
		 */
		add_action( 'rtcl_widget_filter_form_end', [ __CLASS__, 'add_hidden_field_filter_form' ], 50 );
		add_action( 'rtcl_login_form_end', [ __CLASS__, 'social_login_shortcode' ], 10 );
		add_action( 'rtcl_login_form_end', [ __CLASS__, 'logged_in_hidden_fields' ], 20 );
		add_action( 'rtcl_register_form', [ __CLASS__, 'registration_privacy_policy' ], 20 );
		add_action( 'rtcl_register_form', [ __CLASS__, 'registration_terms_and_conditions' ], 30 );
		add_action( 'rtcl_register_form_end', [ __CLASS__, 'registration_hidden_fields' ], 100 );

		add_action( 'rtcl_listing_badges', [ __CLASS__, 'listing_new_badge' ], 10 );
		add_action( 'rtcl_listing_badges', [ __CLASS__, 'listing_featured_badge' ], 20 );

		// Profile page
		add_action( 'rtcl_edit_account_form', [ __CLASS__, 'edit_account_form_social_profile_field' ], 70 );
		add_action( 'rtcl_edit_account_form', [ __CLASS__, 'edit_account_form_location_field' ], 50 );
		if ( Functions::has_map() ) {
			if ( 'geo' === Functions::location_type() ) {
				remove_action( 'rtcl_edit_account_form', [ __CLASS__, 'edit_account_form_location_field' ], 50 );
				add_action( 'rtcl_edit_account_form', [ __CLASS__, 'edit_account_form_geo_location' ], 50 );
			}
			add_action( 'rtcl_edit_account_form', [ __CLASS__, 'edit_account_map_field' ], 60 );
		}
		add_action( 'rtcl_edit_account_form_end', [ __CLASS__, 'edit_account_form_submit_button' ], 10 );
		add_action( 'rtcl_edit_account_form_end', [ __CLASS__, 'edit_account_form_hidden_field' ], 50 );

		add_action( 'rtcl_listing_meta_buttons', [ __CLASS__, 'add_favourite_button' ], 10 );


		// My listing actions
		add_action( 'rtcl_my_listing_actions', [ __CLASS__, 'my_listing_promotion_button' ] );
		add_action( 'rtcl_my_listing_actions', [ __CLASS__, 'my_listing_renew_button' ], 15 );
		add_action( 'rtcl_my_listing_actions', [ __CLASS__, 'my_listing_edit_button' ], 20 );
		add_action( 'rtcl_my_listing_actions', [ __CLASS__, 'my_listing_delete_button' ], 30 );

		// Listing seller contact
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'seller_location' ], 10 );
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'seller_phone_whatsapp_number' ], 20 );
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'seller_email' ], 30 );
		add_action( 'rtcl_listing_seller_information', [ __CLASS__, 'seller_website' ], 50 );

		// payment receipt
		add_action( 'rtcl_payment_receipt_top_offline', [ __CLASS__, 'offline_payment_instruction' ], 10, 2 );
		add_action( 'rtcl_payment_receipt', [ __CLASS__, 'payment_receipt_payment_info' ], 10, 2 );
		add_action( 'rtcl_payment_receipt', [ __CLASS__, 'payment_receipt_pricing_info' ], 20, 2 );
		add_action( 'rtcl_payment_receipt', [ __CLASS__, 'payment_receipt_actions' ], 50 );
	}

	/**
	 * @param int     $paymentId
	 * @param Payment $payment
	 */
	public static function payment_receipt_pricing_info( $paymentId, $payment ) {
		Functions::get_template( "checkout/pricing-info", compact( 'payment' ) );
	}

	/**
	 * @param int     $paymentId
	 * @param Payment $payment
	 */
	public static function payment_receipt_payment_info( $paymentId, $payment ) {
		Functions::get_template( "checkout/payment-info", compact( 'payment' ) );
	}

	public static function payment_receipt_actions() {
		?>
		<div class="action-btn text-center">
			<a href="<?php echo Link::get_account_endpoint_url( "listings" ); ?>"
			   class="btn btn-success"><?php esc_html_e( 'View all my listings', 'classified-listing' ); ?></a>
		</div>
		<?php
	}

	/**
	 * @param int     $paymentId
	 * @param Payment $payment
	 */
	public static function offline_payment_instruction( $paymentId, $payment ) {
		if ( $payment->get_status() === "rtcl-pending" ) {
			Functions::the_offline_payment_instructions();
		}
	}

	/**
	 * @param Listing $listing
	 */
	public static function seller_website( $listing ) {
		if ( is_a( $listing, Listing::class ) && $website = get_post_meta( $listing->get_id(), 'website', true ) ) {
			?>
			<div class='rtcl-website list-group-item'>
				<a class="rtcl-website-link btn btn-primary" href="<?php echo esc_url( $website ); ?>"
				   target="_blank"<?php echo Functions::is_external( $website ) ? ' rel="nofollow"' : ''; ?>><span
						class='rtcl-icon rtcl-icon-globe text-white'></span><?php esc_html_e( "Visit Website", "classified-listing" ) ?>
				</a>
			</div>
			<?php
		}
	}

	/**
	 * @param Listing $listing
	 */
	public static function seller_email( $listing ) {
		if ( is_a( $listing, Listing::class ) && Functions::get_option_item( 'rtcl_moderation_settings', 'has_contact_form', false, 'checkbox' ) && $email = get_post_meta( $listing->get_id(), 'email', true ) ) {
			?>
			<div class='rtcl-do-email list-group-item'>
				<div class='media'>
					<span class='rtcl-icon rtcl-icon-mail mr-2'></span>
					<div class='media-body'>
						<a class="rtcl-do-email-link" href='#'>
							<span><?php echo Text::get_single_listing_email_button_text(); ?></span>
						</a>
					</div>
				</div>
				<?php $listing->email_to_seller_form(); ?>
			</div>
			<?php
		}
	}

	/**
	 * @param Listing $listing
	 */
	public static function seller_phone_whatsapp_number( $listing ) {
		if ( is_a( $listing, Listing::class ) ) {
			$phone           = get_post_meta( $listing->get_id(), 'phone', true );
			$whatsapp_number = get_post_meta( $listing->get_id(), '_rtcl_whatsapp_number', true );
			if ( $phone || ( $whatsapp_number && ! Functions::is_field_disabled( 'whatsapp_number' ) ) ) {
				$mobileClass   = wp_is_mobile() ? " rtcl-mobile" : null;
				$phone_options = [];
				if ( $phone ) {
					$phone_options = [
						'safe_phone'   => mb_substr( $phone, 0, mb_strlen( $phone ) - 3 ) . apply_filters( 'rtcl_phone_number_placeholder', 'XXX' ),
						'phone_hidden' => mb_substr( $phone, - 3 )
					];
				}
				if ( $whatsapp_number && ! Functions::is_field_disabled( 'whatsapp_number' ) ) {
					$phone_options['safe_whatsapp_number'] = mb_substr( $whatsapp_number, 0, mb_strlen( $whatsapp_number ) - 3 ) . apply_filters( 'rtcl_phone_number_placeholder', 'XXX' );
					$phone_options['whatsapp_hidden']      = mb_substr( $whatsapp_number, - 3 );
				}
				$phone_options = apply_filters( 'rtcl_phone_number_options', $phone_options, [
					'phone'           => $phone,
					'whatsapp_number' => $whatsapp_number
				] )
				?>
				<div class='list-group-item reveal-phone<?php echo esc_attr( $mobileClass ); ?>'
					 data-options="<?php echo htmlspecialchars( wp_json_encode( $phone_options ) ); ?>">
					<div class='media'>
						<span class='rtcl-icon rtcl-icon-phone mr-2'></span>
						<div class='media-body'><span><?php esc_html_e(
									"Contact Number",
									"classified-listing"
								); ?></span>
							<div class='numbers'><?php
								if ( $phone ) {
									echo esc_html( $phone_options['safe_phone'] );
								} elseif ( $whatsapp_number ) {
									echo esc_html( $phone_options['safe_whatsapp_number'] );
								} ?></div>
							<small class='text-muted'><?php esc_html_e(
									"Click to reveal phone number",
									"classified-listing"
								) ?></small>
						</div>
					</div>
				</div>
				<?php
			}
		}
	}

	/**
	 * @param Listing $listing
	 */
	public static function seller_location( $listing ) {
		if ( is_a( $listing, Listing::class ) && $location = $listing->user_contact_location_at_single() ) {
			?>
			<div class='list-group-item'>
				<div class='media'>
					<span class='rtcl-icon rtcl-icon-location mr-2'></span>
					<div class='media-body'><span><?php esc_html_e( "Location", "classified-listing" ) ?></span>
						<div class='locations'><?php echo implode(
								'<span class="rtcl-delimiter">,</span> ',
								$location
							) ?></div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * @param Listing $listing
	 */
	public static function my_listing_promotion_button( $listing ) {
		if ( is_a( $listing, Listing::class ) && ! Functions::is_payment_disabled() ) {
			?>
			<a href="<?php echo esc_url( Link::get_checkout_endpoint_url( "submission", $listing->get_id() ) ); ?>"
			   class="btn btn-primary btn-sm rtcl-promote-btn">
				<?php esc_html_e( 'Promote', 'classified-listing' ) ?>
			</a>
			<?php
		}
	}


	/**
	 * @param Listing $listing
	 */
	public static function my_listing_renew_button( Listing $listing ) {
		if ( ! $listing->isExpired() ) {
			return;
		}

		if ( ! apply_filters( 'rtcl_enable_renew_button', Functions::is_enable_renew(), $listing ) ) {
			return;
		}

		?>
		<a href="#" data-id="<?php echo absint( $listing->get_id() ) ?>"
		   class="btn btn-primary btn-sm rtcl-renew-btn">
			<?php esc_html_e( 'Renew', 'classified-listing' ); ?>
		</a>
		<?php

	}

	/**
	 * @param Listing $listing
	 */
	public static function my_listing_edit_button( $listing ) {
		if ( is_a( $listing, Listing::class ) && Functions::current_user_can( 'edit_' . rtcl()->post_type, $listing->get_id() ) ) {
			?>
			<a href="<?php echo esc_url( Link::get_listing_edit_page_link( $listing->get_id() ) ); ?>"
			   class="btn btn-info btn-sm rtcl-edit-listing"
			   data-id="<?php echo esc_attr( $listing->get_id() ) ?>">
				<?php esc_html_e( 'Edit', 'classified-listing' ) ?>
			</a>
			<?php
		}
	}

	/**
	 * @param Listing $listing
	 */
	public static function my_listing_delete_button( $listing ) {
		if ( is_a( $listing, Listing::class ) && Functions::current_user_can( 'delete_' . rtcl()->post_type, $listing->get_id() ) ) {
			?>
			<a href="#" class="btn btn-danger btn-sm rtcl-delete-listing"
			   data-id="<?php echo esc_attr( $listing->get_id() ) ?>">
				<?php esc_html_e( 'Delete', 'classified-listing' ) ?>
			</a>
			<?php
		}
	}

	/**
	 * @param Listing $listing
	 */
	public static function add_favourite_button( $listing ) {
		if ( Functions::is_enable_favourite() ) { ?>
			<div class="rtcl-btn"
				 data-tooltip="<?php esc_attr_e( "Add to favourite", "classified-listing" ) ?>"
				 data-listing_id="<?php echo absint( $listing->get_id() ) ?>">
				<?php echo Functions::get_favourites_link( $listing->get_id() ) ?>
			</div>
		<?php }
	}

	/**
	 * @param Filter $object
	 */
	public static function widget_filter_form_category_item( $object ) {
		Functions::print_html( $object->get_category_filter(), true );
	}

	/**
	 * @param Filter $object
	 */
	public static function widget_filter_form_location_item( $object ) {
		Functions::print_html( $object->get_location_filter(), true );
	}

	/**
	 * @param Filter $object
	 */
	public static function widget_filter_form_radius_item( $object ) {
		Functions::print_html( $object->get_radius_search(), true );
	}

	/**
	 * @param Filter $object
	 */
	public static function widget_filter_form_price_item( $object ) {
		Functions::print_html( $object->get_price_filter(), true );
	}

	/**
	 * @param Filter $object
	 */
	public static function widget_filter_form_ad_type_item( $object ) {
		Functions::print_html( $object->get_ad_type_filter(), true );
	}

	public static function edit_account_form_submit_button() {
		?>
		<div class="form-group row">
			<div class="col-sm-offset-3 col-sm-9">
				<input type="submit" name="submit" class="btn btn-primary"
					   value="<?php esc_attr_e( 'Update Account', 'classified-listing' ); ?>"/>
			</div>
		</div>
		<?php
	}

	public static function edit_account_form_social_profile_field() {
		?>
		<div class="form-group rtcl-social-wrap-row row">
			<label for="rtcl-social" class="col-sm-3 control-label">
				<?php esc_html_e( 'Social Profile', 'classified-listing' ); ?>
			</label>
			<div class="col-sm-9">
				<?php
				$social_options = Options::get_social_profiles_list();
				$social_media   = get_current_user_id() ? Functions::get_user_social_profile( get_current_user_id() ) : [];
				foreach ( $social_options as $key => $social_option ) {
					echo sprintf(
						'<input type="url" name="social_media[%1$s]" id="rtcl-account-social-%1$s" value="%2$s" placeholder="%3$s" class="form-control"/>',
						$key,
						esc_url( isset( $social_media[ $key ] ) ? $social_media[ $key ] : '' ),
						$social_option
					);
				} ?>
			</div>
		</div>
		<?php
	}

	public static function edit_account_form_location_field() {
		$user_id        = get_current_user_id();
		$location_id    = $sub_location_id = 0;
		$user_locations = (array) get_user_meta( $user_id, '_rtcl_location', true );
		$zipcode        = get_user_meta( $user_id, '_rtcl_zipcode', true );
		$address        = get_user_meta( $user_id, '_rtcl_address', true );
		$state_text     = Text::location_level_first();
		$city_text      = Text::location_level_second();
		$town_text      = Text::location_level_third(); ?>
		<div class="form-group row">
			<label class="col-sm-3 control-label"><?php esc_html_e( 'Location', 'classified-listing' ); ?></label>
			<div class="col-sm-9">
				<div class="form-group" id="rtcl-location-row">
					<label for='rtcl-location'><?php echo esc_html( $state_text ); ?><span
							class="require-star">*</span></label>
					<select id="rtcl-location" name="location"
							class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
						<option value="">--<?php esc_html_e( 'Select state', 'classified-listing' ) ?>--</option>
						<?php
						$locations = Functions::get_one_level_locations();
						if ( ! empty( $locations ) ) {
							foreach ( $locations as $location ) {
								$slt = '';
								if ( in_array( $location->term_id, $user_locations ) ) {
									$location_id = $location->term_id;
									$slt         = " selected";
								}
								echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
							}
						} ?>
					</select>
				</div>
				<?php
				$sub_locations = [];
				if ( $location_id ) {
					$sub_locations = Functions::get_one_level_locations( $location_id );
				} ?>
				<div class="form-group<?php echo empty( $sub_locations ) ? ' rtcl-hide' : ''; ?>"
					 id="sub-location-row">
					<label for='rtcl-sub-location'><?php echo esc_html( $city_text ); ?><span
							class="require-star">*</span></label>
					<select id="rtcl-sub-location" name="sub_location"
							class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
						<option value="">--<?php esc_html_e( 'Select location', 'classified-listing' ) ?>--</option>
						<?php
						if ( ! empty( $sub_locations ) ) {
							foreach ( $sub_locations as $location ) {
								$slt = '';
								if ( in_array( $location->term_id, $user_locations ) ) {
									$sub_location_id = $location->term_id;
									$slt             = " selected";
								}
								echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
							}
						} ?>
					</select>
				</div>
				<?php
				$sub_sub_locations = [];
				if ( $sub_location_id ) {
					$sub_sub_locations = Functions::get_one_level_locations( $sub_location_id );
				} ?>
				<div class="form-group<?php echo empty( $sub_sub_locations ) ? ' rtcl-hide' : ''; ?>"
					 id="sub-sub-location-row">
					<label for='rtcl-sub-sub-location'><?php echo esc_html( $town_text ); ?>
						<span class="require-star">*</span></label>
					<select id="rtcl-sub-sub-location" name="sub_sub_location"
							class="rtcl-select2 rtcl-select form-control rtcl-map-field" required>
						<option value="">--<?php esc_html_e( 'Select location', 'classified-listing' ) ?>--</option>
						<?php
						if ( ! empty( $sub_sub_locations ) ) {
							foreach ( $sub_sub_locations as $location ) {
								$slt = '';
								if ( in_array( $location->term_id, $user_locations ) ) {
									$slt = " selected";
								}
								echo "<option value='{$location->term_id}'{$slt}>{$location->name}</option>";
							}
						} ?>
					</select>
				</div>
				<div class="form-group">
					<label for="rtcl-zipcode"><?php esc_html_e( "Zip Code", "classified-listing" ) ?></label>
					<input type="text" name="zipcode" value="<?php echo esc_attr( $zipcode ); ?>"
						   class="rtcl-map-field form-control" id="rtcl-zipcode"/>
				</div>
				<div class="form-group">
					<label for="rtcl-address"><?php esc_html_e( "Address", "classified-listing" ) ?></label>
					<textarea name="address" rows="2" class="rtcl-map-field form-control"
							  id="rtcl-address"><?php echo esc_textarea( $address ); ?></textarea>
				</div>
			</div>
		</div>
		<?php
	}


	public static function edit_account_form_geo_location() {
		$user_id     = get_current_user_id();
		$geo_address = get_user_meta( $user_id, '_rtcl_geo_address', true ); ?>
		<div class="form-group row">
			<label class="col-sm-3"
				   for="rtcl-geo-address"><?php esc_html_e( "Location", "classified-listing" ) ?></label>
			<div class="col-sm-9">
				<div class="rtcl-geo-address-field">
					<input type="text" name="rtcl_geo_address" autocomplete="off"
						   value="<?php echo esc_attr( $geo_address ) ?>"
						   id="rtcl-geo-address"
						   placeholder="<?php esc_attr_e( "Select a location", "classified-listing" ) ?>"
						   class="form-control rtcl-geo-address-input rtcl_geo_address_input"/>
					<i class="rtcl-get-location rtcl-icon rtcl-icon-target" id="rtcl-geo-loc-form"></i>
				</div>
			</div>
		</div>
		<?php
	}

	public static function edit_account_map_field() {
		$user_id   = get_current_user_id();
		$address   = get_user_meta( $user_id, '_rtcl_address', true );
		$latitude  = get_user_meta( $user_id, '_rtcl_latitude', true );
		$longitude = get_user_meta( $user_id, '_rtcl_longitude', true ); ?>
		<div class="form-group row">
			<label for="rtcl-map"
				   class="col-sm-3 control-label"><?php esc_html_e( 'Map', 'classified-listing' ); ?></label>
			<div class="col-sm-9">
				<div class="rtcl-map-wrap">
					<div class="rtcl-map" data-type="input">
						<div class="marker" data-latitude="<?php echo esc_attr( $latitude ); ?>"
							 data-longitude="<?php echo esc_attr( $longitude ); ?>"
							 data-address="<?php echo esc_attr( $address ); ?>"><?php echo esc_html( $address ); ?></div>
					</div>
				</div>
			</div>
		</div>
		<!-- Map Hidden field-->
		<input type="hidden" name="latitude" value="<?php echo esc_attr( $latitude ); ?>" id="rtcl-latitude"/>
		<input type="hidden" name="longitude" value="<?php echo esc_attr( $longitude ); ?>" id="rtcl-longitude"/>
		<?php
	}

	/**
	 * @param Listing $listing
	 */
	public static function listing_featured_badge( $listing ) {
		if ( ! $listing->is_featured() ) {
			return;
		}
		$display_option    = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';
		$can_show          = apply_filters( 'rtcl_listing_can_show_featured_badge', true, $listing );
		$can_show_settings = Functions::get_option_item( 'rtcl_moderation_settings', $display_option, 'featured', 'multi_checkbox' );

		$can_show_settings = apply_filters( 'rtcl_listing_can_show_featured_badge_settings', $can_show_settings );

		if ( ! $can_show || ! $can_show_settings ) {
			return;
		}
		$label = Functions::get_option_item( 'rtcl_moderation_settings', 'listing_featured_label' );
		$label = $label ?: esc_html__( "Featured", "classified-listing" );
		echo '<span class="badge rtcl-badge-featured">' . esc_html( $label ) . '</span>';
	}

	/**
	 * @param Listing $listing
	 */
	public static function listing_new_badge( $listing ) {
		$can_show = apply_filters( 'rtcl_listing_can_show_new_badge', true, $listing );
		if ( ! $can_show || ! $listing->is_new() ) {
			return;
		}
		$display_option    = is_singular( rtcl()->post_type ) ? 'display_options_detail' : 'display_options';
		$can_show_settings = Functions::get_option_item( 'rtcl_moderation_settings', $display_option, 'new', 'multi_checkbox' );
		$can_show_settings = apply_filters( 'rtcl_listing_can_show_new_badge_settings', $can_show_settings );
		if ( ! $can_show_settings ) {
			return;
		}

		$label = Functions::get_option_item( 'rtcl_moderation_settings', 'new_listing_label' );
		$label = $label ?: esc_html__( "New", "classified-listing" );
		echo '<span class="badge rtcl-badge-new">' . esc_html( $label ) . '</span>';
	}

	/**
	 * @param int $post_id
	 *
	 * @throws \Exception
	 */
	public static function listing_category( $post_id ) {
		if ( $post_id ) {
			$category_id   = wp_get_object_terms( $post_id, rtcl()->category, [ 'fields' => 'ids' ] );
			$category_id   = ( is_array( $category_id ) && ! empty( $category_id ) ) ? end( $category_id ) : 0;
			$selected_type = get_post_meta( $post_id, 'ad_type', true );
		} else {
			$category_id   = isset( $_GET['category'] ) ? absint( $_GET['category'] ) : 0;
			$selected_type = ( isset( $_GET['type'] ) && in_array( $_GET['type'], array_keys( Functions::get_listing_types() ) ) ) ? $_GET['type'] : '';
		}
		Functions::get_template( "listing-form/category-section", compact( 'post_id', 'category_id', 'selected_type' ) );
	}

	/**
	 * @param int $post_id
	 *
	 * @throws \Exception
	 */
	public static function listing_information( $post_id ) {
		if ( $post_id ) {
			$category_id   = wp_get_object_terms( $post_id, rtcl()->category, [ 'fields' => 'ids' ] );
			$category_id   = ( is_array( $category_id ) && ! empty( $category_id ) ) ? end( $category_id ) : 0;
			$selected_type = get_post_meta( $post_id, 'ad_type', true );
		} else {
			$category_id   = isset( $_GET['category'] ) ? absint( $_GET['category'] ) : 0;
			$selected_type = ( isset( $_GET['type'] ) && in_array( $_GET['type'], array_keys( Functions::get_listing_types() ) ) ) ? $_GET['type'] : '';
		}
		$general_settings    = Functions::get_option( 'rtcl_general_settings' );
		$moderation_settings = Functions::get_option( 'rtcl_moderation_settings' );
		$editor              = ! empty( $general_settings['text_editor'] ) ? $general_settings['text_editor'] : 'wp_editor';
		$price               = $post_content = $listing_pricing = $price_type = $title = '';
		$listing             = null;
		if ( $post_id > 0 ) {
			$listing         = new Listing( $post_id );
			$category_id     = wp_get_object_terms( $post_id, rtcl()->category, [ 'fields' => 'ids' ] );
			$category_id     = ( is_array( $category_id ) && ! empty( $category_id ) ) ? end( $category_id ) : 0;
			$price_type      = get_post_meta( $post_id, 'price_type', true );
			$listing_pricing = get_post_meta( $post_id, '_rtcl_listing_pricing', true );
			$price           = get_post_meta( $post_id, 'price', true );
			global $post;
			$post = get_post( $post_id );
			setup_postdata( $post );
			$title        = get_the_title();
			$post_content = get_the_content();
			wp_reset_postdata();
		}
		Functions::get_template( "listing-form/information", [
			'listing'           => $listing,
			'post_id'           => $post_id,
			'title'             => $title,
			'post_content'      => $post_content,
			'price'             => $price,
			'listing_pricing'   => in_array( $listing_pricing, array_keys( Options::get_listing_pricing_types() ) ) ? $listing_pricing : 'price',
			'price_type'        => $price_type,
			'editor'            => $editor,
			'category_id'       => $category_id,
			'selected_type'     => $selected_type,
			'title_limit'       => Functions::get_title_character_limit(),
			'description_limit' => Functions::get_description_character_limit(),
			'parent_cat_id'     => 0,
			'child_cat_id'      => 0,
			'hidden_fields'     => ( ! empty( $moderation_settings['hide_form_fields'] ) ) ? $moderation_settings['hide_form_fields'] : []
		] );
	}

	/**
	 * @param $post_id
	 */
	public static function listing_gallery( $post_id ) {
		// Images
		if ( ! Functions::is_gallery_disabled() ) {
			Functions::get_template( "listing-form/gallery", compact( 'post_id' ) );


			// Videos
			if ( ! Functions::is_video_urls_disabled() ) {
				$video_urls = get_post_meta( $post_id, '_rtcl_video_urls', true );
				$video_urls = ! empty( $video_urls ) && is_array( $video_urls ) ? $video_urls : [];
				Functions::get_template( "listing-form/video-urls", compact( 'post_id', 'video_urls' ) );
			}
		}
	}

	public static function listing_recaptcha( $post_id ) {
		$settings = Functions::get_option_item( 'rtcl_misc_settings', 'recaptcha_forms', [] );
		if ( ! empty( $settings ) && is_array( $settings ) && in_array( 'listing', $settings ) ) {
			Functions::get_template( "listing-form/recaptcha", compact( 'post_id' ) );
		}
	}

	public static function listing_terms_conditions( $post_id ) {
		$agreed = get_post_meta( $post_id, 'rtcl_agree', true );
		Functions::get_template( "listing-form/terms-conditions", compact( 'post_id', 'agreed' ) );
	}

	public static function listing_contact( $post_id ) {
		$location_id        = $sub_location_id = $sub_sub_location_id = 0;
		$user_id            = get_current_user_id();
		$user               = get_userdata( $user_id );
		$email              = $user ? $user->user_email : '';
		$phone              = get_user_meta( $user_id, '_rtcl_phone', true );
		$whatsapp_number    = get_user_meta( $user_id, '_rtcl_whatsapp_number', true );
		$website            = get_user_meta( $user_id, '_rtcl_website', true );
		$selected_locations = (array) get_user_meta( $user_id, '_rtcl_location', true );
		$zipcode            = get_user_meta( $user_id, '_rtcl_zipcode', true );
		$geo_address        = get_user_meta( $user_id, '_rtcl_geo_address', true );
		$address            = get_user_meta( $user_id, '_rtcl_address', true );
		$latitude           = get_user_meta( $user_id, '_rtcl_latitude', true );
		$longitude          = get_user_meta( $user_id, '_rtcl_longitude', true );

		if ( $post_id ) {
			$selected_locations = 'local' === Functions::location_type() ? wp_get_object_terms( $post_id, rtcl()->location, [ 'fields' => 'ids' ] ) : [];
			$latitude           = get_post_meta( $post_id, 'latitude', true );
			$longitude          = get_post_meta( $post_id, 'longitude', true );
			$zipcode            = get_post_meta( $post_id, 'zipcode', true );
			$address            = get_post_meta( $post_id, 'address', true );
			$geo_address        = get_post_meta( $post_id, '_rtcl_geo_address', true );
			$phone              = get_post_meta( $post_id, 'phone', true );
			$whatsapp_number    = get_post_meta( $post_id, '_rtcl_whatsapp_number', true );
			$email              = get_post_meta( $post_id, 'email', true );
			$website            = get_post_meta( $post_id, 'website', true );
		}
		$moderation_settings = Functions::get_option( 'rtcl_moderation_settings' );
		$data                = [
			'post_id'                    => $post_id,
			'state_text'                 => Text::location_level_first(),
			'city_text'                  => Text::location_level_second(),
			'town_text'                  => Text::location_level_third(),
			'selected_locations'         => $selected_locations,
			'latitude'                   => $latitude,
			'longitude'                  => $longitude,
			'zipcode'                    => $zipcode,
			'address'                    => $address,
			'geo_address'                => $geo_address,
			'phone'                      => $phone,
			'whatsapp_number'            => $whatsapp_number,
			'email'                      => $email,
			'website'                    => $website,
			'location_id'                => $location_id,
			'sub_location_id'            => $sub_location_id,
			'sub_sub_location_id'        => $sub_sub_location_id,
			'hidden_fields'              => ( ! empty( $moderation_settings['hide_form_fields'] ) ) ? $moderation_settings['hide_form_fields'] : [],
			'enable_post_for_unregister' => ! is_user_logged_in() && Functions::is_enable_post_for_unregister()
		];
		Functions::get_template( "listing-form/contact", apply_filters( 'rtcl_listing_form_contact_tpl_attributes', $data, $post_id ) );
	}

	public static function add_apply_filter_button() {
		?>
		<div class="ui-buttons has-expanded">
			<button class="btn btn-primary rtcl-filter-btn">
				<?php echo esc_html__( "Apply filters", 'classified-listing' ); ?>
			</button>
			<?php if ( isset( $_GET['filters'] ) ): ?>
				<a class="btn btn-primary rtcl-filter-clear-btn"
				   href="<?php echo esc_url( Link::get_listings_page_link() ) ?>">
					<?php echo esc_html__( "Clear filters", 'classified-listing' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function listing_form_submit_button( $post_id ) {
		?>
		<button type="submit" class="btn btn-primary rtcl-submit-btn">
			<?php
			if ( $post_id > 0 ) {
				echo apply_filters( 'rtcl_listing_form_update_btn_text', esc_html__( 'Update', 'classified-listing' ) );
			} else {
				echo apply_filters( 'rtcl_listing_form_submit_btn_text', esc_html__( 'Submit', 'classified-listing' ) );
			} ?>
		</button>
		<?php
	}

	public static function add_wpml_support( $post_id ) {
		if ( function_exists( 'icl_object_id' ) && isset( $_REQUEST['lang'] ) ) {
			echo sprintf( '<input type="hidden" name="lang" value="%s" />', esc_attr( $_REQUEST['lang'] ) );
		}
	}

	public static function add_listing_form_hidden_field( $post_id ) {
		echo sprintf( '<input type="hidden" name="_post_id" id="_post_id" value="%d"/>', esc_attr( $post_id ) );
		wp_nonce_field( rtcl()->nonceText, rtcl()->nonceId );
		if ( ! $post_id ) {
			$category_id   = isset( $_GET['category'] ) ? absint( $_GET['category'] ) : 0;
			$selected_type = ( isset( $_GET['type'] ) && in_array( $_GET['type'], array_keys( Functions::get_listing_types() ) ) ) ? $_GET['type'] : '';
			echo sprintf( '<input type="hidden" name="_category_id" id="category-id" value="%d"/>', esc_attr( $category_id ) );
			echo sprintf( '<input type="hidden" name="_ad_type" id="ad-type" value="%s"/>', esc_attr( $selected_type ) );
		}
	}

	public static function add_name_fields_at_registration_form() {
		?>
		<div class="form-group row">
			<div class="col-md-6">
				<label for="rtcl-reg-first-name" class="control-label">
					<?php esc_html_e( 'First Name', 'classified-listing' ); ?>
					<strong class="rtcl-required">*</strong>
				</label>
				<input type="text" name="first_name" id="rtcl-reg-first-name"
					   value="<?php if ( ! empty( $_POST['first_name'] ) ) {
						   echo esc_attr( $_POST['first_name'] );
					   } ?>"
					   class="form-control" required/>
			</div>
			<div class="col-md-6">
				<label for="rtcl-reg-last-name" class="control-label">
					<?php esc_html_e( 'Last Name', 'classified-listing' ); ?>
					<strong class="rtcl-required">*</strong>
				</label>
				<input type="text" name="last_name"
					   value="<?php if ( ! empty( $_POST['last_name'] ) ) {
						   echo esc_attr( $_POST['last_name'] );
					   } ?>"
					   id="rtcl-reg-last-name" class="form-control" required/>
			</div>
		</div>
		<?php
	}

	public static function add_phone_at_registration_form() {
		$is_required = (boolean) apply_filters( 'rtcl_registration_phone_validation', false, '' );
		?>
		<div class="form-group phone-row">
			<?php do_action( 'rtcl_register_form_phone_start' ); ?>
			<label for="rtcl-reg-phone" class="control-label phone-label">
				<?php esc_html_e( 'Phone Number', 'classified-listing' ); ?>
				<?php if ( $is_required ): ?>
					<strong class="rtcl-required">*</strong>
				<?php endif; ?>
			</label>
			<input type="text" name="phone"
				   value="<?php if ( ! empty( $_POST['phone'] ) ) {
					   echo esc_attr( $_POST['phone'] );
				   } ?>"
				   id="rtcl-reg-phone" class="form-control"<?php echo $is_required ? ' required' : '' ?>/>
			<?php do_action( 'rtcl_register_form_phone_end' ); ?>
		</div>
		<?php
	}

	public static function add_single_listing_review() {
		if ( current_theme_supports( 'rtcl' ) && ( comments_open() || get_comments_number() ) ) {
			comments_template();
		}
	}

	public static function add_single_listing_sidebar() {
		Functions::get_template( "listing/listing-sidebar" );
	}

	public static function add_single_listing_inner_sidebar_custom_field() {
		/** @var Listing $listing */
		global $listing;
		$listing->the_custom_fields();
	}

	public static function add_single_listing_inner_sidebar_action() {
		/** @var Listing $listing */
		global $listing;
		$listing->the_actions();
	}

	public static function add_single_listing_gallery() {
		/** @var Listing $listing */
		global $listing;
		$listing->the_gallery();
	}

	/**
	 * @param $listing Listing
	 */
	public static function single_listing_map_content( $listing ) {
		if ( is_a( $listing, Listing::class ) ) {
			$latitude  = get_post_meta( $listing->get_id(), 'latitude', true );
			$longitude = get_post_meta( $listing->get_id(), 'longitude', true );
			$address   = null;
			if ( 'geo' === Functions::location_type() ) {
				$address = esc_html__( strip_tags( get_post_meta( $listing->get_id(), '_rtcl_geo_address', true ) ) );
			}

			if ( ! $address ) {
				$locations    = [];
				$rawLocations = $listing->get_locations();
				if ( count( $rawLocations ) ) {
					foreach ( $rawLocations as $location ) {
						$locations[] = $location->name;
					}
				}
				if ( $zipcode = get_post_meta( $listing->get_id(), 'zipcode', true ) ) {
					$locations[] = esc_html( $zipcode );
				}
				if ( $address = get_post_meta( $listing->get_id(), 'address', true ) ) {
					$locations[] = esc_html( $address );
				}
				$locations = array_reverse( $locations );
				$address   = ! empty( $locations ) ? implode( ',', $locations ) : null;
			}
			$map_options  = [];
			$map_settings = [
				'has_map'     => Functions::has_map() && ! Functions::hide_map( $listing->get_id() ),
				'latitude'    => $latitude,
				'longitude'   => $longitude,
				'address'     => $address,
				'map_options' => $map_options
			];
			$map_settings = apply_filters( 'rtcl_single_listing_map_settings', $map_settings ); // Filter Added By Rashid
			Functions::get_template( "listing/map", $map_settings );
		}
	}

	public static function add_single_listing_meta() {
		/** @var Listing $listing */
		global $listing; ?>
		<!-- Meta data -->
		<div class="rtcl-listing-meta">
			<?php $listing->the_badges(); ?>
			<?php $listing->the_meta(); ?>
		</div>
		<?php
	}

	public static function add_single_listing_title() {
		/** @var Listing $listing */
		global $listing; ?>
		<div class="rtcl-listing-title"><h2 class="entry-title"><?php $listing->the_title(); ?></h2></div>
		<?php
	}


	public static function listing_actions() {
		Functions::get_template( 'listing/loop/actions' );
	}

	public static function pagination() {
		Functions::pagination();
	}


	/**
	 * Output the Listing sorting options.
	 */
	public static function catalog_ordering() {
		if ( ! Functions::get_loop_prop( 'is_paginated' ) ) {
			return;
		}
		$orderby                 = Functions::get_option_item( 'rtcl_general_settings', 'orderby' );
		$order                   = Functions::get_option_item( 'rtcl_general_settings', 'order' );
		$orderby_order           = $orderby . "-" . $order;
		$catalog_orderby_options = Options::get_listing_orderby_options();

		$default_orderby = Functions::get_loop_prop( 'is_search' ) ? 'relevance' : $orderby_order;
		$orderby         = isset( $_GET['orderby'] ) ? Functions::clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby; // WPCS: sanitization ok, input var ok, CSRF ok.

		if ( Functions::get_loop_prop( 'is_search' ) ) {
			$catalog_orderby_options = array_merge( [ 'relevance' => esc_html__( 'Relevance', 'classified-listing' ) ], $catalog_orderby_options );

			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
			$orderby = current( array_keys( $catalog_orderby_options ) );
		}

		Functions::get_template(
			'listing/loop/orderby',
			[
				'catalog_orderby_options' => $catalog_orderby_options,
				'orderby'                 => $orderby
			]
		);
	}


	/**
	 * Output the result count text (Showing x - x of x results).
	 */
	public static function result_count() {
		if ( ! Functions::get_loop_prop( 'is_paginated' ) ) {
			return;
		}
		$args = [
			'total'    => Functions::get_loop_prop( 'total' ),
			'per_page' => Functions::get_loop_prop( 'per_page' ),
			'current'  => Functions::get_loop_prop( 'current_page' ),
		];

		Functions::get_template( 'listing/loop/result-count', $args );
	}

	/**
	 * Outputs all queued notices on.
	 *
	 * @since 1.5.5
	 */
	public static function output_all_notices() {
		echo '<div class="rtcl-notices-wrapper">';
		Functions::print_notices();
		echo '</div>';
	}

	public static function loop_item_excerpt() {
		/** @var Listing $listing */
		global $listing;
		if ( empty( $listing ) ) {
			return;
		}
		if ( $listing->can_show_excerpt() ) {
			?>
			<p class="rtcl-excerpt"><?php $listing->the_excerpt(); ?></p>
			<?php
		}
	}

	public static function loop_item_meta() {
		/** @var Listing $listing */
		global $listing;
		if ( empty( $listing ) ) {
			return;
		}
		$listing->the_meta();
	}

	public static function loop_item_meta_buttons() {
		Functions::get_template( 'listing/meta-buttons' );
	}

	public static function loop_item_badges() {
		/** @var Listing $listing */
		global $listing;
		if ( empty( $listing ) ) {
			return;
		}
		$listing->the_badges();
	}

	public static function loop_item_listing_title() {
		/** @var Listing $listing */
		global $listing;

		if ( empty( $listing ) ) {
			return;
		}
		echo '<h3 class="' . esc_attr( apply_filters( 'rtcl_listing_loop_title_classes', 'listing-title rtcl-listing-title' ) ) . '"><a href="' . $listing->get_the_permalink() . '">' . $listing->get_the_title() . '</a></h3>';
	}

	public static function loop_item_wrapper_start() {
		/** @var Listing $listing */
		global $listing;
		if ( empty( $listing ) ) {
			return;
		}
		echo apply_filters( 'rtcl_loop_item_wrapper_start', sprintf( '<div class="item-content%s">', ! $listing->can_show_price() ? ' no-price' : '' ) );
	}

	public static function loop_item_wrapper_end() {
		echo apply_filters( 'rtcl_loop_item_wrapper_end', '</div>' );
	}

	public static function listing_price() {
		Functions::get_template( 'listing/loop/price' );
	}

	public static function listing_thumbnail() {
		Functions::get_template( 'listing/loop/thumbnail' );
	}

	public static function astra_sidebar() {
		$template = Functions::get_theme_slug_for_templates();

		if ( $template == 'astra' && function_exists( 'astra_page_layout' ) ) {
			if ( is_active_sidebar( 'rtcl-archive-sidebar' ) ) {
				remove_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 6 );
				add_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 15 );
			} else {
				if ( astra_page_layout() == 'left-sidebar' ) {
					Functions::get_template( 'global/sidebar' );
					remove_action( 'rtcl_sidebar', [ __CLASS__, 'get_sidebar' ], 10 );
					remove_action( 'rtcl_sidebar', [ __CLASS__, 'output_main_wrapper_end' ], 15 );
					remove_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 6 );
					add_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 15 );
					remove_action( 'rtcl_before_main_content', [ __CLASS__, 'output_main_wrapper_start' ], 8 );
				} else if ( astra_page_layout() == 'no-sidebar' ) {
					remove_action( 'rtcl_before_main_content', [ __CLASS__, 'output_main_wrapper_start' ], 8 );
					remove_action( 'rtcl_sidebar', [ __CLASS__, 'get_sidebar' ], 10 );
					remove_action( 'rtcl_sidebar', [ __CLASS__, 'output_main_wrapper_end' ], 15 );
					remove_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 6 );
					add_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 15 );
				} else if ( astra_page_layout() == 'right-sidebar' ) {
					remove_action( 'rtcl_before_main_content', [ __CLASS__, 'output_main_wrapper_start' ], 8 );
					remove_action( 'rtcl_sidebar', [ __CLASS__, 'output_main_wrapper_end' ], 15 );
					remove_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 6 );
					add_action( 'rtcl_before_main_content', [ __CLASS__, 'breadcrumb' ], 15 );
				}
			}
		}
	}

	public static function output_main_wrapper_start() {
		Functions::print_html( '<div class="rtcl-wrapper">' );
	}

	public static function output_content_wrapper() {
		Functions::get_template( 'global/wrapper-start' );
	}

	public static function breadcrumb() {
		Functions::breadcrumb();
	}

	public static function output_main_wrapper_end() {
		Functions::print_html( '</div>' );
	}

	public static function output_content_wrapper_end() {
		Functions::get_template( 'global/wrapper-end' );
	}

	public static function get_sidebar() {
		Functions::get_template( 'global/sidebar' );
	}

	/**
	 * Show an archive description on taxonomy archives.
	 */
	public static function taxonomy_archive_description() {
		if ( Functions::is_listing_taxonomy() && 0 === absint( get_query_var( 'paged' ) ) ) {
			$term = get_queried_object();

			if ( $term && ! empty( $term->description ) ) {
				echo '<div class="rtcl-term-description">' . Functions::format_content( $term->description ) . '</div>'; // WPCS: XSS ok.
			}
		}
	}

	public static function listing_archive_description() {
		// Don't display the description on search results page.
		if ( is_search() ) {
			return;
		}

		if ( is_post_type_archive( rtcl()->post_type ) && in_array( absint( get_query_var( 'paged' ) ), [
				0,
				1
			], true ) ) {
			$listings_page = get_post( Functions::get_page_id( 'listings' ) );
			if ( $listings_page ) {
				$description = Functions::format_content( $listings_page->post_content );
				if ( $description ) {
					echo '<div class="rtcl-page-description">' . $description . '</div>'; // WPCS: XSS ok.
				}
			}
		}
	}

	public static function no_listings_found() {
		Functions::get_template( 'listing/loop/no-listings-found' );
	}


	/**
	 * Add body classes for Rtcl pages.
	 *
	 * @param array $classes Body Classes.
	 *
	 * @return array
	 */
	public static function body_class( $classes ) {
		$classes = (array) $classes;
		if ( Functions::is_rtcl() ) {
			$classes[] = 'rtcl';
			$classes[] = 'rtcl-page';
		} elseif ( Functions::is_checkout_page() ) {
			$classes[] = 'rtcl-checkout';
			$classes[] = 'rtcl-page';
		} elseif ( Functions::is_account_page() ) {
			$classes[] = 'rtcl-account';
			$classes[] = 'rtcl-page';
			if ( Functions::is_registration_page_separate() ) {
				if ( Functions::is_account_page( 'registration' ) ) {
					$classes[] = 'rtcl-page-registration';
				} elseif ( ! is_user_logged_in() ) {
					$classes[] = 'rtcl-page-login';
				}
			}
		} elseif ( Functions::is_listing_form_page() ) {
			$classes[] = 'rtcl-form-page';
			$classes[] = 'rtcl-page';
		}

		if ( Functions::is_listing() && ! is_active_sidebar( 'rtcl-single-sidebar' ) ) {
			$classes[] = 'rtcl-single-no-sidebar';
		} elseif ( ( Functions::is_listings() || Functions::is_listing_taxonomy() ) && ! is_active_sidebar( 'rtcl-archive-sidebar' ) ) {
			$classes[] = 'rtcl-archive-no-sidebar';
		}

		$classes[] = 'rtcl-no-js';

		add_action( 'wp_footer', [ __CLASS__, 'no_js' ] );

		return array_unique( $classes );
	}


	/**
	 * Adds extra post classes for listings via the WordPress post_class hook, if used.
	 *
	 * Note: For performance reasons we instead recommend using listing_class/get_listing_class instead.
	 *
	 * @param array        $classes Current classes.
	 * @param string|array $class   Additional class.
	 * @param int          $post_id Post ID.
	 *
	 * @return array
	 * @since 1.5.4
	 */
	public static function listing_post_class( $classes, $class = '', $post_id = 0 ) {
		if ( ! $post_id || rtcl()->post_type !== get_post_type( $post_id ) ) {
			return $classes;
		}

		$listing = rtcl()->factory->get_listing( $post_id );

		if ( ! $listing ) {
			return $classes;
		}

		$classes[] = 'listing-item';
		$classes[] = 'rtcl-listing-item';

		return $classes;
	}


	/**
	 * NO JS handling.
	 *
	 * @since 1.5.4
	 */
	public static function no_js() {
		?>
		<script type="text/javascript">
			var c = document.body.className;
			c = c.replace(/rtcl-no-js/, 'rtcl-js');
			document.body.className = c;
		</script>
		<?php
	}

	public static function user_information( $current_user ) {
		$note = Functions::get_option_item( 'rtcl_general_settings', 'admin_note_to_users' );
		Functions::get_template( 'myaccount/user-info', compact( 'current_user', 'note' ) );
	}

	public static function account_navigation() {
		Functions::get_template( 'myaccount/navigation' );
	}

	public static function account_content() {
		global $wp;

		if ( ! empty( $wp->query_vars ) ) {
			foreach ( $wp->query_vars as $key => $value ) {
				// Ignore pagename param.
				if ( 'pagename' === $key ) {
					continue;
				}

				if ( has_action( 'rtcl_account_' . $key . '_endpoint' ) ) {
					do_action( 'rtcl_account_' . $key . '_endpoint', $value );

					return;
				}
			}
		}

		// No endpoint found? Default to dashboard.
		Functions::get_template( 'myaccount/dashboard', [
			'current_user' => get_user_by( 'id', get_current_user_id() ),
		] );
	}

	public static function checkout_content() {
		global $wp;

		if ( ! empty( $wp->query_vars ) ) {
			foreach ( $wp->query_vars as $key => $value ) {
				// Ignore pagename param.
				if ( 'pagename' === $key ) {
					continue;
				}

				if ( has_action( 'rtcl_checkout_' . $key . '_endpoint' ) ) {
					do_action( 'rtcl_checkout_' . $key . '_endpoint', $key, $value );

					return;
				}
			}
		}

		// No endpoint found? Default to error.
		Functions::get_template( 'checkout/error' );
	}

	public static function checkout_submission_endpoint( $type, $listing_id ) {
		Checkout::checkout_form( $type, $listing_id );
	}

	public static function checkout_payment_receipt_endpoint( $type, $payment_id ) {
		Checkout::payment_receipt( $payment_id );
	}

	public static function account_edit_account_endpoint() {
		MyAccount::edit_account();
	}

	public static function account_listings_endpoint() {
		MyAccount::my_listings();
	}

	public static function account_favourites_endpoint() {
		MyAccount::favourite_listings();
	}

	public static function account_payments_endpoint() {
		MyAccount::payments_history();
	}


	public static function social_login_shortcode() {
		if ( ! apply_filters( 'rtcl_social_login_shortcode_disabled', false ) ) {
			$shortcode = apply_filters( 'rtcl_social_login_shortcode', Functions::get_option_item( 'rtcl_account_settings', 'social_login_shortcode', '' ) );
			if ( $shortcode ) {
				echo sprintf( '<div class="rtcl-social-login-wrap">%s</div>', do_shortcode( $shortcode ) );
			}
		}
	}

	public static function logged_in_hidden_fields() {
		wp_nonce_field( 'rtcl-login', 'rtcl-login-nonce' );
		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = $_REQUEST['redirect_to'];
		} else {
			$redirect_to = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		echo sprintf( '<input type="hidden" name="redirect_to" value="%s" />', esc_url( $redirect_to ) );
	}

	public static function registration_hidden_fields() {
		wp_nonce_field( 'rtcl-register', 'rtcl-register-nonce' );
		if ( ! empty( $_REQUEST['redirect_to'] ) ) {
			$redirect_to = $_REQUEST['redirect_to'];
		} else {
			$redirect_to = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		echo sprintf( '<input type="hidden" name="redirect_to" value="%s" />', esc_url( $redirect_to ) );
	}

	/**
	 * Render privacy policy text on the register forms.
	 */
	public static function registration_privacy_policy() {
		Functions::privacy_policy_text( 'registration' );
	}


	public static function add_checkout_form_instruction() {
		?>
		<p><?php esc_html_e( 'Please review your order, and click Purchase once you are ready to proceed.', 'classified-listing' ); ?></p>
		<?php
	}


	public static function add_checkout_form_promotion_options( $type, $listing_id ) {
		if ( 'submission' === $type ) {
			if ( $listing_id && rtcl()->post_type === get_post_type( $listing_id ) ) {
				$pricing_options = Functions::get_regular_pricing_options();
				Functions::get_template( "checkout/promotions", [
					'pricing_options' => $pricing_options,
					'listing_id'      => $listing_id
				] );
			} else {
				Functions::add_notice( __( "Given Listing Id is not a valid listing", "classified-listing" ), "error" );
				Functions::get_template( "checkout/error" );
			}
		}
	}

	public static function add_checkout_payment_method() {
		Functions::get_template( "checkout/payment-methods" );
	}

	public static function add_checkout_billing_details() {
		if ( ! Functions::is_billing_address_disabled() ) {
			Functions::get_template( "checkout/form-billing" );
		}
	}


	public static function checkout_terms_and_conditions() {
		Functions::get_template( "checkout/terms-conditions" );
	}

	public static function registration_terms_and_conditions() {
		Functions::get_template( "myaccount/terms-conditions" );
	}


	public static function checkout_form_submit_button() {
		?>
		<div class="rtcl-submit-btn-wrap">
			<a class="btn btn-primary"
			   href="<?php echo esc_url( Link::get_my_account_page_link() ) ?>"><?php echo apply_filters( 'rtcl_checkout_myaccount_btn_text', __( 'Go to My Account', 'classified-listing' ) ); ?></a>
			<button type="submit" id="rtcl-checkout-submit-btn" name="rtcl-checkout" class="btn btn-primary"
					value="1"><?php echo apply_filters( 'rtcl_checkout_payment_btn_text', __( 'Proceed to payment', 'classified-listing' ) ); ?></button>
		</div>
		<?php
	}


	public static function edit_account_form_hidden_field() {
		wp_nonce_field( 'rtcl_update_user_account', 'rtcl_user_account_nonce' ); ?>
		<div class="rtcl-response"></div><?php
	}

	public static function add_checkout_hidden_field( $type ) {
		wp_nonce_field( 'rtcl_checkout', 'rtcl_checkout_nonce' );
		printf( '<input type="hidden" name="type" value="%s"/>', esc_attr( $type ) ); ?><input type="hidden"
																							   name="action"
																							   value="rtcl_ajax_checkout_action"/><?php
	}


	public static function add_submission_checkout_hidden_field( $type, $listing_id ) {
		if ( 'submission' === $type ) {
			printf( '<input type="hidden" name="listing_id" value="%d"/>', absint( $listing_id ) );
		}
	}


	/**
	 * Render privacy policy text on the checkout.
	 */
	public static function checkout_privacy_policy_text() {
		Functions::privacy_policy_text();
	}


	public static function checkout_terms_and_conditions_page_content() {
		$terms_page_id = Functions::get_terms_and_conditions_page_id();

		if ( ! $terms_page_id ) {
			return;
		}

		$page = get_post( $terms_page_id );

		if ( $page && 'publish' === $page->post_status && $page->post_content && ! has_shortcode( $page->post_content, 'rtcl_checkout' ) ) {
			echo '<div class="rtcl-terms-and-conditions" style="display: none; max-height: 200px; overflow: auto;">' . wp_kses_post( Functions::format_content( $page->post_content ) ) . '</div>';
		}
	}


	/**
	 * @param Filter $object
	 */
	public static function add_hidden_field_filter_form( $object ) {
		$args             = $object->get_instance();
		$current_category = ! empty( $args['current_taxonomy'][ rtcl()->category ] ) ? $args['current_taxonomy'][ rtcl()->category ]->slug : '';
		$current_location = ! empty( $args['current_taxonomy'][ rtcl()->location ] ) ? $args['current_taxonomy'][ rtcl()->location ]->slug : ''; ?>
		<input type="hidden" name="rtcl_category" value="<?php echo esc_attr( $current_category ) ?>">
		<input type="hidden" name="rtcl_location" value="<?php echo esc_attr( $current_location ) ?>">
		<?php
	}
}
