<?php
/**
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 */
?>
<form id="rtcl-contact-form" class="form-vertical">
    <div class="form-group">
        <input type="text" name="name" class="form-control" id="rtcl-contact-name"
               placeholder="<?php esc_attr_e( "Name *", "classified-listing" ) ?>"
               required/>
    </div>
    <div class="form-group">
        <input type="email" name="email" class="form-control" id="rtcl-contact-email"
               placeholder="<?php esc_attr_e( "Email*", "classified-listing" ) ?>"
               required/>
    </div>
    <div class="form-group">
        <textarea class="form-control" name="message" id="rtcl-contact-message" rows="3"
                  placeholder="<?php esc_attr_e( "Message*", "classified-listing" ) ?>"
                  required></textarea>
    </div>

    <div id="rtcl-contact-g-recaptcha"></div>
    <p id="rtcl-contact-message-display"></p>

    <button type="submit"
            class="btn btn-primary"><?php esc_html_e( "Submit", "classified-listing" ) ?>
    </button>
</form>
