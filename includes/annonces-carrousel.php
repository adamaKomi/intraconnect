<?php
//authentification obligatoire
require_once("traitement/auth_connect_needed.php");
//connexion a la base de donnees
require_once("includes/bdd-connect.php");
//recuperer les annonces dans la bdd
$now = date('Y-m-d');;
$stmt = $bdd->prepare("SELECT * FROM annonce WHERE date >= ?");
$stmt->execute([$now]);
$annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php if (count($annonces) > 0) { ?>
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <h2>Annonces</h2>
        <div class="carousel-inner w-100 h-100" style="background-color: white;">
            <?php foreach ($annonces as $key => $annonce) :
                if ($annonce['imageAnnonce'] !== null) { //si une image a ete definie pour l'annonce
                    $imageAnnonce = base64_encode($annonce['imageAnnonce']); // Convertir les données de l'image en base64
                    $imageAnnonceType = $annonce['imageAnnonceType']; // Récupérer le type de l'image
                    $src = "data:{$imageAnnonceType};base64,{$imageAnnonce}"; // Format de l'URL de l'image
                }
            ?>
                <a href="<?php echo $annonce['lien']; ?>" class="carousel-item <?php echo ($key === 0) ? 'active' : ''; ?> w-100 h-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Cliquer pour acceder a l'evenement">
                    <img src="<?php echo isset($src) ? $src : "img/annonce-default2.jpg";
                                unset($src); ?>" class="d-block w-100 h-100" alt="<?php echo $annonce['titreAnnonce']; ?>">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="carousel-title"><?php echo $annonce['titreAnnonce']; ?></h5>
                        <?php if ($annonce['descriptionAnnonce']) : ?>
                            <p class="carousel-description"> <?php echo $annonce['descriptionAnnonce'];?> </p>
                        <?php endif; ?>
                    </div>
                </a>
            <?php
            endforeach; //fin de foreach  
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev" data-bs-toggle="tooltip" data-bs-placement="top" title="Previous">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next" data-bs-toggle="tooltip" data-bs-placement="top" title="Next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>

    </div>
<?php
} //fin de if (count($annonces) > 0) 
?>