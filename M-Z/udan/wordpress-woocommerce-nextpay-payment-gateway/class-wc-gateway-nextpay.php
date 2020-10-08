<?php

if (!defined('ABSPATH'))
    exit;

function Load_NextPay_Gateway()
{

    if (class_exists('WC_Payment_Gateway') && !class_exists('WC_Nextpay') && !function_exists('Woocommerce_Add_NextPay_Gateway')) {


        add_filter('woocommerce_payment_gateways', 'Woocommerce_Add_NextPay_Gateway');

        function Woocommerce_Add_NextPay_Gateway($methods)
        {
            $methods[] = 'WC_Nextpay';
            return $methods;
        }

        //add_filter('woocommerce_currencies', 'add_IR_currency');
/*
        function add_IR_currency($currencies)
        {

            $currencies['IRR']  = __('Rial',           'woocommerce');
            $currencies['IRT']  = __('Toman',          'woocommerce');
            $currencies['IRHR'] = __('Thousand rials', 'woocommerce');
            $currencies['IRHT'] = __('one thousand Tomans', 'woocommerce');

            return $currencies;
        }
*/
        add_filter('woocommerce_currency_symbol', 'add_IR_currency_symbol', 10, 2);

        function add_IR_currency_symbol($currency_symbol, $currency)
        {
            switch ($currency) {
                case 'IRR':
                    $currency_symbol = 'Rials';
                    break;
                case 'IRT':
                    $currency_symbol = 'Toman';
                    break;
                case 'IRHR':
                    $currency_symbol = 'Thousand rials';
                    break;
                case 'IRHT':
                    $currency_symbol = 'one thousnad Tomans';
                    break;
            }
            return $currency_symbol;
        }

        class WC_Nextpay extends WC_Payment_Gateway
        {

            public function __construct()
            {

                $this->id = 'WC_Nextpay';
                $this->method_title = __('Next Pay secure payments', 'woocommerce');
                $this->method_description = __('Next Pay payment gateway settings for WooCommerce Store Builder plugin', 'woocommerce');
                $this->icon = apply_filters('WC_Nextpay_logo', WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/assets/images/logo.png');
                $this->has_fields = false;

                $this->init_form_fields();
                $this->init_settings();

                $this->title = $this->settings['title'];
                $this->description = $this->settings['description'];

                $this->apikey = $this->settings['apikey'];

                $this->success_massage = $this->settings['success_massage'];
                $this->failed_massage = $this->settings['failed_massage'];

                if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>='))
                    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                else
                    add_action('woocommerce_update_options_payment_gateways', array($this, 'process_admin_options'));

                add_action('woocommerce_receipt_' . $this->id . '', array($this, 'Send_to_NextPay_Gateway'));
                add_action('woocommerce_api_' . strtolower(get_class($this)) . '', array($this, 'Return_from_NextPay_Gateway'));


            }


            public function admin_options()
            {
                parent::admin_options();
            }

            public function init_form_fields()
            {
                $this->form_fields = apply_filters('WC_Nextpay_Config', array(
                        'base_confing' => array(
                            'title' => __('Basic settings', 'woocommerce'),
                            'type' => 'title',
                            'description' => '',
                        ),
                        'enabled' => array(
                            'title' => __('Activation / deactivation', 'woocommerce'),
                            'type' => 'checkbox',
                            'label' => __('Activating the NextPay port', 'woocommerce'),
                            'description' => __('To activate the NextPay payment gateway, you must check the checkbox', 'woocommerce'),
                            'default' => 'yes',
                            'desc_tip' => true,
                        ),
                        'title' => array(
                            'title' => __('Port Title', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('The title of the port that is displayed to the customer during the purchase', 'woocommerce'),
                            'default' => '',
                            'desc_tip' => true,
                        ),
                        'description' => array(
                            'title' => __('Port Description', 'woocommerce'),
                            'type' => 'text',
                            'desc_tip' => true,
                            'description' => __('Descriptions that will be displayed to the gateway during the payment process', 'woocommerce'),
                            'default' => __('Secure payment by all Shetab member cards through NextPay portal', 'woocommerce')
                        ),
                        'account_confing' => array(
                            'title' => __('NextPay account settings', 'woocommerce'),
                            'type' => 'title',
                            'description' => '',
                        ),
                        'apikey' => array(
                            'title' => __('Licensing key', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('Nextpay port authorization key', 'woocommerce'),
                            'default' => '',
                            'desc_tip' => true
                        ),
                        'payment_confing' => array(
                            'title' => __('Payment operation settings', 'woocommerce'),
                            'type' => 'title',
'description' => __('Enter the text of the message you want to display to the user after successful payment', 'woocommerce'),
                            'default' => __('(Token) Use NextPay.', 'woocommerce'),
                        ),
                        'failed_massage' => array(
                            'title' => __('Successful payment message', 'woocommerce'),
                            'type' => 'texarea', 'woocommerce',
                            'default' => __( 'Enter the text of the message you want to display to the user after a failed payment.', 'woocommerce'),
                        ),
                    )
                );
            }

            public function process_payment($order_id)
            {
                $order = new WC_Order($order_id);
                return array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_payment_url(true)
                );
            }

            public function Send_to_NextPay_Gateway($order_id)
            {


                global $woocommerce;
                $order = new WC_Order($order_id);
                $currency = $order->get_order_currency();
                $currency = apply_filters('WC_Nextpay_Currency', $currency, $order_id);


                $form = '<form action="" method="POST" class="nextpay-checkout-form" id="nextpay-checkout-form">
						<input type="submit" name="nextpay_submit" class="button alt" id="nextpay-payment-button" value="' . __('پرداخت', 'woocommerce') . '"/>
						<a class="button cancel" href="' . $woocommerce->cart->get_checkout_url() . '">' . __('بازگشت', 'woocommerce') . '</a>
					 </form><br/>';
                $form = apply_filters('WC_Nextpay_Form', $form, $order_id, $woocommerce);

                do_action('WC_Nextpay_Gateway_Before_Form', $order_id, $woocommerce);
                echo $form;
                do_action('WC_Nextpay_Gateway_After_Form', $order_id, $woocommerce);


                $Amount = intval($order->order_total);
                $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_before_check_currency', $Amount, $currency);
                if (strtolower($currency) == strtolower('IRT') || strtolower($currency) == strtolower('TOMAN') || strtolower($currency) == strtolower('Iran TOMAN') || strtolower($currency) == strtolower('Iranian TOMAN') || strtolower($currency) == strtolower('Iran-TOMAN') || strtolower($currency) == strtolower('Iranian-TOMAN') || strtolower($currency) == strtolower('Iran_TOMAN') || strtolower($currency) == strtolower('Iranian_TOMAN') || strtolower($currency) == strtolower('تومان') || strtolower($currency) == strtolower('تومان ایران')
                )
                    $Amount = $Amount * 1;
                else if (strtolower($currency) == strtolower('IRHT'))
                    $Amount = $Amount * 1000;
                else if (strtolower($currency) == strtolower('IRHR'))
                    $Amount = $Amount * 100;
                else if (strtolower($currency) == strtolower('IRR'))
                    $Amount = $Amount / 10;


                $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_after_check_currency', $Amount, $currency);
                $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_irt', $Amount, $currency);
                $Amount = apply_filters('woocommerce_order_amount_total_NextPay_gateway', $Amount, $currency);

                $apikey = $this->apikey;
                $CallbackUrl = add_query_arg('wc_order', $order_id, WC()->api_request_url('WC_Nextpay'));

                $products = array();
                $order_items = $order->get_items();
                foreach ((array)$order_items as $product) {
                    $products[] = $product['name'] . ' (' . $product['qty'] . ') ';
                }
                $products = implode(' - ', $products);

                $Description = 'Purchase to order number : ' . $order->get_order_number() . ' | Buyer : ' . $order->billing_first_name . ' ' . $order->billing_last_name . ' | Products : ' . $products;
                $Mobile = get_post_meta($order_id, '_billing_phone', true) ? get_post_meta($order_id, '_billing_phone', true) : '-';
                $Email = $order->billing_email;
                $Paymenter = $order->billing_first_name . ' ' . $order->billing_last_name;
                $ResNumber = intval($order->get_order_number());

                //Hooks for iranian developer
                $Description = apply_filters('WC_Nextpay_Description', $Description, $order_id);
                $Mobile = apply_filters('WC_Nextpay_Mobile', $Mobile, $order_id);
                $Email = apply_filters('WC_Nextpay_Email', $Email, $order_id);
                $Paymenter = apply_filters('WC_Nextpay_Paymenter', $Paymenter, $order_id);
                $ResNumber = apply_filters('WC_Nextpay_ResNumber', $ResNumber, $order_id);
                do_action('WC_Nextpay_Gateway_Payment', $order_id, $Description, $Mobile);
                $Email = !filter_var($Email, FILTER_VALIDATE_EMAIL) === false ? $Email : '';
                $Mobile = preg_match('/^09[0-9]{9}/i', $Mobile) ? $Mobile : '';

                $data = array(
                                'api_key' => $this->apikey,
                                'amount' => $Amount,
                                'callback_uri' => $CallbackUrl,
                                'order_id' => $order_id
                );

                require_once("nextpay_payment.php");
                $nextpay = new Nextpay_Payment($data);
                $nextpay->setDefaultMethod(Type_Method_Payment::SoapClient);
                $result = $nextpay->token();
                if (intval($result->code) == -1) {                    
                    wp_redirect($nextpay->getRequestPaymentURL($result->trans_id));
                    exit;
                } else {
                    $Message = ' Transaction failed - error code : ' . $result->code;
                    $Fault = $nextpay->code_error($result->code);
                }

                if (!empty($Message) && $Message) {

                    $Note = sprintf(__('Error sending to bank : %s', 'woocommerce'), $Message);
                    $Note = apply_filters('WC_Nextpay_Send_to_Gateway_Failed_Note', $Note, $order_id, $Fault);
                    $order->add_order_note($Note);


                    $Notice = sprintf(__('The following error occurred while connecting to the bank : <br/>%s', 'woocommerce'), $Message);
                    $Notice = apply_filters('WC_Nextpay_Send_to_Gateway_Failed_Notice', $Notice, $order_id, $Fault);
                    if ($Notice)
                        wc_add_notice($Notice, 'error');

                    do_action('WC_Nextpay_Send_to_Gateway_Failed', $order_id, $Fault);
                }
            }


            public function Return_from_NextPay_Gateway()
            {


                $order_id       = isset($_POST['order_id'])     ? $_POST['order_id']    : false;
                $trans_id       = isset($_POST['trans_id'])     ? $_POST['trans_id']    : false ;
                $card_holder    = isset($_POST['card_holder'])  ? $_POST['card_holder'] : "0000-0000-0000-0000" ;

                global $woocommerce;
                
                require_once("nextpay_payment.php");
                $nextpay = new Nextpay_Payment();
                $nextpay->setDefaultMethod(Type_Method_Payment::SoapClient);


                if (isset($_GET['wc_order'])) $order_id = $_GET['wc_order'];
                
                if (!is_string($trans_id) || (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $trans_id) !== 1)) {
                    $message = ' شماره خطا: -34 <br />';
                    $message .='<br>'.$nextpay->code_error(-34);
                    $trans_id = false;
                    $order_id = false;
                }

                if ( $trans_id && $order_id ) {

                    $order = new WC_Order($order_id);
                    $currency = $order->get_order_currency();
                    $currency = apply_filters('WC_Nextpay_Currency', $currency, $order_id);

                    if ($order->status != 'completed') {

                        $api_key = $this->apikey;
                        $Amount = intval($order->order_total);
                        $Amount = apply_filters('woocommerce_order_amount_total_IRANIAN_gateways_before_check_currency', $Amount, $currency);
                        if (strtolower($currency) == strtolower('IRT') || strtolower($currency) == strtolower('TOMAN') || strtolower($currency) == strtolower('Iran TOMAN') || strtolower($currency) == strtolower('Iranian TOMAN') || strtolower($currency) == strtolower('Iran-TOMAN') || strtolower($currency) == strtolower('Iranian-TOMAN') || strtolower($currency) == strtolower('Iran_TOMAN') || strtolower($currency) == strtolower('Iranian_TOMAN') || strtolower($currency) == strtolower('تومان') || strtolower($currency) == strtolower('تومان ایران')
                        )
                            $Amount = $Amount * 1;
                        else if (strtolower($currency) == strtolower('IRHT'))
                            $Amount = $Amount * 1000;
                        else if (strtolower($currency) == strtolower('IRHR'))
                            $Amount = $Amount * 100;
                        else if (strtolower($currency) == strtolower('IRR'))
                            $Amount = $Amount / 10;

                        $data = array
                        (
                            'api_key'	=> $api_key,
                            'order_id'	=> $order_id,
                            'trans_id' 	=> $trans_id,
                            'amount'	=> $Amount
                        );
                        $result = $nextpay->verify_request($data);

                        if ($result == 0) {
                            $Status = 'completed';
                            $Transaction_ID = $trans_id;
                            $Fault = '';
                            $Message = '';
                        } elseif ($result < 0) {
                            $Status = 'failed';
                            $Fault = $nextpay->code_error($result);
                            $Message = 'The transaction failed';
                            exit;
                        } else {
                            $Status = 'failed';
                            $Fault = $nextpay->code_error($result);
                            $Message = 'The transaction failed';
                        }


                        if ($Status == 'completed' && isset($Transaction_ID) && $Transaction_ID != 0) {
                            update_post_meta($order_id, '_transaction_id', $Transaction_ID);


                            $order->payment_complete($Transaction_ID);
                            $woocommerce->cart->empty_cart();

                            $Note = sprintf(__('Payment was successful .<br/> Tracking Code : %s', 'woocommerce'), $Transaction_ID);
                            $Note = apply_filters('WC_Nextpay_Return_from_Gateway_Success_Note', $Note, $order_id, $Transaction_ID);
                            if ($Note)
                                $order->add_order_note($Note, 1);


                            $Notice = wpautop(wptexturize($this->success_massage));

                            $Notice = str_replace("{transaction_id}", $Transaction_ID, $Notice);

                            $Notice = apply_filters('WC_Nextpay_Return_from_Gateway_Success_Notice', $Notice, $order_id, $Transaction_ID);
                            if ($Notice)
                                wc_add_notice($Notice, 'success');

                            do_action('WC_Nextpay_Return_from_Gateway_Success', $order_id, $Transaction_ID);

                            wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
                            exit;
                        } else {


                            $tr_id = ($Transaction_ID && $Transaction_ID != 0) ? ('<br/>Token : ' . $Transaction_ID) : '';

                            $Note = sprintf(__('Error when returning from the bank : %s %s', 'woocommerce'), $Message, $tr_id);

                            $Note = apply_filters('WC_Nextpay_Return_from_Gateway_Failed_Note', $Note, $order_id, $Transaction_ID, $Fault);
                            if ($Note)
                                $order->add_order_note($Note, 1);

                            $Notice = wpautop(wptexturize($this->failed_massage));

                            $Notice = str_replace("{transaction_id}", $Transaction_ID, $Notice);

                            $Notice = str_replace("{fault}", $Message, $Notice);
                            $Notice = apply_filters('WC_Nextpay_Return_from_Gateway_Failed_Notice', $Notice, $order_id, $Transaction_ID, $Fault);
                            if ($Notice)
                                wc_add_notice($Notice, 'error');

                            do_action('WC_Nextpay_Return_from_Gateway_Failed', $order_id, $Transaction_ID, $Fault);

                            wp_redirect($woocommerce->cart->get_checkout_url());
                            exit;
                        }

                    } else {


                        $Transaction_ID = get_post_meta($order_id, '_transaction_id', true);

                        $Notice = wpautop(wptexturize($this->success_massage));

                        $Notice = str_replace("{transaction_id}", $Transaction_ID, $Notice);

                        $Notice = apply_filters('WC_Nextpay_Return_from_Gateway_ReSuccess_Notice', $Notice, $order_id, $Transaction_ID);
                        if ($Notice)
                            wc_add_notice($Notice, 'success');


                        do_action('WC_Nextpay_Return_from_Gateway_ReSuccess', $order_id, $Transaction_ID);

                        wp_redirect(add_query_arg('wc_status', 'success', $this->get_return_url($order)));
                        exit;
                    }

                } else {

                    $Fault = __('There is no order number .', 'woocommerce');
                    $Notice = wpautop(wptexturize($this->failed_massage));
                    $Notice = str_replace("{fault}", $Fault, $Notice);
                    $Notice = apply_filters('WC_Nextpay_Return_from_Gateway_No_Order_ID_Notice', $Notice, $order_id, $Fault);
                    if ($Notice)
                        wc_add_notice($Notice, 'error');

                    do_action('WC_Nextpay_Return_from_Gateway_No_Order_ID', $order_id, $Transaction_ID, $Fault);

                    wp_redirect($woocommerce->cart->get_checkout_url());
                    exit;
                }

            }

        }

    }

}

add_action('plugins_loaded', 'Load_NextPay_Gateway', 0);
