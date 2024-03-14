<?php

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: access");
    header("Access-Control-Allow-Methods: GET"); // Change to POST because you're sending data
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $host       = 'localhost';
    $dbname     = 'aqua'; // Assurez-vous que c'est le bon nom de la base de données
    $username   = 'aqua';
    $password   = 'aqua!';
    $myResponse = new stdClass();

    // INCLUDING DATABASE AND MAKING OBJECT
    require 'crudPDO.php';

    // GET DATA FROM JSON REQUEST
    $JsonParams = json_decode(file_get_contents("php://input"), false);

    // CHECK identifiant AND mdp PARAMETERS
    if (isset($JsonParams->identifiant) && isset($JsonParams->mdp) && !empty($JsonParams->identifiant) && !empty($JsonParams->mdp)) 
    {
        $identifiant    = $JsonParams->identifiant;
        $mdp            = $JsonParams->mdp;

        try {
            $crud = new CrudPDO($host, $dbname, $username, $password);
            
            // Prepare the SQL statement
            $params = ['identifiant'=> $identifiant,'mdp'=>$mdp];
            $results = $crud->readCustom("SELECT * FROM users WHERE identifiant=:identifiant AND mdp=:mdp",$params); // Nom de table corrigé
            
            // Check if user is found
            if (isset($results['error'])) 
            {
                $myResponse->message = "Error :" . $results['error'];
            } 
            else {
                if(count($results)!=0)
                    $myResponse->message = "OK";
                else
                    $myResponse->message = "identifiant ou mot de passe incorrect";
            }
        } catch (Exception $e) {
            // Handle the connection error
            $myResponse->message = $e->getMessage();
        }
    } else {
        $myResponse->message = "Identifiant et mot de passe requis";
    }

    // Return Json Response
    echo json_encode($myResponse);

?>