<?php

//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

try {
    //recuperer les questions
    $stmt = $bdd->prepare("SELECT * FROM pub LIMIT 3");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        .page-title .titrePage {
            color: #012970;
            font-family: 'Georgia', serif;
            margin-bottom: 20px;
        }

        .page-title .sloganPage {
            color: #012970;
            font-family: 'Georgia', serif;
            text-align: center;
            font-size: 1.6rem;
        }

        .section-item {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
        }

        .section-item .partie-gauche {
            width: 70%;
        }

        .section-item>.partie-gauche>.partie-bas {
            display: flex;
            flex-direction: column;
        }

        .partie-bas-1 {
            display: flex;
            flex-direction: row;
            margin-top: 100px;
        }

        .partie-bas-1 .forum {
            margin-right: 1rem;
        }

        .partie-bas-2 {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            margin-top: 100px;
        }

        .partie-bas-2-item {
            background-color: white;
            padding: 10px 5px;
            margin-right: 10px;
            border-radius: 10px;
        }

        .partie-questions-item {
            padding: 10px 25px;
            border-bottom: 1px solid black;
        }


        .section-item .partie-droite {
            padding: 100px 1rem;
            width: 25%;
            /* background-color: white; */
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
        }

        .partie-droite-item {
            background-color: white;
            padding: 10px 5px;
        }

        .dashboard {
            width: 100%;
            justify-content: center;
            padding: auto;
        }

        .dashboard span {
            width: 60%;
            margin: 3px auto;
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
                <h1 class="titrePage">IntraConnect</h1>
            </div>
            <h2 class="sloganPage">Collaborer autrement</h2>
        </div>
        <section class="section">
            <div class="section-item">

                <!-- partie centrale -->
                <div class="partie-gauche">
                    <!-- annonces -->
                    <!-- inclure le carrousel des annonces -->
                    <?php include_once("includes/annonces-carrousel.php"); ?>
                    <!-- partie du bas -->
                    <div class="partie-bas">
                        <!-- haut -->
                        <div class="partie-bas-1">
                            <div class="forum">
                                <span class="btn btn-primary">Forum</span>
                            </div>
                            <div>
                                <span class="btn btn-primary">Projets</span>
                            </div>
                        </div>
                        <!-- bas -->
                        <div class="partie-bas-2">
                            <!-- questions -->
                            <div class="partie-questions partie-bas-2-item">
                                <h3>Questions</h3>
                                <?php if (isset($questions) && count($questions) > 0) : ?>
                                    <?php foreach ($questions as $key => $question) : ?>
                                        <div class="card">
                                            <h5><?php echo nl2br(substr($question['titrePub'], 0, 49));
                                                echo strlen($question['titrePub']) > 50 ? '...' : '' ?></h5>
                                            <p><?php echo nl2br(substr($question['descriptionPub'], 0, 79));
                                                echo strlen($question['descriptionPub']) > 80 ? '...' : '' ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <a href="#">Voirs plus</a>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <div class="card info-card sales-card">                                    
                                    <div class="card-body col">
                                        <h5 class="card-title">Les projets</h5>
                                        <div class="row">
                                            <div class="col-6">
                                                <span class="">Mois dernier</span>
                                                <div class="d-flex align-items-center ">
                                                    <div class="ps-3">
                                                        <h6><?php echo isset($nb_projets_mois_dernier) ? $nb_projets_mois_dernier : 0; ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <span class="">Ce mois</span>
                                                <div class="d-flex align-items-center">
                                                    <div class="ps-3">
                                                        <h6><?php echo isset($nb_projets_mois_courant) ? $nb_projets_mois_courant : 0; ?></h6>
                                                        <span class="text-<?php echo isset($infoProjet['couleur']) ? $infoProjet['couleur'] : ''; ?> small pt-1 fw-bold">
                                                            <?php echo isset($infoProjet) ? $infoProjet['pourcentage'] : 0 ?>
                                                        </span>
                                                        <span class="text-muted small pt-2 ps-1">
                                                            <?php echo isset($infoProjet['couleur']) ? ($infoProjet['couleur'] == 'danger' ? 'decrease' : ($infoProjet['couleur'] == 'success' ? 'increase' : 'static')) : '' ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- End Sales Card -->
                        </div>
                        <!-- connaissances -->
                        <div class="partie-bas-2-item">
                            Parties...
                            <div>
                                <h4>Ma question est de savoir......</h4>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</p>
                            </div>
                            <div>
                                <h4>Ma question est de savoir......</h4>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</p>
                            </div>
                            <div>
                                <h4>Ma question est de savoir......</h4>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</p>
                            </div>
                        </div>
                        <!-- derniers projets -->
                        <div class="partie-bas-2-item">
                            Partie projets
                            <div>
                                <h4>Ma question est de savoir......</h4>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</p>
                            </div>
                            <div>
                                <h4>Ma question est de savoir......</h4>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</p>
                            </div>
                            <div>
                                <h4>Ma question est de savoir......</h4>
                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- partie droite -->
            <div class="partie-droite">
                <!-- formations -->
                <div class="partie-droite-item">
                    Partie formations
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                </div>
                <!-- connaissances -->
                <div class="partie-droite-item">
                    Partie connaissances
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                    <div>
                        <h5>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolore, modi!</h5>
                    </div>
                </div>
                <!-- dashboard -->
                <div class="partie-droite-item dashboard">
                    <div>
                        Plus de reponses au cours du mois:
                        <ol>
                            <li>Ali Ki</li>
                            <li>Jean Dubois</li>
                            <li>Kadi Toe</li>
                        </ol>
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