<?php
namespace Api\Controller;

use Security\MrMeSecurity as MrMeSecurity;
use MrMe\Web\Validate as WebValidate;
use MrMe\Web\Controller;

use MrMe\Database\MySql\MySqlCommand;
use MrMe\Database\MySql\MySqlConnection;


class ExampleCommand extends Controller
{
    public function cmd()
    {
        // Header Form
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: POST");  
        header('Access-Control-Allow-Methods: *');
        header('Content-Type: application/json');

        // Request Form
        $param_name = $this->request->params->param_name; 
        $body_name  = $this->request->body->body_name;
        $attri_name = $this->request->json->attribute_name;

        // WebValidate Example
        WebValidate::isEmpty ($check, "Error response message.");
        WebValidate::isDate  ($check, "Error response message.");
        WebValidate::isEmail ($check, "Error response message.");
        WebValidate::isNumber($check, "Error response message.");

        $table  = "`table_name`";

        // SELECT SQL Command
        $select = ["column_name1 AS name1", "column_name2 AS name2"];

        $this->db->select($table, $select);
        $this->db->where("column_name", "=", "@VALUE");
        $this->db->order("column_name", "DESC OR ASC");
        $this->db->group("column_name");

        $this->db->bindParam("@VALUE", $VALUE);

        $model = $this->db->executeReader(); // Return in json form

        // INSERT SQL Command
        $field = ["column_name1", "column_name2"];
        $value = ["@VALUE1", "@VALUE2"];

        $this->db->insert($table, $field, $value);
        $this->db->bindParam("@VALUE1", $VALUE1);
        $this->db->bindParam("@VALUE2", $VALUE2);

        $err = $this->db->execute(); // If success $err = NULL

        $id = $this->db->getLastInsertId(); // Get Last Insert Id

        // UPDATE SQL Command
        $this->db->update("`table_name`", ["column_name1 = @VALUE1", "column_name = @VALUE2"], "WHERE clause");
        $this->db->bindParam("@VALUE1", $VALUE1);
        $this->db->bindParam("@VALUE2", $VALUE2);

        $err = $this->db->execute(); // If success $err = NULL

        // DELETE SQL Command
        $this->db->delete("`table_name`", "WHERE clause");
        $err = $this->db->execute(); // If success $err = NULL

        // MrMeSecurity Example
        $ciphertext = MrMeSecurity::xEncrypt($plain_text);
        $plain_text = MrMeSecurity::xDecrypt($ciphertext);

        // Use jwt system in payload security
        // Auto insert login and exp time to payload
        $payload = array(
          "id"         => $id_of_user, // Must
          "attribute1" => $VALUE1,
          "attribute1" => $VALUE2,
          "group"      => $permission_group // Must
        );

        $token = MrMeSecurity::encodeToken($payload);
        MrMeSecurity::insertToken($token); // Insert token data to mrme_token table

        MrMeSecurity::allowGrantAndCheck($array_of_permission_grant_id);

        // Response Form
        $this->response->success($model); // Success Case
        $this->response->error($err); // Error Case
    }
    
    public function test()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: POST");  
        header('Access-Control-Allow-Methods: *');
        header('Content-Type: application/json');

        $this->response->success(array("success" => "test!",
                                       "message" => "hello world!!"));
    }
}
?>