<?php
/**
 * Payment notes HTML for meta box.
 *
 * @var array $notes
 */

use Rtcl\Helpers\Functions;

defined('ABSPATH') || exit;

?>
<ul class="rtcl_payment_notes">
    <?php
    if ($notes) {
        foreach ($notes as $note) {
            $css_class = array('note');
            $css_class[] = $note->customer_note ? 'customer-note' : '';
            $css_class[] = 'system' === $note->added_by ? 'system-note' : '';
            $css_class = apply_filters('rtcl_order_note_class', array_filter($css_class), $note);
            ?>
            <li rel="<?php echo absint($note->id); ?>" class="<?php echo esc_attr(implode(' ', $css_class)); ?>">
                <div class="note_content">
                    <?php echo wpautop(wptexturize(wp_kses_post($note->content))); // @codingStandardsIgnoreLine ?>
                </div>
                <p class="meta">
                    <abbr class="exact-date"
                          title="<?php echo esc_attr($note->date_created->date('y-m-d h:i:s')); ?>">
                        <?php
                        /* translators: %1$s: note date %2$s: note time */
                        echo esc_html(sprintf(__('%1$s at %2$s', 'classified-listing'), $note->date_created->date_i18n(Functions::date_format()), $note->date_created->date_i18n(Functions::time_format())));
                        ?>
                    </abbr>
                    <?php
                    if ('system' !== $note->added_by) :
                        /* translators: %s: note author */
                        echo esc_html(sprintf(' ' . __('by %s', 'classified-listing'), $note->added_by));
                    endif;
                    ?>
                    <a href="#" class="delete_note"
                       role="button"><?php esc_html_e('Delete note', 'classified-listing'); ?></a>
                </p>
            </li>
            <?php
        }
    } else {
        ?>
        <li><?php esc_html_e('There are no notes yet.', 'classified-listing'); ?></li>
        <?php
    }
    ?>
</ul>
