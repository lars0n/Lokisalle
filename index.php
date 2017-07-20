<?php 
 require_once("inc/init.inc.php");

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

    require("inc/header.inc.php");

?>

<div class="container">
    <?= $message ?>
</div>

<?php
    require("inc/footer.inc.php");
    

    