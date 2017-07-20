<?php 
 require_once("inc/init.inc.php");

/* code pour l'inscription */

$membre = [];
$erreur = false;

if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']))
{
    foreach ($_POST as $key => $value) {
        $membre[$key] = $value;
    }

    /* vérifi si le pseudo, email et le mdp est fournie*/
    if(empty($membre['pseudo']))
    {
        $message .= '<li>Le Pseudo est requis</li>';
        $erreur = true;
    }

    if(empty($membre['mdp']))
    {
        $message .= '<li>L\'adresse email est requis</li>';
        $erreur = true;
    }

    if(filter_var($membre['email'], FILTER_VALIDATE_EMAIL))
    {
        $message .= '<li>Le Mot de Passe est requis</li>';
        $erreur = true;
    }

    if(!$erreur) {
        $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistremen) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
    }
}

 require("inc/header.inc.php");

?>

<div class="container">
    <?php if($message): ?>
    <div class="alert alert-danger">
        <?= $message ?>
    </div>
    <?php endif ?>
    <form action="" method="post" class="col-sm-7 col-sm-offset-2 well">
        <legend>Inscription</legend>
          <div class="form-group">
            <label for="pseudo">Pseudo</label>
            <input class="form-control" type="text" name="pseudo" id="pseudo">
          </div>
          <div class="form-group">
            <label for="mdp">Mot de passe</label>
            <input class="form-control" type="text" name="mdp" id="mdp">
          </div>
          <div class="form-group">
            <label for="nom">Nom</label>
            <input class="form-control" type="text" name="nom" id="nom">
          </div>
          <div class="form-group">
            <label for="prenom">Prenom</label>
            <input class="form-control" type="text" name="prenom" id="prenom">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="text" name="email" id="email">
          </div>
          <div class="form-group">
            <label for="civilite">Civilité</label>
            <select class="form-control" name="civilite" id="civilite">
              <option value="m">Homme</option>
              <option value="f">Femme</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Inscription</button>
        </form>
</div>

<?php
    require("inc/footer.inc.php");
    
