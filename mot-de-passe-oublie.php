<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Changer mot de passe</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Inclure les fichier css -->
    <?php require_once "includes/fichiers-css.php"; ?>

    <style>
        .erreur {
            color: red !important;
            font-style: italic !important;

        }
    </style>
</head>

<body>

    <main>
        <div class="container">

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="d-flex justify-content-center py-4">
                                <a href="dash.html" class="logo d-flex align-items-center w-auto">
                                    <img src="assets/img/logo.png" alt="">
                                    <span class="d-none d-lg-block">IntraConnect</span>
                                </a>
                            </div><!-- End Logo -->

                            <div class="card mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Changer votre mot de passe</h5>
                                        <!-- <p class="text-center small">V</p> -->
                                    </div>
                                    <form class="row g-3 needs-validation" novalidate action="traitement/forgot-password.php" method="post">

                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label">Entrer votre adresse email</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <input type="email" name="email" class="form-control" id="yourUsername" value="<?php echo isset($_SESSION['theEmail']) ? $_SESSION['theEmail'] : '';
                                                unset($_SESSION['theEmail']);?>" required >
                                                <div class="invalid-feedback">S'il vous plaît entrez un email valide.</div>
                                            </div>
                                            <div class="col-12">
                                                <label for="newPassword" class="form-label">Mot de passe</label>
                                                <input type="password" name="newPassword" class="form-control" id="newPassword" required>
                                                <div class="invalid-feedback">S'il vous plait entrez votre mot de passe!
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="renewPassword" class="form-label">Confirmer mot de passe</label>
                                                <input type="password" name="renewPassword" class="form-control" id="renewPassword" required>
                                                <div class="invalid-feedback">S'il vous plait confirmer le mot de passe!
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100 mt-5" type="submit" name="passwordForgot">Poursuivre</button>
                                        </div>
                                        <h4 class="erreur"><?php
                                                                //afficher le message d'erreur pour informations incorrectes
                                                                echo isset($_SESSION['error']) ? $_SESSION['error'] : '';
                                                                unset($_SESSION['error']);
                                                                ?></h4>
                                        <div class="col-12">
                                            <div>
                                                <a href="pages-login.php" class="li li-link">Annuler le processus</a>
                                            </div>
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

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>

</body>

</html>