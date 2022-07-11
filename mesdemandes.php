<?php
include('databaseconnect.php');
@session_start();

//On sélectionne toutes les demandes liées à l'user connecté et on les ordonner par id_demande
$req = $cnx->query('SELECT * FROM categorie ,user, priorite ,demandes ,secteur
                        WHERE acheteur = "' . $_SESSION['id_user'] . '"
                        AND categorie.id_categorie = demandes.id_categorie
                        AND priorite.id_priorite = demandes.id_priorite 
                        AND user.id_user = demandes.id_user
                        AND user.id_secteur = secteur.id_secteur
                        ORDER BY id_demande DESC');

//On compte le nombre de demandes
@$nombre_demande = $req->rowCount();
if (!empty($_SESSION)) {
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
        <title>Mes demandes</title>

    </head>

        <?php

        if ($_SESSION['admin'] == 2) {
            include "header.php";
        }
        ?>
    <body>

    <?php

if ($_SESSION['admin'] == 2) {
    ?>
    <a href="index.php"><span class="fi-rr-arrow-left" style="font-size:45px; margin-left: 5vw;margin-top:1vh"></a>
<?php }?>




        <div class="container-fluid mt-100" style="margin-top:25px; margin-bottom:35px;">


            <h1>Vos demandes assignée</h1>

            <div style="margin-bottom:2px;">
                <input type="text" id="findField" placeholder="mots-clées" size="10" style="margin-bottom: 15px;outline:none;border:1px solid blue;margin-top: 10px;margin-left: 10px;"> 
                <input type="submit" id="rec" value="rechercher" onclick="chercher();" style="color:gray;outline:none;border:1px solid gray">
        </div>


            <?php
            //On verifie que le nombre de demandes est supérieur ou égale a 1
            if ($nombre_demande >= 1) {
            ?>

                <!-- Si oui on affiche les demande de l'user -->
                <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-header pr-0 pl-0">
                            <div class="row no-gutters align-items-center w-100">
                                <div class="col font-weight-bold pl-2">Demandes</div>
                                <div class="d-none d-md-block col-6 text-muted">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-3" style="text-align: center;padding-top:20px;margin-left:-50px;">Echeance souhaitée <br> Délai a respecter </div>
                                        <div class="col-3" style="text-align: center;padding-top:20px;margin-left:-10px;">Priorite</div>
                                        <div class="col-3" style="text-align: center;padding-top:20px;margin-left:-5px;">Acheteur</div>
                                        <div class="col-6" id="action">Etat</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <?php
                            //On récupère les information de notre selection de la var $req 
                            //et on les stock dans la var $info
                            while ($info = $req->fetch()) {

                                
                                $reqx = $cnx->query('SELECT nom_user FROM demandes,user 
                                WHERE user.id_user = demandes.acheteur  
                                AND demandes.id_demande = "' . $info['id_demande'] . '" 
                                ');

                                $reqxd = $reqx->fetch();
                                if(!empty($reqxd['nom_user'])){
                                    $avatar = strtoupper(substr($reqxd['nom_user'], 0, 2));
                                }else{
                                    $avatar = "NA";
                                }
                                $annee = substr($info['date_demande'], 0, 4);
                                $min_titre = substr($info['titre'], 0, 50);
                                if( date("Y-m-d") > $info["delai"]){
                                    $style = "background-color:rgba(255, 51, 51, 0.363)";
                                }else{
                                    $style = "unset";
                                }
                            ?>
                                                                                 <div class="card-body py-3" style="<?= $style;
                                                                $styles ?>">
                                <div class="row no-gutters align-items-center">
                                    <div class="col">
                                        <a href="overview.php?acheteur=<?= $info['acheteur'] = -1 ?>&commentaire_1=<?= $info['commentaire'] ?>&commentaire_2=<?= $info['commentaire_1'] ?>&commentaire_3=<?= $info['commentaire_2'] ?>&titre=<?= $info['titre'] ?>&description=<?= $info['description'] ?>&priorite=<?= $info['id_priorite'] ?>&categorie=<?= $info['id_categorie'] ?>&id_user=<?= $info['id_user'] ?>&demandeur=<?= $info['nom_user'] ?>&secteur=<?= $info['nom_secteur'] ?>&Beneficiaire=<?= $info['Beneficiaire'] ?>&date_demande=<?= $info['date_demande'] ?>&date_echeance=<?= $info['date_echeance'] ?>&document=<?= $info['id_demande'] ?>&nom_document=<?= $info['document'] ?>" class="text-big font-weight-semibold" data-abc="true">
                                            <h4 style="color:black;">DA - <?= $annee ?> - <?= $info['id_demande']; ?> : <?= $min_titre; ?><h4>
                                                    <h5 style="color:blue"><?= $info['nom_categorie']; ?></h5>
                                                    <h6 style="color:black;"><?= $info['date_demande']; ?> - <i style="color:green;">demandeur : <?= $info['nom_user'] ?></i></h6>
                                        </a>
                                    </div>
                                    <div class="d-none d-md-block col-6">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col-3" style=" margin-left: -10px;">
                                                <p><?= $info['date_echeance']; ?> <br> --------------- <br> <?php 
                                                        if($info['delai'] == "0000-00-00"){echo "Aucun delai";} else {echo $info['delai'];}  ?></p>
                                            </div>
                                            <div class="col-3" style=" margin-left: 10px;">
                                                <p><?= $info['nom_priorite']; ?></p>
                                            </div>
                                            <?php
                                            if ($_SESSION['admin'] == 2) {
                                            ?>
                                                <div class="media col-6 align-items-center" style="display: flex;align-items: center;">
                                                    <div style="display: flex;align-items: center;justify-content: center;flex-direction: column;width: 35px;">
                                                        <div style="    background-image: initial;background-color: rgb(16, 60, 139);color: rgb(232, 230, 227); width:50px;height:50px;border-radius:100px;display:flex;justify-content:center;align-items:center;">
                                                            <?= $avatar; ?>
                                                        </div>
                                                        <div class="media-body flex-truncate ml-2">
                                                            <div class="text-muted small text-truncate"> <a href="javascript:void(0)" class="text-muted" data-abc="true">
                                                                                                                                    <?php if (!empty($reqxd['nom_user'])) {
                                                                                                                                            echo $reqxd['nom_user'];
                                                                                                                                        } else {
                                                                                                                                            echo "Non assignée";
                                                                                                                                        } ?>
                                                                                                        </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="prise">
                                                        <?php
                                                        //On vérifie si la case 'etat de la demande' est vide dans la bdd , si c'est le cas alors on affiche le message non traitée
                                                        if (empty($info['etat_demande'])) {
                                                            echo "Non traitée";
                                                        } else {
                                                            echo $info['etat_demande'];
                                                        }  ?>
                                                    </div>
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
                echo '<div class="alert alert-danger text-center">vous n\'avez pas de demande</div>';
            }
        } else {
    ?>
    <div class="alert alert-danger text-center"><a href="login.php"> Connectez vous </a></div>

<?php
        }
?>
        <div class="add"><a href="create.php"><i class="fi-rr-plus"></i></a></div>

        <?php include "footer.php" ?>

</div>
    </body>

    </html>