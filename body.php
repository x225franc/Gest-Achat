<?php
include('databaseconnect.php');

//On récupère les 2 premiers letttre du nom de l'user dans la var $avatar
$avatar = strtoupper(substr(@$_SESSION['nom'], 0, 2));

//On effectue une requete a la bdd pour recuperer toutes les demandes de l'utilisateur connectée via sa variable de session.
$req = $cnx->query('SELECT * FROM categorie ,user, priorite ,demandes ,secteur
                        WHERE user.id_user = "' . @$_SESSION['id_user'] . '"
                        AND categorie.id_categorie = demandes.id_categorie
                        AND priorite.id_priorite = demandes.id_priorite 
                        AND user.id_user = demandes.id_user
                        AND secteur.id_secteur = user.id_secteur
                        ORDER BY id_demande DESC');
?>

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
    <title>Demandes</title>
</head>

<body>
    <div class="container-fluid mt-100" style="margin-top:25px; margin-bottom:35px;">

        <h1>Vos demandes </h1><br>

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
                        while ($info = $req->fetch()) {
                            //On récupere les 4 premiers caractère de la var $info['date_demande']
                            $annee = substr($info['date_demande'], 0, 4);
                           
                            //On récupère les 50 premier caractères du titre
                            $min_titre = substr($info['titre'], 0, 50);
                        ?>
                            <div class="card-body py-3">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <a href="overview.php?titre=<?= $info['titre'] ?>&description=<?= $info['description'] ?>&priorite=<?= $info["id_priorite"] ?>&categorie=<?= $info["id_categorie"] ?>&demandeur=<?= $info["nom_user"] ?>&secteur=<?= $info["nom_secteur"] ?>&Beneficiaire=<?= $info["Beneficiaire"] ?>&date_echeance=<?= $info["date_echeance"] ?>&document=<?= $info['id_demande'] ?>&nom_document=<?= $info['document'] ?>" class="text-big font-weight-semibold" data-abc="true">
                                            <h4 style="color:black;">DA - <?= $annee ?> - <?= $info['id_demande']; ?> : <?= $min_titre ?><h4>
                                                    <h5 style="color:blue"><?= $info['nom_categorie']; ?></h5>
                                                    <h6 style="color:black;"><?= $info['date_demande']; ?></h6>
                                        </a>
                                    </div>




                                    <div class="d-none d-md-block col-6">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-3" style=" margin-left: -10px;">
                                                <p><?= $info['date_echeance']; ?></p>
                                            </div>
                                            <div class="col-3">
                                                <p><?= $info['nom_priorite']; ?></p>
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

                                            <?php
                                            if ($_SESSION['admin'] == 2) {

                                            ?>
                                                <div class="media col-6 align-items-center">
                                                    <div style="    background-image: initial;background-color: rgb(16, 60, 139);color: rgb(232, 230, 227); width:50px;height:50px;border-radius:100px;display:flex;justify-content:center;align-items:center;">
                                                        <?= $avatar; ?>
                                                    </div>
                                                    <div class="media-body flex-truncate ml-2"> <a href="javascript:void(0)" class="d-block text-truncate" data-abc="true"><?= $info['description']; ?></a>
                                                        <div class="text-muted small text-truncate"><a href="">Prendre en charge</a> <a href="javascript:void(0)" class="text-muted" data-abc="true"><?= $info['nom_user']; ?></a></div>
                                                    </div>
                                                </div>
                                                <div id="prise">
                                                    <?php if (empty($info['etat_demande'])) {
                                                        echo "Non traitée";
                                                    } else {
                                                        echo $info['etat_demande'];
                                                    }  ?>
                                                </div>
                                            <?php
                                            }
                                            ?>
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
            if ($_SESSION['admin'] == 0) {
    ?>
        <div class="add"><a href="create.php"><i class="fi-rr-plus"></i></a></div>
    <?php
            }
    ?>

<?php
        } else {
?>
    <div class="alert alert-danger text-center"><a href="login.php"> Connectez vous </a></div>

<?php
        }
?>
</div>
</body>

</html>