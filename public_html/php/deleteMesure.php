<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: DELETE");
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
        if (isset($JsonParams->horodatage) && isset($JsonParams->idUser)) 
        {
            if (!empty($JsonParams->horodatage) && !empty($JsonParams->idUser)) 
            {
                // get ajax parameter's call values in PHP variable       
                $idUser         = $JsonParams->idUser;
                $horodatage     = $JsonParams->horodatage;  

                //recherche de l'id de la mesure
                $params = ['horodatage' => $horodatage, "idUser" => $idUser];
                $results = $crud->readCustom("SELECT idMesure FROM mesures WHERE horodatage=:horodatage AND idUser=:idUser", $params);

                if (isset($results['error'])) {
                    $myResponse->message = "Error: " . $results['error'];
                } 
                else {
                    if(count($results))
                    {
                        foreach ($results as $row)
                        $idMesure = $row["idMesure"];
                        //var_dump($idMesure);
    
                        // suppression de la mesure
                        //$params = ['idMesure' =>$idMesure];
                        $results = $crud->delete("mesures", $idMesure);
    
                        if (isset($result['error'])) {
                            $myResponse->message = "Error: " . $result['error'];
                        } else {
                            $myResponse->message = "Mesures successfully deleted";
                        }    
                    }
                    else
                    {
                        $myResponse->message = "Aucune mesure à cette date deleted";
                    }
                }
            } else {
                $myResponse->message = "fiels empty";
            }
        } else {
            $myResponse->message = "fields missing...";
        }
    } catch (Exception $e) {
        // Handle the connection error
        $myResponse->message = $e->getMessage(); // problème de connexion
    }

// Return Json Response
echo json_encode($myResponse);
?>
