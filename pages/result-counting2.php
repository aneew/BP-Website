<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../stylesheeres.css"> 
        <link rel="stylesheet" href="../stylepage.css"> 
        <script src="webfunc.js"></script>
    </head>

    <body>

        <div id="navbar" class="navbar">
            <ul>
                <h1 style="text-align: center;">Menu</h1>
                <li><a href="../index.php">Main</a></li>
                <!-- <li><a href="#" onclick="loadPage('pages/insert.php')">Insert</a></li> -->
                <li><a href="view.php">View</a></li>
                <!-- <li><a href="#" onclick="loadPage('pages/viewprograms.php')">ViewPrograms</a></li> -->
                <li><a href="edit.php">Edit</a></li>
                <li><a href="insert1.php">Insert do DB</a></li>
                <li><a href="result-counting.php">Vypocet</a></li>
            </ul>
        </div>
        <div class="rest rounded-border">
            <h1>Vypocet</h1>

        <?php
            include '../includes/functions.php';
            include '../includes/dbh.inc.php';
            $pdo = connectToDatabase();   
            loadPredmetyV4($pdo);
            deleteUppRecord2($pdo);
        ?>
        </div>
    </body>

</html>