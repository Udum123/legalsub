<?php

/**
 * This template displays custom fields in the search form.
 *
 * @link    https://pluginsware.com
 * @since   1.0.0
 *
 * @package Advanced_Classifieds_And_Directory_Pro
 */

$cached_meta = array();
if ( isset( $_POST['cached_meta'] ) ) {
	parse_str( $_POST['cached_meta'], $cached_meta );
} 
?>

<?php 
if ( $acadp_query->have_posts() ) : 
	$i = 0;
	
	while ( $acadp_query->have_posts() ) : 
		$acadp_query->the_post(); 
		$field_meta = get_post_meta( $post->ID ); 
		?>
    
    	<?php if ( $i % 3 == 0 ) : ?>
  			<div class="row acadp-no-margin">
        <?php endif; ?>
        
    	<div class="form-group col-md-4">
    		<label><?php echo esc_html( get_the_title() ); ?></label>			
            
            <?php
			$value = '';

			if ( isset( $_GET['cf'][ $post->ID ] ) ) {
				$value = $_GET['cf'][ $post->ID ];
			}

			if ( isset( $cached_meta['cf'] ) && isset( $cached_meta['cf'][ $post->ID ] ) ) {
				$value = $cached_meta['cf'][ $post->ID ];
			}
					
			switch ( $field_meta['type'][0] ) {
				case 'text':	
					printf( '<input type="text" name="cf[%d]" class="form-control" placeholder="%s" value="%s"/>', $post->ID, esc_attr( $field_meta['placeholder'][0] ), esc_attr( $value ) );
					break;
				case 'textarea':
					printf( '<textarea name="cf[%d]" class="form-control" rows="%d" placeholder="%s">%s</textarea>', $post->ID, esc_attr( $field_meta['rows'][0] ), esc_attr( $field_meta['placeholder'][0] ), esc_textarea( $value ) );
					break;
				case 'select':
					$choices = $field_meta['choices'][0];
					$choices = explode( "\n", trim( $choices ) );
				
					printf ( '<select name="cf[%d]" class="form-control">', $post->ID );
					if ( ! empty( $field_meta['allow_null'][0] ) ) {
						printf( '<option value="">%s</option>', '- ' . esc_html__( 'Select an Option', 'advanced-classifieds-and-directory-pro' ) . ' -' );
					}

					foreach ( $choices as $choice ) {
						if ( strpos( $choice, ':' ) !== false ) {
							$_choice = explode( ':', $choice );
							$_choice = array_map( 'trim', $_choice );
							
							$_value  = $_choice[0];
							$_label  = $_choice[1];
						} else {
							$_value  = trim( $choice );
							$_label  = $_value;
						}
				
						$_selected = '';
						if ( trim( $value ) == $_value ) {
							$_selected = ' selected="selected"';
						}
			
						printf( '<option value="%s"%s>%s</option>', esc_attr( $_value ), $_selected, esc_html( $_label ) );
					} 
					echo '</select>';
					break;
				case 'checkbox':
					$choices = $field_meta['choices'][0];
					$choices = explode( "\n", trim( $choices ) );
				
					$values = array_map( 'trim', (array) $value );
				
					foreach ( $choices as $index => $choice ) {
						if ( strpos( $choice, ':' ) !== false ) {
							$_choice = explode( ':', $choice );
							$_choice = array_map( 'trim', $_choice );
							
							$_value  = $_choice[0];
							$_label  = $_choice[1];
						} else {
							$_value  = trim( $choice );
							$_label  = $_value;
						}
					
						$_checked = '';
						if ( in_array( $_value, $values ) ) {
							$_checked = ' checked="checked"';
						}
						
						printf( '<div class="checkbox"><label><input type="checkbox" name="cf[%d][%d]" value="%s"%s>%s</label></div>', $post->ID, $index, esc_attr( $_value ), $_checked, esc_html( $_label ) );
					}
					break;
				case 'radio':
					$choices = $field_meta['choices'][0];
					$choices = explode( "\n", trim( $choices ) );
				
					foreach ( $choices as $choice ) {
						if ( strpos( $choice, ':' ) !== false ) {
							$_choice = explode( ':', $choice );
							$_choice = array_map( 'trim', $_choice );
							
							$_value  = $_choice[0];
							$_label  = $_choice[1];
						} else {
							$_value  = trim( $choice );
							$_label  = $_value;
						}
					
						$_checked = '';
						if ( trim( $value ) == $_value ) {
							$_checked = ' checked="checked"';
						}
					
						printf( '<div class="radio"><label><input type="radio" name="cf[%d]" value="%s"%s>%s</label></div>', $post->ID, esc_attr( $_value ), $_checked, esc_html( $_label ) );
					}
					break;
				case 'number':	
					$attrs = array();

					if ( isset( $field_meta['min'] ) && '' != $field_meta['min'][0] ) {
						$attrs[] = 'min="' . (int) $field_meta['min'][0] . '"';
					}

					if ( isset( $field_meta['max'] ) && '' != $field_meta['max'][0] ) {
						$attrs[] = 'max="' . (int) $field_meta['max'][0] . '"';
					}

					printf( '<input type="number" name="cf[%d]" class="form-control" placeholder="%s" value="%s" %s/>', $post->ID, esc_attr( $field_meta['placeholder'][0] ), esc_attr( $value ), implode( ' ', $attrs ) );
					break;
				case 'range':
					$values = array_map( 'sanitize_text_field', (array) $value );
					if ( 1 == count( $values ) ) {
						$values[1] = '';
					}

					$attrs = array();

					if ( isset( $field_meta['min'] ) && '' != $field_meta['min'][0] ) {
						$attrs[] = 'min="' . (int) $field_meta['min'][0] . '"';
					}

					if ( isset( $field_meta['max'] ) && '' != $field_meta['max'][0] ) {
						$attrs[] = 'max="' . (int) $field_meta['max'][0] . '"';
					}

					echo '<div class="row">';
					echo '<div class="col-md-6 col-xs-6">';
					printf( '<input type="number" name="cf[%d][0]" class="form-control" placeholder="%s" value="%s" %s/>', $post->ID, esc_attr__( 'min', 'advanced-classifieds-and-directory-pro' ), esc_attr( $values[0] ), implode( ' ', $attrs ) );
					echo '</div>';
					echo '<div class="col-md-6 col-xs-6">';
					printf( '<input type="number" name="cf[%d][1]" class="form-control" placeholder="%s" value="%s" %s/>', $post->ID, esc_attr__( 'max', 'advanced-classifieds-and-directory-pro' ), esc_attr( $values[1] ), implode( ' ', $attrs ) );
					echo '</div>';
					echo '</div>';
					break;
				case 'date':
				case 'datetime':	
					$classes = array( 'form-control', 'acadp-date-picker' );

					$type_search = ! empty( $field_meta['type_search'][0] ) ? sanitize_text_field( $field_meta['type_search'][0] ) : '';
					if ( 'daterange' == $type_search ) {
						$classes[] = 'acadp-has-daterange';
					}

					printf( '<input type="text" name="cf[%d]" class="%s" placeholder="%s" value="%s" autocomplete="off" />', $post->ID, implode( ' ', $classes ), esc_attr( $field_meta['placeholder'][0] ), esc_attr( $value ) );
					break;
				case 'url':	
					printf( '<input type="text" name="cf[%d]" class="form-control" placeholder="%s" value="%s"/>', $post->ID, esc_attr( $field_meta['placeholder'][0] ), esc_url( $value ) );
					break;
			}
			?>

			<?php if ( ! empty( $field_meta['instructions'][0] ) ) : ?>
        		<small class="help-block"><?php echo esc_html( $field_meta['instructions'][0] ); ?></small>
        	<?php endif; ?> 
    	</div>
        
        <?php 
		$i++;
		
		if ( $i % 3 == 0 || $i == $acadp_query->post_count ) : ?>
  			</div>
		<?php 
		endif;
        
	endwhile;
endif;