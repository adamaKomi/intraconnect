<?php
session_start();

//se connecter a la base de donnnees
require_once('../includes/bdd-connect.php');

try {



    if (isset($_POST['creerConnaissance'])) {
        if (empty($_POST['titre']) || empty($_POST['description']) || empty($_POST['categorie']) || empty($_POST['motCle'])) {
            $_SESSION['titre'] = $_POST['titre'];
            $_SESSION['description'] = $_POST['description'];
            $_SESSION['categorie'] = $_POST['categorie'];
            $_SESSION['motCle'] = $_POST['motCle'];
            $_SESSION['projetError'] = "Veuillez remplir tous les champs et assigner des roles";
            header("Location:../creer-connaisssance.php");
            exit;
        }
        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $idCategorie = $_POST['categorie'];

        if (empty($idCategorie)) {
            $_SESSION['titre'] = $_POST['titre'];
            $_SESSION['description'] = $_POST['description'];
            $_SESSION['categorie'] = $_POST['categorie'];
            $_SESSION['motCle'] = $_POST['motCle'];
            $_SESSION['connaissanceError'] = "Veuillez choisir une categorie!!!";
            header("Location:../creer-connaisssance.php");
            exit;
        }
        $motCles = htmlspecialchars($_POST['motCle']);
        $motCles = explode(",", $motCles);
        if ((count($motCles) < 1) || (count($motCles) > 3)) {
            $_SESSION['titre'] = $_POST['titre'];
            $_SESSION['description'] = $_POST['description'];
            $_SESSION['motCle'] = $_POST['motCle'];
            $_SESSION['connaissanceError'] = "Ajouter des mots-cles separes par des virgules(,) (min 1 et max 3)";
            header("Location:../creer-connaissance.php");
            exit;
        }


        //inserer les donnees dans la base de donnees
        $stmt = $bdd->prepare("INSERT INTO connaissance(idCollabo,idCat,titreConn,descriptionConn) VALUES(?,?,?,?)");
        $stmt->execute([$idCollabo, $idCategorie, $titre, $description]);

        //recuperer l'identifiant de la connaissance publiee
        $stmt = $bdd->prepare("SELECT id FROM connaissance WHERE idCat = ? AND titreConn = ? AND descriptionConn = ? ");
        $stmt->execute([$idCategorie, $titre, $description]);
        $idConnaissance = $stmt->fetch(PDO::FETCH_ASSOC)['id'];



        foreach ($motCles as $mot) {
            //supprimer les espaces au debut du mot
            $mot = ltrim($mot);
            //inserer dans la base
            $stmt = $bdd->prepare("INSERT INTO motcle(idConnaissance,mot) VALUES(?,?)");
            $stmt->execute([$idConnaissance, $mot]);
            
        }
        //rediriger vers une page
        echo "<script>
                var confirmation = alert('Votre publication a été partagée avec succès');
                window.location.href = '../creer-connaissance.php';
                
        </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
