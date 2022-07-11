<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Demandes cloturées</title>
</head>


<?php include "header.php" ?>

<?php
include('databaseconnect.php');

//On récupère les demandes de la deuxième bd et on les ordonne du plus récent au moins récent.
$req = $cnx2->query('SELECT * FROM demandes  ORDER BY id_demande DESC');
?>

<body>

<a href="index.php"><span class="fi-rr-arrow-left" style="font-size:45px; margin-left: 5vw;margin-top:1vh"></a>

    <div class="container-fluid mt-100" style="margin-top:25px; margin-bottom:35px;">

        <h1>Demandes cloturées</h1><br>

        <div style="margin-bottom:2px;">
                <input type="text" id="findField" placeholder="mots-clées" size="10" style="margin-bottom: 15px;outline:none;border:1px solid blue;margin-top: 10px;margin-left: 10px;"> 
                <input type="submit" id="rec" value="rechercher" onclick="chercher();" style="color:gray;outline:none;border:1px solid gray">
        </div>


        <?php
        if (!empty($_SESSION)) {

        ?>


            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header pr-0 pl-0">
                            <div class="row no-gutters align-items-center w-100">
                                <div class="col font-weight-bold pl-3">Mes demandes</div>
                                <div class="d-none d-md-block col-6 text-muted">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-3">Echeance</div>
                                        <div class="col-3">Priorite</div>
                                        <div class="col-6">Etat</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        //On récupère les information de notre selection de la var $req 
                        //et on les stock dans la var $info et on les fait apparaitre via une boucle
                        while ($info = $req->fetch()) {
                            $annee = substr($info['date_demande'], 0, 4);
                            $min_titre = $info['titre'];
                        ?>
                            <div class="card-body py-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">


                                        <h5 style="color:red;"> <u style="text-decoration:underline;color:red"> Demande N</u><i style="color:red;">°</i>
                                            : DA - <?= $annee ?> - <?= $info['id_demande']; ?></h5>
                                        <h2 style="color:lightseagreen;"><?= $min_titre ?><h2>
                                                <span style="height:100px;font-size:15px">
                                                    <p><?= $info['description'] ?>
                                                    <p>
                                                </span>
                                                <!-- affichage des differentes informations liees aux demandes cloturees -->
                                                <h6 style="color:navy;"> <i><u>Instruction</u></i> : <?= $info['instruction']; ?> </h6>
                                                <h6 style="color:navy;"> <i><u>Commentaire 1</u></i> : <?= $info['commentaire']; ?> </h6>
                                                <h6 style="color:navy;"> <i><u>Commentaire 2</u></i> : <?= $info['commentaire_1']; ?> </h6>
                                                <h6 style="color:navy;"> <i><u>Commentaire 3</u></i> : <?= $info['commentaire_2']; ?> </h6>
                                                <h6 style="color:green"> <i><u>Nom</u></i> : <?= $info['nom_user']; ?> </h6>
                                                <h6 style="color:gray;"><i><u>Catégorie</u></i> : <?= $info['categorie']; ?> </h6>
                                                <h6> <i><u>Date demande</u></i> : <?= $info['date_demande']; ?></h6>
                                    </div>


                                    <div class="d-none d-md-block col-6">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-3" style=" margin-left: -10px;">
                                                <p><?= $info['date_echeance']; ?></p>
                                            </div>
                                            <div class="col-3">
                                                <p><?= $info['priorite']; ?></p>
                                            </div>
                                            <div class="col-3">
                                                <p>
                                                    <?php if (empty($info['etat_demande'])) {
                                                        echo "Non traitée";
                                                    } else {
                                                        echo $info['etat_demande'];
                                                    }  ?>
                                                </p>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="m-0">
                        <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
    </div>



<?php
        } else {
?>
    <div class="alert alert-danger text-center"><a href="login.php"> Connectez vous </a></div>

<?php
        }
?>
</div>

<?php include "footer.php" ?>

</body>

</html>