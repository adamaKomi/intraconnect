<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");

if(isset($_GET['action']) && $_GET['action'] ==='envoieReussi'){
  echo "

                <script>
                    alert('Envoie Reussi');
                </script>
            
            ";
            unset($_POST['envoieReussi']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Contact</title>
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

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Feedback</h1>
    </div><!-- End Page Title -->

    <section class="section contact">

      <div class="row gy-4">

        <div class="col-xl-6" >
          <div class="card p-4">
            <h1 class="card-title" >Envoyer un Feedback</h1>
            <form action="traitement/add-feedback.php" method="post" >

              <div class="row gy-4">

                <div class="col-md-12">
                  <input type="text" class="form-control" name="sujet" placeholder="Sujet" required>
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="contenu" rows="6" placeholder="Message" required></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <button type="submit" name="feedback" class="btn btn-primary" >Send Message</button>
                </div>

              </div>
            </form>
          </div>

        </div>

      </div>

    </section>

  </main><!-- End #main -->

  <!-- Inclure les fichier javaScript -->
  <?php require_once "includes/fichiers-js.php"; ?>


</body>

</html>