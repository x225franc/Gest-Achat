<?php
include("databaseconnect.php");

// selection des mails de directeurs dans la bdd
$reqkt = $cnx->query('SELECT directeurs FROM secteur,user 
                        WHERE secteur.id_secteur = user.id_secteur
                        AND id_user = "' . @$_SESSION['id_user'] . '" ');
$rekt = $reqkt->fetch();

//On recupere le nom du user connecté
$reqox = $cnx->query('SELECT nom_user FROM user
                    WHERE id_user = "' . @$_SESSION['id_user'] . '"');
$reqoxx = $reqox->fetch();

//On sélectionne le nom du secteur lié a l'user connecté
$reqo = $cnx->query('SELECT nom_secteur FROM user,secteur 
                    WHERE user.id_secteur = secteur.id_secteur
                    AND id_user = "' . @$_SESSION['id_user'] . '"');
$reqon = $reqo->fetch();

//

//On vérifie que l'utilisateur clique sur le boutons soumettre
if (isset($_POST['soumettre'])) {

    

    //Si oui on stock les information du formulaire dans des variables
    @$titre = htmlspecialchars($_POST["titre"]);
    @$description = htmlspecialchars($_POST["description"]);
    @$categorie = intval($_POST["categories"]);
    @$priorite = intval($_POST["priorités"]);
    @$name = $_FILES["document"]["name"];
    @$beneficiaire = htmlspecialchars($_POST["beneficiaire"]);
    @$date = htmlspecialchars($_POST["date"]);

    //On vérifie que les champs ne sont pas vides
    if (!empty($titre) && !empty($description) && !empty($categorie) && !empty($priorite)) {

        //Si oui on procède à l'insertion
        $insert = $cnx->prepare('INSERT INTO demandes(id_user,id_categorie,id_priorite,Beneficiaire,date_echeance,titre,description,date_demande,document) VALUES(?,?,?,?,?,?,?,NOW(),?)');

        //La fonction NOW() sert à avoir la date et l'heure à l'instant ou l'insertion est effectuée
        $insert->execute(array(
            $_SESSION['id_user'],
            $categorie,
            $priorite,
            $beneficiaire,
            $date,
            $titre,
            $description,
            $name
        ));

        //Juste après que l'insertion est été effectuée, on récupère le dernier id_demande qui à été inséré dans la bdd
        @$lastid = $cnx->lastInsertId();
        @$chemin = 'Document/' . $lastid . "_" . $_FILES['document']['name'];
        move_uploaded_file($_FILES['document']['tmp_name'], $chemin);

        // @$reki = $cnx->query('SELECT * FROM `demandes` WHERE id_demande =  '.$lastid.' ');
        

        $ret = $cnx->query('SELECT * FROM categorie ,user, priorite ,demandes ,secteur
                        WHERE id_demande =  '.$lastid.'
                        AND categorie.id_categorie = demandes.id_categorie
                        AND priorite.id_priorite = demandes.id_priorite 
                        AND user.id_user = demandes.id_user
                        AND secteur.id_secteur = user.id_secteur
                        ');

        @$quer = $ret->fetch();


        
        @$email_adm = $cnx->query('SELECT email_user FROM user WHERE admin = 2 ');

        
        @$annee = substr($date, 0, 4);

        // ENVOIE EMAIL // 
        
        

        @$mail = $email_adm->fetch();
        @$subject ="[GEST-ACHAT]  Nouvelle DA - ".@$annee.' - '.@$lastid." "; 
        @$message =

' 
<span style="color:red;font-size:30px";>/!\ VEUILLEZ NE PAS RÉPONDRE À CE MAIL /!\</span> <br> <br>

<span style="color:green;font-size:20px;">Nouvelle demande d\'achat à traiter.</span><br><br>

<u style=""> Emetteur de la demande d\'achat</u>: "'.$quer['nom_user'].'".<br><br>

<u>Nature de la demande</u>:  "'.$quer['nom_categorie'].'".<br><br>

<u>Béneficiaire</u>: "'.$quer['Beneficiaire'].'".<br><br>

<u>Description de la demande</u>: "'.$quer['titre'].'".<br><br>

<u>Motif de la demande</u>: "'.$quer['description'].'".<br><br>

<u>Priorité</u>: "'.$quer['nom_priorite'].'".<br><br>

<u>Date de réalisation souhaitée</u>: "'.$quer['date_echeance'].'".<br><br>

<i style="text-align:center;font-size:12px;color: rgba(234, 240, 246, 0.1)">GEST-ACHAT © '.@$annee.' Copyright</i> ';



        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= 'Cc: '.$quer['directeurs'].', '.$quer['email_user'].' ';
        

        @mail($mail['email_user'], $subject, $message,$headers);



        //Et enfin on créer une variable pour stocker notre message de réussite
        @$msg = '<span class="alert alert-success"> Demande soumise ! Redirection à l\'accueil</span>';

        ?>
        
        <script language="Javascript">
        setTimeout(() => document.location.replace("http://localhost/app/index.php"), 3000);
        </script>

        <?php 
    } else {

        //Si le format d'image ne correspond pas on créer une variable pour stocker le message d'erreur.
        @$msg = '<span class="alert alert-danger"> Veuillez remplir tout les champs !</span>';
    }
}
?>