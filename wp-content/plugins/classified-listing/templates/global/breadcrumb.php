<?php
/**
 * Listings breadcrumb
 *
 * @package     ClassifiedListing/Templates
 * @version     1.5.4
 *
 * @var string $wrap_before
 * @var string $before
 * @var string $after
 * @var string $delimiter
 * @var string $wrap_after
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!empty($breadcrumb)) {

    echo $wrap_before;

    foreach ($breadcrumb as $key => $crumb) {

        echo $before;

        if (!empty($crumb[1]) && sizeof($breadcrumb) !== $key + 1) {
            echo '<a href="' . esc_url($crumb[1]) . '">' . esc_html($crumb[0]) . '</a>';
        } else {
            echo esc_html($crumb[0]);
        }

        echo $after;

        if (sizeof($breadcrumb) !== $key + 1) {
            echo $delimiter;
        }
    }

    echo $wrap_after;

}
