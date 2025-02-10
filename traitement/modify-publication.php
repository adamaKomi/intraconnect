<?php
session_start();

//se connecter a la base de donnnees
require_once("../includes/bdd-connect.php");

if (isset($_POST['modifer-publication'], $_GET['idPub'])) {
    try {
        //verifier si le titre et la description son bien saisis
        if (empty($_POST['titre']) || empty($_POST['description'])) {
            $_SESSION['pubError'] = "Veuillez remplir tous les champs obligatoires";
            $_SESSION['titrePub'] = $_POST['titre'];
            $_SESSION['descPub'] = $_POST['description'];
            header("Location:../modifier-publication.php");
            exit();
        }
        //recuperer les informations
        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $idPub = $_GET['idPub'];

        //traiter le cas ou un projet a ete selectionné
        $idProjet = NULL;
        if (isset($_POST['idProjet']) && !empty($_POST['idProjet'])) {
            //recuperer l'identifiant du projet
            $idProjet = $_POST['idProjet'];
        }


        //gerer le status de la publication
        $statut = "Nouveau";

        //inserer les informations de la publication dans la table pub
        $stmt = $bdd->prepare("UPDATE pub SET idCollabo = ?, idProjet = ?, titrePub = ?, descriptionPub = ?, statutPub=? WHERE idPub = ?");
        $stmt->execute([$idCollabo, $idProjet, $titre, $description, $statut, $idPub]);
        echo "<script>
                var confirmation = confirm('Votre publication a été publiée avec succès. Allez a la page principale ?');

                if (confirmation) {
                    window.location.href = '../pages-publications.php'; // Rediriger vers la page du profil si l'utilisateur confirme
                } else {
                    window.location.href = '../modifier-publication.php'; // Rediriger vers la page d'accueil sinon
                }
        </script>";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} elseif (isset($_GET['action'], $_GET['idPub'], $_GET['idAuteurPub']) && $_GET['action'] == 'marquer-question-comme-resolu') {

    try {
        $idAuteurPub = $_GET['idAuteurPub'];
        $idPub = $_GET['idPub'];

        // mis a jour du statut de la publication
        $stmt = $bdd->prepare("UPDATE pub SET statutPub = 'resolu' WHERE idPub = ?");
        $stmt->execute([$idPub]);

        //rediriger l'utilisateur
        header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$idAuteurPub");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} elseif (isset($_GET['action'], $_GET['idPub'], $_GET['idAuteurPub']) && $_GET['action'] == 'annuler-question') {
    try {
        $idAuteurPub = $_GET['idAuteurPub'];
        $idPub = $_GET['idPub'];

        // mis a jour du statut de la publication
        $stmt = $bdd->prepare("UPDATE pub SET statutPub = 'annulé' WHERE idPub = ?");
        $stmt->execute([$idPub]);

        //rediriger l'utilisateur
        header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$idAuteurPub");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
} elseif (isset($_GET['action'], $_GET['idPub'], $_GET['idAuteurPub']) && $_GET['action'] == 'relancer-question') {
    try {
        $idAuteurPub = $_GET['idAuteurPub'];
        $idPub = $_GET['idPub'];

        // mis a jour du statut de la publication
        $stmt = $bdd->prepare("UPDATE pub SET statutPub = 'relancé' WHERE idPub = ?");
        $stmt->execute([$idPub]);

        //rediriger l'utilisateur
        header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$idAuteurPub");
        exit;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
