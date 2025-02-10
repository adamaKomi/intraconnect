<aside id="sidebar" class="sidebar">
  <?php if (isset($collaborateur['nom'], $collaborateur['prenom']) && !empty($collaborateur['nom']) && !empty($collaborateur['prenom'])) : ?>
    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="dash.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-heading">Pages</li>

      <li class="nav-item">
        <a class="nav-link " href="users-profile.php">
          <i class="bi bi-person"></i>
          <span>Profile</span>
        </a>

      </li><!-- End Profile Page Nav -->

      <?php if (isset($_SESSION['admin'])) { ?>
        <!-- Afficher si l'utilisateur est un admin -->
        <li class="nav-item" id="administrer">
          <a class="nav-link collapsed" href="#">
            <i class="bi bi-person-fill-gear"></i></i><span style="color:green;">Administrer</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="administrer-elements">
            <li>
              <a class="nav-link collapsed" href="creer-compte.php">
                <i class="bi bi-person-add"></i>
                <span id="">Ajouter un utilisateur</span>
              </a>
            </li>
            <li>
              <a class="nav-link collapsed" href="nommer-administrateur.php">
                <i class="bi bi-person-up"></i>
                <span id="">Nommer un administrateur</span>
              </a>
            </li>
            <li>
              <a class="nav-link collapsed" href="pages-projets.php">
                <i class="bi bi-calendar2-event"></i>
                <span id="">Gestion de Projets</span>
              </a>
            </li>
            <li>
              <a class="nav-link collapsed" href="creer-competence.php">
                <i class="bi bi-luggage"></i>
                <span id="">Ajouter une competence</span>
              </a>
            </li>
            <li>
              <a class="nav-link collapsed" href="creer-niveau-maitrise.php">
                <i class="bi bi-bar-chart-steps"></i>
                <span id="">Ajouter un niveau de maitrise</span>
              </a>
            </li>
            <li>
              <a class="nav-link collapsed" href="creer-categorie.php">
                <i class="bi bi-back"></i>
                <span id="">Ajouter une categorie</span>
              </a>
            </li>
            <li>
              <a class="nav-link collapsed" href="creer-role.php">
                <i class="bi bi-people"></i>
                <span id="">Ajouter un role</span>
              </a>
            </li>
          </ul>
        </li>
      <?php } ?>
      <li class="nav-item" id="publier">
        <a class="nav-link collapsed" href="#">
          <i class="bi bi-menu-button-wide"></i></i><span style="color:red;">Publier</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="publier-elements">
          <li>
            <a class="nav-link collapsed" href="creer-publication.php">
              <i class="bi bi-question-lg"></i>
              <span id="">Poser une question</span>
            </a>
          </li>
          <li>
            <a class="nav-link collapsed" href="creer-annonce.php">
              <i class="bi bi-megaphone-fill"></i>
              <span id="">Faire une annonce</span>
            </a>
          </li>
          <li>
            <a class="nav-link collapsed" href="creer-formation.php">
              <i class="bi bi-person-rolodex"></i>
              <span id="">Programmer une formation</span>
            </a>
          </li>
          <li>
            <a class="nav-link collapsed" href="creer-connaissance.php">
              <i class="bi bi-person-lines-fill"></i>
              <span id="">Partager une connaissance</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item" id="publier">
        <a class="nav-link collapsed" href="#">
          <i class="bi bi-eye-fill"></i></i></i><span style="color:blue;">Consulter</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="publier-elements">
          <li>
            <a class="nav-link collapsed" href="pages-publications.php">
              <i class="bi bi-question-lg"></i>
              <span id="">Consulter les questions</span>
            </a>
          </li>
          <!-- <li>
          <a class="nav-link collapsed" href="#">
            <i class="bi bi-megaphone-fill"></i>
            <span id="">Voir les annonces</span>
          </a>
        </li> -->
          <li>
            <a class="nav-link collapsed" href="pages-formations.php">
              <i class="bi bi-person-rolodex"></i>
              <span id="">Consulter les formations</span>
            </a>
          </li>
          <li>
            <a class="nav-link collapsed" href="pages-connaissances.php">
              <i class="bi bi-person-lines-fill"></i>
              <span id="">Voir les connaissances</span>
            </a>
          </li>
          <li>
            <a class="nav-link collapsed" href="pages-projets.php">
              <i class="bi bi-collection-fill"></i>
              <span id="">Voir mes projets</span>
            </a>
          </li>
        </ul>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="pages-feedback.php">
          <i class="bi bi-envelope"></i>
          <span>Feedback</span>
        </a>
      </li><!-- End Contact Page Nav -->


    </ul>
  <?php else : ?>
    <h5 class="text-danger" >Veuillez completer vos nom et prenom pour voir cette section</h5>
  <?php endif ?>

</aside><!-- End Sidebar-->