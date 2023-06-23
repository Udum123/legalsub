<?php
/**
 * Trait for listing widget
 *
 * The Elementor builder.
 *
 * @since    2.0.10
 */

namespace Rtcl\Controllers\Elementor\ELWidgetsTraits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;

trait ArchiveGeneralTrait {
	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function archive_general_fields() {
		$fields = [
			[
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_general',
				'label' => __('General', 'classified-listing'),
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_archive_result_count',
				'label'         => __('Result count', 'classified-listing'),
				'label_on'      => __('On', 'classified-listing'),
				'label_off'     => __('Off', 'classified-listing'),
				'description'   => __('Switch to Show Result Count', 'classified-listing'),
				'default'       => 'yes',
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_archive_catalog_ordering',
				'label'         => __('Catalog Ordering', 'classified-listing'),
				'label_on'      => __('On', 'classified-listing'),
				'label_off'     => __('Off', 'classified-listing'),
				'description'   => __('Switch to Show Catalog Ordering', 'classified-listing'),
				'default'       => 'yes',
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_archive_view_switcher',
				'label'         => __('View Switcher', 'classified-listing'),
				'label_on'      => __('On', 'classified-listing'),
				'label_off'     => __('Off', 'classified-listing'),
				'description'   => __('Switch to Show View Switcher', 'classified-listing'),
				'default'       => 'yes',
			],
			[
				'type'          => Controls_Manager::SWITCHER,
				'id'            => 'rtcl_listing_pagination',
				'label'         => __('Pagination', 'classified-listing'),
				'label_on'      => __('On', 'classified-listing'),
				'label_off'     => __('Off', 'classified-listing'),
				'description'   => __('Switch to Show Pagination', 'classified-listing'),
				'default'       => 'yes',
			],
			[
				'label'         => __('Image Size', 'classified-listing'),
				'type'          => Group_Control_Image_Size::get_type(),
				'id'            => 'rtcl_thumb_image',
				'exclude'       => ['custom'],
				'mode'          => 'group',
				'default'       => 'rtcl-thumbnail',
				'separator'     => 'none',
				'description'   => __('Select Image Size', 'classified-listing'),
			],
			[
				'mode' => 'section_end',
			],
		];

		return $fields;
	}
}
