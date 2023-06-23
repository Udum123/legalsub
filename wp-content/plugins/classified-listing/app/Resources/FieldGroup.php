<?php

namespace Rtcl\Resources;

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclCFGField;

class FieldGroup
{

    static function rtcl_cfg_content($post) {
        $associate = get_post_meta($post->ID, 'associate', true);
        if (!in_array($associate, array('categories', 'all'))) {
            $associate = 'all';
        }
        ?>
        <div id="rtcl-custom-field-place" class="postbox">
            <div class="postbox-header">
                <h2 class="hndle"><?php _e("Where to include this Field Group", 'classified-listing') ?></h2>
                <div class="handle-actions hide-if-no-js">
                    <button type="button" class="handlediv" aria-expanded="true">
                        <span class="screen-reader-text"><?php _e('Listing meta field category','classified-listing') ?></span>
                        <span class="toggle-indicator" aria-hidden="false"><br></span>
                    </button>
                </div>
            </div>
            <div class="inside">
                <div class="rtcl-cfg-field-group">
                    <div class="rtcl-cfg-field-label"><?php _e("This Post Field Group is used with:",
                            'classified-listing') ?></div>
                    <div class="rtcl-cfg-field">
                        <label for="rtcl-cfg-all"><input
                                    type="radio" <?php echo $associate == 'all' ? ' checked' : ''; ?>
                                    name="associate" id="rtcl-cfg-all"
                                    value="all"> <?php _e("For all Categories",
                                "classified-listing") ?></label>
                        <label for="rtcl-cfg-categories"><input
                                    type="radio" <?php echo $associate == 'categories' ? ' checked' : ''; ?>
                                    name="associate"
                                    id="rtcl-cfg-categories" value="categories"> <?php _e("For Selected Categories",
                                "classified-listing") ?></label>
                    </div>
                </div>
            </div>
        </div>
        <div id="rtcl-cfg-wrapper">
            <a class="rtcl-cf-add-new button" data-dialog-title="<?php esc_attr_e("Add New Field", "classified-listing"); ?>"
               data-message-loading="<?php esc_attr_e("Please Wait, Loading…", "classified-listing"); ?>">
                <span class="dashicons dashicons-plus"></span> <?php _e("Add New Field", "classified-listing") ?>
            </a>
            <div id="rtcl-cfg">
                <?php
                $fields = Functions::get_all_cf_fields_by_cfg_id($post->ID);
                if (!empty($fields)) {
                    foreach ($fields as $field) {
                        $f = new RtclCFGField($field->ID);
                        echo $f->get_field_data();
                    }
                }
                ?>
            </div>
            <a class="rtcl-cf-add-new button" data-dialog-title="<?php esc_attr_e("Add New Field", "classified-listing"); ?>"
               data-message-loading="<?php esc_attr_e("Please Wait, Loading…", "classified-listing"); ?>">
                <span class="dashicons dashicons-plus"></span> <?php _e("Add New Field", "classified-listing") ?>
            </a>
        </div>
        <?php
    }

}