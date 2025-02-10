<?php
session_start();

//se connecter a la base de donnnees
require_once ('../includes/bdd-connect.php');


if (isset($_POST['publier'])) {
    //verifier si le titre et la description son bien saisis
    if (empty($_POST['titre']) || empty($_POST['description'])) {
        $_SESSION['pubError'] = "Veuillez remplir tous les champs obligatoires";
        $_SESSION['titrePub'] = $_POST['titre'];
        $_SESSION['descPub'] = $_POST['description'];
        header("Location:../creer-publication.php");
        exit();
    }
    //recuperer les informations
    $titre = $_POST['titre'];
    $description = $_POST['description'];

    //traiter le cas ou un projet a ete selectionné
    $idProjet = NULL;
    if (isset($_POST['monProjet']) && !empty($_POST['monProjet'])) {
        $monProjet = $_POST['monProjet'];
        //recuperer l'identifiant du projet
        $stmt = $bdd->prepare("SELECT idProjet FROM projet WHERE nomProjet=?");
        $stmt->execute([$monProjet]);
        $idProjet = $stmt->fetch(PDO::FETCH_ASSOC)['idProjet'];
    }

    //gerer l'image de la publication
    //initialisation des variables de l'image a NULL
    // $imageData = NULL;
    // $imageType = NULL;
    // Vérifie si une photo a été téléchargée
    // if (isset($_FILES['image']) &&$_FILES['image']['error'] === 0) {
    //     $file = $_FILES['image'];
    //     // Vérifie s'il n'y a pas d'erreur lors du téléchargement
    //     // Lit le contenu du fichier téléchargé
    //     $imageData = file_get_contents($file['tmp_name']);
    //     $imageType = $file['type']; // type MIME de l'image 
    //     //si le format de l'image n'est pas reconnu
    //     if (($imageType != 'image/jpeg') && ($imageType != 'image/png')  && ($imageType != 'image/gif')) {
    //         $_SESSION['pubError'] = "Les formats d'images autorisés : jpeg/png/gif";
    //         $_SESSION['titrePub'] = $_POST['titre'];
    //         $_SESSION['descPub'] = $_POST['description'];
    //         //repartir dans la page de creation de publication
    //         header("location:../creer-publication.php");
    //         exit;
    //     }
    // }

    

    //inserer les informations de la publication dans la table pub
    $stmt = $bdd->prepare("INSERT INTO pub(idCollabo,idProjet,titrePub,descriptionPub) VALUE(?,?,?,?)");
    $stmt->execute([$idCollabo, $idProjet, $titre, $description]);
    echo "<script>
                alert('Votre publication a été publiée avec succès !!!');
                window.location.href = '../creer-publication.php';
        </script>";
}
