<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");


//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");
include_once("includes/fonctions.php");

try {



    if ($collaborateur['imageProfil']) {
        // Afficher l'image
        $imageData = base64_encode($collaborateur['imageProfil']); // Convertir les données de l'image en base64
        $imageType = $collaborateur['imageProfilType']; // Récupérer le type de l'image
        $srcProfil = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image
    }


    //recuperer les projets

    //le cas ou on vient d'une notification
    if (isset($_GET['idProjet'], $_GET['idNotification'])) {
        $id_Projet = $_GET['idProjet'];
        $idNotification = $_GET['idNotification'];
        //projet concerné par la notification
        $stmt = $bdd->prepare("SELECT * FROM projet WHERE idProjet = ?");
        $stmt->execute([$id_Projet]);
        $projetNotification = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($_SESSION['admin'])) { //si c'est un administrateur on recupere tous les projets
            $stmt = $bdd->prepare("SELECT * FROM projet WHERE idProjet != ? ORDER BY DateFin DESC");
            $stmt->execute([$id_Projet]);
            $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else { //sinon on recupere seulement les projets dans lesquels il participe 
            $stmt = $bdd->prepare(" SELECT p.* FROM projet p
                                    JOIN projetCollaboRole pc ON p.idProjet = pc.idProjet
                                    WHERE pc.idCollabo = ? AND p.idProjet != ?
                                    ORDER BY p.DateFin DESC
                            ");
            $stmt->execute([$idCollabo, $id_Projet]);
            $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //marquer la notification comme lue
        $stmt = $bdd->prepare("UPDATE notificationCollabo SET statut = 0 WHERE idNotification = ? AND idCollabo = ?");
        $stmt->execute([$idNotification, $idCollabo]);
    } else { //cas normal 
        if (isset($_SESSION['admin'])) { //si c'est un administrateur on recupere tous les projets
            $stmt = $bdd->prepare("SELECT * FROM projet ORDER BY DateFin DESC");
            $stmt->execute();
            $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else { //sinon on recupere seulement les projets dans lesquels il participe 
            $stmt = $bdd->prepare(" SELECT p.* FROM projet p
                                    JOIN projetCollaboRole pc ON p.idProjet = pc.idProjet
                                    WHERE pc.idCollabo = ?
                                    ORDER BY p.DateFin DESC
                            ");
            $stmt->execute([$idCollabo]);
            $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    //tous les projets pour afficher dans la liste de choix
    $stmt = $bdd->prepare("SELECT * FROM projet WHERE statut!='complété' ");
    $stmt->execute();
    $tousProjets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //recuperer les roles
    $stmt = $bdd->prepare("SELECT * FROM role");
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}





?>

<!doctype html>
<html lang="En">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Inclure les fichier css -->
    <?php require_once("includes/fichiers-css.php"); ?>
    <!-- fichier css pour le carousel de l'annonce -->
    <link rel="stylesheet" href="includes/annonces.css">

    <title>Social Community</title>

    <link rel="icon" type="image/png" href="assets/vendor/post/images/favicon.png">



    <style>
        /* 
            choix des projets
        */

        .role-projet {
            display: flex !important;
            width: 50% !important;
            flex-direction: row !important;
            justify-content: space-around !important;
            margin: auto !important;
        }

        .role-projet select:nth-child(1) {
            margin-right: 20px !important;
        }

        /* 
            #la page contenant les projets 
        
        */
        .content-page-box-area {
            margin: auto !important;
        }

        .main-content-wrapper {
            padding-right: 0 !important;
        }

        .main-content-wrapper:nth-child(1) {
            /* width: 100% !important; */
            margin-right: 50px !important;
        }

        .main-content-wrapper:nth-child(2) {
            /* width: 30% !important; */
            margin: auto !important;
        }

        .news-feed-area .news-feed {
            margin-bottom: 100px !important;
        }

        .post-header .nomProjet a {
            color: white !important;
        }

        .post-body {
            display: flex;
            flex-direction: column;
        }

        .post-body div:nth-child(1) {
            margin-right: 20px;
        }

        .post-body .description {
            font-size: 18px !important;
            color: black !important;
        }

        .post-body .post-meta-wrap li {
            width: fit-content !important;
            padding: 5px !important;
            margin: 0 0 0 50px !important;
        }

        .post-meta-wrap label {
            margin: 0 !important;
            font-size: 18px !important;
            color: blue !important;
        }

        .post-meta-wrap li p {
            margin: 0 !important;
            font-size: 16px !important;
        }

        .projetNotification {
            border: 3px solid red !important;
        }
        .en-cours,.complete{            
            color: white;
            padding: 2px 5px;
            border-radius: 5px;
        }
        .en-cours{
            background-color: green;
        }
        .complete{
            background-color: red;
        }
    </style>


</head>

<body>


    <!-- ======= Header ======= -->
    <?php require_once("includes/main-header.php") ?>

    <!-- ======= Sidebar ======= -->
    <?php include_once("includes/main-sidebar.php") ?>


    <main id="main" class="main">

        <div class="pagetitle">
            <h1 style="text-align: start !important;">Projets</h1>
            
        </div><!-- End Page Title -->
        <?php include_once('includes/annonces-carrousel.php'); ?>
        <section class="section profile">

            <div class="main-content-wrapper d-flex flex-row justify-content-between row" style="display: flex !important;flex-direction: row !important; justify-content: space-between !important;">
                <?php if ((count($tousProjets) > 0 && count($roles) > 0) || isset($_SESSION['admin'])) : ?>
                    <div class="col-12">
                        <?php if (isset($_SESSION['admin'])) : ?>
                            <p class="text-center">
                                <a href="creer-projet.php" class="btn btn-primary w-50 h-100"><i class="bi bi-folder-plus mr-1"></i> Ajouter un projet</a>
                            </p>
                        <?php endif; ?>
                        <?php if (count($tousProjets) > 0 && count($roles) > 0) : ?>
                            <div class="text-center">
                                <h5>Renseigner vos projets</h5>
                                <div class="role-projet">
                                    <!-- afficher les projets -->
                                    <select name="un-projet" id="" class="liste-projets mon-projet form-select form-select-sm mb-3" aria-label="Small select example">
                                        <option value="" selected>--Choisir un projet--</option>
                                        <?php foreach ($tousProjets as $key => $leProjet) : ?>
                                            <option value="<?php echo $leProjet['idProjet']; ?>"><?php echo $leProjet['nomProjet']; ?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- afficher les roles -->
                                    <select name="un-role" id="" class="liste-roles mon-role form-select form-select-sm mb-3" aria-label="Small select example">
                                        <option value="" selected>--Choisir un role--</option>
                                        <?php foreach ($roles as $key => $role) : ?>
                                            <option value="<?php echo $role['idRole']; ?>"><?php echo $role['nomRole']; ?> </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="content-page-box-area col-12">
                    <div class="row">
                        <div class="main-content-wrapper d-flex flex-column">
                            <h2>Liste des projets</h2>
                            <div class="col-lg-12 col-md-12">
                                <div class="news-feed-area">
                                    <?php if (isset($projetNotification)) : ?>
                                        <?php

                                        //recuperer les collaborateurs qui travaillent sur le projet
                                        $stmt = $bdd->prepare("SELECT collabo.idCollabo,nom,prenom,nomRole from collabo 
                                                                    JOIN projetcollaborole ON collabo.idCollabo = projetcollaborole.idCollabo
                                                                    JOIN role ON role.idRole = projetcollaborole.idRole WHERE projetcollaborole.idProjet = ?");
                                        $stmt->execute([$projetNotification['idProjet']]);
                                        $rolesCollaboNotif = $stmt->fetchAll(PDO::FETCH_ASSOC);



                                        ?>
                                        <div class="news-feed news-feed-post projetNotification" id="pointer-sur<?php echo $projetNotification['idProjet']; ?>">
                                            <div class="post-header  justify-content-between align-items-center">
                                                <div class="d-flex justify-content-between">
                                                    <span class="<?php echo $projetNotification['statut']=='en-cours'?'en-cours':'complete'; ?>"><?php echo $projetNotification['statut']; ?></span>
                                                    <?php if (isset($_SESSION['admin'])) : ?>
                                                        <a href="modifier-projet.php?idProjet=<?php echo $projetNotification['idProjet']; ?>"><i class="bi bi-pencil-square"></i></a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class=" ms-1 fs-4 mt-1 " style="font-size: 10px; display:flex; justify-content: space-between;  width:100%; background:#293a4e; padding: 5px 10px;" >
                                                    <span class="nomProjet"><a href="users-profile.php">Projet : <?php echo $projetNotification['nomProjet']; ?></a></span>
                                                </div>
                                            </div>

                                            <div class="post-body">
                                                <div>
                                                    <div>
                                                        <p class="description"><?php echo $projetNotification['descriptionProjet']; ?></p>
                                                        <p>Date de fin probable : <span style="color:red;"><?php echo $projetNotification['dateFin']; ?></span></p>
                                                    </div>
                                                </div>
                                                <?php if (count($rolesCollaboNotif) > 0) { ?>
                                                    <div class="projet d-block mt-3 ">
                                                        <ul class="post-meta-wrap  align-items-center">
                                                            <label class="col-form-label">Collaborateurs travaillant sur le projet</label>
                                                            <?php foreach ($rolesCollaboNotif as $key => $roleC) { ?>
                                                                <li class="col col-8 margin-auto">
                                                                    <p class="col" <?php echo $roleC['idCollabo'] == $idCollabo ? "style='border:1px solid red;border-radius:3px;padding:0 3px;'" : ''; ?>><span class="role"><?php echo $roleC['nomRole'] . ' : ' ?> </span> <span class="nomPrenom"> <?php echo $roleC['prenom'] . ' ' . $roleC['nom'] ?></span></p>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                <?php } ?>
                                                <p style="font-weight:bold;"><?php echo dateAction($projetNotification['dateAction']); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (count($projets) > 0) { ?>
                                        <?php foreach ($projets as $key => $projet) {
                                            //recuperer les collaborateurs qui travaillent sur le projet
                                            $stmt = $bdd->prepare("SELECT collabo.idCollabo,nom,prenom,nomRole from collabo 
                                                                    JOIN projetcollaborole ON collabo.idCollabo = projetcollaborole.idCollabo
                                                                    JOIN role ON role.idRole = projetcollaborole.idRole WHERE projetcollaborole.idProjet = ?");
                                            $stmt->execute([$projet['idProjet']]);
                                            $rolesCollabo = $stmt->fetchAll(PDO::FETCH_ASSOC);


                                        ?>
                                            <div class="news-feed news-feed-post" id="pointer-sur<?php echo $projet['idProjet']; ?>">
                                                <div class="post-header d-fle justify-content-between align-items-center">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="<?php echo $projet['statut']=='en-cours'?'en-cours':'complete'; ?>"><?php echo $projet['statut']; ?></span>
                                                        <?php if (isset($_SESSION['admin'])) : ?>
                                                            <a href="modifier-projet.php?idProjet=<?php echo $projet['idProjet']; ?>"><i class="bi bi-pencil-square"></i></a>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class=" ms-1 fs-4 mt-1 " style="font-size: 10px; display:flex; justify-content: space-between;  width:100%; background:#293a4e; padding: 5px 10px;">
                                                        <span class="nomProjet"><a href="#">Projet : <?php echo $projet['nomProjet']; ?></a></span>
                                                    </div>
                                                </div>

                                                <div class="post-body">
                                                    <div>
                                                        <p class="description"><?php echo $projet['descriptionProjet']; ?></p>
                                                        <p>Date de fin probable : <span style="color:red;"><?php echo $projet['dateFin']; ?></span></p>
                                                    </div>
                                                    <?php if (count($rolesCollabo) > 0) { ?>
                                                        <div class="projet d-block mt-3 ">
                                                            <ul class="post-meta-wrap  align-items-center">
                                                                <label class="col-form-label" >Collaborateurs travaillant sur le projet</label>
                                                                <?php foreach ($rolesCollabo as $key => $roleCol) { ?>
                                                                    <li class="col col-8 margin-auto">
                                                                        <p class="col" <?php echo $roleCol['idCollabo'] == $idCollabo ? "style='border:1px solid red;border-radius:3px;padding:0 3px;'" : ''; ?>><span class="role" style="color:red;" ><?php echo $roleCol['nomRole'] . ' : ' ?> </span> <span class="nomPrenom"> <?php echo $roleCol['prenom'] . ' ' . $roleCol['nom'] ?></span></p>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                    <p style="font-weight:bold;"><?php echo dateAction($projet['dateAction']); ?></p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- End Content Page Box Area -->
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->



    <!-- Inclure les fichier javaScript -->
    <?php require_once("includes/fichiers-js.php"); ?>

    <script>
        //recuperer le projet selectionné
        var monProjet = document.querySelector(".mon-projet");
        //recuperer le role selectionné
        var monRole = document.querySelector(".mon-role");

        monRole.addEventListener("focus", function() {
            if (!monProjet.value) {
                alert("Veuillez selectionner d'abord un projet");
            } else {
                this.addEventListener("change", function() {
                    var conf = confirm("Voulez-vous vraiment ajouter ce role???");
                    if (conf) {
                        window.location.href = "traitement/modify-projet.php?action=ajouter-projet-role&idProjet=" + monProjet.value + "&idRole=" + monRole.value;
                    }
                });
            }
        });
    </script>


</body>

</html>