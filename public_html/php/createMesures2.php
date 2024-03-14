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

    // The request is using the POST method
    try {
        $crud = new CrudPDO($host, $dbname, $username, $password);
    
        // CHECK GET PARAMETER OR NOT
       // if (isset($JsonParams->temperature) && isset($JsonParams->ph) && isset($JsonParams->id) && isset($JsonParams->o2) && isset($JsonParams->turbidite)) {
        if (isset($JsonParams->temperature) && isset($JsonParams->ph) && isset($JsonParams->id) ) {
            if (!empty($JsonParams->temperature) && !empty($JsonParams->ph) && !empty($JsonParams->id)) {
                // get ajax parameter's call values in PHP variable       
                $idUser         = $JsonParams->id;
                $temperature    = $JsonParams->temperature;  
                $ph             = $JsonParams->ph;
                $o2             = 1;
                $turbidite      = 1;
                
                // MAKE SQL QUERY
                $params = ['Temperature' => $temperature, 'pH' => $ph, "o2" => $o2, "Turbidite" => $turbidite, "idUser" => $idUser];
                $results = $crud->create("mesures", $params);
                //$results = $crud->read("mesures");
                //$par = array($temperature);
                //$results = $crud->readCustom("SELECT * FROM mesures WHERE temperature = ?", $par);
    
                if (isset($result['error'])) {
                    $myResponse->message = "Error: " . $result['error'];
                } else {
                    $myResponse->message = "Mesures successfully added";
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

// Return Json Response
echo json_encode($myResponse);

?>
