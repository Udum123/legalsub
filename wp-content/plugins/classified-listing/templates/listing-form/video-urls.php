<?php
/**
 * Video urls
 *
 * @author        RadiusTheme
 * @package       classified-listing/templates
 * @version       1.0.0
 *
 * @var string $post_id
 * @var array $video_urls
 */
?>
<div class="rtcl-post-video-urls rtcl-post-section<?php echo esc_attr( is_admin() ? " rtcl-is-admin" : '' ) ?>">
    <div class="rtcl-post-section-title">
        <h3><i class="rtcl-icon rtcl-icon-link"></i><?php esc_html_e( "Video URL", "classified-listing" ); ?></h3>
    </div>
    <div class="form-group">
        <input type="url"
               class="form-control"
               value="<?php echo esc_url( isset( $video_urls[0] ) ? $video_urls[0] : '' ) ?>"
               id="video-urls"
               data-rule-pattern="(https?:\/\/)(www.)?(youtube.com\/watch[?]v=([a-zA-Z0-9_-]{11}))|https?:\/\/(www.)?vimeo.com\/([0-9]{8,9})"
               data-msg-pattern="<?php esc_attr_e( "Given url is not a valid YouTube or Vimeo URL", "classified-listing" ); ?>"
               placeholder="<?php esc_attr_e( "Only YouTube & Vimeo URL", "classified-listing" ); ?>"
               name="_rtcl_video_urls[]">
        <small class="form-text">E.g. https://www.youtube.com/watch?v=RiXdDGk_XCU, https://vimeo.com/620922414</small>
    </div>
</div>
