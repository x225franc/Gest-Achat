<?php
include("databaseconnect.php");
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">

<?php
include("header.php");

//srtoupper permet de mettre en maj la chaine de caractère,
//substr permet d'extraire un caractère a partir d'un endroit de la chaine de caractères.
//sa syntaxe substr(string,start,length) 

$avatar = strtoupper(substr(@$_SESSION['nom'], 0, 2));
if (!empty($_SESSION)) {
?>
    <a href="index.php"><span class="fi-rr-arrow-left" style="font-size:45px; margin-left: 5vw;margin-top:1vh"></a>

    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-7">
                <div class="card p-3 py-4">
                    <div class="text-center avatar"> <?= $avatar ?> </div>
                    <div class="text-center mt-3">
                        <?php
                        if ($_SESSION['admin'] == 2) {
                            echo '<span class="bg-primary p-1 px-4 rounded text-white">Responsable Achat</span>';
                        } elseif ($_SESSION['admin'] == 1) {
                            echo '<span class="bg-success p-1 px-4 rounded text-white">Acheteur</span>';
                        } else {
                            echo '<span class="bg-secondary p-1 px-4 rounded text-white">Demandeur</span>';
                        }
                        ?>
                        <h5 class="mt-2 mb-0"><?= $_SESSION['nom'];  ?></h5> <span><?= $_SESSION['email']; ?>
                            <br>Direction : <?= $_SESSION['secteur']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
}

?>