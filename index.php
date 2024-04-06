<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="stylepage.css"> 
        <script src="webfunc.js"></script>
    </head>

    <body>

        <div id="navbar">
            <ul>
	            <h1 style="text-align: center;">Menu</h1>

                <!-- <li><a href="#" onclick="loadPage('pages/insert.php')">Insert</a></li> -->
                <li><a href="#" onclick="loadPage('pages/view.php')">View</a></li>
                <!-- <li><a href="#" onclick="loadPage('pages/viewprograms.php')">ViewPrograms</a></li> -->
                <li><a href="#" onclick="loadPage('pages/edit.php')">Edit</a></li>
                <li><a href="pages/insert1.php">Insert do DB</a></li>
            </ul>
        </div>

        <div id="content" class="rounded-border">

            <h1>Automatizace tvorby pracovnich uvazku</h1>
                <p>Daniel Gágyor</p>
                <p>Univerzita Tomase Bati (UTB)</p>
                <p>FAI - Softwarové inženýrství</p>
        </div>
        <button id="toggleButton" onclick="toggleNavbar()">Skrýt Menu</button>
    </body>

</html>