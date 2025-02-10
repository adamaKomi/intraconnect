<?php
session_start();
// var_dump($_POST);
// exit;
//se connecter a la base de donnnees
require_once ("../includes/bdd-connect.php");

if (isset($_POST['envoyer-commentaire'])) {
        try {
                //recuperer l'identifiant de la publication
                $idPub = $_GET['idPub'];
                $idCollaboQuestion = $_GET['idCollaboQuestion'];
                //si c'est un commentaire vide on revient sur la publication
                if (empty($_POST['commentaire'])) {

                        // if(isset($_GET['action']) && ($_GET['action']=='voir-plus-reponse')){
                        //         $idCollaboQuestion = $_GET['idCollaboQuestion'];
                        //         header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$idCollaboQuestion");
                        //         exit;
                        // }
                        // header("location:../pages-publications.php#pointer-sur$idPub");
                        header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$idCollaboQuestion");
                        exit;
                }
                //recuperer le commentaire
                $commentaire = $_POST['commentaire'];
                
                //inserer dans la base de données
                $stmt = $bdd->prepare("INSERT INTO reponse(idCollabo,idPub,reponse) VALUES(?,?,?)");
                $stmt->execute([$idCollabo,$idPub,$commentaire]);
                //revenir sur la publication
                // if(isset($_GET['action']) && ($_GET['action']=='voir-plus-reponse')){
                //         $idCollaboQuestion = $_GET['idCollaboQuestion'];
                //         header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$idCollaboQuestion");
                //         exit;
                // }
                // header("location:../pages-publications.php#pointer-sur$idPub");
                header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$idCollaboQuestion");
                exit;





        } catch (PDOException $e) {
                echo "Erreur, impossible de se connecter à la base de données : " . $e->getMessage();
        }
}

if(isset($_POST['repondre-commentaire']))
{
        try {
                //recuperer l'identifiant de la publication
                $idPub = $_GET['idPub'];
                //auteur de la publication
                $id_auteur_pub = $_GET['AuteurQuestion'];
                //auteur du commentaire sur lequel on veut repondre
                $idAuteurCommentaire = $_GET['idAuteurCommentaire'];
                //identifiant du commentaire sur lequel on veut repondre
                $idCommentaire = $_GET['idRep'];
                //le commentaire sur lequel on veut repondre
                $commentaire = $_GET['reponse'];
                
                //si c'est un commentaire vide on revient sur la publication
                if (empty($_POST['commentaire'])) {
                        
                        header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$id_auteur_pub");
                        exit;
                }
                //recuperer la reponse au commentaire
                $repCommentaire = $_POST['commentaire'];

                
                //inserer dans la base de données
                $stmt = $bdd->prepare("INSERT INTO reponse(idCollabo,idPub,reponse) VALUES(?,?,?)");
                $stmt->execute([$idCollabo,$idPub,$repCommentaire]);

                //recuperer la reponse qui vient d'etre enregistrer dans la bdd
                $stmt = $bdd->prepare("SELECT idRep FROM reponse WHERE idCollabo = ? AND idPub = ? AND reponse = ?");
                $stmt->execute([$idCollabo,$idPub,$repCommentaire]);
                $idNewRep = $stmt->fetch(PDO::FETCH_ASSOC)['idRep'];


                //inserer la reponse au commentaire dans la base
                $stmt = $bdd->prepare("INSERT INTO reponseCommentaire(idCommentaire,idAuteurCommentaire,idRepCommentaire,idPub,commentaire) VALUES(?,?,?,?,?)");
                $stmt->execute([$idCommentaire,$idAuteurCommentaire,$idNewRep,$idPub,$commentaire]);

                //revenir sur la publication               
                header("location:../voir-plus-reponse.php?idPub=$idPub&idCollabo=$id_auteur_pub");
                exit;



















        } catch (PDOException $e) {
                echo "Erreur, impossible de se connecter à la base de données : " . $e->getMessage();
        }

}
