<?php

session_start();

// Connexion à la base de données
require_once('../includes/bdd-connect.php');

if (isset($_POST['editPassword'])) {

    try {

        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $renewPassword = $_POST['renewPassword'];


        if (count($collaborateur) > 0) {
            if (!password_verify($currentPassword, $collaborateur['password'])) {
                $_SESSION['profileError']  = "Mot de pass incorrecte";
                $_SESSION['passEntered'] = $currentPassword;
                header("location:../users-profile.php");
                exit;
            }
            if ($newPassword !== $renewPassword) {
                $_SESSION['profileError']  = "Les mots de pass ne sont pas similaires";
                header("location:../users-profile.php");
                exit;
            }
            $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $bdd->prepare("UPDATE collabo SET password=? WHERE idCollabo=?");
            $stmt->execute([$newPassword, $collaborateur['idCollabo']]);
            header("location:../users-profile.php");
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}