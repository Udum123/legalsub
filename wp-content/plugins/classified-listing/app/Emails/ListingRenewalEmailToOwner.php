<?php


namespace Rtcl\Emails;


use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;

class ListingRenewalEmailToOwner extends RtclEmail
{

    public $data = array();

    function __construct() {
        $this->db = true;
        $this->id = 'renewal';
        $this->template_html = 'emails/listing-renewal-email-to-owner';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_name}] {listing_title} - Expiration notice', 'classified-listing');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('Expiration notice', 'classified-listing');
    }

    /**
     * Trigger the sending of this email.
     *
     * @param               $listing_id
     * @param Listing|false $listing Listing
     *
     * @return bool
     * @throws \Exception
     */
    public function trigger($listing_id, $listing = false) {

        $return = false;

        $this->setup_locale();

        if ($listing_id && !is_a($listing, Listing::class)) {
            $listing = rtcl()->factory->get_listing($listing_id);
        }

        if (is_a($listing, Listing::class)) {
            $this->object = $listing;

            $never_expires = get_post_meta($listing->get_id(), 'never_expires', true);
            $expiry_date = get_post_meta($listing->get_id(), 'expiry_date', true);
            $this->placeholders = wp_parse_args(array(
                '{expiration_date}' => !empty($never_expires) ? __('Never Expires', 'classified-listing') : date_i18n(get_option('date_format'), strtotime($expiry_date)),
                '{renewal_link}'    => Link::get_listing_promote_page_link($listing->get_id()),
                '{listing_title}'   => $listing->get_the_title()
            ), $this->placeholders);
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
                'email'   => $this
            )
        );
    }

}