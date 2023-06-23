<?php

namespace Rtcl\Models;


use DOMDocument;
use Rtcl\Helpers\Functions;

/**
 * Class RtclEmail
 *
 * @package Rtcl\Models
 */
class RtclEmail
{

    public $id;


    /**
     * Default heading.
     *
     * @var string
     */
    public $heading = '';

    /**
     * Default subject.
     *
     * @var string
     */
    public $subject = '';

    /**
     * Plain text template path.
     *
     * @var string
     */
    public $template_plain;

    /**
     * HTML template path.
     *
     * @var string
     */
    public $template_html;

    /**
     * Template path.
     *
     * @var string
     */
    public $template_base;

    /**
     * Recipients for the email.
     *
     * @var string
     */
    public $recipient;

    /**
     * Object this email is for, for example a customer, product, or email.
     *
     * @var object|bool
     */
    public $object;

    private $headers;
    private $content;
    private $attachments = [];
    protected $settings;
    private $email_content_type;
    private $replay_to_email;
    private $replay_to_name;

    /**
     * Strings to find/replace in subjects/headings.
     *
     * @var array
     */
    protected $placeholders = array();


    /**
     *  List of preg* regular expression patterns to search for,
     *  used in conjunction with $plain_replace.
     *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
     *
     * @var array $plain_search
     * @see $plain_replace
     */
    public $plain_search = array(
        "/\r/",                                                  // Non-legal carriage return.
        '/&(nbsp|#0*160);/i',                                    // Non-breaking space.
        '/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i', // Double quotes.
        '/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',               // Single quotes.
        '/&gt;/i',                                               // Greater-than.
        '/&lt;/i',                                               // Less-than.
        '/&#0*38;/i',                                            // Ampersand.
        '/&amp;/i',                                              // Ampersand.
        '/&(copy|#0*169);/i',                                    // Copyright.
        '/&(trade|#0*8482|#0*153);/i',                           // Trademark.
        '/&(reg|#0*174);/i',                                     // Registered.
        '/&(mdash|#0*151|#0*8212);/i',                           // mdash.
        '/&(ndash|minus|#0*8211|#0*8722);/i',                    // ndash.
        '/&(bull|#0*149|#0*8226);/i',                            // Bullet.
        '/&(pound|#0*163);/i',                                   // Pound sign.
        '/&(euro|#0*8364);/i',                                   // Euro sign.
        '/&(dollar|#0*36);/i',                                   // Dollar sign.
        '/&[^&\s;]+;/i',                                         // Unknown/unhandled entities.
        '/[ ]{2,}/',                                             // Runs of spaces, post-handling.
    );

    /**
     *  List of pattern replacements corresponding to patterns searched.
     *
     * @var array $plain_replace
     * @see $plain_search
     */
    public $plain_replace = array(
        '',                                             // Non-legal carriage return.
        ' ',                                            // Non-breaking space.
        '"',                                            // Double quotes.
        "'",                                            // Single quotes.
        '>',                                            // Greater-than.
        '<',                                            // Less-than.
        '&',                                            // Ampersand.
        '&',                                            // Ampersand.
        '(c)',                                          // Copyright.
        '(tm)',                                         // Trademark.
        '(R)',                                          // Registered.
        '--',                                           // mdash.
        '-',                                            // ndash.
        '*',                                            // Bullet.
        '£',                                            // Pound sign.
        'EUR',                                          // Euro sign. € ?.
        '$',                                            // Dollar sign.
        '',                                             // Unknown/unhandled entities.
        ' ',                                             // Runs of spaces, post-handling.
    );


    /**
     * True when email is being sent.
     *
     * @var bool
     */
    public $sending;


    public $db = false;

    /**
     * RtclEmail constructor.
     */
    public function __construct() {
        $this->initSettings();
        add_action('phpmailer_init', array($this, 'handle_multipart'));
    }


    /**
     * Set replay to name
     *
     * @param mixed $name
     */
    public function set_replay_to_name($name) {
        $this->replay_to_name = esc_html($name);
    }

    /**
     * Set replay to email
     *
     * @param mixed $email
     */
    public function set_replay_to_email_address($email) {
        $this->replay_to_email = $email;
    }


    /**
     * Get the from name for outgoing emails.
     *
     * @return string
     */
    public function get_replay_to_name() {
        $from_name = apply_filters('rtcl_email_replay_to_name', $this->format_string($this->replay_to_name), $this);

        return wp_specialchars_decode(esc_html($from_name), ENT_QUOTES);
    }

    /**
     * Get the replay to email address.
     *
     * @return string
     */
    public function get_replay_to_email_address() {
        $replay_to_email_address = apply_filters('rtcl_email_replay_to_email_address', $this->replay_to_email, $this);

        return sanitize_email($replay_to_email_address);
    }

    /**
     * Handle multipart mail.
     *
     * @param \PHPMailer $mailer PHPMailer object.
     *
     * @return \PHPMailer
     */
    public function handle_multipart($mailer) {
        if ($this->sending && 'multipart' === $this->get_email_content_type()) {
            $mailer->AltBody = wordwrap( // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
                preg_replace($this->plain_search, $this->plain_replace, strip_tags($this->get_content_plain()))
            );
            $this->sending = false;
        }

        return $mailer;
    }

    /**
     * Set recipients
     *
     * @param $recipient
     *
     * @return RtclEmail
     */
    public function set_recipient($recipient) {
        if (is_array($recipient)) {
            $this->recipient = $recipient;
        } else {
            $this->recipient = [$recipient];
        }

        return $this;
    }


    /**
     * Get recipients
     *
     * @return array
     */
    public function get_recipient() {
        $to = apply_filters('rtcl_email_recipient_' . $this->id, $this->recipient, $this->object);
        if (!is_array($to)) {
            $to = array_map('trim', explode(',', $to));
        }
        return array_filter($to, 'is_email');
    }

    /**
     * @param $id
     *
     * @return mixed|string|null
     */
    public function get_placeholders_item($id) {
        if (!$id) {
            return '';
        }

        return isset($this->placeholders[$id]) ? $this->placeholders[$id] : null;
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_heading() {
        if (!$this->id && $this->heading) {
            return apply_filters('rtcl_email_heading', $this->format_string($this->heading), $this);
        }

        return apply_filters('rtcl_email_heading_' . $this->id, $this->format_string($this->get_option($this->id . '_heading', $this->get_default_heading())), $this);
    }

    public function set_heading($heading) {
        if ($heading) {
            $this->heading = $heading;
        }

        return $this;
    }


    /**
     * Get email headers.
     *
     * @return string
     */
    public function get_headers() {

        $headers = 'Content-Type: ' . $this->get_content_type() . "\r\n";
        if ($this->get_replay_to_email_address() && $this->get_replay_to_name()) {
            $headers .= 'Reply-to: ' . $this->get_replay_to_name() . ' <' . $this->get_replay_to_email_address() . ">\r\n";
        } elseif ($this->get_from_address() && $this->get_from_name()) {
            $headers .= 'Reply-to: ' . $this->get_from_name() . ' <' . $this->get_from_address() . ">\r\n";
        }

        if ($this->headers) {
            $headers = $this->headers;
        }

        return apply_filters('rtcl_email_headers', $headers, $this->id, $this->object);
    }

    /**
     * @param $headers
     *
     * @return RtclEmail
     */
    public function set_headers($headers) {
        if ($headers) {
            $this->headers = $headers;
        }

        return $this;
    }


    /**
     * Set the locale to the store locale for customer emails to make sure emails are in the store load_language.
     */
    public function setup_locale() {
        if (apply_filters('rtcl_email_setup_locale', true)) {
            Functions::switch_to_site_locale();
        }
    }

    /**
     * Restore the locale to the default locale. Use after finished with setup_locale.
     */
    public function restore_locale() {
        if (apply_filters('rtcl_email_restore_locale', true)) {
            Functions::restore_locale();
        }
    }

    /**
     * Get email subject.
     *
     * @return string
     * @since  3.1.0
     */
    public function get_default_subject() {
        return $this->subject;
    }

    /**
     * Get email heading.
     *
     * @return string
     * @since  3.1.0
     */
    public function get_default_heading() {
        return $this->heading;
    }

    /**
     * @param $subject
     *
     * @return RtclEmail
     */
    public function set_subject($subject) {
        if ($subject) {

            $this->subject = $subject;
        }

        return $this;
    }

    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_subject() {

        if (!$this->id && $this->subject) {
            return apply_filters('rtcl_email_subject', $this->format_string($this->subject), $this->object);
        }

        return apply_filters('rtcl_email_subject_' . $this->id, $this->format_string($this->get_option($this->id . '_subject', $this->get_default_subject())), $this->object);
    }


    /**
     * Attach a file or array of files.
     * File paths must be absolute.
     *
     * @param array $paths
     *
     * @return Object $this
     */
    public function set_attachments($paths = array()) {
        if (is_array($paths) && !empty($paths)) {
            $this->attachments = $paths;
        }

        return $this;
    }

    /**
     * Setup the email settings
     *
     * @return RtclEmail
     */
    public function initSettings() {
        $this->email_content_type = $this->get_option('email_content_type', 'html');
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $current_time = current_time('timestamp');
        if (empty($this->placeholders)) {
            $this->placeholders = array(
                '{site_name}'     => Functions::get_blogname(),
                '{site_title}'    => Functions::get_blogname(),
                '{admin_email}'   => get_option('admin_email'),
                '{site_url}'      => Functions::get_home_url(),
                '{site_link}'     => sprintf('<a href="%s">%s</a>', Functions::get_home_url(), Functions::get_blogname()),
                '{site_link_url}' => sprintf('<a href="%s">%s</a>', Functions::get_home_url(), Functions::get_home_url()),
                '{rtcl}'          => '<a href="https://radiustheme.com">Classified Listing</a>',
                '{today}'         => date_i18n($date_format, $current_time),
                '{now}'           => date_i18n($date_format . ' ' . $time_format, $current_time)
            );
        }

        return $this;
    }

    /**
     * @param      $id
     * @param null $default
     * @param null $type
     *
     * @return bool|int|null
     */
    public function get_option($id, $default = null, $type = null) {
        if (!$this->settings) {
            $this->settings = Functions::get_option('rtcl_email_settings');
        }

        if ($type == 'checkbox') {
            return (isset($this->settings[$id]) && $this->settings[$id] == 'yes') ? true : false;
        } elseif ($type == 'multi_checkbox') {
            return (isset($this->settings[$id]) && is_array($this->settings[$id]) && in_array($default, $this->settings[$id])) ? true : false;
        } elseif ($type == 'number') {
            return isset($this->settings[$id]) ? absint($this->settings[$id]) : absint($default);
        }

        return isset($this->settings[$id]) && !empty($this->settings[$id]) ? $this->settings[$id] : $default;
    }


    /**
     * Get the email content in plain text format.
     *
     * @return string
     */
    public function get_content_plain() {
        return '';
    }

    /**
     * Get the email content in HTML format.
     *
     * @return string
     */
    public function get_content_html() {
        return '';
    }


    /**
     * Get Trigger to send format.
     *
     * @param $id
     * @param $object
     *
     * @return string
     */
    public function trigger($id, $object = null) {
        return '';
    }

    /**
     * Set email content.
     *
     * @param $message
     *
     * @return RtclEmail
     */
    public function set_content($message) {
        if ($message) {
            $this->content = $message;
        }

        return $this;
    }


    /**
     * Get email content.
     *
     * @return string
     */
    public function get_content() {
        $this->sending = true;

        if ($this->content) {
            return $this->content;
        }

        if ('plain' === $this->get_email_content_type()) {
            $email_content = wordwrap(preg_replace($this->plain_search, $this->plain_replace, wp_strip_all_tags($this->get_content_plain())), 70);

        } else {
            $email_content = $this->get_content_html();
        }

        return $email_content;
    }

    public function get_footer_text() {
        return apply_filters('rtcl_email_footer_text', $this->format_string($this->get_option('email_footer_text')), $this);
    }

    public function get_header_image_url() {
        $image_id = $this->get_option('email_header_image');
        $image_url = null;
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'full');
        }

        return apply_filters('rtcl_email_header_image_url', $image_url, $this);
    }

    /**
     * Return email content type.
     *
     * @return string
     */
    public function get_email_content_type() {
        return $this->email_content_type && class_exists(DOMDocument::class) ? $this->email_content_type : 'plain';
    }


    /**
     * Get the from name for outgoing emails.
     *
     * @return string
     */
    public function get_from_name() {
        $from_name = apply_filters('rtcl_email_from_name', $this->format_string($this->get_option('from_name', get_option('blogname'))), $this);

        return wp_specialchars_decode(esc_html($from_name), ENT_QUOTES);
    }

    /**
     * Get the from address for outgoing emails.
     *
     * @return string
     */
    public function get_from_address() {
        $from_address = apply_filters('rtcl_email_from_address', $this->format_string($this->get_option('from_email', get_option('admin_email'))), $this);

        return sanitize_email($from_address);
    }


    /**
     * Get email content type.
     *
     * @return string
     */
    public function get_content_type() {
        switch ($this->get_email_content_type()) {
            case 'html':
                return 'text/html';
            case 'multipart':
                return 'multipart/alternative';
            default:
                return 'text/plain';
        }
    }


    /**
     * Get email attachments.
     *
     * @return array
     */
    public function get_attachments() {
        return apply_filters('rtcl_email_attachments', $this->attachments, $this->id, $this->object, $this);
    }


    /**
     * Return the email's title
     *
     * @return string
     */
    public function get_title() {
        return apply_filters('rtcl_email_title', $this->title, $this);
    }

    /**
     * Return the email's description
     *
     * @return string
     */
    public function get_description() {
        return apply_filters('rtcl_email_description', $this->description, $this);
    }


    /**
     * Apply inline styles to dynamic content.
     * We only inline CSS for html emails, and to do so we use Emogrifier library (if supported).
     *
     * @param string|null $content Content that will receive inline styles.
     *
     * @return string
     */
    public function style_inline($content) {
        if (in_array($this->get_content_type(), array('text/html', 'multipart/alternative'), true)) {
            $style_html = Functions::get_template_html('emails/email-styles', array('email' => $this));
            $css = apply_filters('rtcl_email_styles', $style_html, $this);
            $emogrifier_class = 'Pelago\\Emogrifier';

            if ($this->supports_emogrifier() && class_exists($emogrifier_class)) {
                try {
                    $emogrifier = new $emogrifier_class($content, $css);

                    do_action('rtcl_emogrifier', $emogrifier, $this);

                    $content = $emogrifier->emogrify();
                    $html_prune = \Pelago\Emogrifier\HtmlProcessor\HtmlPruner::fromHtml($content);
                    $html_prune->removeElementsWithDisplayNone();
                    $content = $html_prune->render();
                } catch (\Exception $e) {
                    $logger = rtcl()->logger();
                    $logger->error($e->getMessage(), array('source' => 'emogrifier'));
                }
            } else {
                $content = '<style type="text/css">' . $css . '</style>' . $content;
            }
        }

        return $content;
    }


    /**
     * Return if emogrifier library is supported.
     *
     * @return bool
     */
    protected function supports_emogrifier() {
        return class_exists('DOMDocument');
    }

    /**
     * Set the wp_mail_content_type filter, if necessary
     *
     * @throws \Exception
     */
    private function beforeSend() {
        add_filter('wp_mail_from', array($this, 'get_from_address'));
        add_filter('wp_mail_from_name', array($this, 'get_from_name'));
        add_filter('wp_mail_content_type', array($this, 'get_content_type'));
    }

    private function afterSend() {
        remove_filter('wp_mail_from', array($this, 'get_from_address'));
        remove_filter('wp_mail_from_name', array($this, 'get_from_name'));
        remove_filter('wp_mail_content_type', array($this, 'get_content_type'));
    }


    /**
     * Format email string.
     *
     * @param mixed $string Text to replace placeholders in.
     *
     * @return string
     */
    public function format_string($string) {

        $placeholders = apply_filters('rtcl_email_placeholders', $this->placeholders, $this);

        $find = array_keys($placeholders);
        $replace = array_values($placeholders);

        return apply_filters('rtcl_email_format_string', str_replace($find, $replace, $string), $this);
    }


    /**
     * Send an email.
     *
     * @return bool success
     * @throws \Exception
     */
    public function send() {
        $this->beforeSend();

        $to = $this->get_recipient();
        $subject = $this->get_subject();
        $message = $this->get_content();
        $headers = $this->get_headers();
        $attachments = $this->get_attachments();
        $message = apply_filters('rtcl_mail_content', $this->style_inline($message));
        $mail_callback = apply_filters('rtcl_mail_callback', 'wp_mail', $this);
        $mail_callback_params = apply_filters('rtcl_mail_callback_params', array(
            $to,
            $subject,
            $message,
            $headers,
            $attachments
        ), $this);
        $return = call_user_func_array($mail_callback, $mail_callback_params);

        $this->afterSend();

        return $return;
    }

}