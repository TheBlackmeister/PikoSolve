<?php
session_start();

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
} // toto je ve vsech krom login a register, je to na odhlaseni

$records = file_get_contents('bin/dataFiles/data.json');
$records = json_decode($records, JSON_OBJECT_AS_ARRAY);

$count = 0;
$pages = 1; // promenne pro pagination
$records = array_reverse($records);
foreach($records as $record) {
    $count = $count + 1;
    if ($count % 10 == 0){
        $pages = $pages + 1;
    }
} // zde je vypocet pro pagination
$records = array_reverse($records);

if (empty($_GET)) {
    header("location: index.php?pagenumber=1"); exit();
}  // osetruje prazdny get

if (!isset($_GET['pagenumber'])) {
    header("location: index.php?pagenumber=1"); exit();
} // osetruje jiny get pole

if ($_GET['pagenumber'] > $pages || $_GET['pagenumber'] < 1) {
    header("location: index.php?pagenumber=1"); exit();
} // osetruje jiny get pole

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
    <script src="js/darkmode_index_smol.js"></script>
    <title>PikoSolve</title>
</head>
<!--zakladni barvy:
    color: white;
    background: #9fd3c7;
    text vetsinou: #f2f2f2;
    background-color:#385170;
    background-color: #142d4c;

    green light color #38aa70;
    green dark color #0d703d;

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
</ul> <!-- zde je navbar normalni-->

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

        </div>
    </div>
</div> <!-- zde je navbar pro telefon-->

<main>




        <div class="grid-container">
            <div>
                <header> <h1> Matematické fórum PikoSolve </h1></header>
            </div>
            <div>
                <div class="problemlist">

                    <?php // vycet
                    $records = array_reverse($records);
                    foreach($records as $key=>$record) {
                        if($_GET['pagenumber'] == 1){
                            if($key < 10) { // pro prvni stranku
                                $username = htmlspecialchars($record['username']);
                                $id = htmlspecialchars($record['id']);
                                $nadpis = htmlspecialchars($record['nadpis']);
                                $obsah = htmlspecialchars($record['obsah']);
                                $datum = htmlspecialchars($record['datum']);
                                $closed = $record['closed'];


                                echo '<div ';
                                if ($closed == 1) {
                                    echo 'class="closed"';
                                } //zde je podminka jestli je zavreny prispevek
                                echo ">";

                                echo '<div class="problemheader">';
                                echo '<div>';
                                echo $username;
                                echo '</div>';
                                echo '<div>';
                                echo $nadpis;
                                echo '</div>';
                                echo '<div>';
                                echo $datum;
                                echo '</div>';

                                echo '</div>';
                                echo "<form action=\"detail.php\" method=\"get\"><button class='detailbutton' name=\"opendetail\" value=\"$id\">";
                                echo 'Detail';
                                echo "</button></form>";
                                echo '<div>';

                                echo $obsah;
                                echo '</div>';

                                echo '</div>';

                            }
                        }
                        else{
                            if($key >= 10 * ($_GET['pagenumber']-1) && $key < 10 + 10 * ($_GET['pagenumber']-1)){ //manualni pagination
                                $username = htmlspecialchars($record['username']); // pro ostatni stranky
                                $id = htmlspecialchars($record['id']);
                                $nadpis = htmlspecialchars($record['nadpis']);
                                $obsah = htmlspecialchars($record['obsah']);
                                $datum = htmlspecialchars($record['datum']);
                                $closed = $record['closed'];


                                echo '<div ';
                                if ($closed == 1) {
                                    echo 'class="closed"';
                                } //zde je podminka jestli je zavreny prispevek
                                echo ">";

                                echo '<div class="problemheader">';
                                echo '<div>';
                                echo $username;
                                echo '</div>';
                                echo '<div class="nadpis">';
                                echo $nadpis;
                                echo '</div>';
                                echo '<div>';
                                echo $datum;
                                echo '</div>';

                                echo '</div>';
                                echo "<form action=\"detail.php\" method=\"get\"><button class='detailbutton' name=\"opendetail\" value=\"$id\">";
                                echo 'Detail';
                                echo "</button></form>";
                                echo '<div>';

                                echo $obsah;
                                echo '</div>';

                                echo '</div>';
                            }
                        }
                    }
//                    echo '</form>';
                    $records = array_reverse($records);
                    ?>    <!--zde se vypisuji prispevky-->
                </div>
            </div>

            <?php
            // TOHLE CELE JE ŘEŠENÍ STRÁNKOVÁNÍ
            echo '<div id="numberrow">';
            echo '<div id="numbering">';
            echo "<form class='query' method=\"get\"><button class='pagenumber' name=\"pagenumber\" value=\"1\">První stránka</button></form>";
            if ($_GET['pagenumber'] == 1 || $_GET['pagenumber'] == 2){ // PODMINKY KDY SE MA CO ZOBRAZIT, ABY TAM NEKTERE NEBYLY DVAKRAT. PROTOZE KDYZ JSEM TREBA NA STRANCE 4, STALE VIDIM CISLO STRANKY 1 A PODOBNE
            }
            else {
                echo "<form class='displayquery' method=\"get\"><button class='pagenumber' name=\"pagenumber\" value=\"1\">1</button></form>";
            }
            for ($x = ($_GET['pagenumber']-1); $x <= ($_GET['pagenumber']+3); $x++){
                if($x==0){continue;}
                if($x>$pages){continue;}
                if(($x == $_GET['pagenumber']) && ($x > 0) && ($x<=$pages)) {
                    echo "<form method=\"get\"><button class='currentpagenumber' name=\"pagenumber\" value=\"$x\">" . $x . "</button></form>"; // pro zvyrazneni aktualni stranky
                    echo "&nbsp;";
                }
                else {
                    echo "<form method=\"get\"><button class='pagenumber' name=\"pagenumber\" value=\"$x\">" . $x . "</button></form>";
                    echo "&nbsp;";
                }
            }
            echo "<form class='query' method=\"get\"><button class='pagenumber' name=\"pagenumber\" value=\"$pages\">Poslední stránka</button></form>";
            if ($_GET['pagenumber'] == $pages || $_GET['pagenumber'] == ($pages-1) || $_GET['pagenumber'] == ($pages-2) || $_GET['pagenumber'] == ($pages-3)){
            }
            else {
                echo "<form class='displayquery' method=\"get\"><button class='pagenumber' name=\"pagenumber\" value=\"$pages\">".$pages."</button></form>";
            }

            echo '</div>';
            echo '</div>';



            ?> <!-- zde je vypocet pro paginaton-->
            <button id="darkmodebtnsmol"><img src="img/moon.png" alt="moon" width="19" height="19"></button> <!-- darkmode tlacitko -->
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
<script>darkmodefuncsmol();</script>  <!--zde spusteni darkmode skriptu-->


</body>
</html>