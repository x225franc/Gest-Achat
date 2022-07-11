<?php
include("databaseconnect.php");
session_start();

//On vérifie si les cookies "email", "password" et "nom" existent
if (!empty(@$_COOKIE['email'] && @$_COOKIE['password'] && @$_COOKIE['nom'])) {

    //Si oui on stocke leur informations dans des variables de session et on redirige l'utilisateur vers la page d'accueil.
    $_SESSION['id_user']  = $_COOKIE['id_user'];
    $_SESSION['nom']  = $_COOKIE['nom'];
    $_SESSION['email'] = $_COOKIE['email'];
    $_SESSION['admin'] = $_COOKIE['admin'];
    $_SESSION['secteur'] = $_COOKIE['nom_secteur'];
    header('Location:index.php');
}
//Sinon si n'existe pas de cookies on vérifie que l'utilisateur clique sur le boutons de connexion
elseif (isset($_POST['login'])) {
    // on stocke les informations du formulaire dans des variable.
    $Email = htmlspecialchars($_POST['Email']);
    $password = sha1($_POST['password']);


    if (!empty($Email)) {

        //On sélectionne toutes les informations de la table user ou email_user égale a la variable $Email.
        $req = $cnx->prepare('SELECT * FROM user,secteur 
                                    WHERE email_user = ?
                                    AND user.id_secteur = secteur.id_secteur');
        $req->execute(array($Email));

        //On récupère les informations de notre requête.
        $ver = $req->fetch();

        //On vérifie la  correspondance de l'email du mot de passe saisie à ceux de la base de donnée.
        if (@$Email == @$ver['email_user'] && @$password == @$ver['password']) {
            //Si oui on stock les informations de la requête dans des variables de session.
            $_SESSION['id_user']  = $ver['id_user'];
            $_SESSION['nom']  = $ver['nom_user'];
            $_SESSION['email'] = $ver['email_user'];
            $_SESSION['admin'] = $ver['admin'];
            $_SESSION['secteur'] = $ver['nom_secteur'];

            //On vérifie que l'utilisateur souhaite rester connecté en ayant cocher le case "rester connecté".
            if (isset($_POST['remind'])) {

                //Si oui on créer différents cookies d'une durée de 1 heure pour stocker l'email, le mot de passe et le nom d'utilisateur.
                setcookie("id_user", $ver['id_user'], time() + 3600 * 24 * 30);
                setcookie("email", $Email, time() + 3600 * 24 * 30);
                setcookie("password", $password, time() + 3600 * 24 * 30);
                setcookie("nom", $ver['nom_user'], time() + 3600 * 24 * 30);
                setcookie("admin", $ver['admin'], time() + 3600 * 24 * 30);

                //setcookie(name, value, expire, path, domain, secure, httponly)
            }

            //Si tout le code a bien été executé on redirige l'utilisateur sur la page d'accueil.
            header('Location:index.php');
        } else {

            //Si les informations ne correspondent pas on créer une variable qui va stocker le message d'erreur.
            @$msg = '<span class="alert alert-danger">informations erronées</span>';
        }
    } else {
        //Si tout les champs n'ont pas été remplis on créer une variable qui va stocker le message d'erreur.
        @$msg = '<span class="alert alert-danger">Veuillez remplir tout les champs</span>';
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
    <title>Connexion</title>
</head>

<body>
    <div class="wrapper">
        <?php if (@$msg) {
            echo @$msg;
        }  ?>
        <div class="logo"> <img src="ressource/logo.png" alt=""> </div>
        <br>
        <div class="text-center mt-4 name" id="nom"> GEST-ACHAT </div>
        <form class="p-3 mt-3" method="post">
            <br>
            <div class="form-field d-flex align-items-center"> <span class="far fa-user"></span> <input type="email" name="Email" id="Email" placeholder="Email" required> </div>
            <div class="form-field d-flex align-items-center"> <span class="fas fa-key"></span> <input type="password" name="password" id="pwd" placeholder="Mot de Passe" required> </div>
            <div class="text-center" id="Check"><input type="checkbox" name="remind" id=""> ‎ rester connecté(e) ? </div>
            <button type="submit" name="login" class="btn mt-3">Connexion</button>
        </form>
        <div class="text-center fs-6" id="text1"> <a href="fpassword.php">Mot de passe oublie? </a>
            <p style="margin-bottom:0"> ou </p> <a href="signup.php"> Inscription</a>
        </div>
    </div>
</body>

</html>