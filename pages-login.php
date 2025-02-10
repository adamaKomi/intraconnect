<?php
session_start();

if(isset($_SESSION['success-email'])){
    echo "<script>

    alert(" . json_encode('Votre mot de passe a été modifié avec succès. Vos informations de connexion ont été envoyées par mail') . ");
    
    </script>";

    unset($_SESSION['success-email']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>login/connexion a la plateforme IntraConnect</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

     <!-- Inclure les fichier css -->
     <?php require_once "includes/fichiers-css.php"; ?>

     <style>
        .erreur{
            color:red !important;
            font-style: italic !important;
        }
     </style>
</head>

<body>

    <main>
        <div class="container">

            <section
                class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
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
                                        <h5 class="card-title text-center pb-0 fs-4">Connectez-vous à votre compte
                                        </h5>
                                        <p class="text-center small">Entrez votre nom d'utilisateur et votre mot de
                                            passe pour vous connecter</p>
                                    </div>                                    

                                    <form class="row g-3 needs-validation" novalidate action="traitement/connect.php" method="post">

                                        <div class="col-12">
                                            <label for="yourUsername" class="form-label" >Nom d'utilisateur</label>
                                            <div class="input-group has-validation">
                                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                <input type="text" name="username" class="form-control"
                                                    id="yourUsername" required>
                                                <div class="invalid-feedback">S'il vous plaît entrez votre nom
                                                    d'utilisateur.</div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="yourPassword" class="form-label">Mot de passe</label>
                                            <input type="password" name="password" class="form-control"
                                                id="yourPassword" required>
                                            <div class="invalid-feedback">S'il vous plait entrez votre mot de passe!
                                            </div>
                                        </div>

                                        <!-- <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember"
                                                    value="true" id="rememberMe">
                                                <label class="form-check-label" for="rememberMe">Remember me</label>
                                            </div>
                                        </div> -->
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100 mt-5" type="submit">Se
                                                connecter</button>
                                        </div>
                                        <small class="erreur"><?php
                                        //afficher le message d'erreur pour informations incorrectes
                                        if (isset($_SESSION['error']))
                                          echo $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        ?></small>
                                        <div class="col-12">
                                            <div>
                                                <a href="mot-de-passe-oublie.php" class="li li-link" >Mot de passe oublié ?</a>
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

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>

</body>

</html>