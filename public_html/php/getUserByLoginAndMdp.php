<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING CRUD DATABASE CLASS
require 'crudPDO.php';

$host = '127.0.0.1';
$dbname = 'test';
$username = 'test';
$password = 'test';

$myResponse = new stdClass();

// CHECK IF RECEIVED DATA FROM THE REQUEST
$JsonParams = json_decode(file_get_contents("php://input"), false);
$results = [];

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    // The request is using the POST method
    try {
        $crud = new CrudPDO($host, $dbname, $username, $password);
        //echo($JsonParams->login);
        // CHECK GET PARAMETER OR NOT
        if (isset($JsonParams->login) && isset($JsonParams->mdp)) {
            if (!empty($JsonParams->login) && !empty($JsonParams->mdp)) {
                // get ajax parameter's call values in PHP variable       
                $login = $JsonParams->login;  
                $mdp = $JsonParams->mdp;
                
                
                // MAKE SQL QUERY
                $params = ['login' => $login, 'mdp' => $mdp];
                //$results = $crud->create("mesures", $params);
                //$results = $crud->read("mesures");
                $par = array($login, $mdp);
                $results = $crud->readCustom("SELECT * FROM user WHERE login = ? AND mdp = ?", $par);
    

                if (isset($result['error'])) {
                    $myResponse->message = "Error: " . $result['error'];
                } else {
                    $myResponse->message = "Mesures successfully added";
                    $myResponse->message = $results;
                }
            } else {
                $myResponse->message = "Mesures not successfully added";
            }
        } else {
            $myResponse->message = "Mesures parameters missing...";
        }
    } catch (Exception $e) {
        // Handle the connection error
        $myResponse->message = $e->getMessage(); // problème de connexion
    }
}


// Return Json Response
echo json_encode($myResponse);

?>
