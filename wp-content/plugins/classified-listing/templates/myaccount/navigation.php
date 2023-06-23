<?php
/**
 *
 * @author 		RadiusTheme
 * @package 	classified-listing/templates
 * @version     1.0.0
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'rtcl_before_account_navigation' );
?>

<nav class="rtcl-MyAccount-navigation">
	<ul>
		<?php foreach ( Functions::get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo Functions::get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( Link::get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'rtcl_after_account_navigation' ); ?>
