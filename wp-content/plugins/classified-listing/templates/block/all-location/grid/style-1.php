<?php

/**
 * Main Gutenberg locationbox.
 *
 * Locationbox style.
 *
 * @package  Classifid-listing
 * @since 1.0.0
 */

$wrap_class = '';
if (isset($settings['blockId'])) {
	$wrap_class .= 'rtcl-block-' . $settings['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if (isset($settings['className'])) {
	$wrap_class .= $settings['className'];
}
?>

<div class="<?php echo esc_attr($wrap_class); ?>">
	<div class="rtcl gb-all-locations grid-style-1">
		<div class="row">
			<?php
			$classes = 'col-xl-' . $settings['col_xl'];
			$classes .= ' col-lg-' . $settings['col_lg'];
			$classes .= ' col-md-' . $settings['col_md'];
			$classes .= ' col-sm-' . $settings['col_sm'];
			$classes .= ' col-' . $settings['col_mobile'];
			?>
			<?php
			if (!empty($terms)) {
				foreach ($terms as $trm) {


					$count_html = null;
					if ($settings['show_count'] && !empty($trm['count'])) {
						ob_start();
						$count_data = sprintf(_n('(%s Ad)', '(%s Ads)', $trm["count"], 'classified-listing'), $trm['count']); ?>
						<span class="rtcl-counter">
							<?php if (!empty($settings['count_after_text'])) { ?>
								<span><?php echo esc_html($trm['count']); ?></span>
								<span><?php echo esc_html($settings['count_after_text']); ?></span>
							<?php } else { ?>
								<?php echo esc_html($count_data); ?>
							<?php } ?>
						</span>
					<?php
						$count_html = ob_get_clean();
					} ?>

					<div class="location-boxes-wrapper <?php echo esc_attr($classes); ?>">

						<div class="location-boxes">
							<div class="title-wrap">
								<h3 class="rtcl-title">
									<?php if ($settings['enable_link']) { ?>
										<a <?php echo esc_attr(isset($settings['enable_nofollow']) && $settings['enable_nofollow'] == '1' ? 'rel=nofollow' : ''); ?> href="<?php echo esc_url($trm['permalink']); ?>">
											<?php echo esc_html($trm['name']); ?>
										</a>
									<?php
									} else {
										echo esc_html($trm['name']);
									} ?>
								</h3>
							</div>
							<?php
							$arr = array(
								'span' => array(
									'class' => array(),
								),
							);
							echo wp_kses($count_html, $arr); ?>
							<?php if ($settings['show_desc'] && !empty($trm['description'])) { ?>
								<div class="rtcl-description">
									<?php
									if ($settings['desc_limit']) {
										echo wp_trim_words($trm['description'], $settings['desc_limit']);
									} else {
										echo wp_kses_post($trm['description']);
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
</div>