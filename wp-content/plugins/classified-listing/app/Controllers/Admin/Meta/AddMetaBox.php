<?php

namespace Rtcl\Controllers\Admin\Meta;

use Rtcl\Resources\FieldGroup;
use Rtcl\Resources\Gallery;
use Rtcl\Resources\ListingDetails;
use Rtcl\Resources\OrderOptions;
use Rtcl\Resources\PricingOptions;

class AddMetaBox
{

    function __construct() {
        add_action('add_meta_boxes', [$this, 'listing_details_meta_box']);
        add_action('edit_form_after_title', [$this, 'prevent_nested']);
        add_action('add_meta_boxes', [$this, 'pricing_meta_box']);
        add_action('add_meta_boxes', [$this, 'payment_meta_box']);

        add_filter('postbox_classes_' . rtcl()->post_type . '_rtcl_listing_details', [
            $this,
            'add_meta_box_classes'
        ]);
        add_filter('postbox_classes_' . rtcl()->post_type . '_rtcl_listing_contact_details', [
            $this,
            'add_meta_box_classes'
        ]);
        add_filter('postbox_classes_' . rtcl()->post_type . '_rtcl_gallery_video_urls', [
            $this,
            'add_meta_box_classes'
        ]);
        add_filter('postbox_classes_' . rtcl()->post_type . '_rtcl_gallery', [$this, 'add_meta_box_classes']);
        add_filter('postbox_classes_' . rtcl()->post_type_pricing . '_rtcl_pricing', [
            $this,
            'add_meta_box_classes'
        ]);
    }

    function add_meta_box_classes($classes = array()) {
        array_push($classes, sanitize_html_class('rtcl'));

        return $classes;
    }

    function listing_details_meta_box() {
        add_meta_box(
            'rtcl_listing_details',
            __('Listing Details', 'classified-listing'),
            [ListingDetails::class, 'listing_details'],
            rtcl()->post_type,
            'normal',
            'high'
        );
        add_meta_box(
            'rtcl_listing_contact_details',
            __('Contact Details', 'classified-listing'),
            [ListingDetails::class, 'contact_details'],
            rtcl()->post_type,
            'normal',
            'high'
        );
        add_meta_box(
            'rtcl_listing_moderation',
            __('Static Report', 'classified-listing'),
            [ListingDetails::class, 'static_report'],
            rtcl()->post_type,
            'side',
            'default'
        );
        add_meta_box(
            'rtcl_gallery',
            __('Gallery', 'classified-listing'),
            [Gallery::class, 'rtcl_gallery_content'],
            rtcl()->post_type,
            'normal',
            'high'
        );
        add_meta_box(
            'rtcl_gallery_video_urls',
            __('Video URL', 'classified-listing'),
            [ListingDetails::class, 'video_urls_box'],
            rtcl()->post_type,
            'normal',
            'high'
        );
        do_action('rtcl_listing_details_meta_box');
    }

    function prevent_nested($post) {
        if ($post->post_type == rtcl()->post_type_cfg) {
            FieldGroup::rtcl_cfg_content($post);
        }
    }

    function pricing_meta_box() {
        add_meta_box(
            'rtcl_pricing',
            __('Pricing Options', 'classified-listing'),
            array(PricingOptions::class, 'rtcl_pricing_option'),
            rtcl()->post_type_pricing,
            'normal',
            'high'
        );
    }

    function payment_meta_box() {
        add_meta_box(
            'rtcl-order-data',
            __('Oder Data', 'classified-listing'),
            array(OrderOptions::class, 'order_data'),
            rtcl()->post_type_payment,
            'normal',
            'high'
        );

        add_meta_box(
            'rtcl-order-items',
            __('Items', 'classified-listing'),
            array(OrderOptions::class, 'order_items'),
            rtcl()->post_type_payment,
            'normal',
            'high'
        );

        add_meta_box(
            'rtcl-order-actions',
            __('Order Actions', 'classified-listing'),
            array(OrderOptions::class, 'order_action'),
            rtcl()->post_type_payment,
            'side',
            'high'
        );
        add_meta_box(
            'rtcl-order-notes',
            __('Order notes', 'classified-listing'),
            array(OrderOptions::class, 'order_notes'),
            rtcl()->post_type_payment,
            'side'
        );
    }

}
