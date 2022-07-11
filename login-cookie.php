<?php
include("databaseconnect.php");
session_start();
//On vérifie que l'utilisateur a des cookies
if (!empty(@$_COOKIE['email'] && @$_COOKIE['password'] && @$_COOKIE['nom'])) {
    
    //Si oui on stock ces différents cookies dans des variables de session
    $_SESSION['nom']  = $_COOKIE['nom'];
    $_SESSION['email'] = $_COOKIE['email'];
    header('Location:re.php');
} 
//Sinon on vérifie que l'user clique sur le boutton de connexion
elseif (isset($_POST['login'])) {
    // on stocke les informations du formulaire dans des variable
    $Email = htmlspecialchars($_POST['Email']);
    $password = sha1($_POST['password']);


    //On vérifie que le champ Email n'est pas vide
    if (!empty($Email)) {

        //On sélectionne toutes les informations de la table user ou email_user égale a la variable $Email
        $req = $cnx->prepare('SELECT * FROM user WHERE email_user = ?');
        $req->execute(array($Email));


        $ver = $req->fetch();

        //On compare l'email , le mdp entree par l'utilisateur a celle qui sont dans la bdd
        if ($Email == $ver['email_user'] && $password == $ver['password']) {
            $_SESSION['nom']  = $ver['nom_user'];
            $_SESSION['email'] = $ver['email_user'];

            //On vérifie que l'user coche la case rester connecté
            if ($_POST['remind']) {
                //Si oui on créer des variables de sessions qui vont stocker son email , password, nom_user
                
                setcookie("email", $Email, time() + 3600);
                setcookie("password", $password, time() + 3600);
                setcookie("nom", $ver['nom_user'], time() + 3600);
            }
            //Si tout le code du dessus s'est executé on redirige l'user sur la page d'acceuil
            header('Location:index.php');
        } else {
            @$msg = '<span class="alert alert-danger">informations erronées</span>';
        }
    } else {
        @$msg = '<span class="alert alert-danger">Veuillez remplir tout les champs';
    }
}
