<?php
// auth_connect_needed.php doit être inclus en premier
require_once("traitement/auth_connect_needed.php");

// connexion à la base de données
require_once("includes/bdd-connect.php");

// inclure les fonctions
include_once("includes/fonctions.php");


try {
    //recherche par mot cle
    if (isset($_GET['motCle']) && !empty($_GET['motCle'])) {
        $motCle = $_GET['motCle'];
        $motCle = "%$motCle%";
        $stmt = $bdd->prepare("SELECT * FROM connaissance c JOIN motCle mc ON c.id = mc.idConnaissance WHERE mc.mot LIKE ? ORDER BY c.dateAction DESC");
        $stmt->execute([$motCle]);
        $afficheTout = true;
    } else {

        if (isset($_GET['idConnaissance']) && !empty($_GET['idConnaissance'])) {
            $idConn = $_GET['idConnaissance'];
            //recuperer la connaissance sur lequel on a cliquer et ses mots cles
            $stmt = $bdd->prepare("SELECT c.idCollabo, c.nom, c.prenom, c.job, c.imageProfil, c.imageProfilType, conn.titreConn, conn.descriptionConn, conn.dateAction 
                                    FROM connaissance conn
                                    NATURAL JOIN collabo c WHERE conn.id = ?");
            $stmt->execute([$idConn]);
            $ma_connaissance = $stmt->fetch(PDO::FETCH_ASSOC);
            //la photo de profil de celui qui a publiee la connaissance
            if (isset($ma_connaissance['imageProfil'], $ma_connaissance['imageProfilType']) && !empty($ma_connaissance['imageProfilType']) && !empty($ma_connaissance['imageProfil'])) {
                $imageData = base64_encode($ma_connaissance['imageProfil']); // Convertir les données de l'image en base64
                $imageType = $ma_connaissance['imageProfilType']; // Récupérer le type de l'image
                $conn_imgProfil = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image
            }

            //recuperer les mots cles
            $stmt = $bdd->prepare("SELECT * FROM motCle WHERE idConnaissance = ?");
            $stmt->execute([$idConn]);
            $mes_motCle = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //pour recuperer tout le reste des connaissances
            $stmt = $bdd->prepare("SELECT * FROM connaissance WHERE id != ? ORDER BY dateAction DESC");
            $stmt->execute([$idConn]);
        } else { //recuperer tout sinon
            $stmt = $bdd->prepare("SELECT * FROM connaissance ORDER BY dateAction DESC");
            $stmt->execute();
        }
    }
    //recuperer les connaissances
    $connaissances = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
<!doctype html>
<html lang="En">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Social Community</title>

    <!-- Inclure les fichier css -->
    <?php require_once "includes/fichiers-css.php"; ?>
    <!-- fichier css pour le carousel de l'annonce -->
    <link rel="stylesheet" href="includes/annonces.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            overflow-x: hidden;
            /* Prevent horizontal scrolling */
        }

        /* la barre de recherche des mots cles */
        .keyword-search-bar {
            margin-bottom: 50px;
            width: 40% !important;
        }
        /* le formulaire de recherche */
        .keyword-search-form {
            margin-bottom: 20px;
        }
        /* zone de recherche */
        #keyword-searchQuery {
            width: 100% !important;
        }
        /* gerer la connaissance sur laquelle on clique pour voir les details */
        .Maconnaissance {
            background-color: white;
            border-radius: 1rem;
            padding: 10px;
            margin-bottom: 20px;
        }
        /* le titre de la connaissance */
        .Maconnaissance h3 {
            text-align: center;
        }
        /* la description de la connaissance */
        .Maconnaissance .description {
            width: 90%;
            margin: 20px 1rem;
        }

        /* phrase "publie par" */
        .publie-par {
            margin-top: 50px;
            font-weight: 700;
        }
        /* celui qui a publie la connaissance */
        .auteur {
            width: 90%;
            display: flex;
            flex-direction: row;
            margin: 10px auto 10px auto;
            background-color: #143D59;
            padding: 10px 15px;
            /* border-radius: 0.6rem; */
        }
        /* la photo de profil de celui qui a publie */
        .auteur img {
            width: 100px;
            height: 100px;
            margin-right: 1rem;
            padding-right: 10px;
            border-right: 3px solid white;
        }

        .auteur div>p {
            margin: 0;
        }
        /* nom et prenom de l'auteur */
        .auteur .nom-prenom {
            color: white;
        }
        /* gerer les mots-cles */
        .mot-cle a{
            font-weight: 600;
            color: red ;
            font-style: italic;
        }
        /* gerer l'affichage de toutes les connaissances dans la page */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, minmax(100px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .item {
            position: relative;
            padding: 20px;
            font-size: 20px;
            height: 300px;
            opacity: 0;
            /* Start hidden for scroll effect */
            transform: translateY(200px);
            /* Start offset for scroll effect */
            transition: opacity 0.5s ease, transform 0.5s ease;
            /* Transition effect */
            border-radius: 1rem;
            overflow: hidden;
        }

        .item h4 {
            text-align: center;
            margin-bottom: 20px;
        }

        .item .description {
            height: 50%;
            overflow: hidden;
        }

        .item .dateAction {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            font-size: 0.8rem;
            font-weight: bold;
        }
        /* le decallage des elements */
        .item:nth-child(odd) {
            margin-top: 150px;
        }
        /* gerer les couleurs de fond different pour elements */
        .color1 {
            background-color: white;
        }

        .color2 {
            /* background-color: rgba(173, 216, 255, 1); */
            background-color: rgba(191, 239, 255, 1);
            /* color: white; */
        }
        /* si la taillea de l'ecran est inferieur a 600px on reajuste l'affichage */
        @media (max-width: 600px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
        }
        /* visibilite lors du scroll */
        .visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <!-- ======= Header ======= -->
    <?php require_once("includes/main-header.php") ?>

    <!-- ======= Sidebar ======= -->
    <?php include_once("includes/main-sidebar.php") ?>

    <main id="main" class="main bg-image">
        <div class="pagetitle">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Connaissances</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section profile">
            <h1>Pages de connaissances</h1>
            <div class="main-content-wrapper d-flex flex-row justify-content-between row">
                <div class="keyword-search-bar">
                    <h5>Rechercher par mot-cle </h5>
                    <form class="keyword-search-form d-flex align-items-center" method="post">
                        <input type="text" id="keyword-searchQuery" name="query" placeholder="Saisir un mot-cle" title="Enter search keyword">
                        <button type="submit" id="keyword-searchButton" title="Search" class="btn-primary"><i class="bi bi-search"></i></button>
                    </form>
                    <?php if (isset($afficheTout)) : ?>
                        <div><a href="pages-connaissances.php" class="btn btn-primary">Affcher tout</a></div>
                    <?php endif; ?>
                </div><!-- End Search Bar -->
                <?php if (isset($ma_connaissance) && !empty($ma_connaissance)) : ?>
                    <div class="Maconnaissance">
                        <div>
                            <h3><?php echo $ma_connaissance['titreConn']; ?></h3>
                            <p class="description"><?php echo $ma_connaissance['descriptionConn']; ?></p>
                        </div>
                        <p class="publie-par">Publié par : </p>
                        <a href="users-profile.php?idCollabo=<?php echo $ma_connaissance['idCollabo']; ?>&amp;action=voir-profil" class="auteur">
                            <img src="<?php echo isset($conn_imgProfil) ? $conn_imgProfil : "assets/img/profile-inconnu.png";
                                        unset($conn_imgProfil); ?>" alt="image de profil">
                            <div>
                                <p class="nom-prenom"><?php echo isset($ma_connaissance['prenom'], $ma_connaissance['nom']) ? $ma_connaissance['prenom'] . ' ' . $ma_connaissance['nom'] : ''; ?></p>
                                <p><?php echo (isset($ma_connaissance['job']) && $ma_connaissance['job'] != 'Non defini') ? $ma_connaissance['job'] : ''; ?></p>
                            </div>
                        </a>
                        <?php if (isset($mes_motCle) && count($mes_motCle) > 0) : ?>
                            <p>Mot-clé :
                            <span class="mot-cle">
                                <?php foreach ($mes_motCle as $key => $mon_mot) : ?>
                                    <a href="pages-connaissances.php?motCle=<?php echo $mon_mot['mot'];?>" ><?php echo $mon_mot['mot'] . ', '; ?></a>
                                <?php endforeach; ?>
                            </span>
                            </p>
                        <?php endif; ?>
                        <p><?php echo dateAction($ma_connaissance['dateAction']) ?></p>
                    </div>
                <?php endif; ?>
                <?php if (isset($connaissances) && count($connaissances) > 0) : ?>
                    <div class="grid-container">
                        <?php foreach ($connaissances as $key => $connaissance) : ?>
                            <a href="pages-connaissances.php?idConnaissance=<?php echo $connaissance['id']; ?>" class="item">
                                <h4><?= isset($connaissance['titreConn']) ? $connaissance['titreConn'] : ''; ?></h4>
                                <p class="description"><?= isset($connaissance['descriptionConn']) ? substr($connaissance['descriptionConn'], 0, 249) : ''; //echo strlen($connaissance['descriptionConn'])>250?'...':''; 
                                                        ?></p>
                                <p class="dateAction">
                                    <?= dateAction($connaissance['dateAction']); ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div>
                        <h2>Aucun resultat trouvé !!!</h2>
                    </div>
                <?php endif; ?>

            </div>
        </section>
    </main><!-- End #main -->
    <!-- End Main Content Wrapper Area -->

    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>

    <script>
        //la recherche de mots cles
        var form_rechercher = document.querySelector('.keyword-search-form');

        form_rechercher.addEventListener('submit', function(e) {
            e.preventDefault(); // Empêche l'envoi du formulaire

            var expression = document.getElementById('keyword-searchQuery').value.trim();
            var regExp = /[^a-zA-Z]/g;

            // Vous pouvez maintenant utiliser regExp.test() pour vérifier votre expression
            if (regExp.test(expression)) {
                alert('Veuillez saisir un seul mot sans espaces ni chiffres ni caracteres speciaux.');
            } else {
                window.location.href = 'pages-connaissances.php?motCle=' + expression;
            }
        });


        //gerer les effets scroll de la page
        document.addEventListener("DOMContentLoaded", function() {
            const items = document.querySelectorAll(".item");

            function applyColorScheme() {
                const screenWidth = window.innerWidth;

                items.forEach((item, index) => {
                    item.classList.remove('color1', 'color2'); // Reset classes
                    if (screenWidth >= 600) {
                        const remainder = (index + 1) % 4;
                        if (remainder === 1) {
                            item.classList.add('color1');
                        } else if (remainder === 2 || remainder === 3) {
                            item.classList.add('color2');
                        } else if (remainder === 0) {
                            item.classList.add('color1');
                        }
                    } else {
                        if (index % 2 === 0) {
                            item.classList.add('color1');
                        } else {
                            item.classList.add('color2');
                        }
                    }
                });
            }

            // Initial application based on screen size
            applyColorScheme();

            // Reapply on window resize
            window.addEventListener('resize', applyColorScheme);

            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                        observer.unobserve(entry.target); // Stop observing once it's visible
                    }
                });
            }, {
                threshold: 0.1
            });

            items.forEach(item => {
                observer.observe(item);
            });
        });
    </script>

</body>

</html>