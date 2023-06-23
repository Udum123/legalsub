<?php


namespace Rtcl\Models;


use Rtcl\Emails\ContactEmailToAdmin;
use Rtcl\Emails\ListingContactEmailToAdmin;
use Rtcl\Emails\ListingContactEmailToOwner;
use Rtcl\Emails\ListingExpiredEmailToAdmin;
use Rtcl\Emails\ListingExpiredEmailToOwner;
use Rtcl\Emails\ListingModerationEmailToOwner;
use Rtcl\Emails\ListingPublishedEmailToOwner;
use Rtcl\Emails\ListingRenewalEmailToOwner;
use Rtcl\Emails\ListingRenewalReminderEmailToOwner;
use Rtcl\Emails\ListingSubmittedEmailToAdmin;
use Rtcl\Emails\ListingSubmittedEmailToOwner;
use Rtcl\Emails\ListingUpdateEmailToAdmin;
use Rtcl\Emails\OrderCompletedEmailToAdmin;
use Rtcl\Emails\OrderCompletedEmailToCustomer;
use Rtcl\Emails\OrderCreatedEmailToAdmin;
use Rtcl\Emails\OrderCreatedEmailToCustomer;
use Rtcl\Emails\ReportAbuseEmailToAdmin;
use Rtcl\Emails\UserNewRegistrationEmailToAdmin;
use Rtcl\Emails\UserNewRegistrationEmailToUser;
use Rtcl\Emails\UserResetPasswordEmailToUser;
use Rtcl\Helpers\Functions;
use Rtcl\Resources\Options;
use Rtcl\Traits\SingletonTrait;

class RtclEmails {

	use SingletonTrait;

	/**
	 * Array of email notification classes
	 *
	 * @var RtclEmail[]
	 */
	public $emails = array();


	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 */
	public function __init() {

		$this->load_email_services();

		// Email Header, Footer and content hooks.
		add_action( 'rtcl_email_header', [ __CLASS__, 'email_header' ] );
		add_action( 'rtcl_email_footer', [ __CLASS__, 'email_footer' ] );

		// Email Order details
		add_action( 'rtcl_email_order_details', [ __CLASS__, 'order_details' ], 10, 3 );

		// Email Order user details
		add_action( 'rtcl_email_order_customer_details', [ __CLASS__, 'order_customer_details' ], 10, 3 );

		// Let 3rd parties unhook the above via this hook.
		do_action( 'rtcl_email', $this );
	}

	/**
	 * Init email classes.
	 */
	public function load_email_services() {

		$this->emails['Listing_Submitted_Email_To_Owner']        = new ListingSubmittedEmailToOwner();
		$this->emails['Listing_Published_Email_To_Owner']        = new ListingPublishedEmailToOwner();
		$this->emails['Listing_Update_Email_To_Admin']           = new ListingUpdateEmailToAdmin();
		$this->emails['Listing_Contact_Email_To_Owner']          = new ListingContactEmailToOwner();
		$this->emails['Listing_Contact_Email_To_Admin']          = new ListingContactEmailToAdmin();
		$this->emails['Report_Abuse_Email_To_Admin']             = new ReportAbuseEmailToAdmin();
		$this->emails['Listing_Submitted_Email_To_Admin']        = new ListingSubmittedEmailToAdmin();
		$this->emails['Listing_Moderation_Email_To_Owner']       = new ListingModerationEmailToOwner();
		$this->emails['Listing_Renewal_Email_To_Owner']          = new ListingRenewalEmailToOwner();
		$this->emails['Listing_Expired_Email_To_Owner']          = new ListingExpiredEmailToOwner();
		$this->emails['Listing_Expired_Email_To_Admin']          = new ListingExpiredEmailToAdmin();
		$this->emails['Listing_Renewal_Reminder_Email_To_Owner'] = new ListingRenewalReminderEmailToOwner();
		$this->emails['Order_Created_Email_To_Customer']         = new OrderCreatedEmailToCustomer();
		$this->emails['Order_Created_Email_To_Admin']            = new OrderCreatedEmailToAdmin();
		$this->emails['Order_Completed_Email_To_Customer']       = new OrderCompletedEmailToCustomer();
		$this->emails['Order_Completed_Email_To_Admin']          = new OrderCompletedEmailToAdmin();
		$this->emails['User_New_Registration_Email_To_Admin']    = new UserNewRegistrationEmailToAdmin();
		$this->emails['User_New_Registration_Email_To_User']     = new UserNewRegistrationEmailToUser();
		$this->emails['User_Reset_Password_Email_To_User']       = new UserResetPasswordEmailToUser();
		$this->emails['Contact_Email_To_Admin']                  = new ContactEmailToAdmin();

		$this->emails = apply_filters( 'rtcl_email_services', $this->emails );
	}


	/**
	 * Get the email header.
	 *
	 * @param RtclEmail $email
	 */
	public static function email_header( $email ) {
		Functions::get_template( 'emails/email-header', array( 'email' => $email ) );
	}

	/**
	 * Get the email footer.
	 *
	 * @param RtclEmail $email 
	 */
	public static function email_footer( $email ) {
		Functions::get_template( 'emails/email-footer', array( 'email' => $email ) );
	}


	/**
	 * Wraps a message in the classified-listing mail template.
	 *
	 * @param string $message Email message.
	 * @param string $email RtclEmail
	 *
	 * @return string
	 */
	public function wrap_message( $message, $email = RtclEmail::class ) {
		// Buffer.
		ob_start();

		do_action( 'rtcl_email_header', $email );

		echo wpautop( wptexturize( $message ) ); // WPCS: XSS ok.

		do_action( 'rtcl_email_footer', $email );

		// Get contents.
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * @param Payment $order
	 * @param bool $sent_to_admin
	 * @param null $email
	 */
	public static function order_details( $order, $sent_to_admin = false, $email = null ) {
		$promotions = Options::get_listing_promotions();
		$promotions_html = '';
		if(!empty($promotions)) {
			foreach ( $promotions as $promo_id => $promotion ) {
				if ( $order->pricing->hasPromotion( $promo_id ) ) {
					$promotions_html .= ', <strong>' . esc_html( $promotion ) . '</strong>';
				}
			}
		}
		$item_details_fields = apply_filters( 'rtcl_email_order_item_details_fields', [
			'item_title'           => [
				'type'  => 'title',
				'label' => esc_html( apply_filters( 'rtcl_email_order_item_details_title', get_the_title( $order->get_listing_id() ), $order ) )
			],
			'payment_option_title' => [
				'label' => esc_html__( 'Payment Option ', 'classified-listing' ),
				'value' => esc_html( $order->pricing->getTitle() )
			],
			'features'             => [
				'label' => esc_html__( 'Features ', 'classified-listing' ),
				'value' => sprintf( '<strong>%d %s</strong>%s',
					absint( $order->pricing->getVisible() ),
					esc_html__( 'Days', 'classified-listing' ),
					$promotions_html
				)
			]

		], $order, $sent_to_admin, $email );

		Functions::get_template(
			'emails/email-order-details',
			array(
				'order'               => $order,
				'sent_to_admin'       => $sent_to_admin,
				'email'               => $email,
				'item_details_fields' => $item_details_fields,
			)
		);
	}

	public static function order_customer_details( $order, $sent_to_admin = false, $email = null ) {
		if ( ! $order instanceof Payment) {
			return;
		}

		$fields = array_filter( apply_filters( 'rtcl_email_order_customer_details_fields', array(), $sent_to_admin, $order ) );

		if ( ! empty( $fields ) ) {
			Functions::get_template( 'emails/email-order-customer-details', array( 'fields' => $fields ) );
		}
	}


	/**
	 * Send the email.
	 *
	 * @param mixed $to Receiver.
	 * @param mixed $subject Email subject.
	 * @param mixed $message Message.
	 * @param string $headers Email headers (default: "Content-Type: text/html\r\n").
	 * @param string $attachments Attachments (default: "").
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = '' ) {
		// Send.
		$email = new RtclEmail();

		$email
			->set_headers( $headers )
			->set_recipient( $to )
			->set_subject( $subject )
			->set_content( $this->wrap_message( $message, $email ) )
			->set_attachments( $attachments );

		return $email->send();
	}


}