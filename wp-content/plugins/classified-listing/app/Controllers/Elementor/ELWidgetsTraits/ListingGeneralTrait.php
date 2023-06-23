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

use Elementor\{
	Controls_Manager,
	Group_Control_Typography,
	Group_Control_Image_Size
};

trait ListingGeneralTrait {
	use ListingStyleTrait;
	/**
	 * Set Query controlls
	 *
	 * @return array
	 */
	public function listings_general_fields() {
		return array();
	}

}
