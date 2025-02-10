<?php

//authentification obligatoire
require_once("traitement/auth_connect_needed.php");
//connexion a la base de donnees
require_once("includes/bdd-connect.php");


try {
  // Requête pour récupérer les compétences
  $competences_query = "SELECT * FROM competence";
  $competences_stmt = $bdd->query($competences_query);
  $competences = $competences_stmt->fetchAll(PDO::FETCH_ASSOC);

  // Requête pour récupérer les niveaux de maîtrise
  $niveaux_query = "SELECT * FROM nivmaitrise";
  $niveaux_stmt = $bdd->query($niveaux_query);
  $niveaux = $niveaux_stmt->fetchAll(PDO::FETCH_ASSOC);

  if ($collaborateur['imageProfil']) {
    // Afficher l'image
    $imageData = base64_encode($collaborateur['imageProfil']); // Convertir les données de l'image en base64
    $imageType = $collaborateur['imageProfilType']; // Récupérer le type de l'image
    $srcProfil = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image
    unset($imageData);
    unset($imageType);
  }
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}






?>
<!-- fichier pour la section de recherche -->
<link rel="stylesheet" href="rechercher.css">
<!-- fichier pour la section de recherche -->


<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="users-profile.php" class="logo d-flex align-items-center">
      <img src="assets/img/logo.png" alt="">
      <span class="d-none d-lg-block">IntraConnect</span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div><!-- End Logo -->

  <div class="search-bar">
    <form class="search-form d-flex align-items-center" method="POST" action="#">
      <input type="text" id="searchQuery" name="query" placeholder="Rechercher dans la page" title="Enter search keyword">
      <button type="button" id="searchButton" title="Search"><i class="bi bi-search"></i></button>
      <small class="nombreOccur"></small>
    </form>
  </div><!-- End Search Bar -->



  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <li class="nav-item d-block d-lg-none">
        <a class="nav-link nav-icon search-bar-toggle " href="#">
          <i class="bi bi-search"></i>
        </a>
      </li><!-- End Search Icon-->

      <?php include_once("notification.php"); ?>
      <?php include_once("message.php"); ?>

      <li class="nav-item dropdown pe-3">

        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img id="myImage" src="<?php echo isset($srcProfil) ? $srcProfil : "assets/img/profile-inconnu.png"; unset($srcProfil); ?>" alt="Image profil" class="rounded-circle">
          <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo isset($collaborateur['prenom'], $collaborateur['nom']) ? substr($collaborateur['prenom'], 0, 1) . '. ' . $collaborateur['nom'] : 'Non defini' ?></span>
        </a><!-- End Profile Iamge Icon -->

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <?php if (isset($collaborateur)) { ?>
              <h6><?php echo (isset($collaborateur['prenom']) && isset($collaborateur['nom'])) ? $collaborateur['prenom'] . ' ' . $collaborateur['nom'] : 'Non defini' ?></h6>
              <span><?php echo isset($collaborateur['job']) ? $collaborateur['job'] : 'Non defini' ?></span>
            <?php } ?>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <!-- <li>
            <a class="dropdown-item d-flex align-items-center" href="users-profile.php">
              <i class="bi bi-gear"></i>
              <span>Account Settings</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>

          <li>
            <a class="dropdown-item d-flex align-items-center" href="pages-faq.php">
              <i class="bi bi-question-circle"></i>
              <span>Need Help?</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li> -->

          <li>
            <a class="dropdown-item d-flex align-items-center" href="traitement/sign-out.php">
              <i class="bi bi-box-arrow-right"></i>
              <span>Sign Out</span>
            </a>
          </li>

        </ul><!-- End Profile Dropdown Items -->
      </li><!-- End Profile Nav -->

    </ul>
  </nav><!-- End Icons Navigation -->
  <!-- gerer la messagerie -->

</header><!-- End Header -->