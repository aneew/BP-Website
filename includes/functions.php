<?php

function myMessage(){
    echo "hello world!";
}

function getUcitele(){

// API endpoint
    $api_url = 'https://stag-ws.utb.cz/ws/services/rest2/ucitel/getUciteleKatedry?lang=en&outputFormat=JSON&katedra=FAI&jenAktualni=true';

// Fetch data from the API
    $response = file_get_contents($api_url);

// Check if the request was successful
    if ($response === FALSE) {
    // Handle error if the request failed
        echo "Error occurred while fetching data from the API.";
    } else {
        $data = json_decode($response, true);
        if ($data === NULL) {
        echo "Error decoding JSON response.";
    } else {
//        $api_response = $data;
        
        if (isset($data['ucitel'])) {
            deleteUcitele();
                // Iterate through each teacher and print their name
            foreach ($data['ucitel'] as $teacher) {
                insertUcitel($teacher['jmeno'], $teacher['prijmeni'], $teacher['ucitIdno']);
                getPredmetyUcitel($teacher['ucitIdno']);
                // echo $teacher['jmeno'] . " " . $teacher['prijmeni'];
            }
        } else {
            echo "No teachers found in the response.";
        }
//            var_dump($api_response);
        }
    }
}

function insertUcitel($name, $surname, $ucitIdno){
    try {
        require "dbh.inc.php";

        $query = "INSERT INTO teachers (name, surname, ucitIdno) VALUES (?, ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$name, $surname, $ucitIdno]);

        // Close statement and database connection properly
//        $stmt = null;
//        $pdo = null;
        echo nl2br("Inserted succesfully: " . $name . " " . $surname . "\n");
//        die(); // Optional: stop script execution
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

function deleteUcitele()
{
    
    require "dbh.inc.php";
    $query = "DELETE FROM teachers;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

function deleteUcitelPredmety()
{
    
    require "dbh.inc.php";
    $query = "DELETE FROM ucitelPredmety;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

function getPredmetyUcitel($ucitIdno){
    $api_url = "https://stag-ws.utb.cz/ws/services/rest2/predmety/getPredmetyByUcitel?ucitIdno=" . $ucitIdno . "&lang=en&outputFormat=JSON&katedra=%25&rok=%25";
    // Create authentication credentials
    require('config.php');
    $auth = base64_encode("$username:$password");

    // Create HTTP headers with authentication
    $context = stream_context_create([
        'http' => [
            'header' => "Authorization: Basic $auth"
        ]
    ]);

    // // Fetch data using HTTP headers with authentication
    // $response2 = file_get_contents($api_url, false, $context);
    // // var_dump($response2);
    // $data2 = json_decode($response2, true);
    // foreach($data2['predmetUcitele'] as $predmet){
    //     insertPredmetyByUcitel($predmet['zkratka'], $ucitIdno);
    //     // echo nl2br($predmet['zkratka'] . "\n");
    // }
    try {
        // Fetch data using HTTP headers with authentication
        $response2 = file_get_contents($api_url, false, $context);

        if ($response2 === false) {
            // Failed to fetch data, log or handle the error as needed
            throw new Exception("Failed to fetch data for ucitIdno: $ucitIdno");
        }

        $data2 = json_decode($response2, true);

        if (!isset($data2['predmetUcitele'])) {
            // Data structure doesn't match what's expected, handle the error
            throw new Exception("Unexpected data structure for ucitIdno: $ucitIdno");
        }

        foreach($data2['predmetUcitele'] as $predmet) {
            insertPredmetyByUcitel($predmet['zkratka'], $ucitIdno);
            // echo nl2br($predmet['zkratka'] . "\n");
        }
    } catch (Exception $e) {
        // Handle the exception, log it, or add the id of the teacher to an array
//        $failed_ucitIdno_array[] = $ucitIdno;
        // Log the error or handle it as needed
        insertErrNumber($ucitIdno);
        echo 'Error: ' . $e->getMessage();
    }

}

function insertPredmetyByUcitel($zkratka, $ucitIdno){
    try {
        require "dbh.inc.php";

        $query = "INSERT INTO ucitelPredmety (predmetzkratka, ucitIdno) VALUES ( ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$zkratka, $ucitIdno]);

        echo nl2br("Inserted succesfully" . $zkratka . " " . $ucitIdno . "\n");
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }   
}

function insertErrNumber($ucitIdno){
    try {
        require "dbh.inc.php";

        $query = "INSERT INTO errnumber (ucitIdno) VALUES (?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ucitIdno]);

        // Close statement and database connection properly
//        $stmt = null;
//        $pdo = null;
        echo nl2br("Inserted error number: " . $ucitIdno . "\n");
//        die(); // Optional: stop script execution
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}