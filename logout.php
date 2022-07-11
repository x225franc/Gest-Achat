<?php
//On demarre puis on détruit la session en cours
session_start();
session_destroy();

//On supprime les cookies déjà existant si l'utilisateur se déconnecte et on le redirige sur la page de connexion.
setcookie("email" , $Email , time() - 3600 * 24 * 30);
setcookie("password" , $password , time() - 3600 * 24 * 30);
setcookie("nom" , $ver['nom_user'] , time() - 3600 * 24 * 30);
setcookie("nom" , $ver['nom_user'] , time() - 3600 * 24 * 30);
setcookie("admin" , $ver['admin'] , time() - 3600 * 24 * 30);

header('Location:login.php');

?>