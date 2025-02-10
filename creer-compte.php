<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Register - IntraConnect </title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Inclure les fichier css -->
  <?php require_once "includes/fichiers-css.php"; ?>

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
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">IntraConnect</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Créer un compte</h5>
                    <p class="text-center small">Entrez les informations pour créer un compte</p>
                  </div>

                  <form class="row g-3 needs-validation" novalidate method="post" action="traitement/add-compte.php">


                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Nom d'utilisateur</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="text" name="username" class="form-control" id="yourUsername" required value="<?php echo isset($_SESSION['usernameExists']) ? $_SESSION['usernameExists'] : ""; unset($_SESSION['usernameExists']);?>">
                        <div class="invalid-feedback">Veuillez choisir un nom d'utilisateur.</div>
                      </div>
                      <?php if (isset($_SESSION['errorUsername']))
                        echo "<br><p style='color:red;font-weight:bold;font-style:italic;'>Username existe deja</p>";
                      unset($_SESSION['errorUsername']); ?>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="yourEmail" required value="<?php echo isset($_SESSION['emailExists']) ? $_SESSION['emailExists'] : ""; unset($_SESSION['emailExists']);?>">
                      <div class="invalid-feedback">S'il vous plaît, mettez une adresse email valide!</div>
                      <?php if (isset($_SESSION['errorEmail']))
                        echo "<br><p style='color:red;font-weight:bold;font-style:italic;'>Cet email existe deja</p>";
                      unset($_SESSION['errorEmail']); ?>
                    </div>
                    <!-- Pour ajouter en tant que Administrateur -->
                    <p style="color: red;font-weight:bold;font-style:italic;"><input class="form-check-input" name="isAdmin" type="checkbox" value="" id="acceptTerms"> L'ajouter comme Administrateur</p>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit" name="createAccount">Créer un compte</button>
                    </div>
                    <div class="col-12">
                      <a class="btn btn-danger w-100" href="users-profile.php">Annuler</a>
                    </div>
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