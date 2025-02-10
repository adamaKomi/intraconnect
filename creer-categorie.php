<?php

//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

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


    <style>
        #section-annonce>div {
            margin: auto;
        }

        #section-annonce .card-title {
            text-align: center;
            font-size: 1.5rem;
        }

        #section-annonce form {
            font-size: 14pt;
        }

        #section-annonce form .etoile {
            font-size: 1rem;
            color: red;
            font-weight: bold;
        }

        .error-message {
            color: #ff0000;
            /* Couleur du texte rouge */
            font-size: 1.4rem !important;
            /* Taille de la police */
            font-weight: bold;
            /* Police en gras */
            margin-bottom: 10px;
            /* Marge en bas pour l'espacement */
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
            <h1>Categorie</h1>           
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row" id="section-annonce">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Definir une categorie</h3>

                            <!-- General Form Elements -->
                            <form method="post" action="traitement/add-categorie.php" enctype="multipart/form-data">
                                <div class="col mb-3">
                                    <label for="nomCategorie" class="col-12 col-form-label">Nom de la categorie<span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="nomCategorie" type="text" class="form-control" name="nomCategorie" required value="<?php echo isset($_SESSION['nomCategorie']) ? $_SESSION['nomCategorie'] : "";
                                                                                                                                        unset($_SESSION['nomCategorie']); ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary" name="definirCategorie">Definir categorie</button>
                                        <input type="reset" class="btn btn-warning" value="Effacer">
                                    </div>
                                </div>
                                <div class="col col-2 mb-3 w-100">
                                    <div class="col-sm-10 w-100">
                                        <a type="submit" class="btn btn-danger" href="creer-categorie.php">Annuler</a>
                                    </div>
                                </div>
                                <p style="font-style:italic">Obligatoire (<span class="etoile">*</span>)</p>
                                <?php if (isset($_SESSION['categorieError'])) { ?>
                                    <p><span class="error-message"><?php echo $_SESSION['categorieError'];
                                                                    unset($_SESSION['categorieError']); ?></span></p>
                                <?php } ?>
                            </form><!-- End General Form Elements -->

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