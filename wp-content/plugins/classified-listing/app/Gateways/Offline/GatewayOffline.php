<?php

namespace Rtcl\Gateways\Offline;

use Rtcl\Helpers\Link;
use Rtcl\Models\Payment;
use Rtcl\Models\PaymentGateway;

class GatewayOffline extends PaymentGateway
{


    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id = 'offline';
        $this->option = $this->option . $this->id;
        $this->order_button_text = esc_html__('Offline Payout', 'classified-listing');
        $this->method_title = esc_html__('Offline', 'classified-listing');
        $this->method_description = esc_html__('Note: There\'s nothing automatic in this offline payment system, you should use this when you don\'t want to collect money automatically. So once money is in your bank account you change the status of the order manually under "Payment History" menu.',
            'classified-listing');
        // Load the settings.
        $this->init_form_fields();

        $this->init_settings();

        // Define user set variables.
        $this->enable = $this->get_option('enable');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields() {
        $this->form_fields = [
            'enabled'      => [
                'title' => esc_html__('Enable/Disable', 'classified-listing'),
                'type'  => 'checkbox',
                'label' => esc_html__('Enable Offline Payment', 'classified-listing'),
            ],
            'title'        => [
                'title'       => esc_html__('Title', 'classified-listing'),
                'type'        => 'text',
                'description' => esc_html__('This controls the title which the user sees during checkout.', 'classified-listing'),
                'default'     => esc_html__('Direct Bank Transfer', 'classified-listing'),
            ],
            'description'  => [
                'title'       => esc_html__('Description', 'classified-listing'),
                'type'        => 'textarea',
                'class'       => 'wide-input',
                'description' => esc_html__('This controls the description which the user sees during checkout.',
                    'classified-listing'),
                'default'     => esc_html__("Make your payment directly in our bank account. Please use your Order ID as payment reference. Your order won't get approved until the funds have cleared in our account.",
                    'classified-listing'),
            ],
            'instructions' => [
                'title'             => esc_html__('Instructions', 'classified-listing'),
                'type'              => 'wysiwyg',
                'custom_attributes' => ['rows' => 13],
                'default'           => esc_html__('Make your payment directly in our bank account. Please use your Order ID as payment reference. Your order won\'t get approved until the funds have cleared in our account.
Account details :
		
Account Name : YOUR ACCOUNT NAME
Account Number : YOUR ACCOUNT NUMBER
Bank Name : YOUR BANK NAME
		
If we don\'t receive your payment within 48 hrs, we will cancel the order.', 'classified-listing'),
                'class'             => 'wide-input',
            ]
        ];
    }


    /**
     * Process the payment and return the result.
     *
     * @param Payment $order
     * @param array   $data
     *
     * @return array
     * @throws \Exception
     */
    public function process_payment($order, $data = []) {
        if (!$order instanceof Payment) {
            return [
                'result'   => 'error',
                'message'  => esc_html__('Payment not found', 'classified-listing'),
                'redirect' => null,
            ];
        }
        $order->set_transaction_id(wp_generate_password(12, true));
        $order->update_status("rtcl-pending");

        return [
            'result'   => 'success',
            'redirect' => Link::get_payment_receipt_page_link($order->get_id()),
        ];

    }


    /**
     * @return array
     */
    public function rest_api_data() {
        return [
            'id'           => $this->id,
            'title'        => strip_tags($this->get_title()),
            'icon'         => $this->get_icon_url(),
            'instructions' => $this->get_option('instructions'),
            'description'  => strip_tags($this->get_description())
        ];
    }

}