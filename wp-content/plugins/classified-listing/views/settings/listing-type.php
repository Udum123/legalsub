<?php

use Rtcl\Helpers\Functions;

?>
<div class="wrap rtcl" id="rtcl-listing-types-wrap">
    <h1><?php esc_html_e( "Listing Types", 'classified-listing' ) ?></h1>
    <div class="rtcl-listing-types-wrapper row">
        <div id="input-new-type-wrapper" class="col-md-4 col-12">
            <form id="input-new-type-form">
                <div class="form-group">
                    <label><?php esc_html_e( "Add new type", "classified-listing" ); ?></label>
                    <input type="text" name="type" id="add-input-type" class="form-control">
                </div>
				<?php do_action( 'rtcl_after_listing_type_input' ); ?>
                <div class="form-group">
                    <button class="btn btn-success" type="submit"
                            id="rtcl-add-btn"><?php esc_html_e( "Add new type", "classified-listing" ); ?></button>
                </div>
            </form>
        </div>
        <div class="col-md-8 col-12" id="rtcl-listing-type-wrap">
			<?php
			$types = Functions::get_listing_types();
			if ( ! empty( $types ) ) {
				?>
                <ul id="listing-types" class="list-group">
					<?php
					foreach ( $types as $typeId => $type ) {
						?>
                        <li class="list-group-item listing-type" data-id="<?php echo esc_attr( $typeId ); ?>">
                            <div class="type-details d-flex">
                                <div class="type-info">
                                    <div class="type-info-id"><?php echo esc_html( $typeId ); ?></div>
                                    <div class="type-info-name"><?php echo esc_html( $type ); ?></div>
                                </div>
                                <div class="action ml-auto">
                                    <span class="btn btn-success btn-sm edit"><?php esc_html_e( 'Edit', 'classified-listing' ); ?></span>
                                    <span class="btn btn-danger btn-sm delete"><?php esc_html_e( 'Delete', 'classified-listing' ); ?></span>
                                </div>
                            </div>
                            <div class="edit-action">
                                <form class="row input-update-type-form">
                                    <div class="form-group col-6">
                                        <label><?php esc_html_e( 'ID', 'classified-listing' ); ?></label>
                                        <input type="text" name="id" class="form-control"
                                               value="<?php echo esc_attr( $typeId ); ?>">
                                    </div>
                                    <div class="form-group col-6">
                                        <label><?php esc_html_e( 'Type', 'classified-listing' ); ?></label>
                                        <input type="text" name="name" class="form-control"
                                               value="<?php echo esc_attr( $type ); ?>">
                                    </div>
	                                <?php do_action( 'rtcl_after_listing_type_input', $typeId ); ?>
                                    <div class="form-group col-12">
                                        <button type="submit"
                                                class="btn btn-primary w-100"><?php esc_html_e( 'Update', 'classified-listing' ); ?></button>
                                    </div>
                                </form>
                            </div>
                        </li>
						<?php
					}
					?>
                </ul>
				<?php
			} else {
				esc_html_e( "No listing type found", "classified-listing" );
			}
			?>
        </div>
    </div>
</div>