<?php
/**
 * Show messages
 *
 * @author     techlabpro01
 * @package    classified-listing/Templates
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $messages ) {
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="rtcl-message alert alert-success" role="alert"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach; ?>
