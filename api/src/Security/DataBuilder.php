<?php
namespace Security;

use \Firebase\JWT\JWT;

class DataBuilder
{
	private $cipher_algorithm;
	private $secret_key;
	private $jwt_key;

	public function __construct()
	{
		global $_CONFIG;

		$path   = "./stemp/";
		$chk    = scandir($path);
		$secret = "";

		while ($chk[2] != "Secret key.smrme")
		{
			$secret .= chr($chk[2]);
			$path .= $chk[2]. "/";
			$chk  = scandir($path);
		}

		$this->jwt_key 		    = $_CONFIG['TOKEN']['KEY'];
		$this->cipher_algorithm = $_CONFIG['CRYPTO']['ALGORITHM']; 
		$this->secret_key 	    = $secret;
	}

	public function encode($payload)
	{		
		return JWT::encode($payload, $this->jwt_key);
	}

	public function decode($jwt)
	{
		try
		{
			$decoded = JWT::decode($jwt, $this->jwt_key, array('HS256'));
		} 
		catch (\Firebase\JWT\ExpiredException $e)
		{
			http_response_code(400);
			$json = array("status"  => false, 
						  "message" => "Token expired.");
			echo json_encode($json);
			exit;
		}
		catch (\Firebase\JWT\SignatureInvalidException $e)
		{
			http_response_code(400);
			$json = array("status"  => false, 
						  "message" => "Invalid key.");
			echo json_encode($json);
			exit;
		}
		
		$payload = (array) $decoded;
		return $payload;
	}

	public function encrypt($plaintext)
	{
		return openssl_encrypt($plaintext, $this->cipher_algorithm, $this->secret_key);
	}

	public function decrypt($cipher_text)
	{
		return openssl_decrypt($cipher_text, $this->cipher_algorithm, $this->secret_key);
	}
}
?>