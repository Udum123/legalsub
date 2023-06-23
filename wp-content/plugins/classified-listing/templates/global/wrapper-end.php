<?php
/**
 * Content wrappers
 *
 * @package     ClassifiedListing/Templates
 * @version     1.4.0
 */

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$template = Functions::get_theme_slug_for_templates();

switch ($template) {
    case 'twentyten' :
        echo '</div></div>';
        break;
    case 'twentyeleven' :
        echo '</div>';
        get_sidebar('shop');
        echo '</div>';
        break;
    case 'twentytwelve' :
        echo '</div></div>';
        break;
    case 'twentythirteen' :
        echo '</div></div>';
        break;
    case 'twentyfourteen' :
        echo '</div></div></div>';
        get_sidebar('content');
        break;
    case 'twentyfifteen' :
        echo '</div></div>';
        break;
    case 'twentysixteen' :
        echo '</main></div>';
        break;
    default :
        echo '</main></div>';
        break;
}
