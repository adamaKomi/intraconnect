<?php
session_start();


//se connecter a la base de donnnees
require_once('../includes/bdd-connect.php');

try {



    if (isset($_POST['ajouterCompetence'])) {
        //traiter le theme
        if (empty($_POST['nomCompetence'])) {
            $_SESSION['competenceError'] = "Veuillez saisir un nom";
            header("Location:../creer-competence.php");
            exit;
        }
        $nomCompt = htmlspecialchars($_POST['nomCompetence']);

        //verifier si la competence existe deja
        $stmt = $bdd->prepare(("SELECT * FROM competence WHERE nomCompt = ?"));
        $stmt->execute([$nomCompt]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $_SESSION['nomCompetence'] = $_POST['nomCompetence'];
            $_SESSION['competenceError'] = "Erreur : cette competence existe deja, veuillez modifier le nom";
            header("Location:../creer-competence.php");
            exit;
        }



        //inserer les donnees dans la base de donnees

        $stmt = $bdd->prepare("INSERT INTO competence(nomCompt) VALUES(?)");
        $stmt->execute([$nomCompt]);


        //rediriger vers une page
        echo "<script>
                alert('La competence a été ajoutée avec succès !!!');

                    window.location.href = '../creer-competence.php';         
        </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
