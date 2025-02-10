<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

try {
    //recuperer les collaborateur
    $stmt = $bdd->prepare("SELECT c.idCollabo, c.nom, c.prenom, c.job FROM collabo c
                           WHERE NOT EXISTS (SELECT 1 FROM admin WHERE idCollabo = c.idCollabo)
    ");
    $stmt->execute();
    $collabos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
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
            <h1>Nommer un administrateur</h1>
            
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row" id="section-annonce">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Nommer un administrateur</h3>
                            <!-- General Form Elements -->
                            <form method="post" action="traitement/add-administrateur.php" enctype="multipart/form-data" class="formulaire">
                                <div class="col mb-3">
                                    <label for="nomMaitrise" class="col-12 col-form-label">Choisir un collaborateur à nommer <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <!-- afficher la liste des utilisateurs -->
                                        <?php if (isset($collabos) && count($collabos) > 0) : ?>
                                            <select name="newAdmin" class="form-select form-select-sm" aria-label="Small select example">
                                                <option value="" selected>--Selectionner un administrateur--</option>
                                                <?php foreach ($collabos as $key => $collabo) : ?>
                                                    <?php if (isset($collabo['nom'], $collabo['prenom'])) : ?>
                                                        <option value="<?php echo $collabo['idCollabo'] ?>"><?php echo $collabo['prenom'] . ' ' . $collabo['nom'] . ' -- ' . $collabo['job']; ?></option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <!-- s'il n'y a pas d'utilisateur inscrit sur cette plateforme -->
                                        <?php else : ?>
                                            <p>Il n'y a aucun utilisateur à nommer actuellement</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary" name="nommerAdmin">Nommer admin</button>
                                        <input type="reset" class="btn btn-warning" value="Effacer">
                                    </div>
                                </div>
                                <div class="col col-2 mb-3 w-100">
                                    <div class="col-sm-10 w-100">
                                        <a type="submit" class="btn btn-danger" href="users-profile.php">Annuler</a>
                                    </div>
                                </div>
                                <p style="font-style:italic">Obligatoire (<span class="etoile">*</span>)</p>
                                <?php if (isset($_SESSION['maitriseError'])) { ?>
                                    <p><span class="error-message"><?php echo $_SESSION['maitriseError'];
                                                                    unset($_SESSION['maitriseError']); ?></span></p>
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
        var formulaire = document.querySelector('.formulaire');
        formulaire.addEventListener('submit', function(e) {
            e.preventDefault();
            var user_liste = document.querySelector('.form-select');
            var choix = user_liste.options[user_liste.selectedIndex].text;

            // Annuler la sélection si l'utilisateur choisit "Annuler"
            if (user_liste.value == "") {
                alert("Veuillez selectionner un collaborateur");
            } else {
                var conf = confirm('Voulez-vous vraiment nommer \"' + choix + '\" comme administrateur?');
                if (conf) window.location.href = "traitement/add-administrateur.php?newAdmin=" + user_liste.value;
            }
        });
    </script>



</body>

</html>