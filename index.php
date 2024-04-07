<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="stylepage.css"> 
        <script src="webfunc.js"></script>
    </head>

    <body>
        <?php
        include_once 'includes/functions.php';
        include_once 'includes/dbh.inc.php';
        $pdo = connectToDatabase();
        if(isset($_POST['oninit'])){
            onInit($pdo);
        }

        ?>
        <div id="navbar">
            <ul>
	            <h1 style="text-align: center;">Menu</h1>
                <li><a href="index.php">Main</a></li>
                <!-- <li><a href="#" onclick="loadPage('pages/insert.php')">Insert</a></li> -->
                <li><a href="pages/view.php">View</a></li>
                <!-- <li><a href="#" onclick="loadPage('pages/viewprograms.php')">ViewPrograms</a></li> -->
                <li><a href="pages/edit.php">Edit</a></li>
                <li><a href="pages/insert1.php">Insert do DB</a></li>
                <li><a href="pages/result-counting.php">Vypocet</a></li>
            </ul>
        </div>

        <div id="content" class="rounded-border">

            <h1>Automatizace tvorby pracovnich uvazku</h1>
                <p>Daniel Gágyor</p>
                <p>Univerzita Tomase Bati (UTB)</p>
                <p>FAI - Softwarové inženýrství</p>
                <br>
                <br>

                <p>Začít/začít od začátku</p>
                <form method="post">
                    <input type="submit" name="oninit" value="Start">
                </form>
                <p><i>Pozn: Pokud při <b>prvním startu</b> toto tlačítko nezmáčknete, nebudou fungovat další funkce vaší aplikace.</i></p>

        <button id="toggleButton" onclick="toggleNavbar()">Skrýt Menu</button>


        </div>


    </body>

</html>