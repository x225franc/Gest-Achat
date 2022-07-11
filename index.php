<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
</head>

<body>

    <?php include "header.php" ?>

    <?php //On inclut les differentes pages et on fais des conditions pour verifier le type d'utilisateur
    if (@$_SESSION['admin'] == 2) {
        include "admin-body.php";
    } else if (@$_SESSION['admin'] == 1) {
        include "mesdemandes.php";
    } else {
        include "body.php";
    }

    ?>

    <?php include "footer.php" ?>

</body>

</html>