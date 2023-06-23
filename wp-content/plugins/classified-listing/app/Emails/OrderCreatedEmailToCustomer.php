<?php


namespace Rtcl\Emails;


use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Models\RtclEmail;

class OrderCreatedEmailToCustomer extends RtclEmail
{

    protected $listing = null;

    function __construct() {
        $this->db = true;
        $this->id = 'order_created';
        $this->template_html = 'emails/order-created-email-to-customer';

        // Call parent constructor.
        parent::__construct();
    }


    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_default_subject() {
        return esc_html__('[{site_title}] #{order_number} Thank you for your order', 'classified-listing');
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__('New Order: #{order_number}', 'classified-listing');
    }

    /**
     * Trigger the sending of this email.
     *
     * @param               $id
     * @param Payment|false $order Payment
     *
     * @return void
     * @throws \Exception
     */
    public function trigger($id, $order = false) {

        $this->setup_locale();

        if ($id && !$order instanceof Payment) {
            $order = rtcl()->factory->get_order($id);
        }
		
        if ($order instanceof Payment) {
            $this->object = $order;
            $this->placeholders = wp_parse_args(array(
                '{order_number}' => $order->get_maybe_order_number()
            ), $this->placeholders);
            $this->set_recipient($order->get_customer_email());
        }
	    
        if (!empty($this->get_recipient())) {
            $this->send();
        }

        $this->restore_locale();

    }


    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
        return Functions::get_template_html(
            $this->template_html, array(
                'order'         => $this->object,
                'email'         => $this,
                'sent_to_admin' => false,
            )
        );
    }

}
