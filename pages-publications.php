<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");
// Connexion à la base de données
require_once("includes/bdd-connect.php");
//les fonctions
include_once("includes/fonctions.php");



try {


    if ($collaborateur['imageProfil']) {
        // Afficher l'image
        $imageData = base64_encode($collaborateur['imageProfil']); // Convertir les données de l'image en base64
        $imageType = $collaborateur['imageProfilType']; // Récupérer le type de l'image
        $srcProfil = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image

    }


    //recuperer les questions de la bdd
    $stmt = $bdd->prepare("SELECT * FROM pub");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);



    //Recuperer les utilisateurs qui ont poser des questions
    //classer par statut
    if (isset($_GET['statut-affichage'])) {
        $stmt = $bdd->prepare("SELECT * FROM pub JOIN collabo ON collabo.idCollabo = pub.idCollabo WHERE statutPub = ? ORDER BY pub.dateAction DESC");
        $stmt->execute([$_GET['statut-affichage']]);
    } else {
        $stmt = $bdd->prepare("SELECT * FROM pub JOIN collabo ON collabo.idCollabo = pub.idCollabo WHERE statutPub !='annulé' ORDER BY pub.dateAction DESC");
        $stmt->execute();
    }
    $questionsCollabo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($questionsCollabo);
    // exit;


    //recuperer les projets
    $stmt = $bdd->prepare("SELECT * FROM projet");
    $stmt->execute();
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
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



    <title>Social Community</title>

    <link rel="icon" type="image/png" href="assets/vendor/post/images/favicon.png">
    <!-- Inclure les fichier css -->
    <?php require_once "includes/fichiers-css.php"; ?>


    <style>
        body div {
            font-size: 16px !important;
        }

        /* CSS personnalisé pour définir la hauteur du carrousel */
        .carousel {
            height: 50vh;
            /* La moitié de la hauteur de la fenêtre visible */
        }

        #carousel .carousel-item,
        .carousel #carousel-item img {
            min-width: 100%;
        }

        #carouselExample {
            width: 50% !important;
            height: 50vh !important;
            margin: auto !important;
            margin-top: 200px !important;
            margin-bottom: 100px !important;
        }

        #carouselExample .carousel-item {
            position: relative !important;
        }

        #carouselExample .carousel-item .carousel-caption {
            position: absolute !important;
            top: 10px !important;
        }

        #carouselExample .carousel-item img {
            width: 100% !important;
            height: 100% !important;
        }

        #carouselExample .carousel-title,
        #carouselExample .carousel-description {
            background-color: rgba(246, 249, 255, 0.5) !important;
            color: black !important;
            font-weight: bold !important;
            border-radius: 20% !important;
            padding: 3px 0 !important;
        }



        .carousel-control-prev,
        .carousel-control-next {
            opacity: 1 !important;
        }


        /* 
        
            #la page contenant les pub 
        
        */
        .main-content-wrapper {
            padding-right: 0 !important;
            display: flex !important;
            flex-direction: row !important;
            /* margin:50px 100px; */
            background: white !important;
            padding: 20px 100px 50px 50px;
        }


        /* menu de navigation horizontal des pubs */
        .menu-publication-parent {
            background-color: white;
            padding: 0 50px 0 0;
        }

        .menu-publication {
            background-color: #f4f7fc !important;
            max-height: 80px !important;
            line-height: 80px !important;
            /* margin-right: 50px; */
        }

        .menu-publication-item {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            max-width: 90% !important;
            margin: 0 auto !important;
            padding: 0;
        }

        .onglet-actif {
            position: relative !important;
            color: red !important;
            text-decoration: underline;
            /* distance du soulignement */
            text-underline-offset: 5px;
            /* epaisseur du soulignement */
            text-decoration-thickness: 5px;
        }

        .content-page-box-area {
            padding: 0 50px 0 0;
        }

        @media (max-width: 700px) {
            .menu-publication {
                max-height: fit-content !important;
                line-height: normal;
            }
        }

        .news-feed-area .news-feed-post {
            padding: 0;
        }





        .news-feed-post {
            /* display: flex;
            flex-direction: row; */
            margin-top: 80px;

        }

        .post-header {
            width: 100%;
            background-color: #143D59;
            height: 100px;
            align-items: center;
        }

        /* image de profil de celui qui a publie */
        .image {

            margin-right: 2rem;

        }

        /* Supprime l'espace sous l'image */
        .image img {
            display: block;
            width: 80px;
            height: 80px;
            border: 2px solid white;
            border-radius: 50%;
            margin-left: 10px;
            background-color: white;
        }



        .post-body {
            width: 100%;
        }

        .post-body .contenu {
            width: 100%;
        }

        .post-body .description {
            font-size: 1rem !important;
            color: black !important;
            width: 90%;
            margin: 0 auto;
        }

        .titrePub {
            text-align: center;
        }




        .questions {
            box-shadow: none !important;
        }


        .liste-questions li {
            margin-bottom: 10px !important;
            border: 0.4px solid black !important;
            border-radius: 0 !important;
        }

        .liste-questions li:hover {
            background-color: #f4f7fc;
        }


        .liste-questions li div {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-around !important;
            margin: 0 auto !important;
        }

        .liste-questions li p {
            margin: 0;
            padding: 0;
        }

        .liste-questions li h5 {
            color: red !important;
            text-decoration: underline !important;
            font-style: oblique;
        }

        .liste-questions li div>:first-child {
            font-weight: bold !important;
        }

        /*
            la publication
        */


        .publications {
            /* border-top: 1px solid black !important; */
            border-radius: unset !important;
            margin-bottom: 200px !important;
            padding-top: 0;


        }


        .post-header .info-auteur-question {
            margin-left: 10px !important;
        }

        .info-auteur-question .name a,
        .info-auteur-question .small-text span {
            color: white;
        }

        .titrePub {
            font-size: 18px !important;
            color: black !important;
            text-align: center !important;
            font-style: italic;
        }

        .titrePub a {
            color: black !important;
        }

        .post-body div:nth-child(1):hover {
            background-color: rgba(252, 252, 252, 1) !important;
            border-radius: 3px !important;
            transition: all 0.3s ease-in-out !important;
        }

        .post-body .description {
            text-align: justify !important;
            padding: 0 5px !important;
        }




        .pagetitle h1{
      margin-bottom: 150px;
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
            <h1>Questions</h1>            
        </div><!-- End Page Title -->

        <section class="section profile">

            <div class="main-content-wrapper d-flex flex-row justify-content-between row">
                <!-- Start Main Content Wrapper Area -->

                <!-- enfant 1 -->
                <!-- menu de navigation pour les publication -->
                <div class="menu-publication-parent">
                    <div class="menu-publication col-lg-12 col-md-12">
                        <div class="menu-publication-item">
                            <a href="?" class="<?php echo isset($_GET['statut-affichage']) ? '' : 'onglet-actif'; ?>">Tout voir</a>
                            <a href="?statut-affichage=nouveau" class="<?php echo (isset($_GET['statut-affichage']) && ($_GET['statut-affichage'] == 'nouveau')) ? 'onglet-actif' : ''; ?>">Nouveaux</a>
                            <a href="?statut-affichage=complété" class="<?php echo (isset($_GET['statut-affichage']) && ($_GET['statut-affichage'] == 'complété')) ? 'onglet-actif' : ''; ?>">Complétés</a>
                            <a href="?statut-affichage=en-cours" class="<?php echo (isset($_GET['statut-affichage']) && ($_GET['statut-affichage'] == 'en-cours')) ? 'onglet-actif' : ''; ?>">En-cours</a>
                            <a href="?statut-affichage=relancé" class="<?php echo (isset($_GET['statut-affichage']) && ($_GET['statut-affichage'] == 'relancé')) ? 'onglet-actif' : ''; ?>">Relancés</a>
                            <a href="?statut-affichage=resolu" class="<?php echo (isset($_GET['statut-affichage']) && ($_GET['statut-affichage'] == 'resolu')) ? 'onglet-actif' : ''; ?>">Resolues</a>
                        </div>
                    </div>
                </div>
                <div class="content-page-box-area col-12">
                    <div class="row">
                        <div class="main-content-wrapper-item d-flex flex-column" style="background:white !important">
                            <div class="col12 col-12">
                                <div class="news-feed-area">
                                    <?php if (count($questionsCollabo) > 0) { ?>
                                        <?php foreach ($questionsCollabo as $key => $question) {
                                            if ($question['imageProfil']) { //si une image a ete definie pour l'annonce
                                                $imageProfil = base64_encode($question['imageProfil']); // Convertir les données de l'image en base64
                                                $imageProfilType = $question['imageProfilType']; // Récupérer le type de l'image
                                                $srcProfil_auteurPub = "data:{$imageProfilType};base64,{$imageProfil}"; // Format de l'URL de l'image

                                            }
                                            // var_dump($question);

                                            /// Pas d'images car cela peut etre consideree comme un reseau social

                                            // if ($question['imagePub']) { //si une image a ete definie pour l'annonce
                                            //     $imagePub = base64_encode($question['imagePub']); // Convertir les données de l'image en base64
                                            //     $imagePubType = $question['imagePubType']; // Récupérer le type de l'image
                                            //     $srcPub = "data:{$imagePubType};base64,{$imagePub}"; // Format de l'URL de l'image
                                            // }

                                            //chercher le projet
                                            if (isset($question['idProjet'])) {
                                                $stmt = $bdd->prepare("SELECT * FROM projet WHERE idProjet=?");
                                                $stmt->execute([$question['idProjet']]);
                                                $leProjet = $stmt->fetch(PDO::FETCH_ASSOC);
                                            }

                                            //recuperer les reponses du commentaire de la bdd
                                            $stmt = $bdd->prepare("SELECT * FROM reponse WHERE idPub = ?");
                                            $stmt->execute([$question['idPub']]);
                                            $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            if ($reponses)  $nbReponses = count($reponses);
                                            // var_dump($question);
                                        ?>
                                            <div class="news-feed news-feed-post publications" id="pointer-sur<?php echo $question['idPub']; ?>" data-statutPub="<?php echo $question['statutPub'] ?>" data-idPub="<?php echo $question['idPub']; ?>">
                                                <div class="post-header d-flex ">
                                                    <div class="image">
                                                        <a href="users-profile.php?idCollabo=<?php echo $question['idCollabo']; ?>&amp;action=voir-profil"><img src="<?php echo isset($srcProfil_auteurPub) ? $srcProfil_auteurPub : "assets/img/profile-inconnu.png";
                                                                                                                                                                        unset($srcProfil_auteurPub); ?>" class="rounded-circle" alt="image-profil"></a>
                                                    </div>
                                                    <div class="info ms-1 info-auteur-question">
                                                        <span class="name"><a href="users-profile.php?idCollabo=<?php echo $question['idCollabo']; ?>&amp;action=voir-profil"><?php echo (isset($question['nom']) && isset($question['prenom'])) ? $question['prenom'] . ' ' . $question['nom'] : "Inconnu" ?></a></span>
                                                        <span class="small-text"><?php echo isset($question['job']) ? ($question['job']=='Non defini'?'':$question['job']) : "" ?></span>
                                                    </div>
                                                    <!-- modifier la publication si le statut est encore nouveau -->
                                                    <?php if (($question['statutPub'] == 'nouveau') && ($question['idCollabo'] == $idCollabo)) : ?>
                                                        <div style="position: absolute;right:80px;">
                                                            <a href="modifier-publication.php?idPub=<?php echo $question['idPub']; ?>"><i class="bi bi-pencil-square" style="color:white;z-index:100;"></i></a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="post-body">
                                                    <div>
                                                        <a class="contenu" href="voir-plus-reponse.php?idPub=<?php echo $question['idPub']; ?>&amp;idCollabo=<?php echo $question['idCollabo']; ?>">
                                                            <p class="titrePub">
                                                                <!-- le titre de la publication -->
                                                                <?php echo isset($question['titrePub']) ? nl2br($question['titrePub']) : "" ?>
                                                            </p>
                                                            <p class="description" style="color: #143D59 !important;">
                                                                <!-- le contenu de la publication (texte) -->
                                                                <?php echo isset($question['descriptionPub']) ? nl2br($question['descriptionPub']) : "" ?>
                                                            </p>
                                                        </a>
                                                    </div>
                                                    <!-- Pas d'images car cela peut etre comme un reseau social -->
                                                    <!-- <div class="post-image">
                                                        <img src="<?php echo isset($srcPub) ? $srcPub : "";
                                                                    unset($srcPub); ?>" alt="image-publication">
                                                    </div> -->

                                                    <!-- la date de publication -->
                                                    <p style="font-size: 12px;text-align:end;display:block;margin:10px 0 0 0;font-weight:bold;border-top:0.4px solid rgba(0,0,0,0.2);">
                                                        <?php
                                                        echo dateAction($question['dateAction']);
                                                        ?>
                                                    </p>

                                                    <ul class="post-meta-wrap d-flex justify-content-between align-items-center">
                                                        <li class="post-comment">
                                                            <a class="nbr-commentaires" href="voir-plus-reponse.php?idPub=<?php echo $question['idPub']; ?>&amp;idCollabo=<?php echo $question['idCollabo']; ?> ">
                                                                <span class="nbCommentaires" style="color : blue !important;">
                                                                    <?php echo isset($nbReponses) ? $nbReponses . " " : "0 "; ?>
                                                                </span>
                                                                <span style="color : blue !important;">
                                                                    Commentaire<?php echo (isset($nbReponses) && ($nbReponses > 1)) ? "s" : "";
                                                                                unset($nbReponses); ?>
                                                                </span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <!-- le projet concernee s'il en existe -->
                                                    <?php if (isset($leProjet) && isset($leProjet['nomProjet'])) { ?>
                                                        <div class="projet d-block mt-3" style="background:#f5f9ff; padding: 2px 10px 2px 15px">
                                                            <p class="nomProjet"><span style="text-decoration:underline;font-weight:bold; ">Projet:</span> <?php echo $leProjet['nomProjet'];
                                                                                                                                                            unset($leProjet); ?></p>
                                                        </div>
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        <?php
                                            unset($question);
                                        }

                                        ?>
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
    <!-- End Main Content Wrapper Area -->


    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>

</body>

</html>