<?php


namespace Rtcl\Models;


use Exception;
use Rtcl\Helpers\Functions;
use Rtcl\Helpers\Link;
use Rtcl\Traits\SingletonTrait;

class Cart
{

    use SingletonTrait;

    /**
     * Hold the content of the cart
     *
     * @var array
     */
    protected $_cart_content = array();

    /**
     * Key of cart content stored in session
     *
     * @var string
     */
    protected $_cart_session_key = 'cart';

    /**
     * Cart total
     *
     * @var int
     */
    public $total = 0;

    /**
     * Cart subtotal
     *
     * @var int
     */
    public $subtotal = 0;


    /**
     * Constructor
     */
    public function __construct() {
        add_action('rtcl_request_handler_add_to_cart', [ $this, 'add_to_cart' ], 20);
        add_action('rtcl_request_handler_remove_cart_item', [ $this, 'remove_item' ], 20);

        add_action('rtcl_add_to_cart', [ $this, 'calculate_totals' ], 10);
        add_action('wp', [ $this, 'maybe_set_cart_cookies' ], 99); // Set cookies
        add_action('shutdown', [ $this, 'maybe_set_cart_cookies' ], 0);
        add_action('wp_loaded', [ $this, 'load_cart_init' ] );
    }

    /**
     * Init cart.
     * Get data from session and put to cart content.
     */
    function load_cart_init() {
        $this->get_cart_from_session();
    }


    public function maybe_set_cart_cookies() {

        if (!headers_sent()/* && did_action( 'wp_loaded' )*/) {
            //$this->set_cart_cookies( ! $this->is_empty() );
        }
    }

    private function set_cart_cookies($set = true) {
        if ($set) {
            Functions::setcookie('wordpress_rtcl_cart', 1);
        } elseif (isset($_COOKIE['wordpress_rtcl_cart'])) {
            Functions::setcookie('wordpress_rtcl_cart', 0, time() - HOUR_IN_SECONDS);
        }
        do_action('rtcl_set_cart_cookies', $set);
    }


    /**
     * Re-calculate cart totals and update data to session
     */
    public function calculate_totals() {

        $this->total = $this->subtotal = 0;

        if ($items = $this->get_cart()) {
            foreach ($items as $cart_id => $item) {
                $pricing = rtcl()->factory->get_pricing($item['pricing_id']);
                if (!$pricing) {
                    continue;
                }
                $subtotal = apply_filters('rtcl_calculate_sub_total', $pricing->getPrice() * $item['quantity'], $item);
                $total = $subtotal;

                $this->_cart_content[$cart_id]['subtotal'] = $subtotal;
                $this->_cart_content[$cart_id]['total'] = $total;

                $this->subtotal += $subtotal;
                $this->total += $total;
            }
        }

        // Update cart content to session
        $this->update_session();
    }


    /**
     * Update cart content to session
     */
    public function update_session() {
	    if ( empty( rtcl()->session ) ) {
		    rtcl()->initialize_session();
	    }
        rtcl()->session->set($this->_cart_session_key, $this->get_cart_for_session());
    }

    /**
     * Get cart content.
     *
     * @return array
     */
    public function get_cart() {
        if (!did_action('wp_loaded')) {
            Functions::debug(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
            _doing_it_wrong(__FUNCTION__, __('Get cart should not be called before the wp_loaded action.', 'classified-listing'), '2.3');
        }

        if (!did_action('rtcl_cart_loaded_from_session')) {
            $this->get_cart_from_session();
        }

        return array_filter((array)$this->_cart_content);
    }


    /**
     * Get all items from cart.
     *
     * @return array
     */
    public function get_items() {
        return $this->get_cart();
    }

    /**
     * Add course to cart.
     *
     * @param int   $pricing_id
     * @param int   $quantity
     * @param array $item_data
     *
     * @return mixed
     */
    public function add_to_cart($pricing_id, $quantity = 1, $item_data = array()) {
        try {
            $pricing = rtcl()->factory->get_pricing($pricing_id);

            $item_data = apply_filters('rtcl_cart_item_data', $item_data, $pricing_id);

            $cart_id = $this->generate_cart_id($pricing_id, $item_data);

            $this->_cart_content[$cart_id] = apply_filters('rtcl_add_cart_item',
                array_merge(
                    $item_data,
                    array(
                        'pricing_id' => $pricing->getId(),
                        'quantity'   => $quantity,
                        'data'       => $pricing
                    )
                )
            );

            if (did_action('wp')) {
                $this->set_cart_cookies(true);
            }

            /**
             * @see Cart::calculate_totals()
             */
            do_action('rtcl_add_to_cart', $pricing_id, $quantity, $item_data, $cart_id, $this);

            return $cart_id;
        } catch (Exception $e) {
            if ($message = $e->getMessage()) {
                Functions::add_notice($e->getMessage(), 'error');
            }

            return false;
        }
    }


    /**
     * Remove an item from cart
     *
     * @param $item_id
     *
     * @return bool
     */
    public function remove_item($item_id) {
        if (isset($this->_cart_content['items'][$item_id])) {

            do_action('rtcl_remove_cart_item', $item_id, $this);

            unset($this->_cart_content['items'][$item_id]);

            do_action('rtcl_cart_item_removed', $item_id, $this);

            $this->calculate_totals();

            return true;
        }

        return false;
    }


    /**
     * Get cart sub-total
     *
     * @return mixed
     */
    public function get_subtotal() {
        $subtotal = Functions::format_decimal($this->subtotal, true);

        return apply_filters('rtcl_cart_subtotal', $subtotal);
    }

    /**
     * Get cart total
     *
     * @return mixed
     */
    public function get_total() {
        $total = Functions::format_decimal($this->total, true);

        return apply_filters('rtcl_cart_total', $total);
    }


    /**
     * Return sub-total of cart content
     *
     * @param Pricing $pricing
     * @param int     $quantity
     *
     * @return mixed
     */
    public function get_item_subtotal($pricing, $quantity = 1) {
        $price = $pricing->getPrice();
        $row_price = $price * $quantity;
        $payment_subtotal = Functions::format_decimal($row_price, true);

        return apply_filters('rtcl_cart_item_subtotal', $payment_subtotal, $pricing, $quantity, $this);
    }


    /**
     * Clean all items from cart
     *
     * @return $this
     */
    public function empty_cart() {

        do_action('rtcl_cart_before_empty');

        $this->_cart_content = array();

        unset(rtcl()->session->order_awaiting_payment);
        unset(rtcl()->session->cart);

        do_action('rtcl_cart_emptied');

        return $this;
    }


    /**
     * Check if cart is empty or not
     *
     * @return bool
     */
    public function is_empty() {
        return sizeof($this->get_cart()) === 0;
    }

    /**
     * Get checkout url for checkout page
     * Return default url of checkout page
     *
     * @param string $type
     * @param array  $data
     *
     * @return mixed
     */
    public function get_checkout_url($type = 'submission', $data = []) {
        $checkout_url = Link::get_checkout_endpoint_url($type, isset($data['listing_id']) ? absint($data['listing_id']) : '');

        return apply_filters('rtcl_get_checkout_url', $checkout_url);
    }

    /**
     * Checks if need to payment
     * Return true if cart total greater than 0
     *
     * @return mixed
     */
    public function needs_payment() {
        return apply_filters('rtcl_cart_needs_payment', $this->total > 0, $this);
    }


    /**
     * Generate unique cart id from course id and data.
     *
     * @param int   $payment_id
     * @param mixed $data
     *
     * @return string
     */
    public function generate_cart_id($payment_id, $data = '') {
        $cart_id = array($payment_id);
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $cart_id[1] = '';
                if (is_array($value) || is_object($value)) {
                    $value = http_build_query($value);
                }
                $cart_id[1] .= trim($key) . trim($value);
            }
        }

        return apply_filters('rtcl_cart_id', md5(join('_', $cart_id)), $cart_id, $data);
    }

    /**
     * Get data from session
     *
     * @return array
     */
    public function get_cart_for_session() {
        $cart_session = array();

        if ($this->get_cart()) {
            foreach ($this->get_cart() as $key => $values) {
                $cart_session[$key] = $values;
                unset($cart_session[$key]['data']); // Unset product object
            }
        }

        return $cart_session;
    }

    /**
     * Load cart content data from session
     */
    public function get_cart_from_session() {
	    if ( empty( rtcl()->session ) ) {
		    rtcl()->initialize_session();
	    }
        if (!did_action('rtcl_get_cart_from_session')) {

            if ($cart = rtcl()->session->get($this->_cart_session_key)) {
                foreach ($cart as $cart_id => $values) {
                    if (!empty($values['pricing_id'])) {
                        $pricing = rtcl()->factory->get_pricing($values['pricing_id']);
                        if ($pricing && $pricing->exists() && $values['quantity'] > 0) {
                            $data = array_merge($values, array('data' => $pricing));
                            $this->_cart_content[$cart_id] = apply_filters('rtcl_get_cart_item_from_session', $data, $values, $cart_id);

                        }
                    }
                }
            }

            do_action('rtcl_cart_loaded_from_session');
            rtcl()->session->set('cart', $this->get_cart_for_session());
            do_action('rtcl_get_cart_from_session');

            // Update total
            $this->calculate_totals();
        }
    }
}