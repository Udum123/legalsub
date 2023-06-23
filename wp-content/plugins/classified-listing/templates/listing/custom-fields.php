<?php
/**
 * @author        RadiusTheme
 * @package       classified-listing/templates/listing
 * @version       1.0.0
 *
 * @var array $fields
 * @var int $listing_id
 */

use Rtcl\Helpers\Functions;
use Rtcl\Models\RtclCFGField;

if ( count( $fields ) ) :
	ob_start();
	foreach ( $fields as $field )  :
		$field = new RtclCFGField( $field->ID );
		$value = $field->getFormattedCustomFieldValue( $listing_id );

		if ( ! empty( $value ) ) : ?>
            <li class="list-group-item rtcl-field-<?php echo esc_attr( $field->getType() ) ?>">
				<?php if ( $field->getType() === 'url' ):
					$nofollow = ! empty( $field->getNofollow() ) ? ' rel="nofollow"' : ''; ?>
                    <a href="<?php echo esc_url( $value ); ?>" target="<?php echo esc_attr( $field->getTarget() ) ?>"<?php echo esc_html( $nofollow ) ?>><?php echo esc_html( $field->getLabel() ) ?></a>
				<?php else : ?>
                    <span class="cfp-label"><span><?php echo esc_html( $field->getLabel() ) ?></span> : </span>
                    <span class="text-muted cfp-value"> <?php Functions::print_html( $value ); ?> </span>
				<?php endif; ?>
            </li>
		<?php endif; ?>
	<?php endforeach;
	$fieldData = ob_get_clean();
	?>
	<?php
	if ( $fieldData ) :
		printf( '<ul class="list-group list-group-flush mb-3 custom-field-properties">%s</ul>', $fieldData );
	endif; ?>
<?php endif;
