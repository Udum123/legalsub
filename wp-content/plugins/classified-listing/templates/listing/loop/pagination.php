<?php
/**
 * Pagination - Show numbered pagination for catalog pages
 *
 * This template can be overridden by copying it to yourtheme/classified-listing/loop/pagination.php.
 *
 *
 * @package classified-listing/Templates
 * @version 1.3
 */

use Rtcl\Helpers\Functions;

if (!defined('ABSPATH')) {
    exit;
}

$total = isset($total) ? $total : Functions::get_loop_prop('total_pages');
$current = isset($current) ? $current : Functions::get_loop_prop('current_page');
$base = isset($base) ? $base : esc_url_raw(str_replace(999999999, '%#%', get_pagenum_link(999999999, false)));
$format = isset($format) ? $format : '';

if ($total <= 1) {
    return;
}
?>
<nav class="rtcl-pagination">
    <?php
    echo paginate_links(apply_filters('rtcl_pagination_args', array( // WPCS: XSS ok.
        'base'      => $base,
        'format'    => $format,
        'add_args'  => false,
        'current'   => max(1, $current),
        'total'     => $total,
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
        'type'      => 'list',
        'end_size'  => 3,
        'mid_size'  => 3,
    )));
    ?>
</nav>
