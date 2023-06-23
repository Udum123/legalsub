<?php
$col_class = 'rtcl-grid-view';
$col_class .= ' columns-' . $settings['col_desktop'];
$col_class .= ' tab-columns-' . $settings['col_tablet'];
$col_class .= ' mobile-columns-' . $settings['col_mobile'];
$col_class .= ' rtcl-gb-cat-wrap';

$item_class = 'listing-item rtcl-gb-cat-box';
$item_class .= ' rtcl-gb-cat-box-' . $settings['col_style']['style'];
$item_class .= ' ' . $settings['content_visibility']['contentAlign'];

$wrap_class = '';
if (isset($settings['blockId'])) {
	$wrap_class .= 'rtcl-block-' . $settings['blockId'];
}
$wrap_class .= ' rtcl-block-frontend ';
if (isset($settings['className'])) {
	$wrap_class .= $settings['className'];
}
if (isset($settings['align'])) {
	$wrap_class .= ' align' . $settings['align'] . ' ';
}
$icon_type = ($settings['icon_type'] == 'icon') ? 'item-icon' : 'item-image';
?>

<?php if (!empty($terms)) { ?>
	<div class="<?php echo esc_attr($wrap_class); ?>">
		<div class="rtcl rtcl-gb-block">
			<div class="<?php echo esc_attr($col_class); ?>">
				<?php foreach ($terms as $term) {
					$count_html = null;
					if ($settings['content_visibility']['counter'] && !empty($term['count'])) {
						ob_start();
						$count_data = sprintf(_n('%s Ad', '%s Ads', $term["count"], 'classified-listing'), $term['count']); ?>
						<span class="rtcl-counter">
							<?php if (!empty($settings['count_after_text'])) { ?>
								<span><?php echo esc_html($term['count']); ?></span>
								<span><?php echo esc_html($settings['count_after_text']); ?></span>
							<?php } else { ?>
								<span><?php echo esc_html($count_data); ?></span>
							<?php } ?>
						</span>
					<?php
						$count_html = ob_get_clean();
					} ?>

					<div class="<?php echo esc_attr($item_class); ?>">
						<?php if ($settings['content_visibility']['icon'] && !empty($term['icon_html'])) : ?>
							<div class="<?php echo esc_attr($icon_type); ?>">
								<a href="<?php echo esc_url($term['permalink']); ?>"><?php echo wp_kses_post($term['icon_html']); ?> </a>
							</div>
						<?php endif; ?>
						<div class="item-content">
							<h3 class="title"><a href="<?php echo esc_url($term['permalink']); ?>"><?php echo esc_html($term['name']); ?></a></h3>
							<?php if (!empty($count_html)) : ?>
								<div class="counter"> <?php echo wp_kses_post($count_html); ?> </div>
							<?php endif; ?>
							<?php if ($settings['content_visibility']['catDesc'] && !empty($term['description'])) : ?>
								<p class="content">
									<?php
									if ($settings['content_limit']) {
										echo wp_trim_words($term['description'], $settings['content_limit']);
									} else {
										echo wp_kses_post($term['description']);
									}
									?>
								</p>
							<?php endif; ?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>