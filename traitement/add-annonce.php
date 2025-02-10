<?php
session_start();


//se connecter a la base de donnnees
require_once('../includes/bdd-connect.php');

try {



    if (isset($_POST['creerAnnonce'])) {
        if (empty($_POST['titre']) || empty($_POST['description'])) {
            $_SESSION['titreAnnonce'] = $_POST['titre'];
            $_SESSION['descriptionAnnonce'] = $_POST['description'];
            $_SESSION['lienAnnonce'] = $_POST['lien'];
            $_SESSION['dateAnnonce'] = $_POST['date'];
            $_SESSION['imageAnnonce'] = $_FILES['image']['tmp_name'];
            $_SESSION['annonceError'] = "Veuillez remplir tous les champs requis";
            header("Location:../creer-annonce.php");
            exit;
        }
        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $lien = htmlspecialchars($_POST['lien']);
        $imageData = NULL;
        $imageType = NULL;
        $date = NULL;

        if (!empty($_POST['date'])) {
            $date = htmlspecialchars($_POST['date']);
        }

        //recuperer l'image
        if ($_FILES['image']['error'] === 0) {

            $maxFileSize = 10 * 1024 * 1024; // Limite de taille maximale en octets (ici 10 Mo)

            if ($_FILES['image']['size'] > $maxFileSize) {
                // La taille du fichier est superieure à la limite maximale
                $_SESSION['annonceError'] = "Erreur: la taille du fichier est superieure à la limite maximale (Max 10 Mo)";
                $_SESSION['titreAnnonce'] = $_POST['titre'];
                $_SESSION['descriptionAnnonce'] = $_POST['description'];
                $_SESSION['lienAnnonce'] = $_POST['lien'];
                $_SESSION['dateAnnonce'] = $_POST['date'];
                $_SESSION['imageAnnonce'] = $_FILES['image']['tmp_name'];
                //repartir dans la page de creation de publication
                header("location:../creer-annonce.php");
                exit;
            }

            $imageData = file_get_contents($_FILES['image']['tmp_name']);
            $imageType = $_FILES['image']['type'];
            //si le format de l'image n'est pas reconnu
            if (($imageType != 'image/jpeg') && ($imageType != 'image/png')  && ($imageType != 'image/gif')) {
                $_SESSION['annonceError'] = "Erreur: les formats d'images autorisés : jpeg/png/gif";
                $_SESSION['titreAnnonce'] = $_POST['titre'];
                $_SESSION['descriptionAnnonce'] = $_POST['description'];
                $_SESSION['lienAnnonce'] = $_POST['lien'];
                $_SESSION['dateAnnonce'] = $_POST['date'];
                $_SESSION['imageAnnonce'] = $_FILES['image']['tmp_name'];
                //repartir dans la page de creation de publication
                header("location:../creer-annonce.php");
                exit;
            }
        }

        //inserer les donnees dans la base de donnees
        $stmt = $bdd->prepare("INSERT INTO annonce(idCollabo,titreAnnonce,descriptionAnnonce,date,lien,imageAnnonce,imageAnnonceType) VALUES(?,?,?,?,?,?,?)");
        $stmt->execute([$idCollabo, $titre, $description, $date, $lien, $imageData, $imageType]);
        //rediriger vers une page
        echo "<script>
                alert('Votre annonce a été publiée avec succès !!!');
                window.location.href = '../creer-annonce.php'; 
               
        </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
