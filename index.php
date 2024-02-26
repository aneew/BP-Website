<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
    </head>

    <body>
        <?php
            include_once 'includes/functions.php';
            if(isset($_POST['load'])){
                deleteUcitele();
                deleteUcitelPredmety();
                getUcitele();
//                getPredmetyUcitel(16843);
            }
        ?>

        <form action="includes/formhandler.inc.php" method="post">
        <!--    <input type="text" name="id" placeholder="id"> -->
            <input type="text" name="name" placeholder="name">
            <input type="text" name="surname" placeholder="surname">
            <button>Signup</button>
        </form>

        <form method="post">
            <input type="submit" name="load" value="Load data">
        </form>

    </body>

</html>