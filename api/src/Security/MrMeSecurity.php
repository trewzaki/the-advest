<?php
namespace Security;

use Security\DataBuilder as DataBuilder;
use Security\TokenValidator as TokenValidator;

use MrMe\Database\MySql\MySqlConnection as MySqlConnection;
use MrMe\Database\MySql\MySqlCommand as MySqlCommand;

class MrMeSecurity
{
	public static function encodeToken($payload)
	{
		global $_CONFIG;
		$dataBuilder = new DataBuilder();

		$payload["login"] = time();
		$payload["exp"]   = time() + (int)($_CONFIG['TOKEN']['TIME'] * 60);

		return $dataBuilder->encode($payload);
	}

	public static function decodeToken($jwt)
	{
		$dataBuilder = new DataBuilder();
		return $dataBuilder->decode($jwt);
	}

	public static function insertToken($token)
	{
		global $_CONFIG;
		$dataBuilder = new DataBuilder();

		$con = new MySqlConnection($_CONFIG, "utf8");
		$con->connect();
		$database = new MySqlCommand($con); 

		$payload = $dataBuilder->decode($token);

		$id = $dataBuilder->decrypt($payload['id']);
		$datetime = new \DateTime(null, new \DateTimeZone('Asia/Bangkok'));

		$unix_expire_time = (int)$datetime->format('U') + $datetime->getOffset() + (int)($_CONFIG['TOKEN']['TIME'] * 60);
		$expire_datetime  = new \DateTime("@$unix_expire_time");
		$expire_datetime->setTimezone(date_default_timezone_get("Asia/Bangkok"));

		$login_time  = $datetime->format('Y-m-d H:i:s');
		$expire_time = $expire_datetime->format('Y-m-d H:i:s');

		$table = "mrme_token";
		$field = ["user_id", "token", "status", "login_time", "expire_time"];
		$value = ["@user_id", "@token", "@status", "@login_time", "@expire_time"];

		$database->insert($table, $field, $value);
		$database->bindParam("@user_id", $id);
		$database->bindParam("@token", $token);
		$database->bindParam("@status", 1);
		$database->bindParam("@login_time", $login_time);
		$database->bindParam("@expire_time", $expire_time);

		$err = $database->execute();

		if ($err)
		{
			http_response_code(400);
			$json = array("status"  => false, 
						  "message" => "MrMeSecuriry insertToken error",
						  "error"   => $err);
			echo json_encode($json);
			exit;
		}
	}

	public static function clearToken()
	{
		global $_CONFIG;
		$dataBuilder = new DataBuilder();

		$header = array();
		$header["token"] = $_SERVER["HTTP_TOKEN"];
		$payload = $dataBuilder->decode($header["token"]);

		$con = new MySqlConnection($_CONFIG, "utf8");
		$con->connect();
		$database = new MySqlCommand($con); 
		
		$id = $dataBuilder->decrypt($payload['id']);

		$database->update("mrme_token", 
		                 ["status = @status"],
						  "WHERE user_id = @id");

		$database->bindParam("@status", 0);
		$database->bindParam("@id"    , $id);

		$err = $database->execute();

		if ($err)
		{
			http_response_code(400);
			$json = array("status"  => false, 
						  "message" => "MrMeSecuriry clearToken error",
						  "error"   => $err);
			echo json_encode($json);
			exit;
		}
	}

	public static function xEncrypt($plaintext)
	{
		$dataBuilder = new DataBuilder();
		return $dataBuilder->encrypt($plaintext);
	}

	public static function xDecrypt($ciphertext)
	{
		$dataBuilder = new DataBuilder();
		return $dataBuilder->decrypt($ciphertext);
	}

	public static function allowGrant($permission, $user)
	{
		$tokenValidate = new TokenValidate();
		return $tokenValidate->allowGrant($permission, $user);
	}

	public static function getId()
	{
		$dataBuilder = new DataBuilder();

		$header = array();
		$header["token"] = $_SERVER["HTTP_TOKEN"];
		if (!$header["token"])
		{	
			http_response_code(400);
			$json = array("status"  => false, 
					      "message" => "No token on header.");
			echo json_encode($json);
			exit;
		}

		$payload = $dataBuilder->decode($header["token"]);
		$id = $dataBuilder->decrypt($payload['id']);

		return $id;
	}

	public static function allowGrantAndCheck($permission)
	{
		global $_CONFIG;

		if ($_CONFIG['TOKEN']['ACTIVE'])
		{
			$tokenValidate = new TokenValidate();
			$dataBuilder = new DataBuilder();

			$header = array();
			$header["token"] = $_SERVER["HTTP_TOKEN"];
			$token = $header["token"];

			if (!$header["token"])
			{	
				http_response_code(400);
				$json = array("status"  => false, 
							  "message" => "No token on header.");
				echo json_encode($json);
				exit;
			}

			$payload = $dataBuilder->decode($header["token"]);
			
			$con = new MySqlConnection($_CONFIG, "utf8");
			$con->connect();
			$database = new MySqlCommand($con); 

			$id = $dataBuilder->decrypt($payload['id']);

			$table  = "mrme_token";
			$select = ["id"];

			$database->select($table, $select);
			$database->where ("user_id", "=", $id)
					 ->and   ("status", "=", 1)
					 ->and   ("token", "LIKE", "'$token'");

			$model = $database->executeReader();

			if (!$model)
			{
				http_response_code(401);
				$json = array("status"  => false, 
							  "message" => "You are already logged out of your account.");
				echo json_encode($json);
				exit;
			}

			if (!$tokenValidate->allowGrant($permission, explode(",", $dataBuilder->decrypt($payload['group']))))
			{
				http_response_code(403);
				$json = array("status"  => false, 
							  "message" => "Permission denied.");
				echo json_encode($json);
				exit;
			}
		}
	}

	// public static function allowGrantAndGroupCheck()
	// {

	// }

	// public static function test($permission, $user)
	// {
	// 	$tokenValidate = new TokenValidate();
	// 	var_dump($tokenValidate->allowGrant($permission, $user));
	// }
}
?>