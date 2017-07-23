<?php
    require_once("../inc/init.inc.php");

    // je recupere les salle
    $salles = $pdo->query("SELECT * FROM salle");

    // ici je récupere les produit
    $produits   = $pdo->query("SELECT produit.id_produit, produit.date_arrivee, produit.date_depart, produit.id_salle, produit.prix, produit.etat, salle.titre, salle.photo FROM produit, salle WHERE produit.id_salle = salle.id_salle");
    $nb_colone  = $produits->columnCount();

    $erreur = false;
    // controle du formulaire
    if(isset($_POST['date_arrivee'], $_POST['date_depart'], $_POST['salle'], $_POST['prix']))
    {
        $arrive = $_POST['date_arrivee'];
        $depart = $_POST['date_depart'];
        $salle  = $_POST['salle'];
        $prix   = $_POST['prix'];

        if(empty($arrive) || empty($depart) || empty($salle) || empty($prix))
        {
            $message = '<div class="alert alert-danger">Tout les champs sont obligatoire, merci de vérifier</div>';
            $erreur  = true;
        }

        if(!$erreur)
        {
            $enregistrement = $pdo->prepare("INSERT INTO produit (id_salle, date_arrivee, date_depart, prix, etat) VALUES (?, ?, ?, ?, ?)");
            $enregistrement->execute([$salle, $arrive, $depart, $prix, 'libre']);
        }
    }

    require("incAdmin/header.inc.php");
?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Gestion des produits
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
                        Produits
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover text-center" id="dataTables-example">
                            <thead>
                                <tr>
                                    <!-- pour  récupérer les nom des colonne-->
                                    <?php for($i = 0 ; $i < $nb_colone; $i++): ?>
                                        <?php if($produits->getColumnMeta($i)['name'] == 'titre' || $produits->getColumnMeta($i)['name'] == 'photo'): ?>
                                            <!-- n'affiche rien-->
                                        <?php else: ?>
                                            <th><?= $produits->getColumnMeta($i)['name'] ?></th>
                                        <?php endif ?>
                                    <?php endfor ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- récupere les salles dans la bdd-->
                                <?php while($produit = $produits->fetch(PDO::FETCH_OBJ)): ?>
                                    <tr>
                                        <?php foreach($produit AS $key => $valeur): ?>
                                            <?php if($key == 'id_salle'): ?>
                                            <td class="text-center">
                                                <span><?= $produit->id_salle . ' - ' .  $produit->titre?><br/><br/></span>
                                                <img width="100" height="100" class="center-block" src="<?= URL . 'assets/photo/' . $produit->photo ?>" alt="">
                                            </td>
                                            <?php elseif($key == 'titre' || $key == 'photo'): ?>
                                            <!-- n'affiche rien-->
                                            <?php else: ?>
                                            <td><?= $valeur ?></td>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                            <td class="text-center">
                                                <a href="?id_salle=<?= $produit->id_produit ?>&action=details" class="btn btn-success"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
                                                <a href="?id_salle=<?= $produit->id_produit ?>&action=modification" class="btn btn-primary" ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                                <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#suppresionModal<?= $produit->id_produit ?>" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                                            </td>
                                    </tr>
                                    <!-- Modal suppression -->
                                    <div class="modal fade" id="suppresionModal<?= $produit->id_produit ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                                <a href="?id_salle=<?= $salle->id_salle ?>&action=supprimer" class="btn btn-danger">Confirmer</a>
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
    </div>
    <!-- /.wrapper-->

    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="ajoutproduit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Ajout d'une salle</h4>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="date_arrivee">Date d'arrivée</label>
                                    <input type="text" class="form-control" name="date_arrivee" id="date_arrivee" value="" placeholder="00/00/0000 00:00">
                                </div>
                                <div class="form-group">
                                    <label for="date_depart">Date de départ</label>
                                    <input type="text" class="form-control" name="date_depart" id="date_depart" value="" placeholder="00/00/0000 00:00">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="salle">Salle</label>
                                    <select class="form-control" name="salle" id="salle">
                                        <?php while($salle = $salles->fetch(PDO::FETCH_OBJ)): ?>
                                            <option value="<?= $salle->id_salle ?>"><?= $salle->id_salle . ' - ' . $salle->titre . ' - ' . $salle->adresse . ' - ' . $salle->cp . ' - ' . $salle->ville . ' - ' . $salle->capacite . ' pers' ?></option>
                                        <?php endwhile ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="prix">Tarif</label>
                                    <input type="text" class="form-control" name="prix" id="prix" value="" placeholder="prix en euros">
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