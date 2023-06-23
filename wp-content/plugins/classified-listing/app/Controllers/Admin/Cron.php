<?php

namespace Rtcl\Controllers\Admin;

use Rtcl\Controllers\SessionHandler;
use Rtcl\Helpers\Functions;

class Cron {

	function __construct() {
		add_action( 'rtcl_hourly_scheduled_events', [ $this, 'hourly_scheduled_events' ] );
		add_action( 'rtcl_daily_scheduled_events', [ $this, 'daily_scheduled_events' ] );
		add_action( 'rtcl_cleanup_sessions', [ $this, 'cleanup_session_data' ] );
	}

	/**
	 * Cleans up session data - cron callback.
	 *
	 * @since 2.2.7
	 */
	function cleanup_session_data() {
		$session_class = apply_filters( 'rtcl_session_handler', SessionHandler::class );
		$session       = new $session_class();

		if ( is_callable( [ $session, 'cleanup_sessions' ] ) ) {
			$session->cleanup_sessions();
		}
	}

	function daily_scheduled_events() {
		do_action( 'rtcl_cron_daily_scheduled_events' );
	}

	function hourly_scheduled_events() {
		// TODO : Active all this function to active
		$this->sent_renewal_email_to_published_listings();
		$this->move_listings_publish_to_expired();
		$this->send_renewal_reminders();
		$this->delete_expired_listings();
		$this->remove_expired_featured();
		do_action( 'rtcl_cron_hourly_scheduled_events' );
	}

	function sent_renewal_email_to_published_listings() {
		$email_settings  = Functions::get_option( 'rtcl_email_settings' );
		$email_threshold = (int) $email_settings['renewal_email_threshold'];

		if ( $email_threshold > 0 ) {

			$email_threshold_date = date( 'Y-m-d H:i:s', strtotime( "+" . $email_threshold . " days" ) );

			// Define the query
			$args = [
				'post_type'           => rtcl()->post_type,
				'posts_per_page'      => - 1,
				'post_status'         => 'publish',
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'meta_query'          => [
					'relation' => 'AND',
					[
						'key'     => 'expiry_date',
						'value'   => $email_threshold_date,
						'compare' => '<',
						'type'    => 'DATETIME'
					],
					[
						'key'     => 'renewal_reminder_sent',
						'compare' => 'NOT EXISTS'
					],
					[
						'key'     => 'never_expires',
						'compare' => 'NOT EXISTS',
					]
				]
			];

			$rtcl_query = new \WP_Query( apply_filters( 'rtcl_cron_sent_renewal_email_to_published_listings_query_args', $args ) );

			if ( ! empty( $rtcl_query->posts ) ) {

				foreach ( $rtcl_query->posts as $post_id ) {
					// Send emails to user
					if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_users', 'listing_renewal', 'multi_checkbox' ) ) {
						if ( rtcl()->mailer()->emails['Listing_Renewal_Email_To_Owner']->trigger( $post_id ) ) {
							update_post_meta( $post_id, 'renewal_reminder_sent', 1 );
						}
					}
					do_action( "rtcl_cron_sent_renewal_email_to_published_listing", $post_id );
				}
			}

		}
	}

	function move_listings_publish_to_expired() {

		$moderation_settings        = Functions::get_option( 'rtcl_moderation_settings' );
		$email_settings             = Functions::get_option( 'rtcl_email_template_renewal_reminder' );
		$renewal_reminder_threshold = isset( $email_settings['renewal_reminder_threshold'] ) ? absint( $email_settings['renewal_reminder_threshold'] ) : 0;
		$delete_expired_listings    = isset( $moderation_settings['delete_expired_listings'] ) ? absint( $moderation_settings['delete_expired_listings'] ) : 0;
		$delete_threshold           = $renewal_reminder_threshold + $delete_expired_listings;

		// Define the query
		$args = [
			'post_type'           => rtcl()->post_type,
			'posts_per_page'      => - 1,
			'post_status'         => 'publish',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'          => [
				'relation' => 'AND',
				[
					'key'     => 'expiry_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<',
					'type'    => 'DATETIME'
				],
				[
					'key'     => 'never_expires',
					'compare' => 'NOT EXISTS',
				]
			]
		];

		$rtcl_query = new \WP_Query( apply_filters( 'rtcl_cron_move_listings_publish_to_expired_query_args', $args ) );

		if ( ! empty( $rtcl_query->posts ) ) {

			foreach ( $rtcl_query->posts as $post_id ) {
				// Update the post into the database
				$newData = [
					'ID'          => $post_id,
					'post_status' => 'rtcl-expired'
				];

				wp_update_post( $newData );      // Update post status to
				delete_post_meta( $post_id, 'expiry_date' );
				delete_post_meta( $post_id, 'never_expired' );
				update_post_meta( $post_id, 'featured', 0 );
				delete_post_meta( $post_id, 'feature_expiry_date' );
				delete_post_meta( $post_id, 'renewal_reminder_sent' );

				if ( $delete_threshold > 0 ) {
					$deletion_date_time = date( 'Y-m-d H:i:s', strtotime( "+" . $delete_threshold . " days" ) );
					update_post_meta( $post_id, 'deletion_date', $deletion_date_time ); // TODO : Need to check from where it to make action
				}

				if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_users', 'listing_expired', 'multi_checkbox' ) ) {
					rtcl()->mailer()->emails['Listing_Expired_Email_To_Owner']->trigger( $post_id );
				}

				if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_admin', 'listing_expired', 'multi_checkbox' ) ) {
					rtcl()->mailer()->emails['Listing_Expired_Email_To_Admin']->trigger( $post_id );
				}

				// Hook for developers
				do_action( 'rtcl_cron_move_listing_publish_to_expired', $post_id );
			}
		}

	}

	function delete_expired_listings() {

		$moderation_settings = Functions::get_option( 'rtcl_moderation_settings' );
		$email_settings      = Functions::get_option( 'rtcl_email_template_renewal_reminder' );

		$renewal_reminder_threshold = isset( $email_settings['renewal_reminder_threshold'] ) ? (int) $email_settings['renewal_reminder_threshold'] : 0;
		$delete_expired_listings    = isset( $moderation_settings['delete_expired_listings'] ) ? (int) $moderation_settings['delete_expired_listings'] : 0;
		$can_renew                  = Functions::get_option_item( 'rtcl_moderation_settings', 'has_listing_renewal', false, 'checkbox' );

		if ( $can_renew ) {
			$delete_threshold = $renewal_reminder_threshold + $delete_expired_listings;
		} else {
			$delete_threshold = $delete_expired_listings;
		}

		if ( $delete_threshold > 0 ) {

			// Define the query
			$args = [
				'post_type'           => rtcl()->post_type,
				'posts_per_page'      => - 1,
				'post_status'         => 'rtcl-expired',
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'meta_query'          => [
					'relation' => 'AND',
					[
						'key'     => 'deletion_date',
						'value'   => current_time( 'mysql' ),
						'compare' => '<',
						'type'    => 'DATETIME'
					],
					[
						'key'     => 'never_expires',
						'compare' => 'NOT EXISTS',
					]
				]
			];

			$rtcl_query = new \WP_Query( apply_filters( 'rtcl_cron_delete_expired_listings_query_args', $args ) );

			if ( ! empty( $rtcl_query->posts ) ) {

				foreach ( $rtcl_query->posts as $post_id ) {
					do_action( "rtcl_cron_delete_expired_listing", $post_id );
					Functions::delete_post( $post_id );
				}
			}
		}
	}

	/**
	 * Renewal Reminders
	 *
	 * @return void
	 */
	function send_renewal_reminders() {
		$email_settings     = Functions::get_option( 'rtcl_email_settings' );
		$reminder_threshold = isset( $email_settings['renewal_reminder_threshold'] ) ? (int) $email_settings['renewal_reminder_threshold'] : 0;

		if ( $reminder_threshold > 0 ) {
			// Define the query
			$args = [
				'post_type'           => rtcl()->post_type,
				'posts_per_page'      => - 1,
				'post_status'         => 'rtcl-expired',
				'fields'              => 'ids',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'meta_query'          => [
					'relation' => 'AND',
					[
						'key'     => 'renewal_reminder_sent',
						'value'   => 0,
						'compare' => '='
					],
					[
						'key'     => 'never_expires',
						'compare' => 'NOT EXISTS',
					]
				]
			];

			$rtcl_query = new \WP_Query( apply_filters( 'rtcl_cron_send_renewal_reminders_query_args', $args ) );

			if ( ! empty( $rtcl_query->posts ) ) {

				foreach ( $rtcl_query->posts as $post_id ) {

					$expiration_date      = get_post_meta( $post_id, 'expiry_date', true );
					$expiration_date_time = strtotime( $expiration_date );
					$reminder_date_time   = strtotime( "+" . $reminder_threshold . " days", strtotime( $expiration_date_time ) );

					if ( current_time( 'timestamp' ) > $reminder_date_time ) {

						// Send renewal reminder emails to listing owner
						update_post_meta( $post_id, 'renewal_reminder_sent', 1 );
						if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_users', 'remind_renewal', 'multi_checkbox' ) ) {
							rtcl()->mailer()->emails['Listing_Renewal_Reminder_Email_To_Owner']->trigger( $post_id );
						}

						do_action( 'rtcl_cron_send_renewal_reminders_listing', $post_id );
					}
				}
			}

		}
	}

	private function remove_expired_featured() {
		// Define the query
		$args = [
			'post_type'           => rtcl()->post_type,
			'posts_per_page'      => - 1,
			'post_status'         => 'publish',
			'fields'              => 'ids',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'          => [
				'relation' => 'AND',
				[
					'key'     => 'feature_expiry_date',
					'value'   => current_time( 'mysql' ),
					'compare' => '<',
					'type'    => 'DATETIME'
				],
				[
					'key'     => 'featured',
					'compare' => '=',
					'value'   => 1,
				]
			]
		];


		$rtcl_query = new \WP_Query( apply_filters( 'rtcl_cron_remove_expired_featured_query_args', $args ) );

		if ( ! empty( $rtcl_query->posts ) ) {

			foreach ( $rtcl_query->posts as $post_id ) {
				delete_post_meta( $post_id, 'featured' );
				delete_post_meta( $post_id, 'feature_expiry_date' );
				do_action( "rtcl_cron_remove_expired_featured_listing", $post_id );
			}
		}
	}

}