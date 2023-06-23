<?php

namespace Rtcl\Controllers\Ajax;


use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclCFGField;
use Rtcl\Resources\Options;

class AjaxCFG
{
    public function __construct() {
        add_action('wp_ajax_rtcl_edit_field_choose', array($this, 'edit_field_choose'));
        add_action('wp_ajax_rtcl_edit_field_insert', array($this, 'edit_field_insert'));
        add_action('wp_ajax_rtcl_edit_field_delete', array($this, 'edit_field_delete'));
    }

    function edit_field_delete() {
        $data = null;
        $error = true;
        $post_id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        if (Functions::verify_nonce()) {
            if ($post_id && ($post = get_post($post_id)) && $post->post_type === rtcl()->post_type_cf) {
                $p = wp_delete_post($post_id, true);
                if ($p) {
                    delete_metadata('post', 0, '_field_' . $post_id, '', true);
                }
                $error = false;
                $data = $p;
                $msg = __("Success", "classified-listing");
            } else {
                $data = $_REQUEST;
                $msg = __("Field was not selected", "classified-listing");
            }
        } else {
            $msg = __("Session expired", "classified-listing");
        }
        wp_send_json(array(
            'data'  => $data,
            'error' => $error,
            'msg'   => $msg
        ));
    }

    function edit_field_choose() {
        $html = null;
        $fields = Options::get_custom_field_list();
        $html .= "<p>" . __("You can choose from the available fields:", "classified-listing") . "</p>";
        foreach ($fields as $type => $field) {
            $html .= "<span class='button rtcl-field-item rtcl-field-button-insert' data-type='{$type}'><i class='rtcl-icon rtcl-icon-{$field['symbol']}'></i>{$field['name']}</span>";
        }
        echo $html;
        die();
    }

    function edit_field_insert() {
        $data = null;
        $error = true;
        $type = !empty($_REQUEST['type']) && array_key_exists($_REQUEST['type'], Options::get_custom_field_list()) ? esc_attr($_REQUEST['type']) : 'text';
        $parent_id = !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        if (Functions::verify_nonce()) {
            if ($type && $parent_id) {
                $field_id = wp_insert_post(array(
                        'post_status' => 'draft',
                        'post_type'   => 'rtcl_cf',
                        'post_parent' => $parent_id
                    )
                );
                update_post_meta($field_id, '_type', $type);
                $field = new RtclCFGField($field_id);
                $data = $field->get_field_data();
                $error = false;
                $msg = __("Success", "classified-listing");
            } else {
                $data = $_REQUEST;
                $msg = __("Select a field type", "classified-listing");
            }
        } else {
            $msg = __("Session expired", "classified-listing");
        }
        wp_send_json(array(
            'data'  => $data,
            'error' => $error,
            'msg'   => $msg
        ));
        die();
    }
}