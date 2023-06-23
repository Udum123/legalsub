<?php

namespace Rtcl\Controllers\Admin;


use DateInterval;
use DateTime;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;

/**
 * Class PaymentStatus
 *
 * @package Rtcl\Controllers\Admin
 */
class PaymentStatus {

	function __construct() {
		add_action( 'transition_post_status', [ $this, 'transition_post_status' ], 10, 3 );
	}


	public function transition_post_status( $new_status, $old_status, $post ) {

		if ( rtcl()->post_type_payment !== $post->post_type ) {
			return;
		}

		// TODO : need to add some logic
		if ( 'rtcl-completed' === $new_status && 'rtcl-completed' !== $old_status ) {
			$order = rtcl()->factory->get_order( $post->ID );

			if ( $order && ( empty( $order->pricing->getType() ) || "regular" === $order->pricing->getType() ) ) {
				$listing = rtcl()->factory->get_listing( $order->get_listing_id() );
				if ( ! absint( get_post_meta( $post->ID, '_applied', true ) ) && $listing && in_array( $listing->get_status(), [
						'publish',
						'rtcl-expired'
					], true ) && $visible = absint( $order->pricing->getVisible() ) ) {
					$promotions                  = [];
					$do_update_status_to_publish = false;
					$hasAnyPromotion             = false;
					$rtcl_promotions             = Options::get_listing_promotions();
					foreach ( $rtcl_promotions as $rtcl_promo_id => $rtcl_promotion ) {
						if ( $order->pricing->hasPromotion( $rtcl_promo_id ) ) {
							$hasAnyPromotion              = true;
							$promotions[ $rtcl_promo_id ] = $visible;
						}
					}
					if ( $hasAnyPromotion ) {
						$do_update_status_to_publish = true;
						try {
							$current_date = new DateTime( current_time( 'mysql' ) );
							$current_date->add( new DateInterval( "P{$visible}D" ) );
							$expDate = $current_date->format( 'Y-m-d H:i:s' );
							update_post_meta( $order->get_listing_id(), 'expiry_date', $expDate );
						} catch ( \Exception $e ) {
							
						}
					}
					$promotions_status = Functions::update_listing_promotions( $order->get_listing_id(), $promotions );
					// Check if post expired , then turn it to published
					if ( "rtcl-expired" === $listing->get_status() && $do_update_status_to_publish && ! empty( $promotions_status ) ) {
						wp_update_post( [
							'ID'          => $listing->get_id(),
							'post_status' => 'publish'
						] );
					}

					update_post_meta( $order->get_id(), '_applied', 1 );

					// Hook for developers
					do_action( 'rtcl_payment_completed', $order );

				}
			}


			if ( $order ) {
				// send emails
				if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_users', 'order_completed', 'multi_checkbox' ) ) {
					rtcl()->mailer()->emails['Order_Completed_Email_To_Customer']->trigger( $order->get_id(), $order );
				}

				if ( Functions::get_option_item( 'rtcl_email_settings', 'notify_admin', 'order_completed', 'multi_checkbox' ) ) {
					rtcl()->mailer()->emails['Order_Completed_Email_To_Admin']->trigger( $order->get_id(), $order );
				}
			}

		}

	}

}