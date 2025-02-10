<?php
//authentification obligatoire
require_once("traitement/auth_connect_needed.php");
//connexion a la base de donnees
require_once("includes/bdd-connect.php");
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
    $stmt = $bdd->prepare("SELECT * FROM pub 
                            JOIN collabo ON collabo.idCollabo = pub.idCollabo
                        ");
    $stmt->execute();
    $questionsCollabo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($questionsCollabo);
    // exit;


    //recuperer les projets
    $stmt = $bdd->prepare("SELECT * FROM projet");
    $stmt->execute();
    $projets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //listes des formations

    //le cas ou on vient d'une notification
    if (isset($_GET['idFormation'], $_GET['idNotification'])) {
        $idFmt = $_GET['idFormation'];
        //formation concernee par la notification
        $stmt = $bdd->prepare("SELECT * FROM formation JOIN fmtpubliee ON fmtpubliee.idFormation=formation.idFormation  WHERE formation.idFormation = ?");
        $stmt->execute([$idFmt]);
        $formationNotification = $stmt->fetch(PDO::FETCH_ASSOC);

        //recuperer les formations sauf celle concernee par la notification
        $stmt = $bdd->prepare("SELECT * FROM formation f JOIN fmtpubliee fp ON fp.idFormation=f.idFormation WHERE f.statutFormation !='terminé' AND f.statutFormation !='fermé' AND f.statutFormation != 'annulé' AND f.idFormation != ? ORDER BY f.DateFin ASC");
        $stmt->execute([$idFmt]);
        $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //marquer la notification comme lue
        $stmt = $bdd->prepare("UPDATE notificationCollabo SET statut = 0 WHERE idNotification = ? AND idCollabo = ?");
        $stmt->execute([$_GET['idNotification'], $idCollabo]);
    } else {
        //cas normal
        $stmt = $bdd->prepare("SELECT * FROM formation f JOIN fmtpubliee fp ON fp.idFormation=f.idFormation WHERE f.statutFormation !='terminé' AND f.statutFormation !='fermé' AND f.statutFormation != 'annulé' ORDER BY f.DateFin ASC");
        $stmt->execute();
        $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //recuperer la base de connaissances
    $stmt = $bdd->prepare("SELECT co.*, c.nom, c.prenom, c.job  FROM connaissance co NATURAL JOIN collabo c ORDER BY co.dateAction DESC LIMIT 5");
    $stmt->execute();
    $connaissances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($connaissances);
    // exit;
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


    <!-- Inclure les fichier css -->
    <?php require_once "includes/fichiers-css.php"; ?>
    <!-- fichier css pour le carousel de l'annonce -->
    <link rel="stylesheet" href="includes/annonces.css">




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


        /* le side bar pour les connaissances */
        .connaissances {
            box-shadow: none !important;
        }

        .liste-connaissances {
            list-style: none;
            /* border: 1px solid black; */
            padding: 20px 0;
        }

        .liste-connaissances li {
            padding: 10px 3px !important;
            /* border: 0.4px solid black !important; */
            border-radius: 0 !important;
            border-bottom: 1px solid black;
        }

        .liste-connaissances li:hover {
            background-color: #f4f7fc;
        }



        .liste-connaissances li .card-text {
            margin: 0;
            padding: 0;
            text-align: start;
        }

        .liste-connaissances li h5 {
            text-decoration: none !important;
        }


        /*
            la publication
        */
        .pubNotification {
            border: 3px solid red !important;
        }

        .titrePub {
            font-size: 18px !important;
            color: black !important;
            text-align: center !important;
            font-style: italic;
        }

        /* les dates */
        .date {
            background: #f5f9ff;
            padding: 2px 10px 2px 15px;
            height: 100px !important;
        }

        .date-item {
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-around !important;
        }

        .date-item div span:nth-child(1) {
            color: red !important;
        }

        /* .date-item div span {
            text-align: center !important;
        } */

        /* faire disparaitre les elements */
        .disparaitre {
            display: none !important;
        }


        /* image de fond */
        .bg-image {

            background-image: url('img/bg1.jpg');

            height: 100%;

            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>


</head>

<body>


    <!-- ======= Header ======= -->
    <?php require_once("includes/main-header.php") ?>

    <!-- ======= Sidebar ======= -->
    <?php include_once("includes/main-sidebar.php") ?>


    <main id="main" class="main bg-image">
        <div class="pagetitle">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Formations</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <!-- inclure le carrousel des annonces -->
        <?php include_once("includes/annonces-carrousel.php"); ?>
        <section class="section profile">
            <div class="main-content-wrapper d-flex flex-row justify-content-between row" style="display: flex !important;flex-direction: row !important; justify-content: space-between !important; margin-left:50px;">
                <!-- Start Main Content Wrapper Area -->

                <!-- enfant 1 -->
                <div class="content-page-box-area col-lg-8 col-md-12">
                    <h1>Formations a venir</h1>

                    <div class="row">
                        <div class="main-content-wrapper d-flex flex-column ">
                            <div class="col-lg-12 col-md-12">
                                <div class="news-feed-area">
                                    <!-- la formation qui vient des notifications -->
                                    <?php if (isset($formationNotification)) : ?>
                                        <div class="news-feed news-feed-post publications pubNotification " id="pointer-sur<?php echo $formationNotification['idFormation']; ?>">
                                            <div class="post-header d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span style="color:<?php
                                                                        if ($formationNotification['statutFormation'] == 'fermé')
                                                                            echo 'red;';
                                                                        elseif ($formationNotification['statutFormation'] == 'ouvert')
                                                                            echo 'green;';
                                                                        elseif ($formationNotification['statutFormation'] == 'nouveau')
                                                                            echo 'blue;';
                                                                        ?>"><?php echo $formationNotification['statutFormation']; ?></span>
                                                </div>
                                                <!-- Annuler la formatoin -->
                                                <?php if (($formationNotification['idCollabo'] == $idCollabo)) { ?>
                                                    <div>
                                                        <?php if ($formationNotification['statutFormation'] == 'nouveau') { ?>
                                                            <!-- supprimer la formation si le statut est encore nouveau -->
                                                            <a href="traitement/supprimer-element.php?idFormation=<?php echo $formationNotification['idFormation']; ?>&amp;action=supprimer-formation" class="supprimer-formation" data-bs-toggle="tooltip" data-bs-placement="top" title="supprimer la formation">
                                                                <i class="bi bi-trash-fill" style="font-size: 18px;color:red;margin-right:20px;"></i>
                                                            </a>
                                                        <?php
                                                            //recuperer la page actuelle pour la redirection
                                                            $_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];
                                                        } ?>
                                                        <!-- annuler la formation  -->
                                                        <?php if ($formationNotification['statutFormation'] != 'annulé') { ?>
                                                            <a href="traitement/annuler-element.php?idFormation=<?php echo $formationNotification['idFormation'];
                                                                                                                $_SESSION['page_precedente'] = $_SERVER['REQUEST_URI']; ?>&amp;action=annuler-formation" class="annuler-formation" data-bs-toggle="tooltip" data-bs-placement="top" title="Annuler la formation">
                                                                <i class="bi bi-sign-turn-left" style="font-size: 18px;color:blue;"></i>
                                                            </a>
                                                        <?php } ?>
                                                        <!-- marquer comme terminee si elle est fermee -->
                                                        <?php if (($formationNotification['statutFormation'] == 'fermé')) { ?>
                                                            <a href="traitement/annuler-element.php?idFormation=<?php echo $formationNotification['idFormation']; ?>&amp;action=marquer-formation-terminee" class="terminer-formation" data-bs-toggle="tooltip" data-bs-placement="top" title="Marquer comme terminée">
                                                                <i class="bi bi-box-arrow-in-right" style="font-size:18px;color:green;margin-left:10px;"></i>
                                                            </a>
                                                        <?php
                                                            //recuperer la page actuelle pour la redirection
                                                            $_SESSION['page_precedente'] = $_SERVER['REQUEST_URI'];
                                                        } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <div class="post-body">
                                                <div>
                                                    <p class="titrePub">
                                                        <!-- le titre de la publication -->
                                                        <?php echo isset($formationNotification['themeFmt']) ? nl2br($formationNotification['themeFmt']) : "" ?>
                                                    </p>
                                                    <p class="description" style="color: #143D59 !important;">
                                                        <!-- le contenu de la publication (texte) -->
                                                        <?php echo isset($formationNotification['descriptionFmt']) ? nl2br($formationNotification['descriptionFmt']) : "" ?>
                                                    </p>
                                                </div>



                                                <!-- les dates cles de la formation -->
                                                <div class="date d-block mt-3">
                                                    <div class="date-item">
                                                        <div>
                                                            <span>Debut des inscriptions: </span>
                                                            <span><?php echo date("d-m-Y", strtotime($formationNotification['DateDebut'])); ?></span>
                                                        </div>
                                                        <div>
                                                            <span>Deadline: </span>
                                                            <span><?php echo date("d-m-Y", strtotime($formationNotification['DateFin'])); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- la date de publication -->
                                                <p style="font-size: 12px;text-align:end;display:block;margin:10px 0 0 0;font-weight:bold;">
                                                    <?php
                                                    echo dateAction($formationNotification['dateAction']);
                                                    ?>
                                                </p>
                                                <!-- lien d'inscription si le statut est ouvert -->
                                                <?php
                                                //verifier si l'utilisateur n'est pas deja inscrit
                                                $stmt = $bdd->prepare("SELECT * FROM inscritfmt WHERE idFormation = ? AND idCollabo = ?");
                                                $stmt->execute([$formationNotification['idFormation'], $idCollabo]);
                                                $result = $stmt->fetch(PDO::FETCH_ASSOC);


                                                if (($formationNotification['statutFormation'] == 'ouvert') && !$result) : ?>
                                                    <div>
                                                        <a class="btn btn-primary inscriptionFormation" type="submit" href="" data-idFormation="<?php echo $formationNotification['idFormation']; ?>">S'inscrire</a>
                                                    </div>
                                                <?php elseif ($result) : ?>
                                                    <div>
                                                        <span style="color : green;" data-bs-toggle="tooltip" data-bs-placement="top" title="Vous etes inscrit a cette formation">
                                                            <i class="bi bi-brightness-high-fill"></i>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($formations) && (count($formations) > 0)) : ?>
                                        <!-- pour toutes les autres formations -->
                                        <?php foreach ($formations as $key => $formation) :

                                        ?>
                                            <div class="news-feed news-feed-post publications" id="pointer-sur<?php echo $formation['idFormation']; ?>">
                                                <div class="post-header d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span style="color:<?php
                                                                            if ($formation['statutFormation'] == 'fermé')
                                                                                echo 'red;';
                                                                            elseif ($formation['statutFormation'] == 'ouvert')
                                                                                echo 'green;';
                                                                            elseif ($formation['statutFormation'] == 'nouveau')
                                                                                echo 'blue;';
                                                                            ?>"><?php echo $formation['statutFormation']; ?></span>
                                                    </div>
                                                    <!-- Annuler la formatoin -->
                                                    <?php if (($formation['idCollabo'] == $idCollabo)) { ?>
                                                        <div>
                                                            <?php if ($formation['statutFormation'] == 'nouveau') { ?>
                                                                <!-- supprimer la formation si le statut est encore nouveau -->
                                                                <a href="traitement/supprimer-element.php?idFormation=<?php echo $formation['idFormation']; ?>&amp;action=supprimer-formation" class="supprimer-formation" data-bs-toggle="tooltip" data-bs-placement="top" title="supprimer la formation">
                                                                    <i class="bi bi-trash-fill" style="font-size: 18px;color:red;margin-right:20px;"></i>
                                                                </a>
                                                            <?php } ?>
                                                            <!-- modifier la formation  -->
                                                            <a href="traitement/annuler-element.php?idFormation=<?php echo $formation['idFormation']; ?>&amp;action=annuler-formation" class="annuler-formation" data-bs-toggle="tooltip" data-bs-placement="top" title="Annuler la formation">
                                                                <i class="bi bi-sign-turn-left" style="font-size: 18px;color:blue;"></i>
                                                                <!-- <i class="bi bi-sign-turn-left-fill"></i> -->
                                                            </a>
                                                            <!-- marquer comme terminee si elle est fermee -->
                                                            <?php if (($formation['statutFormation'] == 'fermé')) { ?>
                                                                <a href="traitement/annuler-element.php?idFormation=<?php echo $formation['idFormation']; ?>&amp;action=marquer-formation-terminee" class="terminer-formation" data-bs-toggle="tooltip" data-bs-placement="top" title="Marquer comme terminée">
                                                                    <i class="bi bi-box-arrow-in-right" style="font-size:18px;color:green;margin-left:10px;"></i>
                                                                </a>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <div class="post-body">
                                                    <div>
                                                        <p class="titrePub">
                                                            <!-- le titre de la publication -->
                                                            <?php echo isset($formation['themeFmt']) ? nl2br($formation['themeFmt']) : "" ?>
                                                        </p>
                                                        <p class="description" style="color: #143D59 !important;">
                                                            <!-- le contenu de la publication (texte) -->
                                                            <?php echo isset($formation['descriptionFmt']) ? nl2br($formation['descriptionFmt']) : "" ?>
                                                        </p>
                                                    </div>



                                                    <!-- les dates cles de la formation -->
                                                    <div class="date d-block mt-3">
                                                        <div class="date-item">
                                                            <div>
                                                                <span>Debut des inscriptions: </span>
                                                                <span><?php echo date("d-m-Y", strtotime($formation['DateDebut'])); ?></span>
                                                            </div>
                                                            <div>
                                                                <span>Deadline: </span>
                                                                <span><?php echo date("d-m-Y", strtotime($formation['DateFin'])); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- la date de publication -->
                                                    <p style="font-size: 12px;text-align:end;display:block;margin:10px 0 0 0;font-weight:bold;">
                                                        <?php
                                                        echo dateAction($formation['dateAction']);
                                                        ?>
                                                    </p>
                                                    <!-- lien d'inscription si le statut est ouvert -->
                                                    <?php
                                                    //verifier si l'utilisateur n'est pas deja inscrit
                                                    $stmt = $bdd->prepare("SELECT * FROM inscritfmt WHERE idFormation = ? AND idCollabo = ?");
                                                    $stmt->execute([$formation['idFormation'], $idCollabo]);
                                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);


                                                    if (($formation['statutFormation'] == 'ouvert') && !$result && ($formation['idCollabo'] !== $idCollabo)) : ?>
                                                        <div>
                                                            <a class="btn btn-primary inscriptionFormation" type="submit" href="" data-idFormation="<?php echo $formation['idFormation']; ?>">S'inscrire</a>
                                                        </div>
                                                    <?php elseif ($result) : ?>
                                                        <div>
                                                            <span style="color : green;" data-bs-toggle="tooltip" data-bs-placement="top" title="Vous etes inscrit a cette formation">
                                                                <i class="bi bi-brightness-high-fill"></i>
                                                            </span>
                                                        </div>
                                                    <?php endif ?>

                                                </div>
                                            </div>
                                        <?php endforeach; //fin de foreach    
                                        ?>

                                        <!-- s'il n'y a pas de formation a venir -->
                                    <?php elseif (!isset($formationNotification)) : ?>
                                        <h3>Aucune formations a venir...</h3>
                                    <?php endif; //fin de if (isset($formations) && (count($formations) > 0)) 
                                    ?>
                                </div>
                            </div>
                            <!-- End Content Page Box Area -->
                        </div>
                    </div>
                </div>

                <!-- enfant 2 -->
                <div class="container content-page-box-area col-lg-3 col-md-12 right-col">
                    <div class="row">
                        <div class="main-content-wrapper d-flex flex-column">
                            <div class="col-lg-12 col-md-12">
                                <div class="news-feed-area">
                                    <div class="news-feed news-feed-post text-center">
                                        <h2 class="card-title">Connaissances partagées</h2>
                                        <?php if (isset($connaissances) && count($connaissances) > 0) : ?>
                                            <div class=" post-body card connaissances">
                                                <div class="card-body">
                                                    <!-- List group Numbered -->
                                                    <ul class="liste-connaissances">
                                                        <?php foreach ($connaissances as $key => $connaissance) : ?>
                                                            <li class="list-connaissances-item post-body">
                                                                <a href="pages-connaissances.php?idConnaissance=<?php echo $connaissance['id']; ?>" class="d-block w-100 h-100">
                                                                    <p class="card-text " style="color: #143D59 !important;"><span style="color:#293a4e;font-weight:bold;"><?php echo ($key + 1) . '- ' ?></span><?php echo substr($connaissance['titreConn'], 0, 59); ?></p>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul><!-- End List group Numbered -->

                                                </div>
                                            </div>
                                            <a href="pages-connaissances.php"><i class="bi bi-link-45deg"></i>Voir plus</a>
                                        <?php else : ?>
                                            <div>
                                                <p>Aucune connaissance n'a été partagée pour le moment</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>
    </main><!-- End #main -->
    <!-- End Main Content Wrapper Area -->


    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>

    <script>
        var inscriptionFormation = document.querySelectorAll('.inscriptionFormation');
        inscriptionFormation.forEach(function(inscription) {
            inscription.addEventListener('click', function(e) {
                e.preventDefault();
                // var lien = this.getAttribute('href');
                var idFormation = this.getAttribute('data-idFormation');
                console.log(idFormation);
                var conf = confirm("Voulez-vous vraiment vous inscrire à cette formation?");
                if (conf) {
                    this.classList.add('disparaitre');
                    $.post("traitement/inscription-formation.php", {
                        idFormation: idFormation,
                        action: "inscriptionFormation"
                    }, function(data) {
                        // Traitement de la réponse du serveur
                        if (data.success) {
                            // Inscription réussie
                            alert("Inscription réussie");
                            // Redirection vers une autre page
                        } else {
                            // Gérer l'erreur si nécessaire
                            alert("Erreur lors de l'inscription");
                        }
                    }, "json");

                }
            });
        });

        //gerer la suppression de la formation
        var supprimerFormation = document.querySelectorAll(".supprimer-formation");
        supprimerFormation.forEach(function(supp) {
            supp.addEventListener("click", function(e) {
                e.preventDefault();
                var lien = this.getAttribute("href");
                var conf = confirm("Voulez-vous vraiment supprimer cette formation? Cette action est irreversible!!!");
                if (conf) {
                    window.location.href = lien;
                }

            })
        })
        var annulerFormation = document.querySelectorAll(".annuler-formation");
        annulerFormation.forEach(function(annuler) {
            annuler.addEventListener("click", function(e) {
                e.preventDefault();
                var lien = this.getAttribute("href");
                var conf = confirm("Voulez-vous vraiment annuler cette formation? Cette action est irreversible!!!");
                if (conf) {
                    window.location.href = lien;
                }

            })
        })
    </script>

</body>

</html>