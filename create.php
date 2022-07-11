<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Flaticon/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="styles/style-req.css">

    <title>Creation d'une demande</title>

</head>

<body>

    <?php include "header.php" ?>


    <div class="container-fluid mt-100">
        <?php
        include("databaseconnect.php");
        require("traitement-requête.php");


        //On Sélectionne les informations des table categorie et priorité 
        $req1  = $cnx->query("SELECT * FROM categorie");
        $req2  = $cnx->query("SELECT * FROM priorite");

        //On sélectionne le nom du secteur lié a l'user connecté
        $req3 = $cnx->query('SELECT nom_secteur FROM user,secteur 
                                    WHERE user.id_secteur = secteur.id_secteur
                                    AND id_user = "' . @$_SESSION['id_user'] . '"');

        //On récupère les information de la sélection $req3 
        //Et on les stock dans la var $secteur
        $secteur = $req3->fetch();

        if (!empty($_SESSION)) {
        ?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="container">
                    <div class="box" style="padding-top:20px;padding-bottom:20px;">
                        <?php
                        if (@$msg) {
                            echo @$msg;
                        }
                        ?>
                        <div style="width: 100%;display: flex;justify-content: space-between;align-items: center">
                            <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;margin-right:70px">
                                <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Demandeur </b> </label><input type="text" class="form-control titre" name="demandeur" placeholder="<?= $_SESSION['nom'] ?> " disabled style="width:100%;">
                            </span>
                            <span style="width:50%;display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;">
                                <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Direction </b> </label><input type="text" class="form-control titre" name="secteur" placeholder="<?= $secteur['nom_secteur'] ?> " disabled style="width:100%;">
                            </span>
                        </div>
                        <div style="width: 100%;display: flex;justify-content: space-between;align-items: center">
                            <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;margin-right:70px">
                                <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Beneficiaire </b> </label><input type="text" class="form-control titre" name="beneficiaire" placeholder="Beneficiaire" style="width:100%;" required>
                            </span>
                            <div style="width:50%;display: flex;justify-content: space-between;align-items: flex-start;flex-direction:row;">
                                <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;margin-right:70px">
                                    <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Date demande </b><input type="text" class="form-control titre" placeholder="<?php echo date("Y-m-d"); ?>" style="width:100%; " disabled>
                                </span>
                                <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column">
                                    <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Date souhaitée </b><input type="date" class="form-control titre" name="date" min="<?php echo date("Y-m-d"); ?>" style="width:100%" required>
                                </span>
                            </div>
                        </div>
                        <span style="width:100%;display: flex;justify-content: space-between;flex-direction:column;">
                            <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Description </b>
                                <input type="text" class="form-control titre" name="titre" placeholder="Description de la demande" required>
                        </span>
                        <div class="desc-img" style="width:100%;">
                            <span style="width:100%;display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column">
                                <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Motif de la demande </b>
                                    <textarea cols="300" rows="10" name="description" class="form-control description" placeholder="Motif de la demande" style="width:80%" required></textarea>
                            </span>
                            <div class="left">
                                <input type="file" name="document" id="image" accept=".pdf"></input><br>
                                <span class="img"><label for="image" class="icofield"><i class="fi-rr-clip upload-ico"></i><br>
                                        <p>Choisir un document (Format pdf) </p>
                                    </label></span>
                            </div>

                        </div>
                        <span class="options">
                            <span style="width:100%;display: flex;justify-content: space-between;flex-direction:column;margin-right:70px">
                                <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Type demande </b> </label>
                                <select name="categories" class="form-select selection1" required>
                                    <?php
                                    //On affiche les categorie de la $req1
                                    while ($afficher_categorie = $req1->fetch()) {
                                        echo '<option value="' . $afficher_categorie['id_categorie'] . '">' . $afficher_categorie['nom_categorie'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </span>
                            <span style="width:100%;display: flex;justify-content: space-between;flex-direction:column;margin-right:70px">
                                <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Priorite </b> </label>
                                <select name="priorités" class="form-select selection2" required>
                                    <?php
                                    //On affiche les priorités de la $req2
                                    while ($afficher_prioprite = $req2->fetch()) {
                                        echo '<option value="' . $afficher_prioprite['id_priorite'] . '">' . $afficher_prioprite['nom_priorite'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </span>
                        </span>
                        <button type="submit" name="soumettre" class="btn btn-primary border bordered" style="margin-bottom: 100px;">Soumettre</button>

                    </div>
                </div>
            </form>
        <?php
        } else {
        ?>

        <?php
        }
        ?>
    </div>
</body>

</html>