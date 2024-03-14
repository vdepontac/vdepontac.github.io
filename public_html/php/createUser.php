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

try {
    $crud = new CrudPDO($host, $dbname, $username, $password);

    // CHECK GET artistName PARAMETER OR NOT
    if (isset($JsonParams->mail) && isset($JsonParams->tel) && isset($JsonParams->login) && isset($JsonParams->motDePasse)) {
        if (!empty($JsonParams->login)) {
            // get ajax parameter's call values in PHP variable
            $login      = $JsonParams->login;
            $motDePasse = $JsonParams->motDePasse;
            $mail       = $JsonParams->mail;
            $tel        = $JsonParams->tel;

            // MAKE SQL QUERY
            $params = [ 'mail' => $mail,'tel' => $tel,'login' => $login,'mdp' => $motDePasse];
            $results = $crud->create("user", $params); // nom de la table

            if (isset($result['error'])) {
                $myResponse->message = "Error: " . $result['error'];
            } else {
                $myResponse->message = "Mesures successfully added";
            }
        } else {
            $myResponse->message = "Mersures not successfully added";
        }
    } else {
        $myResponse->message = "Mesures parameter missing...";
    }
} catch (Exception $e) {
    // Handle the connection error
    $myResponse->message = $e->getMessage(); // problème de connexion
}

// Return Json Response
echo json_encode($myResponse);

?>
