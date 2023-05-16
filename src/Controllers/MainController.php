<?php

//espace de nom correspondant a l'emplacement physique du fichier ds le projet(ds le dossier "src")
namespace Controllers;
//importation des classes utilisées
use DateTime;
use Models\DTO\User;
use Models\DAO\UserManager;

//classe contenant tous les controleurs de notre site


class MainController
{

    //controleur de la page d'accueil
    public function home(): void
    {
        //charge la vue "home.php ds le dossier "views""
        require VIEWS_DIR . '/home.php';
    }

    /*controleur de la page d'inscription*/
    public function register(): void
    {

        //TODO: redirigé sur l'accueil si on est déja connecté

        //traitement de formulaire d'inscription
        // Appel des variables
        if(
            isset($_POST['email']) &&
            isset($_POST['password']) &&
            isset($_POST['confirm-password']) &&
            isset($_POST['firstname']) &&
            isset($_POST['lastname'])
        ){

            // Vérifs
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                $errors[] = 'Adresse email invalide';
            }

            if(!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/u', $_POST['password'])){
                $errors[] = 'Mot de passe invalide';
            }

            if($_POST['password'] != $_POST['confirm-password']){
                $errors[] = 'La confirmation ne correspond pas au mot de passe';
            }

            if(mb_strlen($_POST['firstname']) < 2 || mb_strlen($_POST['firstname']) > 50){
                $errors[] = 'Le prénom est invalide (entre 2 et 50 caractères)';
            }

            if(mb_strlen($_POST['lastname']) < 2 || mb_strlen($_POST['lastname']) > 50){
                $errors[] = 'Le nom est invalide (entre 2 et 50 caractères)';
            }

            // Si pas d'erreurs
            if(!isset($errors)){
                //instentiacion du manager des users
                $userManager = new UserManager();
                //verif. si email est deja pris
                $checkUser = $userManager->findOneBy('email', $_POST['email']);

                if(!empty($checkUser)){
                    $errors[]= 'Cette adresse email est déjà utilisée !';
                }else{




                // ...Créer un nouvel utilisateur
                $newUserToInsert = new User();
                //date actuelle (pout hydrater la date d'inscription)
                $today = new DateTime();
                //hydratation
                $newUserToInsert
                    ->setEmail($_POST['email'])
                    ->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT))
                    ->setFirstname($_POST['firstname'])
                    ->setLastname($_POST['lastname'])
                    ->setRegisterDate($today)
                ;


                //on demande au manager de sauvgarder notre nouvel utilisateur ds la BDD
                $userManager ->save($newUserToInsert);
                //message de succes
                $success = 'Votre compte a bien été créé !';
                }
            }

        }

        require VIEWS_DIR . '/register.php';
    }


    //controleur de la page 404
    public function page404(): void
    {
        //modifie le code HTTP pour qu'il soit bien 404 et pas 200
        header('HTTP/1.1 404 Not Found');
        require VIEWS_DIR . '/404.php';
    }

}