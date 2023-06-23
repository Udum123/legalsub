<?php
/**
 * Login Form Gallery
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var string $post_id
 */

use Rtcl\Resources\Gallery;

?>
<div class="rtcl-post-gallery rtcl-post-section">
    <div class="rtcl-post-section-title">
        <h3><i class="rtcl-icon rtcl-icon-users"></i><?php _e("Images", "classified-listing"); ?></h3>
    </div>
    <?php Gallery::rtcl_gallery_content(get_post($post_id), array('post_id_input' => '#_post_id')); ?>
</div>