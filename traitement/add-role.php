<?php
session_start();


//se connecter a la base de donnnees
require_once ('../includes/bdd-connect.php');


try {



    if (isset($_POST['ajouterRole'])) {
        //traiter le theme
        if (empty($_POST['nomRole'])) {
            $_SESSION['roleError'] = "Veuillez saisir un nom";
            header("Location:../creer-role.php");
            exit;
        }
        $nomRole = htmlspecialchars($_POST['nomRole']);

        //verifier si le role existe deja
        $stmt = $bdd->prepare(("SELECT * FROM role WHERE nomRole = ?"));
        $stmt->execute([$nomRole]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $_SESSION['nomRole'] = $_POST['nomRole'];
            $_SESSION['roleError'] = "Erreur : ce role existe deja, veuillez modifier le nom";
            header("Location:../creer-role.php");
            exit;
        }
        
    

        //inserer les donnees dans la base de donnees
        
            $stmt = $bdd->prepare("INSERT INTO role(nomRole) VALUES(?)");
            $stmt->execute([$nomRole]);
       
       
        //rediriger vers une page
        echo "<script>
                alert('Le role a été definie avec succès. Allez a la page principale ?');
                window.location.href = '../creer-role.php';                 
            </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
