<?php
include("databaseconnect.php");


//On verifie que l'user clique sur le bouttons login
if (isset($_POST['login'])) {

    //On récupère le contenu du
    // champ email de notre formulaire et on le sécurise avec la fonction htmlspecialchars()
    @$Email = htmlspecialchars($_POST['Email']);

    //On recupere les addresse email de la bdd  et on les stock dans la variable $mail
    $req = $cnx->prepare('SELECT email_user FROM user WHERE email_user = ?');
    $req->execute(array($Email));

    $mail = $req->fetch();
    
    //On compare les variables pour verifier si l'email n'existe pas deja en bdd
    if ($Email = $mail) {

        //On envoie un mail de reinitialisation de mot de passe a l'utilisateur et l'on encode le lien
        $email = htmlspecialchars($_POST['Email']);

        $subject = "[GEST-ACHAT] Reinitialisez votre mot de passe GEST-ACHAT";
        $message = '
Cliquez sur ce lien pour reinitialiser votre mot de passe : http://localhost/app/rpassword.php?qNx!=' . base64_encode($_POST['Email']) . ' 
        
                                GEST-ACHAT © '.@$annee.'Copyright';
        $headers = 'Content-type: text/html; charset=UTF-8' . "\r\n";

        @mail($email, $subject, $message,$headers);

        @$msg = '<span class="alert alert-success">Lien de reinitialisation envoyer !</span>';
        header("refresh: 5; url=http://localhost/app/login.php");
    } else {
        @$msg = '<span class="alert alert-danger">Email inconnu ! </span>';
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
    <link rel="stylesheet" href="style.css">
    <title>Reinitialiser mot de passe</title>
</head>

<body>
    <div class="wrapper">
        <?php if (@$msg) {
            echo @$msg;
        }  ?>
        <div class="logo">
            <img src="ressource/logo.png" alt="">
        </div>
        <br>
        <div class="text-center mt-4 name" id="nom"> GEST-ACHAT </div>
        <form class="p-3 mt-3" method="post">
            <br>
            <div class="form-field d-flex align-items-center">
                <span class="far fa-user"></span>
                <input type="email" name="Email" id="Email" placeholder="Entrez votre Email" required>
            </div>

            <button type="submit" name="login" class="btn mt-3">Envoyer</button>
        </form>

    </div>
</body>

</html>