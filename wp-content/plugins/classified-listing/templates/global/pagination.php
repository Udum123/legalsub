<?php
/**
 * Pagination
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( 1 != $pages ) : ?>
    <nav aria-label="Page navigation" class="my-3" role="navigation">
        <ul class="pagination justify-content-center">
			<?php if ( $paged > 1 && $showItems < $pages ) : ?>
                <li class="page-item"><a class="page-link" href="<?php echo get_pagenum_link( $paged - 1 ) ?>"
                                         aria-label="Previous Page"><span aria-hidden="true">&laquo;</span>
                        <span class="sr-only"><?php esc_html__( "Previous", "classified-listing" ) ?></span></a></li>
			<?php endif; ?>

			<?php for ( $i = 1; $i <= $pages; $i ++ ): ?>
				<?php if ( $paged == $i ) : ?>
                    <li class="page-item active"><span class="page-link"><span
                                    class="sr-only"><?php esc_html_e( "Current Page", "classified-listing" ) ?></span><?php echo absint($i); ?></span>
                    </li>
				<?php else: ?>
                    <li class="page-item"><a class="page-link" href="<?php echo get_pagenum_link( $i ) ?>"><span
                                    class="sr-only"><?php esc_html_e("Page", "classified-listing") ?> </span><?php echo absint($i); ?></a></li>
				<?php endif; ?>
			<?php endfor; ?>

			<?php if ( $paged < $pages && $showItems < $pages ) : ?>
                <li class="page-item"><a class="page-link" href="<?php echo get_pagenum_link( $paged + 1 ) ?>"
                                         aria-label="Next Page"><span aria-hidden="true">&raquo;</span> <span
                                class="sr-only"><?php esc_html_e( "Next", "classified-listing" ) ?></span></a></li>
			<?php endif; ?>

        </ul>
    </nav>
<?php endif;