<?php

//besoin de s'authentifier
require "traitement/auth_connect_needed.php";
//se connecter a la base de donnees
require_once('includes/bdd-connect.php');
//inclure les fonctions
include_once("includes/fonctions.php");

try {

    //recupere l'id de la notification dont on a cliquer dessus (s'il y en a)
    if (isset($_GET['idNotification'])) $idNotif = $_GET['idNotification'];

    // Récupérer les notifications_all
    $stmt = $bdd->prepare(
        "SELECT n.*, nc.statut
                        FROM notification n
                        JOIN notificationCollabo nc ON nc.idNotification = n.id
                        WHERE nc.idCollabo = ?
                        ORDER BY nc.statut DESC, nc.dateAction DESC"
    );


    $stmt->execute([$idCollabo]);
    $notifications_all = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // var_dump($notifications_all);
    // exit;

    // Récupérer le nombre de notifications_all non lues
    $stmt = $bdd->prepare("SELECT * FROM notificationcollabo WHERE idCollabo = ? AND statut = ? ");
    $stmt->execute([$idCollabo, "1"]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $nbNewNotifs = count($result);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
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
        .pagetitle{
            margin-bottom: 100px;
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
            <h1>Notifications</h1>
        </div><!-- End Page Title -->

        <section class="section profile">
            <div class="contairner">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-9">                            
                            <?php if (count($notifications_all) > 0) : ?>
                                <?php foreach ($notifications_all as $key => $notification) : ?>
                                    <div class="card mb-4" style="width: 100%;">
                                        <a href="<?php echo $notification['lien']."&idNotification=".$notification['id']; ?>" class="card-body">
                                            <?php if ($notification['statut'] == 1) : ?>
                                                <h5 class="card-title"><?php echo $notification['natureElement']; ?></h5>
                                                <h6><?php echo $notification['titre']; ?></h6>
                                            <?php else : ?>
                                                <p><?php echo $notification['natureElement']; ?></p>
                                                <p><?php echo $notification['titre']; ?></p>
                                            <?php endif ?>
                                            <!-- <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo $notification['titre']; ?></h6> -->
                                            <p class="card-text"><?php echo $notification['contenu']; ?></p>
                                            <!-- <a href="#" class="card-link">Card link</a>
                                            <a href="#" class="card-link">Another link</a> -->
                                        </a>
                                        <p class="d-flex justify-content-around mb-1">
                                            <span><?php echo dateAction($notification['dateAction']); ?></span>
                                            <!-- marquer comme non lu -->
                                            <?php if ($notification['statut'] == 0) : ?>
                                                <a href="traitement/annuler-element.php?idNotification=<?php echo $notification['id']; ?>&amp;action=marquer-notification-comme-non-lu" class="btn btn-warning btn-sm">Marquer comme non lu</a>
                                            <?php else : ?>
                                                <a href="traitement/annuler-element.php?idNotification=<?php echo $notification['id']; ?>&amp;action=marquer-notification-comme-lu" class="btn btn-primary btn-sm">Marquer comme lu</a>
                                            <?php endif;
                                            //recuperer la page actuelle pour la redirection
                                            $_SESSION['page_precedente'] = $_SERVER['REQUEST_URI']; ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <h3 style="text-align:center;" >Aucune notification</h3>
                            <?php endif; ?>
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