<?php
/**
 * Plugin Name: EBSNext Online ePay
 * Plugin URI: https://
 * Description: NextTech Online ePay payment gateway for WooCommerce
 * Version: 1.0.1
 * Author: Tradesouthwest
 * Author URI: https://tradesouthwest.com
 * Text Domain: ebsnext-online-classic
 *
 * @author ebsnext
 * @package ebsnext_online_classic
 */

define( 'EBSNEXT_PATH', dirname( __FILE__ ) );
define( 'EBSNEXT_VERSION', '1.0.1' );

add_action( 'plugins_loaded', 'init_ebsnext_online_classic', 0 );

/**
 * Initilize ebsnext Online Classic
 *
 * @return void
 */
function init_ebsnext_online_classic() {
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        return;
    }

    include( EBSNEXT_PATH . '/lib/ebsnext-online-classic-soap.php' );
    include( EBSNEXT_PATH . '/lib/ebsnext-online-classic-helper.php' );
    include( EBSNEXT_PATH . '/lib/ebsnext-online-classic-log.php' );
    include( EBSNEXT_PATH . '/nextpay-authenticate.php' );

    /**
     * Gateway class
     **/
    class Ebsnext_Online_Classic extends WC_Payment_Gateway {
        /**
         * Singleton instance
         *
         * @var Ebsnext_Online_Classic
         */
        private static $_instance;

        /**
         * @param Ebsnext_Online_Classic_Log
         */
        private $_boclassic_log;

        /**
         * get_instance
         *
         * Returns a new instance of self, if it does not already exist.
         *
         * @access public
         * @static
         * @return Ebsnext_Online_Classic
         */
        public static function get_instance() {
            if ( ! isset( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Construct
         */
        public function __construct() {
            $this->id = 'znextech'; //epay_dk 
            $this->method_title = 'Ebsnext Online ePay';
            $this->method_description = 'Ebsnext Online ePay enables easy and secure payments on your shop';
            $this->icon = WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/ebsnext-logo.svg';
            $this->has_fields = false;

            $this->supports = array(
                'products',
                'subscriptions',
                'subscription_cancellation',
                'subscription_suspension',
                'subscription_reactivation',
                'subscription_amount_changes',
                'subscription_date_changes',
                'subscription_payment_method_change_customer',
                'multiple_subscriptions'
                );

            // Init the Ebsnext Online Classic logger
            $this->_boclassic_log = new Ebsnext_Online_Classic_Log();

            // Load the form fields.
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Initilize Ebsnext Online Classic Settings
            $this->init_ebsnext_online_classic_settings();

            if ( $this->remoteinterface === 'yes' ) {
                $this->supports = array_merge( $this->supports, array( 'refunds' ) );
            }
        }

        /**
         * Initilize Ebsnext Online Classic Settings
         */
        public function init_ebsnext_online_classic_settings() {
            // Define user set variables
            $this->enabled = array_key_exists( 'enabled', $this->settings ) ? $this->settings['enabled'] : 'yes';
            $this->title = array_key_exists( 'title', $this->settings ) ? $this->settings['title'] : 'Ebsnext Online ePay';
            $this->description = array_key_exists( 'description', $this->settings ) ? $this->settings['description'] : 'Pay using Ebsnext Online ePay';
            $this->merchant = array_key_exists( 'merchant', $this->settings ) ? $this->settings['merchant'] : '';
            $this->windowid = array_key_exists( 'windowid', $this->settings ) ? $this->settings['windowid'] : '1';
            $this->md5key = array_key_exists( 'md5key', $this->settings ) ? $this->settings['md5key'] : '';
            $this->instantcapture = array_key_exists( 'instantcapture', $this->settings ) ? $this->settings['instantcapture'] : 'no';
            $this->group = array_key_exists( 'group', $this->settings ) ? $this->settings['group'] : '';
            $this->authmail = array_key_exists( 'authmail', $this->settings ) ? $this->settings['authmail'] : '';
            $this->ownreceipt = array_key_exists( 'ownreceipt', $this->settings ) ? $this->settings['ownreceipt'] : 'no';
            $this->remoteinterface = array_key_exists( 'remoteinterface', $this->settings ) ? $this->settings['remoteinterface'] : 'no';
            $this->remotepassword = array_key_exists( 'remotepassword', $this->settings ) ? $this->settings['remotepassword'] : '';
            $this->enableinvoice = array_key_exists( 'enableinvoice', $this->settings ) ? $this->settings['enableinvoice'] : 'no';
            $this->addfeetoorder = array_key_exists( 'addfeetoorder', $this->settings ) ? $this->settings['addfeetoorder'] : 'no';
            $this->enablemobilepaymentwindow = array_key_exists( 'enablemobilepaymentwindow', $this->settings ) ? $this->settings['enablemobilepaymentwindow'] : 'yes';
            $this->roundingmode = array_key_exists( 'roundingmode', $this->settings ) ? $this->settings['roundingmode'] : Ebsnext_Online_Classic_Helper::ROUND_DEFAULT;
            $this->captureonstatuscomplete = array_key_exists( 'captureonstatuscomplete', $this->settings ) ? $this->settings['captureonstatuscomplete'] : 'no';
            $this->override_subscription_need_payment = array_key_exists( 'overridesubscriptionneedpayment', $this->settings ) ? $this->settings['overridesubscriptionneedpayment'] : 'yes';
        }

        /**
         * Init hooks
         */
        public function init_hooks() {
            // Actions
            add_action( 'woocommerce_api_' . strtolower( get_class() ), array( $this, 'ebsnext_online_classic_callback' ) );
            add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
            if ( is_admin() ) {
                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

                if( $this->remoteinterface == 'yes' ) {
                    add_action( 'add_meta_boxes', array( $this, 'ebsnext_online_classic_meta_boxes' ) );
                    add_action( 'wp_before_admin_bar_render', array( $this, 'ebsnext_online_classic_actions' ) );
                    add_action( 'admin_notices', array( $this, 'ebsnext_online_classic_admin_notices' ) );

                    if($this->captureonstatuscomplete === 'yes') {
                        add_action( 'woocommerce_order_status_completed', array( $this, 'ebsnext_online_classic_order_status_completed' ) );
                    }
                }
            }

            if ( class_exists( 'WC_Subscriptions_Order' ) ) {
                // Subscriptions
                add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );
                add_action( 'woocommerce_subscription_cancelled_' . $this->id, array( $this, 'subscription_cancellation' ) );

                if( !is_admin() && $this->override_subscription_need_payment === 'yes') {
                    // Maybe order don't need payment because lock.
                    add_filter( 'woocommerce_order_needs_payment', array( $this, 'maybe_override_needs_payment' ), 10, 2 );
                }
            }
            // Register styles!
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_wc_ebsnext_online_classic_admin_styles_and_scripts' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_wc_ebsnext_online_classic_front_styles' ) );
        }

        /**
         * Show messages in the Administration
         */
        public function ebsnext_online_classic_admin_notices(){
            Ebsnext_Online_Classic_Helper::echo_admin_notices();
        }

        /**
         * Enqueue Admin Styles and Scripts
         */
        public function enqueue_wc_ebsnext_online_classic_admin_styles_and_scripts() {
            wp_register_style( 'ebsnext_online_classic_admin_style', plugins_url( 'ebsnext-online-classic/style/ebsnext-online-classic-admin.css' ) );
            wp_enqueue_style( 'ebsnext_online_classic_admin_style' );

            // Fix for load of Jquery time!
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'ebsnext_online_classic_admin', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/scripts/ebsnext-online-classic-admin.js' );
        }

        /**
         * Enqueue Frontend Styles and Scripts
         */
        public function enqueue_wc_ebsnext_online_classic_front_styles() {
            wp_register_style( 'ebsnext_online_classic_front_style', plugins_url( 'ebsnext-online-classic/style/ebsnext-online-classic-front.css' ) );
            wp_enqueue_style( 'ebsnext_online_classic_front_style' );
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Activate module',
                    'type' => 'checkbox',
                    'label' => 'Enable Ebsnext Online ePay as a payment option.',
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'description' => 'The title of the payment method displayed to the customers.',
                    'default' => 'EBS Online ePay'
                ),
                'description' => array(
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'The description of the payment method displayed to the customers.',
                    'default' => 'Pay using Ebsnext Online ePay'
                ),
                'merchant' => array(
                    'title' => 'Merchant number',
                    'type' => 'text',
                    'description' => 'The number identifying your ePay merchant account.',
                    'default' => ''
                ),
                'windowid' => array(
                    'title' => 'Window ID',
                    'type' => 'text',
                    'description' => 'The ID of the payment window to use.',
                    'default' => '1'
                ),
                'md5key' => array(
                    'title' => 'MD5 Key',
                    'type' => 'text',
                    'description' => 'The MD5 key is used to stamp data sent between WooCommerce and Ebsnext to prevent it from being tampered with. The MD5 key is optional but if used here, must be the same as in the ePay administration.',
                    'default' => ''
                ),
                'remotepassword' => array(
                    'title' => 'Remote password',
                    'type' => 'password',
                    'description' => 'if a Remote password is set in the ePay administration, then the same password must be entered here',
                    'default' => '',
                    'custom_attributes' => array( 'autocomplete' => 'new-password' ) // Fix for input field gets populated with saved login info
                ),
                /*
                'ownreceipt' => array(
                    'title' => 'Own receipt',
                    'type' => 'checkbox',
                    'description' => 'Immediately redirect your customer back to you shop after the payment completed.',
                    'default' => 'no'
                ),
                
                'remoteinterface' => array(
                    'title' => 'Remote interface',
                    'type' => 'checkbox',
                    'description' => 'Use remote interface',
                    'default' => 'no'
                ),
                
                'captureonstatuscomplete' => array(
                    'title' => 'Capture on status Completed',
                    'type' => 'checkbox',
                    'description' => 'When this is enabled the full payment will be captured when the order status changes to Completed',
                    'default' => 'no'
                ),  */
                'roundingmode' => array(
                    'title' => 'Rounding mode',
                    'type' => 'select',
                    'description' => 'Please select how you want the rounding of the amount sendt to the payment system',
                    'options' => array( Ebsnext_Online_Classic_Helper::ROUND_DEFAULT => 'Default', Ebsnext_Online_Classic_Helper::ROUND_UP => 'Always up', Ebsnext_Online_Classic_Helper::ROUND_DOWN => 'Always down' ),
                    'default' => 'normal'
                ),
            );
        }

        /**
         * Admin Panel Options
         */
        public function admin_options() {
            $version = EBSNEXT_VERSION;

            $html = "<h3>EBS NextPay Online ePay v{$version}</h3>";
            $html .= Ebsnext_Online_Classic_Helper::create_admin_debug_section();
            $html .= '<h3 class="wc-settings-sub-title">Module configuration</h3>';
            $html .= '<table class="form-table">';

            // Generate the HTML For the settings form.!
            $html .= $this->generate_settings_html( array(), false );
            $html .= '</table>';

            echo ent2ncr( $html );
        }

        /**
         * When using a coupon for x free payments after the initial trial on a subscription then this will set the payment requirement to true
         *
         * @param bool     $needs_payment
         * @param WC_Order $order
         * @return bool
         */
        public function maybe_override_needs_payment( $needs_payment, $order ) {

            if (!$needs_payment && $this->id === $order->get_payment_method() &&  Ebsnext_Online_Classic_Helper::get_order_contains_subscription( $order, array( 'parent' ) ) ) {
                $needs_payment = true;
            }

            return $needs_payment;
        }

        /**
         * Capture the payment on order status completed
         * @param mixed $order_id
         */
        public function ebsnext_online_classic_order_status_completed($order_id){
            if( !$this->module_check( $order_id ) ) {
                return;
            }

            $order = wc_get_order( $order_id );
            $order_total = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_total() : $order->order_total;
            $capture_result = $this->ebsnext_online_classic_capture_payment($order_id, $order_total, '');

            if ( is_wp_error( $capture_result ) ) {
                $message = $capture_result->get_error_message( 'ebsnext_online_classic_error' );
                $this->_boclassic_log->add( $message );
                Ebsnext_Online_Classic_Helper::add_admin_notices(Ebsnext_Online_Classic_Helper::ERROR, $message);
            } else {
                $message = sprintf( __( 'The Capture action was a success for order %s', 'ebsnext-online-classic' ), $order_id );
                Ebsnext_Online_Classic_Helper::add_admin_notices(Ebsnext_Online_Classic_Helper::SUCCESS, $message);
            }
        }


        /**
         * There are no payment fields for epay, but we want to show the description if set.
         **/
        public function payment_fields() {
            $text_replace = wptexturize( $this->description );
            $paymentFieldDescription = wpautop( $text_replace );
            $paymentLogoes = '<div id="boclassic_card_logos">';
            $merchant_number = $this->merchant;
            if ( $merchant_number ) {
                $paymentLogoes .= '';
            }
            $paymentLogoes .= '</div>';
            $paymentFieldDescription .= $paymentLogoes;
            echo $paymentFieldDescription;
        }

        /**
         * Create invoice lines
         *
         * @param WC_Order $order
         * @param int      $minorunits
         * @return string
         * */
        protected function create_invoice( $order, $minorunits ) {
            if ( $this->enableinvoice == 'yes' ) {
                if ( Ebsnext_Online_Classic_Helper::is_woocommerce_3() ) {
                    $invoice['customer']['emailaddress'] = $order->get_billing_email();
                    $invoice['customer']['firstname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_billing_first_name() );
                    $invoice['customer']['lastname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_billing_last_name() );
                    $invoice['customer']['address'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_billing_address_1() );
                    $invoice['customer']['zip'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_billing_postcode() );
                    $invoice['customer']['city'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_billing_city() );
                    $invoice['customer']['country'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_billing_country() );

                    $invoice['shippingaddress']['firstname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_shipping_first_name() );
                    $invoice['shippingaddress']['lastname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_shipping_last_name() );
                    $invoice['shippingaddress']['address'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_shipping_address_1() );
                    $invoice['shippingaddress']['zip'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_shipping_postcode() );
                    $invoice['shippingaddress']['city'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_shipping_city() );
                    $invoice['shippingaddress']['country'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->get_shipping_country() );
                } else {
                    $invoice['customer']['emailaddress'] = $order->billing_email;
                    $invoice['customer']['firstname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->billing_first_name );
                    $invoice['customer']['lastname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->billing_last_name );
                    $invoice['customer']['address'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->billing_address_1 );
                    $invoice['customer']['zip'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->billing_postcode );
                    $invoice['customer']['city'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->billing_city );
                    $invoice['customer']['country'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->billing_country );

                    $invoice['shippingaddress']['firstname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->shipping_first_name );
                    $invoice['shippingaddress']['lastname'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->shipping_last_name );
                    $invoice['shippingaddress']['address'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->shipping_address_1 );
                    $invoice['shippingaddress']['zip'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->shipping_postcode );
                    $invoice['shippingaddress']['city'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->shipping_city );
                    $invoice['shippingaddress']['country'] = Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $order->shipping_country );
                }
                $invoice['lines'] = $this->create_invoice_order_lines( $order, $minorunits );

                return wp_json_encode( $invoice, JSON_UNESCAPED_UNICODE );
            } else {
                return '';
            }
        }

        /**
         * Create Ebsnext Online Classic orderlines for invoice
         *
         * @param WC_Order $order
         * @return array
         */
        protected function create_invoice_order_lines( $order, $minorunits ) {
            $items = $order->get_items();
            $invoice_order_lines = array();
            foreach ( $items as $item ) {
                $item_total = $order->get_line_total( $item, false, true );
                if($item['qty'] > 1) {
                    $item_price = $item_total / $item['qty'];
                } else {
                    $item_price = $item_total;
                }
                $item_vat_amount = $order->get_line_tax( $item );
                $invoice_order_lines[] = array(
                        'id' => $item['product_id'],
                        'description' => Ebsnext_Online_Classic_Helper::json_value_remove_special_characters( $item['name'] ),
                        'quantity' => $item['qty'],
                        'price' => Ebsnext_Online_Classic_Helper::convert_price_to_minorunits( $item_price, $minorunits, $this->roundingmode ),
                        'vat' => $item_vat_amount > 0 ? ( $item_vat_amount / $item_total ) * 100 : 0,
                    );
            }
            $shipping_methods = $order->get_shipping_methods();
            if ( $shipping_methods && count( $shipping_methods ) !== 0 ) {
                $shipping_total = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_shipping_total() : $order->get_total_shipping();
                $shipping_tax = (float) $order->get_shipping_tax();
                $shipping_method = reset( $shipping_methods );
                $invoice_order_lines[] = array(
                        'id' => $shipping_method->get_method_id(),
                        'description' => $shipping_method->get_method_title(),
                        'quantity' => 1,
                        'price' => Ebsnext_Online_Classic_Helper::convert_price_to_minorunits( $shipping_total, $minorunits, $this->roundingmode ),
                        'vat' => $shipping_tax > 0 ? ( $shipping_tax / $shipping_total ) * 100  : 0,
                    );
            }

            return $invoice_order_lines;
        }

        /**
         * Process the payment and return the result
         *
         * @param int $order_id
         * @return string[]
         */
        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );

            return array(
                'result' => 'success',
                'redirect' => $order->get_checkout_payment_url( true ),
            );
        }

        /**
         * Process Refund
         *
         * @param int        $order_id
         * @param float|null $amount
         * @param string     $reason
         * @return bool
         */
        public function process_refund( $order_id, $amount = null, $reason = '' ) {
            if ( ! isset( $amount ) ) {
                return true;
            }

            $refund_result = $this->ebsnext_online_classic_refund_payment($order_id, $amount, '');
            if ( is_wp_error( $refund_result ) ) {
                return $refund_result;
            } else {
                $message = __( "The Refund action was a success for order {$order_id}", 'ebsnext-online-classic' );
                Ebsnext_Online_Classic_Helper::add_admin_notices(Ebsnext_Online_Classic_Helper::SUCCESS, $message);
            }

            return true;
        }

        /**
         * Handle scheduled subscription payments
         *
         * @param mixed    $amount_to_charge
         * @param WC_Order $renewal_order
         */
        public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {
            $subscription = Ebsnext_Online_Classic_Helper::get_subscriptions_for_renewal_order( $renewal_order );
            $result = $this->process_subscription_payment( $amount_to_charge, $renewal_order, $subscription );
            $renewal_order_id = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $renewal_order->get_id() : $renewal_order->id;

            // Remove the Ebsnext Online Classic subscription id copyid from the subscription
            delete_post_meta( $renewal_order_id, Ebsnext_Online_Classic_Helper::EBSNEXT_ONLINE_CLASSIC_SUBSCRIPTION_ID );

            if ( is_wp_error( $result ) ) {
                $message = sprintf( __( 'Ebsnext Online ePay Subscription could not be authorized for renewal order # %s - %s', 'ebsnext-online-classic' ), $renewal_order_id, $result->get_error_message( 'ebsnext_online_classic_error' ) );
                $renewal_order->update_status( 'failed', $message );
                $this->_boclassic_log->add( $message );
            }
        }

        /**
         * Process a subscription renewal
         *
         * @param mixed           $amount
         * @param WC_Order        $renewal_order
         * @param WC_Subscription $subscription
         */
        public function process_subscription_payment( $amount, $renewal_order, $subscription ) {
            try {
                $ebsnext_subscription_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_subscription_id( $subscription );
                if ( strlen( $ebsnext_subscription_id ) === 0 ) {
                    return new WP_Error( 'ebsnext_online_classic_error', __( 'Ebsnext Online ePay Subscription id was not found', 'ebsnext-online-classic' ) );
                }

                $order_currency = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $renewal_order->get_currency() : $renewal_order->get_order_currency();
                $minorunits = Ebsnext_Online_Classic_Helper::get_currency_minorunits( $order_currency );
                $amount = Ebsnext_Online_Classic_Helper::convert_price_to_minorunits( $amount, $minorunits, $this->roundingmode );
                $renewal_order_id = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $renewal_order->get_id() : $renewal_order->id;

                $webservice = new Ebsnext_Online_Classic_Soap( $this->remotepassword, true );
                $authorize_response = $webservice->authorize( $this->merchant, $ebsnext_subscription_id, $renewal_order_id, $amount, Ebsnext_Online_Classic_Helper::get_iso_code( $order_currency ), (bool) Ebsnext_Online_Classic_Helper::yes_no_to_int( $this->instantcapture ), $this->group, $this->authmail );
                if ( $authorize_response->authorizeResult === false ) {
                    $error_message = '';
                    if ( $authorize_response->epayresponse != '-1' ) {
                        $error_message = $webservice->get_epay_error( $this->merchant, $authorize_response->epayresponse );
                    } elseif ( $authorize_response->pbsresponse != '-1' ) {
                        $error_message = $webservice->get_pbs_error( $this->merchant, $authorize_response->pbsresponse );
                    }
                    return new WP_Error( 'ebsnext_online_classic_error', $error_message );
                }
                $renewal_order->payment_complete( $authorize_response->transactionid );

                // Add order note
                $message = sprintf( __( 'Ebsnext Online ePay Subscription was authorized for renewal order %s with transaction id %s','ebsnext-online-classic' ), $renewal_order_id, $authorize_response->transactionid );
                $renewal_order->add_order_note( $message );
                $subscription->add_order_note( $message );

                return true;
            }
            catch ( Exception $ex ) {
                return new WP_Error( 'ebsnext_online_classic_error', $ex->getMessage() );
            }
        }

        /**
         * Cancel a subscription
         *
         * @param WC_Subscription $subscription
         * @param bool            $force_delete
         */
        public function subscription_cancellation( $subscription, $force_delete = false ) {
            if ( 'cancelled' === $subscription->get_status() || $force_delete ) {
                $result = $this->process_subscription_cancellation( $subscription );

                if ( is_wp_error( $result ) ) {
                    $message = sprintf( __( 'Ebsnext Online ePay Subscription could not be canceled - %s', 'ebsnext-online-classic' ), $result->get_error_message( 'ebsnext_online_classic_error' ) );
                    $subscription->add_order_note( $message );
                    $this->_boclassic_log->add( $message );
                }
            }
        }

        /**
         * Process canceling of a subscription
         *
         * @param WC_Subscription $subscription
         */
        protected function process_subscription_cancellation( $subscription ) {
            try {
                if ( Ebsnext_Online_Classic_Helper::order_is_subscription( $subscription ) ) {
                    $ebsnext_subscription_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_subscription_id( $subscription );
                    if ( strlen( $ebsnext_subscription_id ) === 0 ) {
                        $order_note = __( 'Ebsnext Online ePay Subscription ID was not found', 'ebsnext-online-classic' );
                        return new WP_Error( 'ebsnext_online_classic_error', $order_note );
                    }

                    $webservice = new Ebsnext_Online_Classic_Soap( $this->remotepassword, true );
                    $delete_subscription_response = $webservice->delete_subscription( $this->merchant, $ebsnext_subscription_id );
                    if ( $delete_subscription_response->deletesubscriptionResult === true ) {
                        $subscription->add_order_note( sprintf( __( 'Subscription successfully Canceled. - Ebsnext Online ePay Subscription Id: %s', 'ebsnext-online-classic' ), $ebsnext_subscription_id ) );
                    } else {
                        $order_note = sprintf( __( 'Ebsnext Online ePay Subscription Id: %s', 'ebsnext-online-classic' ), $ebsnext_subscription_id );
                        if ( $delete_subscription_response->epayresponse != '-1' ) {
                            $order_note .= ' - ' . $webservice->get_epay_error( $this->merchant, $delete_subscription_response->epayresponse );
                        }
                        return new WP_Error( 'ebsnext_online_classic_error', $order_note );
                    }
                }
                return true;
            }
            catch ( Exception $ex ) {
                return new WP_Error( 'ebsnext_online_classic_error', $ex->getMessage() );
            }
        }

        /**
         * receipt_page
         **/
        public function receipt_page( $order_id ) {
            $order = wc_get_order( $order_id );
            $is_request_to_change_payment_method = Ebsnext_Online_Classic_Helper::order_is_subscription( $order );

            $order_currency = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_currency() : $order->get_order_currency();
            $order_total = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_total() : $order->order_total;
            $minorunits = Ebsnext_Online_Classic_Helper::get_currency_minorunits( $order_currency );

            $epay_args = array(
                'encoding' => 'UTF-8',
                'cms' => Ebsnext_Online_Classic_Helper::get_module_header_info(),
                'windowstate' => "3",
                'mobile' => Ebsnext_Online_Classic_Helper::yes_no_to_int( $this->enablemobilepaymentwindow ),
                'merchantnumber' => $this->merchant,
                'windowid' => $this->windowid,
                'currency' => $order_currency,
                'amount' => Ebsnext_Online_Classic_Helper::convert_price_to_minorunits( $order_total, $minorunits, $this->roundingmode ),
                'orderid' => $this->clean_order_number($order->get_order_number()),
                'accepturl' => Ebsnext_Online_Classic_Helper::get_accept_url( $order ),
                'cancelurl' => Ebsnext_Online_Classic_Helper::get_decline_url( $order ),
                'callbackurl' => apply_filters( 'ebsnext_online_classic_callback_url', Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_callback_url( $order_id ) ),
                'mailreceipt' => $this->authmail,
                'instantcapture' => Ebsnext_Online_Classic_Helper::yes_no_to_int( $this->instantcapture ),
                'group' => $this->group,
                'language' => Ebsnext_Online_Classic_Helper::get_language_code( get_locale() ),
                'ownreceipt' => Ebsnext_Online_Classic_Helper::yes_no_to_int( $this->ownreceipt ),
                'timeout' => '60',
            );

            if ( ! $is_request_to_change_payment_method ) {
                $epay_args['invoice'] = $this->create_invoice( $order, $minorunits );
            }

            if ( Ebsnext_Online_Classic_Helper::woocommerce_subscription_plugin_is_active() && ( Ebsnext_Online_Classic_Helper::order_contains_subscription( $order ) || $is_request_to_change_payment_method ) ) {
                $epay_args['subscription'] = 1;
            }

            if ( strlen( $this->md5key ) > 0 ) {
                $hash = '';
                foreach ( $epay_args as $value ) {
                    $hash .= $value;
                }
                $epay_args['hash'] = md5( $hash . $this->md5key );
            }

            $epay_args_json = wp_json_encode( $epay_args );
            $payment_html = Ebsnext_Online_Classic_Helper::create_ebsnext_online_classic_payment_html( $epay_args_json );

            echo ent2ncr( $payment_html );
        }

        /**
         * Removes any special charactors from the order number
         *
         * @param string $order_number
         * @return string
         */
        protected function clean_order_number($order_number) {
            return preg_replace('/[^a-z\d ]/i', "", $order_number );
        }

        /**
         * Check for epay IPN Response
         **/
        public function ebsnext_online_classic_callback() {
            $params = stripslashes_deep( $_GET );
            $message = '';
            $order = null;
            $response_code = 400;
            try {
                $is_valid_call = Ebsnext_Online_Classic_Helper::validate_ebsnext_online_classic_callback_params( $params, $this->md5key, $order, $message );
                if ( $is_valid_call ) {
                    $message = $this->process_ebsnext_online_classic_callback( $order, $params );
                    $response_code = 200;
                } else {
                    if ( ! empty( $order ) ) {
                        $order->update_status( 'failed', $message );
                    }
                    $this->_boclassic_log->separator();
                    $this->_boclassic_log->add( "Callback failed - {$message} - GET params:" );
                    $this->_boclassic_log->add( $params );
                    $this->_boclassic_log->separator();
                }
            }
            catch (Exception $ex) {
                $message = 'Callback failed Reason: ' . $ex->getMessage();
                $response_code = 500;
                $this->_boclassic_log->separator();
                $this->_boclassic_log->add( "Callback failed - {$message} - GET params:" );
                $this->_boclassic_log->add( $params );
                $this->_boclassic_log->separator();
            }

            $header = 'X-EPay-System: ' . Ebsnext_Online_Classic_Helper::get_module_header_info();
            header( $header, true, $response_code );
            die( $message );

        }

        /**
         * Process the Ebsnext Callback
         *
         * @param WC_Order $order
         * @param mixed    $ebsnext_transaction
         */
        protected function process_ebsnext_online_classic_callback( $order, $params ) {
            try {
                $type = '';
                $ebsnext_subscription_id = array_key_exists( 'subscriptionid', $params ) ? $params['subscriptionid'] : null;
                if ( ( Ebsnext_Online_Classic_Helper::order_contains_subscription( $order ) || Ebsnext_Online_Classic_Helper::order_is_subscription( $order ) ) && isset( $ebsnext_subscription_id ) ) {
                    $action = $this->process_subscription( $order, $params );
                    $type = "Subscription {$action}";
                } else {
                    $action = $this->process_standard_payments( $order, $params );
                    $type = "Standard Payment {$action}";
                }
            }
            catch ( Exception $e ) {
                throw $e;
            }

            return  "Ebsnext Online ePay Callback completed - {$type}";
        }

        /**
         * Process standard payments
         *
         * @param WC_Order $order
         * @param array    $params
         * @return string
         */
        protected function process_standard_payments( $order, $params ) {
            $action = '';
            $old_transaction_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_transaction_id( $order );
            if ( empty( $old_transaction_id ) ) {
                $this->add_surcharge_fee_to_order( $order, $params );
                $order->add_order_note( sprintf( __( 'Ebsnext Online ePay Payment completed with transaction id %s', 'ebsnext-online-classic' ), $params['txnid'] ) );
                $this->add_or_update_payment_type_id_to_order( $order, $params['paymenttype'] );
                $action = 'created';
            } else {
                $action = 'created (Called multiple times)';
            }
            $order->payment_complete( $params['txnid'] );
            return $action;
        }

        /**
         * Process the subscription
         *
         * @param WC_Order|WC_Subscription $order
         * @param array                    $params
         * @return string
         */
        protected function process_subscription( $order, $params ) {
            $action = '';
            $ebsnext_subscription_id = $params['subscriptionid'];
            if ( Ebsnext_Online_Classic_Helper::order_is_subscription( $order ) ) {
                // Do not cancel subscription if the callback is called more than once !
                $old_ebsnext_subscription_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_subscription_id( $order );
                if ( $ebsnext_subscription_id != $old_ebsnext_subscription_id ) {
                    $this->subscription_cancellation( $order, true );
                    $action = 'changed';
                    $order->add_order_note( sprintf( __( 'Ebsnext Online ePay Subscription changed from: %s to: %s', 'ebsnext-online-classic' ), $old_ebsnext_subscription_id, $ebsnext_subscription_id ) );
                    $order->payment_complete();
                    $this->save_subscription_meta( $order, $ebsnext_subscription_id, true );
                } else {
                    $action = 'changed (Called multiple times)';
                }
            } else {
                // Do not add surcharge if the callback is called more than once!
                $old_transaction_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_transaction_id( $order );
                $ebsnext_transaction_id = $params['txnid'];
                if ( $ebsnext_transaction_id != $old_transaction_id ) {
                    $this->add_surcharge_fee_to_order( $order, $params );
                    $action = 'activated';
                    $order->add_order_note( sprintf( __( 'Ebsnext Online ePay Subscription activated with subscription id: %s', 'ebsnext-online-classic' ), $ebsnext_subscription_id ) );
                    $order->payment_complete( $ebsnext_transaction_id );
                    $this->save_subscription_meta( $order, $ebsnext_subscription_id, false );
                    $this->add_or_update_payment_type_id_to_order( $order, $params['paymenttype'] );
                    do_action( 'processed_subscription_payments_for_order', $order );
                } else {
                    $action = 'activated (Called multiple times)';
                }
            }

            return $action;
        }

        /**
         * Add surcharge to order
         *
         * @param WC_Order $order
         * @param array    $params
         */
        protected function add_surcharge_fee_to_order( $order, $params ) {
            $order_currency = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_currency() : $order->get_order_currency;
            $minorunits = Ebsnext_Online_Classic_Helper::get_currency_minorunits( $order_currency );
            $fee_amount_in_minorunits = $params['txnfee'];
            if ( $fee_amount_in_minorunits > 0 && $this->addfeetoorder === 'yes' ) {
                $fee_amount = Ebsnext_Online_Classic_Helper::convert_price_from_minorunits( $fee_amount_in_minorunits, $minorunits );
                $fee = (object) array(
                    'name'          => __( 'Surcharge Fee', 'ebsnext-online-classic' ),
                    'amount'        => $fee_amount,
                    'taxable'       => false,
                    'tax_class'     => null,
                    'tax_data'      => array(),
                    'tax'           => 0,
                    );
                if ( ! Ebsnext_Online_Classic_Helper::is_woocommerce_3() ) {
                    $order->add_fee( $fee );
                } else {
                    $fee_item = new WC_Order_Item_Fee();
                    $fee_item->set_props( array(
                        'name' => $fee->name,
                        'tax_class' => $fee->tax_class,
                        'total' => $fee->amount,
                        'total_tax' => $fee->tax,
                        'order_id' => $order->get_id(),
                        )
                    );
                    $fee_item->save();
                    $order->add_item( $fee_item );
                }

                $total_incl_fee = ( Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_total() : $order->order_total ) + $fee_amount;
                $order->set_total( $total_incl_fee );
            }
        }

        /**
         * Add Payment Type id Meta to the order
         * @param WC_Order $order
         * @param mixed $payment_type_id
         * @return void
         */
        protected function add_or_update_payment_type_id_to_order( $order, $payment_type_id) {
            $order_id = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_id() : $order->id;
            $existing_payment_type_id = get_post_meta($order_id, Ebsnext_Online_Classic_Helper::EBSNEXT_ONLINE_CLASSIC_PAYMENT_TYPE_ID, true);

            if(!isset($existing_payment_type_id) || $existing_payment_type_id !== $payment_type_id) {
                update_post_meta( $order_id, Ebsnext_Online_Classic_Helper::EBSNEXT_ONLINE_CLASSIC_PAYMENT_TYPE_ID, $payment_type_id );
            }
        }

        /**
         * Store the Ebsnext Online Classic subscription id on subscriptions in the order.
         *
         * @param WC_Order $order_id
         * @param string   $ebsnext_subscription_id
         * @param bool     $is_subscription
         */
        protected function save_subscription_meta( $order, $ebsnext_subscription_id, $is_subscription ) {
            $ebsnext_subscription_id = wc_clean( $ebsnext_subscription_id );
            $order_id = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_id() : $order->id;
            if ( $is_subscription ) {
                update_post_meta( $order_id, Ebsnext_Online_Classic_Helper::EBSNEXT_ONLINE_CLASSIC_SUBSCRIPTION_ID, $ebsnext_subscription_id );
            } else {
                // Also store it on the subscriptions being purchased in the order
                $subscriptions = Ebsnext_Online_Classic_Helper::get_subscriptions_for_order( $order_id );
                foreach ( $subscriptions as $subscription ) {
                    $wc_subscription_id = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $subscription->get_id() : $subscription->id;
                    update_post_meta( $wc_subscription_id, Ebsnext_Online_Classic_Helper::EBSNEXT_ONLINE_CLASSIC_SUBSCRIPTION_ID, $ebsnext_subscription_id );
                    $subscription->add_order_note( sprintf( __( 'Ebsnext Online ePay Subscription activated with subscription id: %s by order %s', 'ebsnext-online-classic' ), $ebsnext_subscription_id, $order_id ) );
                }
            }
        }

        /**
         * Handle Ebsnext Online Classic Actions
         */
        public function ebsnext_online_classic_actions() {
            if ( isset( $_GET['boclassicaction'] ) && isset( $_GET['boclassicnonce'] ) && wp_verify_nonce( $_GET['boclassicnonce'], 'boclassic_process_payment_action' ) ) {
                $params = $_GET;
                $result = $this->process_ebsnext_online_classic_action( $params );
                $action = $params['boclassicaction'];
                $order_id = isset( $params['post'] ) ?  $params['post'] : '-1';
                if ( is_wp_error( $result ) ) {
                    $message = $result->get_error_message( 'ebsnext_online_classic_error' );
                    $this->_boclassic_log->add( $message );
                    Ebsnext_Online_Classic_Helper::add_admin_notices(Ebsnext_Online_Classic_Helper::ERROR, $message);
                } else {
                    global $post;
                    $message = sprintf( __( 'The %s action was a success for order %s', 'ebsnext-online-classic' ), $action, $order_id );
                    Ebsnext_Online_Classic_Helper::add_admin_notices(Ebsnext_Online_Classic_Helper::SUCCESS, $message, true);
                    $url = admin_url( 'post.php?post=' . $post->ID . '&action=edit' );
                    wp_safe_redirect( $url );
                }
            }
        }

        /**
         * Validate Action params
         *
         * @param array  $get_params
         * @param string $failed_message
         * @return bool
         */
        protected function validate_ebsnext_online_classic_action( $get_params, &$failed_message ) {
            $required_params = array(
                'boclassicaction',
                'post',
                'currency',
                'amount',
            );
            foreach ( $required_params as $required_param ) {
                if ( ! array_key_exists( $required_param, $get_params ) || empty( $get_params[ $required_param ] ) ) {
                    $failed_message = $required_param;
                    return false;
                }
            }
            return true;
        }

        /**
         * Process the action
         *
         * @param array $params
         * @return bool|WP_Error
         */
        protected function process_ebsnext_online_classic_action( $params ) {
            $failed_message = '';
            if ( ! $this->validate_ebsnext_online_classic_action( $params, $failed_message ) ) {
                return new WP_Error( 'ebsnext_online_classic_error', sprintf( __( 'The following get parameter was not provided "%s"' ), $failed_message ) );
            }

            try {
                $order_id = $params['post'];
                $currency = $params['currency'];
                $action = $params['boclassicaction'];
                $amount = $params['amount'];

                switch ( $action ) {
                    case 'capture':
                        $capture_result = $this->ebsnext_online_classic_capture_payment($order_id, $amount, $currency);
                        return $capture_result;
                    case 'refund':
                        $refund_result = $this->ebsnext_online_classic_refund_payment($order_id, $amount, $currency);
                        return $refund_result;
                    case 'delete':
                        $delete_result = $this->ebsnext_online_classic_delete_payment($order_id);
                        return $delete_result;
                }
            }
            catch (Exception $ex) {
                return new WP_Error( 'ebsnext_online_classic_error', $ex->getMessage() );
            }
            return true;
        }



        /**
         * Capture a payment
         *
         * @param mixed $order_id
         * @param mixed $amount
         * @param mixed $currency
         * @return bool|WP_Error
         */
        public function ebsnext_online_classic_capture_payment($order_id, $amount, $currency) {

            $order = wc_get_order( $order_id );
            if( empty( $currency ) ) {
                $currency = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_currency() : $order->get_order_currency;
            }
            $minorunits = Ebsnext_Online_Classic_Helper::get_currency_minorunits( $currency );
            $amount = str_replace( ',', '.', $amount );
            $amount_in_minorunits = Ebsnext_Online_Classic_Helper::convert_price_to_minorunits( $amount, $minorunits, $this->roundingmode );
            $transaction_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_transaction_id( $order );

            $webservice = new Ebsnext_Online_Classic_Soap( $this->remotepassword );
            $capture_response = $webservice->capture( $this->merchant, $transaction_id, $amount_in_minorunits );
            if ( $capture_response->captureResult === true ) {
                do_action( 'ebsnext_online_classic_after_capture', $order_id );
                return true;
            } else {
                $message = sprintf( __( 'Capture action failed for order %s', 'ebsnext-online-classic' ), $order_id );
                if ( $capture_response->epayresponse != '-1' ) {
                    $message .= ' - ' . $webservice->get_epay_error( $this->merchant, $capture_response->epayresponse );
                } elseif ( $capture_response->pbsResponse != '-1' ) {
                    $message .= ' - ' . $webservice->get_pbs_error( $this->merchant, $capture_response->pbsResponse );
                }
                $this->_boclassic_log->add( $message );
                return new WP_Error( 'ebsnext_online_classic_error', $message );
            }
        }

        /**
         * Refund a payment
         *
         * @param mixed $order_id
         * @param mixed $amount
         * @param mixed $currency
         * @return bool|WP_Error
         */
        public function ebsnext_online_classic_refund_payment( $order_id, $amount, $currency ) {

            $order = wc_get_order( $order_id );

            if( empty( $currency ) ) {
                $currency = Ebsnext_Online_Classic_Helper::is_woocommerce_3() ? $order->get_currency() : $order->get_order_currency;
            }

            $minorunits = Ebsnext_Online_Classic_Helper::get_currency_minorunits( $currency );
            $amount = str_replace( ',', '.', $amount );
            $amount_in_minorunits = Ebsnext_Online_Classic_Helper::convert_price_to_minorunits( $amount, $minorunits, $this->roundingmode );
            $transaction_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_transaction_id( $order );

            $webservice = new Ebsnext_Online_Classic_Soap( $this->remotepassword );
            $refund_response = $webservice->refund( $this->merchant, $transaction_id, $amount_in_minorunits );
            if ( $refund_response->creditResult === true ) {
                do_action( 'ebsnext_online_classic_after_refund', $order_id );
                return true;
            } else {
                $message = sprintf( __( 'Refund action failed for order %s', 'ebsnext-online-classic' ), $order_id );
                if ( $refund_response->epayresponse != '-1' ) {
                    $message .= ' - ' . $webservice->get_epay_error( $this->merchant, $refund_response->epayresponse );
                } elseif ( $refund_response->pbsResponse != '-1' ) {
                    $message .= ' - ' . $webservice->get_pbs_error( $this->merchant, $refund_response->pbsResponse );
                }
                $this->_boclassic_log->add( $message );
                return new WP_Error( 'ebsnext_online_classic_error', $message );
            }
        }

        /**
         * Delete a payment
         *
         * @param mixed $order_id
         * @return bool|WP_Error
         */
        public function ebsnext_online_classic_delete_payment( $order_id ) {
            $order = wc_get_order( $order_id );
            $transaction_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_transaction_id( $order );

            $webservice = new Ebsnext_Online_Classic_Soap( $this->remotepassword );
            $delete_response = $webservice->delete( $this->merchant, $transaction_id );
            if ( $delete_response->deleteResult === true ) {
                do_action( 'ebsnext_online_classic_after_delete', $order_id );
                return true;
            } else {
                $message = sprintf( __( 'Delete action failed for order %s', 'ebsnext-online-classic' ), $order_id );
                if ( $delete_response->epayresponse != '-1' ) {
                    $message .= ' - ' . $webservice->get_epay_error( $this->merchant, $delete_response->epayresponse );
                }
                $this->_boclassic_log->add( $message );
                return new WP_Error( 'ebsnext_online_classic_error', $message );
            }
        }

        /**
         * Add Ebsnext Online Classic Meta boxes
         */
        public function ebsnext_online_classic_meta_boxes() {
            global $post;
            $order_id = $post->ID;
            if( !$this->module_check( $order_id ) ) {
                return;
            }

            add_meta_box(
                'epay-payment-actions',
                'Ebsnext Online ePay',
                array( &$this, 'ebsnext_online_classic_meta_box_payment' ),
                'shop_order',
                'side',
                'high'
            );
        }

        /**
         * Create the Ebsnext Online Classic Meta Box
         */
        public function ebsnext_online_classic_meta_box_payment() {
            global $post;
            $html = '';
            try {
                $order_id = $post->ID;
                $order = wc_get_order( $order_id );
                if ( ! empty( $order ) ) {
                    $transaction_id = Ebsnext_Online_Classic_Helper::get_ebsnext_online_classic_transaction_id( $order );
                    if ( strlen( $transaction_id ) > 0 ) {
                        $html = $this->ebsnext_online_classic_meta_box_payment_html( $order, $transaction_id );
                    } else {
                        $html = sprintf( __( 'No transaction was found for order %s', 'ebsnext-online-classic' ), $order_id );
                        $this->_boclassic_log->add( $html );
                    }
                } else {
                    $html = sprintf( __( 'The order with id %s could not be loaded', 'ebsnext-online-classic' ), $order_id );
                    $this->_boclassic_log->add( $html );
                }
            }
            catch ( Exception $ex ) {
                $html = $ex->getMessage();
                $this->_boclassic_log->add( $html );
            }
            echo ent2ncr( $html );
        }

        /**
         * Create the HTML for the Ebsnext Online Classic Meta box payment field
         *
         * @param WC_Order $order
         * @param string   $transaction_id
         * @return string
         */
        protected function ebsnext_online_classic_meta_box_payment_html( $order, $transaction_id ) {
            try {
                $html = '';
                $webservice = new Ebsnext_Online_Classic_Soap( $this->remotepassword );
                $get_transaction_response = $webservice->get_transaction( $this->merchant, $transaction_id );
                if ( $get_transaction_response->gettransactionResult === false ) {
                    $html = __( 'Get Transaction action failed', 'ebsnext-online-classic' );
                    if ( $get_transaction_response->epayresponse != '-1' ) {
                        $html .= ' - ' . $webservice->get_epay_error( $this->merchant, $get_transaction_response->epayresponse );
                    }
                    return $html;
                }
                $transaction = $get_transaction_response->transactionInformation;
                $currency_code = $transaction->currency;
                $currency = Ebsnext_Online_Classic_Helper::get_iso_code( $currency_code, false );
                $minorunits = Ebsnext_Online_Classic_Helper::get_currency_minorunits( $currency );

                $total_authorized = Ebsnext_Online_Classic_Helper::convert_price_from_minorunits( $transaction->authamount, $minorunits );
                $total_captured = Ebsnext_Online_Classic_Helper::convert_price_from_minorunits( $transaction->capturedamount, $minorunits );
                $total_credited = Ebsnext_Online_Classic_Helper::convert_price_from_minorunits( $transaction->creditedamount, $minorunits );
                $available_for_capture = $total_authorized - $total_captured;
                $transaction_status = $transaction->status;

                $card_info = Ebsnext_Online_Classic_Helper::get_cardtype_groupid_and_name($transaction->cardtypeid);
                $card_group_id = $card_info[1];
                $card_name = $card_info[0];

                if(isset($card_group_id) && $card_group_id != '-1') {
                    $this->add_or_update_payment_type_id_to_order( $order, $card_group_id );
                }

                $html = '<div class="boclassic-info">';
                if(isset($card_group_id) && $card_group_id != '-1') {
                    $html .= '<img class="boclassic-paymenttype-img" src="' . $card_group_id . '.png" alt="' . $card_name . '" title="' . $card_name . '" />';
                }
                $html .= '<div class="boclassic-transactionid">';
                $html .= '<p>' . __( 'Transaction ID', 'ebsnext-online-classic' ) . '</p>';
                $html .= '<p>' . $transaction->transactionid . '</p>';
                $html .= '</div>';
                $html .= '<div class="boclassic-paymenttype">';
                $html .= '<p>' . __( 'Payment Type', 'ebsnext-online-classic' ) . '</p>';
                $html .= '<p>' . $card_name . '</p>';
                $html .= '</div>';

                $html .= '<div class="boclassic-info-overview">';
                $html .= '<p>' . __( 'Authorized:', 'ebsnext-online-classic' ) . '</p>';
                $html .= '<p>' . wc_format_localized_price( $total_authorized ) . ' ' . $currency . '</p>';
                $html .= '</div>';
                $html .= '<div class="boclassic-info-overview">';
                $html .= '<p>' . __( 'Captured:', 'ebsnext-online-classic' ) . '</p>';
                $html .= '<p>' . wc_format_localized_price( $total_captured ) . ' ' . $currency . '</p>';
                $html .= '</div>';
                $html .= '<div class="boclassic-info-overview">';
                $html .= '<p>' . __( 'Refunded:', 'ebsnext-online-classic' ) . '</p>';
                $html .= '<p>' . wc_format_localized_price( $total_credited ) . ' ' . $currency . '</p>';
                $html .= '</div>';
                $html .= '</div>';

                if ( $transaction_status === 'PAYMENT_NEW' || ( $transaction_status === 'PAYMENT_CAPTURED' && $total_credited === 0 ) ) {
                    $html .= '<div class="boclassic-action-container">';
                    $html .= '<input type="hidden" id="boclassic-currency" name="boclassic-currency" value="' . $currency . '">';
                    wp_nonce_field( 'boclassic_process_payment_action', 'boclassicnonce' );
                    if ( $transaction_status === 'PAYMENT_NEW' ) {
                        $html .= '<input type="hidden" id="boclassic-capture-message" name="boclassic-capture-message" value="' . __( 'Are you sure you want to capture the payment?', 'ebsnext-online-classic' ) . '" />';
                        $html .= '<div class="boclassic-action">';
                        $html .= '<p>' . $currency . '</p>';
                        $html .= '<input type="text" value="' . $available_for_capture . '" id="boclassic-capture-amount" class="boclassic-amount" name="boclassic-amount" />';
                        $html .= '<input id="boclassic-capture-submit" class="button capture" name="boclassic-capture" type="submit" value="' . __( 'Capture', 'ebsnext-online-classic' ) . '" />';
                        $html .= '</div>';
                        $html .= '<br />';
                        if ( $total_captured === 0 ) {
                            $html .= '<input type="hidden" id="boclassic-delete-message" name="boclassic-delete-message" value="' . __( 'Are you sure you want to delete the payment?', 'ebsnext-online-classic' ) . '" />';
                            $html .= '<div class="boclassic-action">';
                            $html .= '<input id="boclassic-delete-submit" class="button delete" name="boclassic-delete" type="submit" value="' . __( 'Delete', 'ebsnext-online-classic' ) . '" />';
                            $html .= '</div>';
                        }
                    } elseif ( $transaction_status === 'PAYMENT_CAPTURED' && $total_credited === 0 ) {
                        $html .= '<input type="hidden" id="boclassic-refund-message" name="boclassic-refund-message" value="' . __( 'Are you sure you want to refund the payment?', 'ebsnext-online-classic' ) . '" />';
                        $html .= '<div class="boclassic-action">';
                        $html .= '<p>' . $currency . '</p>';
                        $html .= '<input type="text" value="' . $total_captured . '" id="boclassic-refund-amount" class="boclassic-amount" name="boclassic-amount" />';
                        $html .= '<input id="boclassic-refund-submit" class="button refund" name="boclassic-refund" type="submit" value="' . __( 'Refund', 'ebsnext-online-classic' ) . '" />';
                        $html .= '</div>';
                        $html .= '<br />';
                    }
                    $html .= '</div>';
                    $warning_message = __( 'The amount you entered was in the wrong format.', 'ebsnext-online-classic' );

                    $html .= '<div id="boclassic-format-error" class="boclassic boclassic-error"><strong>' . __( 'Warning', 'ebsnext-online-classic' ) . ' </strong>' . $warning_message . '<br /><strong>' . __( 'Correct format is: 1234.56', 'ebsnext-online-classic' ) . '</strong></div>';

                }

                $history_array = $transaction->history->TransactionHistoryInfo;

                if ( ! array_key_exists( 0, $transaction->history->TransactionHistoryInfo ) ) {
                    $history_array = array( $transaction->history->TransactionHistoryInfo );
                }

                // Sort the history array based on when the history event is created
                $histrory_created = array();
                foreach ( $history_array as $history ) {
                    $histrory_created[] = $history->created;
                }
                array_multisort( $histrory_created, SORT_ASC, $history_array );

                if ( count( $history_array ) > 0 ) {
                    $html .= '<h4>' . __( 'TRANSACTION HISTORY', 'ebsnext-online-classic' ) . '</h4>';
                    $html .= '<table class="boclassic-table">';

                    foreach ( $history_array as $history ) {
                        $html .= '<tr class="boclassic-transaction-row-header">';
                        $html .= '<td>' . Ebsnext_Online_Classic_Helper::format_date_time( $history->created ) . '</td>';
                        $html .= '</tr>';
                        if ( strlen( $history->username ) > 0 ) {
                            $html .= '<tr class="boclassic-transaction-row-header boclassic-transaction-row-header-user">';
                            $html .= '<td>' . sprintf( __( 'By: %s', 'ebsnext-online-classic' ), $history->username ) . '</td>';
                            $html .= '</tr>';
                        }
                        $html .= '<tr class="boclassic-transaction">';
                        $html .= '<td>' . $history->eventMsg . '</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</table>';
                }

                return $html;
            }
            catch ( Exception $ex ) {
                throw $ex;
            }
        }

        /**
         * Get the ebsnext online checkout logger
         *
         * @return Ebsnext_Online_Classic_Log
         */
        public function get_boclassic_logger() {
            return $this->_boclassic_log;
        }

        public function module_check($order_id) {
            $payment_method = get_post_meta( $order_id, '_payment_method', true );
            return $this->id === $payment_method;
        }

        /**
         * Returns a plugin URL path
         *
         * @param string $path
         * @return string
         */
        public function plugin_url( $path ) {
            return plugins_url( $path, __FILE__ );
        }

        public function get_icon() {
            $icon_html = '<img src="' . $this->icon . '" alt="' . $this->method_title . '" width="50"  />';
            return apply_filters( 'woocommerce_gateway_icon', $icon_html );
        }
    }

    add_filter( 'woocommerce_payment_gateways', 'add_ebsnext_online_classic_woocommerce' );
    Ebsnext_Online_Classic::get_instance()->init_hooks();

    /**
     * Add the Gateway to WooCommerce
     **/
    function add_ebsnext_online_classic_woocommerce( $methods ) {
        $methods[] = 'Ebsnext_Online_Classic';
        return $methods;
    }

    $plugin_dir = basename( dirname( __FILE__ ) );
    load_plugin_textdomain( 'ebsnext-online-classic', false, $plugin_dir . '/languages' );
}
