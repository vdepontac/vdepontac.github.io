<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-RequestedWith");

$host = "localhost";
$dbname = "id21881071_koifarme";
$username = "id21881071_aqua";
$password = "Aquafarme2023!";
$myResponse = new stdClass();
// INCLUDING DATABASE AND MAKING OBJECT
require "crudPDO.php";
// GET DATA FROM JSON REQUEST
$JsonParams = json_decode(file_get_contents("php://input"), false);

// CHECK id PARAMETER
if (isset($JsonParams->id) && !empty($JsonParams->id)) 
{
    $post_id = $JsonParams->id;
} 
else 
{
    $post_id = "all_posts";
} // si pas de paramètre id

$results = [];
try {
    $crud = new CrudPDO($host, $dbname, $username, $password);
    // on teste si nos paramètres sont définies
    if (is_numeric($post_id)) {
        // Si tout est OK, on va préparer les paramètres de la requête SQL
        $params = ["postID" => $post_id];
        $results = $crud->readCustom(
            "SELECT * FROM mesures WHERE id=:postID",
            $params
        );
    } else {
        $results = $crud->read("mesures");

    }
    if (isset($result["error"])) {
        $myResponse->message = "Error: " . $result["error"];
    } else {
        $myResponse->message = "OK";
        $myResponse->results = $results;
    }
} catch (Exception $e) {
    // Handle the connection error
    $myResponse->message = $e->getMessage(); // problème de connexion
}

// Return Json Response
echo json_encode($myResponse);
?>
