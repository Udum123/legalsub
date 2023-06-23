<?php
$count_html = '';
if (!empty($term['count'])) {
	$count_html = sprintf(_nx('%s Ad', '%s Ads', $term['count'], 'Number of Ads', 'classified-listing'), number_format_i18n($term['count']));
}

$location_box = $settings['col_style']['style'] ? $settings['col_style']['style'] : '1';
$class        = $settings['show_count'] ? 'rtcl-gb-has-count' : '';
$class       .= ' location-box-style-' . $location_box;

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
	<div class="rtcl">
		<div class="rtcl-gb-listing-location-box <?php echo esc_attr($class); ?>">
			<div class="rtcl-gb-img"></div>
			<div class="rtcl-gb-content">
				<h3 class="rtcl-gb-title"><a href="#"><?php echo esc_html($term['title']); ?></a></h3>
				<?php if ($settings['show_count'] && !empty($term['count'])) : ?>
					<div class="rtcl-gb-counter"><?php echo esc_html($count_html); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>