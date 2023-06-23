<?php

namespace Rtcl\Controllers\Admin;


use Rtcl\Helpers\Functions;

class AddConfig {

	function __construct() {
		add_action( 'init', array( $this, 'addConfigurations' ), 5 );
		// Add a post display state for special  pages.
		add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
	}

	static function get_custom_page_list() {
		$pages = array(
			'listings'     => array(
				'title'   => esc_html__( 'Listings', 'classified-listing' ),
				'content' => ''
			),
			'listing_form' => array(
				'title'   => esc_html__( 'Listing Form', 'classified-listing' ),
				'content' => '[rtcl_listing_form]'
			),
			'checkout'     => array(
				'title'   => esc_html__( 'Checkout', 'classified-listing' ),
				'content' => '[rtcl_checkout]'
			),
			'myaccount'    => array(
				'title'   => esc_html__( 'My Account', 'classified-listing' ),
				'content' => '[rtcl_my_account]'
			)
		);

		return apply_filters( 'rtcl_custom_pages_list', $pages );
	}

	public function add_display_post_states( $post_states, $post ) {
		$page_settings = Functions::get_page_ids();
		$pList         = $this->get_custom_page_list();
		foreach ( $page_settings as $type => $id ) {
			if ( $post->ID == $id ) {
				$post_states[] = $pList[ $type ]['title'] . " " . esc_html__( "Page", "classified-listing" );
			}
		}

		return $post_states;
	}

	function addConfigurations() {
		$ms = Functions::get_option( 'rtcl_misc_settings' );

		rtcl()->gallery = array(
			'option_name'    => 'rtcl_gallery',
			'image_edit_cap' => (isset($ms['image_edit_cap']) && $ms['image_edit_cap'] == 'yes') ? true : false,
			'image_sizes'    => array(
				"rtcl-gallery"           => array(
					'width'  => isset( $ms['image_size_gallery']['width'] ) ? absint( $ms['image_size_gallery']['width'] ) : 924,
					'height' => isset( $ms['image_size_gallery']['width'] ) ? absint( $ms['image_size_gallery']['height'] ) : 462,
					'crop'   => isset( $ms['image_size_gallery']['crop'] ) && $ms['image_size_gallery']['crop'] === 'yes' ? true : false
				),
				"rtcl-thumbnail"         => array(
					'width'  => isset( $ms['image_size_thumbnail']['width'] ) ? absint( $ms['image_size_thumbnail']['width'] ) : 320,
					'height' => isset( $ms['image_size_thumbnail']['width'] ) ? absint( $ms['image_size_thumbnail']['height'] ) : 240,
					'crop'   => isset( $ms['image_size_thumbnail']['crop'] ) && $ms['image_size_thumbnail']['crop'] === 'yes' ? true : false
				),
				"rtcl-gallery-thumbnail" => array(
					'width'  => isset( $ms['image_size_gallery_thumbnail']['width'] ) ? absint( $ms['image_size_gallery_thumbnail']['width'] ) : 150,
					'height' => isset( $ms['image_size_gallery_thumbnail']['width'] ) ? absint( $ms['image_size_gallery_thumbnail']['height'] ) : 105,
					'crop'   => isset( $ms['image_size_gallery_thumbnail']['crop'] ) && $ms['image_size_gallery_thumbnail']['crop'] === 'yes' ? true : false
				),
			)
		);

		$this->addImageSizes();
	}

	private function addImageSizes() {
		foreach ( rtcl()->gallery['image_sizes'] as $image_key => $image_size ) {
			add_image_size( $image_key, $image_size["width"], $image_size["height"], $image_size["crop"] );
		}
	}

}