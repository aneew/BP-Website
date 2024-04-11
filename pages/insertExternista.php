<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../stylepages/stylepage_updatepocetstudentu.css"> 
</head>
<body>
    <div class="container">
        <?php
            include_once '../includes/functions.php';
            include_once '../includes/dbh.inc.php';
            $pdo = connectToDatabase();
            if(isset($_POST['send'])){
                $name = $_POST["name"];
                $surname = $_POST["surname"];
                novyExternista($pdo, $name, $surname);
            }
        ?>
        <h1>Tvorba nového externisty</h1>
        <form method="post">
            <input type="text" id="name" name="name"><br>
            <input type="text" id="surname" name="surname"><br>
            <input type="submit"  name="send" value="Vytvorit">
        </form>
    </div>
    <a href=../index.php> back to main </a>
</body>
</html>