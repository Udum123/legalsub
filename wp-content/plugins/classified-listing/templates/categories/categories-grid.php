<?php
/**
 * @author    RadiusTheme
 * @version       1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

?>

<div class="rtcl rtcl-categories rtcl-categories-grid<?php echo esc_attr($settings['equal_height'] ? ' rtcl-equal-height' : ''); ?>">
    <div class="row rtcl-no-margin">
        <?php
        $span = 'col-md-'.floor(12 / $settings['columns']);
        $i = 0;
        foreach ($terms as $term) {
            $count = 0;
            if (! empty($settings['hide_empty']) || ! empty($settings['show_count'])) {
                $count = Functions::get_listings_count_by_taxonomy(
                    $term->term_id,
                    rtcl()->category,
                    $settings['pad_counts']
                );

                if (! empty($settings['hide_empty']) && 0 == $count) {
                    continue;
                }
            }

            echo '<div class="cat-item-wrap equal-item '.$span.'">';
            echo '<div class="cat-details text-center">';
            echo "<div class='icon'>";
            $image_id = get_term_meta($term->term_id, '_rtcl_image', true);
            if ($image_id && $settings['image']) {
                $image_attributes = wp_get_attachment_image_src((int) $image_id, 'medium');
                $image = $image_attributes[0];

                if ('' !== $image) {
                    echo '<a href="'.get_term_link($term).'" class="rtcl-responsive-container" title="'.sprintf(__(
                        'View all posts in %s',
                        'classified-listing'
                    ), $term->name).'" '.'>';
                    echo '<img src="'.$image.'" class="rtcl-responsive-img" />';
                    echo '</a>';
                }
            }
            $icon_id = get_term_meta($term->term_id, '_rtcl_icon', true);
            if ($icon_id && $settings['icon']) {
                printf(
                    '<a href="%s" title="%s"><span class="rtcl-icon rtcl-icon-%s"></span></a>',
                    get_term_link($term),
                    sprintf(__('View all posts in %s', 'classified-listing'), esc_html($term->name)),
                    esc_attr($icon_id)
                );
            }
            echo '</div>';
            printf(
                "<h3><a href='%s' title='%s'>%s</a></h3>",
                get_term_link($term),
                sprintf(__('View all posts in %s', 'classified-listing'), esc_html($term->name)),
                esc_html($term->name)
            );

            if (! empty($settings['show_count'])) {
                printf("<div class='views'>(%d)</div>", absint($count));
            }
            if ($settings['description'] && $term->description) {
                printf('<p>%s</p>', esc_html($term->description));
            }
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>