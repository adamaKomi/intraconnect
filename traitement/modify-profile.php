<?php
session_start();

//connexion a la base de donnees
require_once("../includes/bdd-connect.php");

// var_dump($_POST);
//         exit;

//cas de la suppression de l'image de profil

if (isset($_GET['action']) && $_GET['action'] === "supprimer-image-profile") {
    $stmt = $bdd->prepare("UPDATE collabo SET imageProfil=NULL, imageProfilType=NULL WHERE idCollabo = ?");
    $stmt->execute([$_GET['idCollabo']]);
    header("location:../users-profile.php");
    exit;
}



if (isset($_POST['editInfos'])) {


    //modification des modifications du profil
    // $fullName = trim($_POST['fullName']); //suprimmer les espaces au debut
    // $fname = explode(" ", $fullName);
    $prenom = htmlspecialchars($_POST['Firstname']);
    $nom = htmlspecialchars($_POST['Lastname']);
    $username = htmlspecialchars($_POST['username']);
    $about = htmlspecialchars($_POST['about']);
    $job = htmlspecialchars($_POST['job']);
    $email = htmlspecialchars($_POST['email']);
    $facebook = htmlspecialchars($_POST['facebook']);
    $twitter = htmlspecialchars($_POST['twitter']);
    $instagram = htmlspecialchars($_POST['instagram']);
    $linkedin = htmlspecialchars($_POST['linkedin']);

    try {


        //recuperer le collaborateur dans la tables colcompmt
        $stmt = $bdd->prepare("SELECT * FROM colcompmt WHERE idCollabo=?");
        $stmt->execute([$idCollabo]);
        $colComptMaitrise = $stmt->fetch(PDO::FETCH_ASSOC);

        // //recuperer l'ancienne image
        // if ($collaborateur['imageProfil']) {
        //     $imageData = $collaborateur['profileimage'];
        //     $imageType = $collaborateur['imageProfilType'];
        // }


        $regExp = '/^\s*$/';
        //verifier si le nom est complet
        if (preg_match($regExp, $prenom) || preg_match($regExp, $nom)) {
            $_SESSION['profileError'] = "Le nom n'est pas complet, veuillez saisir le prenom et le nom";
            $_SESSION['incompleteFirstname'] = $prenom;
            $_SESSION['incompleteLastname'] = $nom;
            header("location:../users-profile.php#editProfile");
            exit;
        }


        //traiter le nom d'utilisateur
        if ($username) {
            //verifier si le nom d'utilisateur existe deja 
            $users = $bdd->prepare("SELECT username FROM collabo where username=? AND idCollabo !=?");
            $users->execute([$username, $idCollabo]);
            $results = $users->fetchAll(PDO::FETCH_ASSOC);
            if ($results) { //existe deja
                $_SESSION['usernameExist'] = $username;
                $_SESSION['profileError'] = "Ce username existe dejà";
                header("location:../users-profile.php#editProfile");
                exit;
            }
        }






        //gerer la photo de profile
        // Vérifie si un fichier a été téléchargé
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $file = $_FILES['image'];
            //la taille 
            // Obtenez la taille du fichier en octets
            $imageSize = $_FILES['image']['size'];
            $fileSizeKB = $imageSize / 1024;
            $imageSize = $fileSizeKB / 1024;
            // var_dump($imageSize);
            // exit;
            // Vérifie s'il n'y a pas d'erreur lors du téléchargement
            // Lit le contenu du fichier téléchargé
            $imageData = file_get_contents($file['tmp_name']);
            $imageType = $file['type']; // type MIME de l'image 
            //si le format de l'image n'est pas reconnu

            if (($imageType != 'image/jpeg') && ($imageType != 'image/png')  && ($imageType != 'image/gif')) {
                $_SESSION['profileError'] = "Les formats d'images autorisés : jpeg/png/gif";
                //repartir dans la page de creation de publication
                header("location:../users-profile.php");
                exit;
            }
        }

        if (isset($imageData)) {
            //ajouter les donnees dans la bdd
            $req = "UPDATE collabo SET nom=?, prenom=?, username=?, email=?,imageProfil=?,imageProfilType=?,job=?,about=?,facebook=?,twitter=?,instagram=?,linkedin=? WHERE idCollabo=?";
            $stmt = $bdd->prepare($req);
            $stmt->execute([$nom, $prenom, $username, $email, $imageData, $imageType, $job, $about, $facebook, $twitter, $instagram, $linkedin, $idCollabo]);
        } else {
            //ajouter les donnees dans la bdd
            $req = "UPDATE collabo SET nom=?, prenom=?, username=?, email=?,job=?,about=?,facebook=?,twitter=?,instagram=?,linkedin=? WHERE idCollabo=?";
            $stmt = $bdd->prepare($req);
            $stmt->execute([$nom, $prenom, $username, $email, $job, $about, $facebook, $twitter, $instagram, $linkedin, $idCollabo]);
        }




        //traiter les competences et les niveaux de maitrise
        if (isset($_POST['competences'])) {

            // Récupérer les compétences et les niveaux depuis les tableaux $_POST
            $competences = $_POST['competences'];
            $niveaux = $_POST['niveaux'];

            // Préparation des requêtes pour récupérer l'identifiant de la compétence et du niveau
            $stmtComp = $bdd->prepare("SELECT idCompt FROM competence WHERE nomCompt=:nomComp");
            $stmtMaitrise = $bdd->prepare("SELECT idMaitrise FROM nivmaitrise WHERE nomMaitrise=:nomM");

            // Insertion ou mise à jour des compétences et niveaux de maîtrise
            foreach ($competences as $competence) {
                if (isset($niveaux[$competence])) {
                    $niveau = $niveaux[$competence];

                    // Exécution de la requête pour récupérer l'identifiant de la compétence
                    $stmtComp->execute(["nomComp" => $competence]);
                    $idCompResult = $stmtComp->fetch(PDO::FETCH_ASSOC);
                    $idComp = $idCompResult['idCompt'];

                    // Exécution de la requête pour récupérer l'identifiant du niveau de compétence
                    $stmtMaitrise->execute(["nomM" => $niveau]);
                    $idMaitriseResult = $stmtMaitrise->fetch(PDO::FETCH_ASSOC);
                    $idMaitrise = $idMaitriseResult['idMaitrise'];

                    // Vérification des résultats
                    if ($idCompResult && $idMaitriseResult) {

                        // Vérification si la compétence existe pour ce collaborateur
                        $stmtCheck = $bdd->prepare("SELECT * FROM colcompmt WHERE idCollabo=? AND idCompetence=?");
                        $stmtCheck->execute([$idCollabo, $idComp]);
                        $existingData = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                        if (!$existingData) {
                            // Insertion des données dans la table colcompmt
                            $stmtInsert = $bdd->prepare("INSERT INTO colcompmt(idCollabo, idCompetence, idMaitrise) VALUES (?, ?, ?)");
                            $stmtInsert->execute([$idCollabo, $idComp, $idMaitrise]);
                        } else {
                            // Mise à jour des données dans la table colcompmt
                            $stmtUpdate = $bdd->prepare("UPDATE colcompmt SET idMaitrise=? WHERE idCollabo=? AND idCompetence=?");
                            $stmtUpdate->execute([$idMaitrise, $idCollabo, $idComp]);
                        }
                        //mettre a jour la variable d'authenfication
                        if (!empty($username))
                            $_SESSION['auth'] = $username;
                        //repartir dans le profil
                        header("location:../users-profile.php#editProfile");
                    } else {
                        echo "Erreur lors de la récupération des IDs pour la compétence: $competence et le niveau: $niveau <br>";
                    }
                }
            }
        }

        //mettre a jour la variable d'authenfication
        if (!empty($username))
            $_SESSION['auth'] = $username;
        //repartir dans le profil
        header("location:../users-profile.php");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
