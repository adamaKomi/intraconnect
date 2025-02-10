<?php
session_start();


//se connecter a la base de donnnees
require_once ('../includes/bdd-connect.php');

try {



    if (isset($_POST['definirCategorie'])) {
        //traiter le theme
        if (empty($_POST['nomCategorie'])) {
            $_SESSION['categorieError'] = "Veuillez saisir un nom";
            header("Location:../creer-categorie.php");
            exit;
        }
        $nomCat = htmlspecialchars($_POST['nomCategorie']);

        //verifier si la competence existe deja
        $stmt = $bdd->prepare(("SELECT * FROM categorie WHERE nomCat = ?"));
        $stmt->execute([$nomCat]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $_SESSION['nomCategorie'] = $_POST['nomCategorie'];
            $_SESSION['categorieError'] = "Erreur : cette categorie existe deja, veuillez modifier le nom";
            header("Location:../creer-categorie.php");
            exit;
        }
        
    

        //inserer les donnees dans la base de donnees
        
            $stmt = $bdd->prepare("INSERT INTO categorie(nomCat) VALUES(?)");
            $stmt->execute([$nomCat]);
       
       
        //rediriger vers une page
        echo "<script>
                alert('La categorie a été ajoutée avec succès !!!');
                window.location.href = '../creer-categorie.php';                 
        </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
