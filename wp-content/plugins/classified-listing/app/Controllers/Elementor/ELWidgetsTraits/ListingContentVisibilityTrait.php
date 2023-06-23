<?php
/**
 * Trait for listing widget
 *
 * The Elementor builder.
 *
 * @package  Classifid-listing
 * @since    2.0.10
 */

namespace Rtcl\Controllers\Elementor\ELWidgetsTraits;

use Elementor\Controls_Manager;

trait ListingContentVisibilityTrait {
	/**
	 * Set field controlls
	 *
	 * @return array
	 */
	public function listing_content_visibility_fields() {
		$fields = array(
			array(
				'mode'  => 'section_start',
				'id'    => 'rtcl_sec_content_visibility',
				'label' => __( 'Content Visibility ', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_image',
				'label'       => __( 'Show Image', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Icon/Image. Default: On', 'classified-listing' ),
			),

			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_title',
				'label'       => __( 'Show Title', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Listing Title. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_description',
				'label'       => __( 'Short Description', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => '',
				'description' => __( 'Show or Hide Listing Description. Default: On', 'classified-listing' ),
			),

			array(
				'type'        => Controls_Manager::NUMBER,
				'id'          => 'rtcl_content_limit',
				'label'       => __( 'Short Description Word Limit', 'classified-listing' ),
				'default'     => '20',
				'description' => __( 'Number of Words to display', 'classified-listing' ),
				'condition'   => array( 'rtcl_show_description' => array( 'yes' ) ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_labels',
				'label'       => __( 'Show Badge', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide labels. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_details_button',
				'label'       => __( 'Show Details Button', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Details Button. Default: On', 'classified-listing' ),
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'list' ),
								),
								array(
									'name'     => 'rtcl_listings_style',
									'operator' => 'in',
									'value'    => array( 'style-1', 'style-2' ),
								),
							),
						),
					),
				),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_date',
				'label'       => __( 'Show Date', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide date. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_location',
				'label'       => __( 'Show Location', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Location. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_category',
				'label'       => __( 'Show Category', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Category. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_price',
				'label'       => __( 'Show Price', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Price. Default: On', 'classified-listing' ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_price_unit',
				'label'     => __( 'Show Price Unit', 'classified-listing' ),
				'label_on'  => __( 'Hide', 'classified-listing' ),
				'label_off' => __( 'Show', 'classified-listing' ),
				'default'   => 'yes',
				'condition' => array( 'rtcl_show_price' => array( 'yes' ) ),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_price_type',
				'label'     => __( 'Show Price Type', 'classified-listing' ),
				'label_on'  => __( 'Hide', 'classified-listing' ),
				'label_off' => __( 'Show', 'classified-listing' ),
				'default'   => 'yes',
				'condition' => array( 'rtcl_show_price' => array( 'yes' ) ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_user',
				'label'       => __( 'Show User', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => '',
				'description' => __( 'Show or Hide User/Author Name. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_views',
				'label'       => __( 'Show Views', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Views Count\'s. Default: On', 'classified-listing' ),
			),
			array(
				'type'        => Controls_Manager::SWITCHER,
				'id'          => 'rtcl_show_types',
				'label'       => __( 'Show Types', 'classified-listing' ),
				'label_on'    => __( 'On', 'classified-listing' ),
				'label_off'   => __( 'Off', 'classified-listing' ),
				'default'     => 'yes',
				'description' => __( 'Show or Hide Types. Default: On', 'classified-listing' ),
			),
			array(
				'type'       => Controls_Manager::SWITCHER,
				'id'         => 'rtcl_show_phone',
				'label'      => __( 'Show Phone', 'classified-listing' ),
				'label_on'   => __( 'On', 'classified-listing' ),
				'label_off'  => __( 'Off', 'classified-listing' ),
				'default'    => 'yes',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'list' ),
								),
								array(
									'name'     => 'rtcl_listings_style',
									'operator' => 'in',
									'value'    => array( 'style-4', 'style-5' ),
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'grid' ),
								),
								array(
									'name'     => 'rtcl_listings_grid_style',
									'operator' => 'in',
									'value'    => array( 'style-3' ),
								),
							),
						),
					),
				),
			),
			array(
				'type'      => Controls_Manager::SWITCHER,
				'id'        => 'rtcl_show_favourites',
				'label'     => __( 'Show Favourites', 'classified-listing' ),
				'label_on'  => __( 'On', 'classified-listing' ),
				'label_off' => __( 'Off', 'classified-listing' ),
				'default'   => 'yes',
			),
			array(
				'type'       => Controls_Manager::SELECT,
				'id'         => 'rtcl_action_button_layout',
				'label'      => __( 'Action Button Layout', 'classified-listing' ),
				'options'    => array(
					'vertical'   => __( 'Vertical View', 'classified-listing' ),
					'horizontal' => __( 'Horizontal View', 'classified-listing' ),
				),
				'default'    => 'horizontal',
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'list' ),
								),
								array(
									'name'     => 'rtcl_listings_style',
									'operator' => 'in',
									'value'    => array( 'style-2', 'style-3' ),
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'rtcl_listings_view',
									'operator' => 'in',
									'value'    => array( 'grid' ),
								),
								array(
									'name'     => 'rtcl_listings_grid_style',
									'operator' => 'in',
									'value'    => array( 'style-1', 'style-2', 'style-4' ),
								),
							),
						),
					),
				),
			),

			array(
				'mode' => 'section_end',
			),
		);
		return apply_filters( 'el_listing_widget_content_visibility_fields', $fields, $this );
	}
	/**
	 * Content visiblity fields.
	 *
	 * @return array
	 */
	public function slider_content_visiblity() {
		$fields       = $this->listing_content_visibility_fields();
		$after_remove = $this->remove_controls(
			array(
				'rtcl_show_details_button',
			),
			$fields
		);
		$the_array    = array(
			array(
				'id'        => 'rtcl_show_phone',
				'condition' => array( 'rtcl_listings_grid_style' => array( 'style-3' ) ),
				'unset'     => array(
					'conditions',
				),
			),
			array(
				'id'        => 'rtcl_action_button_layout',
				'unset'     => array(
					'conditions',
				),
				'condition' => array(
					'rtcl_listings_grid_style' => array( 'style-1', 'style-2', 'style-4' ),
				),
			),
		);

		$modified = $this->modify_controls( $the_array, $after_remove );
		return $modified;
	}
}
