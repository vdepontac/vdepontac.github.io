<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: DELETE");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    // INCLUDING CRUD DATABASE CLASS
    require 'crudPDO.php';

    $host = '127.0.0.1';
    $dbname = 'id21881071_koifarme';
    $username = 'id21881071_aqua';
    $password = 'Aquafarm2023';

    $myResponse = new stdClass();

    // CHECK IF RECEIVED DATA FROM THE REQUEST
    $JsonParams = json_decode(file_get_contents("php://input"), false);
    $results = [];


    // The request is using the POST method
    try {
        $crud = new CrudPDO($host, $dbname, $username, $password);
    
        // CHECK GET PARAMETER OR NOT
        if (isset($JsonParams->identifiant) && isset($JsonParams->mdp)) 
        {
            if (!empty($JsonParams->identifiant) && !empty($JsonParams->mdp)) 
            {
                // get ajax parameter's call values in PHP variable       
                $identifiant   = $JsonParams->identifiant;
                $mdp     = $JsonParams->mdp;  

                //recherche de l'id du User
                $params = ['identifiant' => $identifiant, "mdp" => $mdp];
                $results = $crud->readCustom("SELECT idUser FROM users WHERE identifiant=:identifiant AND mdp=:mdp", $params);

                if (isset($results['error'])) {
                    $myResponse->message = "Error: " . $results['error'];
                } 
                else {
                    if(count($results))
                    {
                        foreach ($results as $row)
                        $idUser = $row["idUser"];
    
                        // suppression de la mesure
                        $results = $crud->delete("users", $idUser);
    
                        if (isset($result['error'])) {
                            $myResponse->message = "Error: " . $result['error'];
                        } else {
                            $myResponse->message = "User successfully deleted";
                        }    
                    }
                    else
                    {
                        $myResponse->message = "Aucun user à deleted";
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
