<?php 
    /* code pour la connexion */
    /* vérifi si le formulaire est valide*/
    if(isset($_POST['email'], $_POST['mdp']))
    {
        $email  = $_POST['email'];
        $mdp    = $_POST['mdp'];

        /* vérifie si l'utilisateur existe */
        $user_exist = $pdo->prepare("SELECT * FROM membre WHERE email = ?");
        $user_exist->execute([$email]);

        /* si l'utilisateur existe et si le mot de passe est correct on le connecte */
        $utilisateur = $user_exist->fetch(PDO::FETCH_ASSOC);
        if($utilisateur && password_verify($mdp, $utilisateur['mdp']))
        {
            utilisateursession($utilisateur);
            $message = '<div class="alert alert-success"><strong>Félicitation!</strong> Vout êtes connecté</div>';
        }else
        {
            $message = '<div class="alert alert-danger">l\'Email ou le Mot de passe sont incorrecte!<br> Merci de réessayer.</div>';
        }
    }

        /* gestion de la deconnexion */
        if(isset($_GET['action']) && $_GET['action'] == 'deconnexion')
        {
            unset($_SESSION['utilisateur']);
            $message = '<div class="alert alert-success">Vous avez été deconnecté.</div>';
            header("Refresh: 3; URL=index.php");    
        }
    
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- custome css-->
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Lokisalle</title>
</head>
<body>
<?php require("navbar.inc.php") ?>