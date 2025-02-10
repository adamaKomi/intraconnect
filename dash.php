<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");
// connexion à la base de données
require_once("includes/bdd-connect.php");

// inclure les fonctions
include_once("includes/fonctions.php");

try {

  /************************
   *
   *   Mes publications
   *
   *************************/
  //recuperer les dernieres publications
  $stmt = $bdd->prepare("SELECT idPub,titrePub,statutPub,dateAction FROM pub WHERE idCollabo = ? ORDER BY dateAction DESC LIMIT 5");
  $stmt->execute([$idCollabo]);
  $mes_dernieres_pub = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //gerer la couleur en fonction du statut de la publication
  $couleur_statut = [
    'nouveau' => 'primary',
    'resolu' => 'success',
    'ouvert' => 'success',
    'complété' => 'danger',
    'en-cours' => 'info',
    'relancé' => 'warning',
    'annulé' => 'warning',
    'fermé' => 'danger',
    0 => 'success',
    1 => 'primary',
    2 => 'info',
    3 => 'warning',
    4 => 'danger',
  ];
  // var_dump($mes_dernieres_pub);
  // exit;


  /************************
   *
   *   Les projets
   *
   *************************/

  //recuperer les projets de ce mois
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM projetcollaborole 
                        WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE) AND MONTH(dateAction) = MONTH(CURRENT_DATE)) AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $nb_projets_mois_courant = $stmt->fetchColumn();

  //recuperer les projets du mois dernier
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM projetcollaborole 
                        WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(dateAction) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)) AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $nb_projets_mois_dernier = $stmt->fetchColumn();

  //calcul du pourcentage des projets
  $infoProjet = pourcentage($nb_projets_mois_courant, $nb_projets_mois_dernier);



  /************************
   *
   *   Les formations
   *
   *************************/
  //Mes formations publiees
  $stmt = $bdd->prepare("SELECT f.idFormation AS id, f.themeFmt AS th, f.statutFormation AS statut, COUNT(ic.idCollabo) AS inscrits 
                          FROM formation f 
                          JOIN fmtpubliee fp ON f.idFormation= fp.idFormation
                          JOIN inscritfmt ic ON ic.idFormation = f.idFormation
                          WHERE fp.idCollabo = ? 
                          GROUP BY ic.idFormation 
                          ORDER BY inscrits DESC LIMIT 5");
  $stmt->execute([$idCollabo]);
  $formations_publie = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //Mes formations suivies
  $stmt = $bdd->prepare("SELECT f.idFormation AS id, f.themeFmt AS th, f.statutFormation AS statut, ic.dateAction 
                        FROM formation f 
                        JOIN inscritfmt ic ON ic.idFormation = f.idFormation
                        WHERE ic.idCollabo = ? 
                        AND f.statutFormation != 'annulé' AND f.statutFormation != 'terminé'
                        ORDER BY ic.dateAction DESC LIMIT 5
                  ");
  $stmt->execute([$idCollabo]);
  $formations_suivie = $stmt->fetchAll(PDO::FETCH_ASSOC);


  /************************
   *
   *   Les questions
   *
   *************************/

  //recuperer les questions de ce mois
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM pub 
                        WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE) AND MONTH(dateAction) = MONTH(CURRENT_DATE)) AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $questions_mois_courant = $stmt->fetchColumn();

  //recuperer les questions du mois dernier
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM pub 
                        WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(dateAction) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)) AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $questions_mois_dernier = $stmt->fetchColumn();

  //calcul du pourcentage des questions
  $infoQuestion = pourcentage($questions_mois_courant, $questions_mois_dernier);


  /************************
   *
   *   Les likes
   *
   *************************/

  //recuperer les likes de ce mois
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM reaction_commentaire 
  WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE) AND MONTH(dateAction) = MONTH(CURRENT_DATE)) AND action = 'like' AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $likes_mois_courant = $stmt->fetchColumn();

  //recuperer les likes du mois dernier
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM reaction_commentaire 
  WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(dateAction) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)) AND action = 'like' AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $likes_mois_dernier = $stmt->fetchColumn();

  //calcul du pourcentage des likes
  $infoLike = pourcentage($likes_mois_courant, $likes_mois_dernier);



  /************************
   *
   *   Les dislikes
   *
   *************************/

  //recuperer les dislikes de ce mois
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM reaction_commentaire 
  WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE) AND MONTH(dateAction) = MONTH(CURRENT_DATE)) AND action = 'dislike' AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $dislikes_mois_courant = $stmt->fetchColumn();

  //recuperer les dislikes du mois dernier
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM reaction_commentaire 
  WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(dateAction) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)) AND action = 'dislike' AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $dislikes_mois_dernier = $stmt->fetchColumn();

  //calcul du pourcentage des dislikes
  $infoDislike = pourcentage($dislikes_mois_courant, $dislikes_mois_dernier);



  /************************
   *
   *   Les notes
   *
   *************************/

  //recuperer les notes de ce mois
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM reaction_note 
  WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE) AND MONTH(dateAction) = MONTH(CURRENT_DATE)) AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $note_mois_courant = $stmt->fetchColumn();

  //recuperer les notes du mois dernier
  $stmt = $bdd->prepare("SELECT count(*) AS nbr FROM reaction_note
  WHERE (YEAR(dateAction) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(dateAction) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)) AND idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $note_mois_dernier = $stmt->fetchColumn();

  //calcul du pourcentage des notes
  $infoNote = pourcentage($note_mois_courant, $note_mois_dernier);



  /************************
   *
   *   Mes commentaires les mieux notes
   *
   *************************/
  $stmt = $bdd->prepare("SELECT p.idCollabo,r.idPub, r.idRep,r.reponse,AVG(note) AS moy FROM reaction_note rn 
                        JOIN reponse r ON rn.idCommentaire=r.idRep 
                        JOIN pub p ON p.idPub = r.idPub
                        WHERE r.idCollabo = ? GROUP BY rn.idCommentaire ORDER BY moy DESC LIMIT 5");
  $stmt->execute([$idCollabo]);
  $mes_commentaires = $stmt->fetchAll(PDO::FETCH_ASSOC);




  // Le nombre de commentaires de l'utilisateur
  $stmt = $bdd->prepare("SELECT count(*) FROM reponse WHERE idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $interactions['nb_comment'] = $stmt->fetchColumn();

  // Le nombre de questions posées
  $stmt = $bdd->prepare("SELECT count(*) FROM pub WHERE idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $interactions['nb_question'] = $stmt->fetchColumn();

  // Le nombre de messages envoyés
  $stmt = $bdd->prepare("SELECT count(*) FROM message WHERE id_emetteur = ?");
  $stmt->execute([$idCollabo]);
  $interactions['nb_message_envoye'] = $stmt->fetchColumn();

  // Le nombre de messages reçus
  $stmt = $bdd->prepare("SELECT count(*) FROM message WHERE id_recepteur = ?");
  $stmt->execute([$idCollabo]);
  $interactions['nb_message_recus'] = $stmt->fetchColumn();

  // Le nombre de likes
  $stmt = $bdd->prepare("SELECT count(*) FROM reaction_commentaire WHERE idCollabo = ? AND action = 'like'");
  $stmt->execute([$idCollabo]);
  $interactions['nb_like'] = $stmt->fetchColumn();

  // Le nombre de dislikes
  $stmt = $bdd->prepare("SELECT count(*) FROM reaction_commentaire WHERE idCollabo = ? AND action = 'dislike'");
  $stmt->execute([$idCollabo]);
  $interactions['nb_dislike'] = $stmt->fetchColumn();

  // La moyenne des notes
  $stmt = $bdd->prepare("SELECT AVG(note) FROM reaction_note WHERE idCollabo = ?");
  $stmt->execute([$idCollabo]);
  $result = $stmt->fetchColumn();
  $interactions['moy_note'] = isset($result) ? number_format($result, 2) : 0;

  // var_dump($interactions);
  // exit;

  // Convertir le tableau en JSON
  $jsonInteractions = json_encode($interactions);
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard - IntraConnect Bootstrap Template</title>
  <!-- Inclure les fichiers CSS -->
  <?php require_once "includes/fichiers-css.php"; ?>
  <link rel="stylesheet" href="includes/dash.css">
  <!-- Inclure Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .container-circle {
      margin-right: 2rem;
      width: 40% !important;
      height: 40% !important;
    }

    /* @media (max-width: 768px) {
      .container-circle{
        width: 80% !important;
        height: 80% !important;
      }
    } */

    .pagetitle h1{
      margin-bottom: 50px;
    }
  </style>
</head>

<body>
  <!-- ======= Header ======= -->
  <?php require_once("includes/main-header.php") ?>
  <!-- ======= Sidebar ======= -->
  <?php include_once("includes/main-sidebar.php") ?>
  <!-- recueillir les données -->
  <span id="data-span" data-mes-donnees="<?php echo htmlspecialchars($jsonInteractions, ENT_QUOTES, 'UTF-8'); ?>"></span>
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <!-- <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dash.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard Collaborateur</li>
        </ol> -->
        <div class="text-start mb-3">
          <button class="btn btn-primary" onclick="window.location.href='creer-publication.php';">Créer Publication</button>
          <button class="btn btn-primary" onclick="window.location.href='pages-publications.php';">Visualiser Publications</button>
        </div>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col">
        <div class="col-12">
          <div class="row">
            <!-- Sales Card -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card sales-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">Aujordui</a></li>
                    <li><a class="dropdown-item" href="#">Ce mois</a></li>
                    <li><a class="dropdown-item" href="#">Cette annee</a></li>
                  </ul>
                </div>
                <div class="card-body col">
                  <h5 class="card-title">Les projets</h5>
                  <div class="row">
                    <div class="col-6">
                      <span class="">Mois dernier</span>
                      <div class="d-flex align-items-center ">
                        <div class="ps-3">
                          <h6><?php echo isset($nb_projets_mois_dernier) ? $nb_projets_mois_dernier : 0; ?></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <span class="">Ce mois</span>
                      <div class="d-flex align-items-center">
                        <div class="ps-3">
                          <h6><?php echo isset($nb_projets_mois_courant) ? $nb_projets_mois_courant : 0; ?></h6>
                          <span class="text-<?php echo isset($infoProjet['couleur']) ? $infoProjet['couleur'] : ''; ?> small pt-1 fw-bold">
                            <?php echo isset($infoProjet) ? $infoProjet['pourcentage'] : 0 ?>
                          </span>
                          <span class="text-muted small pt-2 ps-1">
                            <?php echo isset($infoProjet['couleur']) ? ($infoProjet['couleur'] == 'danger' ? 'decrease' : ($infoProjet['couleur'] == 'success' ? 'increase' : 'static')) : '' ?>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div><!-- End Sales Card -->

            <!-- Les questions posees -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card revenue-card" style="background:rgba(173, 216, 255, 0.5);">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>
                <div class="card-body col">
                  <h5 class="card-title">Les questions posées</h5>
                  <div class="row">
                    <div class="col-6">
                      <span class="">Mois dernier</span>
                      <div class="d-flex align-items-center ">
                        <div class="ps-3">
                          <h6><?php echo isset($questions_mois_dernier) ? $questions_mois_dernier : 0; ?></h6>
                        </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <span class="">Ce mois</span>
                      <div class="d-flex align-items-center">
                        <div class="ps-3">
                          <h6><?php echo isset($questions_mois_courant) ? $questions_mois_courant : 0; ?></h6>
                          <span class="text-<?php echo isset($infoQuestion['couleur']) ? $infoQuestion['couleur'] : ''; ?> small pt-1 fw-bold">
                            <?php echo isset($infoQuestion) ? $infoQuestion['pourcentage'] : '0%'; ?>
                          </span>
                          <span class="text-muted small pt-2 ps-1">
                            <?php echo isset($infoQuestion['couleur']) ? ($infoQuestion['couleur'] == 'danger' ? 'decrease' : ($infoQuestion['couleur'] == 'success' ? 'increase' : 'static')) : '' ?>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- les reactions aux commentaires -->
            <div class="col-xxl-6 col-md-6">
              <div class="card info-card revenue-card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>
                <div class="card-body col">
                  <h5 class="card-title">les reactions aux commentaires</h5>
                  <div class="row">
                    <div class="col-4">
                      <span class="">Mois dernier</span>
                      <div class="d-flex align-items-center ">
                        <div class="ps-3">
                          <p>Likes : <?php echo isset($likes_mois_dernier) ? $likes_mois_dernier : 0; ?></p>
                          <p>Disikes : <?php echo isset($dislikes_mois_dernier) ? $dislikes_mois_dernier : 0; ?></p>
                          <p>Notes : <?php echo isset($note_mois_dernier) ? $note_mois_dernier : 0; ?></p>
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <span class="">Ce mois</span>
                      <div class="d-flex align-items-center">
                        <div>
                          <div class="ps-3">
                            <p>Likes : <?php echo isset($likes_mois_courant) ? $likes_mois_courant : 0; ?></p>
                            <p>Disikes : <?php echo isset($dislikes_mois_courant) ? $dislikes_mois_courant : 0; ?></p>
                            <p>Notes : <?php echo isset($note_mois_courant) ? $note_mois_courant : 0; ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-4 ">
                      <span>Evolution</span>
                      <div class="d-flex align-items-center"></div>
                      <div>
                        <div class="">
                          <!-- likes -->
                          <p class="">
                            <span class="text-<?php echo isset($infoLike['couleur']) ? $infoLike['couleur'] : ''; ?> small fw-bold">
                              <?php echo isset($infoLike) ? $infoLike['pourcentage'] : '0%'; ?>
                            </span>
                            <span class="text-muted small">
                              <?php echo isset($infoLike['couleur']) ? ($infoLike['couleur'] == 'danger' ? 'decrease' : ($infoLike['couleur'] == 'success' ? 'increase' : 'static')) : '' ?>
                            </span>
                          </p>
                          <!-- dislikes -->
                          <p class="">
                            <span class="text-<?php echo isset($infoDislike['couleur']) ? $infoDislike['couleur'] : ''; ?> small fw-bold">
                              <?php echo isset($infoDislike) ? $infoDislike['pourcentage'] : '0%'; ?>
                            </span>
                            <span class="text-muted small">
                              <?php echo isset($infoDislike['couleur']) ? ($infoDislike['couleur'] == 'danger' ? 'decrease' : ($infoQuestion['couleur'] == 'success' ? 'increase' : 'static')) : '' ?>
                            </span>
                          </p>
                          <!-- notes -->
                          <p class="">
                            <span class="text-<?php echo isset($infoNote['couleur']) ? $infoNote['couleur'] : ''; ?> small fw-bold">
                              <?php echo isset($infoNote) ? $infoNote['pourcentage'] : '0%'; ?>
                            </span>
                            <span class="text-muted small">
                              <?php echo isset($infoNote['couleur']) ? ($infoNote['couleur'] == 'danger' ? 'decrease' : ($infoNote['couleur'] == 'success' ? 'increase' : 'static')) : '' ?>
                            </span>
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Les dernieres Publications -->
            <div class="col-xxl-4 col-md-6">
              <div class="card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Mes dernières publications</h5>

                  <div class="activity">
                    <?php if (isset($mes_dernieres_pub) && count($mes_dernieres_pub) > 0) : ?>
                      <?php foreach ($mes_dernieres_pub as $key => $ma_pub) : ?>
                        <div class="activity-item d-flex">
                          <div class="activite-label"><?php echo dateAction($ma_pub['dateAction']); ?></div>
                          <i class='bi bi-circle-fill activity-badge text-<?php echo $couleur_statut[$ma_pub['statutPub']]; ?> align-self-start'></i>
                          <a href="voir-plus-reponse?idPub=<?php echo $ma_pub['idPub']; ?>&amp;idCollabo=<?php echo $idCollabo; ?>" class="activity-content text-dark">
                            <?php echo $ma_pub['titrePub']; ?>
                          </a>
                        </div>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <div>
                        <p class="activity-content">
                          Aucune publication...
                        </p>
                      </div><!-- End activity item-->
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div><!-- End Les dernieres Publication -->

            <!-- Les formations publiees -->
            <div class="col-xxl-4 col-md-6">
              <div class="card" style="background:rgba(173, 216, 230, 0.5);">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Mes formations suivies</h5>

                  <div class="activity">
                    <?php if (isset($formations_suivie) && count($formations_suivie) > 0) : ?>
                      <!-- <span class="card-text">Inscrits</span> -->
                      <?php foreach ($formations_suivie as $key => $formation) : ?>
                        <div class="activity-item d-flex">
                          <div class="activite-label"><?php echo dateAction($formation['dateAction']); ?></div>
                          <i class='bi bi-circle-fill activity-badge text-<?php echo $couleur_statut[$formation['statut']]; ?> align-self-start'></i>
                          <a href="pages-formations.php#pointer-sur<?php echo $formation['id']; ?>" class="activity-content text-dark">
                            <?php echo isset($formation['th']) && !empty($formation['th']) ? $formation['th'] : ''; ?>
                          </a>
                        </div>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <div>
                        <p class="activity-content">
                          Aucune formation...
                        </p>
                      </div><!-- End activity item-->
                    <?php endif; ?>
                  </div>
                </div>
              </div><!-- End Les formations publiees-->

            </div>
            <!-- Les formations publiees -->
            <div class="col-xxl-4 col-md-6">
              <div class="card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Mes formations publiées</h5>

                  <div class="activity">
                    <?php if (isset($formations_publie) && count($formations_publie) > 0) : ?>
                      <span class="card-text">Inscrits</span>
                      <?php foreach ($formations_publie as $key => $ma_formation) : ?>
                        <div class="activity-item d-flex">
                          <div class="activite-label"><?php echo isset($ma_formation['inscrits']) ? $ma_formation['inscrits'] : '0'; ?></div>
                          <i class='bi bi-circle-fill activity-badge text-<?php echo $couleur_statut[$ma_formation['statut']]; ?> align-self-start'></i>
                          <a href="pages-formations.php#pointer-sur<?php echo $ma_formation['id']; ?>" class="activity-content text-dark">
                            <?php echo isset($ma_formation['th']) && !empty($ma_formation['th']) ? $ma_formation['th'] : ''; ?>
                          </a>
                        </div>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <div>
                        <p class="activity-content">
                          Aucune formation...
                        </p>
                      </div><!-- End activity item-->
                    <?php endif; ?>
                  </div>
                </div>
              </div><!-- End Les formations publiees-->

            </div>
          </div>
        </div>
      </div><!-- End Left side columns -->



      <div class="col">
        <div class="col-12">
          <div class="row">

            <div class="col-xxl-8 col-md-12">
              <h2>Les interactions</h2>
              <div class="card col-12" style="display:flex;justify-content:center;">
                <div class="container-circle col-6 mr-3">
                  <canvas id="myPieChart"></canvas>
                </div>
              </div>
            </div><!-- End Right side columns -->

            <!-- Les commentaires les mieux notes -->
            <div class="card col-xxl-4 col-md-12" style="background:rgba(191, 239, 255, 0.5);">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>

                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Mes commentaires les mieux notés</h5>

                <div class="activity">
                  <?php if (isset($mes_commentaires) && count($mes_commentaires) > 0) : ?>
                    <span class="card-text">Moyenne</span>
                    <?php foreach ($mes_commentaires as $key => $mon_comment) : ?>
                      <div class="activity-item d-flex">
                        <div class="activite-label"><?php echo isset($mon_comment['moy']) ? number_format($mon_comment['moy'], 2) : '0'; ?></div>
                        <i class='bi bi-circle-fill activity-badge text-<?php echo $couleur_statut[$key]; ?> align-self-start'></i>
                        <a href="voir-plus-reponse?idPub=<?php echo $mon_comment['idPub']; ?>&amp;idCollabo=<?php echo $mon_comment['idCollabo']; ?>#commentaire<?php echo $mon_comment['idRep']; ?>" class="activity-content text-dark">
                          <?php if (isset($mon_comment['reponse']) && !empty($mon_comment['reponse'])) {
                            // Chaîne de commentaire à vérifier
                            $comment = $mon_comment['reponse'];

                            // Expression régulière pour trouver la structure <pre><code>...</code></pre>
                            $regExp = "/(.|\n)*<pre(.|\n)*><code(.|\n)*>(.|\n)*<\/code><\/pre>(.|\n)*/";
                            // Vérifie si la chaîne de commentaire correspond à l'expression régulière
                            if (preg_match($regExp, $comment)) {
                              $regExp2 = "/<pre(.|\n)*><code(.|\n)*>(.|\n)*<\/code><\/pre>/";
                              // Remplace la structure <pre><code>...</code></pre> par une chaîne vide dans une copie de $comment
                              $commentSansCode = preg_replace($regExp2, '', $comment);

                              // Affiche les premiers 100 caractères de la chaîne sans la structure
                              echo substr($commentSansCode, 0, 79);

                              // Si la chaîne est plus longue que 100 caractères, ajoute '...'
                              if (strlen($commentSansCode) > 80) {
                                echo '...';
                              }
                            } else {
                              // Si la structure n'est pas présente, affiche les premiers 100 caractères
                              echo substr($comment, 0, 79);

                              // Si la chaîne est plus longue que 100 caractères et ne contient pas la structure, ajoute '...'
                              if (strlen($comment) > 80) {
                                echo '...';
                              }
                            }
                          }
                          ?>
                        </a>
                      </div>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <div>
                      <p class="activity-content">
                        Aucun commentaire...
                      </p>
                    </div><!-- End activity item-->
                  <?php endif; ?>
                </div>
              </div>
            </div><!-- End Les commentaires les mieux notes -->
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Inclure les fichiers JS -->
  <?php require_once "includes/fichiers-js.php"; ?>

  <!-- Récupérer les données et initialiser le graphique -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Récupérer les données du span
      const spanElement = document.getElementById('data-span');
      const jsonData = spanElement.getAttribute('data-mes-donnees');

      // Convertir les données JSON en objet JavaScript
      const donnees_dash = JSON.parse(jsonData);

      // Rendre les données accessibles globalement
      window.donneesGlobales = donnees_dash;

      // Initialiser le graphique
      const ctx = document.getElementById('myPieChart').getContext('2d');
      const data = {
        labels: ['Questions', 'Réponses', 'Messages envoyés', 'Messages reçus', 'Likes', 'Dislikes'],
        datasets: [{
          data: [
            donnees_dash.nb_question,
            donnees_dash.nb_comment,
            donnees_dash.nb_message_envoye,
            donnees_dash.nb_message_recus,
            donnees_dash.nb_like,
            donnees_dash.nb_dislike
          ],
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
          ],
          borderWidth: 1
        }]
      };

      const options = {
        responsive: true,
        plugins: {
          legend: {
            position: 'left',
            align: 'start',
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
              }
            }
          },
          title: {
            display: true,
            // text: 'Répartition des interactions'
          }
        },
        animation: {
          animateRotate: true,
          animateScale: true
        }
      };

      const myPieChart = new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options,
        plugins: [{
          id: 'insideLabel',
          beforeDraw: (chart) => {
            const {
              ctx,
              chartArea
            } = chart;
            ctx.save();

            const {
              x,
              y,
              width,
              height
            } = chartArea;
            const fontSize = (height / 100).toFixed(2);
            ctx.font = fontSize + 'em Verdana';
            ctx.textBaseline = 'middle';

            const total = chart.data.datasets[0].data.reduce((acc, value) => acc + value, 0);
            const offset = Math.PI / 2; // rotation pour commencer à 12h

            chart.data.labels.forEach((label, index) => {
              const value = chart.data.datasets[0].data[index];
              const angle = (value / total) * 2 * Math.PI;
              const midAngle = offset + angle / 2;

              const xInside = x + width / 2 * Math.cos(midAngle);
              const yInside = y + height / 2 * Math.sin(midAngle);

              ctx.fillStyle = 'white';
              ctx.fillText(value.toFixed(2), xInside, yInside);
            });

            ctx.restore();
          }
        }]
      });
    });
  </script>

</body>

</html>