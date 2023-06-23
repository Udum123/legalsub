<?php
/**
 * Show messages
 *
 * @author     techlabpro01
 * @package    classified-listing/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ) {
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="rtcl-info alert alert-info"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach; ?>
