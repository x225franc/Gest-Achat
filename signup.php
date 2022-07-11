<?php
include("databaseconnect.php");

//On recupere l'email du directeur de direction
$rex = $cnx->query('SELECT email_user FROM `user` WHERE admin = 2;');
$ema = $rex->fetch();

//recuperation des differents secteurs
$rec = $cnx->query('SELECT * FROM secteur');

//On vérifie si l'utilisateur clique sur le boutton d'inscription.
if (isset($_POST['inscription'])) {

  /*Et on stock le contenu des champs du formulaire dans des variable
    en les sécurisant avec la fonction htmlspecialchars*/
    
  $userName = htmlspecialchars($_POST['userName']);
  $Email = htmlspecialchars($_POST['Email']);
  $secteurs = htmlspecialchars($_POST["secteur"]);

  //On recupère les informations de la base de données 
  //correspondant à l'email.
  $req = $cnx->prepare('SELECT * FROM user WHERE email_user = ?');
  $req->execute(array($Email));

  //On compte le nombre de lignes que retourne la requête
  $count = $req->rowCount();
  if ($count < 1) {

    //On Hash les mot de passe avec la fonction de hashage sha1.
    $password1 = sha1($_POST['password1']);
    $password2 = sha1($_POST['password2']);

    //On vérifie que tout les champs sont bien remplie
    if (!empty($userName) && !empty($Email)) {

      //On vérifie que les mots de passe correspondent bien
      if ($password1 === $password2) {
        //$insert = $cnx->query("INSERT INTO user(nom_user,email_user,password) VALUES($userName , $Email, $password1)");

        $insert = $cnx->prepare("INSERT INTO user(nom_user,email_user,password,id_secteur) VALUES(?,?,?,?)");
        $insert->execute(array(
          $userName,
          $Email,
          $password1,
          $secteurs
        ));
        @$msg = '<span class="alert alert-success">Inscription réussie ! redirection en cours... <i class="fas fa-checkmark"></i></span>';

$sub = "[GEST-ACHAT] Nouvel utilisateur enregistré !";
$mes = " 
L'utilisateur : ' ".$_POST['userName']." '  Viens de s'incrire à GEST-ACHAT ! 

                GEST-ACHAT © Copyright";

        @mail($ema['email_user'], $sub, $mes);

        header("refresh: 3; url=http://localhost/app/login.php");



      } else {
        @$msg = '<span class="alert alert-danger">Mot de passes non identiques ! <i class="fas fa-cross"></i></span>';
      }
      // Le @ permet de masquer les message d'erreur.
    }
  } else {
    @$msg = '<span class="alert alert-danger">Email déjà utilisée <i class="fas fa-cross"></i></span>';
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
  <title>Inscription</title>
</head>

<body>
  <div class="wrapper">
    <?php
    if (@$msg) {
      echo @$msg;
    }
    ?>
    <div class="logo"> <img src="ressource/logo.png" alt=""> </div>
    <br>
    <div class="text-center mt-4 name" id="nom"> GEST-ACHAT </div>
    <form class="p-3 mt-3" method="post">
      <br>
      <div class="form-field d-flex align-items-center"> <span class="far fa-user"></span> <input type="text" name="userName" id="userName" placeholder="Nom Complet" required> </div>
      <div class="form-field d-flex align-items-center"> <span class="far fa-user"></span> <input type="email" name="Email" id="Email" placeholder="Email" required> </div>
      <div class="form-field d-flex align-items-center"> <span class="fas fa-key"></span> <input type="password" name="password1" id="pwd" placeholder="Mot de Passe" required> </div>
      <div class="form-field d-flex align-items-center"> <span class="fas fa-key"></span> <input type="password" name="password2" id="pwd" placeholder="Confirmez le mot de Passe" required> </div>
      <select class="form-select" name="secteur" id="secteur">
        <?php
        while ($secteur = $rec->fetch()) {
          var_dump($secteur);
        ?>
          <option value=<?= $secteur["id_secteur"] ?>> <?= $secteur["nom_secteur"] ?> </option>
        <?php } ?>
      </select>
      <button type="submit" name="inscription" class="btn mt-3">Inscription</button>
      <h1 style="text-align:center;"><a href="login.php">Déjà inscrit ?</a></h1>
  </div>
</body>

</html>