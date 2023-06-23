<?php

/**
 * This template displays the listing form.
 *
 * @link    https://pluginsware.com
 * @since   1.0.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */

if ( $post_id > 0 ) {
	$email = '';

	if ( isset( $post_meta['email'] ) ) {
		$email = $post_meta['email'][0];
	}
} else {
	$current_user = wp_get_current_user();
	$email = $current_user->user_email;
}

$required_fields = array();
if ( isset( $general_settings['required_fields'] ) ) {
	$required_fields = $general_settings['required_fields'];
}
?>

<div class="acadp acadp-user acadp-post-form">
	<form action="<?php echo esc_url( acadp_get_listing_form_page_link() ); ?>" method="post" id="acadp-post-form" class="form-vertical" role="form">
		<?php acadp_status_messages(); ?>

        <div id="acadp-post-errors" class="alert alert-danger" role="alert" style="display: none;">
            <?php esc_html_e( 'Please fill in all required fields.', 'advanced-classifieds-and-directory-pro' ); ?>
        </div>
        
    	<!-- Choose category -->
    	<div class="panel panel-default">
        	<div class="panel-heading"><?php esc_html_e( 'Choose category', 'advanced-classifieds-and-directory-pro' ); ?></div>
            
            <div class="panel-body">
            	<div class="form-group">
					<label class="col-md-3 control-label" for="acadp_category"><?php esc_html_e( 'Category', 'advanced-classifieds-and-directory-pro' ); ?><span class="acadp-star">*</span></label>
                	<div class="col-md-6">
						<?php
						$args = array(
							'show_option_none'  => '-- ' . esc_html__( 'Select category', 'advanced-classifieds-and-directory-pro' ) . ' --',
							'option_none_value' => '',
							'taxonomy'          => 'acadp_categories',
							'name' 			    => 'acadp_category',
							'class'             => 'form-control acadp-category-listing',
							'required'          => true,
							'orderby'           => sanitize_text_field( $categories_settings['orderby'] ), 
							'order'             => sanitize_text_field( $categories_settings['order'] ),
							'selected'          => (int) $category
						);
						
						if ( $disable_parent_categories ) {
							$args['walker'] = new ACADP_Walker_CategoryDropdown;
						}
            
                        echo apply_filters( 'acadp_listing_form_categories_dropdown', acadp_dropdown_terms( $args, false ), $post_id );
                        ?>
            		</div>
            	</div>
        	</div>
    	</div>
        
        <!-- Listing details -->
        <div class="panel panel-default">
        	<div class="panel-heading"><?php esc_html_e( 'Listing details', 'advanced-classifieds-and-directory-pro' ); ?></div>
        
        	<div class="panel-body">
            	<div class="form-group">
      				<label class="control-label" for="acadp-title"><?php esc_html_e( 'Title', 'advanced-classifieds-and-directory-pro' ); ?><span class="acadp-star">*</span></label>
      				<input type="text" name="title" id="acadp-title" class="form-control" value="<?php if ( $post_id > 0 ) echo esc_attr( $post->post_title ); ?>" required />
    			</div>
                
                <div id="acadp-custom-fields-listings" data-post_id="<?php echo esc_attr( $post_id ); ?>">
  	  				<?php do_action( 'wp_ajax_acadp_public_custom_fields_listings', $post_id ); ?>
				</div>
                
                <div class="form-group">
            		<label class="control-label" for="description"><?php esc_html_e( 'Description', 'advanced-classifieds-and-directory-pro' ); ?></label>
      				<?php
						$post_content = ( $post_id > 0 ) ? $post->post_content : '';
						
						if ( is_admin() ) { // Fix for Gutenberg
							$editor = 'textarea';
						}

						if ( 'textarea' == $editor ) {
							printf( '<textarea name="%s" class="form-control" rows="8">%s</textarea>', 'description', esc_textarea( $post_content ) );
						} else {
							wp_editor(
								wp_kses_post( $post_content ),
								'description',
								array(
									'media_buttons' => false,
									'quicktags'     => true,
									'editor_height' => 200
								)
							);
						}
	  				?>
                </div>
            </div>
        </div>        
       
        <?php if ( $can_add_location ): ?>
        	 <!-- Contact details -->
        	<div id="acadp-contact-details" class="panel panel-default">
        		<div class="panel-heading"><?php esc_html_e( 'Contact details', 'advanced-classifieds-and-directory-pro' ); ?></div> 
            
            	<div class="panel-body">
                	<div class="row">
                		<div class="col-md-6">
                        	<div class="form-group">
                                <label class="control-label" for="acadp-address">
									<?php 
									esc_html_e( 'Address', 'advanced-classifieds-and-directory-pro' ); 
									if ( in_array( 'address', $required_fields ) ) echo '<span class="acadp-star">*</span>';
									?>
								</label>
                                <textarea name="address" id="acadp-address" class="form-control acadp-map-field" rows="3" <?php if ( in_array( 'address', $required_fields ) ) echo 'required'; ?>><?php if ( isset( $post_meta['address'] ) ) echo esc_textarea( $post_meta['address'][0] ); ?></textarea>
                            </div>
                            
                    		<div class="form-group">
        						<label class="control-label" for="acadp_location">
									<?php 
									esc_html_e( 'Location', 'advanced-classifieds-and-directory-pro' ); 
									if ( in_array( 'location', $required_fields ) ) echo '<span class="acadp-star">*</span>';
									?>
								</label>
        						<?php
								acadp_dropdown_terms(array(
									'show_option_none'  => '-- ' . esc_html__( 'Select location', 'advanced-classifieds-and-directory-pro' ) . ' --',
									'option_none_value' => in_array( 'location', $required_fields ) ? '' : (int) $general_settings['base_location'],
									'base_term'         => max( 0, (int) $general_settings['base_location'] ),
									'parent'            => max( 0, (int) $general_settings['base_location'] ),									
									'taxonomy'          => 'acadp_locations',
									'name'              => 'acadp_location',	
									'class'             => 'form-control acadp-map-field',
									'required'          => in_array( 'location', $required_fields ) ? true : false,	
									'orderby'           => sanitize_text_field( $locations_settings['orderby'] ), 
									'order'             => sanitize_text_field( $locations_settings['order'] ),				
									'selected'          => (int) $location
								));
	    						?>
      						</div>
                            
                            <div class="form-group">
        						<label class="control-label" for="acadp-zipcode">
									<?php 
									esc_html_e( 'Zip Code', 'advanced-classifieds-and-directory-pro' ); 
									if ( in_array( 'zipcode', $required_fields ) ) echo '<span class="acadp-star">*</span>';
									?>
								</label>
        						<input type="text" name="zipcode" id="acadp-zipcode" class="form-control acadp-map-field" value="<?php if ( isset( $post_meta['zipcode'] ) ) echo esc_attr( $post_meta['zipcode'][0] ); ?>" <?php if ( in_array( 'zipcode', $required_fields ) ) echo 'required'; ?>/>
                        	</div>
                   		</div>
                    
                    	<div class="col-md-6">
                    		<div class="form-group">
        						<label class="control-label" for="acadp-phone">
									<?php 
									esc_html_e( 'Phone', 'advanced-classifieds-and-directory-pro' ); 
									if ( in_array( 'phone', $required_fields ) ) echo '<span class="acadp-star">*</span>';
									?>
								</label>
        						<input type="text" name="phone" id="acadp-phone" class="form-control" value="<?php if ( isset( $post_meta['phone'] ) ) echo esc_attr( $post_meta['phone'][0] ); ?>" <?php if ( in_array( 'phone', $required_fields ) ) echo 'required'; ?>/>
                    		</div>
                            
                            <div class="form-group">
        						<label class="control-label" for="acadp-email">
									<?php 
									esc_html_e( 'Email', 'advanced-classifieds-and-directory-pro' ); 
									if ( in_array( 'email', $required_fields ) ) echo '<span class="acadp-star">*</span>';
									?>
								</label>
        						<input type="text" name="email" id="acadp-email" class="form-control" value="<?php echo esc_attr( $email ); ?>" <?php if ( in_array( 'email', $required_fields ) ) echo 'required'; ?>/>
                    		</div>
                            
                            <div class="form-group">
        						<label class="control-label" for="acadp-website">
									<?php 
									esc_html_e( 'Website', 'advanced-classifieds-and-directory-pro' ); 
									if ( in_array( 'website', $required_fields ) ) echo '<span class="acadp-star">*</span>';
									?>
								</label>
        						<input type="text" name="website" id="acadp-website" class="form-control" value="<?php if ( isset( $post_meta['website'] ) ) echo esc_attr( $post_meta['website'][0] ); ?>" <?php if ( in_array( 'website', $required_fields ) ) echo 'required'; ?>/>
                    		</div>
      					</div>
                	</div>
                
                	<?php if ( $has_map ) : ?>
                		<div class="acadp-map embed-responsive embed-responsive-16by9" data-type="form">
                			<?php
							$latitude  = isset( $post_meta['latitude'] ) ? esc_attr( $post_meta['latitude'][0] ) : 0;
							$longitude = isset( $post_meta['longitude'] ) ? esc_attr( $post_meta['longitude'][0] ) : 0;

							if ( empty( $latitude ) ) {
								$coordinates = acadp_get_location_coordinates( (int) $location );
					
								$latitude  = $coordinates['latitude']; 
								$longitude = $coordinates['longitude'];
							}
							?>
	    					<div class="marker" data-latitude="<?php echo $latitude; ?>" data-longitude="<?php echo $longitude; ?>"></div>    
	  					</div>
                		<input type="hidden" id="acadp-default-location" value="<?php echo esc_attr( $default_location ); ?>" />
            			<input type="hidden" id="acadp-latitude" name="latitude" value="<?php echo $latitude; ?>" />
	  					<input type="hidden" id="acadp-longitude" name="longitude" value="<?php echo $longitude; ?>" />
                
                		<div class="checkbox">
                			<label><input type="checkbox" name="hide_map" value="1" <?php if ( isset( $post_meta['hide_map'] ) ) checked( $post_meta['hide_map'][0], 1 ); ?>><?php esc_html_e( "Don't show the Map", 'advanced-classifieds-and-directory-pro' ); ?></label>
                		</div> 
                    <?php endif; ?>         
            	</div>
        	</div>
        <?php endif; ?>
        
        <?php if ( $can_add_images ) : ?>
        	<!-- Images -->
        	<div id="acadp-images-panel" class="panel panel-default">
        		<div class="panel-heading">
					<span>
						<?php 
						esc_html_e( 'Images', 'advanced-classifieds-and-directory-pro' );
						if ( in_array( 'image', $required_fields ) ) echo '<span class="acadp-star">*</span>';
						?>
					</span>
				</div>
            
            	<div class="panel-body">
                	<?php if ( $images_limit > 1 ) : ?>
                    	<p class="help-block">
							<span>
								<strong><?php esc_html_e( 'Note', 'advanced-classifieds-and-directory-pro' ); ?></strong>: 
								<?php 
								if ( in_array( 'image', $required_fields ) ) {
									printf( esc_html__( 'You can upload up to %d images. At least one image is required.', 'advanced-classifieds-and-directory-pro' ), $images_limit );
								} else {
									printf( esc_html__( 'You can upload up to %d images.', 'advanced-classifieds-and-directory-pro' ), $images_limit );
								}
								?>
							</span>
                        </p>
                    <?php endif; ?>
                    
            		<table class="acadp-images" id="acadp-images">
                		<tbody>
                    		<?php
							$disable_image_upload_attr = '';
						
							if ( isset( $post_meta['images'] ) ) {	
								$images = unserialize( $post_meta['images'][0] );		    
								foreach ( $images as $index => $image ) {	
									$image_attributes = wp_get_attachment_image_src( $images[ $index ] );

									if ( isset( $image_attributes[0] ) )  {			
										echo '<tr class="acadp-image-row">' . 
											'<td class="acadp-handle"><span class="glyphicon glyphicon-th-large"></span></td>' .         	
											'<td class="acadp-image">' . 
												'<img src="' . esc_url( $image_attributes[0] ) . '" alt="" />' . 
												'<input type="hidden" class="acadp-image-field" name="images[]" value="' . esc_attr( $images[ $index ] ) . '" />' . 
											'</td>' . 
											'<td>' .
												'<span class="acadp-image-url">' . esc_html( basename( $image_attributes[0] ) ) . '</span><br />' . 
												'<a href="javascript:void(0);" class="acadp-delete-image" data-attachment_id="' . esc_attr( $images[ $index ] ) . '">' . esc_html__( 'Delete Permanently', 'advanced-classifieds-and-directory-pro' ) . '</a>' . 
											'</td>' .              
										'</tr>';						
									}			
								}								
								
								if ( count( $images ) >= $images_limit ) {
									$disable_image_upload_attr = ' disabled';
								}		
							}
							?>
                    	</tbody>
                	</table>                
                	<div id="acadp-progress-image-upload"></div>
                	<a href="javascript:void(0);" class="btn btn-default" id="acadp-upload-image" data-limit="<?php echo esc_attr( $images_limit ); ?>"<?php echo $disable_image_upload_attr; ?>><?php esc_html_e( 'Upload Image', 'advanced-classifieds-and-directory-pro' ); ?></a>
            	</div>
        	</div>
        <?php endif; ?>        
        
        <?php if ( $can_add_video ) : ?>
        	<!-- Video -->
        	<div class="panel panel-default">
        		<div class="panel-heading">
					<span>
						<?php 
						esc_html_e( 'Video URL', 'advanced-classifieds-and-directory-pro' );
						if ( in_array( 'video', $required_fields ) ) echo '<span class="acadp-star">*</span>';
						?>
					</span>
				</div>
            
             	<div class="panel-body">
				 	<div class="form-group">
             			<input type="text" name="video" id="acadp-video" class="form-control" placeholder="<?php esc_attr_e( 'Only YouTube & Vimeo URLs', 'advanced-classifieds-and-directory-pro' ); ?>" value="<?php if ( isset( $post_meta['video'] ) ) echo esc_attr( $post_meta['video'][0] ); ?>" <?php if ( in_array( 'video', $required_fields ) ) echo 'required'; ?>/>
					</div>
				</div>
        	</div>
        <?php endif; ?>

        <?php if ( $can_add_price ) : ?>
        	<!-- Your price -->
        	<div class="panel panel-default">
        		<div class="panel-heading">
					<span>
						<?php 
						printf( '%s [%s]', esc_html__( "Your price", 'advanced-classifieds-and-directory-pro' ), acadp_get_currency() );
						if ( in_array( 'price', $required_fields ) ) echo '<span class="acadp-star">*</span>';
						?>
					</span>
				</div>
            
            	<div class="panel-body">
            		<div class="row">
            			<div class="col-md-6">
                			<div class="form-group">
                        		<label class="control-label" for="acadp-price"><?php esc_html_e( 'How much do you want it to be listed for?', 'advanced-classifieds-and-directory-pro' ); ?></label>
                				<input type="text" name="price" id="acadp-price" class="form-control" value="<?php if ( isset( $post_meta['price'] ) ) echo esc_attr( $post_meta['price'][0] ); ?>" <?php if ( in_array( 'price', $required_fields ) ) echo 'required'; ?>/>
                    		</div>
                		</div>
                
                		<div class="col-md-6">
                    		<p class="help-block"><?php esc_html_e( 'You can adjust your price anytime you like, even after your listing is published.', 'advanced-classifieds-and-directory-pro' ); ?></p>
                		</div>   
            		</div>
            	</div>
        	</div>
        <?php endif; ?>
        
        <!-- Hook for developers to add new fields -->
        <?php do_action( 'acadp_listing_form_fields' ); ?>
        
        <!-- Complete listing -->
        <div class="panel panel-default">
        	<div class="panel-heading"><?php esc_html_e( 'Complete listing', 'advanced-classifieds-and-directory-pro' ); ?></div>
            
            <div class="panel-body">
				<?php if ( $mark_as_sold ) : ?>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="sold" value="1" <?php if ( isset( $post_meta['sold'] ) ) checked( $post_meta['sold'][0], 1 ); ?>>
							<?php esc_html_e( "Mark as", 'advanced-classifieds-and-directory-pro' ); ?>&nbsp;
							<strong><?php echo esc_html( $general_settings['sold_listing_label'] ); ?></strong>
						</label>
					</div>
				<?php endif; ?>

            	<?php echo the_acadp_terms_of_agreement(); ?>
				<?php echo the_acadp_privacy_policy(); ?>
                
                <?php if ( $post_id == 0 ) : ?>
                	<div id="acadp-listing-g-recaptcha"></div>
                    <div id="acadp-listing-g-recaptcha-message" class="help-block text-danger"></div>
				<?php endif; ?>
                
                <?php wp_nonce_field( 'acadp_save_listing', 'acadp_listing_nonce' ); ?>
                <input type="hidden" name="post_type" value="acadp_listings" />              
      			
                <?php if ( $has_draft ) : ?>
                	<input type="submit" name="action" class="btn btn-default acadp-listing-form-submit-btn" value="<?php esc_html_e( 'Save Draft', 'advanced-classifieds-and-directory-pro' ); ?>" />
                <?php endif; ?>
                
                <?php if ( $post_id > 0 ) : ?>
                	<input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>" />  
                	<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="btn btn-default" target="_blank"><?php esc_html_e( 'Preview', 'advanced-classifieds-and-directory-pro' ); ?></a>
                <?php endif; ?>
                
                <?php if ( $has_draft ) { ?>
                	<input type="submit" name="action" class="btn btn-primary pull-right acadp-listing-form-submit-btn" value="<?php esc_html_e( 'Place Listing', 'advanced-classifieds-and-directory-pro' ); ?>" />
                <?php } else { ?>
                	<input type="submit" name="action" class="btn btn-primary pull-right acadp-listing-form-submit-btn" value="<?php esc_html_e( 'Save Changes', 'advanced-classifieds-and-directory-pro' ); ?>" />
                <?php } ?>
               	
                <div class="clearfix"></div>                
             </div>
        </div>
    </form>
    
    <form id="acadp-form-upload" class="hidden" method="post" action="#" enctype="multipart/form-data">
  		<input type="file" multiple name="acadp_image[]" id="acadp-upload-image-hidden" />
        <input type="hidden" name="action" value="acadp_public_image_upload" />
		<?php wp_nonce_field( 'acadp_upload_images', 'acadp_images_nonce' ); ?>
	</form>
</div>