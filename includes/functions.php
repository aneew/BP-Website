<?php

function myMessage(){
    echo "hello world!";
}

// function getUcitele($pdo){

// // API endpoint
//     $api_url = 'https://stag-ws.utb.cz/ws/services/rest2/ucitel/getUciteleKatedry?lang=en&outputFormat=JSON&katedra=FAI&jenAktualni=true';

// // Fetch data from the API
//     $response = file_get_contents($api_url);

// // Check if the request was successful
//     if ($response === FALSE) {
//     // Handle error if the request failed
//         echo "Error occurred while fetching data from the API.";
//     } else {
//         $data = json_decode($response, true);
//         if ($data === NULL) {
//         echo "Error decoding JSON response.";
//     } else {
// //        $api_response = $data;
        
//         if (isset($data['ucitel'])) {
//             deleteUcitele($pdo);
//                 // Iterate through each teacher and print their name
//             foreach ($data['ucitel'] as $teacher) {
//                 insertUcitel($pdo ,$teacher['jmeno'], $teacher['prijmeni'], $teacher['ucitIdno']);
//                 getPredmetyUcitel($pdo ,$teacher['ucitIdno']);
//                 // echo $teacher['jmeno'] . " " . $teacher['prijmeni'];
//             }
//         } else {
//                 echo "No teachers found in the response.";
//                 }
// //            var_dump($api_response);
//         }
//     }
// }

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
    $rok = getYear($pdo);
    $api_url = "https://stag-ws.utb.cz/ws/services/rest2/programy/getStudijniProgramy?kod=%25&pouzePlatne=true&fakulta=" . "&outputFormat=JSON&rok=" . $rok;
    
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

//dodelat
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
                nazev varchar(50),
                aktualnipracoviste int);
                ";
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

function getSemestr($pdo){
    $pdo = connectToDatabase();
    $stmt = $pdo->query("SELECT semestr FROM roky WHERE aktualnisemestr=1;");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $semestr = (string) $result['semestr'];
//    echo $year;
    return $semestr;
}

function aktualniSemestr($pdo, $semestr){
    $query = "UPDATE semestr SET aktualnisemestr=0;
              UPDATE semestr SET aktualnisemestr=1 WHERE semestr='" . $semestr . "';";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo $query;
    echo nl2br("Deleted succesfully\n"); 
}

function onInit($pdo){
    try {
        deleteAll($pdo);
    }
    catch (Exception $e) {
        echo "nothing to delete";
    }
    $query = "create table roky(
        rok int primary key,
        akademickyrok varchar(10),
        zvoleny int);
        INSERT INTO roky (rok, akademickyrok, zvoleny) VALUES (2023, '2023/2024', 1);
        INSERT INTO roky (rok, akademickyrok) VALUES (2024, '2024/2025');
        INSERT INTO roky (rok, akademickyrok) VALUES (2025, '2025/2026');
        INSERT INTO roky (rok, akademickyrok) VALUES (2026, '2026/2027');
        INSERT INTO roky (rok, akademickyrok) VALUES (2027, '2027/2028');
        INSERT INTO roky (rok, akademickyrok) VALUES (2028, '2028/2029');
        INSERT INTO roky (rok, akademickyrok) VALUES (2029, '2029/2030');
        INSERT INTO roky (rok, akademickyrok) VALUES (2030, '2030/2031');
        INSERT INTO roky (rok, akademickyrok) VALUES (2031, '2031/2032');
        INSERT INTO roky (rok, akademickyrok) VALUES (2032, '2032/2033');
        INSERT INTO roky (rok, akademickyrok) VALUES (2033, '2033/2034');
        INSERT INTO roky (rok, akademickyrok) VALUES (2034, '2034/2035');
        INSERT INTO roky (rok, akademickyrok) VALUES (2035, '2035/2036');
        INSERT INTO roky (rok, akademickyrok) VALUES (2036, '2036/2037');
        INSERT INTO roky (rok, akademickyrok) VALUES (2037, '2037/2038');
        INSERT INTO roky (rok, akademickyrok) VALUES (2038, '2038/2039');
        INSERT INTO roky (rok, akademickyrok) VALUES (2039, '2039/2040');
        INSERT INTO roky (rok, akademickyrok) VALUES (2040, '2040/2041');
        INSERT INTO roky (rok, akademickyrok) VALUES (2041, '2041/2042');
        INSERT INTO roky (rok, akademickyrok) VALUES (2042, '2042/2043');
        INSERT INTO roky (rok, akademickyrok) VALUES (2043, '2043/2044');
        INSERT INTO roky (rok, akademickyrok) VALUES (2044, '2044/2045');
        INSERT INTO roky (rok, akademickyrok) VALUES (2045, '2045/2046');
        INSERT INTO roky (rok, akademickyrok) VALUES (2046, '2046/2047');
        INSERT INTO roky (rok, akademickyrok) VALUES (2047, '2047/2048');
        INSERT INTO roky (rok, akademickyrok) VALUES (2048, '2048/2049');
        INSERT INTO roky (rok, akademickyrok) VALUES (2049, '2049/2050');
        INSERT INTO roky (rok, akademickyrok) VALUES (2050, '2050/2051');
        INSERT INTO roky (rok, akademickyrok) VALUES (2051, '2051/2052');
        INSERT INTO roky (rok, akademickyrok) VALUES (2052, '2052/2053');
        INSERT INTO roky (rok, akademickyrok) VALUES (2053, '2053/2054');
        INSERT INTO roky (rok, akademickyrok) VALUES (2054, '2054/2055');
        INSERT INTO roky (rok, akademickyrok) VALUES (2055, '2055/2056');
        INSERT INTO roky (rok, akademickyrok) VALUES (2056, '2056/2057');
        INSERT INTO roky (rok, akademickyrok) VALUES (2057, '2057/2058');
        INSERT INTO roky (rok, akademickyrok) VALUES (2058, '2058/2059');
        INSERT INTO roky (rok, akademickyrok) VALUES (2059, '2059/2060');
        INSERT INTO roky (rok, akademickyrok) VALUES (2060, '2060/2061');
        INSERT INTO roky (rok, akademickyrok) VALUES (2061, '2061/2062');
        INSERT INTO roky (rok, akademickyrok) VALUES (2062, '2062/2063');
        INSERT INTO roky (rok, akademickyrok) VALUES (2063, '2063/2064');
        INSERT INTO roky (rok, akademickyrok) VALUES (2064, '2064/2065');
        INSERT INTO roky (rok, akademickyrok) VALUES (2065, '2065/2066');
        INSERT INTO roky (rok, akademickyrok) VALUES (2066, '2066/2067');
        INSERT INTO roky (rok, akademickyrok) VALUES (2067, '2067/2068');
        INSERT INTO roky (rok, akademickyrok) VALUES (2068, '2068/2069');
        INSERT INTO roky (rok, akademickyrok) VALUES (2069, '2069/2070');
        INSERT INTO roky (rok, akademickyrok) VALUES (2070, '2070/2071');
        INSERT INTO roky (rok, akademickyrok) VALUES (2071, '2071/2072');
        INSERT INTO roky (rok, akademickyrok) VALUES (2072, '2072/2073');
        INSERT INTO roky (rok, akademickyrok) VALUES (2073, '2073/2074');
        INSERT INTO roky (rok, akademickyrok) VALUES (2074, '2074/2075');
        INSERT INTO roky (rok, akademickyrok) VALUES (2075, '2075/2076');
        INSERT INTO roky (rok, akademickyrok) VALUES (2076, '2076/2077');
        INSERT INTO roky (rok, akademickyrok) VALUES (2077, '2077/2078');
        INSERT INTO roky (rok, akademickyrok) VALUES (2078, '2078/2079');
        INSERT INTO roky (rok, akademickyrok) VALUES (2079, '2079/2080');
        INSERT INTO roky (rok, akademickyrok) VALUES (2080, '2080/2081');
        INSERT INTO roky (rok, akademickyrok) VALUES (2081, '2081/2082');
        INSERT INTO roky (rok, akademickyrok) VALUES (2082, '2082/2083');
        INSERT INTO roky (rok, akademickyrok) VALUES (2083, '2083/2084');
        INSERT INTO roky (rok, akademickyrok) VALUES (2084, '2084/2085');
        INSERT INTO roky (rok, akademickyrok) VALUES (2085, '2085/2086');
        INSERT INTO roky (rok, akademickyrok) VALUES (2086, '2086/2087');
        INSERT INTO roky (rok, akademickyrok) VALUES (2087, '2087/2088');
        INSERT INTO roky (rok, akademickyrok) VALUES (2088, '2088/2089');
        INSERT INTO roky (rok, akademickyrok) VALUES (2089, '2089/2090');
        INSERT INTO roky (rok, akademickyrok) VALUES (2090, '2090/2091');
        INSERT INTO roky (rok, akademickyrok) VALUES (2091, '2091/2092');
        INSERT INTO roky (rok, akademickyrok) VALUES (2092, '2092/2093');
        INSERT INTO roky (rok, akademickyrok) VALUES (2093, '2093/2094');
        INSERT INTO roky (rok, akademickyrok) VALUES (2094, '2094/2095');
        INSERT INTO roky (rok, akademickyrok) VALUES (2095, '2095/2096');
        INSERT INTO roky (rok, akademickyrok) VALUES (2096, '2096/2097');
        INSERT INTO roky (rok, akademickyrok) VALUES (2097, '2097/2098');
        INSERT INTO roky (rok, akademickyrok) VALUES (2098, '2098/2099');
        INSERT INTO roky (rok, akademickyrok) VALUES (2099, '2099/2100');
        create table semestr(
            semestr varchar(3) primary key,
            popis varchar(15),
            aktualnisemestr int);
        insert into semestr (semestr, popis) values ('ZS', 'Zimni semestr');
        insert into semestr (semestr, popis) values ('LS', 'Letni semestr');
        create table pracoviste(
            idpracoviste int primary key,
            zkratka varchar(7),
            typpracoviste varchar(2),
            nadrazenepracoviste varchar(7),
            nazev varchar(50),
            aktualnipracoviste int);
        create table cisfakulta(
            idcis int primary key auto_increment,
            zkratka varchar(5));
        insert into cisfakulta (zkratka) values ('FAI');
        insert into cisfakulta (zkratka) values ('FAM');
        insert into cisfakulta (zkratka) values ('FLK');
        insert into cisfakulta (zkratka) values ('FMK');
        insert into cisfakulta (zkratka) values ('FHS');
        insert into cisfakulta (zkratka) values ('FT');
        insert into cisfakulta (zkratka) values ('IMS');
        create table predmet(
            zkratka varchar(10) primary key,
            nazev varchar(50),
            cviciciUcitIdno varchar(200),
            seminariciUcitIdno varchar(200),
            prednasejiciUcitIdno varchar(200),
            vyucovaciJazyky varchar(30),
            nahrazPredmety varchar(30));
        create table studijniprogram (
            stprIdno int primary key,
            nazev varchar(100),
            kod varchar(20),
            platnyod int,
            pocetprijimanych varchar(50),
            stddelka varchar(4),
            pocetstudentu int
            );
        create table teachers (
            id int(10),
            name varchar(50),
            surname varchar(50),
            ucitIdno int,
            iddbversion int);
        create table ucitelpredmety(
            id int primary key auto_increment,
            ucitIdno int,
            predmetzkratka varchar(20),
            iddbversion int);
        create table predmetlast(
            zkratka varchar(10) primary key,
            nazev varchar(50),
            cviciciUcitIdno varchar(200),
            seminariciUcitIdno varchar(200),
            prednasejiciUcitIdno varchar(200),
            vyucovaciJazyky varchar(30),
            nahrazPredmety varchar(30),
            rok int);    
        ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
//    echo $query;
    echo nl2br("Deleted succesfully\n"); 

}

function deleteAll($pdo){
    $query = "DROP TABLE cisfakulta;
    DROP TABLE pracoviste;
    DROP TABLE predmet;
    DROP TABLE roky;
    DROP TABLE semestr;
    DROP TABLE studijniprogram;
    DROP TABLE teachers;
    DROP TABLE ucitelpredmety;
    DROP TABLE predmetlast;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 
}

function getUcitele($pdo){

    // API endpoint
        $katedra = getKatedra($pdo);
        $api_url = "https://stag-ws.utb.cz/ws/services/rest2/ucitel/getUciteleKatedry?lang=en&outputFormat=JSON&katedra=" . $katedra . "&jenAktualni=true";
    
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

function getKatedra($pdo){
    $pdo = connectToDatabase();
    $stmt = $pdo->query("SELECT DISTINCT nadrazenepracoviste FROM pracoviste");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $katedra = (string) $result['nadrazenepracoviste'];
//    echo $year;
    return $katedra;
}


function setKatedra($pdo, $katedra){
    $query = "UPDATE pracoviste SET aktualnipracoviste=0;
              UPDATE aktualnipracoviste SET aktualnipracoviste=1 WHERE katedra='" . $katedra . "';";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    // echo $query;
    echo nl2br("Deleted succesfully\n"); 
}

function getPredmetyByKatedraLast($pdo, $katedra){
    $year = getYear($pdo) - 1;
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
        deletePredmetLast($pdo);
        foreach ($data['predmetKatedryFullInfo'] as $predmet) {
            insertPredmetLast($pdo, $predmet['zkratka'], $predmet['nazev'], $predmet['cviciciUcitIdno'], $predmet['seminariciUcitIdno'], $predmet['prednasejiciUcitIdno'], $predmet['vyucovaciJazyky'], $predmet['rok']);
            // $ints = parseStringToIntegers($predmet['cviciciUcitIdno']);
            // echo $ints;
        }
    }
}

function insertPredmetLast($pdo, $zkratka, $nazev, $cviciciUcitIdno, $seminariciUcitIdno, $prednasejiciUcitIdno, $vyucovaciJazyky, $rok){
    try {
        $query = "INSERT INTO predmetlast (zkratka, nazev, cviciciUcitIdno, seminariciUcitIdno, prednasejiciUcitIdno, vyucovaciJazyky, rok) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$zkratka, $nazev, $cviciciUcitIdno, $seminariciUcitIdno, $prednasejiciUcitIdno, $vyucovaciJazyky, $rok]);

        echo nl2br("Inserted successfully: " . $nazev . " " . $rok . "\n");
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}

function deletePredmetLast($pdo){
    $query = "DROP TABLE predmetlast;
                create table predmetlast(
                    zkratka varchar(10) primary key,
                    nazev varchar(50),
                    cviciciUcitIdno varchar(200),
                    seminariciUcitIdno varchar(200),
                    prednasejiciUcitIdno varchar(200),
                    vyucovaciJazyky varchar(30),
                    nahrazPredmety varchar(30),
                    rok int)";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    echo nl2br("Deleted succesfully\n"); 

}