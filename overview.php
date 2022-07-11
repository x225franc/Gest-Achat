<?php
include('databaseconnect.php');
session_start();

$headers = 'Content-Type: text/html; charset=UTF-8';
//On effectue une requete a la bdd pour recuperer toutes les demandes de l'utilisateur connectée via sa variable de session.
$reqkt = $cnx->query('SELECT * FROM categorie ,user, priorite ,demandes ,secteur
                        WHERE user.id_user = "' . @$_SESSION['id_user'] . '"
                        AND categorie.id_categorie = demandes.id_categorie
                        AND priorite.id_priorite = demandes.id_priorite 
                        AND user.id_user = demandes.id_user
                        AND secteur.id_secteur = user.id_secteur
                        ORDER BY id_demande DESC');

//On sélectionne le nom_user lié à la demande actuel .
//Et on stocke ce nom dans la var $reqxd
$reqx = $cnx->query('SELECT nom_user FROM demandes,user 
                    WHERE user.id_user = demandes.acheteur  
                    AND demandes.id_demande = '.$_GET['document'].'');
$reqxd = $reqx->fetch();

//On sélectionne instruction,commentaire,commentaire_1,commentaire_2 lié à la demande actuel .
//Et on stocke ce nom dans la var $reqxd
$reqy = $cnx->query('SELECT instruction,commentaire,commentaire_1,commentaire_2 FROM demandes,user 
                    WHERE user.id_user = demandes.acheteur  
                    AND demandes.id_demande = '.$_GET['document'].'');
$reqyd = $reqy->fetch();
@$msgs = '<span id="n" class="alert alert-success"> Demande assignée a : '.$reqxd['nom_user'].'  </span>';

//On recupere la categorie
$req = $cnx->query('SELECT * FROM categorie WHERE id_categorie = "' . $_GET['categorie'] . '" ');
$nom_categorie = $req->fetch();

//On recupere la priorite
$req2 = $cnx->query('SELECT * FROM priorite WHERE id_priorite = "' . $_GET['priorite'] . '" ');
$nom_priorite = $req2->fetch();

//On recupere uniquement la liste des admin 1 et 2 et on fais correspondre entre 2 tables
$req3 = $cnx->query('SELECT DISTINCT id_user,nom_user,email_user FROM user,secteur 
                        WHERE user.id_secteur = secteur.id_secteur
                        AND admin = 1
                        OR admin = 2;
                    ');

//On recupere l'id des acheteurs qui ont ete assigne a la demande
$req4 = $cnx->query('SELECT acheteur FROM demandes WHERE id_demande = "' . $_GET['document'] . '" ');
$demandes = $req4->fetch();

//On recupere l'email de l'utilisateur
$req5 = $cnx->query('SELECT email_user FROM user WHERE id_user = "' . @$_GET["id_user"] . '" ');
$email_dem = $req5->fetch();

//On recupere toutes les priorites
$prio = $cnx->query("SELECT * FROM priorite");

@$commentaire = htmlspecialchars($_POST['commentaire']);
@$commentaire_1 = htmlspecialchars($_POST['commentaire_1']);
@$commentaire_2 = htmlspecialchars($_POST['commentaire_2']);
@$instruction = htmlspecialchars($_POST['instruction']);

//On recupere l'id de l'acheteurs qui est egale a l'utilisateur actuellemenent connecté
$req6 = $cnx->query('SELECT acheteur FROM `demandes` WHERE  id_demande = ' . $_GET['document'] . ' AND acheteur = ' . $_SESSION['id_user'] . ' ');
$ver_dem = $req6->fetch();

//On recupere l'id des acheteurs qui ont ete assigne a la demande , cette requete nous permet de verifier si la demande est deja attribue
$req7 = $cnx->query('SELECT acheteur FROM `demandes` WHERE  id_demande = ' . $_GET['document'] . ' ');
$ver_dem2 = $req7->fetch();



@$delai = htmlspecialchars($_POST['delai']);
@$date = htmlspecialchars($_GET["date_demande"]);
@$annee = substr($date, 0, 4);
@$date_rea = htmlspecialchars($_POST['date_rea']);


//On assigne la demande en mettant a jour la colone id_admin 
if (isset($_POST['assigner'])) {

    if (empty($demandes['acheteur'])) {

        $ach = htmlspecialchars($_POST['acheteur']);
        $nouvelle_priorite = htmlspecialchars($_POST['nouvelle_priorite']);
        $email_ach = $cnx->prepare('SELECT email_user FROM user WHERE id_user = ? ');
        $email_ach->execute(array($ach));

        $mail = $email_ach->fetch();

        $update = $cnx->query('UPDATE demandes SET id_priorite="' . $nouvelle_priorite . '" , acheteur = "' . $ach . '" , delai = "' . $delai . '", etat_demande = "En cours de traitement" ,commentaire = "'.$commentaire.'", instruction = "' . $instruction . '"  WHERE id_demande = "' . $_GET['document'] . '" ');
        @$lastid = $cnx->lastInsertId();


       

        // ENVOIE DE MAIL




        $subject = "[GEST-ACHAT] Une nouvelle demande : DA-". @$annee .'-'.$_GET['document'].' : '.$_GET['titre']." - vous a été assignée";
$message = ' 
<span style="color:red;font-size:25px";>/!\ VEUILLEZ NE PAS RÉPONDRE À CE MAIL /!\</span> <br> <br>

<span style="color:green;font-size:20px;">Nouvelle demande d\'achat à traiter.</span><br><br>

<u>Béneficiaire</u>: "'.$_GET['Beneficiaire'].'".<br><br>

<u>Description de la demande</u>: "'.$_GET['titre'].'".<br><br>

<u>Motif de la demande</u>: "'.$_GET['description'].'".<br><br>

Veuillez vous connecté sur votre espace personnel : http://localhost/app/login.php <br><br>

<i style="text-align:center;font-size:12px;color: rgba(234, 240, 246, 0.1)">GEST-ACHAT © '.@$annee.' Copyright</i> ';



        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

        @mail($mail['email_user'], $subject, $message,$headers);


        $sub = "[GEST-ACHAT]Votre Demande N° : DA-". @$annee .'-'.$_GET['document'].' : '.$_GET['titre']." / est prise en charge !";
        $mes = '
Votre Demande : DA - '  . @$annee . ' - ' .$_GET['document'] . '
Description :  "'.$_GET['titre'] .'"  EST EN COURS DE TRAITEMENT !
                GEST-ACHAT © '.@$annee.' Copyright';

        @mail($email_dem['email_user'], $sub, $mes);

        @$msg = '<span class="alert alert-success"> La DA - '  . @$annee . ' - ' .$_GET['document']  . ' a bien été assignée, redirection... </span>';
        header("refresh: 3; url=http://localhost/app/index.php");
    } else {
        @$msg = '<span class="alert alert-danger"> Cette demande a déjà été assignée';
    }
}
@$date_rea = htmlspecialchars($_POST['date_rea']);

if (isset($_POST['cloturer'])) {
    if(!empty($date_rea)){


    //On recupere toutes les informations de la demandes
    $non = $cnx2->prepare('INSERT INTO demandes(id_demande,nom_user,categorie,priorite,acheteur,Beneficiaire,date_echeance,titre,description,date_demande,etat_demande,date_realisation,commentaire,instruction,commentaire_1,commentaire_2) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $non->execute(array(
        $_GET['document'],
        $_GET['demandeur'],
        $nom_categorie['nom_categorie'],
        $nom_priorite['nom_priorite'],
        $_SESSION['id_user'],
        $_GET['Beneficiaire'],
        $_GET['date_echeance'],
        $_GET['titre'],
        $_GET['description'],
        $_GET['date_demande'],
        "traitée",
        $date_rea,
        $reqyd['commentaire'],
        $reqyd['instruction'],
        $reqyd['commentaire_1'],
        $reqyd['commentaire_2']
    ));
    if ($non) {
        //et on l'injecte dans la bdd 2 , dans les demandes cloturé
        
        $non2 = $cnx->query('DELETE FROM demandes WHERE id_demande = "' . $_GET['document'] . '" ');
        @$msg = '<span class="alert alert-success"> La demande á bien été cloturée, redirection... </span>';

        $subi = "[GEST-ACHAT] DEMANDE : DA-". @$annee .'-'.$_GET['document'].' : '.$_GET['titre']." TRAITÉE !";
        $mesi = 
        "La DA-".@$annee ."-".@$_GET['document']." est désormais cloturé ! <br><br>
        
                        GEST-ACHAT © ".@$annee." Copyright";
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= ' mariame.sanfo@bollore.com, johanne.bagou@bollore.com  ';

        @mail($email_dem['email_user'], $subi, $mesi,$headers);

        header("refresh: 3; url=http://localhost/app/index.php");
    }
}else{
    @$msg = '<span class="alert alert-danger"> Veuillez saisir la date de realistation. </span>';
}
}
if (@$_SESSION['admin'] == 2) {


    if (isset($_POST['update'])) {
        //On stock les commentaires et bdd et si jamais un commentaire a deja ete envoye en bdd , alors on desactive le champ et on fais 
        // apparaitre le commentaire qui s'y trouve selon la demande actuelle
        if(!empty($commentaire)){
            $update = $cnx->query('UPDATE demandes SET  commentaire= "'.$commentaire.'" WHERE id_demande = "'.$_GET['document'].'"');
            @$msg = '<span class="alert alert-success"> La DA - '  . @$annee . ' - ' .$_GET['document']  . ' a bien été mise à jour, redirection... </span>';
            header("refresh: 3; url=http://localhost/app/mesdemandes.php");
        }
        if(!empty($commentaire_1)){
            $update = $cnx->query('UPDATE demandes SET  commentaire_1= "'.$commentaire_1.'" WHERE id_demande = "'.$_GET['document'].'"');
            @$msg = '<span class="alert alert-success"> La DA - '  . @$annee . ' - ' .$_GET['document']  . ' a bien été mise à jour, redirection... </span>';
            header("refresh: 3; url=http://localhost/app/mesdemandes.php");
        }
        if(!empty($commentaire_2)){
            $update = $cnx->query('UPDATE demandes SET  commentaire_2= "'.$commentaire_2.'" WHERE id_demande = "'.$_GET['document'].'"');
            @$msg = '<span class="alert alert-success"> La DA - '  . @$annee . ' - ' .$_GET['document']  . ' a bien été mise à jour, redirection... </span>';
            header("refresh: 3; url=http://localhost/app/mesdemandes.php");
        }


    }
}

if (@$_SESSION['admin'] == 1) {

    if (isset($_POST['update'])) {
        //On stock les commentaires et bdd et si jamais un commentaire a deja ete envoye en bdd , alors on desactive le champ et on fais 
        // apparaitre le commentaire qui s'y trouve selon la demande actuelle
        @$date_rea = htmlspecialchars($_POST['date_rea']);
        if(!empty($commentaire)){
            $update = $cnx->query('UPDATE demandes SET  commentaire= "'.$commentaire.'" WHERE id_demande = "'.$_GET['document'].'"');
            @$msg = '<span class="alert alert-success"> La DA - '  . @$annee . ' - ' .$_GET['document']  . ' a bien été mise à jour, redirection... </span>';
            header("refresh: 3; url=http://localhost/app/index.php");
        }
        if(!empty($commentaire_1)){
            $update = $cnx->query('UPDATE demandes SET  commentaire_1= "'.$commentaire_1.'" WHERE id_demande = "'.$_GET['document'].'"');
            @$msg = '<span class="alert alert-success"> La DA - '  . @$annee . ' - ' .$_GET['document']  . ' a bien été mise à jour, redirection... </span>';
            header("refresh: 3; url=http://localhost/app/index.php");
        }
        if(!empty($commentaire_2)){
            $update = $cnx->query('UPDATE demandes SET  commentaire_2= "'.$commentaire_2.'" WHERE id_demande = "'.$_GET['document'].'"');
            @$msg = '<span class="alert alert-success"> La DA - '  . @$annee . ' - ' .$_GET['document']  . ' a bien été mise à jour, redirection... </span>';
            header("refresh: 3; url=http://localhost/app/index.php");
        }


    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.1.3-dist/css/bootstrap.min.css">
    <title>Prise en charge</title>
</head>

<link rel="stylesheet" href="styles/style2.css">

<?php
include("header.php");
?>

<body id="print">

    <!-- imprimer -->

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print * {
                visibility: visible;

            }

            .left * {
                display: none;
            }

            #n {
                visibility: hidden;
                display: none;
            }

            #date {
                font-size: 11.5px;
            }

        }
    </style>

<?php if (@$_SESSION['admin'] == 2) {
?>
    <a href="mesdemandes.php"><span class="fi-rr-arrow-left" style="font-size:45px; margin-left: 5vw;margin-top:1vh"></a>

    <?php } ?>

    <?php if (@$_SESSION['admin'] == 1) {
?>
    <a href="index.php"><span class="fi-rr-arrow-left" style="font-size:45px; margin-left: 5vw;margin-top:1vh"></a>

    <?php } ?>



    <a href="" onclick="window.print()"><span class="fi-rr-print" style="font-size:30px; float:right;margin-right: 5vw; margin-top:1vh;"></a>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="container print">
            <div class="box" style="padding-top:20px;padding-bottom:30px;">

                <?php
                if (@$_SESSION['admin'] >= 1){
                @$annee = substr($_GET['date_demande'], 0, 4);
                }
                if (@$_SESSION['admin'] == 0){ 
                    $infost = $reqkt->fetch();
                @$annee = substr($infost['date_demande'], 0, 4);
                }
                if (@$msg) {
                    echo @$msg;
                } else if(!empty ($ver_dem2['acheteur'])) {
                    echo @$msgs;
                }
                ?>

                <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;margin-right:70px">
                    <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""><b>Demande N° :</b></label><input type="text" class="form-control titre" name="demandeur" placeholder=" DA - <?= $annee; ?> - <?= $_GET['document'] ?> " disabled style="width:100%; ">

                <div style="width: 100%;display: flex;justify-content: space-between;align-items: center">
                    <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;margin-right:70px">
                        <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""><b>Demandeur</b></label><input type="text" class="form-control titre" name="demandeur" placeholder=" <?= $_GET['demandeur'] ?> " disabled style="width:100%; ">
                    </span>
                    <span style="width:50%;display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;">
                        <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""><b>Direction</b></label><input type="text" class="form-control titre" name="secteur" placeholder="<?= $_GET['secteur'] ?> " disabled style="width:100%;">
                    </span>
                </div>
                <div style="width: 100%;display: flex;justify-content: space-between;align-items: center">
                    <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;margin-right:70px">
                        <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"><b>Benificiaire</b></label><input type="text" class="form-control titre" name="beneficiaire" placeholder="<?= $_GET['Beneficiaire'] ?>" style="width:100%" disabled>
                    </span>
                    <div style="width:50%;display: flex;justify-content: space-between;align-items: flex-start;flex-direction:row;">
                        <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column;margin-right:70px">
                            <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Date demande </b>
                                <input type="text" class="form-control titre" placeholder="<?php echo date("Y-m-d"); ?>" style="width:100%; " id="date" disabled>
                        </span>
                        <span style="display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column">
                            <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"><b>Date souhaitée</b></label><input type="text" class="form-control titre" name="date" placeholder="<?= $_GET['date_echeance'] ?> " id="date" style="width:100%" disabled>
                        </span>
                    </div>

                </div>
                <span style="width:100%;display: flex;justify-content: space-between;flex-direction:column;">
                    <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Description </b>
                        <input type="text" class="form-control titre" name="titre" placeholder=" <?= $_GET['titre']; ?>" disabled style="width:100%">
                </span>
                <div class="desc-img" id="motif" style="width:100%;">
                    <span style="width:100%;display: flex;justify-content: space-between;align-items: flex-start;flex-direction:column">
                        <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Motif de la demande </b>
                            <textarea style="width:80%;" cols="300" rows="10" name="description" class="form-control description" placeholder="<?= $_GET['description']; ?> " disabled></textarea>
                    </span>
                    <div class="left" id="n">
                        <input type="file" name="image" required id="image" disabled>
                        <br>
                        <?php if(!empty($_GET['nom_document'])){ 
                        ?>
                        <!-- Pour changer le lien du fichier -->
                         <a href="visualiser.php?file=http://localhost/app/Document/<?= $_GET['document'] ?>_<?= $_GET['nom_document'] ?>" target="_blank" style="margin-bottom:-2px;"> Previsualiser le document </a>
                            <a href="http://localhost/app/Document/<?= $_GET['document'] ?>_<?= $_GET['nom_document'] ?>" download="<?= $_GET['nom_document'] ?>">
                            <br> Telecharger le document
                            <?php } else { echo "Aucun fichier joint";} ?>
                            <span class="img">
                                <label style="text-shadow: 2px 2px 5px rgba(0,0,0,0.71);" for="btn" class="icofield"><?= $_GET['nom_document'] ?><i class="fi-rr-clip upload-ico"></i><br>
                                </label><img src="" alt="" id="imgprev">
                            </span> </a> 
                            
                    </div>
                </div>
                <div class="options">
                    <span style="width:50%;display: flex;justify-content: space-between;flex-direction:column;margin-right:70px">
                        <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Type demande </b> </label>
                        <input type="text" name="categories" class="form-select" placeholder="<?= $nom_categorie['nom_categorie'] ?>" id="date" disabled>
                    </span>
                    <div style="width:50%;display: flex;justify-content: flex-end;align-items:center">
                        <span style="width:100%;display: flex;justify-content: space-between;flex-direction:column;margin-right:30px;">
                            <label for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Priorite </b> </label>
                            <input type="text" name="priorite" class="form-select" placeholder="<?= $nom_priorite['nom_priorite']; ?>" id="date" disabled style="margin-right:15px;">
                        </span>



                        <span id="n" style="width:100%;align-items:center;justify-content:flex-start;flex-direction:column">
                        
                            
                            <?php
                            //On verifie que l'utilisateur est un admin de niveau 2 , que la case acheteur est bien vide et 
                            // que le token dans notre get est different de 15 , alors on affiche les champs
                            if ($_SESSION['admin'] == 2) {
                                if (empty($ver_dem2['acheteur']) && $_SESSION['admin'] == 2 and @$_GET['token'] != 15) {
                            ?>
                            
                                    <label id="n" for="" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);"> <b> Confirmer priorité </b> </label>
                                    <select name="nouvelle_priorite" class="form-select" required>
                                        <?php
                                        //Boucle pour faire apparaitre tout les priorites
                                        while ($priorite = $prio->fetch()) {
                                        ?>
                                            <option value=<?= $priorite['id_priorite'] ?>> <?= $priorite['nom_priorite'] ?> </option>
                                        <?php
                                        }
                                        ?>
                                    </select>

                                <?php
                                }
                            }
                                ?>

                                <?php 
                                //On verifie si l'admin est superieur ou egal a 1 , si la case acheteur n'est pas vide et si 
                                // l'utilisateur connectee est egale a celle de l'acheteur en bdd , alors on fais apparaitre les champs
                                if($_SESSION['admin'] >= 1 AND !empty($ver_dem2['acheteur']) AND $_SESSION['id_user'] == $ver_dem2['acheteur'] ){
                                ?>

                                <span id="n" style="width:100%;display: flex;justify-content: space-between;flex-direction:column;margin-right:30px;">
                                    <label id="n" for="">Date de realisation effective</label>
                                    <input id="daterea n" type="date" class="form-control" name="date_rea">
                                </span>
                                <?php
                                }
                                ?>
                
                        </span>
                    </div>
                </div>
                <br>

                <?php if ($_SESSION['admin'] == 2) { ?>
                    <?php 
                    //Si l'admin est de niveau 2 alors on affiche les champs pour l'assignation de demandes
                    if ($_SESSION['admin'] == 2) {
                    ?>
                        <div style="width:100%;display: flex;flex-direction:row;align-items:center;margin-bottom:3vh">
                            <?php if (empty($ver_dem2['acheteur']) && $_SESSION['admin'] == 2 and @$_GET['token'] != 15) {  ?>
                                <span style="width:50%;display: flex;    margin-right: 35px;flex-direction:column;">
                                    <label id="n"> <b> Assigner cette demande a : </b> </label>
                                    <select id="n" class="form-select" name="acheteur" id="acheteur" style="width: 92%;padding: 10px 15px;" required>

                                        <?php
                                        while ($acheteur = $req3->fetch()) {
                                        ?>
                                            <option id="<?= $acheteur['id_user'] ?>" value=<?= $acheteur['id_user'] ?>><?= $acheteur['nom_user'] ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                            <?php } ?>
                            <?php
                            //On recupere les informations depuis l'url et on affiche les champs
                            $_GET['acheteur'] = -2;
                            if ($_GET['acheteur'] = -2 && empty($ver_dem2['acheteur'] && $_SESSION['id_user'] == $ver_dem2['acheteur'])) {
                            ?>
                                <span  id="n" style="display: flex;justify-content: center;align-items: flex-start;flex-direction:column;margin-right:10px">
                                    <label id="n" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Delai a respecter </b>
                                        <input type="date" class="form-control" style="width:100%;padding:10px;" id="n" name="delai" required>
                                </span>
                        </div>
                        <span id="n" style="margin-bottom:10px;float:left;">
                            <label style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b> Instructions </b>
                                <textarea rows="5" cols="200" name="instruction" placeholder="Instructions" class="form-control" style="width:100%;" required></textarea>
                        </span>
                    <?php } ?>

                <?php
                    }
                ?>

                <?php
                    if (empty($ver_dem2['acheteur']) && $_SESSION['admin'] == 2 &&  @$_GET['token'] != 15) {
                ?>
                    <div style="width:100%;display: flex;justify-content: space-between;flex-direction:row;align-items:center">
                        <button class="button btn-success" id="n" target="_blank" name="assigner">Assigner</button>
                    <?php
                    }
                    ?>
                <?php } ?>



                <?php
                    //On verifie si l'utilisateur connecte est bien assigne a la demande , on verifie si la case acheteur n'est pas vide
                    //on verifie si l'admin est superieur ou egale a 1 et on verifie si le get de l'url est egale a -2 ,
                    // alors on fais apparaitre la case commentaires qui peut etre rempli.
                    //si jamais un commentaire a deja ete envoye en bdd , alors on desactive le champ et on fais 
                    // apparaitre le commentaire qui s'y trouve selon la demande actuelle
                    if ($_SESSION['id_user'] == $ver_dem2['acheteur'] && !empty($ver_dem2['acheteur']) && $_SESSION['admin'] >= 1 && $_GET['acheteur'] = -2) {

                    if(!empty($reqyd['commentaire'])){
                        $disable1 = "disabled";
                        @$placeholder1 = $reqyd['commentaire'];
                    }else{
                        $disable1 = "";
                        @$placeholder1 = "";
                    } 
                    if(!empty($reqyd['commentaire_1'])){
                        $disable2 = "disabled";
                        @$placeholder2 = $reqyd['commentaire_1'];
                    }else{
                        $disable2 = "";
                        @$placeholder2 = "";
                    } 
                    if(!empty($reqyd['commentaire_2'])){
                        $disable3 = "disabled";
                        @$placeholder3 = $reqyd['commentaire_2'];
                    }else{
                        $disable3 = "";
                        @$placeholder3 ="";
                    }
                    
                    
                ?>
                    <span id="n" style="margin-bottom:10px;float:left;">
                        <label id="n" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b id="n"> Commentaire 1</b>
                            <textarea id="n" rows="5" cols="200" name="commentaire" placeholder="<?= @$placeholder1 ?>" class="form-control" style="width:100%;"  <?=  @$disable1;  ?>></textarea>
                    </span>
                    
                    <span id="n" style="margin-bottom:10px;float:left;">
                        <label id="n" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b id="n"> Commentaire 2</b>
                            <textarea id="n" rows="5" cols="200" name="commentaire_1" placeholder="<?= @$placeholder2 ?>" class="form-control" style="width:100%;" <?=  @$disable2;  ?>></textarea>
                    </span>

                    <span id="n" style="margin-bottom:10px;float:left;">
                        <label id="n" style="text-shadow: 3px 1px 0px rgba(0,90,90,0.1);" for=""> <b id="n"> Commentaire 3</b>
                            <textarea id="n" rows="5" cols="200" name="commentaire_2" placeholder="<?= @$placeholder3 ?>" class="form-control" style="width:100%;" <?=  @$disable3;  ?>></textarea>
                    </span>


                <?php }  ?>


                    
                <?php
                // on verifie si l'admin est superieur ou egale a 2 , dans ce cas on fais apparaitre le boutton
                if ($_SESSION['admin'] >= 1) {
                    ?>
                 <span style="display:flex;justify-content:space-around;align-items:center;width:50%;">
                    <?php if($_SESSION['id_user'] == $ver_dem2['acheteur'] && !empty($ver_dem2['acheteur']) && $_SESSION['admin'] >= 1 && $_GET['acheteur'] = -2){ ?>
                    <button class="button btn-secondary" id="n" target="_blank" name="update" style="margin-left:1vw;width:50%;">Actualiser</button>
                    <?php } ?>
                    <button class="button btn-danger" id="n" target="_blank" name="cloturer" style="width:50%;">Cloturer</button>
                </span>
                <?php
                }//   }
                ?>
                
            </div>
                   
            </div>
        </div>
    </form>




</body>

</html>