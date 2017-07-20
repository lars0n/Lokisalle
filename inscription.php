<?php 
require_once("inc/init.inc.php");

if(utilisateur_est_connecte()) {
    header('location:index.php');
}

/* code pour l'inscription */

$membre = [];
$erreur = false;

/* verifie si le formulaire est valide */
if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']))
{
    /* crée un tableau avec les info de l'utilisateur*/
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
        $message .= '<li>Le Mot de Passe est requis</li>';    
        $erreur = true;
    }

    if(!filter_var($membre['email'], FILTER_VALIDATE_EMAIL))
    {
        $message .= '<li>L\'adresse email est requis</li>';
        $erreur = true;
    }

    /* si il n'a pas d'erreur on peut enregistrer l'utilisateur*/
    if(!$erreur) {

        /* on verifie si il n'est pas deja présent dans la bdd*/
        $req = $pdo->prepare("SELECT * FROM membre WHERE email = ?");
        $req->execute([$membre['email']]);

        /* si c'est le cas on ne l'enregistre pas */
        if($req->fetch()) {
            $message = '<li>Cette utilisateur existe déja</li>';
        }else
        {
            $mdp = password_hash($membre['mdp'], PASSWORD_BCRYPT);
            $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
            $enregistrement->execute([$membre['pseudo'], $mdp, $membre['nom'], $membre['prenom'], $membre['email'], $membre['civilite']]);

            header('location:index.php');
        }
    }
}

 require("inc/header.inc.php");

?>

<div class="container">
    <?php if($message): ?>
    <div class="alert alert-danger">
        <ul>
        <?= $message ?>
        </ul>
    </div>
    <?php endif ?>
    <form action="" method="post" class="col-sm-7 col-sm-offset-2 well">
        <legend>Inscription</legend>
          <div class="form-group">
            <label for="pseudo">Pseudo</label>
            <input class="form-control" type="text" name="pseudo" id="pseudo" value="<?= (isset($membre['pseudo'])? $membre['pseudo'] : null) ?>">
          </div>
          <div class="form-group">
            <label for="mdp">Mot de passe</label>
            <input class="form-control" type="text" name="mdp" id="mdp">
          </div>
          <div class="form-group">
            <label for="nom">Nom</label>
            <input class="form-control" type="text" name="nom" id="nom" value="<?= (isset($membre['nom'])? $membre['nom'] : null) ?>">
          </div>
          <div class="form-group">
            <label for="prenom">Prenom</label>
            <input class="form-control" type="text" name="prenom" id="prenom" value="<?= (isset($membre['prenom'])? $membre['prenom'] : null) ?>">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="text" name="email" id="email" value="<?= (isset($membre['email'])? $membre['email'] : null) ?>">
          </div>
          <div class="form-group">
            <label for="civilite">Civilité</label>
            <select class="form-control" name="civilite" id="civilite">
              <option value="m">Homme</option>
              <option value="f" <?= (isset($membre['civilite']) && $membre['civilite'] == 'f')? "selected" : null ?>>Femme</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Inscription</button>
        </form>
</div>

<?php
    require("inc/footer.inc.php");
    
