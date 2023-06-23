<?php

namespace Rtcl\Controllers\Admin\Meta;


use Rtcl\Helpers\Functions;

class SavePricingMetaData
{

    public function __construct() {
        add_action('save_post', array($this, 'save_pricing_meta_data'), 10, 2);
    }


    /**
     * @param $post_id
     * @param $post
     *
     * @return mixed|void
     */
    function save_pricing_meta_data($post_id, $post) {

        if (!isset($_POST['post_type'])) {
            return $post_id;
        }

        if (rtcl()->post_type_pricing != $post->post_type) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        if (!Functions::verify_nonce()) {
            return $post_id;
        }

        // Price
        if (isset($_POST['price'])) {
            $price = Functions::format_decimal($_POST['price']);
            update_post_meta($post_id, 'price', $price);
        }
        if (isset($_POST['description'])) {
            $description = Functions::sanitize($_POST['description'], 'html_textarea');
            update_post_meta($post_id, 'description', $description);
        }
        if (isset($_POST['visible'])) {
            $visible = Functions::sanitize($_POST['visible']);
            update_post_meta($post_id, 'visible', absint($visible));
        }
        if (isset($_POST['featured'])) {
            update_post_meta($post_id, 'featured', 1);
        } else {
            delete_post_meta($post_id, 'featured');
        }

        do_action('rtcl_save_pricing_meta_data', $post_id, $_POST, $post);
    }
}
