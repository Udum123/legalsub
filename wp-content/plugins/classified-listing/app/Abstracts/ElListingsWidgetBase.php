<?php
/**
 * Elementor addons base.
 *
 * @package  Classifid-listing
 * @subpackage Classifid-listing/Abstracts
 */

namespace Rtcl\Abstracts;

use Rtcl\Controllers\Elementor\ELWidgetsTraits\{
	ListingMetaTrait,
	ListingDescTrait,
	ListingPriceTrait,
	ListingTitleTrait,
	ListingBadgeTrait,
	ListingImageTrait,
	ListingWrapperTrait,
	ListingActionBtnTrait,
	ListingPaginationTrait,
	ListingPromotionFieldsTrait,
	ListingResponsiveControlTrait,
	ListingContentVisibilityTrait
};


/**
 * Abstract ElListingsWidgetBase Class.
 *
 * Listing related widget, ( Example: Listing list/view/archive/relatedlisting ).
 *
 * @version  1.0.0
 * @package  Classifid-listing/Abstracts
 */
abstract class ElListingsWidgetBase extends ElementorWidgetBase {

	/**
	 * Content visiblity.
	*/
	use ListingContentVisibilityTrait;
	/**
	 * Responsive control.
	 */
	use ListingResponsiveControlTrait;
	/**
	 * Listing Wrpper.
	 */
	use ListingWrapperTrait;
	/**
	 * Promotion Section.
	 */
	use ListingPromotionFieldsTrait;
	/**
	 * Promotion Section.
	 */
	use ListingBadgeTrait;
	/**
	 * Title Section.
	 */
	use ListingTitleTrait;
	/**
	 * Meta Section.
	 */
	use ListingMetaTrait;
	/**
	 * Action Button Section.
	 */
	use ListingActionBtnTrait;
	/**
	 * Action Pagination Section.
	 */
	use ListingPaginationTrait;
	/**
	 * Action Price Section.
	 */
	use ListingPriceTrait;
	/**
	 * Action Description Section.
	 */
	use ListingDescTrait;
	/**
	 * Action Image Section.
	 */
	use ListingImageTrait;

}
