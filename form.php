<?php
session_start();
//
//if(isset($_GET['logout'])){
//    unset($_SESSION['user']);
//    header("location: login.php"); exit();
//}


require 'bin/dbfiles/register.class.php';

$errorlocal = "";
if (isset($_POST['addNote'])) {
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                $user = new RegisterUser($_POST['username'], $_POST['password'], $_POST['name'], $_POST['surname'], $_POST['sex'], $_POST['ZS'], $_POST['school'], $_POST['pikomatsolver'], $_POST['email']);
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
} // poslani formulare


// vytvoreni csrf tokenu
$token = uniqid(rand(),true);
$_SESSION['csrftoken'] = $token;
$_SESSION['csrftoken_time'] = time();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
<!--        <style>-->
<!--            @import url("css/styles_form.css");-->
<!--        </style>-->


        <title> Registrační formulář PikoSolve</title> <!--co se objevi v tabu-->

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/styles_form.css" rel="stylesheet" id="theme-link">
        <script src="js/validace_form.js"></script>
        <script src="js/darkmode_form.js"></script>
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

    <div class="overall">
    <div class="flexform">
        <header>
            <h1>
                Registrační dotazník PikoSolve
            </h1>
        </header>


    <form id="form1" action="form.php" method="post" > <!--enctype="multipart/form-data"-->


        <fieldset>  <!--tady formular-->

            <legend>Osobní údaje: </legend>

            <div class ="row">
            <label class="require" for="name">
                Jméno:
                <input autofocus type="text" required placeholder="př. Jan" name="name" id="name" value="<?php echo isset($_POST["name"]) ? $_POST["name"] : ''; ?>">
            </label>
            </div>


            <div class ="row">
            <label class="require" for="surname">
                Příjmení:
                <input type="text" required placeholder="př. Novák" name="surname" id="surname" value="<?php echo isset($_POST["surname"]) ? $_POST["surname"] : ''; ?>">
            </label>
            </div>


            <div class="row">
            <label class="require"> Pohlaví:</label>
                <input required class="sex" value="Nezadáno" type="radio" name="sex" id="sex1" <?php if (isset($_POST["sex"]) && ($_POST["sex"]=="Nezadáno")) {echo "checked";}?>> <label for="sex1">nechci uvádět</label>
                <input required class="sex" value="Muž" type="radio" name="sex" id="sex2" <?php if (isset($_POST["sex"]) && ($_POST["sex"]=="Muž")) {echo "checked";}?>> <label for="sex2">muž</label>
                <input required class="sex" value="Žena" type="radio" name="sex" id="sex3" <?php if (isset($_POST["sex"]) && ($_POST["sex"]=="Žena")) {echo "checked";}?>> <label for="sex3">žena</label>
            </div>

        </fieldset>



        <fieldset>
            <legend>Škola:  </legend>

            <div class ="row">
            <label class="require" for="ZS1"> Jsi na ZŠ? </label>
                <input value="ano" type="radio" name="ZS" id="ZS1" <?php if (isset($_POST["ZS"]) && ($_POST["ZS"]=="ano")) {echo "checked";}?>> <label for="ZS1">Ano</label>
                <input value="ne" type="radio" name="ZS" id="ZS2" <?php if (isset($_POST["ZS"]) && ($_POST["ZS"]=="ne")) {echo "checked";}?>> <label for="ZS2">Ne</label>
            </div>


            <div class ="row">
            <label for="school"> Pokud ano, jakou ZŠ studuješ? </label>
                <input type="text" placeholder="př. ZŠ Karla Čapka, Praha 10" name="school" id="school" value="<?php echo isset($_POST["school"]) ? $_POST["school"] : ''; ?>">
            </div>


            <div class="row">
            <label class="require" for="pikomatsolver1">Řešíš Pikomat? </label>
                <input  value="ano" type="radio" name="pikomatsolver" id="pikomatsolver1" <?php if (isset($_POST["pikomatsolver"]) && ($_POST["pikomatsolver"]=="ano")) {echo "checked";}?>> <label for="pikomatsolver1">Ano</label>
                <input  value="ne" type="radio" name="pikomatsolver" id="pikomatsolver2" <?php if (isset($_POST["pikomatsolver"]) && ($_POST["pikomatsolver"]=="ne")) {echo "checked";}?>> <label for="pikomatsolver2">Ne</label>
            </div>

        </fieldset>



        <fieldset>
            <legend>Uživatelské údaje:  </legend>

            <div class ="row">
            <label class="require" for="username">
                Uživatelské jméno:
                <input type="text" required placeholder="př. beruska007" pattern=".{5,}" title="Zadejte uživatelské jméno, které má alespoň 5 znaků" name="username" id="username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ''; ?>">
            </label>
            </div>

            <div class ="row">
            <label class="require" for="password">
                Heslo:
                <input type="password" required placeholder="př. Jméno vašeho psa" name="password" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Zadejte heslo, které má 8 nebo více znaků, jeden velké a jedno malé písmeno.">
            </label>
            </div>

            <div class ="row">
                <label class="require" for="passrepeat">
                    Zopakujte heslo:
                    <input type="password" required placeholder="př. Jméno vašeho psa" name="passrepeat" id="passrepeat">
                </label>
            </div>

            <div class ="row">
                <label class="require" for="email">
                    Zadejte emailovou adresu:
                    <input type="email" required placeholder="...XY@email.cz..." name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="Zadejte správný formát emailu!" <?php echo isset($_POST["email"]) ? $_POST["email"] : ''; ?>>
                </label>
            </div>


        </fieldset>
        <input type="hidden" name="csrftoken" value="<?php echo $token; ?>">   <!--csrf-->
            <p>
                <input class="submitform" id="formregister" type='submit' value="Odeslat dotazník" name="addNote">
            </p>

            <p>
                Již jsi registrovaný? &nbsp; <a href="login.php">Přihlas se zde!</a>
            </p>

        <p class="error"><?php echo @$user->error ?></p>
        <p class="success"><?php echo @$user->success ?></p>   <!--ohlaseni stavu-->
        <p class="error"><?php echo $errorlocal ?></p>

    </form>
        <script>init();</script>

        <script>darkmodefunc();</script>


    </div>
    </div>

    </body>

</html>