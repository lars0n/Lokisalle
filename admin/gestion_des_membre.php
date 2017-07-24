<?php
    require_once("../inc/init.inc.php");

    // ici je récupere les membres
    $membres   = $pdo->query("SELECT id_membre, pseudo, nom, prenom, email, civilite, statut, date_enregistrement FROM membre");
    $nb_colone  = $membres->columnCount();

    // ici je recupere le membres
    if(isset($_GET['action']) && $_GET['action'] == 'modification' && is_numeric($_GET['id_membre']))
    {

        $membre_details = $pdo->prepare("SELECT * FROM membre WHERE id_membre = ?");
        $membre_details->execute([$_GET['id_membre']]);
        $membre_details = $membre_details->fetch(PDO::FETCH_OBJ);
    }

    // mettre en place un controle pour savoir si l'utilisateur veut une suppression d'une salle
    if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_membre']) && is_numeric($_GET['id_membre'])) 
    {
        // is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POST sont toujours de type string)
        // on fait une requete pour récupérer les informations de l'article afin de connaitre la photo pour la supprimer
        $id_membre = $_GET['id_membre'];

        $pdo->prepare("DELETE FROM membre WHERE id_membre = ?")->execute([$id_membre]);

        header('location:gestion_des_membre.php');
    } 

    $membre_post = [];
    $erreur = false;
    // controle du formulaire
    if(isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['civilite']) && isset($_POST['statut']))
    {
        /* crée un tableau avec les info de l'utilisateur*/
        foreach ($_POST as $key => $value) {
            $membre_post[$key] = $value;
        }

        /* vérifi si le pseudo, email et le mdp est fournie*/
        if(empty($membre_post['pseudo']))
        {
            $message .= '<li>Le Pseudo est requis</li>';
            $erreur = true;
        }

        if(empty($membre_post['mdp']) && $_GET['action'] != 'modification')
        {
            $message .= '<li>Le Mot de Passe est requis</li>';    
            $erreur = true;
        }

        if(!filter_var($membre_post['email'], FILTER_VALIDATE_EMAIL))
        {
            $message .= '<li>L\'adresse email est requis</li>';
            $erreur = true;
        }        

        /* si il n'a pas d'erreur on peut enregistrer l'utilisateur*/
        if(!$erreur) {
       
            /* on verifie si il n'est pas deja présent dans la bdd*/
            $req = $pdo->prepare("SELECT * FROM membre WHERE email = ?");
            $req->execute([$membre_post['email']]);

            /* mise a jour du membres*/
            if(isset($_GET['action']) && $_GET['action'] == 'modification' && is_numeric($_GET['id_membre']))
            {
                $id_membre = $_GET['id_membre'];
                $enregistrement = $pdo->prepare("UPDATE membre SET pseudo = ?, nom = ?, prenom = ?, email = ?, civilite = ?, statut = ? WHERE id_membre = ?");
                $enregistrement->execute([$membre_post['pseudo'], $membre_post['nom'], $membre_post['prenom'], $membre_post['email'], $membre_post['civilite'], $membre_post['statut'], $id_membre]);

                header('location:gestion_des_membre.php');
                exit();

            }/* si c'est le cas on ne l'enregistre pas */
            elseif($req->fetch()) {
                $message = '<li>Cette utilisateur existe déja</li>';
            }else
            {
                $mdp = password_hash($membre_post['mdp'], PASSWORD_BCRYPT);
    
                    $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                    $enregistrement->execute([$membre_post['pseudo'], $mdp, $membre_post['nom'], $membre_post['prenom'], $membre_post['email'], $membre_post['civilite'], $membre_post['statut']]);

                header('location:gestion_des_membre.php');
            }
        }
    }

    require("incAdmin/header.inc.php");
?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Gestion des Membres
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-outline btn-primary pull-right" data-toggle="modal" data-target="#ajoutproduit">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    </button> 
                </h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Membres
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover text-center" id="dataTables-example">
                            <thead>
                                <tr>
                                    <!-- pour  récupérer les nom des colonne-->
                                    <?php for($i = 0 ; $i < $nb_colone; $i++): ?>
                                        <th><?= $membres->getColumnMeta($i)['name'] ?></th>
                                    <?php endfor ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- récupere les membres dans la bdd-->
                                <?php while($membre = $membres->fetch(PDO::FETCH_OBJ)): ?>
                                    <tr>
                                        <?php foreach($membre AS $key => $valeur): ?>
                                            <?php if($key == 'civilite'): ?>
                                            <td><?= ($valeur == 'm')? "Homme": "Femme" ?></td>  
                                            <?php elseif($key == 'statut'): ?>
                                            <td><?= ($valeur == 1)? "Admin": "Membre" ?></td>
                                            <?php else: ?>
                                            <td><?= $valeur ?></td>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                            <td class="text-center">
                                                <a href="?id_membre=<?= $membre->id_membre ?>&action=details" class="btn btn-success"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
                                                <a href="?id_membre=<?= $membre->id_membre ?>&action=modification" class="btn btn-primary" ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                                <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#suppresionModal<?= $membre->id_membre ?>" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                                            </td>
                                    </tr>
                                    <!-- Modal suppression -->
                                    <div class="modal fade" id="suppresionModal<?= $membre->id_membre ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Supprimer une salle</h4>
                                            </div>
                                            <div class="modal-body">
                                                Etes Vous sure de vouloir supprimer cette salle?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                                <a href="?id_membre=<?= $membre->id_membre ?>&action=supprimer" class="btn btn-danger">Confirmer</a>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                <?php endwhile ?>
                            </tbody>
                        </table>
                    </div><!-- panel body-->
                </div><!-- panel default-->
            </div><!-- col-12 -->
        </div><!-- row -->

        <?php if(isset($_GET['action']) && $_GET['action'] == 'modification'): ?>
        <form action="" method="post" class="well">
        <legend>Modification d'un Membre <a href="gestion_des_membre.php" class="btn btn-danger pull-right">X</a></legend>
            <div class="row">
                <div class="col-sm-6">
                    <input type="hidden" name="mdp">
                    <div class="form-group">
                        <label for="pseudo">Pseudo</label>
                        <input class="form-control" type="text" name="pseudo" id="pseudo" value="<?= $membre_details->pseudo ?>">
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input class="form-control" type="text" name="nom" id="nom" value="<?= $membre_details->nom ?>">
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prenom</label>
                        <input class="form-control" type="text" name="prenom" id="prenom" value="<?= $membre_details->prenom ?>">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input class="form-control" type="text" name="email" id="email" value="<?= $membre_details->email ?>">
                    </div>
                    <div class="form-group">
                        <label for="civilite">Civilité</label>
                        <select class="form-control" name="civilite" id="civilite">
                        <option value="m">Homme</option>
                        <option value="f" <?= ($membre_details->civilite == 'f')? "selected" : null ?>>Femme</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statut">Statut</label>
                        <select class="form-control" name="statut" id="statut">
                        <option value="0">Membre</option>
                        <option value="1" <?= ($membre_details->statut == 1)? "selected" : null ?>>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
                </div>
            </div><!-- /.row-->
        </form>
        <?php endif ?>
    </div>
    <!-- /.wrapper-->


    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="ajoutproduit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Ajout d'un membre</h4>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="pseudo">Pseudo</label>
                                    <input class="form-control" type="text" name="pseudo" id="pseudo" value="<?= (isset($membre_post['pseudo'])? $membre_post['pseudo'] : null) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="mdp">Mot de passe</label>
                                    <input class="form-control" type="text" name="mdp" id="mdp">
                                </div>
                                <div class="form-group">
                                    <label for="nom">Nom</label>
                                    <input class="form-control" type="text" name="nom" id="nom" value="<?= (isset($membre_post['nom'])? $membre_post['nom'] : null) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="prenom">Prenom</label>
                                    <input class="form-control" type="text" name="prenom" id="prenom" value="<?= (isset($membre_post['prenom'])? $membre_post['prenom'] : null) ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input class="form-control" type="text" name="email" id="email" value="<?= (isset($membre_post['email'])? $membre_post['email'] : null) ?>">
                                </div>
                                <div class="form-group">
                                    <label for="civilite">Civilité</label>
                                    <select class="form-control" name="civilite" id="civilite">
                                    <option value="m">Homme</option>
                                    <option value="f" <?= (isset($membre_post['civilite']) && $membre_post['civilite'] == 'f')? "selected" : null ?>>Femme</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="statut">Statut</label>
                                    <select class="form-control" name="statut" id="statut">
                                    <option value="0">Membre</option>
                                    <option value="1" <?= (isset($membre_post['statut']) && $membre_post['statut'] == 1)? "selected" : null ?>>Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
                            </div>
                        </div><!-- /.row-->
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php 
    require("incAdmin/footer.inc.php");