<?php
session_start();
//connexion a la base de donnees
require_once ("../includes/bdd-connect.php");


if( isset($_GET['action']) && ($_GET['action']=='supprimer-role-collabo') )
{
    $idRole = $_GET['idRole'];
    $idCollaboRole = $_GET['idCollabo'];
    $idProjet = $_GET['idProjet'];



    try {
        //supprimer -role -collabo
        $stmt = $bdd->prepare("DELETE FROM projetcollaborole WHERE idRole = ? AND idCollabo = ?");
        $stmt->execute([$idRole, $idCollaboRole]);
        header("Location:../modifier-projet.php?idProjet=$idProjet");
        exit;

    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}elseif( isset($_GET['action']) && ($_GET['action']=='supprimer-formation') ){
    $idFormation = $_GET['idFormation'];
    //suppression de la formation
    $stmt = $bdd->prepare("DELETE FROM formation WHERE idFormation= ?");
    $stmt->execute([$idFormation]);
    header("Location:../pages-formations.php");
    exit;
}