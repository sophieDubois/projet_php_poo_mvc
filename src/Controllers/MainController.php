<?php

//espace de nom correspondant a l'emplacement physique du fichier ds le projet(ds le dossier "src")
namespace Controllers;


//classe contenant tous les controleurs de notre site
class MainController
{

    //controleur de la page d'accueil
    public function home(): void
    {
        //charge la vue "home.php ds le dossier "views""
        require VIEWS_DIR . '/home.php';
    }


    //controleur de la page 404
    public function page404(): void
    {
        require VIEWS_DIR . '/404.php';
    }

}