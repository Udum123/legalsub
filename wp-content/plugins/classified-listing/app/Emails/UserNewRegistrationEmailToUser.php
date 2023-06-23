<?php

namespace Rtcl\Emails;

use Rtcl\Models\RtclEmail;
use Rtcl\Helpers\Functions;

class UserNewRegistrationEmailToUser extends RtclEmail
{

    public $user = null;
    public $data = [];

    function __construct() {

        $this->id = 'new_user_registration_to_user';
        $this->template_html = 'emails/user-new-registration-email-to-user';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('Registration successful at [{site_title}] ', 'classified-listing');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('Registration successful', 'classified-listing');
    }


    /**
     * Trigger the sending of this email.
     *
     * @param       $user_id
     * @param array $data
     *
     * @throws \Exception
     */
    public function trigger($user_id, $data = array()) {
        if (!$user_id) {
            return;
        }
        $this->data = $data;
        $this->setup_locale();
        $this->user = get_userdata($user_id);
        $this->set_recipient($this->user->user_email);
        if ($this->get_recipient()) {
            $this->send();
        }

        $this->restore_locale();
    }


    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
        return Functions::get_template_html(
            $this->template_html, array(
                'email' => $this,
                'user'  => $this->user,
                'data'  => $this->data
            )
        );
    }

}