<?php

namespace Rtcl\Emails;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclEmail;

class ContactEmailToAdmin extends RtclEmail
{
    public $data = array();

    function __construct() {

        $this->id = 'contact_admin';
        $this->template_html = 'emails/contact-email-to-admin';

        // Call parent constructor.
        parent::__construct();
    }

    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_title}] Contact request', 'classified-listing');
    }


    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('An email is Received.', 'classified-listing');
    }


    /**
     * Trigger the sending of this email.
     *
     * @param array $data
     *
     * @param null  $optional
     *
     * @return bool
     * @throws \Exception
     */
    public function trigger($data, $optional = null) {
        $return = false;
        $this->setup_locale();
        $this->data = $data;
        $this->set_recipient(Functions::get_admin_email_id_s());
        if ($this->get_recipient()) {
            if (!empty($this->data['name']) && !empty($this->data['email'])) {
                $this->set_replay_to_name($this->data['name']);
                $this->set_replay_to_email_address($this->data['email']);
            }
            $return = $this->send();
        }
        $this->restore_locale();
        return $return;
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
                'data'  => $this->data
            )
        );
    }

}