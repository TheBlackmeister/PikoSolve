<?php
session_start();
$errorlocal=''; // promenna pro praci s lokalnimi errory na strance
if(isset($_POST['logout'])){
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                unset($_SESSION['user']);
                unset($_SESSION['opravovatel']);
                unset($_SESSION['admin']);
                session_unset();
                session_destroy();
                header("location: login.php"); exit();
            }
            else {
                unset($_SESSION['csrftoken']);
                unset($_SESSION['csrftoken_time']);
                $errorlocal = "Platnost CSRF tokenu vypršela. Načti znovu stránku a pošli příspěvek.";
            }
        }
    } // csrf validace
    else {
        $errorlocal = "Nesprávný CSRF token. Načti znovu stránku a pošli příspěvek.";
    }
} // logout

// vytvoreni csrf tokenu
$token = uniqid(rand(),true);
$_SESSION['csrftoken'] = $token;
$_SESSION['csrftoken_time'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/styles_index.css" rel="stylesheet" id="theme-link">
    <script src="js/darkmode_index.js"></script>
    <title>Informace o PikoSolve</title>
</head>
<body onLoad ="checkLS()">  <!--darkmode-->
<ul class ="navbar">
    <li><a class="logo" href="index.php"><img class="logocolor" src="img/bile_logo.svg" alt="logo" width="35" height="19">PikoSolve</a></li>
    <li><a href="onas.php">O nás</a></li>
    <?php
    if(isset($_SESSION['user'])){
        echo '<li><a href="newdoc.php">Nový příklad</a></li>';
    }
    ?>
    <li class="darkmodebtn"><a><img src="img/moon.png" alt="moon" width="19" height="19"></a></li>
    <?php
    if(isset($_SESSION['user'])){
        echo '<li class ="navright"><a href="myprofile.php">Můj profil</a></li>';
    }
    ?>
    <?php
    if(!isset($_SESSION['user'])){
        echo '<li class ="navright"><a href="login.php">Login</a></li>';
    }
    ?>
</ul>

<div class="navbar">
    <div class="dropdown">
        <button class="dropbtn"><img class="logocolor" src="img/bile_logo.svg" alt="logo" width="35" height="19">PikoSolve
            <i class="fa fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="index.php">Hlavní stránka</a>
            <a href="onas.php">O nás</a>
            <?php
            if(isset($_SESSION['user'])){
                echo '<a href="newdoc.php">Nový příklad</a>';
            }
            ?>
            <?php
            if(isset($_SESSION['user'])){
                echo '<a href="myprofile.php">Můj profil</a>';
            }
            ?>
            <?php
            if(!isset($_SESSION['user'])){
                echo '<a href="login.php">Login</a>';
            }
            ?>
            <a class="darkmodebtn"><img src="img/moon.png" alt="moon" width="19" height="19"> Dark Mode</a>
        </div>
    </div>
</div>

<main>
    <div class="grid-container">
        <div>
            <header> <h1> Informace o projektu PikoSolve </h1></header>
        </div>
        <div>
            <p>Vítám vás na stránkách studentského projektu PikoSolve!</p>
            <p>Web umožňuje přihlášeným uživatelům (zpravidla řešitelům PIKOMATu) nahrávat matematické příklady/dotazy, skládající se z textu, popř. obrázku popisující příklad. Dotaz se objeví jako příspěvek v chronologickém seznamu po 10 příspěvcích. OPRAVOVATEL příklad vyřeší, pověsí textovou odpověď, popíše – autor příspěvku, popř. ostatní přihlášení mohou komentovat. V případě potřeby se doptá a autor může otázku doplnit formou komentáře. Nevyřešené úlohy lze lehce odlišit od vyřešených na hlavní stránce zelenou barvou.
            Vkládat komentáře může jakýkoli přihlášený uživatel, avšak opravovatelé a adminové je mohou mazat. Přihlášený uživatel může na otevřeném příspěvku vkládat nové komentáře. Odpovědi od opravovatelů jsou též jasně označené zelenou barvou. Po rozkliknutí na detail je možnost přidání komentáře kýmkoliv, dokud bude příspěvek odemčen.</p>

        </div>
        <div>
            <h2 class="h2onas">Martin Černý &nbsp;- Autor projektu</h2>
            <img src="img/profile_me.jpeg" alt="Profilová fotka autora projektu">
            <p>Ahoj! Jmenuji se Martin a jsem studentem prvního ročníku Softwarového inženýrství na FEL ČVUT. Rád fotografuji, zejména uměleckou a reportážní fotografii. Ve volném čase, když nějaký zrovna je, rád vařím, poslouchám hudbu, či hraju na kytaru.</p>
            <p>Kontaktujte mě na adrese:</p>
            <p>cernym68@fel.cvut.cz</p>
        </div>


    </div>

    <aside>
        <?php if(isset($_SESSION['user'])){
            echo '<li class="nohover">';
            echo '<h2> Ahoj uživateli ' .$_SESSION['user']. '!</h2>';
            if ($_SESSION['admin'] == 1) {
                echo "<p>Jsi admin!</p>";
            }
            if ($_SESSION['opravovatel'] == 1) {
                echo "<p>Jsi opravovatel!</p>";
            } // tohle ukazuje jestli jsem admin či opravovatel
            echo "<form method=\"post\"><button name=\"logout\" value=\"logout\">Odhlásit se</button>";
            echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";


            echo '</form>';
            echo '</li>';
        }
        ?>

        <ul>
            <li class="nohover"> <h2><a href="index.php">Poslední příspěvky:</a></h2></li>
            <?php
            $records = file_get_contents('bin/dataFiles/data.json');
            $records = json_decode($records, JSON_OBJECT_AS_ARRAY);
            $records = array_reverse($records);
            $record0 = $records[0];
            $record1 = $records[1];
            $record2 = $records[2];
            $username0= htmlspecialchars($record0['username']);
            $username1= htmlspecialchars($record1['username']);
            $username2= htmlspecialchars($record2['username']);
            $nadpis0 = htmlspecialchars($record0['nadpis']);
            $nadpis1 = htmlspecialchars($record1['nadpis']);
            $nadpis2 = htmlspecialchars($record2['nadpis']);
            $id0 = htmlspecialchars($record0['id']);
            $id1 = htmlspecialchars($record1['id']);
            $id2 = htmlspecialchars($record2['id']);

            echo "<li>";
            echo '<p> Uživatel ' .$username0. ' přidal příspěvek </p>';
            echo '<p>'.$nadpis0.'</p>';
            echo "<form action=\"detail.php\" method=\"get\"><button class='detailbutton' name=\"opendetail\" value=\"$id0\">";
            echo 'Detail';
            echo "</button></form>";
            echo '</li>';

            echo "<li>";
            echo '<p> Uživatel ' .$username1. ' přidal příspěvek </p>';
            echo '<p>'.$nadpis1.'</p>';
            echo "<form action=\"detail.php\" method=\"get\"><button class='detailbutton' name=\"opendetail\" value=\"$id1\">";
            echo 'Detail';
            echo "</button></form>";
            echo '</li>';

            echo "<li>";
            echo '<p> Uživatel ' .$username2. ' přidal příspěvek </p>';
            echo '<p>'.$nadpis2.'</p>';
            echo "<form action=\"detail.php\" method=\"get\"><button class='detailbutton' name=\"opendetail\" value=\"$id2\">";
            echo 'Detail';
            echo "</button></form>";
            echo '</li>';

            $records = array_reverse($records);
            ?>

        </ul>
    </aside> <!-- bocni prispevky -->
</main>
<script>darkmodefunc();</script>
</body>
</html>