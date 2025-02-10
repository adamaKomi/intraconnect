<?php
session_start();
//se connecter a la base de donnees
require_once("../includes/bdd-connect.php");


if (isset($_GET["newAdmin"]) && !empty($_GET["newAdmin"])) {

    try {
        $newAdmin = $_GET["newAdmin"];
        // ajouter l'utilisateur dans la table des admins s'il n'existe pas deja
        $stmt = $bdd->prepare("  INSERT INTO admin (idCollabo)
                                SELECT ? AS idCollabo
                                WHERE NOT EXISTS (
                                    SELECT 1
                                    FROM admin
                                    WHERE idCollabo = ?
                                )
                            ");
        $stmt->execute([$newAdmin, $newAdmin]);

        //rediriger vers une page
        echo "<script>
                alert('Ce administrateur a été ajouté avec succès!!!!');
                window.location.href = '../nommer-administrateur.php'; 
             </script>";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
