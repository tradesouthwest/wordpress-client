<?php


	/*
	 Authentication: 
	 to get token
	*/
	function getToken() {
	
        $url = 'https://dev.znextech.com/nextpay/api/authenticate';
        
	$jsonData = array(
            'username' => 'udan',
            'password' => '1234',
            'rememberMe' => 'true'
            
        );

        $context = stream_context_create([
            "http" => [
                "method"  => "POST",
                "header"  => "Content-type: application/json\r\n".
                "content" => json_encode($jsonData)
            ]
        ]);

        $response = file_get_contents($url, true, $context);  
        $body = json_decode($response, true );
        return $body.id_token;
        }
        
        /////////////////////////////////////////////////////////////////////////////////////
        
        
        
        /*
        Encryption-key:
        to get encryption key
        */
        function getPublicKey (){
        
        $url = 'https://dev.znextech.com/nextpay/api/encryption-key';
        
        $token =  getToken():
        
        $context = stream_context_create([
            "http" => [
                "method"  => "POST",
                "header"  => "Content-type: application/json\r\n".
                "Authorization: Bearer ". $token . "\r\n",
                "content" => " ")
            ]
        ]);

        $response = file_get_contents($url, true, $context);  
        $body = json_decode($response, true );
        
        return $body;
        }
        /////////////////////////////////////////////////////////////////////////////////////


	/*
	Payment Request:
	
	*/

	function doMerchantPayments(){

        $token =  getToken():

        $key = getPublicKey();

        $url = 'https://dev.znextech.com/nextpay/api/merchant-payments';

        $expDate = '';
        $pan = '';
        $ipin = '';
        
        $UUID = getUUID(openssl_random_pseudo_bytes(16));

        $creptIpin = encryptByPublicKey($UUID . $ipin ,$key);

        $jsonData = array(
            'amount' => '',
            'expDate' => $expDate,
            'id' => '',
            'ipin' => $creptIpin,
            'pan' => $pan,
            'uuid' => $UUID,
            'mobilePayment' => false,
        );

        $context = stream_context_create([
            "http" => [
                "method"  => "POST",
                "header"  => "Content-type: application/json\r\n".
                "Authorization: Bearer ". $token . "\r\n",
                "content" => json_encode($jsonData)
            ]
        ]);

        $response = file_get_contents($url, true, $context);

        if($response !== false) {
            $body = json_decode( $response, true );
            var_dump($body);
        }

	}
        ///////////////////////////////////////////////////////////////////////

	    function encryptByPublicKey($data ,$key) {
		$publicKey = "-----BEGIN PUBLIC KEY-----\r\n" . $key . "\r\n-----END PUBLIC KEY-----";
		openssl_public_encrypt($data,$decrypted,$publicKey, OPENSSL_PKCS1_PADDING);
		return base64_encode($decrypted);

	    }

	    function getUUID($data)
	    {
		assert(strlen($data) == 16);

		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	    }

    ?>
