<?php
/**
 * Main Elementor locationbox.
 *
 * Locationbox style.
 *
 * @package  Classifid-listing
 * @since 2.1.0
 */

use Rtcl\Helpers\Link;
use Rtcl\Helpers\Functions;
use \Elementor\Icons_Manager;
use RtclPro\Helpers\Fns;



$login_icon_title = is_user_logged_in() ? esc_html__( ' My Account', '' ) : esc_html__( ' Sign in', '' );

?>
<div class="rtcl-el-listing-header-action listing-header-action">
	<ul class="rtcl-el-header-btn">
		<?php

		if ( rtcl()->has_pro() && Fns::is_enable_compare() && $settings['rtcl_show_compare'] ) :
			if ( empty( rtcl()->session ) ) {
				rtcl()->initialize_session();
			}
			$compare_ids = rtcl()->session->get( 'rtcl_compare_ids', array() );
			if ( ! empty( $compare_ids ) || is_array( $compare_ids ) ) {
				$compare_ids = count( $compare_ids );
			}
			$compare_btn_order = 'order:' . $settings['rtcl_compare_icon_order'];
			?>
			<li class="rtcl-el-compare-btn rtcl-el-has-count-number" style="<?php echo esc_attr( $compare_btn_order ); ?>">
				<a class="rtcl-el-item-btn"
					data-toggle="rtcl-el-tooltip"
					data-placement="bottom"
					title="<?php echo esc_attr( 'Compare' ); ?>"
					href="<?php echo esc_url( Link::get_page_permalink( 'compare_page' ) ); ?>">
					<?php
						Icons_Manager::render_icon( $settings['compare_icon'], array( 'aria-hidden' => 'true' ) );
					?>
					<span class="count rtcl-el-compare-count"><?php echo esc_html( $compare_ids ); ?></span>
				</a>
			</li>
		<?php endif; ?>
		<?php
		if ( class_exists( 'rtcl' ) && Functions::is_enable_favourite() && $settings['rtcl_show_favourites'] ) :
			$favourite_posts = get_user_meta( get_current_user_id(), 'rtcl_favourites', true );
			if ( ! empty( $favourite_posts ) || is_array( $favourite_posts ) ) {
				$favourite_posts = count( $favourite_posts );
			}
			$favourite_posts = $favourite_posts ? $favourite_posts : '0';
			$fav_btn_order   = 'order:' . $settings['rtcl_favourites_icon_order'];
			?>
				<li class="rtcl-el-favourite rtcl-el-has-count-number" style="<?php echo esc_attr( $fav_btn_order ); ?>">
					<a class="item-btn"
						data-toggle="tooltip"
						data-placement="bottom"
						title="<?php esc_attr_e( 'Favourites', 'classified-listing' ); ?>"
						href="<?php echo esc_url( Link::get_my_account_page_link( 'favourites' ) ); ?>">
						<?php
							Icons_Manager::render_icon( $settings['favourites_icon'], array( 'aria-hidden' => 'true' ) );
						?>
						<span class="count rt-el-header-favourite-count"><?php echo esc_html( $favourite_posts ); ?></span>
					</a>
				</li>
		<?php endif; ?>
		<?php if ( rtcl()->has_pro() && Fns::is_enable_chat() && $settings['rtcl_show_chat_option'] ) : ?>
			<li class="rtcl-el-header-chat-icon" style="order:<?php echo absint( $settings['rtcl_show_chat_icon_order'] ); ?>">
				<a class="header-chat-icon" title="<?php esc_attr_e( 'Chat', 'classified-listing' ); ?>" href="<?php echo esc_url( Link::get_my_account_page_link( 'chat' ) ); ?>">
					<?php
						Icons_Manager::render_icon( $settings['sec_chat_option_icon'], array( 'aria-hidden' => 'true' ) );
					?>
				</a>
			</li>
		<?php endif; ?>

		<?php if ( $settings['rtcl_show_sec_sign_in'] ) { ?>
		<li class="rtcl-el-login-btn" style="order:<?php echo absint( $settings['rtcl_sign_in_icon_order'] ); ?>">
			<a class="rtcl-el-item-btn"
			data-toggle="tooltip"
			data-placement="bottom"
			title="<?php echo esc_attr( $login_icon_title ); ?>"
			href="<?php echo esc_url( Link::get_my_account_page_link() ); ?>">
				<?php
					Icons_Manager::render_icon( $settings['sign_in_icon'], array( 'aria-hidden' => 'true' ) );
				?>
			</a>
		</li>
		<?php } ?>
		<?php if ( $settings['rtcl_show_add_listing_button'] ) { ?>
		<li class="rtcl-el-add-listing-btn" style="order:<?php echo absint( $settings['rtcl_add_listing_button_order'] ); ?>">
			<a href="<?php echo esc_url( Link::get_listing_form_page_link() ); ?>" class="rtcl-el-item-btn">
				<span>
					<?php
						Icons_Manager::render_icon( $settings['add_listing_icon'], array( 'aria-hidden' => 'true' ) );
					?>
				</span> 
				<?php if ( $settings['add_listing_button_text'] ) { ?>
					<div class="rtcl-el-btn-text"> <?php echo esc_html( $settings['add_listing_button_text'] ); ?> </div>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		
	</ul>
</div>
