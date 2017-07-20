<?php
    function pre($var) {
        echo '<pre>';print_r($var); echo '</pre>';
    }

    function sessionenregistrement($membre, $id) {
        $_SESSION['utilisateur'] = [];
        $_SESSION['utilisateur']['id']          = $id;
        $_SESSION['utilisateur']['pseudo']      = $membre['pseudo'];
        $_SESSION['utilisateur']['nom']         = $membre['nom'];
        $_SESSION['utilisateur']['prenom']      = $membre['prenom'];
        $_SESSION['utilisateur']['email']       = $membre['email'];
        $_SESSION['utilisateur']['civilite']    = $membre['civilite'];
    }