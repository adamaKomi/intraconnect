<?php
session_start();

//  var_dump($_POST);

// // Récupérer les données de rolesCollabo
// $rolesCollabo = json_decode($_POST['rolesCollabo'], true);
// var_dump($rolesCollabo);
//  exit;



//connexion a la base de donnees
require_once ("../includes/bdd-connect.php");



if( isset($_GET['action'] , $_GET['idProjet'], $_GET['idRole']) && $_GET['action'] == 'ajouter-projet-role'){
    $idProjet = $_GET['idProjet'];
    $idRole = $_GET['idRole'];
    //verifier si ce role existe deja pour cet utilisateur
    $stmt = $bdd->prepare("SELECT * FROM projetCollaboRole WHERE idProjet = ? AND idCollabo = ? AND idRole = ?");
    $stmt->execute( [$idProjet, $idCollabo, $idRole] );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(empty($result)){
        //faire l'insertion dans la table projetCollaboRole
        $stmt = $bdd->prepare("INSERT INTO projetCollaboRole (idProjet, idCollabo, idRole) VALUES(?,?,?)");
        $stmt->execute( [$idProjet, $idCollabo, $idRole] );
    }
    header("Location:../pages-projets.php");
    exit;


}elseif (isset($_POST['modifierProjet'])) {
        try {
        //traiter le theme
        if (empty($_POST['nomProjet']) || empty($_POST['description']) /*|| !$_GET['idProjet']*/ ) {
            $_SESSION['nomProjet'] = $_POST['nomProjet'];
            $_SESSION['descriptionProjet'] = $_POST['description'];
            $_SESSION['projetError'] = "Veuillez remplir tous les champs et assigner des roles";
            header("Location:../modifier-projet.php");
            exit;
        }
        //recuperer les information
        $nomPojet = htmlspecialchars($_POST['nomProjet']);
        $description = htmlspecialchars($_POST['description']);
        $idProjet = $_GET['idProjet'];

        // // Récupérer les données de rolesCollabo
        // if($_POST['rolesCollabo'])
        //     $rolesCollabo = json_decode($_POST['rolesCollabo'], true);

        //inserer le projet dans la base de donnees
        $stmt = $bdd->prepare("UPDATE projet SET nomProjet = ?, descriptionProjet = ? WHERE idProjet = ?");
        $stmt->execute([$nomPojet, $description, $idProjet]);


        // //ajouter le projet et les roles definis
        // if(isset($rolesCollabo))
        // {
        //     foreach ($rolesCollabo as $key => $roleCol) {
        //         //verifier si ca existe deja
        //         $stmt = $bdd->prepare("SELECT * FROM projetCollaboRole WHERE idProjet = ? AND idRole = ? AND idCollabo = ?");
        //         $stmt->execute([$idProjet, $roleCol['role'] , $roleCol['collabo'] ]);
        //         $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //         if (count($result) < 1) {// si ca n'existe pas deja on l'ajoute
        //             $stmt = $bdd->prepare("INSERT INTO projetCollaboRole(idProjet, idRole, idCollabo) VALUES(?,?,?)");
        //             $stmt->execute([$idProjet, $roleCol['role'] , $roleCol['collabo'] ]);
        //         }
        //     }
        // }


        //rediriger vers une page
        echo "<script>
                var confirmation = confirm('Le projet a été modifieé avec succès. Allez a la page principale ?');

                if (confirmation) {
                    window.location.href = '../pages-projets.php'; // Rediriger vers la page du profil si l'utilisateur confirme
                } else {
                    window.location.href = '../modifier-projet.php?idProjet=$idProjet'; // Rediriger vers la page d'accueil sinon
                }
        </script>";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
