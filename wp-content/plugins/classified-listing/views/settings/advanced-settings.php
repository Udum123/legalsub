<?php

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings for misc
 */
$options = array(
    'permalink_section'                 => array(
        'title'       => esc_html__('Permalink slugs', 'classified-listing'),
        'type'        => 'title',
        'description' => esc_html__('NOTE: Just make sure that, after updating the fields in this section, you flush the rewrite rules by visiting Settings > Permalinks. Otherwise you\'ll still see the old links.',
            'classified-listing'),
    ),
    'permalink'                         => array(
        'title'       => esc_html__('Listing base', 'classified-listing'),
        'type'        => 'text',
        'default'     => rtcl()->post_type,
        'description' => esc_html__('Listing base permalink. Default "rtcl_listing"', 'classified-listing'),
    ),
    'category_base'                     => array(
        'title'       => esc_html__('Listing category base', 'classified-listing'),
        'type'        => 'text',
        'default'     => _x('listing-category', 'slug', 'classified-listing'),
        'description' => esc_html__('Listing category base permalink.', 'classified-listing'),
    ),
    'location_base'                     => array(
        'title'       => esc_html__('Listing location base', 'classified-listing'),
        'type'        => 'text',
        'default'     => _x('listing-location', 'slug', 'classified-listing'),
        'description' => esc_html__('Listing location base permalink.', 'classified-listing'),
    ),
    'page_setup'                        => array(
        'title'       => esc_html__('Page setup', 'classified-listing'),
        'type'        => 'title',
        'description' => esc_html__('These pages need to be set so that listing endpoint.', 'classified-listing'),
    ),
    'listings'                          => array(
        'title'       => esc_html__('Listings page', 'classified-listing'),
        'options'     => Functions::get_pages(),
        'type'        => 'select',
        'description' => esc_html__('This is the page where all the active listings are displayed.',
            'classified-listing'),
        'class'       => 'rtcl-select2',
        'blank_text'  => esc_html__("Select a page", 'classified-listing'),
        'css'         => 'min-width:300px;',
    ),
    'listing_form'                      => array(
        'title'       => esc_html__('Listing form page', 'classified-listing'),
        'type'        => 'select',
        'class'       => 'rtcl-select2',
        'blank_text'  => esc_html__("Select a page", 'classified-listing'),
        'options'     => Functions::get_pages(),
        'css'         => 'min-width:300px;',
        'description' => esc_html__('This is the listing form page used to add or edit listing details. The [rtcl_listing_form] short code must be on this page.',
            'classified-listing')
    ),
    'myaccount'                         => array(
        'title'       => esc_html__('My account', 'classified-listing'),
        'type'        => 'select',
        'class'       => 'rtcl-select2',
        'blank_text'  => esc_html__("Select a page", 'classified-listing'),
        'options'     => Functions::get_pages(),
        'css'         => 'min-width:300px;',
        'description' => esc_html__('This is the page where the users can view/edit their account info. The [rtcl_my_account] short code must be on this page.',
            'classified-listing')
    ),
    'checkout'                          => array(
        'title'       => esc_html__('Checkout page', 'classified-listing'),
        'type'        => 'select',
        'class'       => 'rtcl-select2',
        'blank_text'  => esc_html__("Select a page", 'classified-listing'),
        'options'     => Functions::get_pages(),
        'description' => esc_html__('This is the checkout page where users will complete their purchases. The [rtcl_checkout] short code must be on this page.',
            'classified-listing'),
        'css'         => 'min-width:300px;',
    ),
    'account_endpoints'                 => array(
        'title'       => esc_html__('Account endpoints', 'classified-listing'),
        'type'        => 'title',
        'description' => esc_html__('Endpoints are appended to your page URLs to handle specific actions on the accounts pages. They should be unique and can be left blank to disable the endpoint.',
            'classified-listing'),
    ),
    'myaccount_listings_endpoint'       => array(
        'title'   => esc_html__('My Listings', 'classified-listing'),
        'type'    => 'text',
        'default' => 'listings'
    ),
    'myaccount_favourites_endpoint'     => array(
        'title'   => esc_html__('Favourites', 'classified-listing'),
        'type'    => 'text',
        'default' => 'favourites'
    ),
    'myaccount_edit_account_endpoint'   => array(
        'title'   => esc_html__('Edit Account', 'classified-listing'),
        'type'    => 'text',
        'default' => 'edit-account'
    ),
    'myaccount_payments_endpoint'       => array(
        'title'   => esc_html__('Payments', 'classified-listing'),
        'type'    => 'text',
        'default' => 'payments'
    ),
    'myaccount_lost_password_endpoint'  => array(
        'title'   => esc_html__('Lost Password', 'classified-listing'),
        'type'    => 'text',
        'default' => 'lost-password'
    ),
    'myaccount_logout_endpoint'         => array(
        'title'   => esc_html__('Logout', 'classified-listing'),
        'type'    => 'text',
        'default' => 'logout'
    ),
    'checkout_endpoints'                => array(
        'title'       => esc_html__('Checkout endpoints', 'classified-listing'),
        'type'        => 'title',
        'description' => esc_html__('Endpoints are appended to your page URLs to handle specific actions during the checkout process. They should be unique.',
            'classified-listing'),
    ),
    'checkout_submission_endpoint'      => array(
        'title'   => esc_html__('Submission', 'classified-listing'),
        'type'    => 'text',
        'default' => 'submission'
    ),
    'checkout_promote_endpoint'         => array(
        'title'   => esc_html__('Promote', 'classified-listing'),
        'type'    => 'text',
        'default' => 'promote'
    ),
    'checkout_payment_receipt_endpoint' => array(
        'title'   => esc_html__('Payment Receipt', 'classified-listing'),
        'type'    => 'text',
        'default' => 'payment-receipt'
    ),
    'checkout_payment_failure_endpoint' => array(
        'title'   => esc_html__('Payment Failure', 'classified-listing'),
        'type'    => 'text',
        'default' => 'payment-failure'
    ),

);

return apply_filters('rtcl_advanced_settings_options', $options);
