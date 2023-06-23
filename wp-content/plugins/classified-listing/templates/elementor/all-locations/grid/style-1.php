<?php
/**
 * Main Elementor locationbox.
 *
 * Locationbox style.
 *
 * @package  Classifid-listing
 * @since 1.0.0
 */

?>
<div class="rtcl el-all-locations grid-<?php echo esc_attr( $style ); ?> ">
	<div class="row">
		<?php
			$classes  = 'col-xl-' . $settings['rtcl_col_xl'];
			$classes .= ' col-lg-' . $settings['rtcl_col_lg'];
			$classes .= ' col-md-' . $settings['rtcl_col_md'];
			$classes .= ' col-sm-' . $settings['rtcl_col_sm'];
			$classes .= ' col-' . $settings['rtcl_col_mobile'];
		?>
			<?php
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $trm ) {
					$count_html = null;
					if ( $settings['display_count'] ) {
						ob_start();
						// $count_data = str_replace( '{COUNT}', $trm->count, $count );
						$count_data = sprintf( _n( '(%s Ad)', '(%s Ads)', $trm->count, 'classified-listing' ), $trm->count );

						?>
							<span class="rtcl-counter">
								<span><?php echo esc_html( $count_data ); ?></span>
							</span>
						<?php
						$count_html = ob_get_clean();
					}

					?>
					<div class="location-boxes-wrapper <?php echo esc_attr( $classes ); ?>">
						<div class="location-boxes">
							<div class="title-wrap">
								<h3 class="rtcl-title">
									<?php if ( $settings['enable_link'] ) { ?>
										<a href="<?php echo get_term_link( $trm ); ?>">
											<?php echo esc_html( $trm->name ); ?>
										</a>
										<?php
									} else {
										echo esc_html( $trm->name );
									}
									?>
								</h3>
							</div>
							<?php
								$arr = array(
									'span' => array(
										'class' => array(),
									),
								);
								echo wp_kses( $count_html, $arr );
								?>
							<?php if ( $settings['display_descriptiuon'] && ! empty( $trm->description ) ) { ?>
							<div class="rtcl-description">
								<?php
								if ( $settings['rtcl_content_limit'] ) {
									echo wp_trim_words( $trm->description, $settings['rtcl_content_limit'] );
								} else {
									echo wp_kses_post( $trm->description );
								}
								?>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php
				}
			}
			?>
	</div>
</div>
