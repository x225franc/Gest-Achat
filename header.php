<?php
include('databaseconnect.php');
@session_start();

// compteur de demandes
$req6 = $cnx->query('SELECT acheteur FROM `demandes` WHERE acheteur = ' . $_SESSION['id_user'] . '');

$ver_dem = $req6->rowCount();

$req7 = $cnx->query('SELECT acheteur FROM `demandes` WHERE acheteur = "" ');

$ver_dem3 = $req7->rowCount();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Flaticon/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <title><?= $_SESSION['nom'] ?></title>


    <!--Javascript : bouton rechercher surlignant le texte entree via mots clées-->
    <script type="text/javascript">
        function chercher () {
            var str = document.getElementById ("findField").value;
            if (str == "") {
                alert ("Veuillez Entrée du texte");
                return;
            }
 
            var supported = false;
            var found = false;
            if (window.find) {        // Firefox, Google Chrome, Safari
                supported = true;

                found = window.find (str);
            }
            else {
                if (document.selection && document.selection.createRange) { 
                    var textRange = document.selection.createRange ();
                    if (textRange.findText) {   
                        supported = true;
                            
                        if (textRange.text.length > 0) {
                            textRange.collapse (true);
                            textRange.move ("character", 1);
                        }
 
                        found = textRange.findText (str);
                        if (found) {
                            textRange.select ();
                        }
                    }
                }
            }
 
            if (supported) {
                if (!found) {
                    alert ("Aucun resultat pour:\n" + str);
                }
            }
            else {
                alert ("Navigateur incompatible!");
            }
        }

    </script>

<style>
    #rec :hover {
        color: gold;;
    }
</style>

</head>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand print">
                <img src="ressource/logo.png" height="70" alt="Logo">
                <b> ‎ GEST-ACHAT </b>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse" id="n">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav">
                </div>
                <div class="navbar-nav ms-auto">
                    <?php //Affichage des differents menu selon l'utilisateur connectée
                    if ($_SESSION['admin'] == 2) {
                        echo ' <a href="filtre.php" style="padding-left:6.5vw; display:flex; align-items:center" class="nav-item nav-link"> <span class="fi-rr-interrogation" style="font-size:35px;margin-top:7px;"></span>‎ ' . $ver_dem3 . '‎ Demandes non assignées</a>';
                        echo ' <a href="cloturer.php" style="padding-left:6.5vw; display:flex; align-items:center" class="nav-item nav-link"> <span class="fi-rr-cross-small" style="font-size:35px;margin-top:7px;"></span>‎Demandes cloturées</a>';
                        echo ' <a href="mesdemandes.php?i=' . $_SESSION['id_user'] . '" class="nav-item nav-link" style="padding-left:6.5vw; display:flex; align-items:center"><i class="  fi-rr-list" style="font-size:25px; margin-right:5px;margin-top:5px;"></i>' . $ver_dem . '‎ Mes Demandes non traitées </a>';
                    }
                    if ($_SESSION['admin'] == 1) {
                        echo ' <a href="cloturer2.php" style="padding-left:6.5vw; display:flex; align-items:center" class="nav-item nav-link"> <span class="fi-rr-cross-small" style="font-size:35px;margin-top:7px;"></span>‎ Mes demandes cloturées</a>';
                    }
                    if (empty($_SESSION)) {
                        echo '<a href="login.php" class="nav-item nav-link"> CONNEXION </a>';
                    } else {
                        echo ' <a href="user.php" class="nav-item nav-link" style="padding-left:6.5vw; display:flex; align-items:center"><i class="fi-rr-user" style="font-size:25px; margin-right:5px"></i> ' . $_SESSION['nom'] . '</a>';
                        echo '<a href="logout.php" class="nav-item nav-link" style="padding-left:6.5vw; display:flex; align-items:center"><i class="fi-rr-sign-out" style="font-size:25px; margin-right:5px"></i></a>';
                    }

                    ?>
                </div>
            </div>
        </div>
    </nav>
</header>

</html>

