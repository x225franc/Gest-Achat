<?php
//Connexion aux bases de données
$user = "root";
$pass = "";
$host = "localhost";
$db = "bollore";
$db2 = "bollore2";

try{
    $cnx = new PDO('mysql:host='.$host.';dbname='.$db.'' , $user , $pass);
    $cnx2 = new PDO('mysql:host='.$host.';dbname='.$db2.'' , $user , $pass);
}catch(exception $e){
    die("Echec de connexion a la base de données".$e->getMessage());
}

?>