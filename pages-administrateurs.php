<?php

//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

try {

    //recuperer les utilisateur 
    $stmt = $bdd->prepare("SELECT * FROM collabo ORDER BY prenom");
    $stmt->execute();
    $collaborateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
        main#main {
            min-height: 100vh;
        }

        section.section {
            display: flex;
            justify-content: center;
            /* Centrer horizontalement */
            align-items: center;
            /* Centrer verticalement */
            min-height: 100vh;
        }

        .icone {
            max-width: 100px;
            max-height: 100px;

        }

        .container.row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            /* Répartir uniformément l'espace horizontalement */
            /* align-items: center; */
            /* Centrer les éléments verticalement */
        }

        .elements-bloc {
            display: flex;
            flex-wrap: wrap;
            flex-direction: row;
            justify-content: space-around;
        }

        .mon-bloc {
            display: flex;
            flex-direction: column;
            /* flex: 0 0 calc(33.33% - 20px); */
            /* Chaque élément occupe 1/3 de la largeur moins un espacement de 20px */
            margin-right: 5px;
            width: max-content;
            margin-bottom: 50px;
            padding: 0;
            text-align: center;
        }

        .mon-bloc a {
            display: flex;
            flex-direction: column;
            /* flex: 0 0 calc(33.33% - 20px); */
            /* Chaque élément occupe 1/3 de la largeur moins un espacement de 20px */
            width: max-content;
            /* Espacement entre les éléments */
            margin-bottom: 50px;
            /* Espacement entre les lignes */
            padding: 10px;
            text-align: center;
            border: 1px solid blue;
        }

        .mon-bloc a:hover {
            border: 1px solid red;
        }

        .mon-bloc a label {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .mon-bloc a:hover label {
            color: red;
        }

        .mon-bloc:last-child {
            margin-right: 0;
            /* Aucun espacement à droite pour le dernier élément de chaque ligne */
        }

        .row-12 {
            width: 100%;
            /* La largeur de l'élément est de 100% */
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
            <h1>Form Elements</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item">Forms</li>
                    <li class="breadcrumb-item active">Elements</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="container">
                <div class="elements-bloc">
                    <div class="row mon-bloc">
                        <a href="" class="row-12">
                            <label for="">Ajouter un projet</label>
                            <img src="img/icone-projets.png" alt="icone de projets" class="icone row-12">
                        </a>
                    </div>
                    <div class="row mon-bloc">
                        <a href="" class="col">
                            <label for="">Ajouter une competence</label>
                            <img src="img/icone-competences.png" alt="icone de competences" class="icone">
                        </a>
                    </div>
                    <div class="row mon-bloc">
                        <a href="">
                            <label for="">Ajouter un role</label>
                            <img src="img/icone-roles.png" alt="icone de role" class="icone">
                        </a>
                    </div>
                    <div class="row mon-bloc">
                        <a href="">
                            <label for="">Ajouter un niveau de maitrise</label>
                            <img src="img/icone-nvmaitrises.png" alt=" icone de niveaux de maitrise" class="icone">
                        </a>
                    </div>
                    <div class="row mon-bloc">
                        <a href="">
                            <label for="">Ajouter une categorie</label>
                            <img src="img/icone-categories.png" alt="icone de categories" class="icone">
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>


</body>

</html>