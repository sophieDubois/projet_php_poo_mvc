<?php


//ce fichier est le controleur frontal, qui chargera tout le site web et ses composants

//inclusion des vendors installées avec composer
require __DIR__ . '/../vendor/autoload.php';

//inclusion du fichier contenant les fonctions et configurations générales du site
require __DIR__ . '/../configs/functions.php';

//inclusion du fichier contenant les parametres personnalisables du site(comme les acces BDD par exemple)
require __DIR__ . '/../configs/params.php';

try{
    
    //inclusion du fichier qui contient toutes les routes (URLs du site et qui chargera le controleur de chaque route
    require __DIR__ . '/../configs/routes.php';

} catch(Throwable $e){

    // Affichage personnalisé sympa de nos erreurs PHP
    ?>

    <div style="background-color: #FFA2A2; padding: 15px;">
        <h1><b>Erreur PHP !</b></h1>
        <hr>
        <p><b>Fichier</b> : <?= $e->getFile() ?></p>
        <p><b>Ligne</b> : <?= $e->getLine() ?></p>
        <p><b>Message</b> : <?= $e->getMessage() ?></p>
        <hr>
        <?php

        // Affichage de la pile d'erreur dans un dump au cas où on aurait besoin de détails sur l'erreur affichée
        dump( $e->getTrace() );

        ?>
    </div>

    <?php

}