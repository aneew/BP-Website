<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../stylepage.css"> 
        <script src="../webfunc.js"></script>
    </head>

    <body>

        <div id="navbar">
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

        <div id="content" class="rounded-border">
        <?php
        include_once '../includes/functions.php';
        include_once '../includes/dbh.inc.php';
        $pdo = connectToDatabase();
        getYear($pdo);
        if(isset($_POST['load'])){
            $fakulta = $_POST['fakulta'];
            getKatedry($pdo, $fakulta);
            // deleteUcitele($pdo);
            // deleteUcitelPredmety($pdo);
            // deleteStudijniProgramy($pdo);
            // getUcitele($pdo);
            // getStudijniProgram($pdo);
        }

        if(isset($_POST['predmety'])){
            $katedra = $_POST['katedra'];
            getPredmetyByKatedra($pdo, $katedra);
            getPredmetyByKatedraLast($pdo, $katedra);
            setKatedra($pdo, $katedra);
            getUcitele($pdo);
        }

        if(isset($_POST['x'])){
            $rok = $_POST['rok'];
            aktualnirok($pdo, $rok);
        }

        if(isset($_POST['semestr'])){
            $semestr = $_POST['semestr'];
            aktualniSemestr($pdo, $semestr);
        }

        if(isset($_POST['oninit'])){
            onInit($pdo);
        }

        ?>
        
        <h1>INSERT</h1>

        <p>Začít/začít od začátku</p>
        <form method="post">
            <input type="submit" name="oninit" value="load">
        </form>

        <p>Vyberte akademický rok:</p>
        <form method="post">
        <select id="rok" name="rok">
                <!-- Option for selection -->
                <option value="">Vyberte...</option>
                <?php
                $pdo = connectToDatabase();
                $stmt = $pdo->query("SELECT rok, akademickyrok FROM roky;");
                $roky = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($roky as $rok) {
                    echo "<option value='{$rok['rok']}'>{$rok['akademickyrok']}</option>";
                }
            ?>       
            </select>
            <input type="submit" name="x" value="load">
        </form>

        <p>Vyberte semestr:</p>
        <form method="post">
        <select id="semestr" name="semestr">
            <!-- Možnosti pro výběr -->
            <option value="">Vyberte...</option>
            <?php
            $pdo = connectToDatabase();
            $stmt = $pdo->query("SELECT semestr FROM semestr;");
            $semestry = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($semestry as $semestr) {
                echo "<option value='{$semestr['semestr']}'>{$semestr['semestr']}</option>";
            }
            ?>       
            </select>
            <input type="submit" name="semestr_submit" value="Načíst">

        </form>

        <p>Načíst katedry fakulty:</p>
        <form method="post">
        <select id="fakulta" name="fakulta">
                <!-- Option for selection -->
                <option value="">Vyberte...</option>
                <?php
                $pdo = connectToDatabase();
                $stmt = $pdo->query("SELECT zkratka FROM cisfakulta;");
                $zkratky = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($zkratky as $zkratka) {
                    echo "<option value='{$zkratka['zkratka']}'>{$zkratka['zkratka']}</option>";
                }
                ?>       
            </select>
            <input type="submit" name="load" value="Load data">
        </form>        

        <p>Načíst předměty katedry:</p>
        <form method="post">
        <select id="katedra" name="katedra">
                <!-- Option for selection -->
                <option value="">Vyberte...</option>
                <?php
                $pdo = connectToDatabase();
                $stmt = $pdo->query("SELECT zkratka, nazev FROM pracoviste ORDER BY zkratka;");
                $zkratky = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($zkratky as $zkratka) {
                    echo "<option value='{$zkratka['zkratka']}'>{$zkratka['zkratka']} - {$zkratka['nazev']}</option>";
                }
                ?>       
            </select>
            <input type="submit" name="predmety" value="Load data">
        </form>        

        </div>
        <button id="toggleButton" onclick="toggleNavbar()">Skrýt Menu</button>
    </body>

</html>