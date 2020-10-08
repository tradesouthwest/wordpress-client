<?php
/**
 * Plugin Name: Wordpress Woocommerce Nextpay Payment Gateway
 * Version: 2.0.1
 * Description:  Next Pay secure payment plugin for WooCommerce
 * author: Nextpay Payment Gateway Co.[Developers Team] and Larry Judd
 * Author URI: https://github.com/nextpay-ir
 * Requires at least: 4.5
 * Tested up to:      5.3.1
 * Requires PHP:      5.4
 * Text Domain:       woocommerce
 * Domain Path:       /languages
 * WC requires at least: 3.4.0
 * WC tested up to:      3.8.0
 * ID: @nextpay
 * Telegram Support: @nextpaysupport
 * Telegram Channel: @nextpay
 * Telegram Bot: @NextpayBot
 * Date: 02/04/2019
 * Time: 22:20 PM
 * Website: https://NextPay.ir 
 * Email: info@nextpay.ir
 * @copyright 2016-2019
 * @package NextPay Payment Gateway
 * @version 2.0.0
 */
include_once("class-wc-gateway-nextpay.php");

class Type_Method_Payment
{
    const NuSoap = 0;
    const SoapClient = 1;
    const Http = 2;
}

class Language
{
    const English = 0;
    const Persian = 1;
}

class Nextpay_Payment
{
    //----- payment properties
    public $api_key = "";
    public $order_id = "";
    public $amount = 0;
    public $trans_id = "";
    public $refund_key = "";
    public $custom = "";
    public $callback_uri = "";
    public $params = array();
    public $nusoap_path = __dir__."/include/nusoap/nusoap.php";
    
    public $request_token_soap = "https://api.nextpay.org/gateway/token.wsdl";
    //public $request_token_soap = "https://api.nextpay.org/gateway/token?wsdl";
    public $request_token_http = "https://api.nextpay.org/gateway/token.http";
    
    public $request_verify_soap = "https://api.nextpay.org/gateway/verify.wsdl";
    //public $request_verify_soap = "https://api.nextpay.org/gateway/verify?wsdl";
    public $request_verify_http = "https://api.nextpay.org/gateway/verify.http";
    
    public $request_refund_soap = "https://api.nextpay.org/gateway/refund.wsdl";
    //public $request_refund_soap = "https://api.nextpay.org/gateway/refund?wsdl";
    public $request_refund_http = "https://api.nextpay.org/gateway/refund.http";
    
    public $request_payment = "https://api.nextpay.org/gateway/payment";
    
    private $keys_for_token = array("api_key","order_id","amount","callback_uri");
    private $keys_for_verify = array("api_key","order_id","amount","trans_id");
    private $keys_for_refund = array("api_key","order_id","amount","trans_id", "refund_key");

    //----- controller properties
    public $default_method = Type_Method_Payment::SoapClient;
    public $lang = Language::Persian;

    /**
     * Nextpay_Payment constructor.
     * @param array|bool $params
     * @param string|bool $api_key
     * @param string|bool $order_id
     * @param string|bool $url
     * @param int|bool $amount
     */
    public function __construct($params=false, $api_key=false, $order_id=false, $amount=false, $callback_uri=false, $custom=false, $refund_key=false)
    {
        $trust = true;
        if(is_array($params))
        {
            foreach ($this->keys_for_token as $key )
            {
                if(!array_key_exists($key,$params))
                {
                    $error = "<h2>The submitted array has a problem.</h2>";
                    $error .= "<h4>Example Example for the submitted array.</h4>";
                    $error .= /** @lang text */
                        "<pre>                        
                        array(\"api_key\"=>\"ID api\",
                              \"order_id\"=>\"Invoice number\",
                              \"amount\"=>\"Amount\",
                              \"callback_uri\"=>\"Return route\")

                        </pre>";
                    $trust = false;
                    $this->show_error($error);
                    break;
                }
            }
            if($trust)
            {
                $this->params = $params;
                $this->api_key = $params['api_key'];
                $this->order_id = $params['order_id'];
                $this->amount = $params['amount'];
                $this->callback_uri = $params['callback_uri'];
                if(isset($params['refund_key']) && array_key_exists('refund_key',$params) && $params['refund_key']){ $this->refund_key = $params['refund_key']; $this->params["refund_key"] = $params['refund_key']; }
                if(isset($params['custom']) && array_key_exists('custom',$params) && $params['custom']) { $this->custom = $params['custom']; $this->params["custom"] = $params['custom']; }
            }
            else
            {
                $this->show_error("To set the parameters, you must act as an array");
                exit("End with Error!!!");
            }
        }
        else
        {
            if($api_key) $this->api_key = $api_key;
            //else
            //    $this->show_error("شناسه مربوط به api مقدار دهی نشده است");

            if($order_id) $this->order_id = $order_id;
            //else
            //    $this->show_error("شماره فاکتور مقداردهی نشده است");

            if($amount) $this->amount = $amount;
            //else
            //    $this->show_error("مبلغ تعیین نشده است");

            if($callback_uri) $this->callback_uri = $callback_uri;
            //else
            //    $this->show_error("مسیر بازگشت تعیین نشده است");

            $this->params = array(
                "api_key"=>$this->api_key,
                "order_id"=>$this->order_id,
                "amount"=>$this->amount,
                "callback_uri"=>$this->callback_uri);
            
            if($custom) {$this->params["custom"] = $custom; $this->custom = $custom;}
            if($refund_key) {$this->params["refund_key"] = $refund_key; $this->refund_key = $refund_key;}
        }
    }

    /**
     * @return string
     * return trans_id
     */
    public function token(){
        return $this->token_request();
    }

    /**
     * @return string
     * return trans_id
     */
    public function token_request()
    {
        $res = "";
        switch ($this->default_method)
        {
            case Type_Method_Payment::SoapClient:
                try
                {
                    $soap_client = new SoapClient($this->request_token_soap, array('encoding' => 'UTF-8'));
                    $res = $soap_client->TokenGenerator($this->params);

                    $res = $res->TokenGeneratorResult;

                    if ($res != "" && $res != NULL && is_object($res)) {
                        if (intval($res->code) == -1)
                            $this->trans_id = $res->trans_id;
                        /*else
                            $this->code_error($res->code);*/
                    }
                    else
                        $this->show_error("Error responding to request with SoapClient");
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            case Type_Method_Payment::NuSoap:
                try
                {
                    if(file_exists($this->nusoap_path) && is_readable($this->nusoap_path)) include_once ($this->nusoap_path);
                    else{
                        $res = "";
                        $this->show_error("nusoap path file don't exist !!!");
                        return false;
                    }

                    $client = new nusoap_client($this->request_token_soap,'wsdl');

                    $error = $client->getError();

                    if ($error)
                        $this->show_error($error);

                    $res = $client->call('TokenGenerator',array($this->params));

                    if ($client->fault)
                    {
                        echo "<h2>Fault</h2><pre>";
                        print_r ($res);
                        echo "</pre>";
                        exit(0);
                    }
                    else
                    {
                        $error = $client->getError();

                        if ($error)
                            $this->show_error($error);

                        $res = $res['TokenGeneratorResult'];

                        if ($res != "" && $res != NULL && is_array($res)) {
                            if (intval($res['code']) == -1) {
                                $this->trans_id = $res['trans_id'];
                                $res = (object)$res;
                            }/*else
                                $this->code_error($res['code']);*/
                        }
                        else
                            $this->show_error("Error responding to request with NuSoap_Client");
                    }
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            case Type_Method_Payment::Http:
                try
                {
                    if( !$this->cURLcheckBasicFunctions() ) $this->show_error("UNAVAILABLE: cURL Basic Functions");
                    $postfields = http_build_query($this->params);
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $this->request_token_http);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    /** @var int | string $server_output */
                    $res = json_decode(curl_exec ($curl));
                    curl_close ($curl);

                    if ($res != "" && $res != NULL && is_object($res)) {

                        if (intval($res->code) == -1)
                            $this->trans_id = $res->trans_id;
                        /*else
                            $this->code_error($res->code);*/
                    }
                    /*else
                        $this->show_error("خطا در پاسخ دهی به درخواست با Curl");*/
                }
                catch (Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            default:
                try
                {
                    $soap_client = new SoapClient($this->request_token_soap, array('encoding' => 'UTF-8'));
                    $res = $soap_client->TokenGenerator($this->params);

                    $res = $res->TokenGeneratorResult;

                    if ($res != "" && $res != NULL && is_object($res)) {
                        if (intval($res->code) == -1)
                            $this->trans_id = $res->trans_id;
                        /*else
                            $this->code_error($res->code);*/
                    }
                    else
                        $this->show_error("Error responding to request with SoapClient");
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
        }
        return $res;
    }
    
    /**
     * @return int|mixed
     */
     public function verify($params=false, $api_key=false, $order_id=false, $trans_id=false, $amount=false){
        $this->params = array();
        return $this->verify_request($params, $api_key, $order_id, $trans_id, $amount);
    }
    
    
    /**
     * @param array|bool $params
     * @param string|bool $api_key
     * @param string|bool $trans_id
     * @param int|bool $amount
     * @param string|bool $order_id
     * @return int|mixed
     */
    public function verify_request($params=false, $api_key=false, $order_id=false, $trans_id=false, $amount=false)
    {
        $res = 0;
        $trust = true;
        if(is_array($params))
        {
            foreach ($this->keys_for_verify as $key )
            {
                if(!array_key_exists($key,$params))
                {
                    $error = "<h2>The submitted array has a problem.</h2>";
                    $error .= "<h4>Example Example for the submitted array.</h4>";
                    $error .= /** @lang text */
                        "<pre>
                            array(\"api_key\"=>\"ID api\",
                                  \"order_id\"=>\"Invoice number\",
                                  \"amount\"=>\"Amount\",
                                  \"trans_id\"=>\"Transaction number\")

                        </pre>";
                    $trust = false;
                    $this->show_error($error);
                    break;
                }
            }
            if($trust)
            {
                $this->trans_id = $params['trans_id'];
                $this->api_key = $params['api_key'];
                $this->order_id = $params['order_id'];
                $this->amount = $params['amount'];
            }
            else
            {
                $this->show_error("You must act as an array for the value of the parameters");
                exit("End with Error!!!");
            }
        }

        if($api_key){
            $this->api_key = $api_key;
            $this->params['api_key'] = $api_key;
        }elseif (isset($this->api_key)) {
            $this->params['api_key'] = $this->api_key;
        }
        //else
        //    $this->show_error("شناسه مربوط به api مقدار دهی نشده است");

        if($order_id){
            $this->order_id = $order_id;
            $this->params['order_id'] = $order_id;
        }elseif (isset($this->order_id)){
            $this->params['order_id'] = $this->order_id;
        }
        //else
        //    $this->show_error("شماره فاکتور مقداردهی نشده است");

        if($amount){
            $this->amount = $amount;
            $this->params['amount'] = $amount;
        }elseif (isset($this->amount)){
            $this->params['amount'] = $this->amount;
        }
        //else
        //    $this->show_error("مبلغ تعیین نشده است");

        if($trans_id){
            $this->trans_id = $trans_id;
            $this->params['trans_id'] = $trans_id;
        }elseif (isset($this->trans_id)){
            $this->params['trans_id'] = $this->trans_id;
        }
        //else
        //    $this->show_error("شماره نراکنش تعیین نشده است");


        switch ($this->default_method)
        {
            case Type_Method_Payment::SoapClient:
                try
                {
                    $soap_client = new SoapClient($this->request_verify_soap, array('encoding' => 'UTF-8'));
                    $res = $soap_client->PaymentVerification($this->params);

                    $res = $res->PaymentVerificationResult;

                    if ($res != "" && $res != NULL && is_object($res)) {
                        $res = $res->code;
                    }
                    else
                        $this->show_error("Error responding to request with SoapClinet");
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            case Type_Method_Payment::NuSoap:
                try
                {
                    if(file_exists($this->nusoap_path) && is_readable($this->nusoap_path)) include_once ($this->nusoap_path);
                    else{
                        $res = "";
                        $this->show_error("nusoap path file don't exist !!!");
                        return false;
                    }

                    $client = new nusoap_client($this->request_verify_soap,'wsdl');

                    $error = $client->getError();

                    if ($error)
                        $this->show_error($error);

                    $res = $client->call('PaymentVerification',array($this->params));

                    if ($client->fault)
                    {
                        echo "<h2>Fault</h2><pre>";
                        print_r ($res);
                        echo "</pre>";
                        exit(0);
                    }
                    else
                    {
                        $error = $client->getError();

                        if ($error)
                            $this->show_error($error);

                        $res = $res['PaymentVerificationResult'];

                        if ($res != "" && $res != NULL && is_array($res)) {
                            $res = $res['code'];
                        }
                        else
                            $this->show_error("Error responding to request with NuSoap_Client");
                    }
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            case Type_Method_Payment::Http:
                try
                {
                    if( !$this->cURLcheckBasicFunctions() ) $this->show_error("UNAVAILABLE: cURL Basic Functions");
                    $postfields = http_build_query($this->params);
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $this->request_verify_http);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    /** @var int | string $server_output */
                    $res = json_decode(curl_exec ($curl));
                    curl_close ($curl);

                    if ($res != "" && $res != NULL && is_object($res)) {
                        $res = $res->code;
                    }
                    else
                        $this->show_error("Error responding to request with Curl");
                }
                catch (Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            default:
                try
                {
                    $soap_client = new SoapClient($this->request_verify_soap, array('encoding' => 'UTF-8'));
                    $res = $soap_client->PaymentVerification($this->params);

                    $res = $res->PaymentVerificationResult;

                    if ($res != "" && $res != NULL && is_object($res)) {
                        $res = $res->code;
                    }
                    else
                        $this->show_error("Error responding to request with SoapClinet");
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
        }
        return $res;
    }
    
    /**
     * @param array|bool $params
     * @param string|bool $api_key
     * @param string|bool $trans_id
     * @param int|bool $amount
     * @param string|bool $order_id     
     * @param string|bool $refund_key
     * @return int|mixed
     */
     public function refund($params=false, $api_key=false, $order_id=false, $trans_id=false, $amount=false, $refund_key=false){
        $this->params = array();
        return $this->verify_request($params, $api_key, $order_id, $trans_id, $amount, $refund_key);
    }
    
    
    /**
     * @param array|bool $params
     * @param string|bool $api_key
     * @param string|bool $trans_id
     * @param int|bool $amount
     * @param string|bool $order_id     
     * @param string|bool $refund_key
     * @return int|mixed
     */
    public function refund_request($params=false, $api_key=false, $order_id=false, $trans_id=false, $amount=false, $refund_key=false)
    {
        $res = 0;
        $trust = true;
        if(is_array($params))
        {
            foreach ($this->keys_for_refund as $key )
            {
                if(!array_key_exists($key,$params))
                {
                    $error = "<h2>The submitted array has a problem.</h2>";
                    $error .= "<h4>Example Example for the submitted array.</h4>";
                    $error .= /** @lang text */
                        "<pre>
                            array(\"api_key\"=>\"ID api\",
                                  \"order_id\"=>\"Invoice number\",
                                  \"amount\"=>\"Amount\",
                                  \"trans_id\"=>\"Transaction number\",
                                  \"refund_key\"=>\"Refund key\")

                        </pre>";
                    $trust = false;
                    $this->show_error($error);
                    break;
                }
            }
            if($trust)
            {
                $this->trans_id = $params['trans_id'];
                $this->api_key = $params['api_key'];
                $this->order_id = $params['order_id'];
                $this->amount = $params['amount'];
                $this->refund_key = $params['refund_key'];
            }
            else
            {
                $this->show_error("You must act as an array for the value of the parameters");
                exit("End with Error!!!");
            }
        }

        if($api_key){
            $this->api_key = $api_key;
            $this->params['api_key'] = $api_key;
        }elseif (isset($this->api_key)) {
            $this->params['api_key'] = $this->api_key;
        }
        //else
        //    $this->show_error("شناسه مربوط به api مقدار دهی نشده است");

        if($order_id){
            $this->order_id = $order_id;
            $this->params['order_id'] = $order_id;
        }elseif (isset($this->order_id)){
            $this->params['order_id'] = $this->order_id;
        }
        //else
        //    $this->show_error("شماره فاکتور مقداردهی نشده است");

        if($amount){
            $this->amount = $amount;
            $this->params['amount'] = $amount;
        }elseif (isset($this->amount)){
            $this->params['amount'] = $this->amount;
        }
        //else
        //    $this->show_error("مبلغ تعیین نشده است");

        if($trans_id){
            $this->trans_id = $trans_id;
            $this->params['trans_id'] = $trans_id;
        }elseif (isset($this->trans_id)){
            $this->params['trans_id'] = $this->trans_id;
        }
        //else
        //    $this->show_error("شماره نراکنش تعیین نشده است");
        
        if($refund_key){
            $this->refund_key = $refund_key;
            $this->params['refund_key'] = $refund_key;
        }elseif (isset($this->refund_key)) {
            $this->params['refund_key'] = $this->refund_key;
        }

        switch ($this->default_method)
        {
            case Type_Method_Payment::SoapClient:
                try
                {
                    $soap_client = new SoapClient($this->request_refund_soap, array('encoding' => 'UTF-8'));
                    $res = $soap_client->PaymentRefund($this->params);

                    $res = $res->PaymentRefundResult;

                    if ($res != "" && $res != NULL && is_object($res)) {
                        $res = $res->code;
                    }
                    else
                        $this->show_error("Error responding to request with SoapClinet");
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            case Type_Method_Payment::NuSoap:
                try
                {
                    if(file_exists($this->nusoap_path) && is_readable($this->nusoap_path)) include_once ($this->nusoap_path);
                    else{
                        $res = "";
                        $this->show_error("nusoap path file don't exist !!!");
                        return false;
                    }

                    $client = new nusoap_client($this->request_refund_soap,'wsdl');

                    $error = $client->getError();

                    if ($error)
                        $this->show_error($error);

                    $res = $client->call('PaymentRefund',array($this->params));

                    if ($client->fault)
                    {
                        echo "<h2>Fault</h2><pre>";
                        print_r ($res);
                        echo "</pre>";
                        exit(0);
                    }
                    else
                    {
                        $error = $client->getError();

                        if ($error)
                            $this->show_error($error);

                        $res = $res['PaymentRefundResult'];

                        if ($res != "" && $res != NULL && is_array($res)) {
                            $res = $res['code'];
                        }
                        else
                            $this->show_error("Error responding to request with NuSoap_Client");
                    }
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            case Type_Method_Payment::Http:
                try
                {
                    if( !$this->cURLcheckBasicFunctions() ) $this->show_error("UNAVAILABLE: cURL Basic Functions");
                    $postfields = http_build_query($this->params);
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $this->request_refund_http);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    /** @var int | string $server_output */
                    $res = json_decode(curl_exec ($curl));
                    curl_close ($curl);

                    if ($res != "" && $res != NULL && is_object($res)) {
                        $res = $res->code;
                    }
                    else
                        $this->show_error("Error responding to request with Curl");
                }
                catch (Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
            default:
                try
                {
                    $soap_client = new SoapClient($this->request_refund_soap, array('encoding' => 'UTF-8'));
                    $res = $soap_client->PaymentRefund($this->params);

                    $res = $res->PaymentRefundResult;

                    if ($res != "" && $res != NULL && is_object($res)) {
                        $res = $res->code;
                    }
                    else
                        $this->show_error("Error responding to request with SoapClinet");
                }
                catch(Exception $e){
                    $this->show_error($e->getMessage());
                }
                break;
        }
        return $res;
    }

    /**
     * @param string $trans_id
     */
    public function send($trans_id = false, $url = false)
    {
        $return = $this->getRequestPaymentURL($trans_id);
        
        if(isset($url) && is_string($url) && strlen($url) > 7) $return = $url;
        if(!$return) {$this->show_error("empty trans_id or url redirect param send"); exit(0); return;}
        
        header_remove();
        ob_clean();
        
        if (headers_sent()) echo "<script> location.replace(\"$return\"); </script>";
        else
        {
            header('Location: '.$return);
            exit(0);
        }
    }
    
    /**
     * @param string $trans_id
     */
     public function checkTransID($trans_id)
     {
        $trans_id = trim($trans_id);
        if (is_string($trans_id) && strlen($trans_id) == 36)
        {        
            if ((preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $trans_id) == 1))
                return true;
        }
        return false;
     }
    
    /**
     * @param string $trans_id
     */
    public function getRequestPaymentURL($trans_id)
    {
        if(isset($trans_id) && $this->checkTransID($trans_id)){
            return $this->request_payment."/$trans_id";
        }else if (isset($this->trans_id) && $this->checkTransID($this->trans_id)){
            return $this->request_payment."/{$this->trans_id}";
        }else{
            $this->show_error("empty trans_id param send");
            return false;
        }  
    }

    /**
     * @param string | string $error
     */
    public function show_error($error)
    {
        $l = $this->lang;
        if($l != 0 && $l != 1){$this->setLanguage(Language::Persian);}
        $error_des = array("error occurred","Occurrence of error");
        echo "<h1>{$error_des[$l]} !!!</h1>";
        echo "<h4>{$error}</h4>";
    }

    /**
     * @param int | string $error_code
     */
    public function code_error($error_code)
    {
        $l = $this->lang;
        if($l != 0 && $l != 1){$this->setLanguage(Language::Persian);}
        $error_des = array("error code","شماره خطا");
        $error_code = intval($error_code);
        $error_array = array(
          0 => ["Complete Transaction", 'پرداخت تکمیل و با موفقیت انجام شده است'],
	     -1 => ["Default State", 'منتظر ارسال تراکنش و ادامه پرداخت'],
	     -2 => ["Bank Failed or Canceled", 'پرداخت رد شده توسط کاربر یا بانک'],
	     -3 => ["Bank Payment Pending", 'پرداخت در حال انتظار جواب بانک'],
	     -4 => ["Bank Canceled", 'پرداخت لغو شده است'],
	    -20 => ["api key is not send", 'کد api_key ارسال نشده است'],
	    -21 => ["empty trans_id param send", 'کد trans_id ارسال نشده است'],
	    -22 => ["amount not send", 'مبلغ ارسال نشده'],
	    -23 => ["callback not send", 'لینک ارسال نشده'],
	    -24 => ["amount incorrect", 'مبلغ صحیح نیست'],
	    -25 => ["trans_id resend and not allow to payment", 'تراکنش قبلا انجام و قابل ارسال نیست'],
	    -26 => ["Token not send", 'مقدار توکن ارسال نشده است'],
	    -27 => ["order_id incorrect", 'شماره سفارش صحیح نیست'],
	    -28 => ["custom field incorrect [must be json]", 'مقدار فیلد سفارشی [custom] از نوع json نیست'],
	    -29 => ["refund key incorrect", 'کد بازگشت مبلغ صحیح نیست'],
	    -30 => ["amount less of limit payment", 'مبلغ کمتر از حداقل پرداختی است'],
	    -31 => ["fund not found", 'صندوق کاربری موجود نیست'],
	    -32 => ["callback error [incorrect]", 'مسیر بازگشت صحیح نیست'],
	    -33 => ["api_key incorrect", 'کلید مجوز دهی صحیح نیست'],
	    -34 => ["trans_id incorrect", 'کد تراکنش صحیح نیست'],
	    -35 => ["type of api_key incorrect", 'ساختار کلید مجوز دهی صحیح نیست'],
	    -36 => ["order_id not send", 'شماره سفارش ارسال نشد است'],
	    -37 => ["transaction not found", 'شماره تراکنش یافت نشد'],
	    -38 => ["token not found", 'توکن ارسالی موجود نیست'],
	    -39 => ["api_key not found", 'کلید مجوز دهی موجود نیست'],
	    -40 => ["api_key is blocked", 'کلید مجوزدهی مسدود شده است'],
	    -41 => ["params from bank invalid", 'خطا در دریافت پارامتر، شماره شناسایی صحت اعتبار که از بانک ارسال شده موجود نیست'],
	    -42 => ["payment system problem", 'سیستم پرداخت دچار مشکل شده است'],
	    -43 => ["payment gateway not found", 'درگاه پرداختی برای انجام درخواست یافت نشد'],
	    -44 => ["response bank invalid", 'پاسخ دریاف شده از بانک نامعتبر است'],
	    -45 => ["payment system deactivated", 'سیستم پرداخت غیر فعال است'],
	    -46 => ["request incorrect", 'درخواست نامعتبر'],
	    -47 => ["api has been deleted", 'کلید مجوز دهی یافت نشد [حذف شده]'],
	    -48 => ["commission rate not detect", 'نرخ کمیسیون تعیین نشده است'],
	    -49 => ["transaction repeated", 'تراکنش مورد نظر تکراریست'],
	    -50 => ["account not found", 'حساب کاربری برای صندوق مالی یافت نشد'],
	    -51 => ["user not found", 'شناسه کاربری یافت نشد'],
	    -52 => ["user not verify", 'حساب کاربری تایید نشده است'],
	    -60 => ["email incorrect", 'ایمیل صحیح نیست'],
	    -61 => ["national code incorrect", 'کد ملی صحیح نیست'],
	    -62 => ["postal code incorrect", 'کد پستی صحیح نیست'],
	    -63 => ["postal address incorrect", 'آدرس پستی صحیح نیست و یا بیش از ۱۵۰ کارکتر است'],
	    -64 => ["desc incorrect", 'توضیحات صحیح نیست و یا بیش از ۱۵۰ کارکتر است'],
	    -65 => ["name and family incorrect", 'نام و نام خانوادگی صحیح نیست و یا بیش از ۳۵ کاکتر است'],
	    -66 => ["tel incorrect", 'تلفن صحیح نیست'],
	    -67 => ["account name incorrect", 'نام کاربری صحیح نیست یا بیش از ۳۰ کارکتر است'],
	    -68 => ["product name incorrect", 'نام محصول صحیح نیست و یا بیش از ۳۰ کارکتر است'],
	    -69 => ["callback success incorrect", 'آدرس ارسالی برای بازگشت موفق صحیح نیست و یا بیش از ۱۰۰ کارکتر است'],
	    -70 => ["callback failed incorrect", 'آدرس ارسالی برای بازگشت ناموفق صحیح نیست و یا بیش از ۱۰۰ کارکتر است'],
	    -71 => ["phone incorrect", 'موبایل صحیح نیست'],
	    -72 => ["bank not response", 'بانک پاسخگو نبوده است لطفا با نکست پی تماس بگیرید'],
	    -73 => ["callback_uri incorrect [with api's address website]", 'مسیر بازگشت دارای خطا میباشد یا بسیار طولانیست'],
	    -80 => ["Comming Soon [None]","تنظیم نشده"],
	    -81 => ["Comming Soon [None]","تنظیم نشده"],
	    -82 => ["ppm incorrect token code", 'احراز هویت موبایل برای پرداخت شخصی صحیح نمیباشد.'],
	    -83 => ["Comming Soon [None]","تنظیم نشده"],
	    -90 => ["refund success", 'بازگشت مبلغ بدرستی انجام شد'],
	    -91 => ["refund failed", 'عملیات ناموفق در بازگشت مبلغ'],
	    -92 => ["refund stoped by error", 'در عملیات بازگشت مبلغ خطا رخ داده است'],
	    -93 => ["amount be less in fund for refund", 'موجودی صندوق کاربری برای بازگشت مبلغ کافی نیست'],
	    -94 => ["refund's key not found", 'کلید بازگشت مبلغ یافت نشد']
        );
        
        if (array_key_exists($error_code, $error_array)) {
            return $error_array[$error_code][$l];
        } else {
            return "{$error_des[$l]} : $error_code";
        }
    }

    /**
     * @return bool
     */
    public function cURLcheckBasicFunctions()
    {
        if( !function_exists("curl_init") &&
            !function_exists("curl_setopt") &&
            !function_exists("curl_exec") &&
            !function_exists("curl_close") ) return false;
        else return true;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @return string
     */
    public function getCallbackUri()
    {
        return $this->callback_uri;
    }

    /**
     * @return string
     */
    public function getTransId()
    {
        return $this->trans_id;
    }

    /**
     * @return string
     */
    public function getCustom()
    {
        return $this->custom;
    }
    
    /**
     * @return string
     */
    public function getRefundKey()
    {
        return $this->refund_key;
    }
    
    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param int|int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        $this->params['amount'] = $amount;
    }

    /**
     * @param bool|string $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        $this->params['api_key'] = $api_key;
    }

    /**
     * @param bool|string $order_id
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
        $this->params['order_id'] = $order_id;
    }

    /**
     * @param string $trans_id
     */
    public function setTransId($trans_id)
    {
        $this->trans_id = $trans_id;
        $this->params['trans_id'] = $trans_id;
    }

    /**
     * @param string $callback_uri
     */
    public function setCallbackUri($callback_uri)
    {
        $this->callback_uri = $callback_uri;
        $this->params['callback_uri'] = $callback_uri;
    }
    
    /**
     * @param string|array $custom
     */
    public function setCustom($custom)
    {
        if(is_array($custom)) $custom = json_encode($custom);
        $this->custom = $custom;
        $this->params['custom'] = $custom;
    }
    
    /**
     * @param string $refund_key
     */
    public function setRefundKey($refund_key)
    {
        $this->refund_key = $refund_key;
        $this->params['refund_key'] = $refund_key;
    }
    
    /**
     * @param string $nusoap_path
     */
    public function setNusoapPath($nusoap_path)
    {
        $this->nusoap_path = $nusoap_path;
    }

    /**
     * @param array $params
     */
    public function setParamsForGetToken($params)
    {
        $trust = true;
        if(is_array($params))
        {
            if (isset($this->$keys_for_token))
            {
                foreach ($this->keys_for_token as $key )
                {
                    if(!array_key_exists($key,$params))
                    {
                        $trust = false;
                        $error = "<h2>The submitted array has a problem.</h2>";
                        $error .= "<h4>Example Example for the submitted array.</h4>";
                        $error .= /** @lang text */
                            "<pre>
                                array(\"api_key\"=>\"ID api\",
                                      \"order_id\"=>\"Invoice number\",
                                      \"amount\"=>\"Amount\",
                                      \"callback_uri\"=>\"Return route\")
    
                            </pre>";
                        $this->show_error($error);
                        break;
                    }
                }
            }
            else
                $this->show_error("You must act as an array for the value of the parameters");
            if($trust)
            {
                $this->params = $params;
                $this->api_key = $params['api_key'];
                $this->order_id = $params['order_id'];
                $this->amount = $params['amount'];
                $this->callback_uri = $params['callback_uri'];
                
                if(isset($params['refund_key']) && array_key_exists('refund_key',$params) && $params['refund_key']){ $this->refund_key = $params['refund_key']; $this->params["refund_key"] = $refund_key; }
                if(isset($params['custom']) && array_key_exists('custom',$params) && $params['custom']) { $this->custom = $params['custom']; $this->params["custom"] = $custom;}
            }
            else
                $this->show_error("You must act as an array for the value of the parameters");

        }
        else
            $this->show_error("You must act as an array for the value of the parameters");
    }

    /**
     * @param int $default_method
     */
    public function setDefaultMethod($default_method)
    {
        switch ($default_method){
            case 0:
            case Type_Method_Payment::NuSoap:
                $this->default_method = Type_Method_Payment::NuSoap;
                break;
            case 1:
            case Type_Method_Payment::SoapClient:
                $this->default_method = Type_Method_Payment::SoapClient;
                break;
            case 2:
            case Type_Method_Payment::Http:
                $this->default_method = Type_Method_Payment::Http;
                break;
            default:
                $this->default_method = Type_Method_Payment::SoapClient;
        }
    }
    
    /**
     * @param int $lang
     */
    public function setLanguage($lang)
    {
        switch ($lang){
            case 0:
            case Language::English:
                $this->lang = Language::English;
                break;
            case 1:
            case Language::Persian:
                $this->lang = Language::Persian;
                break;
            default:
                $this->lang = Language::Persian;
        }
    }
}
