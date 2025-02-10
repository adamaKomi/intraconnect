<?php

//authentification obligatoire
require_once("traitement/auth_connect_needed.php");
//connexion a la base de donnees
require_once("includes/bdd-connect.php");

try {

    //recuperer les utilisateur 
    $stmt = $bdd->prepare("SELECT * FROM collabo ORDER BY prenom");
    $stmt->execute();
    $collaborateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h1>Formation</h1>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row" id="section-annonce">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Planification d'une formation</h3>

                            <!-- General Form Elements -->
                            <form method="post" action="traitement/add-formation.php" enctype="multipart/form-data">
                                <div class="col mb-3">
                                    <label for="themeFormation" class="col-12 col-form-label">Thème <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="themeFormation" type="text" class="form-control" name="theme" required value="<?php echo isset($_SESSION['themeFormation']) ? $_SESSION['themeFormation'] : "";
                                                                                                                                    unset($_SESSION['themeFormation']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="description" class="col-12 col-form-label">Description <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <textarea id="description" class="form-control" style="height: 100px" name="description" required><?php echo isset($_SESSION['description']) ? $_SESSION['description'] : "";
                                                                                                                                            unset($_SESSION['description']); ?></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="dateDebut" class="col-12 col-form-label">Date de debut <span class="etoile">*</span></label>
                                    <div class="col-3">
                                        <input id="dateDebut" type="date" class="form-control" name="dateDebut" required value="<?php echo isset($_SESSION['dateDebut']) ? $_SESSION['dateDebut'] : "";
                                                                                                                                unset($_SESSION['dateDebut']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="dateFin" class="col-12 col-form-label">Date de fin <span class="etoile">*</span></label>
                                    <div class="col-3">
                                        <input id="dateFin" type="date" class="form-control" name="dateFin" required value="<?php echo isset($_SESSION['dateFin']) ? $_SESSION['dateFin'] : "";
                                                                                                                            unset($_SESSION['dateFin']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="horaire" class="col-12 col-form-label">Volume horaire <span class="etoile">*</span></label>
                                    <div class="col-3">
                                        <input id="horaire" type="number" class="form-control" name="horaire" required value="<?php echo isset($_SESSION['horaire']) ? $_SESSION['horaire'] : "";
                                                                                                                                unset($_SESSION['horaire']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="lien" class="col-12 col-form-label">Lien <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="lien" type="text" class="form-control" name="lien" required value="<?php echo isset($_SESSION['lienFormation']) ? $_SESSION['lienFormation'] : "";
                                                                                                                        unset($_SESSION['lienFormation']); ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="lien" class="col-12 col-form-label">Ajouter un formateur(<span class="etoile">séparés par des virgules (,) s'ils sont plusieurs</span>) </label>
                                    <div class="col-12">
                                        <input id="formateur" type="text" class="form-control" name="formateur" required value="<?php echo isset($_SESSION['formateur']) ? $_SESSION['formateur'] : "";
                                                                                                                        unset($_SESSION['formateur']); ?>">
                                    </div>
                                </div>
                                <!-- <?php if (count($collaborateurs) > 0) { ?>
                                    <div class="row mb-3">
                                        <label for="categorie" class="col-12 col-form-label">Designer les formateurs (optionnel) </label>
                                        <div class="col-12">
                                            <select class="form-select" name="formateurs[]" id="categorie" multiple>
                                                <option value="" style="font-style: italic;">-- Choisir formateurs --</option>
                                                <?php foreach ($collaborateurs as $key => $collaborateur) {
                                                    if (!empty($collaborateur['prenom']) && !empty($collaborateur['nom'])) { ?>
                                                        <option value="<?php echo $collaborateur['idCollabo'] ?>"><?php
                                                                                                                    echo $collaborateur['prenom'] . ' ' . $collaborateur['nom']; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                            <?php if (isset($_SESSION['catError'])) { ?>
                                                <p class="text-danger"><?php echo $_SESSION['catError'];
                                                                        unset($_SESSION['catError']); ?></p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?> -->
                                <div class="row mb-3">
                                    <div class="form-check form-check-lg">
                                        <input id="annonceFormation" type="checkbox" class="form-check-input border border-dark" name="annonceFormation">
                                        <label for="annonceFormation" class="form-check-label" style="font-weight: bold;color:red;">Associer a une annonce ?</label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary" name="planifierFormation">Planifier</button>
                                        <input type="reset" class="btn btn-warning" value="Effacer">
                                    </div>
                                </div>
                                <div class="col col-2 mb-3 w-100">
                                    <div class="col-sm-10 w-100">
                                        <a type="submit" class="btn btn-danger" href="pages-formations.php">Annuler</a>
                                    </div>
                                </div>
                                <p style="font-style:italic">Obligatoire (<span class="etoile">*</span>)</p>
                                <?php if (isset($_SESSION['formationError'])) { ?>
                                    <p><span class="error-message"><i class="bi bi-exclamation-triangle"></i><?php echo $_SESSION['formationError'];
                                                                                                                unset($_SESSION['formationError']); ?></span></p>
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