<?php

function myMessage(){
    echo "hello world!";
}

function getUcitele($pdo){

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
            deleteUcitele($pdo);
                // Iterate through each teacher and print their name
            foreach ($data['ucitel'] as $teacher) {
                insertUcitel($pdo ,$teacher['jmeno'], $teacher['prijmeni'], $teacher['ucitIdno']);
                getPredmetyUcitel($pdo ,$teacher['ucitIdno']);
                // echo $teacher['jmeno'] . " " . $teacher['prijmeni'];
            }
        } else {
                echo "No teachers found in the response.";
                }
//            var_dump($api_response);
        }
    }
}

function insertUcitel($pdo ,$name, $surname, $ucitIdno){
    try {
        // require "dbh.inc.php";

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

function deleteUcitele($pdo)
{
    
//    require "dbh.inc.php";
    $query = "DELETE FROM teachers;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

function deleteUcitelPredmety($pdo)
{
    
    // require "dbh.inc.php";
    $query = "DELETE FROM ucitelPredmety;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

function getPredmetyUcitel($pdo ,$ucitIdno){
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
            insertPredmetyByUcitel($pdo ,$predmet['zkratka'], $ucitIdno);
            // echo nl2br($predmet['zkratka'] . "\n");
        }
    } catch (Exception $e) {
        // Handle the exception, log it, or add the id of the teacher to an array
//        $failed_ucitIdno_array[] = $ucitIdno;
        //  error handle
        insertErrNumber($pdo ,$ucitIdno);
        echo "Error: " . $e->getMessage();
    }

}

function getStudijniProgram($pdo){
    $api_url = "https://stag-ws.utb.cz/ws/services/rest2/programy/getStudijniProgramy?kod=%25&pouzePlatne=true&fakulta=FAI&outputFormat=JSON&rok=2024";
    
    $response = file_get_contents($api_url);

    if ($response === FALSE) {
        echo "error in connection";
    }
    else {
        $data = json_decode($response, true);
        if ($data === NULL) {
            echo "Error decoding JSON response.";
        }

        foreach ($data['programInfo'] as $stp) {
            insertStudijniProgram($pdo, $stp['stprIdno'], $stp['nazev'], $stp['kod'], $stp['platnyOd'], $stp['pocetPrijimanych'], $stp['stdDelka']);
            // echo $teacher['jmeno'] . " " . $teacher['prijmeni'];
        }

    }
}

function insertStudijniProgram($pdo, $stprIdno, $nazev, $kod, $platnyod, $pocetprijimanych, $stddelka){
    try{
        $query = "INSERT INTO studijniprogram (stprIdno, nazev, kod, platnyod, pocetprijimanych, stddelka) VALUES ( ?, ?, ?, ?, ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$stprIdno, $nazev, $kod, $platnyod, $pocetprijimanych, $stddelka]);

        echo nl2br("Inserted succesfully" . $nazev . "\n");
    }
    catch(PDOException $e) {
        echo "error" . $e->getMessage();
    }
}

function insertPredmetyByUcitel($pdo ,$zkratka, $ucitIdno){
    try {
        // require "dbh.inc.php";

        $query = "INSERT INTO ucitelPredmety (predmetzkratka, ucitIdno) VALUES ( ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$zkratka, $ucitIdno]);

        echo nl2br("Inserted succesfully" . $zkratka . " " . $ucitIdno . "\n");
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }   
}

function insertErrNumber($pdo ,$ucitIdno){
    try {
        // require "dbh.inc.php";

        $query = "INSERT INTO errnumber (ucitIdno) VALUES (?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$ucitIdno]);

        // Close statement and database connection properly
//        $stmt = null;
//        $pdo = null;
        echo nl2br("Inserted error number: " . $ucitIdno . "\n");
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

// function loadTeachers($pdo) {
//     $stmt = $pdo->query("SELECT name, surname FROM teachers");
//     $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
//     // show list of teachers
//     echo "<h2>Teachers</h2>";
//     echo "<ul>";
//     foreach ($teachers as $teacher) {
//         echo "<li>{$teacher['name']} {$teacher['surname']}</li>";
//     }
//     echo "</ul>";
// }

function loadTeachers($pdo) {
    $stmt = $pdo->query("SELECT ucitIdno, name, surname FROM teachers;");
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // show list of teachers
    echo "<h2>Teachers</h2>";
    echo "<ul>";
    foreach ($teachers as $teacher) {
        echo "<li><a href='javascript:void(0);' onclick='selectTeacher({$teacher['ucitIdno']});'>{$teacher['name']} {$teacher['surname']}</a></li>";
    }
    echo "</ul>";
}

function loadStudijniProgramy($pdo) {
    $stmt = $pdo->query("SELECT * FROM studijniprogram;");
    $programs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // show list of studijni programy
    echo "<h2>Programs</h2>";

    foreach ($programs as $program) {
        echo "<div class='card'>
            <h2>{$program['nazev']}</h3> 
            <div class='ul-card'>
            <ul>
                <li>kod: {$program['kod']}</li>
                <li>platnyod: {$program['platnyod']}</li> 
                <li>pocetprijmanych: {$program['pocetprijimanych']}</li>
            </ul>
            </div>
            </div>";
    }
}

function updateStudentNumber($pdo, $number, $stprIdno){
    $query = $pdo->prepare("UPDATE studijniprogram SET pocetstudentu=? WHERE stprIdno=?");
    $query->execute([$number, $stprIdno]);
}

function deleteStudijniProgramy($pdo)
{  
//    require "dbh.inc.php";
    $query = "DELETE FROM studijniprogram;";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

// function getKatedry($pdo, $fakulta){
//     $api_url = "https://stag-ws.utb.cz/ws/services/rest2/ciselniky/getSeznamPracovist?typPracoviste=%25&zkratka=%25&nadrazenePracoviste=%25&outputFormat=JSON";
    
//     $response = file_get_contents($api_url);

//     if ($response === FALSE) {
//         echo "error in connection";
//     }
//     else {
//         $data = json_decode($response, true);
//         if ($data === NULL) {
//             echo "Error decoding JSON response.";
//         }
        
//         foreach ($data['pracoviste'] as $pracoviste) {
//             //echo $pracoviste;
//             if (['nadrazenepracoviste'==$fakulta]){
//             insertPracoviste($pdo, ['cisloPracoviste'], ['zkratka'], ['typpracoviste'], ['nadrazenepracoviste'], ['nazev']);
//             // echo $teacher['jmeno'] . " " . $teacher['prijmeni'];
//             }
//         }
//     }
// }

// function insertPracoviste($pdo, $idpracoviste, $zkratka, $typpracoviste, $nadrazenepracoviste, $nazev){
//     try {
//         // require "dbh.inc.php";

//         $query = "INSERT INTO pracoviste (idpracoviste, zkratka, typpracoviste, nadrazenepracoviste, nazev) VALUES ( ?, ?, ?, ?, ?);";
//         $stmt = $pdo->prepare($query);
//         $stmt->execute([$idpracoviste, $zkratka, $typpracoviste, $nadrazenepracoviste, $nazev]);

//         echo nl2br("Inserted succesfully" . $nazev . "\n");
//     } catch (PDOException $e) {
//         die("Query failed: " . $e->getMessage());
//     }
// }

function getKatedry($pdo, $fakulta){
    $api_url = "https://stag-ws.utb.cz/ws/services/rest2/ciselniky/getSeznamPracovist?typPracoviste=%25&zkratka=%25&nadrazenePracoviste=%25&outputFormat=JSON";
    
    $response = file_get_contents($api_url);

    if ($response === FALSE) {
        echo "error in connection";
    }
    else {
        $data = json_decode($response, true);
        if ($data === NULL) {
            echo "Error decoding JSON response.";
        }
        deleteKatedry($pdo);
        foreach ($data['pracoviste'] as $pracoviste) {
            if ($pracoviste['nadrazenePracoviste'] == $fakulta) {
                insertPracoviste($pdo, $pracoviste['cisloPracoviste'], $pracoviste['zkratka'], $pracoviste['typPracoviste'], $pracoviste['nadrazenePracoviste'], $pracoviste['nazev']);
            }
        }
    }
}

function insertPracoviste($pdo, $idpracoviste, $zkratka, $typpracoviste, $nadrazenepracoviste, $nazev){
    try {
        $query = "INSERT INTO pracoviste (idpracoviste, zkratka, typpracoviste, nadrazenepracoviste, nazev) VALUES (?, ?, ?, ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$idpracoviste, $zkratka, $typpracoviste, $nadrazenepracoviste, $nazev]);

        echo nl2br("Inserted successfully: " . $nazev . "\n");
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

function getPredmetyByKatedra($pdo, $katedra){
    $year = getYear($pdo);
    $api_url = "https://stag-ws.utb.cz/ws/services/rest2/predmety/getPredmetyByKatedraFullInfo?semestr=LS&outputFormat=JSON&katedra=" . $katedra . "&rok=" . $year;
    $response = file_get_contents($api_url);
    echo $api_url;

    if ($response === FALSE) {
        echo "error in connection";
    }
    else {
        $data = json_decode($response, true);
        if ($data === NULL) {
            echo "Error decoding JSON response.";
        }
        deletePredmet($pdo);
        foreach ($data['predmetKatedryFullInfo'] as $predmet) {
            insertPredmet($pdo, $predmet['zkratka'], $predmet['nazev'], $predmet['cviciciUcitIdno'], $predmet['seminariciUcitIdno'], $predmet['prednasejiciUcitIdno'], $predmet['vyucovaciJazyky'], $predmet['rok']);
            // $ints = parseStringToIntegers($predmet['cviciciUcitIdno']);
            // echo $ints;
        }
    }
}

function insertPredmet($pdo, $zkratka, $nazev, $cviciciUcitIdno, $seminariciUcitIdno, $prednasejiciUcitIdno, $vyucovaciJazyky, $rok){
    try {
        $query = "INSERT INTO predmet (zkratka, nazev, cviciciUcitIdno, seminariciUcitIdno, prednasejiciUcitIdno, vyucovaciJazyky, rok) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$zkratka, $nazev, $cviciciUcitIdno, $seminariciUcitIdno, $prednasejiciUcitIdno, $vyucovaciJazyky, $rok]);

        echo nl2br("Inserted successfully: " . $nazev . " " . $rok . "\n");
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

function deleteKatedry($pdo)
{  
//    require "dbh.inc.php";
    $query = "DROP TABLE pracoviste;
            create table pracoviste(
                idpracoviste int primary key,
                zkratka varchar(7),
                typpracoviste varchar(2),
                nadrazenepracoviste varchar(7),
                nazev varchar(50));";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

function deletePredmet($pdo){
    $query = "DROP TABLE predmet;
    create table predmet(
        zkratka varchar(10) primary key,
        nazev varchar(50),
        cviciciUcitIdno varchar(200),
        seminariciUcitIdno varchar(200),
        prednasejiciUcitIdno varchar(200),
        vyucovaciJazyky varchar(30),
        nahrazPredmety varchar(30),
        rok int);";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

function parseStringToIntegers($string) {
    // Split the string into an array based on the comma delimiter
    $integers = explode(',', $string);
    
    // Trim whitespace from each substring and convert it to an integer
    $integers = array_map('trim', $integers);
    $integers = array_map('intval', $integers);
    
    return $integers;
}

function aktualnirok($pdo, $rok){
    $query = "UPDATE roky SET zvoleny=0;
        UPDATE roky SET zvoleny=1 WHERE rok=" . $rok . ";";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo $query;
    echo nl2br("Deleted succesfully\n"); 
}

function getYear($pdo){
    $pdo = connectToDatabase();
    $stmt = $pdo->query("SELECT rok FROM roky WHERE zvoleny=1;");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $year = (int) $result['rok'];
//    echo $year;
    return $year;
}