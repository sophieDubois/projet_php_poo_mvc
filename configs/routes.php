<?php
//importation et inclusion de la classe "MainController"qui est ds le dossier "src/Controllers"
use Controllers\MainController;



//on instance le controleur général qui contient les controleurs de toutes les pages du site
$mainController = new MainController();


//liste des routes avec leur controleur
//chaque URL correspond à une nouvelle page du site
//"default"est la page par defaut si aucune autre page ne correspond a l'URL demandé (page 404)
switch (ROUTE){

    //route de page d'accueil
    case '/';
        $mainController->home();
    break;

    //Route de la page d'inscription
    case '/creer-un-compte/';
        $mainController->register();
        break;

    //route de la page de connexion
    case '/connexion/';
        $mainController->login();
    break;

    //route de la page de deconnexion
    case '/deconnexion/';
        $mainController->logout();
    break;






    //si aucunes des URL précédentes ne match, c'est la page qui sera appelé par défaut
    default;
        $mainController->page404();
    break;





}