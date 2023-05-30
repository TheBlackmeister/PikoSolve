<?php
session_start();
require("bin/dbfiles/switchroles.class.php");

if(!isset($_SESSION['user'])){
    header('location: login.php'); exit();
} // POKUD NENI LOGGED

if(!isset($_SESSION['admin'])){
    header('location: index.php'); exit();
} // POKUD NENI LOGGED NEBO NASTALA CHYBA, NEMELO BY SE TO STAT, JE TO TU PRO JISTOTU

if($_SESSION['admin'] == 0){
    header('location: index.php'); exit();
} // POKUD NEJSEM ADMIN

$errorlocal = "";
if (isset($_POST['usedmethod'])) {
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                $change = new RoleSwitch($_POST['usedmethod'],$_POST['changeroles']); // VOLANI CLASSY PRI SPLNENI PODMINEK NA ZMENU ROLE ADMIN/OPRAVOVATEL
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
} // odkaz na zmeny admin opravovatel


if(isset($_POST['logout'])){ // LOGOUT
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





    // nacteme data z DB souboru
    // vypisme je cyklem do stranky
    // mazani polozek
    if (isset($_POST['deleteNote'])) {      // zde mazani polozek

        if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
            if (isset($_SESSION['csrftoken_time'])) {
                $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
                $token_time = $_SESSION['csrftoken_time'];
                if (($token_time + $max_time) >= time()) {

                    $deleteid = $_POST['deleteNote'];
                    $data = file_get_contents('bin/dbfiles/users.json');
                    $data = json_decode($data, JSON_OBJECT_AS_ARRAY);
                    foreach ($data as $key=>$record) {
                        if ($record['id'] == $deleteid) {
                            unset($data[$key]);
                        }
                    }
                    $data = array_values($data);
                    file_put_contents('bin/dbfiles/users.json', json_encode($data, JSON_PRETTY_PRINT));
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
    } // delete a specific user


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
    <link href="css/styles_myprofile.css" rel="stylesheet" id="theme-link">
    <script src="js/darkmode_myprofile.js"></script>
    <title>Databáze PikoSolve</title>
</head>
<!--zakladni barvy:
    color: white;
    background: #9fd3c7;
    text vetsinou: #f2f2f2;
    background-color:#385170;
    background-color: #142d4c;
-->

<body onLoad ="checkLS()"> <!--darkmode-->
<ul class ="navbar">
    <li><a class="logo" href="index.php"><img class="logocolor" src="img/bile_logo.svg" alt="logo" width="35" height="19">PikoSolve</a></li>
    <li><a href="onas.php">O nás</a></li>
    <?php
    if(isset($_SESSION['user'])){
        echo '<li><a href="newdoc.php">Nový příklad</a></li>';
    }
    ?>
    <li class="darkmodebtn"><a><img src="img/moon.png" alt="moon"></a></li>
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
            <a class="darkmodebtn"><img src="img/moon.png" alt="moon"> Dark Mode</a>
        </div>
    </div>
</div>

<main>




    <div class="grid-container">
        <div>
            <header> <h1> Registrovaní uživatelé </h1></header>
        </div>
        <div class="problemlist" id="users">
            <p class="error"><?php echo @$change->error ?></p>
            <p class="success"><?php echo @$change->success ?></p>   <!--ohlaseni stavu-->
            <p class="error"><?php echo $errorlocal ?></p>
            <div>
                <?php
                $users = file_get_contents('bin/dbfiles/users.json');
                $users = json_decode($users, JSON_OBJECT_AS_ARRAY);
                $users = array_reverse($users);
                foreach($users as $user) {
                    $username = htmlspecialchars($user['username']);
                    $id = htmlspecialchars($user['id']);
                    $password = htmlspecialchars($user['password']);
                    $name = htmlspecialchars($user['name']);
                    $surname = htmlspecialchars($user['surname']);
                    $sex = htmlspecialchars($user['sex']);
                    $ZS = htmlspecialchars($user['ZS']);
                    $school = htmlspecialchars($user['school']);
                    $pikomat = htmlspecialchars($user['pikomatsolver']);
                    $datum = htmlspecialchars($user['datum']);
                    $admin = htmlspecialchars($user['admin']);
                    $opravovatel = htmlspecialchars($user['opravovatel']);
                    $email = htmlspecialchars($user['email']);
                    echo '<div class="">';
                    echo '<fieldset class = "listusers">';
                    echo '<div>';
                    echo '<h2>'.$username.'</h2>';
                    echo '<p> Jméno: '.$name. ' ' .$surname.'</p>';
                    echo '<p> Pohlaví: '.$sex.'</p>';
                    echo '<p> Studuje ZŠ: '.$ZS.', '.$school.'</p>';
                    echo '<p>Je řešitelem Pikomatu? '.$pikomat.'</p>';
                    echo '<p>Emailová adresa: '.$email.'</p>';
                    echo '<p>Datum přidání: '.$datum.'</p>';
                    echo "<form method=\"post\"><button name=\"deleteNote\" value=\"$id\">Smazat záznam</button>";
                    echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                    echo '</form>';
                    echo '<p>Opravovatel: '.$opravovatel.'</p>';
                    echo '<p>Admin: '.$admin.'</p>';
                    if($admin == 1){
                        echo "<form method=\"post\"><button name=\"changeroles\" value=\"$id\">Odstranit admin práva</button><input type='hidden' name='usedmethod' value='deleteAdmin'>";
                        echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                        echo '</form>';
                    }
                    if($admin == 0){
                        echo "<form method=\"post\"><button name=\"changeroles\" value=\"$id\">Přidat admin práva</button><input type='hidden' name='usedmethod' value='addAdmin'>";
                        echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                        echo '</form>';
                    }
                    if($opravovatel == 1){
                        echo "<form method=\"post\"><button name=\"changeroles\" value=\"$id\">Odstranit práva opravovatele</button><input type='hidden' name='usedmethod' value='deleteOrg'>";
                        echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                        echo '</form>';
                    }
                    if($opravovatel == 0){
                        echo "<form method=\"post\"><button name=\"changeroles\" value=\"$id\">Přidat práva opravovatele</button><input type='hidden' name='usedmethod' value='addOrg'>";
                        echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                        echo '</form>';
                    }
                    echo '</div>';
                    echo '</fieldset>';
                    echo '</div>';
                }
                $users = array_reverse($users);   // VYPIS UZIVATELU
                ?>
            </div>

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