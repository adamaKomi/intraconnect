<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

try {
  //recuperer les projets dans lesquels le collaborateur est impliqué
  $stmt = $bdd->prepare("SELECT * FROM projet WHERE idProjet = ANY(SELECT idProjet FROM projetcollaborole WHERE idCollabo =?)");
  $stmt->execute([$idCollabo]);
  $projetsCollabo = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}








?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / CreerPublication - IntraConnect </title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Inclure les fichier css -->
  <?php require_once "includes/fichiers-css.php"; ?>


  <style>
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

    .etoile {
      font-size: 1rem;
      color: red;
      font-weight: bold;
    }
    .formTextArea {
            width: 100% !important;
        }
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <?php require_once("includes/main-header.php") ?>

  <!-- ======= Sidebar ======= -->
  <?php include_once("includes/main-sidebar.php") ?>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">IntraConnect</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Créer une Publication</h5>
                    <p class="text-center small">Entrez vos informations pour créer votre Publication</p>
                  </div>

                  <form class="row g-3 needs-validation" enctype="multipart/form-data" action="traitement/add-pub.php" method="post">
                    <div class="col-12">
                      <label for="yourName" class="form-label">Titre de Publication <span class="obligatoire-etoile">*</span></label>
                      <input type="text" name="titre" class="form-control" id="yourName" value="<?php echo isset($_SESSION['titrePub']) ? $_SESSION['titrePub'] : "";
                                                                                                unset($_SESSION['titrePub']); ?>" required>
                      <div class="invalid-feedback">S'il vous plaît entrez votre nom!</div>
                    </div>
                    <div>
                      <label for="yourDescription">Description du Publication <span class="obligatoire-etoile">*</span></label><br>
                      <textarea id="yourDescription" class="formTextArea" width="50%" name="description" required><?php echo isset($_SESSION['descPub']) ? $_SESSION['descPub'] : "";
                                                                                                                  unset($_SESSION['descPub']); ?></textarea><br>
                    </div>
                    <div class="col-12">
                      <!-- Afficher les projets du collaborateur s'ils existent -->
                      <?php if (count($projetsCollabo) > 0) { ?>
                        <label for="projectName" class="form-label">Projet concerné (optionnel)</label><br>
                        <select id="projectName" name="monProjet">
                          <option style="font-style: italic;" value="">--Mon projet--</option>
                          <?php foreach ($projetsCollabo as $projet) { ?>
                            <option value="<?php echo $projet['nomProjet']; ?>"><?php echo '<italic>' . $projet['nomProjet'] . '</italic>'; ?></option>
                        <?php }
                        } ?>
                        </select>
                    </div>
                    <!-- pas d'images car cela pourrait etre comme un reseau social -->
                    <!-- <div class="col-12">
                      <label for="fileUpload" class="form-label">Télécharger un fichier (optionnel)</label>
                      <input type="file" class="form-control" id="fileUpload" name="image">
                    </div> -->
                    <div class="row mb-2 mt-4">
                      <div class="col-sm-10">
                        <button type="submit" class="btn btn-primary" name="publier">Publier</button>
                        <input type="reset" class="btn btn-warning" value="Effacer">
                      </div>
                    </div>
                    <div class="col col-2 mb-2 w-100">
                      <div class="col-sm-10 w-100">
                        <a class="btn btn-danger" href="pages-publications.php">Annuler</a>
                      </div>
                    </div>
                    <p style="font-style:italic">Obligatoire (<span class="etoile">*</span>)</p>

                    <?php if (isset($_SESSION['pubError'])) { ?>
                      <p><span class="error-message"><i class="bi bi-exclamation-triangle"></i><?php echo $_SESSION['pubError'];
                                                                                                unset($_SESSION['pubError']); ?></span></p>
                    <?php } ?>

                  </form>


                </div>
              </div>


            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <!-- Inclure les fichier javaScript -->
  <?php require_once "includes/fichiers-js.php"; ?>

</body>

</html>