<?php
session_start();
//se connecter a la base de donnnees
require_once ("../includes/bdd-connect.php");

// var_dump($_POST);

// // Récupérer les données de rolesCollabo
// $rolesCollabo = json_decode($_POST['rolesCollabo'], true);
// var_dump($rolesCollabo);
// exit;





try {

    if (isset($_POST['ajouterProjet'])) {
        //traiter le theme
        if (empty($_POST['nomPojet']) || empty($_POST['description']) /* || !$_POST['rolesCollabo']*/ ) {
            $_SESSION['nomProjet'] = $_POST['nomPojet'];
            $_SESSION['descriptionProjet'] = $_POST['description'];
            $_SESSION['dateFin'] = $_POST['dateFin'];
            $_SESSION['projetError'] = "Veuillez remplir tous les champs et assigner des roles";
            header("Location:../creer-projet.php");
            exit;
        }
        $nomPojet = htmlspecialchars($_POST['nomPojet']);
        //verifier si ce nom existe deja
        $stmt = $bdd->prepare(("SELECT * FROM projet WHERE nomProjet = ?"));
        $stmt->execute([$nomPojet]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $_SESSION['nomProjet'] = $nomPojet;
            $_SESSION['descriptionProjet'] = $_POST['description'];
            $_SESSION['dateFin'] = $_POST['dateFin'];
            $_SESSION['projetError'] = "Erreur : Ce nom existe deja, veuillez modifier";
            header("Location:../creer-projet.php");
            exit;
        }
        $description = htmlspecialchars($_POST['description']);
        $dateFin = $_POST['dateFin'];

        // // Récupérer les données de rolesCollabo
        // $rolesCollabo = json_decode($_POST['rolesCollabo'], true);



        //inserer le projet dans la base de donnees
        $stmt = $bdd->prepare("INSERT INTO projet(nomProjet,descriptionProjet,dateFin) VALUES(?,?,?)");
        $stmt->execute([$nomPojet, $description, $dateFin]);

        // //recupere l'identifiant du projet ajouté
        // $stmt = $bdd->prepare("SELECT * FROM projet WHERE nomProjet = ? AND descriptionProjet = ?");
        // $stmt->execute([$nomPojet, $description]);
        // $idProjet = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['idProjet'];

        // //ajouter le projet et les roles definis
        // foreach ($rolesCollabo as $key => $roleCol) {
        //     //verifier si ca existe deja
        //     $stmt = $bdd->prepare("SELECT * FROM projetCollaboRole WHERE idProjet = ? AND idRole = ? AND idCollabo = ?");
        //     $stmt->execute([$idProjet, $roleCol['role'] , $roleCol['collabo'] ]);
        //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //     if (count($result) < 1) {// si ca n'existe pas deja on l'ajoute
        //         $stmt = $bdd->prepare("INSERT INTO projetCollaboRole(idProjet, idRole, idCollabo) VALUES(?,?,?)");
        //         $stmt->execute([$idProjet, $roleCol['role'] , $roleCol['collabo'] ]);
        //     }
        // }

        //associer la formation a une annonce
        if (isset($_POST['annonceProjet'])) {

            //pas d'image
            $imageData = NULL;
            $imageType = NULL;
            //pas de date et pas de lien
            $lien = NULL;
            //inserer les donnees dans la base de donnees
            $stmt = $bdd->prepare("INSERT INTO annonce(idCollabo,titreAnnonce,descriptionAnnonce,date,lien,imageAnnonce,imageAnnonceType) VALUES(?,?,?,?,?,?,?)");
            $stmt->execute([$idCollabo, $nomPojet, $description, $dateFin, $lien, $imageData, $imageType]);
        }

        //rediriger vers une page
        echo "<script>
                var confirmation = confirm('Le projet a été ajouté avec succès. Allez a la page principale ?');

                if (confirmation) {
                    window.location.href = '../pages-projets.php'; // Rediriger vers la page du profil si l'utilisateur confirme
                } else {
                    window.location.href = '../creer-projet.php'; // Rediriger vers la page d'accueil sinon
                }
        </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
