<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title', 'classified-listing'); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
		   name="<?php echo $this->get_field_name('title'); ?>" type="text"
		   value="<?php echo esc_attr($instance['title']); ?>">
</p>

<p>
	<label
		for="<?php echo $this->get_field_id('parent'); ?>"><?php esc_html_e('Select Parent', 'classified-listing'); ?></label>
	<?php
	wp_dropdown_categories(array(
		'show_option_none'  => '-- ' . esc_html__('Select Parent', 'classified-listing') . ' --',
		'option_none_value' => 0,
		'taxonomy'          => rtcl()->category,
		'name'              => $this->get_field_name('parent'),
		'class'             => 'widefat',
		'orderby'           => 'name',
		'selected'          => (int)$instance['parent'],
		'hierarchical'      => true,
		'depth'             => 10,
		'show_count'        => false,
		'hide_empty'        => false,
	));
	?>
</p>

<p>
	<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php esc_html_e('Order By', 'classified-listing'); ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>"
			name="<?php echo $this->get_field_name('orderby'); ?>">
		<?php
		$options = array(
			'name'        => esc_html__('Name', 'classified-listing'),
			'id'          => esc_html__('Id', 'classified-listing'),
			'count'       => esc_html__('Count', 'classified-listing'),
			'slug'        => esc_html__('Slug', 'classified-listing'),
			'_rtcl_order' => esc_html__('Custom', 'classified-listing'),
			'none'        => esc_html__('None', 'classified-listing'),
		);

		foreach ($options as $key => $value) {
			printf('<option value="%s"%s>%s</option>', $key, selected($key, $instance['orderby']), $value);
		}
		?>
	</select>
</p>
<p>
	<label for="<?php echo $this->get_field_id('order'); ?>"><?php esc_html_e('Order', 'classified-listing'); ?></label>
	<select class="widefat" id="<?php echo $this->get_field_id('order'); ?>"
			name="<?php echo $this->get_field_name('order'); ?>">
		<?php
		$options = array(
			'asc'  => esc_html__('ASC', 'classified-listing'),
			'desc' => esc_html__('DESC', 'classified-listing')
		);

		foreach ($options as $key => $value) {
			printf('<option value="%s"%s>%s</option>', $key, selected($key, $instance['order']), $value);
		}
		?>
	</select>
</p>

<p>
	<input <?php checked($instance['imm_child_only']); ?> id="<?php echo $this->get_field_id('imm_child_only'); ?>"
														  name="<?php echo $this->get_field_name('imm_child_only'); ?>"
														  type="checkbox"/>
	<label
		for="<?php echo $this->get_field_id('imm_child_only'); ?>"><?php esc_html_e('Show only the immediate children of the selected category. Displays all the top level categories if no parent is selected.', 'classified-listing'); ?></label>
</p>

<p>
	<input <?php checked($instance['show_image']); ?> id="<?php echo $this->get_field_id('show_image'); ?>"
													  name="<?php echo $this->get_field_name('show_image'); ?>"
													  type="checkbox"/>
	<label
		for="<?php echo $this->get_field_id('show_image'); ?>"><?php esc_html_e('Show image of the Categories', 'classified-listing'); ?></label>
</p>
<p>
	<input <?php checked($instance['show_icon']); ?> id="<?php echo $this->get_field_id('show_icon'); ?>"
													 name="<?php echo $this->get_field_name('show_icon'); ?>"
													 type="checkbox"/>
	<label
		for="<?php echo $this->get_field_id('show_icon'); ?>"><?php esc_html_e('Show icon of the Categories', 'classified-listing'); ?></label>
</p>

<p>
	<input <?php checked($instance['hide_empty']); ?> id="<?php echo $this->get_field_id('hide_empty'); ?>"
													  name="<?php echo $this->get_field_name('hide_empty'); ?>"
													  type="checkbox"/>
	<label
		for="<?php echo $this->get_field_id('hide_empty'); ?>"><?php esc_html_e('Hide Empty Categories', 'classified-listing'); ?></label>
</p>

<p>
	<input <?php checked($instance['show_count']); ?> id="<?php echo $this->get_field_id('show_count'); ?>"
													  name="<?php echo $this->get_field_name('show_count'); ?>"
													  type="checkbox"/>
	<label
		for="<?php echo $this->get_field_id('show_count'); ?>"><?php esc_html_e('Show Listing Counts', 'classified-listing'); ?></label>
</p>