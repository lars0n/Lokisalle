<?php
    require_once("../inc/init.inc.php");

    $salles     = $pdo->query("SELECT * FROM salle");
    $nb_colone  = $salles->columnCount();

    if(isset($_GET['action']) && $_GET['action'] == 'modification' && is_numeric($_GET['id_salle']))
    {

        $salle_details = $pdo->prepare("SELECT * FROM salle WHERE id_salle = ?");
        $salle_details->execute([$_GET['id_salle']]);
        $salle_details = $salle_details->fetch(PDO::FETCH_OBJ);
    }

    $salle_post = [];
    $photo_bdd  = "";
    $error = false;
    // verifi si le formulaire est valide
    if(isset($_POST['titre'], $_POST['description'], $_POST['capacite'], $_POST['categorie'], $_POST['pays'], $_POST['ville'], $_POST['adresse'], $_POST['cp']))
    {
        foreach($_POST AS $key => $value)
        {
            $salle_post[$key] = $value;
        }

        if(empty($salle_post['titre']))
        {
            $message .= 'le titre doit etre defini';
            $error = true;
        }

        // récupération de l'ancienne photo dans le cas d'une modification 
        if(isset($_GET['action']) && $_GET['action'] == "modification")
        {
            if(isset($_POST['ancienne_photo']))
            {
            $photo_bdd = $_POST['ancienne_photo'];
            }
        }

        // vérification si l'utilisateur a chargé une image
        if(!empty($_FILES['photo']['name']))
        {
            // si ce n'est pas vide alors un fichier a bien été chargé via le formulaire.
            
            // on concatène la référence sur le titre afin de ne jamais avoir un fichier avec un nom déja existant sur le serveur.
            $photo_bdd = $salle_post['cp'] . '_'  . $_FILES['photo']['name'];

            // vérification de l'extention de l'image (extension acceptées: jpg, jpeg, png, gif)
            $extension = strrchr($_FILES['photo']['name'], '.');// cette fonction prédéfine permet de découper une chaine selon un caractére fournie en 2eme argument (ici le .) Attention, cette fonction découpera la chaine à partir de la derniere occurence du 2eme argument (donc nous renvoie la chaine comprise après le dernier ponit trouvbé)
            // exemple: maphoto.jpg => on récupère .jpeg
            // exemple: maphoto.photo.png => on récupere .png
            // var_dump($extension) 

            // on transforme $extension afin que tous les caracteres soient en minuscule
            $extension = strtolower($extension); 
            // on enlève le .
            $extension = substr($extension, 1); // exemple: .jpg => jpg
            // les extentions acceptées
            $tab_extension_valide = ["jpg", "jpeg", "png", "gif"];
            // nous pouvons donc vérifier si $extention fait partie des valeur autorisé dans $tab_extention_valide.
            $verif_extension = in_array($extension, $tab_extension_valide);

            if($verif_extension && !$error)
            {
            // si $verif_extention est égal à true et que $erreur n'est pas egale a true (il n'y a pas eu d'erreur au préalable)
            $photo_dossier = RACINE_SITE . '/assets/photo/' . $photo_bdd;

            copy($_FILES['photo']['tmp_name'], $photo_dossier);
            // copy() permet de copier un fichier depuis un emplacement fourni en premier argument vers un autre emplacement fourni en deuxieme argument.
            }
            elseif(!$verif_extension) {
            $message .= '<div class="alert alert-danger">Attention, la photo n\' a pas une extension valide (extension acceptées: jpg/ jpeg/ png/ gif)</div>';
            $error = true;
            }
        }

        if(!$error)
        {
            if(isset($_GET['action']) && $_GET['action'] == 'modification')
            {
                $enrigstrement = $pdo->prepare("UPDATE salle SET titre = ?, description = ?, photo = ?, pays = ?, ville = ?, adresse = ?, cp = ?, capacite = ?, categorie = ? WHERE id_salle = ?");
                //insertion dans  la bdd de l'article
                $enrigstrement->execute([$salle_post['titre'], $salle_post['description'], $photo_bdd, $salle_post['pays'], $salle_post['ville'], $salle_post['adresse'], $salle_post['cp'], $salle_post['capacite'], $salle_post['categorie'], $salle_post['id_salle']]);
                header('location:gestion_des_salle.php');
            }else{
                $enrigstrement = $pdo->prepare("INSERT INTO salle (titre, description, photo, pays, ville, adresse, cp, capacite, categorie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                //insertion dans  la bdd de l'article
                $enrigstrement->execute([$salle_post['titre'], $salle_post['description'], $photo_bdd, $salle_post['pays'], $salle_post['ville'], $salle_post['adresse'], $salle_post['cp'], $salle_post['capacite'], $salle_post['categorie']]);
            }    
        }
    }

    require("incAdmin/header.inc.php");
?>

    <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Gestion des salles
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-outline btn-primary pull-right" data-toggle="modal" data-target="#myModal">
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
                            Salles
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <!-- pour  récupérer les nom des colonne-->
                                        <?php for($i = 0 ; $i < $nb_colone; $i++): ?>
                                        <th><?= $salles->getColumnMeta($i)['name'] ?></th>
                                        <?php endfor ?>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- récupere les salles dans la bdd-->
                                    <?php while($salle = $salles->fetch(PDO::FETCH_OBJ)): ?>
                                        <tr>
                                            <?php foreach($salle AS $key => $valeur): ?>
                                                <?php if($key == 'photo'): ?>
                                                <td><img width="150" class="img-responsive" src="<?= URL . 'assets/photo/' . $valeur ?>" alt=""></td>
                                                <?php else: ?>
                                                <td><?= $valeur ?></td>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                                <td class="text-center"><a href="?id_salle=<?= $salle->id_salle ?>&action=details" class="btn btn-success"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a> <a href="?id_salle=<?= $salle->id_salle ?>&action=modification" class="btn btn-primary" ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a> <a href="?id_salle=<?= $salle->id_salle ?>" class="btn btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
                                        </tr>
                                    <?php endwhile ?>
                                </tbody>
                            </table>
                        </div><!-- panel body-->
                    </div><!-- panel default-->
                </div><!-- col-12 -->
            </div><!-- row -->

            <?php if(isset($_GET['action']) && $_GET['action'] == 'modification'): ?>
                <form action="" method="post" enctype='multipart/form-data' class="well">
                    <legend>Modification d'une salle <a href="gestion_des_salle.php" class="btn btn-danger pull-right">X</a></legend>
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="hidden" name="id_salle" value="<?= $salle_details->id_salle ?>">
                            <div class="form-group">
                                <label for="titre">Titre</label>
                                <input  class="form-control" type="text" name="titre" id="titre" placeholder="Titre de la salle" value="<?= $salle_details->titre ?>">
                            </div>
                            <div class="form-group">
                                <label for="description">Déscription</label>
                                <textarea class="form-control" name="description" id="description" cols="30" rows="5" placeholder="Description de la salle"><?= $salle_details->description ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo</label>
                                <input class="form-control" type="file" id="photo" name="photo">
                                <p class="help-block">jpg, jpeg, png, gif</p>
                                <img width="100" src="<?= URL . 'assets/photo/' . $salle_details->photo ?>" alt="">
                                <input type="hidden" name="ancienne_photo" value="<?= $salle_details->photo ?>">
                            </div>
                            <div class="form-group">
                                <label for="capacite">Capacité</label>
                                <select class="form-control" name="capacite" id="capacite">
                                    <?php for($i = 0; $i <= 50; $i++): ?>
                                    <option <?= ($salle_details->capacite == $i)? "selected" : null ?> ><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="categorie">Catégorie</label>
                                <select class="form-control" name="categorie" id="categorie">
                                    <option value="réunion">Réunion</option>
                                    <option value="bureau" <?= ($salle_details->categorie    == 'bureau')? "selected" : null ?> >Bureau</option>
                                    <option value="formation" <?= ($salle_details->categorie == 'formation')? "selected" : null ?> >Formation</option>
                                </select>
                            </div>
                        </div><!-- col-sm-6-->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pays">Pays</label>
                                <select class="form-control" name="pays" id="pays">
                                    <option>France</option>
                                    <option <?= ($salle_details->pays == 'Belgique')? "selected" : null ?> >Belgique</option>
                                    <option <?= ($salle_details->pays == 'Espagne')? "selected" : null ?> >Espagne</option>
                                    <option <?= ($salle_details->pays == 'Italie')? "selected" : null ?> >Italie</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <select class="form-control" name="ville" id="ville">
                                    <option <?= ($salle_details->ville == 'Paris')? "selected" : null ?> >Paris</option>
                                    <option <?= ($salle_details->ville == 'Lyon')? "selected" : null ?> >Lyon</option>
                                    <option <?= ($salle_details->ville == 'Marseille')? "selected" : null ?> >Marseille</option>
                                    <option <?= ($salle_details->ville == 'Lille')? "selected" : null ?> >Lille</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <textarea class="form-control" name="adresse" id="adresse" cols="30" rows="10" placeholder="Adresse de la salle"><?= $salle_details->adresse ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="cp">Code Postal</label>
                                <input class="form-control" type="text" name="cp" id="cp" placeholder="Code Postal de la salle" value="<?= $salle_details->cp ?>">
                            </div>
                        </div><!-- col-sm-6-->
                        <button type="submit" class="btn btn-primary col-sm-10 col-sm-offset-1">Enregistrer</button>
                    </div><!-- row -->
                </form>
            <?php endif ?>

    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Ajouter une salle</h4>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype='multipart/form-data'>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="titre">Titre</label>
                                <input  class="form-control" type="text" name="titre" id="titre" placeholder="Titre de la salle" value="<?= (isset($salle_post['titre'])? $salle_post['titre'] : null) ?>">
                            </div>
                            <div class="form-group">
                                <label for="description">Déscription</label>
                                <textarea class="form-control" name="description" id="description" cols="30" rows="5" placeholder="Description de la salle"><?= (isset($salle_post['description'])? $salle_post['description'] : null) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="photo">Photo</label>
                                <input class="form-control" type="file" id="photo" name="photo">
                                <p class="help-block">jpg, jpeg, png, gif</p>
                            </div>
                            <div class="form-group">
                                <label for="capacite">Capacité</label>
                                <select class="form-control" name="capacite" id="capacite">
                                    <?php for($i = 0; $i <= 50; $i++): ?>
                                    <option><?= $i ?></option>
                                    <?php endfor ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="categorie">Catégorie</label>
                                <select class="form-control" name="categorie" id="categorie">
                                    <option value="réunion">Réunion</option>
                                    <option value="bureau" <?= (isset($salle_post['categorie']) && $salle_post['categorie'] == 'bureau')? "selected" : null ?> >Bureau</option>
                                    <option value="formation" <?= (isset($salle_post['formation']) && $salle_post['formation'] == 'formation')? "selected" : null ?> >Formation</option>
                                </select>
                            </div>
                        </div><!-- col-sm-6-->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="pays">Pays</label>
                                <select class="form-control" name="pays" id="pays">
                                    <option>France</option>
                                    <option <?= (isset($salle_post['pays']) && $salle_post['pays'] == 'Belgique')? "selected" : null ?> >Belgique</option>
                                    <option <?= (isset($salle_post['pays']) && $salle_post['pays'] == 'Espagne')? "selected" : null ?> >Espagne</option>
                                    <option <?= (isset($salle_post['pays']) && $salle_post['pays'] == 'Italie')? "selected" : null ?> >Italie</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <select class="form-control" name="ville" id="ville">
                                    <option <?= (isset($salle_post['ville']) && $salle_post['ville'] == 'Paris')? "selected" : null ?> >Paris</option>
                                    <option <?= (isset($salle_post['ville']) && $salle_post['ville'] == 'Lyon')? "selected" : null ?> >Lyon</option>
                                    <option <?= (isset($salle_post['ville']) && $salle_post['ville'] == 'Marseille')? "selected" : null ?> >Marseille</option>
                                    <option <?= (isset($salle_post['ville']) && $salle_post['ville'] == 'Lille')? "selected" : null ?> >Lille</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="adresse">Adresse</label>
                                <textarea class="form-control" name="adresse" id="adresse" cols="30" rows="10" placeholder="Adresse de la salle"><?= (isset($salle_post['adresse'])? $salle_post['adresse'] : null) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="cp">Code Postal</label>
                                <input class="form-control" type="text" name="cp" id="cp" placeholder="Code Postal de la salle" value="<?= (isset($salle_post['cp'])? $salle_post['cp'] : null) ?>">
                            </div>
                        </div><!-- col-sm-6-->
                        <button type="submit" class="btn btn-primary col-sm-10 col-sm-offset-1">Enregistrer</button>
                    </div><!-- row -->
                </form>
            </div>
        </div>
    </div>
    </div>

<?php
    require("incAdmin/footer.inc.php");