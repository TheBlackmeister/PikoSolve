<?php

require("bin/dataFiles/newanswer.class.php");

if (empty($_GET)) {
    header("location: index.php"); exit();
}  // osetruje prazdny get
$errorlocal ='';
$detailid = $_GET['opendetail']; // tahle cast je o nacteni detailu o prispevku
$data = file_get_contents('bin/dataFiles/data.json');
$data = json_decode($data, JSON_OBJECT_AS_ARRAY);
$found = false;
foreach ($data as $key=>$record) {
    if ($record['id'] == $detailid) {
        $found = true;
        $username = htmlspecialchars($record['username']);
        $stringid = htmlspecialchars($record['id']);
        $nadpis = htmlspecialchars($record['nadpis']);
        $obsah = htmlspecialchars($record['obsah']);
        $datum = htmlspecialchars($record['datum']);
        $photo = htmlspecialchars($record['photo']);
        $photoid = htmlspecialchars($record['photoid']);
        $closed = htmlspecialchars($record['closed']);
    }

}
if ($found == false) {
    header("location: index.php"); exit();
}
$data = array_values($data);
?>
<?php
if (!empty($photo)) { // zde vyrobeni miniatury.
    if(!file_exists("bin/dataFiles/pic/thumb/" . $photoid)) {
// (A) READ THE ORIGINAL IMAGE
        if (exif_imagetype($photo)==3) { // 3 == png
            $original = imagecreatefrompng($photo);
        }
        elseif (exif_imagetype($photo)==2) { // 2 == jpeg
            $original = imagecreatefromjpeg($photo);
        }
        elseif (exif_imagetype($photo)==1) { // 1 == gif
            $original = imagecreatefromgif($photo);
        }
        elseif (exif_imagetype($photo)==6) { // 6 == bmp
            $original = imagecreatefrombmp($photo);
        }//otherwise convert to jpeg
        else {
            $original = imagecreatefromjpeg('img/A_black_image.jpg');
        }

// (B) EMPTY CANVAS WITH REQUIRED DIMENSIONS
// IMAGECREATETRUECOLOR(WIDTH, HEIGHT)
        $resized = imagecreatetruecolor(150, 150); // 150 je sirka miniatury

// (C) RESIZE THE IMAGE
// IMAGECOPYRESAMPLED(TARGET, SOURCE, TX, TY, SX, SY, TWIDTH, THEIGHT, SWIDTH, SHEIGHT)
        imagecopyresampled($resized, $original, 0, 0, 0, 0, 150, 150, imagesx($original), imagesy($original));

// (D) SAVE/OUTPUT RESIZED IMAGE
        imagejpeg($resized, 'bin/dataFiles/pic/thumb/' . $photoid);

// (E) CLEAN UP
        imagedestroy($original);
        imagedestroy($resized);
    }
}

//tady je presmerovani na odpoved.
if (isset($_POST['odpoved'])) {
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                $answer = new AddAnswer($detailid,$_POST['odpoved']);
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


}

// tady mazani odpovedi
if (isset($_POST['deleteNote'])) {      // zde mazani polozek
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                $deleteid = $_POST['deleteNote'];
                $data = file_get_contents('bin/dataFiles/answers.json');
                $data = json_decode($data, JSON_OBJECT_AS_ARRAY);


                foreach ($data as $key=>$record) {
                    if ($record['id'] == $deleteid) {
                        unset($data[$key]);
                    }
                }
                $data = array_values($data);
                file_put_contents('bin/dataFiles/answers.json', json_encode($data, JSON_PRETTY_PRINT));
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

}

// tady mazani prispevku
if (isset($_POST['deleteIssue'])) {      // zde mazani odpovedi mazaneho prispevku
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                $deleteid = $_POST['deleteIssue'];
                $data = file_get_contents('bin/dataFiles/answers.json');
                $data = json_decode($data, JSON_OBJECT_AS_ARRAY);


                foreach ($data as $key=>$record) {
                    if ($record['stringid'] == $deleteid) {

                        unset($data[$key]);
                    }
                }
                $data = array_values($data);
                file_put_contents('bin/dataFiles/answers.json', json_encode($data, JSON_PRETTY_PRINT));

                // ZDE SMAZANI SAMOTNEHO PRISPEVKU
                $data = file_get_contents('bin/dataFiles/data.json');
                $data = json_decode($data, JSON_OBJECT_AS_ARRAY);
                foreach ($data as $key=>$record) {
                    if ($record['id'] == $deleteid) {
                        if(!empty($record['photo'])){
                            $photopath = getcwd(). '/bin/dataFiles/pic/' .$record['photoid'];
                            unlink($photopath);
                            $photopath = getcwd(). '/bin/dataFiles/pic/thumb/' .$record['photoid'];
                            unlink($photopath);
                        }
                        unset($data[$key]);
                    }
                }
                $data = array_values($data);
                file_put_contents('bin/dataFiles/data.json', json_encode($data, JSON_PRETTY_PRINT));
                header("location: index.php"); exit();
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

}





//zde uzavreni issue
if (isset($_POST['closeIssue'])) {
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                $closeid = $_POST['closeIssue'];
                $data = file_get_contents('bin/dataFiles/data.json');
                $data = json_decode($data, JSON_OBJECT_AS_ARRAY);


                foreach ($data as $key=>$record) {
                    if ($record['id'] == $closeid) {
                        $data[$key]['closed'] = 1;
                    }
                }
                $data = array_values($data);
                file_put_contents('bin/dataFiles/data.json', json_encode($data, JSON_PRETTY_PRINT));
                header("location: index.php"); exit();
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
}


//zde otevreni issue
if (isset($_POST['openIssue'])) {
    if ((isset($_POST['csrftoken']) & ($_POST['csrftoken'] == $_SESSION['csrftoken']))) {
        if (isset($_SESSION['csrftoken_time'])) {
            $max_time = 60 * 60 * 2; // platnost tokenu jsou dve hodiny
            $token_time = $_SESSION['csrftoken_time'];
            if (($token_time + $max_time) >= time()) {
                $closeid = $_POST['openIssue'];
                $data = file_get_contents('bin/dataFiles/data.json');
                $data = json_decode($data, JSON_OBJECT_AS_ARRAY);
                foreach ($data as $key=>$record) {
                    if ($record['id'] == $closeid) {
                        $data[$key]['closed'] = 0;
                    }
                }
                $data = array_values($data);
                file_put_contents('bin/dataFiles/data.json', json_encode($data, JSON_PRETTY_PRINT));
                header("location: index.php"); exit();
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
  <title>Detail příspěvku PikoSolve</title>
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
</ul> <!-- v php udelane pdminky navbaru pro prihlasene uzivatele-->

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
      <header> <h1> <?php echo 'Příspěvek '; echo $nadpis; ?> </h1></header>
    </div>
    <div class="problemlist" id="detailprikladu">
        <div class="aligndiff">  <?php if(!empty($photo)) { echo "<a href='$photo'>";   }?>  <img src="<?php if(empty($photo)) {echo 'img/images.png';} else {echo "bin/dataFiles/pic/thumb/$photoid";} ?>" alt="popis problému" width="150" height="150"><?php if(!empty($photo)) { echo "</a>"; }?>
        <fieldset>
          <legend>Příklad:  </legend>
          <div class ="row">
            <label>
              Autor Příspěvku:
            </label>
            <?php echo $username ?>
          </div>

          <div class ="row">
            <label>
              Datum přidání:
            </label>
              <?php echo $datum ?>
          </div>
            <?php // tlacitka na prispevky
            if(isset($_SESSION['user'])) {
                if ($_SESSION['admin'] == 1) {
                    echo "<form method=\"post\"><button name=\"deleteIssue\" value=\"$stringid\">Smazat příspěvek</button>";
                    echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                    echo '</form>';
                }
            }
            ?>

            <?php
            if(isset($_SESSION['user'])) {
                if ($closed == 0 && $_SESSION['opravovatel'] == 1) {
                    echo "<form method=\"post\"><button name=\"closeIssue\" value=\"$stringid\">Uzavřít příspěvek</button>";
                    echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                    echo '</form>';

                }
            }
            ?>

            <?php
            if($closed==1) echo '<h2> Tento příspěvek je uzavřený.</h2>';
            ?>
            <?php
            if(isset($_SESSION['user'])) {
                if ($closed == 1 && $_SESSION['opravovatel'] == 1) {
                    echo "<form method=\"post\"><button name=\"openIssue\" value=\"$stringid\">Znovu otevřít příspěvek</button>";
                    echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                    echo '</form>';

                }
            }
            ?>
        </fieldset>
      </div>

      <div>
        <p id="popisprikladu"><?php echo $obsah ?></p>
      </div>
    </div>

      <!--TODO chtelo by to udelat v php if na to jestli je to closed nebo ne, pokud ano, zobraz mi 'toto je uzavreno', myslena odpoved-->
      <?php
        $records = file_get_contents('bin/dataFiles/answers.json');
        $records = json_decode($records, JSON_OBJECT_AS_ARRAY);
        $records = array_reverse($records);
        echo '<ul class="problemlist">';




          if(!empty($_SESSION['user'])) { // to je odpovedni bublina
              if($closed == 0) {


                  echo '<li class="nohover">';
                  echo '<div class="aligndiff">';
                  echo '<fieldset>';
                  echo '<legend>Napiš odpověď:  </legend>';
                  echo "<form method=\"post\"><textarea id='freeformodpoved' name=\"odpoved\" placeholder='Zde napiš odpověď' ></textarea>";
                  echo "<button value=\"" . $_GET['opendetail'] . "\">Odpovědět</button>";
                  echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                  echo '</form>';
                  echo '</fieldset>';
                  echo '</div>';
                  echo '<p class="error">';
                  echo @$answer->error; // ohlaseni stavu
                  echo '</p>';
                  echo '<p class="success">';
                  echo @$answer->success;
                  echo '</p>';
                  echo '</li>';
              }
          }



            // odpovedi
        foreach($records as $record) {

            $username = htmlspecialchars($record['username']);
            $obsah = htmlspecialchars($record['obsah']);
            $datum = htmlspecialchars($record['datum']);
            $id = $record['id'];
            $stringid = htmlspecialchars($record['stringid']);
            $isGOD = htmlspecialchars($record['isGOD']);
            if ($stringid == $_GET['opendetail']) {

                echo "<li";   if($isGOD == 1) {echo ' class="odpovediGOD">';} else {echo ' class="odpovedi">';}
                echo '<fieldset>';
                echo '<div class="aligndiff"><img src="img/profile_generic.jpg" alt="profilová fotka" width="150" height="150">';
                echo '<fieldset>';
                echo '<legend>Odpověď</legend>';
                echo '<div class ="row">';
                echo '<label>';
                echo 'Autor příspěvku:';
                echo '</label>';
                echo $username;
                echo '</div>';
                echo '<div class ="row">';
                echo '<label>';
                echo 'Datum přidání:';
                echo '</label>';
                echo $datum;
                echo '</div>';
                if($isGOD == 1) {
                    echo '<p> Uživatel je opravovatel </p>';
                }

                // zde tlacitko pro adminy a opravovatele
                if(isset($_SESSION['user'])) {
                    if ($closed == 0 && $_SESSION['opravovatel'] == 1) {
                        echo "<form method=\"post\"><button name=\"deleteNote\" value=\"$id\">Smazat odpověď</button>";
                        echo "<input type=\"hidden\" name=\"csrftoken\" value=\"$token\">";
                        echo '</form>';
                    }
                }
                echo '</fieldset>';
                echo '</div>';
                echo '<div class ="row">';
                echo '<label>';
                echo 'Odpověď:';
                echo '</label>';
                echo $obsah;
                echo '</div>';
                echo '</fieldset>';
                echo '</li>';

            }
            $records = array_reverse($records);
        }
            echo '</ul>';
            ?>
  </div>  <!--grid container div -->

    <aside>
        <ul>
            <li class="nohover"> <h2><a href="index.php">Poslední příspěvky:</a></h2></li>
            <?php // bocni panel
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