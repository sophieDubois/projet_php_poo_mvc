<?php
//CONSTANTES
//fichier contenant les parametres de configuration du site

//création d'une constante contenant la route actuelle
define('ROUTE', request_path());

//emplacement du dossier qui contient les vues du site
define('VIEWS_DIR', __DIR__ . '/../views');