<?php

namespace Rtcl\Resources;


use Rtcl\Helpers\Functions;

class PricingOptions
{

    static function rtcl_pricing_option($post) {
        $description = get_post_meta($post->ID, "description", true);
        $price = esc_attr(get_post_meta($post->ID, "price", true));
        $visible = get_post_meta($post->ID, "visible", true);

        wp_nonce_field(rtcl()->nonceText, rtcl()->nonceId);

        $promotion_html = '';
        $promotions = Options::get_listing_promotions();
        foreach ($promotions as $promo_id => $promotion) {
            $promo_value = get_post_meta($post->ID, $promo_id, true) ? 1 : 0;
            $promotion_html .= sprintf('<div class="form-check">
                                    <input class="form-check-input" name="%1$s" type="checkbox"
                                           value="1" %2$s id="allowed_featured_%1$s">
                                    <label class="form-check-label" for="allowed_featured_%1$s">%3$s</label>
                                </div>', esc_attr($promo_id), checked($promo_value, 1, false), $promotion);
        }

        $data = array(
            'price'       => sprintf('<div class="row form-group">
                                            <label class="col-2 col-form-label"
                                                   for="rtcl-pricing-price">%s</label>
                                            <div class="col-10">
                                                <input 
                                                type="text" 
                                                id="rtcl-pricing-price" 
                                                name="price" 
                                                value="%s" 
                                                class="form-control"
                                                       required>
                                            </div>
                                        </div>',
                sprintf('%s [%s]', __("Price", 'classified-listing'),
                    Functions::get_currency_symbol(Functions::get_order_currency())),
                $price
            ),
            'visible'     => sprintf('<div class="row form-group">
                                            <label class="col-2 col-form-label" for="visible">%s</label>
                                            <div class="col-10">
                                                <input type="number" step="1" id="visible" name="visible" value="%s"
                                                       class="form-control" required>
                                                <span class="description">%s</span>
                                            </div>
                                        </div>',
                __("Validate until", "classified-listing"),
                esc_attr($visible),
                __("Number of days the pricing will be validate.", "classified-listing")
            ),
            'allowed'     => sprintf('<div class="row form-group">
                            <label class="col-2 col-form-label"
                                   for="pricing-featured">%s</label>
                            <div class="col-10">%s</div>
                        </div>',
                __("Allowed", 'classified-listing'),
                $promotion_html
            ),
            'description' => sprintf('<div class="row form-group">
                                                <label class="col-2 col-form-label" for="pricing-description">%s</label>
                                                <div class="col-10">
                                                    <textarea rows="5" id="pricing-description" class="form-control"
                                                              name="description">%s</textarea>
                                                              <div class="description">%s</div>
                                                </div>
                                            </div>',
                __("Description", 'classified-listing'),
                $description,
                __("HTML is allowed :)", 'classified-listing')
            )
        );

        $data = apply_filters('rtcl_pricing_admin_options', $data, $post);

        echo implode('', $data);
    }

}
