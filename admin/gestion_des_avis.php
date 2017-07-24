<?php
    require_once("../inc/init.inc.php");

    // ici je récupere les avis
    $avis   = $pdo->query("SELECT avis.id_avis, avis.id_membre, avis.id_salle, avis.commentaire, avis.note, avis.date_enregistrement, membre.email, salle.titre  FROM avis, membre, salle WHERE avis.id_membre = membre.id_membre AND avis.id_salle = salle.id_salle");
    $nb_colone  = $avis->columnCount();

    // ici je recupere l'avis
    if(isset($_GET['action']) && $_GET['action'] == 'modification' && is_numeric($_GET['id_avis']))
    {

        $avis_details = $pdo->prepare("SELECT * FROM avis WHERE id_avis = ?");
        $avis_details->execute([$_GET['id_avis']]);
        $avis_details = $avis_details->fetch(PDO::FETCH_OBJ);
    }

    // mettre en place un controle pour savoir si l'utilisateur veut une suppression d'un avis
    if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_avis']) && is_numeric($_GET['id_avis'])) 
    {
        // is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POST sont toujours de type string)
        // on fait une requete pour récupérer les informations de l'article afin de connaitre la photo pour la supprimer
        $id_avis = $_GET['id_avis'];

        $pdo->prepare("DELETE FROM avis WHERE id_avis = ?")->execute([$id_avis]);

        header('location:gestion_des_avis.php');
    } 

   
    $erreur = false;
    // controle du formulaire
    if(isset($_POST['commentaire']) && isset($_POST['note']))
    {
        $commentaire = $_POST['commentaire'];       
        $note = $_POST['note'];       

        /* si il n'a pas d'erreur on peut enregistrer l'utilisateur*/
        if(!$erreur) {
       
            /* mise a jour du membres*/
            if(isset($_GET['action']) && $_GET['action'] == 'modification' && is_numeric($_GET['id_avis']))
            {
                $id_avis = $_GET['id_avis'];
                $enregistrement = $pdo->prepare("UPDATE avis SET commentaire = ?, note = ? WHERE id_avis = ?");
                $enregistrement->execute([$commentaire, $note, $id_avis]);

                header('location:gestion_des_avis.php');
                exit();

            }
        }
    }

    require("incAdmin/header.inc.php");
?>
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Gestion des Avis
                </h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    

        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Avis
                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-bordered table-hover text-center" id="dataTables-example">
                            <thead>
                                <tr>
                                    <!-- pour  récupérer les nom des colonne-->
                                    <?php for($i = 0 ; $i < $nb_colone; $i++): ?>
                                        <?php if($avis->getColumnMeta($i)['name'] == 'email'): ?>
                                        <?php elseif($avis->getColumnMeta($i)['name'] == 'titre'): ?>
                                        <?php else: ?>
                                        <th><?= $avis->getColumnMeta($i)['name'] ?></th>
                                        <?php endif ?>
                                    <?php endfor ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- récupere les membres dans la bdd-->
                                <?php while($un_avis = $avis->fetch(PDO::FETCH_OBJ)): ?>
                                    <tr>
                                        <?php foreach($un_avis AS $key => $valeur): ?>
                                            <?php if($key == 'email'): ?>
                                            <?php elseif($key == 'titre'): ?>
                                            <?php elseif($key == 'id_membre'): ?>
                                            <td><?= $valeur . ' - ' . $un_avis->email ?></td>
                                            <?php elseif($key == 'id_salle'): ?>
                                            <td><?= $valeur . ' - ' . $un_avis->titre ?></td>
                                            <?php else: ?>
                                            <td><?= $valeur ?></td>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                            <td class="text-center">
                                                <a href="?id_avis=<?= $un_avis->id_avis ?>&action=details" class="btn btn-success"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>
                                                <a href="?id_avis=<?= $un_avis->id_avis ?>&action=modification" class="btn btn-primary" ><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                                                <a href="#" class="btn btn-danger" data-toggle="modal" data-target="#suppresionModal<?= $un_avis->id_avis ?>" ><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                                            </td>
                                    </tr>
                                    <!-- Modal suppression -->
                                    <div class="modal fade" id="suppresionModal<?= $un_avis->id_avis ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="myModalLabel">Supprimer un un avis</h4>
                                            </div>
                                            <div class="modal-body">
                                                Etes Vous sure de vouloir supprimer cette avis?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                                <a href="?id_avis=<?= $un_avis->id_avis ?>&action=supprimer" class="btn btn-danger">Confirmer</a>
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
        <legend>Modification d'un Membre <a href="gestion_des_avis.php" class="btn btn-danger pull-right">X</a></legend>
            <div class="row">
                <div class="col-sm-6">
                    <p><strong>id_avis</strong>     : <?= $avis_details->id_avis ?></p>
                    <p><strong>id_membre</strong>   : <?= $avis_details->id_membre ?></p>
                    <p><strong>id_salle</strong>    : <?= $avis_details->id_salle ?></p>
                    <p><strong>date d'enregistrement</strong>    : <?= $avis_details->date_enregistrement ?></p>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="commentaire">Commentaire</label>
                        <textarea class="form-control" name="commentaire" id="commentaire" cols="30" rows="5"><?= $avis_details->commentaire ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="note">Note</label>
                        <select class="form-control" name="note" id="note">
                        <?php for($i = 0; $i <= 5; $i++): ?>
                            <option <?= ($avis_details->note == $i )? "selected" : null ?>><?= $i ?></option>
                        <?php endfor?>
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

<?php 
    require("incAdmin/footer.inc.php");