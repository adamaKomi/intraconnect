<?php
//authentification obligatoire
require_once("traitement/auth_connect_needed.php");
//connexion a la base de donnees
require_once("includes/bdd-connect.php");

if (isset($_GET['idCollabo'], $_GET['action']) && $_GET['idCollabo'] != $idCollabo && $_GET['action'] == 'voir-profil') {
  // voir le profil d'un collaborateur 
  $idCol = $_GET['idCollabo'];

  //recuperer les informations du collaborateur
  $stmt = $bdd->prepare("SELECT * FROM collabo WHERE idCollabo = ?");
  $stmt->execute([$idCol]);
  $collaboProfil = $stmt->fetch(PDO::FETCH_ASSOC);

  //recuperer la liste des competences du collaborateur
  // Exécutez la requête préparée uniquement si $idCollabo est défini et non vide
  $req = "SELECT competence.nomCompt AS competence, nivmaitrise.nomMaitrise AS maitrise
            FROM competence
            INNER JOIN colcompmt ON competence.idCompt = colcompmt.idCompetence
            INNER JOIN nivmaitrise ON colcompmt.idMaitrise = nivmaitrise.idMaitrise
            WHERE colcompmt.idCollabo=?";

  $stmt = $bdd->prepare($req);
  $stmt->execute([$idCol]);
  $CompetencesCollaboProfil = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //recuperer la photo de profil
  $imageData = null;
  $imageType = null;
  $srcProfilVoir = null;
  if ($collaboProfil['imageProfil']) {
    // Afficher l'image
    $imageData = base64_encode($collaboProfil['imageProfil']); // Convertir les données de l'image en base64
    $imageType = $collaboProfil['imageProfilType']; // Récupérer le type de l'image
    $srcProfilVoir = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image

  }
} else {

  //le profil de l'utilisateur actuel
  try {
    // Requête pour récupérer les compétences
    $competences_query = "SELECT * FROM competence";
    $competences_stmt = $bdd->query($competences_query);
    $competences = $competences_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Requête pour récupérer les niveaux de maîtrise
    $niveaux_query = "SELECT * FROM nivmaitrise";
    $niveaux_stmt = $bdd->query($niveaux_query);
    $niveaux = $niveaux_stmt->fetchAll(PDO::FETCH_ASSOC);


    //recuperer la liste des competences du collaborateur
    // Exécutez la requête préparée uniquement si $idCollabo est défini et non vide
    if (!empty($idCollabo)) {
      $req = "SELECT competence.nomCompt AS competence, nivmaitrise.nomMaitrise AS maitrise
            FROM competence
            INNER JOIN colcompmt ON competence.idCompt = colcompmt.idCompetence
            INNER JOIN nivmaitrise ON colcompmt.idMaitrise = nivmaitrise.idMaitrise
            WHERE colcompmt.idCollabo=?";

      $stmt = $bdd->prepare($req);
      $stmt->execute([$idCollabo]);
      $CompetencesCollabo = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {

      // Gérer le cas où $idCollabo n'est pas défini ou vide
      // Peut-être définir $CompetencesCollabo à une valeur par défaut ou afficher un message d'erreur
      $CompetencesCollabo = [];
    }


    $imageData = null;
    $imageType = null;
    $src_Profil = null;
    if (isset($collaborateur['imageProfil']) && !empty($collaborateur['imageProfil'])) {
      // Afficher l'image
      $imageData = base64_encode($collaborateur['imageProfil']); // Convertir les données de l'image en base64
      $imageType = $collaborateur['imageProfilType']; // Récupérer le type de l'image
      $src_Profil = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image

    }
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Users / Profile </title>
  <meta content="" name="description">
  <meta content="" name="keywords">



  <!-- Inclure les fichier css -->
  <?php require_once("includes/fichiers-css.php"); ?>

</head>

<body>

  <!-- ======= Header ======= -->
  <?php require_once("includes/main-header.php") ?>

  <!-- ======= Sidebar ======= -->
  <?php include_once("includes/main-sidebar.php") ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1 class="mb-3">Profile</h1>
      <!-- <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav> -->
    </div><!-- End Page Title -->

    <section class="section profile">

      <div class="row">
        <div class="col-xl-4">

          <div class="card">
            <!-- si c'est le profil d'un autre utilisateur -->
            <?php if (isset($collaboProfil)) : ?>
              <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <!-- Afficher l'image de profil -->
                <img id="myImage" src="<?php echo isset($srcProfilVoir) ? $srcProfilVoir : "assets/img/profile-inconnu.png";
                                        unset($srcProfilVoir); ?>" alt="Image profil">
                <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
                <h2><?php echo (isset($collaboProfil['nom']) && isset($collaboProfil['prenom'])) ? $collaboProfil['prenom'] . " " . $collaboProfil['nom'] : "Non defini"; ?></h2>
                <h3><?php echo isset($collaboProfil['job']) ? $collaboProfil['job'] : "Non defini"; ?></h3>
                <div class="social-links mt-2">
                  <a href="<?php echo isset($collaboProfil['twitter']) ? $collaboProfil['twitter'] : "#" ?>" class="twitter"><i class="bi bi-twitter"></i></a>
                  <a href="<?php echo isset($collaboProfil['facebook']) ? $collaboProfil['facebook'] : "#" ?>" class="facebook"><i class="bi bi-facebook"></i></a>
                  <a href="<?php echo isset($collaboProfil['instagram']) ? $collaboProfil['instagram'] : "#" ?>" class="instagram"><i class="bi bi-instagram"></i></a>
                  <a href="<?php echo isset($collaboProfil['linkedin']) ? $collaboProfil['linkedin'] : "#" ?>" class="linkedin"><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
              <!-- si c'est mon profil -->
            <?php else : ?>
              <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <!-- Afficher l'image de profil -->
                <img id="myImage" src="<?php echo isset($src_Profil) ? $src_Profil : "assets/img/profile-inconnu.png"; ?>" alt="Image profil">
                <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
                <h2><?php echo (isset($collaborateur['nom']) && isset($collaborateur['prenom'])) ? $collaborateur['prenom'] . " " . $collaborateur['nom'] : "Non defini"; ?></h2>
                <h3><?php echo isset($collaborateur['job']) ? $collaborateur['job'] : "Non defini"; ?></h3>
                <div class="social-links mt-2">
                  <a href="<?php echo isset($collaborateur['twitter']) ? $collaborateur['twitter'] : "#" ?>" class="twitter"><i class="bi bi-twitter"></i></a>
                  <a href="<?php echo isset($collaborateur['facebook']) ? $collaborateur['facebook'] : "#" ?>" class="facebook"><i class="bi bi-facebook"></i></a>
                  <a href="<?php echo isset($collaborateur['instagram']) ? $collaborateur['instagram'] : "#" ?>" class="instagram"><i class="bi bi-instagram"></i></a>
                  <a href="<?php echo isset($collaborateur['linkedin']) ? $collaborateur['linkedin'] : "#" ?>" class="linkedin"><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <!-- afficher si c'est mon profil -->
          <?php if (!isset($collaboProfil)) : ?>
            <div class="text-center">
              <a href="pages-projets.php" class="btn btn-primary">Mes Projets</a>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-xl-8">

          <div class="card">
            <!--Affichage des erreurs -->
            <?php if (isset($_SESSION['profileError'])) { ?>
              <p class="error-message ml-2"><?php echo $_SESSION['profileError'];
                                            unset($_SESSION['profileError']); ?></p>
            <?php } ?>

            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>
                <!-- ne pas montrer les autres parametres si ce profil est le profil d'un autre collaborateur -->
                <?php if (!isset($collaboProfil)) : ?>
                  <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                  </li>

                  <!-- <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-settings">Settings</button>
                  </li> -->

                  <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                  </li>
                <?php endif; ?>

              </ul>
              <div class="tab-content pt-2">

                <!-- overview -->
                <!-- montrer ca si ce profil est un profil d'un autre utilisateur -->
                <?php if (isset($collaboProfil)) : ?>
                  <div class="tab-pane fade show active profile-overview" id="profile-overview">
                    <h5 class="card-title">About</h5>
                    <p class="small fst-italic"><?php echo isset($collaboProfil['about']) ? $collaboProfil['about'] : "Non defini"; ?></p>

                    <h5 class="card-title">Profile Details</h5>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label ">Nom Complet</div>
                      <div class="col-lg-9 col-md-8"><?php echo isset($collaboProfil['nom'], $collaboProfil['prenom']) ? $collaboProfil['prenom'] . " " . $collaboProfil['nom'] : "Non defini"; ?></div>
                    </div>

                    <div class="row">
                      <!-- Afficher le job -->
                      <div class="col-lg-3 col-md-4 label">Job</div>
                      <div class="col-lg-9 col-md-8"><?php echo isset($collaboProfil['job']) ? $collaboProfil['job'] : "Non defini"; ?></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Competences</div>
                      <!--Afficher les competences et leurs niveaux de maitrise-->
                      <table cellpadding="5" style="margin-left: 40px;">
                        <?php
                        if (isset($CompetencesCollaboProfil) && count($CompetencesCollaboProfil) > 0) :
                          foreach ($CompetencesCollaboProfil as $comptcollab) : ?>
                            <tr>
                              <td> <?php echo $comptcollab['competence']; ?> </td>
                              <td><?php echo $comptcollab['maitrise']; ?></td>
                            </tr>
                          <?php
                          endforeach; ?>
                        <?php else : ?>
                          <tr>
                            <td>Aucune compétence n'a été définie</td>
                          </tr>
                        <?php endif; ?>
                      </table>
                    </div>
                    <!-- ne pas montrer son email  -->
                    <!-- <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?php echo $collaboProfil['email']; ?></div>
                  </div> -->

                  </div>
                  <!-- montrer ca si ce profil est mon profil -->
                <?php else : ?>
                  <div class="tab-pane fade show active profile-overview" id="profile-overview">
                    <h5 class="card-title">About</h5>
                    <p class="small fst-italic"><?php echo isset($collaborateur['about']) ? $collaborateur['about'] : "Non defini"; ?></p>

                    <h5 class="card-title">Profile Details</h5>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label ">Nom Complet</div>
                      <div class="col-lg-9 col-md-8"><?php echo (isset($collaborateur['nom']) && isset($collaborateur['prenom'])) ? $collaborateur['prenom'] . " " . $collaborateur['nom'] : "Non defini"; ?></div>
                    </div>

                    <div class="row">
                      <!-- Afficher le job -->
                      <div class="col-lg-3 col-md-4 label">Job</div>
                      <div class="col-lg-9 col-md-8"><?php echo isset($collaborateur['job']) ? $collaborateur['job'] : "Non defini"; ?></div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Competences</div>
                      <!--Afficher les competences et leurs niveaux de maitrise-->
                      <table cellpadding="5" style="margin-left: 40px;">
                        <?php
                        if (isset($CompetencesCollabo) && count($CompetencesCollabo) > 0) :
                          foreach ($CompetencesCollabo as $comptcollabo) : ?>
                            <tr>
                              <td> <?php echo $comptcollabo['competence']; ?> </td>
                              <td><?php echo $comptcollabo['maitrise']; ?></td>
                            </tr>
                            <?php
                          endforeach; ?>
                        <?php else : ?>
                          <tr>
                            <td style="color:red;" >Aucune compétence n'a été définie</td>
                          </tr>
                        <?php endif; ?>
                      </table>
                    </div>
                    <div class="row">
                      <div class="col-lg-3 col-md-4 label">Email</div>
                      <div class="col-lg-9 col-md-8"><?php echo $collaborateur['email']; ?></div>
                    </div>

                  </div>
                <?php endif; ?>

                <!-- Edit Profile -->
                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form id="editProfile" action="traitement/modify-profile.php" method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <!-- Afficher l'image -->
                        <img id="myImage" src="<?php echo isset($src_Profil) ? $src_Profil : "assets/img/profile-inconnu.png";
                                                unset($src_Profil); ?>" alt="Image profil">
                        <div class="pt-2">
                          <input type="file" name="image" accept="image/*" class="btn btn-primary btn-sm image-input" title="Upload new profile image">
                          <a href="traitement/modify-profile.php?idCollabo=<?php echo $idCollabo ?>&amp;action=supprimer-image-profile" class="btn btn-danger btn-sm supprimer-image-profile" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Firstname" class="col-md-4 col-lg-3 col-form-label">Prenom <span class="error-message">*</span></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Firstname" type="text" class="form-control user-info" id="Firstname" value="<?php echo isset($_SESSION['incompleteFirstname']) ? $_SESSION['incompleteFirstname'] : $collaborateur['prenom'];
                                                                                                                  unset($_SESSION['incompleteFirstname']); ?>" required>

                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="Lastname" class="col-md-4 col-lg-3 col-form-label">Nom <span class="error-message">*</span></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="Lastname" type="text" class="form-control user-info" id="Lastname" value="<?php echo isset($_SESSION['incompleteLastname']) ? $_SESSION['incompleteLastname'] : $collaborateur['nom'];
                                                                                                                unset($_SESSION['incompleteLastname']); ?>" required>

                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="username" class="col-md-4 col-lg-3 col-form-label">Username <span class="error-message">*</span></label>
                      <div class="col-md-8 col-lg-9">
                        <input name="username" type="text" class="form-control user-info" id="username" data-username="<?php echo $collaborateur['username']; ?>" value="<?php echo isset($_SESSION['usernameExist']) ? $_SESSION['usernameExist'] :  $collaborateur['username'];
                                                                                                                                                                          unset($_SESSION['usernameExist']); ?>" required>

                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="about" class="col-md-4 col-lg-3 col-form-label">About</label>
                      <div class="col-md-8 col-lg-9">
                        <textarea name="about" class="form-control" id="about" style="height: 100px"><?php echo isset($collaborateur['about']) ? $collaborateur['about'] : ""; ?></textarea>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="company" class="col-md-4 col-lg-3 col-form-label">Comptences</label>
                      <div class="col-md-8 col-lg-9">

                        <table cellpadding="5">
                          <?php foreach ($competences as $competence) { ?>
                            <tr>
                              <td>
                                <?php echo $competence['nomCompt']; ?>
                              </td>
                              <td>
                                <input type="checkbox" name="competences[]" value="<?php echo $competence['nomCompt'] ?>">
                                <select name="niveaux[<?php echo $competence['nomCompt'] ?>]">
                                  <?php foreach ($niveaux as $niveau) { ?>
                                    <option value="<?php echo $niveau['nomMaitrise'] ?>"><?php echo $niveau['nomMaitrise'] ?></option>
                                  <?php } ?>
                                </select>
                              </td>
                            </tr>
                          <?php
                            # code...
                          } ?>
                        </table>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Job" class="col-md-4 col-lg-3 col-form-label">Job</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="job" type="text" class="form-control" id="Job" value="<?php echo isset($collaborateur['job']) ? $collaborateur['job'] : "Non defini"; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email" value="<?php echo isset($collaborateur['email']) ? $collaborateur['email'] : "Non defini"; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Twitter" class="col-md-4 col-lg-3 col-form-label">Twitter Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="twitter" type="text" class="form-control" id="Twitter" value="<?php echo isset($collaborateur['twitter']) ? $collaborateur['twitter'] : "#"; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Facebook" class="col-md-4 col-lg-3 col-form-label">Facebook Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="facebook" type="text" class="form-control" id="Facebook" value="<?php echo isset($collaborateur['facebook']) ? $collaborateur['facebook'] : "#"; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Instagram" class="col-md-4 col-lg-3 col-form-label">Instagram Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="instagram" type="text" class="form-control" id="Instagram" value="<?php echo isset($collaborateur['instagram']) ? $collaborateur['instagram'] : "#"; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Linkedin" class="col-md-4 col-lg-3 col-form-label">Linkedin Profile</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="linkedin" type="text" class="form-control" id="Linkedin" value="<?php echo isset($collaborateur['linkedin']) ? $collaborateur['linkedin'] : "#"; ?>">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="editInfos">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>
                <!-- Settings -->
                <div class="tab-pane fade pt-3" id="profile-settings">

                  <!-- Settings Form -->
                  <form>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Email Notifications</label>
                      <div class="col-md-8 col-lg-9">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="changesMade" checked>
                          <label class="form-check-label" for="changesMade">
                            Changes made to your account
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="newProducts" checked>
                          <label class="form-check-label" for="newProducts">
                            Information on new products and services
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="proOffers">
                          <label class="form-check-label" for="proOffers">
                            Marketing and promo offers
                          </label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="securityNotify" checked disabled>
                          <label class="form-check-label" for="securityNotify">
                            Security alerts
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End settings Form -->
                </div>
                <!-- Change Password -->
                <div class="tab-pane fade pt-3" id="profile-change-password">

                  <!-- Change Password Form -->
                  <form method="post" action="traitement/modify-password.php">

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="currentPassword" type="password" class="form-control" id="currentPassword" value="<?php echo isset($_SESSION['passEntered']) ? $_SESSION['passEntered'] : "";
                                                                                                                        unset($_SESSION['passEntered']); ?>">

                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newPassword" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewPassword" type="password" class="form-control" id="renewPassword">

                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="editPassword">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>
        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Inclure les fichier javaScript -->
  <?php require_once("includes/fichiers-js.php"); ?>


  <script>
    var supp_imgProfil = document.querySelector(".supprimer-image-profile");
    supp_imgProfil.addEventListener("click", function(e) {
      e.preventDefault();
      var lien = this.getAttribute("href");
      var supp = confirm("Etes-vous sûr de vouloir supprimer votre photo de profil?");
      if (supp) {
        window.location.href = lien;
      }
    })

    // gerer les champs d'informations personnelles (nom,prenom,username)
    var user_info = document.querySelectorAll('.user-info');
    user_info.forEach(info => {
      info.addEventListener('blur', emptyInfo);
    });
    // fonction pour verifier si les champs ne sont pas vides
    function emptyInfo(e) {
      e.preventDefault();
      var regExp = /^\s*$/;
      if (regExp.test(this.value)) {
        alert('Le champ ne doit pas etre vide');
        var username = this.getAttribute('data-username');
        if (username && username != "") {
          this.value = username;
        }
      }
    }

    // gerer la taille de l'image
    var image_input = document.querySelector('.image-input');
    var imageValue = image_input.value;
    image_input.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        var Size = file.size;
        // Convertissez la taille du fichier Mo
        const imageSize = (Size / 1024 / 1024).toFixed(2);
        if (imageSize > 10) {
          alert('La taille de l\'image ne doit pas depasser 10Mo');
          this.value = '';
        }
      }
    });
  </script>

</body>

</html>