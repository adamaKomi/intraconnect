<?php

//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

try {
    //recuperer les dernieres questions
    $stmt = $bdd->prepare("SELECT * FROM pub ORDER BY dateAction DESC LIMIT 3");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //recuperer les formations 
    $stmt = $bdd->prepare("SELECT * FROM formation 
                           WHERE statutFormation = 'ouvert' 
                           OR statutFormation = 'nouveau' 
                           ORDER BY dateAction DESC LIMIT 3");
    $stmt->execute();
    $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Users / Profile - NiceAdmin Bootstrap Template</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Inclure les fichier css -->
    <?php require_once "includes/fichiers-css.php"; ?>
    <!-- fichier css pour le carousel de l'annonce -->
    <link rel="stylesheet" href="includes/annonces.css">
    <!-- police -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap">



    <style>
        .diviseur {
            width: 90%;
            height: 1px;
            margin: 10px auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .question .voir-plus {
            display: block;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>

</head>

<body>

    <!-- ======= Header ======= -->
    <?php require_once("includes/main-header.php") ?>

    <!-- ======= Sidebar ======= -->
    <?php include_once("includes/main-sidebar.php") ?>

    <main id="main" class="main">

        <!-- titre et slogan -->
        <div class="page-title">
            <div>
                <img src="assets/img/logo.png" alt="">
                <!-- <h1 class="titrePage">IntraConnect</h1> -->
            </div>
            <!-- <h2 class="sloganPage">Collaborer autrement</h2> -->
        </div>
        <section class="section">

            <div>
                <div class="row">
                    <div class="col col-8">
                        <!-- inclure le carrousel des annonces -->
                        <?php include_once("includes/annonces-carrousel.php"); ?>
                    </div>
                    <div class="col col-4">
                        <div class="card" >
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptas, officia.</p>
                            <p>Deserunt accusamus sapiente exercitationem veniam rem ipsam fugit nam maiores.</p>
                            <p>Alias aspernatur necessitatibus, a totam quos eveniet laboriosam tempore aperiam.</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col-5">
                            <div class="card question" style="padding: 20px 0;">
                                <h4 style="margin: 30px auto;text-align:center;color:#0c3276;">Dernières questions</h4>
                                <?php if (isset($questions) && count($questions) > 0) : ?>
                                    <?php foreach ($questions as $key => $question) : ?>
                                        <a href="voir-plus-reponse.php?idPub=<?php echo $question['idPub'] ?>&amp;idCollabo=<?php echo $question['idCollabo'] ?>" class="card-body">
                                            <h5><?php echo nl2br(substr($question['titrePub'], 0, 49));
                                                echo strlen($question['titrePub']) > 50 ? '...' : '' ?></h5>
                                            <p><?php echo nl2br(substr($question['descriptionPub'], 0, 79));
                                                echo strlen($question['descriptionPub']) > 80 ? '...' : '' ?></p>
                                        </a>
                                        <div class="diviseur"></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <a href="pages-publications.php" class="li li-link voir-plus">Voirs plus</a>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="card question" style="padding: 20px 0;background:rgba(173, 216, 230, 0.5);">
                                <h4 style="margin: 30px auto;text-align:center;color:#0c3276;">Dernières formations</h4>
                                <?php if (isset($formations) && count($formations) > 0) : ?>
                                    <?php foreach ($formations as $key => $formation) : ?>
                                        <a href="pages-formations.php" class="card-body">
                                            <h5><?php echo nl2br(substr($formation['themeFmt'], 0, 49));
                                                echo strlen($formation['themeFmt']) > 50 ? '...' : '' ?></h5>
                                            <p><?php echo nl2br(substr($formation['descriptionFmt'], 0, 79));
                                                echo strlen($formation['descriptionFmt']) > 80 ? '...' : '' ?></p>
                                        </a>
                                        <div class="diviseur"></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <a href="pages-formations.php" class="li li-link voir-plus">Voirs plus</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>


    </main><!-- End #main -->



    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>

</body>

</html>