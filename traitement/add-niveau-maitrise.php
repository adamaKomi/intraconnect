<?php
session_start();


//se connecter a la base de donnnees
require_once ('../includes/bdd-connect.php');


try {



    if (isset($_POST['definirMaitrise'])) {
        //traiter le theme
        if (empty($_POST['nomMaitrise'])) {
            $_SESSION['maitriseError'] = "Veuillez saisir un nom";
            header("Location:../creer-niveau-maitrise.php");
            exit;
        }
        $nomMt = htmlspecialchars($_POST['nomMaitrise']);

        //verifier si la competence existe deja
        $stmt = $bdd->prepare(("SELECT * FROM nivmaitrise WHERE nomMaitrise = ?"));
        $stmt->execute([$nomMt]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $_SESSION['nomMaitrise'] = $_POST['nomMaitrise'];
            $_SESSION['maitriseError'] = "Erreur : ce niveau de maitrise existe deja, veuillez modifier le nom";
            header("Location:../creer-niveau-maitrise.php");
            exit;
        }



        //inserer les donnees dans la base de donnees

        $stmt = $bdd->prepare("INSERT INTO nivmaitrise(nomMaitrise) VALUES(?)");
        $stmt->execute([$nomMt]);


        //rediriger vers une page
        echo "<script>
                alert('Le niveau de maitrise a été ajouté avec succès !!!');
                window.location.href = '../creer-niveau-maitrise.php'; 
                
        </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
