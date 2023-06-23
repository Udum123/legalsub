<?php

namespace Rtcl\Models;

class Factory
{

    /**
     * Get a Listing.
     *
     * @param bool $pricing_id
     *
     * @return Pricing|bool Listing object or null if the listing cannot be loaded.
     */
    public function get_pricing($pricing_id = false) {
        $pricing_id = $this->get_pricing_id($pricing_id);

        if (!$pricing_id) {
            return false;
        }

        try {
            return new Pricing($pricing_id);
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Get the pricing ID depending on what was passed.
     *
     * @param Pricing|\WP_Post|int|bool $pricing Pricing instance, post instance, numeric or false to use global $post.
     *
     * @return int|bool false on failure
     * @since  1.0.0
     */
    private function get_pricing_id($pricing) {
        global $post;

        if (false === $pricing && isset($post, $post->ID) && rtcl()->post_type_pricing === get_post_type($post->ID)) {
            return absint($post->ID);
        } elseif (is_numeric($pricing)) {
            return $pricing;
        } elseif ($pricing instanceof Pricing) {
            return $pricing->getId();
        } elseif (!empty($pricing->ID)) {
            return $pricing->ID;
        } else {
            return false;
        }
    }


    /**
     * Get a Listing.
     *
     * @param bool $listing_id
     *
     * @return Listing|bool Listing object or null if the listing cannot be loaded.
     */
    public function get_listing($listing_id = false) {
        $listing_id = $this->get_listing_id($listing_id);

        if (!$listing_id) {
            return false;
        }

        try {
            $listing = new Listing($listing_id); // TODO: need to add caching like membership
            if ($listing->exists()) {
                return $listing;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Get the listing ID depending on what was passed.
     *
     * @param Listing|\WP_Post|int|bool $listing Listing instance, post instance, numeric or false to use global $post.
     *
     * @return int|bool false on failure
     * @since  1.0.0
     */
    private function get_listing_id($listing) {
        global $post;

        if (false === $listing && isset($post, $post->ID) && rtcl()->post_type === get_post_type($post->ID)) {
            return absint($post->ID);
        } elseif (is_numeric($listing)) {
            return $listing;
        } elseif ($listing instanceof Listing) {
            return $listing->get_id();
        } elseif (!empty($listing->ID)) {
            return $listing->ID;
        } else {
            return false;
        }
    }

    /**
     * @param false $order
     *
     * @return Payment|bool Payment object or null if the listing cannot be loaded.
     */
    public function get_payment($order = false) {
        return $this->get_order($order);
    }

    /**
     * Get a Listing.
     *
     * @param bool|integer|Payment $order
     *
     * @return Payment|bool Payment object or null if the listing cannot be loaded.
     */
    public function get_order($order = false) {
        $oder_id = $this->get_order_id($order);

        if (!$oder_id) {
            return false;
        }
 
        try {
            $order = new Payment($oder_id); // TODO: need to add caching like membership
            if ($order->exists()) {
                return $order;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $order
     *
     * @return false|int|mixed|string
     */
    private function get_order_id($order) {
        global $post;

        if (false === $order && isset($post, $post->ID) && rtcl()->post_type_payment === get_post_type($post->ID)) {
            return absint($post->ID);
        } elseif (is_numeric($order)) {
            return $order;
        } elseif ($order instanceof Payment) {
            return $order->get_id();
        } elseif (!empty($order->ID)) {
            return $order->ID;
        } else {
            return false;
        }
    }


    /**
     * Get the listing ID depending on what was passed.
     *
     * @param RtclCFGField|\WP_Post|int|bool $custom_field
     *
     * @return int|bool false on failure
     * @since  1.0.0
     */
    private function get_cf_id($custom_field) {
        global $post;

        if (false === $custom_field && isset($post, $post->ID) && rtcl()->post_type_payment === get_post_type($post->ID)) {
            return absint($post->ID);
        } elseif (is_numeric($custom_field)) {
            return $custom_field;
        } elseif ($custom_field instanceof RtclCFGField) {
            return $custom_field->getFieldId();
        } elseif (!empty($custom_field->ID)) {
            return $custom_field->ID;
        } else {
            return false;
        }
    }


    /**
     * Get a Listing.
     *
     * @param bool|integer|Payment $field_id
     *
     * @return RtclCFGField|bool Custom Field object or null if the listing cannot be loaded.
     */
    public function get_custom_field($field_id = false) {
        $custom_field_id = $this->get_cf_id($field_id);

        if (!$custom_field_id) {
            return false;
        }

        try {
            $fields = new RtclCFGField($custom_field_id); // TODO: need to add caching like membership
            if ($fields->exists()) {
                return $fields;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

}
