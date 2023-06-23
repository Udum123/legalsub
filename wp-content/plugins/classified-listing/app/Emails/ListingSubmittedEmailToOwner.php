<?php


namespace Rtcl\Emails;


use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

class ListingSubmittedEmailToOwner extends RtclEmail
{
    /**
     * Constructor.
     */
    public function __construct() {
        $this->db = true;
        $this->id = 'listing_submitted';
        $this->template_html = 'emails/listing-submitted-email-to-owner';

        // Call parent constructor.
        parent::__construct();

    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_title}] Listing "{listing_title}" is received', 'classified-listing');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('Your listing is received', 'classified-listing');
    }

    /**
     * Trigger the sending of this email.
     *
     * @param               $listing_id
     * @param Listing|false $listing Listing object.
     *
     * @throws \Exception
     */
    public function trigger($listing_id, $listing = false) {
        $this->setup_locale();

        if ($listing_id && !is_a($listing, Listing::class)) {
            $listing = new Listing($listing_id);
        }

        if (is_a($listing, Listing::class)) {
            $this->object = $listing;
            $this->placeholders = wp_parse_args(array(
                '{listing_title}' => $listing->get_the_title()
            ), $this->placeholders);
            $this->set_recipient($listing->get_owner_email());
        }

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
                'listing' => $this->object,
                'email'   => $this,
            )
        );
    }
}