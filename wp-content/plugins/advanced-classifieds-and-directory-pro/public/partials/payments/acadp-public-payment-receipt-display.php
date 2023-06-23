<?php

/**
 * This template displays the payment receipt.
 *
 * @link    https://pluginsware.com
 * @since   1.0.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */
?>

<div class="acadp acadp-user acadp-payment-receipt">
	<p><?php esc_html_e( 'Thank you for your order!', 'advanced-classifieds-and-directory-pro' ); ?></p>
    
    <?php
	if ( isset( $post_meta['payment_gateway'] ) && 'offline' == $post_meta['payment_gateway'][0] && 'created' == $post_meta['payment_status'][0] ) {
		the_acadp_offline_payment_instructions();
	}
	?>
    
    <div class="row">
    	<div class="col-md-6">
   			<table class="table table-bordered">
    			<tr>
    				<td><?php esc_html_e( 'ORDER', 'advanced-classifieds-and-directory-pro' ); ?> #</td>
            		<td><?php echo esc_html( $order->ID ); ?></td>
        		</tr>
        
        		<tr>
    				<td><?php esc_html_e( 'Total Amount', 'advanced-classifieds-and-directory-pro' ); ?></td>
            		<td>
						<?php
						if( isset( $post_meta['amount'] ) ) {
							$amount = acadp_format_payment_amount( $post_meta['amount'][0] );
							$amount = acadp_payment_currency_filter( $amount );
							
							echo esc_html( $amount );
						}
						?>
                     </td>
        		</tr>
                
        		<tr>
    				<td><?php esc_html_e( 'Date', 'advanced-classifieds-and-directory-pro' ); ?></td>
            		<td>
                    	<?php
						$date = strtotime( $order->post_date );
						echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $date );
						?>
					</td>
        		</tr>
    		</table>
		</div>
    	
        <div class="col-md-6">
			<table class="table table-bordered">
        		<tr>
    				<td><?php esc_html_e( 'Payment Method', 'advanced-classifieds-and-directory-pro' ); ?></td>
            		<td>
                    	<?php 
						$gateway = esc_html( $post_meta['payment_gateway'][0] );
						if ( 'free' == $gateway ) {
							esc_html_e( 'Free Submission', 'advanced-classifieds-and-directory-pro' );
						} else {
							$gateway_settings = get_option( 'acadp_gateway_' . $gateway . '_settings' );				
							echo ! empty( $gateway_settings['label'] ) ? esc_html( $gateway_settings['label'] ) : $gateway;
						}
						?>
                    </td>
        		</tr>
                
                <tr>
    				<td><?php esc_html_e( 'Payment Status', 'advanced-classifieds-and-directory-pro' ); ?></td>
            		<td>
						<?php 
						$status = isset( $post_meta['payment_status'] ) ? $post_meta['payment_status'][0] : 'created';
						echo esc_html( acadp_get_payment_status_i18n( $status ) );
						?>
                  	</td>
        		</tr
                
                ><tr>
    				<td><?php esc_html_e( 'Transaction Key', 'advanced-classifieds-and-directory-pro' ); ?></td>
            		<td><?php echo isset( $post_meta['transaction_id'] ) ? esc_html( $post_meta['transaction_id'][0] ) : ''; ?></td>
        		</tr>
    		</table>
    	</div>
    </div>

    <h2><?php esc_html_e( 'ITEMS', 'advanced-classifieds-and-directory-pro' ); ?></h2>
    <table class="table table-bordered table-striped">
    	<tr>
        	<th><?php esc_html_e( 'NAME', 'advanced-classifieds-and-directory-pro' ); ?></th>
            <th><?php esc_html_e( 'PRICE', 'advanced-classifieds-and-directory-pro' ); ?></th>
        </tr>
        <?php foreach ( $order_details as $order_detail ) : ?>
       		<tr>
       			<td>
					<h3 class="acadp-no-margin"><?php echo esc_html( $order_detail['label'] ); ?></h3>
           			<?php if ( isset( $order_detail['description'] ) ) echo esc_html( $order_detail['description'] ); ?>
        		</td>
       			<td>
					<?php echo esc_html( acadp_format_payment_amount( $order_detail['price'] ) ); ?>
                </td>
       		</tr>
        <?php endforeach; ?>
        <tr>
        	<td class="text-right acadp-vertical-middle"><?php printf( esc_html__( 'Total amount [%s]', 'advanced-classifieds-and-directory-pro' ), acadp_get_payment_currency() ); ?></td>
            <td><?php echo esc_html( acadp_format_payment_amount( $post_meta['amount'][0] ) ); ?></td>
        </tr>
    </table>
    
    <a href="<?php echo esc_url( acadp_get_manage_listings_page_link() ); ?>" class="btn btn-success"><?php esc_html_e( 'View all my listings', 'advanced-classifieds-and-directory-pro' ); ?></a>
</div>