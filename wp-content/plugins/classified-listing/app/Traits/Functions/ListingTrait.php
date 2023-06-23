<?php


namespace Rtcl\Traits\Functions;


use Rtcl\Helpers\Functions;
use Rtcl\Models\Listing;
use Rtcl\Resources\Options;

trait ListingTrait
{

    static function get_title_character_limit() {
        return apply_filters('rtcl_listing_get_title_character_limit', absint(Functions::get_option_item('rtcl_moderation_settings', 'title_max_limit')));
    }

    static function get_description_character_limit() {
        return apply_filters('rtcl_listing_get_description_character_limit', absint(Functions::get_option_item('rtcl_moderation_settings', 'description_max_limit')));
    }


    static function is_gallery_slider_enabled() {
        $misc_settings = !Functions::get_option_item('rtcl_misc_settings', 'disable_gallery_slider', false, 'checkbox');
        return (bool)apply_filters('rtcl_single_listing_slider_enabled', $misc_settings);
    }


    /**
     * @param       $cat_id
     * @param Listing/null $listing
     *
     * @return mixed|void
     * @var Listing $listing
     */
    static function get_listing_form_price_unit_html($cat_id, $listing = null) {
        if (!$cat_id && !$listing) {
            return;
        }

        $price_unit = null;
        $price_units = array();
        if (is_a($listing, Listing::class)) {
            $price_units = $listing->get_price_units();
            $price_unit = $listing->get_price_unit();
        } else if ($cat_id) {
            $price_units = self::get_category_price_units($cat_id);
        }
        $price_unit_list = Options::get_price_unit_list();
        $html = Functions::get_template_html('listing-form/price-unit', compact('price_unit_list', 'price_units', 'price_unit', 'cat_id', 'listing'));

        return apply_filters('rtcl_get_listing_form_price_unit_html', $html, $cat_id, $listing);
    }

    /**
     * @param $cat_id
     *
     * @return array
     */
    static function get_category_price_units($cat_id) {
        $price_units = get_term_meta($cat_id, '_rtcl_price_units');
        if (empty($price_units) && $term = get_term($cat_id, rtcl()->category)) {
            if ($term->parent) {
                $price_units = get_term_meta($term->parent, '_rtcl_price_units');
            }
        }

        return $price_units;
    }


    /**
     * @param $cat_id
     *
     * @return boolean
     */
    static function category_has_price_units($cat_id) {

        return count(self::get_category_price_units($cat_id)) > 0;
    }


}