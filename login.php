<?php require("bin/dbfiles/login.class.php") ?>
<?php


if(isset($_SESSION['user'])){
    header('location: index.php'); exit();
} // KDYZ JE BOREC PRIHLASENEJ, TAK HO HODIT PRYC
session_start();
$errorlocal = "";
    if(isset($_POST['submitlogin'])) {
        if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
            if (isset($_SESSION['csrftoken_time'])) {
                $max_time = 60*60*2; // platnost tokenu jsou dve hodiny
                $token_time = $_SESSION['csrftoken_time'];
                if (($token_time + $max_time) >= time()) {
                    if ((!isset($_POST['username'])) || (!isset($_POST['password']))){
                        $errorlocal = "Musíš vyplnit všechny kolonky!";
                    }
                    else {
                        $user = new LoginUser($_POST['username'], $_POST['password']);
                    }
                } else {
                    unset($_SESSION['csrftoken']);
                    unset($_SESSION['csrftoken_time']);
                    $errorlocal = "Platnost CSRF tokenu vypršela. Načti znovu stránku a pošli příspěvek.";
                }
            }
        } // csrf validace
        else {
            $errorlocal = "Nesprávný CSRF token. Načti znovu stránku a pošli příspěvek.";
        }
    }


// vytvoreni csrf tokenu
$token = uniqid(rand(),true);
$_SESSION['csrftoken'] = $token;
$_SESSION['csrftoken_time'] = time();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title> Přihlášení do PikoSolve</title> <!--co se objevi v tabu-->

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/styles_login.css" rel="stylesheet" id="theme-link">
  <script src="js/darkmode_login.js"></script>
  <script src="js/validace_login.js"></script>


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
        Přihlášení do PikoSolve
      </h1>
    </header>


      <form id="login" action="login.php" method="post" > <!--enctype="multipart/form-data"-->

        <fieldset>
          <legend>Uživatelské údaje:  </legend>

          <div class ="row">
            <label class="require" for="username">
              Uživatelské jméno:
              <input autofocus type="text" required placeholder="Uživatelské jméno" name="username" id="username">
            </label>
          </div>

          <div class ="row">
            <label class="require" for="password">
              Heslo:
              <input type="password" required placeholder="Vaše heslo" name="password" id="password">
            </label>
          </div>


        </fieldset>
          <input type="hidden" name="csrftoken" value="<?php echo $token; ?>">   <!--csrf-->
        <p>
          <input class="submitform" type="submit" value="Přihlásit se" name="submitlogin">
        </p>

        <p>
          Nejsi registrovaný? &nbsp; <a href="form.php">Registruj se zde!</a>
        </p>

        <p class="error"><?php echo @$user->error ?></p>
        <p class="success"><?php echo @$user->success ?></p>   <!--ohlaseni stavu-->
        <p class="error"><?php echo $errorlocal ?></p>

      </form>
  </div>
</div>
<script>darkmodefunc();</script>
<script>init();</script>
</body>
<!--    <footer>-->
<!--        <li>©Martin Černý, 2022, SIT FEL ČVUT</li>-->
<!--    </footer>-->

</html>