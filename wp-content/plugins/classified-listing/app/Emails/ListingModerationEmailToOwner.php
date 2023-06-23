<?php


namespace Rtcl\Emails;


use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

class ListingModerationEmailToOwner extends RtclEmail
{

    public $data = array();

    function __construct() {

        $this->id = 'listing_moderation_to_user';
        $this->template_html = 'emails/listing-moderation-email-to-owner';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_name}] Your listing need to be improved', 'classified-listing');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('Your listing need to be improved', 'classified-listing');
    }

    /**
     * Trigger the sending of this email.
     *
     * @param               $listing_id
     * @param array         $data
     *
     * @return bool
     * @throws \Exception
     */
    public function trigger($listing_id, $data = array()) {

        $this->setup_locale();

        $return = false;

        $this->data = $data;
        $listing = null;
        if ($listing_id) {
            $listing = new Listing($listing_id);
        }

        if (is_a($listing, Listing::class)) {
            $this->object = $listing;
            $this->set_recipient($listing->get_owner_email());
        }

        if ($this->get_recipient()) {
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
                'listing' => $this->object,
                'email'   => $this,
                'data'    => $this->data
            )
        );
    }

}