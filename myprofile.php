<?php
session_start();

if(!isset($_SESSION['user'])){
    header('location: login.php'); exit();
} // kam se neprihlaseny uzivatel nema dostat

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



$users = file_get_contents('bin/dbfiles/users.json');
$users = json_decode($users, JSON_OBJECT_AS_ARRAY);
$users = array_reverse($users);
foreach ($users as $index=>$user) {
    if ($_SESSION['user'] == $user['username']) {
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="css/styles_myprofile.css" rel="stylesheet" id="theme-link">
  <script src="js/darkmode_myprofile.js"></script>
  <title>Můj profil PikoSolve</title>
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
        </div>
    </div>
</div>

<main>




  <div class="grid-container">
    <div>
      <header> <h1> Můj Profil </h1></header>
    </div>
    <div class="problemlist" id="detailprikladu">
      <div class="aligndiff"><img src="img/profile_generic.jpg" alt="profilová fotka">
        <fieldset>
          <legend>Uživatelské údaje:  </legend>

          <div class ="row">
            <label>
              Uživatelské jméno:
            </label>
            <?php
            echo $username;
            ?>
          </div>
            <p><?php
                echo "<form method=\"post\"><button class='logoutmyprofile' name=\"logout\" value=\"logout\">Odhlásit se</button>";
                echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                echo '</form>';
                ?></p>
            <?php if ($_SESSION['admin']==1){
            echo "<p>Jsi admin!</p>";
            }
            if ($_SESSION['opravovatel']==1){
            echo "<p>Jsi opravovatel!</p>";
            } // tohle ukazuje jestli jsem admin či opravovatel
            ?>
        </fieldset>
      </div>


      <div>

        <fieldset>

          <legend>Osobní údaje: </legend>

          <div class ="row">
            <label>
              Jméno:
            </label>
              <?php echo $name; ?>
          </div>


          <div class ="row">
            <label>
              Příjmení:
            </label>
              <?php echo $surname; ?>
          </div>


          <div class="row">
            <label> Pohlaví:</label>
              <?php echo $sex; ?>
          </div>

        </fieldset>

        <fieldset>
          <legend>Škola:  </legend>

          <div class ="row">
            <label> Žák Základní školy: </label>
              <?php echo $ZS; ?>
          </div>


          <div class ="row">
            <label> Název základní školy: </label>
              <?php echo $school; ?>
          </div>


          <div class="row">
            <label> Řešitel Pikomatu: </label>
              <?php echo $pikomat; ?>
          </div>


        <div class="row">
            <label> Datum registrace: </label>
            <?php echo $datum; ?>
        </div>


        </fieldset>

      </div>
      <?php
      if($_SESSION['admin'] == 1) {
          echo '<a class="submitform" href="listNotes_0.php">Vstup do databáze uživatelů</a>';

      }
      ?>


    </div>

    <!--TODO SEZNAM MYCH PRISPEVKU-->

      <ul class="aside">
          <li> <h2><a href="index.php">Moje příspěvky:</a></h2></li>
          <?php
          $records = file_get_contents('bin/dataFiles/data.json');
          $records = json_decode($records, JSON_OBJECT_AS_ARRAY);
          $records = array_reverse($records);

          foreach($records as $record) {
              $username = htmlspecialchars($record['username']);
              if ($username == $_SESSION['user']) {

                  $nadpis = htmlspecialchars($record['nadpis']);
                  $datum = htmlspecialchars($record['datum']);
                  $id = htmlspecialchars($record['id']);
                  $closed = htmlspecialchars($record['closed']);

                  echo "<li ";
                  if ($closed == 1) {
                      echo 'class="closed" ';
                  }
                  echo '<p> Dne ' . $datum . ' jsi přidal příspěvek </p>';
                  echo '<p>' . $nadpis . '</p>';
                  echo "<form action=\"detail.php\" method=\"get\"><button class='detailbutton' name=\"opendetail\" value=\"$id\">";
                  echo 'Detail';
                  echo "</button></form>";
                  echo '</li>';
              }
          }

          $records = array_reverse($records);
          ?>

      </ul>

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