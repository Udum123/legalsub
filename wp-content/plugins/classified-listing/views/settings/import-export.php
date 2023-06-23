<div class="wrap rtcl-import-export rtcl">
    <h1><?php esc_html_e( "Import", 'classified-listing' ) ?></h1>
    <div class="rtcl-ie-wrap" id="rtcl-import-wrap">
        <form class="form" id="rtcl-import-form">
            <div class="form-group row">
                <label for="rtcl-import-file"
                       class="rtcl-label col-sm-2 col-form-label"><?php esc_html_e( "Select Import File", "classified-listing" ) ?></label>
                <div class="col-sm-10">
                    <div class="col-sm-10 custom-file" style="width: 250px;">
                        <input type="file" class="custom-file-input rtcl-import-file" name="import-file"
                               id="rtcl-import-file" required>
                        <label class="custom-file-label"
                               for="rtcl-import-file"><?php esc_html_e( "Choose file...", "classified-listing" ) ?></label>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary" type="submit"
                    id="rtcl-import-btn"><?php esc_html_e( "Import", "classified-listing" ) ?></button>
            <p class="description my-4"><?php echo sprintf( __( "Sample data <a href='%s' target='_blank'>click here</a>", 'classified-listing' ), 'https://gist.github.com/radiustheme/7a15605eac0a6a952d90e5853f5e9c39' ) ?></p>
        </form>
        <div id="import-response" class=""></div>
    </div>
</div>