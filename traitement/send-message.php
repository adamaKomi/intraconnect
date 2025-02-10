<?php
session_start();
//se connecter a la base de donnnees
require_once("../includes/bdd-connect.php");

if (isset($_POST['envoyer-message'], $_GET['id_recepteur'])) {

    try {
        
        //les donnees
        $message = $_POST['message'];
        $id_recepteur = $_GET['id_recepteur'];

        //inserer le message dans la base de donnees
        $stmt = $bdd->prepare("INSERT INTO message( id_emetteur,id_recepteur,contenu ) VALUES (?,?,?)");
        $stmt->execute([$idCollabo, $id_recepteur, $message]);
        //rediriger vers la page des messages
        header("Location:../pages-messages.php?id_emetteur=" . $id_recepteur);
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
