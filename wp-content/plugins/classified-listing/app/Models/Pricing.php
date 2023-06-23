<?php

namespace Rtcl\Models;


use Rtcl\Resources\Options;

class Pricing
{

    protected $id;
    protected $price;
    protected $title;
    protected $description;
    protected $type;
    protected $visible;
    protected $featured;
    protected $top;
    protected $bump_up;

    function __construct($pricing_id) {
        $post = get_post($pricing_id);
        if (is_object($post) && $post->post_type == rtcl()->post_type_pricing) {
            $this->setData($post);
        } else {
            return false;
        }
    }


    /**
     * Course is exists if the post is not empty
     *
     * @return bool
     */
    public function exists() {
        return rtcl()->post_type_pricing === get_post_type($this->getId());
    }

    private function setData($post) {
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->price = get_post_meta($this->id, 'price', true);
        $this->description = get_post_meta(apply_filters('rtcl_i18_get_post_id', $this->id), 'description', true);
        $this->type = get_post_meta($this->id, 'pricing_type', true);
        $this->visible = absint(get_post_meta($this->id, 'visible', true));
        $this->featured = get_post_meta($this->id, 'featured', true);
        $this->top = get_post_meta($this->id, '_top', true);
        $this->bump_up = get_post_meta($this->id, '_bump_up', true);
    }

    /**
     * @return mixed
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @return int
     */
    public function get_regular_ads() {
        return absint(get_post_meta($this->id, 'regular_ads', true));
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
	    return apply_filters('rtcl_get_pricing_title', get_the_title(apply_filters( 'rtcl_i18_get_post_id', $this->id )));
    }

    /**
     * @return mixed
     */
    public function the_title() {
        echo apply_filters('rtcl_the_pricing_title', $this->getTitle());
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return apply_filters('rtcl_get_pricing_description', html_entity_decode($this->description));
    }

    /**
     * @return mixed
     */
    public function getType() {
        $types = Options::get_pricing_types();
        return in_array($this->type, array_keys($types)) ? $this->type : 'regular';
    }

    /**
     * @return integer
     */
    public function getVisible() {
        return $this->visible;
    }

    /**
     * @return string
     */
    public function getFeatures() {
        $featuresHTML = '';
        $features = array();
        $features = apply_filters('rtcl_get_pricing_features', $features, $this);
        if (!empty($features)) {
            $featuresHTML .= '<ul class="list-group list-group-flush">';
            foreach ($features as $featureId => $feature) {
                $featuresHTML .= "<li class='list-group-item rtcl-feature-$featureId'>$feature</li>";
            }
            $featuresHTML .= '</ul>';
        }

        return $featuresHTML;
    }

    /**
     * @return boolean
     * @deprecated
     * @use hasFeatured() function
     */
    public function getFeatured() {
        _deprecated_function(__METHOD__, '2.0.0', '$this->hasFeatured()');

        return $this->hasFeatured();
    }

    /**
     * @return boolean
     * @deprecated
     * @use hasTop feature
     */
    public function getTop() {
        _deprecated_function(__METHOD__, '2.0.0', '$this->hasTop()');

        return $this->hasTop();
    }

    public function getRegularPromotions() {
        $promotions = [];
        if ('regular' === $this->getType()) {
            $promotions = [];
            $promotion_list = Options::get_listing_promotions();
            foreach ($promotion_list as $promo_id => $promotion) {
                if ($this->hasPromotion($promo_id)) {
                    $promotions[$promo_id] = true;
                } else {
                    $promotions[$promo_id] = false;
                }
            }
        }

        return $promotions;
    }

    /**
     * @return boolean
     * @deprecated
     */
    public function isBumpUp() {
        _deprecated_function(__METHOD__, '2.0.0', '$this->hasBumpUp()');

        return $this->hasBumpUp();
    }

    public function hasBumpUp() {
        return $this->bump_up ? true : false;
    }

    public function hasTop() {
        return !empty($this->top);
    }


    public function hasFeatured() {
        return !empty($this->featured);
    }

    /**
     * @param $promo_id
     *
     * @return bool
     */
    public function hasPromotion($promo_id) {
        return $promo_id && get_post_meta($this->id, $promo_id, true);
    }

    /**
     * @return mixed|void
     */
    public function hasAnyPromotion() {
        $promotions = Options::get_listing_promotions();
        foreach ($promotions as $promo_id => $promotion) {
            if ($this->hasPromotion($promo_id)) {
                return apply_filters('rtcl_pricing_has_any_promotion', true, $promo_id, $promotions, $this);
            }
        }
        return apply_filters('rtcl_pricing_has_any_promotion', false, null, $promotions, $this);
    }

}