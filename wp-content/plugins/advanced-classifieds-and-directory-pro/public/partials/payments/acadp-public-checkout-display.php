<?php

/**
 * This template displays the checkout page.
 *
 * @link    https://pluginsware.com
 * @since   1.0.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */
?>

<div class="acadp acadp-user acadp-checkout">
	<?php acadp_status_messages(); ?>

	<p><?php esc_html_e( 'Please review your order, and click Purchase once you are ready to proceed.', 'advanced-classifieds-and-directory-pro' ); ?></p>
    
    <form id="acadp-checkout-form" class="form-vertical" method="post" action="" role="form">
		<table id="acadp-checkout-form-data" class="table table-stripped table-bordered">
        	<?php foreach ( $options as $option ) : ?>            	
                <?php if ( 'header' == $option['type'] ) { ?>                
                	<tr>
                		<td colspan="3">
                    		<h3 class="acadp-no-margin"><?php echo esc_html( $option['label'] ); ?></h3>
                        	<?php if ( isset( $option['description'] ) ) echo esc_html( $option['description'] ); ?>
                    	</td>
                	</tr>                
            	<?php } else { ?>
                	<tr>
                		<td>
                    		<?php
							switch ( $option['type'] ) {
								case 'checkbox' :
									$checked = isset( $option['selected'] ) && 1 == $option['selected'] ? ' checked' : '';
									printf( '<input type="checkbox" name="%s[]" value="%s" class="acadp-checkout-fee-field" data-price="%s" %s/>', esc_attr( $option['name'] ), esc_attr( $option['value'] ), esc_attr( $option['price'] ), $checked );
									break;
								case 'radio' :
									$checked = isset( $option['selected'] ) && 1 == $option['selected'] ? ' checked' : '';
									printf( '<input type="radio" name="%s" value="%s" class="acadp-checkout-fee-field" data-price="%s" %s/>', esc_attr( $option['name'] ), esc_attr( $option['value'] ), esc_attr( $option['price'] ), $checked );
									break;
							}                    		
							?>
                    	</td>
						<td>
                        	<?php if ( isset( $option['label'] ) ) : ?>
								<h4 class="acadp-no-margin"><?php echo esc_html( $option['label'] ); ?></h4>
                            <?php endif; ?>
                    		<?php if ( isset( $option['description'] ) ) echo esc_html( $option['description'] ); ?>
                		</td>
        				<td align="right" class="text-right"><?php echo esc_html( acadp_format_payment_amount(  $option['price'] ) ); ?> </td>
        			</tr>
                <?php } ?>           	
            <?php endforeach; ?>    		
            <tr>
            	<td colspan="2" class="text-right acadp-vertical-middle">
                	<strong><?php printf( esc_html__( 'Payable amount [%s]', 'advanced-classifieds-and-directory-pro' ), acadp_get_payment_currency() ); ?></strong>
                </td>
                <td class="text-right acadp-vertical-middle"><div id="acadp-checkout-total-amount"></div></td>
            </tr>
    	</table>
        
        <div id="acadp-payment-gateways" class="panel panel-default">
        	<div class="panel-heading"><?php esc_html_e( 'Choose payment method', 'advanced-classifieds-and-directory-pro' ); ?></div>
            
            <?php the_acadp_payment_gateways(); ?>
        </div>
        
        <div id="acadp-cc-form"></div>
        
        <p id="acadp-checkout-errors" class="text-danger"></p>
        
        <?php wp_nonce_field( 'acadp_process_payment', 'acadp_checkout_nonce' ); ?>
        <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />
        <div class="pull-right">
        	<a href="<?php echo esc_url( acadp_get_manage_listings_page_link() ); ?>" class="btn btn-default"><?php esc_html_e( 'Not now', 'advanced-classifieds-and-directory-pro' ); ?></a>
        	<input type="submit" id="acadp-checkout-submit-btn" class="btn btn-primary" value="<?php esc_attr_e( 'Proceed to payment', 'advanced-classifieds-and-directory-pro' ); ?>" />
        </div>
    </form>
</div>