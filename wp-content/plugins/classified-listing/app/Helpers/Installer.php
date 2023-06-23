<?php

namespace Rtcl\Helpers;


use Rtcl\Models\Roles;

class Installer {

	private static $db_updates = [
		'3.4.4' => [
			'update_344_recreate_roles',
			'update_344_db_version',
		],
	];

	public static function init() {
		add_action( 'init', [ __CLASS__, 'check_version' ], 5 );
	}

	public static function check_version() {
		if ( version_compare( get_option( 'rtcl_version' ), RTCL_VERSION, '<' ) ) {
			self::activate();
			do_action( 'rtcl_upgraded' );
		}
	}

	/**
	 * Get list of DB update callbacks.
	 *
	 * @return array
	 * @since  1.5.58
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;

	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {
		$current_db_version = get_option( 'rtcl_db_version' );
		$loop               = 0;

		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					rtcl()->queue()->schedule_single(
						time() + $loop,
						'rtcl_run_update_callback',
						[
							'update_callback' => $update_callback,
						],
						'rtcl-db-updates'
					);
					$loop ++;
				}
			}
		}
	}


	public static function activate() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'rtcl_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'rtcl_installing', 'yes', MINUTE_IN_SECONDS * 10 );


		if ( ! get_option( 'rtcl_version' ) ) {
			self::create_options();
		}

		self::create_tables();
		self::create_roles();
		self::upgrade();
		self::create_cron_jobs();
		self::update_rtcl_version();

		delete_transient( 'rtcl_installing' );

		do_action( 'rtcl_flush_rewrite_rules' );
		do_action( 'rtcl_installed' );

	}

	// TODO remove this method after few version update
	private static function update_user_email_settings() {
		$user_email_settings_updated = get_option( 'rtcl_email_user_settings_updated_temp' );
		if ( 'yes' !== $user_email_settings_updated && version_compare( RTCL_VERSION, '2.3.7', '>=' ) ) {
			$email_options = get_option( 'rtcl_email_settings' );
			if ( isset( $email_options['notify_users'] ) && is_array( $email_options['notify_users'] ) ) {
				array_push( $email_options['notify_users'], 'register_new_user' );
			}
			update_option( 'rtcl_email_settings', $email_options );
			update_option( 'rtcl_email_user_settings_updated_temp', 'yes' );
		}
	}

	private static function update_rtcl_version() {
		update_option( 'rtcl_version', RTCL_VERSION );
	}

	private static function create_options() {
		// Insert plugin settings and default values for the first time
		$options = [
			'rtcl_general_settings'    => [
				'load_bootstrap'               => [ 'css', 'js' ],
				'include_results_from'         => [ 'child_categories', 'child_locations' ],
				'listings_per_page'            => 20,
				'related_posts_per_page'       => 4,
				'orderby'                      => 'date',
				'order'                        => 'desc',
				'taxonomy_orderby'             => 'title',
				'taxonomy_order'               => 'asc',
				'text_editor'                  => 'wp_editor',
				'location_type'                => 'local',
				'location_level_first'         => esc_html__( "State", 'classified-listing' ),
				'location_level_second'        => esc_html__( "City", 'classified-listing' ),
				'location_level_third'         => esc_html__( "Town", 'classified-listing' ),
				'currency'                     => 'USD',
				'currency_position'            => 'right',
				'currency_thousands_separator' => ',',
				'currency_decimal_separator'   => '.',
			],
			'rtcl_moderation_settings' => [
				'listing_duration'             => 15,
				'new_listing_threshold'        => 3,
				'new_listing_label'            => esc_html__( "New", 'classified-listing' ),
				'popular_listing_threshold'    => 1000,
				'popular_listing_label'        => esc_html__( "Popular", 'classified-listing' ),
				'listing_featured_label'       => esc_html__( "Featured", 'classified-listing' ),
				'display_options'              => [
					'category',
					'location',
					'date',
					'user',
					'price',
					'views',
					'featured',
					'new',
					'popular'
				],
				'display_options_detail'       => [
					'category',
					'location',
					'date',
					'user',
					'price',
					'views',
					'featured',
					'new',
					'popular'
				],
				'detail_page_sidebar_position' => 'right',
				'has_favourites'               => 'yes',
				'has_report_abuse'             => 'yes',
				'has_contact_form'             => 'yes',
				'has_map'                      => 'yes',
				'maximum_images_per_listing'   => 5,
				'delete_expired_listings'      => 15,
				'new_listing_status'           => 'pending',
				'edited_listing_status'        => 'pending'
			],
			'rtcl_payment_settings'    => [
				'payment'                      => 'yes',
				'use_https'                    => 'no',
				'currency'                     => 'USD',
				'currency_position'            => 'right',
				'currency_thousands_separator' => ',',
				'currency_decimal_separator'   => '.',
			],
			'rtcl_payment_offline'     => [
				'enabled'      => 'yes',
				'title'        => esc_html__( 'Direct Bank Transfer', 'classified-listing' ),
				'description'  => esc_html__( "Make your payment directly in our bank account. Please use your Order ID as payment reference. Your order won't get approved until the funds have cleared in our account.",
					'classified-listing' ),
				'instructions' => esc_html__( 'Make your payment directly in our bank account. Please use your Order ID as payment reference. Your order won\'t get approved until the funds have cleared in our account.
Account details :
		
Account Name : YOUR ACCOUNT NAME
Account Number : YOUR ACCOUNT NUMBER
Bank Name : YOUR BANK NAME
		
If we don\'t receive your payment within 48 hrs, we will cancel the order.', 'classified-listing' ),
			],
			'rtcl_email_settings'      => [
				'from_name'                  => get_option( 'blogname' ),
				'from_email'                 => get_option( 'admin_email' ),
				'admin_notice_emails'        => get_option( 'admin_email' ),
				'email_type'                 => 'html',
				'notify_admin'               => [
					'register_new_user',
					'listing_submitted',
					'order_created',
					'payment_received'
				],
				'notify_users'               => [
					'register_new_user',
					'listing_submitted',
					'listing_published',
					'listing_renewal',
					'listing_expired',
					'remind_renewal',
					'order_created',
					'order_completed'
				],
				'listing_submitted_subject'  => esc_html__( '[{site_title}] Listing "{listing_title}" is received', 'classified-listing' ),
				'listing_submitted_heading'  => esc_html__( 'Your listing is received', 'classified-listing' ),
				'listing_published_subject'  => esc_html__( '[{site_title}] Listing "{listing_title}" is published', 'classified-listing' ),
				'listing_published_heading'  => esc_html__( 'Your listing is published', 'classified-listing' ),
				'renewal_email_threshold'    => 3,
				'renewal_subject'            => esc_html__( '[{site_name}] {listing_title} - Expiration notice', 'classified-listing' ),
				'renewal_heading'            => esc_html__( 'Expiration notice', 'classified-listing' ),
				'expired_subject'            => esc_html__( '[{site_title}] {listing_title} - Expiration notice', 'classified-listing' ),
				'expired_heading'            => esc_html__( 'Expiration notice', 'classified-listing' ),
				'renewal_reminder_threshold' => 3,
				'renewal_reminder_subject'   => esc_html__( '[{site_title}] {listing_title} - Renewal reminder', 'classified-listing' ),
				'renewal_reminder_heading'   => esc_html__( 'Renewal reminder', 'classified-listing' ),
				'order_created_subject'      => esc_html__( '[{site_title}] #{order_number} Thank you for your order', 'classified-listing' ),
				'order_created_heading'      => esc_html__( 'New Order: #{order_number}', 'classified-listing' ),
				'order_completed_subject'    => esc_html__( '[{site_title}] : #{order_number} Order is completed.', 'classified-listing' ),
				'order_completed_heading'    => esc_html__( 'Payment is completed: #{order_number}', 'classified-listing' ),
				'contact_subject'            => esc_html__( '[{site_title}] Contact via "{listing_title}"', 'classified-listing' ),
				'contact_heading'            => esc_html__( 'Thank you for mail', 'classified-listing' )
			],
			'rtcl_account_settings'    => [
				'enable_myaccount_registration' => "yes"
			],
			'rtcl_style_settings'      => [
				'primary'      => "#0066bf",
				'link'         => "#111111",
				'link_hover'   => "#0066bf",
				'button'       => "#0066bf",
				'button_hover' => "#3065c1",
				'button_text'  => "#ffffff"
			],
			'rtcl_misc_settings'       => [
				'image_size_gallery'           => [ 'width' => 924, 'height' => 462, 'crop' => 'yes' ],
				'image_size_gallery_thumbnail' => [ 'width' => 150, 'height' => 105, 'crop' => 'yes' ],
				'image_size_thumbnail'         => [ 'width' => 320, 'height' => 240, 'crop' => 'yes' ],
				'image_allowed_type'           => [ 'png', 'jpg', 'jpeg' ],
				'image_allowed_memory'         => 2,
				'image_edit_cap'               => 'yes',
				'social_services'              => [ 'facebook', 'twitter' ],
				'social_pages'                 => [ 'listing' ],
				'map_type'                     => 'osm',
				'map_zoom_level'               => 10,
				'map_center'                   => [
					'address' => '',
					'lat'     => 0,
					'lng'     => 0,
				]
			],
			'rtcl_chat_settings'       => [
				'enable'                                => 'yes',
				'unread_message_email'                  => 'yes',
				'remove_inactive_conversation_duration' => 30
			],
			'rtcl_advanced_settings'   => [
				'permalink'                         => 'rtcl_listing',
				'category_base'                     => esc_html_x( 'listing-category', 'slug', 'classified-listing' ),
				'location_base'                     => esc_html_x( 'listing-location', 'slug', 'classified-listing' ),
				'myaccount_listings_endpoint'       => 'listings',
				'myaccount_favourites_endpoint'     => 'favourites',
				'myaccount_chat_endpoint'           => 'chat',
				'myaccount_edit_account_endpoint'   => 'edit-account',
				'myaccount_payments_endpoint'       => 'payments',
				'myaccount_lost_password_endpoint'  => 'lost-password',
				'myaccount_logout_endpoint'         => 'logout',
				'checkout_submission_endpoint'      => 'submission',
				'checkout_promote_endpoint'         => 'promote',
				'checkout_payment_receipt_endpoint' => 'payment-receipt',
				'checkout_payment_failure_endpoint' => 'payment-failure'
			]
		];

		foreach ( $options as $option_name => $defaults ) {
			if ( false === get_option( $option_name ) ) {
				add_option( $option_name, apply_filters( $option_name . '_defaults', $defaults ) );
			}
		}

		$pages = Functions::insert_custom_pages();
		if ( ! empty( $pages ) ) {
			$pSettings = get_option( 'rtcl_advanced_settings', [] );
			foreach ( $pages as $pSlug => $pId ) {
				if ( $pId > 0 ) {
					$pSettings[ $pSlug ] = $pId;
				}
			}
			update_option( 'rtcl_advanced_settings', $pSettings );
		}
	}

	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::get_schema() );
	}

	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		return [
			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}rtcl_sessions (
						  session_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
						  session_key char(32) NOT NULL,
						  session_value longtext NOT NULL,
						  session_expiry BIGINT UNSIGNED NOT NULL,
						  PRIMARY KEY  (session_key),
						  UNIQUE KEY session_id (session_id)
						) $collate;"
		];
	}

	private static function upgrade() {
		self::update_user_email_settings(); // TODO remove this method after few version update
	}


	public static function deactivate() {
		self::clean_cron_jobs();
	}

	public static function clean_cron_jobs() {
		// Un-schedules all previously-scheduled cron jobs
		wp_clear_scheduled_hook( 'rtcl_hourly_scheduled_events' );
		wp_clear_scheduled_hook( 'rtcl_daily_scheduled_events' );
		wp_clear_scheduled_hook( 'rtcl_cleanup_sessions' );
	}

	/**
	 * Create cron jobs (clear them first).
	 */
	private static function create_cron_jobs() {
		self::clean_cron_jobs();
		if ( ! wp_next_scheduled( 'rtcl_cleanup_sessions' ) ) {
			wp_schedule_event( time() + ( 6 * HOUR_IN_SECONDS ), 'twicedaily', 'rtcl_cleanup_sessions' );
		}
		if ( ! wp_next_scheduled( 'rtcl_hourly_scheduled_events' ) ) {
			wp_schedule_event( time(), 'hourly', 'rtcl_hourly_scheduled_events' );
		}

		if ( ! wp_next_scheduled( 'rtcl_daily_scheduled_events' ) ) {
			$ve = get_option( 'gmt_offset' ) > 0 ? '-' : '+';
			wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . absint( get_option( 'gmt_offset' ) ) . ' HOURS' ), 'daily', 'rtcl_daily_scheduled_events' );
		}
	}

	public static function create_roles() {
		Roles::create_roles();
	}
}