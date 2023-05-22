<?php

//espace de nom correspondant a l'emplacement physique du fichier ds le projet(ds le dossier "src")
namespace Controllers;
//importation des classes utilisées
use DateTime;
use Models\DAO\FruitManager;
use Models\DTO\Fruit;
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

//redirige sur l'accueil si on est deja connecté
        if(isConnected()){
            header('Location: ' . PUBLIC_PATH . '/');
            die();
        }

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
//controleur de la page de connexion





    public function login():void
    {//TODO rediriger sur l'accueil si on est deja connecté

        //redirige l'utilisateur sur la page de connexion
        if(isConnected()){
            header('Location: ' . PUBLIC_PATH . '/');
            die();
        }

        // Appel des variables
        if(
            isset($_POST['email']) &&
            isset($_POST['password'])
        ) {

            // Vérifs
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Adresse email invalide';
            }

            if (!preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[ !"#\$%&\'()*+,\-.\/:;<=>?@[\\\\\]\^_`{\|}~]).{8,4096}$/u', $_POST['password'])) {
                $errors[] = 'Mot de passe invalide';
            }


            // Si pas d'erreurs
            if(!isset($errors)){

                // instanciation manager des utilisateur
                $userManager = new UserManager();
                //recup du compte correspondent a l'email envoyé ds la formulaire
                $userToConnect = $userManager->FindOneBy('email', $_POST['email']);

               //si le compte n'existe pas
                if(empty($userToConnect)){
                    $errors[] = 'Le compte n\'existe pas.';
                }else{
                    //si le mot de passe est pas bon
                    if(!password_verify($_POST['password'], $userToConnect->getPassword())){
                        $errors[]= 'Le mot de passe n\'est pas le bon mot de passe.';
                    }else{
                        //stockage de l'utilisateur a connecter en session
                        $_SESSION['user'] = $userToConnect;


                        $success = 'Vous êtes bien connecté !';
                    }
                }

            }

        }


        require VIEWS_DIR . '/login.php';
    }
    //controleur de la page de deconnexion


    public function logout():void
    {
        //redirige l'utilisateur sur la page de connexion
        if(!isConnected()){
            header('Location: ' . PUBLIC_PATH . '/connexion/');
            die();
        }


        //suppression de la variable "user"stocké en session (deconnexion)
        unset($_SESSION['user']);

        require VIEWS_DIR . '/logout.php';
    }

    //controleur de la page mon-profil
    public function profil():void
    {
        //redirige l'utilisateur sur la page de connexion
        if(!isConnected()){
            header('Location: ' . PUBLIC_PATH . '/connexion/');
            die();

        }
        //charge la vue "profil.php"
        require VIEWS_DIR . '/profil.php';
    }

//controlleur de la page fruitAdd
    public  function fruitAdd():void
    //redirige l'utilisateur sur la page de connexion si pas connecté
    {        if(!isConnected()){
        header('Location: ' . PUBLIC_PATH . '/connexion/');
        die();

    }

        // Appel des variables
        if(
            isset($_POST['name']) &&
            isset($_POST['color']) &&
            isset($_POST['origin']) &&
            isset($_POST['price-per-kilo']) &&
            isset($_POST['description'])
        ){

            // Vérifs
            if(mb_strlen($_POST['name']) < 2 || mb_strlen($_POST['name']) > 50){
                $errors[] = 'Nom invalide';
            }

            if(mb_strlen($_POST['color']) < 2 || mb_strlen($_POST['color']) > 50){
                $errors[] = 'Couleur invalide';
            }

            if(mb_strlen($_POST['origin']) < 2 || mb_strlen($_POST['origin']) > 50){
                $errors[] = 'Pays d\'origine invalide';
            }

            if(!preg_match('/^[0-9]{1,7}([.,][0-9]{1,2})?$/', $_POST['price-per-kilo'])){
                $errors[] = 'Prix invalide';
            }

            if(mb_strlen($_POST['description']) < 5 || mb_strlen($_POST['description']) > 10000){
                $errors[] = 'Description invalide';
            }

            // Si pas d'erreurs
            if(!isset($errors)){

                // Création du fruit

                $newfruit = new Fruit();


                //hydrater le nouveau fruit
                $newfruit
                    ->setName($_POST['name'])
                    ->setColor($_POST['color'])
                    ->setOrigin($_POST['origin'])
                    //on remplace la virgule par un point sinon problème avec la base de données
                    ->setPricePerKilo( str_replace(',','.', $_POST['price-per-kilo']))
                    ->setUser($_SESSION['user'])
                    ->setDescription($_POST['description'])
                ;

                //recuperation du manager des fruits
                $fruitManager = new FruitManager();

                //sauvegarde du fruit en BDD
                $fruitManager ->save($newfruit);


                // Message de succès
                $success = 'Le fruit a bien été ajouté !';

            }

        }



        //charge la vue fruitAdd.php
        require VIEWS_DIR . '/fruitAdd.php';
    }

    //controleur de la page liste les fruits
    public function fruitList(): void
    {
        //recuperation du manager des fruits
        $fruitManager = new FruitManager();

        //recuperation de tous les fruits ds la base de données
        $fruits =$fruitManager->findAll();




        //charge la vue fruitList ds le dossier views
        require VIEWS_DIR . '/fruitList.php';
    }

    //controleur de la page qui affiche un fruit en detail
    public function fruitDetails(): void
    {
        //recup. du manager des fruits
        $fruitManager = new FruitManager();

        //on recup. le fruit dont l'id est stocké ds url
        $fruit = $fruitManager->findOneBy('id', $_GET['id']);

        //si aucun fruit n'a été trouvé, on affiche la page 404
        if(empty($fruit)){
            $this->page404();
            die();


        }

        //charge la vue fruitDetails.php ds le dossier 'views
        require VIEWS_DIR . '/fruitDetails.php';
    }





    //controleur de la page 404
    public function page404(): void
    {
        //modifie le code HTTP pour qu'il soit bien 404 et pas 200
        header('HTTP/1.1 404 Not Found');
        require VIEWS_DIR . '/404.php';
    }

}
