<?php
session_start();


//se connecter a la base de donnnees
require_once('../includes/bdd-connect.php');

try {



    if (isset($_POST['planifierFormation'])) {
        //traiter le theme
        if (empty($_POST['theme']) || empty($_POST['description']) || empty($_POST['lien'])) {

            $_SESSION['themeFormation'] = $_POST['theme'];
            $_SESSION['description'] = $_POST['description'];
            $_SESSION['dateDebut'] = $_POST['dateDebut'];
            $_SESSION['dateFin'] = $_POST['dateFin'];
            $_SESSION['horaire'] = $_POST['horaire'];
            $_SESSION['lienFormation'] = $_POST['lien'];
            $_SESSION['formateur'] = $_POST['formateur'];
            $_SESSION['formationError'] = "Veuillez remplir tous les champs obligatoires!!!";
            header("Location:../creer-formation.php");
            exit;
        }
        $theme = htmlspecialchars($_POST['theme']);

        //verifier si ce theme existe deja
        $stmt = $bdd->prepare(("SELECT * FROM formation WHERE themeFmt = ?"));
        $stmt->execute([$theme]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            $_SESSION['themeFormation'] = $_POST['theme'];
            $_SESSION['description'] = $_POST['description'];
            $_SESSION['dateDebut'] = $_POST['dateDebut'];
            $_SESSION['dateFin'] = $_POST['dateFin'];
            $_SESSION['horaire'] = $_POST['horaire'];
            $_SESSION['lienFormation'] = $_POST['lien'];
            $_SESSION['formateur'] = $_POST['formateur'];
            $_SESSION['formationError'] = "Erreur : ce theme existe deja, juste une petite modification s'impose";
            header("Location:../creer-formation.php");
            exit;
        }
        //la description, le lien et l'horaire
        $description = htmlspecialchars($_POST['description']);
        $lien = htmlspecialchars($_POST['lien']);
        $horaire = $_POST['horaire'];
        //traiter les dates
        $dateDebut = NULL;
        $dateFin = NULL;
        //date du debut
        if (empty($_POST['dateDebut'])) {
            $_SESSION['themeFormation'] = $_POST['theme'];
            $_SESSION['description'] = $_POST['description'];
            $_SESSION['dateDebut'] = $_POST['dateDebut'];
            $_SESSION['dateFin'] = $_POST['dateFin'];
            $_SESSION['horaire'] = $_POST['horaire'];
            $_SESSION['lienFormation'] = $_POST['lien'];
            $_SESSION['formateur'] = $_POST['formateur'];
            $_SESSION['formationError'] = "Veuillez selectionner une date de debut";
            header("Location:../creer-formation.php");
            exit;
        }
        $dateDebut = $_POST['dateDebut'];
        //date de fin
        if (!empty($_POST['dateFin'])) {
            $dateFin = $_POST['dateFin'];
            //si la date de fin est anterieur a la date de debut
            if (strtotime($dateFin) < strtotime($dateDebut)) {
                $_SESSION['themeFormation'] = $_POST['theme'];
                $_SESSION['description'] = $_POST['description'];
                $_SESSION['dateDebut'] = $_POST['dateDebut'];
                $_SESSION['dateFin'] = $_POST['dateFin'];
                $_SESSION['horaire'] = $_POST['horaire'];
                $_SESSION['lienFormation'] = $_POST['lien'];
                $_SESSION['formateur'] = $_POST['formateur'];
                $_SESSION['formationError'] = "Erreur : la date de debut doit etre anterieure a la date de fin";
                header("Location:../creer-formation.php");
                exit;
            }
        }


        //inserer les donnees dans la base de donnees
        if (!empty($horaire)) { // s'il y a une horaire
            $req = "INSERT INTO formation(themeFmt,descriptionFmt,DateDebut,DateFin,VolumeHoraire,lienFmt) VALUES(?,?,?,?,?,?)";
            $stmt = $bdd->prepare($req);
            $stmt->execute([$theme, $description, $dateDebut, $dateFin, $horaire, $lien]);
        } else {// s'il n'y a pas d'horaires
            $req = "INSERT INTO formation(themeFmt,descriptionFmt,DateDebut,DateFin,lienFmt) VALUES(?,?,?,?,?)";
            $stmt = $bdd->prepare($req);
            $stmt->execute([$theme, $description, $dateDebut, $dateFin, $lien]);
        }

        //recuper l'identifiant de la formation
        $stmt = $bdd->prepare("SELECT idFormation FROM formation WHERE themeFmt = ? AND descriptionFmt = ?");
        $stmt->execute([$theme, $description]);
        $idFormation = $stmt->fetch(PDO::FETCH_ASSOC)['idFormation'];

        // if (isset($_POST['formateurs']) && !empty($idFormation)) {
        //     $formateurs = $_POST['formateurs'];
        //     //enregistrer les formateurs concernes par la formation
        //     foreach ($formateurs as $formateur) {
        //         $stmt = $bdd->prepare("INSERT INTO formateur(idFormateur,idFormation ) VALUES (?,?)");
        //         $stmt->execute([$formateur, $idFormation]);
        //     }
        // }

        //gerer les formateurs
        if (isset($_POST['formateur']) && !empty($_POST['formateur'])) {
            $formateurs = htmlspecialchars($_POST['formateur']);
            $formateurs = explode(",", $formateurs);

            foreach ($formateurs as $key => $formateur) {
                $stmt = $bdd->prepare("INSERT INTO formateur(idFormation, Nom_Prenom ) VALUES (?,?)");
                $stmt->execute([$idFormation, $formateur]);
            }
        }

        //enregistrer celui qui a publier la formation 
        $stmt = $bdd->prepare("INSERT INTO fmtpubliee(idCollabo ,idFormation ) VALUES (?,?)");
        $stmt->execute([$idCollabo, $idFormation]);

        //associer la formation a une annonce
        if (isset($_POST['annonceFormation'])) {

            //pas d'image
            $imageData = NULL;
            $imageType = NULL;
            //inserer les donnees dans la base de donnees
            $stmt = $bdd->prepare("INSERT INTO annonce(idCollabo,titreAnnonce,descriptionAnnonce,date,lien,imageAnnonce,imageAnnonceType) VALUES(?,?,?,?,?,?,?)");
            $stmt->execute([$idCollabo, $theme, $description, $dateFin, $lien, $imageData, $imageType]);
        }
        //rediriger vers une page
        echo "<script>
                alert('Votre formation a été publiée avec succès !!!');
                window.location.href = '../creer-formation.php';                
            </script>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
