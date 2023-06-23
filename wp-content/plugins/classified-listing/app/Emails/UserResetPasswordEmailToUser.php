<?php

namespace Rtcl\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

class UserResetPasswordEmailToUser extends RtclEmail {

	public $user = null;
	public $reset_key = null;

	function __construct() {

		$this->id            = 'user_reset_password';
		$this->template_html = 'emails/user-reset-password-email-to-user';

		// Call parent constructor.
		parent::__construct();
	}


	/**
	 * Get email subject.
	 * @return string
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] Reset your password', 'classified-listing' );
	}

	/**
	 * Get email heading.
	 * @return string
	 */
	public function get_default_heading() {
		return esc_html__( 'Reset your password', 'classified-listing' );
	}


	/**
	 * Trigger the sending of this email.
	 *
	 * @param       $user \WP_User
	 * @param       $reset_key
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function trigger( $user, $reset_key = null ) {
		if ( ! $user || ! $reset_key ) {
			return;
		}

		$this->setup_locale();
		$this->user      = $user;
		$this->reset_key = $reset_key;
		$this->set_recipient( $user->user_email );
		if ( $this->get_recipient() ) {
			$this->send();
		}

		$this->restore_locale();

	}


	/**
	 * Get content html.
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return Functions::get_template_html(
            $this->template_html, array(
                'email'     => $this,
                'user'      => $this->user,
                'reset_key' => $this->reset_key
            )
		);
	}

}