<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="stylepage.css"> 
        <script src="webfunc.js"></script>
    </head>

    <body>

        <?php
        include_once '../includes/functions.php';
        include_once '../includes/dbh.inc.php';
        $pdo = connectToDatabase();
        if(isset($_POST['load'])){
            deleteUcitele($pdo);
            deleteUcitelPredmety($pdo);
            getUcitele($pdo);
        }
        ?>
        
        <h1>INSERT</h1>

        <form method="post">
            <input type="submit" name="load" value="Load data">
        </form>
    </body>

</html>