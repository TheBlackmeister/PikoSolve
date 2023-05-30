<?php
session_start();
require 'bin/dataFiles/newstring.class.php';
require 'bin/dataFiles/newstringWithPic.class.php';

$errorlocaljpeg = Null; // promenna pro validaci jpeg
if(!isset($_SESSION['user'])){
    header('location: login.php'); exit();
}

$errorlocal='';
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





// VALIDACE OBRAZKU
if (isset($_POST['submitform'])) {
    if((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if(isset($_SESSION['csrftoken_time'])) {
            $max_time = 60*60*2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if(($token_time + $max_time) >= time()){
                if (isset($_FILES['fileToUpload']) && ($_FILES['fileToUpload']['size'] > 100)) { // validace obrazku, mensi nez 10 MB,
                    if (($_FILES['fileToUpload']['size'] < 10485760)) { // kontrola velikosti, jsou tu veci, ktere nelze kontrolovat po nahrani funkci $_FILES
                        if ($_FILES['fileToUpload']['type'] == "image/jpeg" || $_FILES['fileToUpload']['type'] == "image/png" || $_FILES['fileToUpload']['type'] == "image/gif" || $_FILES['fileToUpload']['type'] == "image/bmp") { // kontrola type JPEG
                            $unikatniid = uniqid() . '.jpg';
                            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "bin/dataFiles/pic/" . $unikatniid);
                            $Doc = new NewDocWithPic($_POST['nadpis'], $_POST['freeform'], "bin/dataFiles/pic/" . $unikatniid, $unikatniid);
                        } else {
                            $errorlocaljpeg = "Soubor není JPEG, PNG, GIF, nebo BMP! Prosím nahraj jeden z těchto formátů.";
                        } // kdyz neprojde zkouskou velikosti
                    } else {
                        $errorlocaljpeg = "Fotka je příliš velká!";
                    } // kdyz neprojde size zkouskou

                } else {
                    $Doc = new NewDoc($_POST['nadpis'], $_POST['freeform']);
                } // pokud neni obrazek
            } // platnost tokenu, else odstraneni
            else {
                unset($_SESSION['csrftoken']);
                unset($_SESSION['csrftoken_time']);
                $errorlocal = "Platnost CSRF tokenu vypršela. Načti znovu stránku a pošli příspěvek.";
            }
        } // casova platnost tokenu
    } // pokud je zadany csrf token
    else {
        $errorlocal = "Nesprávný CSRF token. Načti znovu stránku a pošli příspěvek.";
    }

} // validace odeslani, csrf a fotky

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
    <script src="js/validace_newdoc.js"></script>
    <title>Nový příspěvek PikoSolve</title>
</head>
<body onLoad ="checkLS()"> <!--darkmode-->
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
            <header> <h1> Matematické fórum PikoSolve </h1></header>
        </div>
        <form id="newdocform" action="newdoc.php" method="post" enctype="multipart/form-data"> <!--form data pro obrazek-->
            <div>

                <label for="nadpis">Nadpis příkladu:</label>
                <br>

                <input type="text" placeholder="Jaký bude nadpis?" id="nadpis" name="nadpis" maxlength="25" value="<?php echo isset($_POST["nadpis"]) ? $_POST["nadpis"] : ''; ?>">
            </div>

            <div>

            <label for="freeform">Popis příkladu:</label>
            <br>

            <textarea placeholder="Popiš nám svůj problém..." id="freeform" name="freeform"><?php echo isset($_POST["freeform"]) ? $_POST["freeform"] : ''; ?></textarea>
        </div>
            <p>Pozor! Nahrávej obrázek menší než 10MB!</p>
        <p>
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="hidden" name="csrftoken" value="<?php echo $token; ?>">
        </p>

        <p>
            <input class="submitform" name="submitform" type='submit' value="Odeslat dotazník">
        </p>
        </form>
        <p class="errorlocal"><?php echo $errorlocal ?></p>
        <p class="error"><?php echo @$Doc->error ?></p>
        <p class="success"><?php echo @$Doc->success ?></p>   <!--ohlaseni stavu-->
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
    <script>init();</script>
</body>
</html>