<?php
/**
 * Listing Form Contact
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var string $selected_type
 */

use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Text;

Functions::print_notices();
?>

<div class="rtcl-listing-info-selecting">
	<?php if ( ! Functions::is_ad_type_disabled() ): ?>
        <div id="rtcl-ad-type-selection">
            <div class="rtcl-post-section-title">
                <h3>
                    <i class="rtcl-icon rtcl-icon-tags"></i><?php esc_html_e( "Select a type", "classified-listing" ); ?>
                </h3>
            </div>
            <div class="form-group row">
                <label for="rtcl-category"
                       class="col-md-2 col-form-label"><?php esc_html_e( 'Ad Type', 'classified-listing' ); ?>
                    <span class="require-star">*</span>
                </label>
                <div class="col-md-10">
                    <select class="rtcl-select2 form-control" id="rtcl-ad-type" name="type" required>
                        <option value="">--<?php esc_html_e( "Select a type", "classified-listing" ) ?>--</option>
						<?php
						$types = Functions::get_listing_types();
						if ( ! empty( $types ) ):
							foreach ( $types as $type_id => $type ):
								$tSlt = $type_id == $selected_type ? ' selected' : '';
								echo "<option value='{$type_id}'{$tSlt}>" . esc_html( $type ) . "</option>";
							endforeach;
						endif;
						?>
                    </select>
                </div>
            </div>
        </div>
	<?php endif; ?>
    <div id="rtcl-ad-category-selection"
         style="display: <?php echo esc_attr( ( ( $selected_type && in_array( $selected_type, array_keys( Functions::get_listing_types() ) ) ) || Functions::is_ad_type_disabled() ) ? 'block' : 'none' ); ?>">
        <div class="rtcl-post-section-title">
            <h3>
                <i class="rtcl-icon rtcl-icon-tags"></i><?php esc_html_e( "Select a category", "classified-listing" ); ?>
            </h3>
        </div>
        <div class="rtcl-post-category">
            <div class="form-group row" id="cat-row">
                <label for="rtcl-category"
                       class="col-md-2 col-form-label"><?php esc_html_e( 'Category', 'classified-listing' ); ?>
                    <span class="require-star">*</span></label>
                <div class="col-md-10" id="rtcl-category-wrap">
                    <select class="rtcl-select2 form-control" id="rtcl-category" required>
                        <option value=""><?php echo esc_html( Text::get_select_category_text() ) ?></option>
						<?php
						$cats          = Functions::get_one_level_categories( 0, $selected_type );
						$parent_cat_id = isset( $parent_cat_id ) ? $parent_cat_id : 0;
						if ( ! empty( $cats ) ) {
							foreach ( $cats as $cat ) {
								$slt = $parent_cat_id == $cat->term_id ? ' selected' : '';
								echo "<option value='{$cat->term_id}'{$slt}>{$cat->name}</option>";
							}
						}
						?>
                    </select>
                </div>
            </div>
			<?php $child_cats = $parent_cat_id ? Functions::get_one_level_categories( $parent_cat_id ) : array() ?>
            <div class="form-group row<?php echo empty( $child_cats ) ? ' rtcl-hide' : ''; ?>" id="sub-cat-row">
                <label for="rtcl-sub-category"
                       class="col-md-2 col-form-label"><?php esc_html_e( 'Sub Category', 'classified-listing' ); ?>
                    <span class="require-star">*</span></label>
                <div class="col-md-10" id="rtcl-sub-category-wrap">
						<?php
						if ( ! empty( $child_cats ) ) {
						    echo '<select class="form-control" required>';
							echo "<option value=''>" . esc_html( Text::get_select_category_text() ) . "</option>";
							foreach ( $child_cats as $cat ) {
								echo "<option value='" . absint( $cat->term_id ) . "'>" . esc_html( $cat->name ) . "</option>";
							}
							echo '</select>';
						}
						?>
                </div>
            </div>
        </div>
    </div>
</div>
