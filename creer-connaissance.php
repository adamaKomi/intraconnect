<?php

//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

try {
    //recuperer les categories
    $stmt = $bdd->prepare("SELECT * FROM categorie");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h1>Connaissance</h1>
            
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row" id="section-annonce">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Partagez-nous vos connaissances</h3>

                            <!-- General Form Elements -->
                            <form method="post" action="traitement/add-connaissance.php">
                                <div class="col mb-3">
                                    <label for="titreConnaissance" class="col-12 col-form-label">Titre <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="titreConnaissance" type="text" class="form-control" name="titre" required value="<?php echo isset($_SESSION['titre']) ? $_SESSION['titre'] : "";
                                                                                                                                    unset($_SESSION['titre']); ?>">
                                    </div>
                                </div>
                                <div class="col mb-3">
                                    <label for="description" class="col-12 col-form-label">Description <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <textarea id="description" class="form-control" style="height: 100px" name="description" required><?php echo isset($_SESSION['description']) ? $_SESSION['description'] : "";
                                                                                                                                            unset($_SESSION['description']); ?></textarea>
                                    </div>
                                </div>
                                <?php if (count($categories) > 0) { ?>
                                    <div class="row mb-3">
                                        <label for="categorie" class="col-12 col-form-label">Choisir une categorie <span class="etoile">*</span></label>
                                        <div class="col-12">
                                            <select class="form-select" name="categorie" id="categorie" required>
                                                <option value="" style="font-style: italic;">-- Choisir une categorie --</option>
                                                <?php foreach ($categories as $key => $categorie) { ?>
                                                    <option value="<?php echo $categorie['idCat'] ?>"><?php echo $categorie['nomCat'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col mb-3">
                                    <label for="motCle" class="col-12 col-form-label">Ajouter des mots-clés séparés par des virgles (,) <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="motCle" type="text" class="form-control" name="motCle" required value="<?php echo isset($_SESSION['motCle']) ? $_SESSION['motCle'] : "";
                                                                                                                            unset($_SESSION['motCle']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary" name="creerConnaissance">Creer connaissance</button>
                                        <input type="reset" class="btn btn-warning" value="Effacer">
                                    </div>
                                </div>
                                <div class="col col-2 mb-3 w-100">
                                    <div class="col-sm-10 w-100">
                                        <a type="submit" class="btn btn-danger" href="pages-annonces.php">Annuler</a>
                                    </div>
                                </div>
                                <p style="font-style:italic">Obligatoire (<span class="etoile">*</span>)</p>
                                <?php if (isset($_SESSION['connaissanceError'])) { ?>
                                    <p><span class="error-message"><?php echo $_SESSION['connaissanceError'];
                                                                    unset($_SESSION['connaissanceError']); ?></span></p>
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