<?php
//authentification obligatoire
require "traitement/auth_connect_needed.php";
include_once("includes/fonctions.php");

//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

// var_dump($idCollabo);
// exit;

//si aucune publication n'est definie on renvoie a la page des publications
// if(!isset($_GET['idPub'])){
//         header('Location:pages-publications.php?');
// }

if (isset($_GET['idPub'], $_GET['idCollabo'])) {
        try {

                //si on vient d'une notification, marquer comme lue
                if (isset($_GET['idNotification'])) {
                        $idNotification = $_GET['idNotification'];
                        $stmt = $bdd->prepare("UPDATE notificationCollabo SET statut = 0 WHERE idCollabo = ? AND idNotification = ?");
                        $stmt->execute([$idCollabo, $idNotification]);
                }

                $idPub = $_GET['idPub'];
                $idCollaboAuteurQuestion = $_GET['idCollabo'];

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

                //Recuperer l'utilisateur qui a poser la question
                $stmt = $bdd->prepare("SELECT * FROM pub
                                    JOIN collabo ON collabo.idCollabo = pub.idCollabo
                                    WHERE collabo.idCollabo = ? AND pub.idPub = ? LIMIT 1
                                ");
                $stmt->execute([$idCollaboAuteurQuestion, $idPub]);
                $questionCollabo = $stmt->fetch(PDO::FETCH_ASSOC);
                // var_dump($questionCollabo);
                // exit;


                //recuperer les projets
                $stmt = $bdd->prepare("SELECT * FROM projet");
                $stmt->execute();
                $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
        }
}

?>

<!doctype html>
<html lang="En">

<head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


        <!-- Inclure les fichier css -->
        <?php require_once "includes/fichiers-css.php"; ?>
        <!-- inclure la bibliotheque Prism pour la coloration syntaxique du code -->
        <link rel="stylesheet" href="includes/prism/prism-okaidia.css">




        <title>Social Community</title>




        <style>
                /*

            #la page contenant les pub

        */
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

                .post-body .description {
                        font-size: 1rem !important;
                        color: black !important;
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
                .titrePub {
                        font-size: 18px !important;
                        color: black !important;
                        text-align: center !important;
                        font-style: italic;
                }

                /* .post-header {
                        border-bottom: 1px solid black !important;
                        padding-bottom: 20px !important;
                        border-bottom-width: 40% !important;
                } */

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
                        margin-left: 1rem;
                        padding: 0;
                }

                /* Supprime l'espace sous l'image */
                .image img {
                        display: block;
                        width: 80px;
                        height: 80px;
                        border: 2px solid white;
                        border-radius: 50%;
                        /* margin-left: 10px; */
                        background-color: white;
                }
                .post-header .name a{
                        color: white !important;
                }

                .post-body {
                        margin-bottom: 10rem;

                }

                .post-body .description {
                        text-align: justify !important;
                        font-size: 16px !important;
                }

                /* .post-body .post-image {
                        width: 100% !important;
                        margin: auto !important;
                }

                .post-body .post-image img {
                        max-width: 100% !important;
                        max-height: 60vh !important;
                        margin: auto !important;
                } */

                /*
            #zone de commantaires
        */
                .disparaitre,
                .disparaitre-info {
                        display: none !important;
                }

                .disparaitre-info {
                        display: none !important;
                }

                .comment-zone,
                .repondre-commentaire-zone {
                        position: relative !important;
                        min-height: 300px !important;
                }

                .comment-area,
                .reponse-comment-area {
                        min-height: 200px !important;
                }

                .form-group {
                        position: relative !important;
                        margin: 20px 0 5px 0 !important;
                }

                .btn-envoie {
                        position: absolute !important;
                        right: 20px !important;
                        bottom: 20px !important;
                }

                .btn-envoie-reponse {
                        position: absolute !important;
                        right: 20px !important;
                        bottom: 50px !important;
                }

                .btn-envoie .bi-send-fill,
                .btn-envoie-reponse .bi-send-fill {
                        color: blue !important;
                }

                .btn-envoie .bi-send-fill:hover,
                .btn-envoie-reponse .bi-send-fill:hover {
                        color: red !important;
                }

                .btn-envoie .bi-send-fill:active,
                .btn-envoie-reponse .bi-send-fill:active {
                        color: green !important;
                }

                .form-group button {
                        background-color: transparent !important;
                }

                .bi-send-fill {
                        cursor: pointer;
                }

                .container-commentaires {
                        margin-bottom: 80px !important;
                        border-top: 1px solid black !important;
                        padding-top: 10px !important;
                }

                .commentaire-repondu {
                        display: flex !important;
                        flex-direction: row !important;
                        justify-content: space-around !important;
                        flex-wrap: wrap !important;
                }

                /* le formulaire pour recueillir le code */
                .code-form,
                .code-form-reponse {
                        position: absolute !important;
                        min-height: 100% !important;
                        top: 0 !important;
                        right: 0 !important;
                        left: 0 !important;
                        z-index: 100 !important;
                        /* display: none !important; */
                        background-color: #f6f9ff !important;
                }

                .code-form textarea,
                .code-form-reponse textarea {
                        width: 90% !important;
                        min-height: 200px !important;
                        margin: 20px !important;


                }

                .code-form .boutons,
                .code-form-reponse .boutons {
                        display: flex !important;
                        flex-direction: row !important;
                        justify-content: space-around !important;
                        margin-top: 10px !important;
                }

                .code-form .boutons .bouton,
                .code-form-reponse .boutons .bouton {
                        font-size: 14px !important;
                        padding: 3px !important;
                        width: fit-content !important;
                        height: fit-content !important;
                }



                /* Les reactions (like et dislike) */

                .reactionsComment {
                        display: flex !important;
                        flex-direction: column !important;
                        height: fit-content;
                }

                .reactions {
                        border-top: 3px solid #f4f7fc !important;
                        padding-top: 10px !important;
                }

                .reactions div {
                        margin-right: 40px !important;
                        text-align: center !important;
                }

                .reactions .note {
                        display: flex !important;
                        flex-direction: column !important;
                        text-align: center !important;
                }

                .reactions .note i,
                .reactions .note select {
                        padding: 0 !important;
                        margin: 0 !important;
                }

                .reactions .note i {
                        width: 100% !important;
                        font-size: 20px !important;
                        text-align: center !important;
                }

                .reactions .note select {
                        width: 100% !important;
                }

                .reactions .moyenne p {
                        margin: 0 !important;
                        padding: 0 !important;
                }

                .repondre-commentaire {
                        margin-top: 20px !important;
                }

                .repondre-commentaire .form-group {
                        position: relative !important;
                }

                .repondre-commentaire .form-group button {
                        position: absolute !important;
                        right: 5% !important;
                        top: 30% !important;
                }

                .repondre-commentaire button #send-fill {
                        font-size: 18px !important;
                }

                .bouton-repondre {
                        cursor: pointer !important;
                }

                /* le contenu du commentaire */
                .contenu-commentaire {
                        color: #1C1C1C !important;
                }

                /* mention du commentaire-repondu */
                .commentaire-repondu {
                        background-color: #f6f9ff;
                        margin: 10px auto !important;
                        padding: 5px 10px !important;
                        font-style: italic !important;
                        width: 94% !important;
                        border-radius: 5px !important;
                        font-size: 30px !important;
                        display: flex !important;
                        flex-direction: row;
                        flex-wrap: wrap !important;
                }

                .commentaire-repondu h6 {
                        font-weight: bold !important;
                }

                .commentaire-repondu p {
                        font-size: 18px !important;
                }


                .like,
                .dislike {
                        color: gray !important;
                }

                .liked {
                        color: blue !important;
                }

                .liked:hover {
                        color: red !important;
                }

                .disliked {
                        color: red !important;
                }

                .note {
                        display: flex;
                        align-items: center;
                }
        </style>

        <!-- pour la recherche dans la pages -->
        <link rel="stylesheet" href="includes/rechercher.css">

</head>

<body class="line-numbers">
        <!-- ======= Header ======= -->
        <?php require_once "includes/main-header.php" ?>

        <!-- ======= Sidebar ======= -->
        <?php include_once "includes/main-sidebar.php" ?>


        <main id="main" class="main .bg-image">
                <div class="pagetitle">
                        <h1>Question</h1>
                        <!-- <nav>
                                <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                        <li class="breadcrumb-item">Users</li>
                                        <li class="breadcrumb-item active">Profile</li>
                                </ol>
                        </nav> -->
                </div><!-- End Page Title -->

                <section class="section profile">

                        <div class="main-content-wrapper d-flex flex-row justify-content-between row" style="background: #f6f9ff !important;">
                                <!-- Start Main Content Wrapper Area -->

                                <!-- enfant 1 -->
                                <div class="content-page-box-area col-11 ml-1 mr-1">
                                        <?php if (isset($questionCollabo['statutPub']) && ($questionCollabo['statutPub'] == 'resolu')) : ?>
                                                <h5 style="width: 100%;height:50px;background-color:#e5f0cb;color:#619b3e;line-height:50px;text-align:center;"><i class="bi bi-check-lg" style="color:#3a8415;font-size:32px;font-weight:bold;"></i>Resolu</h5>
                                        <?php elseif (isset($questionCollabo['statutPub']) && ($questionCollabo['statutPub'] == 'annulé')) : ?>
                                                <h5 style="width: 100%;height:50px;background-color:rgba(255,0,0,0.5);color:white;line-height:50px;text-align:center;">Annulé</h5>
                                        <?php endif; ?>
                                        <div class="row">
                                                <div class="main-content-wrapper d-flex flex-column" style="background: #f6f9ff !important;">
                                                        <div class="col-lg-12 col-md-12">
                                                                <?php if (isset($questionCollabo['statutPub']) && ($questionCollabo['idCollabo'] == $idCollabo)) : ?>
                                                                        <select name="lien" id="" class="form-select form-select-sm mb-3 modifierPub" style="width: 20%;">
                                                                                <option value="">--Modifier--</option>
                                                                                <?php if (($questionCollabo['statutPub'] == 'complété') || ($questionCollabo['statutPub'] == 'en-cours') || ($questionCollabo['statutPub'] == 'relancé')) : ?>
                                                                                        <option value="traitement/modify-publication.php?idPub=<?php echo $questionCollabo['idPub']; ?>&amp;idAuteurPub=<?php echo $questionCollabo['idCollabo'] ?>&amp;action=marquer-question-comme-resolu">
                                                                                                Marquer la question comme resolue
                                                                                        </option>
                                                                                <?php endif; ?>
                                                                                <?php if (($questionCollabo['statutPub'] == 'nouveau') || ($questionCollabo['statutPub'] == 'en-cours')) : ?>
                                                                                        <option value="traitement/modify-publication.php?idPub=<?php echo $questionCollabo['idPub']; ?>&amp;idAuteurPub=<?php echo $questionCollabo['idCollabo'] ?>&amp;action=annuler-question">
                                                                                                Annuler la question
                                                                                        </option>
                                                                                <?php endif; ?>
                                                                                <?php if ($questionCollabo['statutPub'] == 'complété') : ?>
                                                                                        <option value="traitement/modify-publication.php?idPub=<?php echo $questionCollabo['idPub']; ?>&amp;idAuteurPub=<?php echo $questionCollabo['idCollabo'] ?>&amp;action=relancer-question">
                                                                                                Relancer la question
                                                                                        </option>
                                                                                <?php endif; ?>
                                                                                <?php if (($questionCollabo['statutPub'] == 'resolu') || ($questionCollabo['statutPub'] == 'annulé')) : ?>
                                                                                        <option value="">
                                                                                                Désolé, il n'y a rien a modifier
                                                                                        </option>
                                                                                <?php endif; ?>

                                                                        </select>
                                                                <?php endif; ?>
                                                                <div class="news-feed-area">
                                                                        <?php if (isset($questionCollabo) && !empty($questionCollabo)) {

                                                                                if (isset($questionCollabo['imageProfil'])) { //si une image a ete definie pour l'annonce
                                                                                        $imageProfil = base64_encode($questionCollabo['imageProfil']); // Convertir les données de l'image en base64
                                                                                        $imageProfilType = $questionCollabo['imageProfilType']; // Récupérer le type de l'image
                                                                                        $srcProfil_auteurPub = "data:{$imageProfilType};base64,{$imageProfil}"; // Format de l'URL de l'image

                                                                                }

                                                                                //chercher le projet
                                                                                if (isset($questionCollabo['idProjet'])) {
                                                                                        $stmt = $bdd->prepare("SELECT * FROM projet WHERE idProjet=?");
                                                                                        $stmt->execute([$questionCollabo['idProjet']]);
                                                                                        $leProjet = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                                }

                                                                                //recuperer les reponses du commentaire de la bdd
                                                                                $stmt = $bdd->prepare("SELECT * FROM reponse WHERE idPub = ? ORDER BY dateAction ASC");
                                                                                $stmt->execute([$questionCollabo['idPub']]);
                                                                                $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                                // var_dump($reponses);
                                                                                // exit;

                                                                                if (isset($reponses)) {
                                                                                        $nbReponses = count($reponses);
                                                                                }

                                                                        ?>
                                                                                <div class="news-feed news-feed-post" id="pointer-sur<?php echo $questionCollabo['idPub']; ?>">
                                                                                        <!-- en-tete de la publication -->
                                                                                        <div class="post-header d-flex justify-content-betwee align-items-center">
                                                                                                <div class="image">
                                                                                                        <!-- charger l'image de profil de l'auteur de la publication -->
                                                                                                        <a href="users-profile.php?idCollabo=<?php echo $questionCollabo['idCollabo']; ?>&amp;action=voir-profil"><img src="<?php echo isset($srcProfil_auteurPub) ? $srcProfil_auteurPub : "assets/img/profile-inconnu.png";
                                                                                                                                                                                                                                unset($srcProfil_auteurPub); ?>" class="rounded-circle" alt="image-profil"></a>
                                                                                                </div>
                                                                                                <div class="info ms-1">
                                                                                                        <!-- ajouter le nom et prenom de l'auteur de la publication -->
                                                                                                        <span class="name">
                                                                                                                <a href="users-profile.php?idCollabo=<?php echo $questionCollabo['idCollabo']; ?>&amp;action=voir-profil">
                                                                                                                        <?php echo (isset($questionCollabo['nom']) && isset($questionCollabo['prenom'])) ? $questionCollabo['prenom'] . ' ' . $questionCollabo['nom'] : "Inconnu" ?>
                                                                                                                </a>
                                                                                                        </span>
                                                                                                        <span class="small-text"><?php echo isset($questionCollabo['job']) ? $questionCollabo['job'] : "" ?></span>
                                                                                                </div>
                                                                                                <!-- modifier la publication si le statut est encore nouveau -->
                                                                                                <?php if (($questionCollabo['statutPub'] == 'Nouveau') && ($questionCollabo['username'] == $_SESSION['auth'])) { ?>
                                                                                                        <div class="">
                                                                                                                <a href="modifier-publication.php?idPub=<?php echo $questionCollabo['idPub']; ?>"><i class="bi bi-pencil-square"></i></a>
                                                                                                        </div>
                                                                                                <?php } ?>
                                                                                        </div>

                                                                                        <div class="post-body ">
                                                                                                <div>
                                                                                                        <p class="titrePub">
                                                                                                                <!-- le titre de la publication -->
                                                                                                                <?php echo (isset($questionCollabo['titrePub']) && !empty($questionCollabo['titrePub'])) ? $questionCollabo['titrePub'] : "" ?> <!--  -->
                                                                                                        </p>
                                                                                                        <p class="description" style="color: #143D59 !important;">
                                                                                                                <!-- le contenu de la publication (texte) -->
                                                                                                                <?php echo isset($questionCollabo['descriptionPub']) ? $questionCollabo['descriptionPub'] : "" ?>
                                                                                                        </p>
                                                                                                </div>


                                                                                                <!-- on affiche le projet concerné (s'il y en existe) -->
                                                                                                <?php if (isset($leProjet) && !empty($leProjet)) { ?>
                                                                                                        <div class="projet d-block mt-3" style="background:#f5f9ff; padding: 2px 10px 2px 15px">
                                                                                                                <p class="nomProjet">
                                                                                                                        <span style="text-decoration:underline;font-weight:bold; ">Projet:</span>
                                                                                                                        <?php echo $leProjet['nomProjet'];
                                                                                                                        unset($leProjet); ?>
                                                                                                                </p>
                                                                                                        </div>
                                                                                                <?php } ?>
                                                                                                <!-- la date de publication -->
                                                                                                <p style="font-size: 12px;text-align:end;display:block;margin:10px 0 0 0;font-weight:bold;">
                                                                                                        <?php
                                                                                                        echo dateAction($questionCollabo['dateAction']);
                                                                                                        ?>
                                                                                                </p>

                                                                                                <ul class="post-meta-wrap d-flex justify-content-between align-items-center">
                                                                                                        <!-- afficher le nombre de commetaires -->
                                                                                                        <li class="post-comment">
                                                                                                                <a>
                                                                                                                        <?php echo isset($nbReponses) ? $nbReponses . " " : "0 "; ?>
                                                                                                                        <span>
                                                                                                                                Commentaire<?php echo (isset($nbReponses) && ($nbReponses > 1)) ? "s" : ""; ?>
                                                                                                                        </span>
                                                                                                                </a>
                                                                                                        </li>
                                                                                                        <li>
                                                                                                                <!-- pour faire apparaitre la zone de texte du commentaire principale -->
                                                                                                                <button href="" class="btn btn-success btn-zone-commentaire-principale">Commenter</button>
                                                                                                        </li>
                                                                                                </ul>
                                                                                                <!-- zone de commentaire -->
                                                                                                <div class="comment-zone disparaitre">
                                                                                                        <form id="comment-form" class="comment-form" action="traitement/add-commentaire.php?idPub=<?php echo $questionCollabo['idPub']; ?>&amp;action=voir-plus-reponse&amp;idCollaboQuestion=<?php echo $questionCollabo['idCollabo']; ?>" method="post">

                                                                                                                <div class="form-group">
                                                                                                                        <!-- zone de saisi du commentaire -->
                                                                                                                        <textarea name="commentaire" class="form-control comment-area" placeholder="Saisir votre commentaire ici..."></textarea>
                                                                                                                        <label for="send-fill" class="btn-envoie" style="color: blue !important;">
                                                                                                                                <button type="submit" class="btn btn-transparent p-0 border-0" name="envoyer-commentaire">
                                                                                                                                        <i class="bi bi-send-fill  fs-5 primary" id="send-fill"></i>
                                                                                                                                </button>
                                                                                                                        </label>
                                                                                                                </div>
                                                                                                                <div class="col col-12">
                                                                                                                        <div class="row align-items-center">
                                                                                                                                <div class="col-4 btn-ajouter-code disparaitre">
                                                                                                                                        <span class=" btn btn-primary dispalay-languages" data-toggle="tooltip" data-placement="top" title="Ajouter un code" style="font-size: 14px;">
                                                                                                                                                Ajouter un code
                                                                                                                                                <!-- <i class="bi bi-braces btn btn-primary" data-toggle="tooltip" data-placement="top" title="Ajouter un code" style="font-size: 14px;" onclick="displayLanguages()"></i> -->
                                                                                                                                        </span>
                                                                                                                                </div>
                                                                                                                                <div class="col-6 select-language disparaitre">
                                                                                                                                        <select class="form-select form-select-sm" name="add-code" aria-label="Small select example">
                                                                                                                                                <option selected>-- Sélectionner le langage --</option>
                                                                                                                                                <option value="html">HTML</option>
                                                                                                                                                <option value="css">CSS</option>
                                                                                                                                                <option value="php">PHP</option>
                                                                                                                                                <option value="javascript">JavaScript</option>
                                                                                                                                        </select>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                        <p class="note-info-code disparaitre-info" style="color:green;">Si vous ajoutez un code, il se presentera automatiquement de la manière suivante : <br> <span style="color: initial !important;"> &lt;pre&gt;&lt;code&gt; <span style="color: red !important;">le code...</span> &lt;/code&gt;&lt;/pre&gt; </span><br> <span style="color: red !important;">Veuillez le laisser sous ce format pour optimiser l'affichage </span></p>
                                                                                                                </div>
                                                                                                        </form>
                                                                                                        <form class="code-form disparaitre" id="code-form">
                                                                                                                <textarea name="" id="" class="leCodeSaisi" placeholder="Saisir ou coller votre code ici..."></textarea>
                                                                                                                <div class="boutons">
                                                                                                                        <input class="btn btn-primary bouton btn-confirmer-ajout-code" type="submit" value="Ajouter">
                                                                                                                        <input class="btn btn-warning bouton" type="reset" value="Effacer">
                                                                                                                        <input class="btn btn-danger bouton btn-annuler-code" type="submit" value="Annuler">
                                                                                                                </div>
                                                                                                        </form>
                                                                                                </div>

                                                                                        </div>

                                                                                        <!-- afficher les reponses -->
                                                                                        <?php if (isset($reponses) && (count($reponses) > 0)) { ?>
                                                                                                <?php foreach ($reponses as $key => $reponse) {

                                                                                                        //recuperer celui qui a publier le commentaire(cette reponse) en cours
                                                                                                        $stmt = $bdd->prepare("SELECT collabo.idCollabo AS idAuteurComment,nom,prenom FROM collabo WHERE idCollabo = ANY (SELECT idCollabo FROM reponse WHERE idRep = ?)");
                                                                                                        $stmt->execute([$reponse['idRep']]);
                                                                                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                                                        // var_dump($reponse);
                                                                                                        if ($result) {
                                                                                                                $auteurReponse = $result;

                                                                                                                //verifier si ce commentaire est une reponse a un autre commentaire
                                                                                                                $stmt = $bdd->prepare("SELECT * FROM reponsecommentaire WHERE idRepCommentaire = ? AND idPub = ? LIMIT 1");
                                                                                                                $stmt->execute([$reponse['idRep'], $questionCollabo['idPub']]);
                                                                                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                                                                // var_dump($result);
                                                                                                                // // exit;
                                                                                                                if ($result) {
                                                                                                                        $repDuCommentaire = $result;
                                                                                                                        // var_dump($result);
                                                                                                                        // exit;

                                                                                                                        //chercher celui a qui il a repondu 
                                                                                                                        $stmt = $bdd->prepare("SELECT nom,prenom FROM collabo WHERE idCollabo = ? LIMIT 1");
                                                                                                                        $stmt->execute([$repDuCommentaire['idAuteurCommentaire']]);
                                                                                                                        $resul = $stmt->fetch(PDO::FETCH_ASSOC);

                                                                                                                        if ($resul) {
                                                                                                                                $auteurCommentaire = $resul;
                                                                                                                                // var_dump($auteurCommentaire);
                                                                                                                        }
                                                                                                                }


                                                                                                ?>
                                                                                                                <div class="container-commentaires" id="commentaire<?php echo $reponse['idRep']; ?>" style="padding:0;">
                                                                                                                        <div class="card-body" style="padding:0;">
                                                                                                                                <!-- l'auteur de ce commentaire courant -->
                                                                                                                                <?php if (isset($auteurReponse)) : ?>
                                                                                                                                        <div style="margin:0;">
                                                                                                                                                <!-- afficher la date et l'heure de l'action -->
                                                                                                                                                <p style="font-size: 10px; color:white;  height:100%;line-height:100%; background:#293a4e; padding: 5px;"><?php $dateAction = new DateTime($reponse['dateAction']);
                                                                                                                                                                                                                                                                echo $dateAction->format("d-m-Y") . " | " . $dateAction->format("H : i"); ?></p>
                                                                                                                                        </div>
                                                                                                                                        <div>
                                                                                                                                                <p class="card-title text-sm" style="padding:0 10px ;margin:0;color:white; background:#293a4e; border-top: 1px solid white;">
                                                                                                                                                        <a href="users-profile.php?idCollabo=<?php echo isset($auteurReponse['idAuteurComment']) ? $auteurReponse['idAuteurComment'] : ''; ?>&amp;action=voir-profil" style="color:white;">
                                                                                                                                                                <?php echo (isset($auteurReponse['nom']) && isset($auteurReponse['nom'])) ? $auteurReponse['prenom'] . ' ' . $auteurReponse['nom'] : "inconnu";
                                                                                                                                                                unset($auteurReponse); ?>
                                                                                                                                                        </a>
                                                                                                                                                </p>
                                                                                                                                                <div>
                                                                                                                                                        <?php if (isset($repDuCommentaire)) { ?>
                                                                                                                                                                <!-- si ce commentaire est une reponse a un autre commentaire on afficher le commentaire concerné et son auteur -->
                                                                                                                                                                <a href="#commentaire<?php echo $repDuCommentaire['idCommentaire']; ?>" class="commentaire-repondu row">
                                                                                                                                                                        <i class="bi bi-quote " style="font-size: 24px;"></i>
                                                                                                                                                                        <div class="">
                                                                                                                                                                                <h6>
                                                                                                                                                                                        <?php echo (isset($auteurCommentaire['prenom']) && isset($auteurCommentaire['nom'])) ? $auteurCommentaire['prenom'] . ' ' . $auteurCommentaire['nom'] : 'Inconnu';
                                                                                                                                                                                        unset($auteurCommentaire); ?>
                                                                                                                                                                                </h6>


                                                                                                                                                                                <p style="font-size: 16px !important;">
                                                                                                                                                                                        <?php
                                                                                                                                                                                        // Chaîne de commentaire à vérifier
                                                                                                                                                                                        $comment = $repDuCommentaire['commentaire'];

                                                                                                                                                                                        // Expression régulière pour trouver la structure <pre><code>...</code></pre>
                                                                                                                                                                                        $regExp = "/(.|\n)*<pre(.|\n)*><code(.|\n)*>(.|\n)*<\/code><\/pre>(.|\n)*/";
                                                                                                                                                                                        // Vérifie si la chaîne de commentaire correspond à l'expression régulière
                                                                                                                                                                                        if (preg_match($regExp, $comment)) {
                                                                                                                                                                                                $regExp2 = "/<pre(.|\n)*><code(.|\n)*>(.|\n)*<\/code><\/pre>/";
                                                                                                                                                                                                // Remplace la structure <pre><code>...</code></pre> par une chaîne vide dans une copie de $comment
                                                                                                                                                                                                $commentSansCode = preg_replace($regExp2, '', $comment);

                                                                                                                                                                                                // Affiche les premiers 100 caractères de la chaîne sans la structure
                                                                                                                                                                                                echo substr($commentSansCode, 0, 100);

                                                                                                                                                                                                // Si la chaîne est plus longue que 100 caractères, ajoute '...'
                                                                                                                                                                                                if (strlen($commentSansCode) > 100) {
                                                                                                                                                                                                        echo '...';
                                                                                                                                                                                                }
                                                                                                                                                                                        } else {
                                                                                                                                                                                                // Si la structure n'est pas présente, affiche les premiers 100 caractères
                                                                                                                                                                                                echo substr($comment, 0, 100);

                                                                                                                                                                                                // Si la chaîne est plus longue que 100 caractères et ne contient pas la structure, ajoute '...'
                                                                                                                                                                                                if (strlen($comment) > 100) {
                                                                                                                                                                                                        echo '...';
                                                                                                                                                                                                }
                                                                                                                                                                                        }
                                                                                                                                                                                        // Libérer la variable $repDuCommentaire
                                                                                                                                                                                        unset($repDuCommentaire);
                                                                                                                                                                                        ?>
                                                                                                                                                                                </p>
                                                                                                                                                                        </div>
                                                                                                                                                                </a>
                                                                                                                                                        <?php } ?>
                                                                                                                                                        <!-- le contenu du commentaire -->
                                                                                                                                                        <p class="contenu-commentaire " style="margin: 50px 0;">
                                                                                                                                                                <?php echo $reponse['reponse']; ?>
                                                                                                                                                        </p>

                                                                                                                                                </div>

                                                                                                                                        </div>
                                                                                                                                <?php endif; ?>
                                                                                                                                <!-- les reactions -->
                                                                                                                                <div class="d-flex reactionsComment">
                                                                                                                                        <div class="d-flex reactions">
                                                                                                                                                <?php
                                                                                                                                                //recuperer les likes et dislikes
                                                                                                                                                $stmt = $bdd->prepare("SELECT action FROM reaction_commentaire WHERE idCommentaire = ? AND action = 'like'");
                                                                                                                                                $stmt->execute([$reponse['idRep']]);
                                                                                                                                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                $likeCount = count($result);

                                                                                                                                                //recuperer les likes et dislikes
                                                                                                                                                $stmt = $bdd->prepare("SELECT action FROM reaction_commentaire WHERE idCommentaire = ? AND action = 'dislike'");
                                                                                                                                                $stmt->execute([$reponse['idRep']]);
                                                                                                                                                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                                                                                                                $dislikeCount = count($result);
                                                                                                                                                // Préparez la requête SQL pour calculer la moyenne et le nombre de votes
                                                                                                                                                $stmt = $bdd->prepare("SELECT AVG(note) AS moyenne, COUNT(note) AS nombreVotes FROM  reaction_note WHERE idCommentaire = ?");
                                                                                                                                                $stmt->execute([$reponse['idRep']]);
                                                                                                                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                                                                                                                                                //recuperer la moyenne et le nombre de votes
                                                                                                                                                $moyenne = $result['moyenne'] !== null ? round($result['moyenne'], 1) : null;
                                                                                                                                                $totalVote = $result['nombreVotes'] !== null ? $result['nombreVotes'] : null;

                                                                                                                                                //verifier si l'utilisateur a deja reagit au commentaire
                                                                                                                                                $stmt = $bdd->prepare("SELECT action FROM reaction_commentaire WHERE idCommentaire = ? AND idCollabo = ?");
                                                                                                                                                $stmt->execute([$reponse['idRep'], $idCollabo]);
                                                                                                                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                                                                                                if ($result) {
                                                                                                                                                        if ($result['action'] == 'like') {
                                                                                                                                                                $Liked = 'liked';
                                                                                                                                                        } else {
                                                                                                                                                                $Disliked = "disliked";
                                                                                                                                                        }
                                                                                                                                                }
                                                                                                                                                //verifier si l'utilisateur a deja noter le commentaire
                                                                                                                                                $stmt = $bdd->prepare("SELECT * FROM reaction_note WHERE idCommentaire = ? AND idCollabo = ?");
                                                                                                                                                $stmt->execute([$reponse['idRep'], $idCollabo]);
                                                                                                                                                $deja_noter = $stmt->fetch(PDO::FETCH_ASSOC);

                                                                                                                                                ?>
                                                                                                                                                <div>
                                                                                                                                                        <a href="pages-publications.php" class="like likeElement <?php echo isset($Liked) ? $Liked : '';
                                                                                                                                                                                                                        unset($Liked); ?>" data-commentaire-id="<?php echo $reponse['idRep']; ?>" data-proprietaire-id="<?php echo $reponse['idCollabo']; ?>" data-user-id="<?php echo $idCollabo; ?>">

                                                                                                                                                                <i class="fa fa-thumbs-up" style="font-size:24px;"></i>
                                                                                                                                                        </a>
                                                                                                                                                        <p class="like-count" data-like-count="<?php echo $likeCount; ?>"></p>
                                                                                                                                                        <input type="hidden" class="like-count-hidden" value="<?php echo $likeCount; ?>">
                                                                                                                                                </div>
                                                                                                                                                <div>
                                                                                                                                                        <a href="#" class="dislike dislikeElement <?php echo isset($Disliked) ? $Disliked : '';
                                                                                                                                                                                                        unset($Disliked); ?>" data-commentaire-id="<?php echo $reponse['idRep']; ?>" data-proprietaire-id="<?php echo $reponse['idCollabo']; ?>" data-user-id="<?php echo $idCollabo; ?>">
                                                                                                                                                                <i class="fa fa-thumbs-down" style="font-size:24px;"></i>
                                                                                                                                                        </a>
                                                                                                                                                        <p class="dislike-count" data-dislike-count="<?php echo $dislikeCount; ?>"></p>
                                                                                                                                                        <input type="hidden" class="dislike-count-hidden" value="<?php echo $dislikeCount; ?>">
                                                                                                                                                </div>
                                                                                                                                                <div>
                                                                                                                                                        <i class="fa fa-reply bouton-repondre" style="font-size:24px;color:green;"></i>
                                                                                                                                                </div>
                                                                                                                                                <!-- si l'utilisateur n'a pas encore noter le commentaire -- et que le commentaire n'est pas de lui  -->
                                                                                                                                                <?php if (!$deja_noter && $reponse['idCollabo'] !== $idCollabo) :
                                                                                                                                                ?>
                                                                                                                                                        <div class="la-note" style="display:flex;flex-direction:column; align-items:center;">
                                                                                                                                                                <i class="bi bi-star-fill" style="color:#FFD700;" data-toggle="tooltip" data-placement="top" title="Donner une note"></i>
                                                                                                                                                                <select name="note" class="note-select" data-commentaire-id="<?php echo $reponse['idRep']; ?>">
                                                                                                                                                                        <option selected></option>
                                                                                                                                                                        <option value="1">1</option>
                                                                                                                                                                        <option value="2">2</option>
                                                                                                                                                                        <option value="3">3</option>
                                                                                                                                                                        <option value="4">4</option>
                                                                                                                                                                        <option value="5">5</option>
                                                                                                                                                                </select>
                                                                                                                                                        </div>
                                                                                                                                                <?php endif; ?>
                                                                                                                                                <div class="moyenne">
                                                                                                                                                        <p>Moyenne</p>
                                                                                                                                                        <i class="afficher-moyenne"><span class="moyenne"><?php echo isset($moyenne) ? $moyenne . "/5" : '.../5'; ?></span></i>
                                                                                                                                                </div>
                                                                                                                                                <div class="totalVote">
                                                                                                                                                        <p style="font-weight:bold;"> <?php echo isset($totalVote) ? $totalVote . " vote" : '0 vote';
                                                                                                                                                                                        echo (isset($totalVote) && $totalVote > 1) ? 's' : ''; ?></p>
                                                                                                                                                </div>
                                                                                                                                        </div>
                                                                                                                                        <!-- zone de commentaire pour repondre -->
                                                                                                                                        <div class="repondre-commentaire-zone disparaitre">
                                                                                                                                                <form class="post-footer post-foor comment-form repondre-commentaire disparaitre" action="traitement/add-commentaire.php?AuteurQuestion=<?php echo $questionCollabo['idCollabo']; ?>&amp;idPub=<?php echo $questionCollabo['idPub']; ?>&amp;idAuteurCommentaire=<?php echo $reponse['idCollabo']; ?>&amp;idRep=<?php echo $reponse['idRep']; ?>&amp;reponse=<?php echo $reponse['reponse']; ?>&amp;idRepCollabo=<?php echo $reponse['idCollabo']; ?>" method="post">

                                                                                                                                                        <div class="form-group">
                                                                                                                                                                <textarea name="commentaire" class="form-control comment-form reponse-comment-area" placeholder="Repondre a ce commentaire..."></textarea>
                                                                                                                                                                <label for="send-fill" class="btn-envoie-reponse" style="color: blue !important;">
                                                                                                                                                                        <button class="btn btn-transparent p-0 border-0" name="repondre-commentaire">
                                                                                                                                                                                <i class="bi bi-send-fill  fs-5" id="send-fill"></i>
                                                                                                                                                                        </button>
                                                                                                                                                                </label>
                                                                                                                                                        </div>
                                                                                                                                                        <div class="col col-12">
                                                                                                                                                                <div class="row align-items-center">
                                                                                                                                                                        <div class="col-4 btn-ajouter-code disparaitre">
                                                                                                                                                                                <span class=" btn btn-primary dispalay-languages" data-toggle="tooltip" data-placement="top" title="Ajouter un code" style="font-size: 14px;">
                                                                                                                                                                                        Ajouter un code
                                                                                                                                                                                </span>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-6 select-language disparaitre">
                                                                                                                                                                                <select class="form-select form-select-sm" name="add-code" aria-label="Small select example">
                                                                                                                                                                                        <option selected>-- Sélectionner le langage --</option>
                                                                                                                                                                                        <option value="html">HTML</option>
                                                                                                                                                                                        <option value="css">CSS</option>
                                                                                                                                                                                        <option value="php">PHP</option>
                                                                                                                                                                                        <option value="javascript">JavaScript</option>
                                                                                                                                                                                </select>
                                                                                                                                                                        </div>
                                                                                                                                                                </div>
                                                                                                                                                                <p class="note-info-code disparaitre-info" style="color:green;">Si vous ajoutez un code, il se presentera automatiquement de la manière suivante : <br> <span style="color: initial !important;"> &lt;pre&gt;&lt;code&gt; <span style="color: red !important;">le code...</span> &lt;/code&gt;&lt;/pre&gt; </span><br> <span style="color: red !important;">Veuillez le laisser sous ce format pour optimiser l'affichage </span></p>
                                                                                                                                                        </div>
                                                                                                                                                </form>
                                                                                                                                                <form class="code-form-reponse disparaitre" id="code-form">
                                                                                                                                                        <textarea name="" id="" class="leCodeSaisi" placeholder="Saisir ou coller votre code ici..."></textarea>
                                                                                                                                                        <div class="boutons">
                                                                                                                                                                <input class="btn btn-primary bouton btn-confirmer-ajout-code" type="submit" value="Ajouter">
                                                                                                                                                                <input class="btn btn-warning bouton" type="reset" value="Effacer">
                                                                                                                                                                <input class="btn btn-danger bouton btn-annuler-code" type="submit" value="Annuler">
                                                                                                                                                        </div>
                                                                                                                                                </form>
                                                                                                                                        </div>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        <?php
                                                                                                        } ?>
                                                                                                <?php
                                                                                                } ?>
                                                                                        <?php } ?>

                                                                                </div>

                                                                        <?php

                                                                        } ?>

                                                                </div>
                                                        </div>
                                                        <!-- End Content Page Box Area -->
                                                </div>

                                        </div>
                                </div>

                                <!-- enfant 2 -->
                        </div>
                </section>
        </main><!-- End #main -->

        <!-- Inclure les fichier javaScript -->
        <?php require_once "includes/fichiers-js.php"; ?>
        <!-- inclure la bibliotheque Prism pour la coloration syntaxique du code -->
        <script src="includes/prism/prism-okaidia.js"></script>


        <script>
                /***********************************
                 * 
                 * zone de commentaire 
                 * principale
                 *                 
                 ************************************/
                var comment_zone = document.querySelector(".comment-zone");
                //le bouton pour afficher la zone de commentaire principale
                var btn_zone_commentaire_principale = document.querySelector(".btn-zone-commentaire-principale");
                // Sélectionnez la zone de commentaire principale
                var comment_area = comment_zone.querySelector('.comment-area');
                //bouton pour l'ajout de commentaire
                var btn_ajouter_code = comment_zone.querySelector('.btn-ajouter-code');
                //bouton pour afficher les langages de programmation
                var select_language = comment_zone.querySelector('.select-language');
                //la zone de texte du code
                var formulaire_code = comment_zone.querySelector('.code-form');
                //l'info sur l'utilisation de la zone de code
                var disparaitre_info = comment_zone.querySelector('.disparaitre-info');
                // afficher les langage de programmation
                var dispalay_languages = comment_zone.querySelector(".dispalay-languages");
                //pour recueillir le langage choisi
                var language = "";


                //faire apparaitre la zone de commentaire principale
                btn_zone_commentaire_principale.addEventListener("click", function(e) {
                        comment_zone.classList.remove("disparaitre");
                        btn_zone_commentaire_principale.classList.add("disparaitre");
                });

                //lorsque l'utilisateur commence a saisir le texte
                // Ajoutez un écouteur d'événement pour l'événement "input"
                comment_area.addEventListener('input', function(event) {
                        btn_ajouter_code.classList.remove('disparaitre');
                        var regExp = /[<>]/g;

                        if (regExp.test(comment_area.value) && (language == "")) {
                                alert("Alert, presence de caractère non autorisé '<' ou '>'\n Ne saisissez pas directement un code dans votre commentaire.\n Si vous voulez ajouter un code, cliquez sur le bouton en dessous du formulaire et choisissez un langage.");
                        }
                });


                //gerer les ajouts de code
                //faire apparaitre la liste de language
                dispalay_languages.addEventListener('click', function(e) {
                        select_language.classList.remove('disparaitre');
                        //faire apparaitre l'info sur l'utilisation de la zone de code
                        disparaitre_info.classList.remove('disparaitre-info');
                });

                //pour echaper le code html
                function escapeHtml(unsafe) {
                        return unsafe
                                .replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/"/g, "&quot;")
                                .replace(/'/g, "&#039;");
                }


                //evenement sur la selection du langage
                select_language.addEventListener('change', function(event) {
                        //recuperer le langage selectionnee
                        language = this.querySelector('.form-select').value;
                        //faire apparaitre le formulaire de saisie du code apres la selection du langage
                        formulaire_code.classList.remove('disparaitre');
                        //recuperer le code qui a ete saisi
                        var leCodeSaisi = document.querySelector('.leCodeSaisi');
                        //le bouton d'annulation
                        var btn_annuler_code = document.querySelector('.btn-annuler-code');
                        //le bouton de confirmation d'ajout du code
                        var btn_confirmer_ajout_code = document.querySelector('.btn-confirmer-ajout-code');

                        //faire disparaitre le formulaire de saisie du code apres clicque sur annuler
                        btn_annuler_code.addEventListener('click', function(event) {
                                event.preventDefault();
                                formulaire_code.classList.add('disparaitre');
                                //supprimer la valeur saisie dans la zone de code
                                leCodeSaisi.value = "";
                                //reinitialiser la selection du language
                                select_language.querySelector('.form-select').selectedIndex = 0;
                                //faire disparaitre la liste des langages
                                select_language.classList.add('disparaitre');
                                //reinitialiser la selection
                                language = "";
                        });

                        //ajouter le code saisi dans la zone de commentaire
                        btn_confirmer_ajout_code.addEventListener('click', function(event) {
                                event.preventDefault();
                                //recuperer la valeur du code saisi
                                var codeValue = leCodeSaisi.value;

                                //traiter le code 
                                if (language != null) {
                                        //echapper si le langage est html ou markup
                                        codeValue = escapeHtml(codeValue);
                                        //ajouter les balise de delimitation
                                        codeValue = "<pre><code type='text/plain' class='language-" + language + "'><br>" + codeValue + "</code></pre>";
                                }

                                // Récupérer la zone de texte
                                var comment_area = document.querySelector('.comment-area');
                                // Récupérer la position du curseur dans la zone de texte
                                var cursorPos = comment_area.selectionStart;
                                //recuperer le texte avant le curseur
                                var textBeforeCursor = comment_area.value.substring(0, cursorPos);
                                //recuperer le texte apres le curseur
                                var textAfterCursor = comment_area.value.substring(cursorPos);
                                //recoller les differentes parties ensemble
                                comment_area.value = textBeforeCursor + codeValue + textAfterCursor;

                                // Réinitialise la position du curseur après l'ajout de texte
                                var newCursorPos = cursorPos + codeValue.length;

                                comment_area.setSelectionRange(newCursorPos, newCursorPos);
                                //faire disparaitre le formulaire de code
                                formulaire_code.classList.add('disparaitre');
                                //reinitialiser la selection du language
                                select_language.querySelector('.form-select').selectedIndex = 0;
                                //faire disparaitre la liste des langages
                                select_language.classList.add('disparaitre');
                                //supprimer la valeur saisie dans la zone de code
                                leCodeSaisi.value = "";
                        });

                });


                /******************************
                 * ***************************
                 * Zone de reponse au commentaire
                 * 
                 * **************************
                 ******************************/

                // Sélection de l'élément bouton-repondre

                //evenement sur chaque bouton "repondre"
                document.querySelectorAll(".bouton-repondre").forEach(function(boutonRepondre) {
                        boutonRepondre.addEventListener('click', function(e) {
                                console.log("hello!");
                                e.preventDefault();
                                // Trouver le formulaire associé au bouton cliqué
                                var containerFormulaire = this.closest(".container-commentaires").querySelector(".repondre-commentaire-zone");
                                var formReponse = containerFormulaire.querySelector(".repondre-commentaire");
                                var reponse_commentaire_area = formReponse.querySelector(".reponse-comment-area");
                                var btn_ajouter_code_commentaire = formReponse.querySelector(".btn-ajouter-code");
                                var listeLangages = formReponse.querySelector(".select-language");
                                var note_info_code = formReponse.querySelector('.note-info-code');
                                var code_formulaire = containerFormulaire.querySelector(".code-form-reponse");
                                var btn_annuler_code_reponse = code_formulaire.querySelector(".btn-annuler-code");
                                var repnse_btn_confirmer_ajout_code = code_formulaire.querySelector(".btn-confirmer-ajout-code");
                                var reponse_leCodeSaisi = code_formulaire.querySelector(".leCodeSaisi");
                                var langage_choisi = "";

                                // afficher le formulaire de reponse
                                containerFormulaire.classList.remove('disparaitre');
                                formReponse.classList.remove('disparaitre');
                                //faire apparaitre le bouton "ajouter code" lorsqu'on commence a ecrire 
                                reponse_commentaire_area.addEventListener('input', function(e) {
                                        btn_ajouter_code_commentaire.classList.remove('disparaitre');
                                });
                                //faire apparaitre la liste des langages disponibles et la note d'information quand on clique sur "ajouter un code"
                                btn_ajouter_code_commentaire.addEventListener('click', function(e) {
                                        listeLangages.classList.remove('disparaitre');
                                        note_info_code.classList.remove('disparaitre-info');
                                });
                                //faire apparaitre le formulaire de saisi du code quand on choisi le langage
                                listeLangages.addEventListener('change', function(e) {
                                        code_formulaire.classList.remove('disparaitre');
                                        langage_choisi = this.querySelector('.form-select').value;
                                });
                                btn_annuler_code_reponse.addEventListener('click', function(e) {
                                        event.preventDefault();
                                        code_formulaire.classList.add('disparaitre');
                                        //supprimer la valeur saisie dans la zone de code
                                        reponse_leCodeSaisi.value = "";
                                        //reinitialiser la selection du language
                                        listeLangages.querySelector('.form-select').selectedIndex = 0;
                                        //faire disparaitre la liste des langages
                                        listeLangages.classList.add('disparaitre');
                                        //reinitialiser la selection du language
                                        langage_choisi = "";
                                });

                                //ajouter le code saisi dans la zone de commentaire
                                repnse_btn_confirmer_ajout_code.addEventListener('click', function(event) {
                                        event.preventDefault();
                                        //recuperer la valeur du code saisi
                                        var reponse_codeValue = reponse_leCodeSaisi.value;

                                        //traiter le code 
                                        if (langage_choisi != null) {
                                                //echapper si le langage est html ou markup
                                                reponse_codeValue = escapeHtml(reponse_codeValue);
                                                //ajouter les balise de delimitation
                                                reponse_codeValue = "<pre><code type='text/plain' class='language-" + langage_choisi + "'><br>" + reponse_codeValue + "</code></pre>";


                                                // Récupérer la zone de texte
                                                var reponse_comment_area = containerFormulaire.querySelector('.reponse-comment-area');
                                                // Récupérer la position du curseur dans la zone de texte
                                                var cursorPos = reponse_comment_area.selectionStart;
                                                //recuperer le texte avant le curseur
                                                var textBeforeCursor = reponse_comment_area.value.substring(0, cursorPos);
                                                //recuperer le texte apres le curseur
                                                var textAfterCursor = reponse_comment_area.value.substring(cursorPos);
                                                //recoller les differentes parties ensemble
                                                reponse_comment_area.value = textBeforeCursor + '<br><br> Code :' + reponse_codeValue + textAfterCursor;

                                                // Réinitialise la position du curseur après l'ajout de texte
                                                var newCursorPos = cursorPos + reponse_codeValue.length;

                                                reponse_comment_area.setSelectionRange(newCursorPos, newCursorPos);
                                                //faire disparaitre le formulaire de code
                                                code_formulaire.classList.add('disparaitre');
                                                //reinitialiser la selection du language
                                                listeLangages.querySelector('.form-select').selectedIndex = 0;
                                                //faire disparaitre la liste des langages
                                                listeLangages.classList.add('disparaitre');
                                                //supprimer la valeur saisie dans la zone de code
                                                reponse_leCodeSaisi.value = "";
                                        }
                                });
                                // Ajoutez un écouteur d'événement pour l'événement "input"
                                reponse_commentaire_area.addEventListener('input', function(event) {
                                        btn_ajouter_code_commentaire.classList.remove('disparaitre');
                                        var regExp = /[<>]/g;

                                        if (regExp.test(reponse_commentaire_area.value) && (langage_choisi == "")) {
                                                alert("Alert, presence de caractère non autorisé '<' ou '>'\n Ne saisissez pas directement un code dans votre commentaire.\n Si vous voulez ajouter un code, cliquez sur le bouton en dessous du formulaire et choisissez un langage.");
                                        }
                                });

                        });
                });


                /******************************
                 * ***************************
                 * gerer la liste des modifications de la publication 
                 * 
                 * **************************
                 ******************************/
                var modifierPub = document.querySelector(".modifierPub");
                if (modifierPub) {
                        modifierPub.addEventListener("change", function(e) {
                                var selectedOption = this.options[this.selectedIndex];
                                var lien = selectedOption.value;
                                var text = selectedOption.text;
                                if (lien) {
                                        if (text == 'Marquer la question comme resolue')
                                                var conf = confirm("Etes-vous sûr d'avoir resolu le probleme?");
                                        else if (text == 'Annuler la question')
                                                var conf = confirm("Voulez-vous vraiment annuler cette question?");
                                        else if (text == 'Relancer la question')
                                                var conf = confirm("Voulez-vous vraiment relancer cette question?");
                                        if (conf)
                                                window.location.href = lien;
                                }
                        });
                }




                /******************************
                 * ***************************
                 * gerer les reactions 
                 * 
                 * **************************
                 ******************************/
                var likes = document.querySelectorAll(".likeElement");
                var dislikes = document.querySelectorAll(".dislikeElement");
                var notes = document.querySelectorAll(".note-select");
                //les likes
                likes.forEach(function(like) {
                        like.addEventListener("click", function(e) {
                                e.preventDefault();
                                var id_proprietaire = this.getAttribute('data-proprietaire-id');
                                var id_user = this.getAttribute('data-user-id');
                                if (id_proprietaire == id_user) {
                                        alert('Vous ne pouvez pas reagir a votre propre commentaire');
                                } else {
                                        // Réinitialiser la couleur du bouton dislike à sa valeur par défaut
                                        var parent = this.closest('.reactions');
                                        // console.log(parent);
                                        var closestDislike = parent.querySelector('.dislikeElement');
                                        var dislike_count = this.closest('.reactions').querySelector('.dislike-count');
                                        //desactiver le dislike s'il y en a 
                                        if (closestDislike.classList.contains("disliked") && !this.classList.contains("liked")) {
                                                closestDislike.classList.remove("disliked");
                                        }

                                        var idCommentaire = this.getAttribute('data-commentaire-id');
                                        $.post("traitement/reaction-commentaire.php", {
                                                id: idCommentaire,
                                                action: "like"
                                        }, function(data) {
                                                $(like).siblings(".like-count").text(data.likes); // Update displayed like count
                                                $(dislike_count).text(data.dislikes); // Update displayed dislike count
                                                $(like).siblings(".like-count-hidden").val(data.likes); // Update hidden like count
                                                $(like).toggleClass("liked");
                                        }, "json");

                                }
                        });
                });
                // Pré-remplir les comptes de likes au chargement de la page
                var likeCountElements = document.querySelectorAll(".like-count-hidden");
                likeCountElements.forEach(function(element) {
                        var likeCount = element.value;
                        $(element).siblings(".like-count").text(likeCount);
                });

                //les dislikes
                dislikes.forEach(function(dislike) {
                        dislike.addEventListener("click", function(e) {
                                e.preventDefault();

                                var id_proprietaire = this.getAttribute('data-proprietaire-id');
                                var id_user = this.getAttribute('data-user-id');
                                if (id_proprietaire == id_user) {
                                        alert('Vous ne pouvez pas reagir a votre propre commentaire');
                                } else {
                                        // Réinitialiser la couleur du bouton dislike à sa valeur par défaut
                                        var parent = this.closest('.reactions');
                                        var closestLike = parent.querySelector('.likeElement');
                                        var like_count = this.closest('.reactions').querySelector('.like-count');
                                        //desactiver le like s'il y en a 
                                        if (closestLike.classList.contains("liked") && !this.classList.contains("disliked")) {
                                                closestLike.classList.remove("liked");
                                        }

                                        var idCommentaire = this.getAttribute('data-commentaire-id');
                                        // console.log(idCommentaire);

                                        $.post("traitement/reaction-commentaire.php", {
                                                id: idCommentaire,
                                                action: "dislike"
                                        }, function(data) {
                                                $(dislike).siblings(".dislike-count").text(data.dislikes); // Update displayed dislike count
                                                $(like_count).text(data.likes); // Update displayed like count
                                                $(dislike).siblings(".dislike-count-hidden").val(data.dislikes); // Update hidden dislike count
                                                $(dislike).toggleClass("disliked");
                                        }, "json");
                                }
                        });
                });
                // Logique similaire pour pré-remplir les comptes de dislikes en utilisant les éléments dislikeCountElements
                var dislikeCountElements = document.querySelectorAll(".dislike-count-hidden");
                dislikeCountElements.forEach(function(element) {
                        var dislikeCount = element.value;
                        $(element).siblings(".dislike-count").text(dislikeCount);
                });

                //les notes
                notes.forEach(function(note) {
                        note.addEventListener('change', function(e) {
                                var valeur_note = this.value;
                                var idCommentaire = this.getAttribute('data-commentaire-id');
                                var afficher_moyenne = this.closest(".reactions").querySelector('.afficher-moyenne .moyenne');
                                var totalVote = this.closest(".reactions").querySelector('.totalVote p');
                                var conf = confirm("Voulez-vous vraiment attribuer la note de \"" + valeur_note + "\" à ce commentaire?");
                                if (conf) {
                                        $.post("traitement/noter-commentaire.php", {
                                                id: idCommentaire,
                                                note: valeur_note
                                        }, function(data) {
                                                $(afficher_moyenne).text(data.moyenne + '/5'); // mis à jour de la moyenne
                                                // Mise à jour du nombre de votes avec ou sans "s"
                                                var voteText = data.totalVote + ' vote' + (data.totalVote > 1 ? 's' : '');
                                                $(totalVote).text(voteText);
                                        }, "json");
                                        //faire disparaitre la partie de note
                                        var partie_note = this.closest('.la-note');
                                        console.log(partie_note);
                                        partie_note.classList.add('disparaitre');
                                        console.log(partie_note);
                                }
                        });
                });
        </script>




</body>

</html>