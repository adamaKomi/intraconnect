<?php

//authentification obligatoire
require_once("traitement/auth_connect_needed.php");
//connexion a la base de donnees
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
            <h1>Annonce</h1>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row" id="section-annonce">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Creation d'une annonce</h3>

                            <!-- General Form Elements -->
                            <form method="post" action="traitement/add-annonce.php" enctype="multipart/form-data">
                                <div class="col mb-3">
                                    <label for="titreAnnonce" class="col-12 col-form-label">Titre <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="titreAnnonce" type="text" class="form-control" name="titre" required value="<?php echo isset($_SESSION['titreAnnonce']) ? $_SESSION['titreAnnonce'] : "";
                                                                                                                                unset($_SESSION['titreAnnonce']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="description" class="col-12 col-form-label">Description <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <textarea id="description" class="form-control" style="height: 100px" name="description" required><?php echo isset($_SESSION['descriptionAnnonce']) ? $_SESSION['descriptionAnnonce'] : "";
                                                                                                                                            unset($_SESSION['descriptionAnnonce']); ?></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="lien" class="col-12 col-form-label">Lien</label>
                                    <div class="col-12">
                                        <input id="lien" type="text" class="form-control" name="lien" value="<?php echo isset($_SESSION['lienAnnonce']) ? $_SESSION['lienAnnonce'] : "";
                                                                                                                unset($_SESSION['lienAnnonce']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="date" class="col-12 col-form-label">Date de fin de l'annonce <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="date" type="date" class="form-control" name="date" value="<?php echo isset($_SESSION['dateAnnonce']) ? $_SESSION['dateAnnonce'] : "";
                                                                                                                unset($_SESSION['dateAnnonce']); ?>" required >
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="imageAnnonce" class="col-12 col-form-label">Choisir une image (optionnel)</label>
                                    <div class="col-12">
                                        <input class="form-control" type="file" id="imageAnnonce" name="image" value="<?php echo isset($_SESSION['imageAnnonce']) ? $_SESSION['imageAnnonce'] : "";
                                                                                                                        unset($_SESSION['imageAnnonce']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary" name="creerAnnonce">Creer annonce</button>
                                        <input type="reset" class="btn btn-warning" value="Effacer">
                                    </div>
                                </div>
                                <div class="col col-2 mb-3 w-100">
                                    <div class="col-sm-10 w-100">
                                        <a type="submit" class="btn btn-danger" href="pages-annonces.php">Annuler</a>
                                    </div>
                                </div>
                                <p style="font-style:italic">Obligatoire (<span class="etoile">*</span>)</p>
                                <?php if (isset($_SESSION['annonceError'])) { ?>
                                    <p class="error-message fs-16"><?php echo $_SESSION['annonceError'];
                                                                    unset($_SESSION['annonceError']); ?></p>
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



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('imageAnnonce').addEventListener('change', function(event) {
                var fileInput = event.target;
                var file = fileInput.files[0]; // Obtenir le premier fichier sélectionné

                if (file) {
                    var maxSize = 10 * 1024 * 1024; // Taille maximale autorisée en octets (10 Mo)
                    if (file.size > maxSize) {
                        alert("Erreur: La taille du fichier dépasse la limite maximale (10 Mo)");
                        // Réinitialiser l'élément d'entrée de fichier pour effacer la sélection
                        fileInput.value = '';
                    }
                }
            });

            // Écouter l'événement de soumission du formulaire
            document.getElementById('annonceForm').addEventListener('submit', function(event) {
                var fileInput = document.getElementById('imageAnnonce');
                var file = fileInput.files[0]; // Obtenir le premier fichier sélectionné

                if (file && file.size > maxSize) {
                    // Annuler l'événement de soumission du formulaire
                    event.preventDefault();
                    alert("Erreur: La taille du fichier dépasse la limite maximale (10 Mo)");
                }
            });
        });
    </script>


</body>

</html>