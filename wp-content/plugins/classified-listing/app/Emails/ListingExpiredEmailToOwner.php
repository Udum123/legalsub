<?php


namespace Rtcl\Emails;


use Rtcl\Helpers\Link;
use Rtcl\Models\Listing;
use Rtcl\Models\RtclEmail;
use Rtcl\Helpers\Functions;

class ListingExpiredEmailToOwner extends RtclEmail
{

    function __construct() {
        $this->db = true;
        $this->id = 'expired';
        $this->template_html = 'emails/listing-expired-email-to-owner';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_title}] {listing_title} - Expiration notice', 'classified-listing');
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
     * @return void
     * @throws \Exception
     */
    public function trigger($listing_id, $listing = false) {

        $this->setup_locale();

        if ($listing_id && !is_a($listing, Listing::class)) {
            $listing = new Listing($listing_id);
        }

        if (is_a($listing, Listing::class)) {
            $this->object = $listing;

            $never_expires = get_post_meta($listing->get_id(), 'never_expires', true);
            $expiry_date = get_post_meta($listing->get_id(), 'expiry_date', true);
            $this->placeholders = wp_parse_args(array(
                '{expiration_date}' => !empty($never_expires) ? esc_html__('Never Expires', 'classified-listing') : date_i18n(get_option('date_format'), strtotime($expiry_date)),
                '{renewal_link}'    => Link::get_listing_promote_page_link($listing->get_id()),
                '{listing_title}'   => $listing->get_the_title()
            ), $this->placeholders);
            $this->set_recipient($listing->get_email() ?: $listing->get_owner_email());
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
                'email'   => $this
            )
        );
    }

}