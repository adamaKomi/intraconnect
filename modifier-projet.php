<?php

//authentification obligatoire
require("traitement/auth_connect_needed.php");
//se connecter a la base de donnnees
require_once("includes/bdd-connect.php");

if(isset($_GET['idProjet']))
{
    $idProjet = $_GET['idProjet'];
    


 

    try {

        if ($collaborateur['imageProfil']) {
            // Afficher l'image
            $imageData = base64_encode($collaborateur['imageProfil']); // Convertir les données de l'image en base64
            $imageType = $collaborateur['imageProfilType']; // Récupérer le type de l'image
            $srcProfil = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image
        }

        //recuperer les utilisateur 
        $stmt = $bdd->prepare("SELECT * FROM collabo ORDER BY prenom");
        $stmt->execute();
        $collaborateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
        //recuperer les roles
        $stmt = $bdd->prepare("SELECT * FROM role");
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);


        //recuperer le projet
        $stmt = $bdd->prepare("SELECT * FROM projet WHERE idProjet = ? ");
        $stmt->execute([$idProjet]);
        $projet = $stmt->fetch(PDO::FETCH_ASSOC);


        //recuperer les collaborateurs qui travaillent sur le projet
        $stmt = $bdd->prepare("SELECT collabo.idCollabo,role.idRole,nom,prenom,nomRole from collabo 
                                JOIN projetcollaborole ON collabo.idCollabo = projetcollaborole.idCollabo
                                JOIN role ON role.idRole = projetcollaborole.idRole WHERE projetcollaborole.idProjet = ?");

        $stmt->execute([$idProjet]);
        $rolesCollabo = $stmt->fetchAll(PDO::FETCH_ASSOC);

      
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

    <title>Users / Profile - NiceAdmin Bootstrap Template</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

       <!-- Inclure les fichier css -->
    <?php require_once("includes/fichiers-css.php"); ?>


    <style>
        #section-annonce>div {
            margin: auto;
        }

        #section-annonce .card-title {
            text-align: center;
            font-size: 1.5rem;
        }

        #section-annonce form {
            font-size: 14pt;
        }

        #section-annonce form .etoile {
            font-size: 1rem;
            color: red;
            font-weight: bold;
        }

        .error-message {
            color: #ff0000;
            /* Couleur du texte rouge */
            font-size: 1.4rem !important;
            /* Taille de la police */
            font-weight: bold;
            /* Police en gras */
            margin-bottom: 10px;
            /* Marge en bas pour l'espacement */
        }

        .terminer:active {
            background-color: red !important;
            /* Changer la couleur de fond en rouge lorsque le bouton est cliqué */
        }

        .affecter:active {
            background-color: blue !important;
            /* Changer la couleur de fond en rouge lorsque le bouton est cliqué */
        }

        .projet-role{
            border: 1px solid red !important;
            border-radius: 10px !important;
        }
        .post-meta-wrap label{
            margin : 0 !important;
            font-size: 18px !important;
            color: blue !important;
        }
        .post-meta-wrap li{
            list-style: none !important;
            margin-left: 50px !important;
        }
        .post-meta-wrap li p{
            margin : 0 0 5px 0 !important;
            font-size: 16px !important;
    
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
            <div class="row" id="section-annonce">
                <div class="col-lg-6">

                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Modifier le projet</h3>

                            <!-- General Form Elements -->
                            <form id="myForm" method="post" action="traitement/modify-projet.php?idProjet=<?php echo $idProjet;?>" enctype="multipart/form-data">
                                <div class="col mb-3">
                                    <label for="titreProjet" class="col-12 col-form-label">Nom du projet <span class="etoile">*</span></label>
                                    <div class="col-12">
                                        <input id="titreProjet" type="text" class="form-control" name="nomProjet" required value="<?php echo isset($projet['nomProjet']) ? $projet['nomProjet'] : ""; ?>" >
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="description" class="col-12 col-form-label">Description <span class="etoile">*</label>
                                    <div class="col-12">
                                        <textarea id="description" class="form-control" style="height: 100px" name="description" required ><?php echo isset($projet['descriptionProjet']) ? $projet['descriptionProjet'] : ""; ?></textarea>
                                    </div>
                                    <?php if (isset($rolesCollabo)) { ?>
                                        <?php if (count($rolesCollabo) > 0) { ?>
                                            <div class="projet-role d-block mt-3 ">
                                                <ul class="post-meta-wrap  align-items-center">
                                                    <label class="col-form-label">Collaborateurs travaillant sur le projet</label>
                                                    <?php foreach ($rolesCollabo as $key => $roleCol) { ?>
                                                        <li class="col col-8 margin-auto">
                                                            <p class="col " >
                                                                <a href="traitement/supprimer-element.php?idCollabo=<?php echo $roleCol['idCollabo'];?>&amp;idRole=<?php echo $roleCol['idRole'];?>&amp;idProjet=<?php echo $projet['idProjet'];?>&amp;action=supprimer-role-collabo" class="btn btn-danger btn-sm supprimer-role-collabo" title="Remove my profile image"><i class="bi bi-trash"></i></a>
                                                                <span class="role" ><?php echo $roleCol['nomRole']. ' : '?> </span> <span class="nomPrenom" > <?php echo $roleCol['prenom'] . ' ' . $roleCol['nom'] ?></span>
                                                            </p>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                                </p>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <!-- <?php if (count($roles) > 0) { ?>
                                        <div class="container mt-3 mb-3 pb-3 pt-3 border border-2 border-primary rounded">
                                            <h5 style="color:red;font-weight:bold;">Assigner des roles</h5>
                                            <div class="form-group">
                                                <label for="role">Choisir un role <?php echo (count($rolesCollabo)>0)?'':"<span class='etoile'>*</span>" ?> </label>
                                                <?php //Afficher les roles ?>
                                                <select class="form-control" id="role" <?php echo (count($rolesCollabo)>0)? '':'required' ?> >
                                                    <option value="">Ajouter un role</option>
                                                    <?php foreach ($roles as $key => $role) { ?>
                                                        <option value="<?php echo $role['idRole']; ?>"><?php echo $role['nomRole']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="collabo">Choisir un collaborateur:</label>
                                                <?php //Afficher les collaborateurs ?>
                                                < class="form-control" id="collabo">
                                                    <option value="">Sélectionnez une option</option>
                                                    <?php foreach ($collaborateurs as $key => $collaborateur) { ?>
                                                        <?php //On Affiche les collaborateurs qui ont defini leurs nom et prenom ?>
                                                        <?php if ($collaborateur['nom'] && $collaborateur['prenom']) { ?>
                                                            <option value="<?php echo $collaborateur['idCollabo']; ?>"><?php echo $collaborateur['prenom'] . ' ' . $collaborateur['nom']; ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-secondary affecter" onclick="linkChoices()">Affecter role</button>
                                            <button type="button" class="btn btn-success terminer" onclick="finish()">Terminer</button>
                                        </div>
                                    <?php } ?> -->
                                    <div class="row mb-3 mt-3">
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn btn-primary" name="modifierProjet">Modifier le projet</button>
                                            <input type="reset" class="btn btn-warning" value="Effacer">
                                        </div>
                                    </div>
                                    <div class="col col-2 mb-3 w-100">
                                        <div class="col-sm-10 w-100">
                                            <a type="submit" class="btn btn-danger" href="modifier-projet.php">Annuler</a>
                                        </div>
                                    </div>
                                    <p style="font-style:italic">Obligatoire (<span class="etoile">*</span>)</p>
                                    <?php if (isset($_SESSION['projetError'])) { ?>
                                        <p><span class="error-message"><i class="bi bi-exclamation-triangle"></i><?php echo $_SESSION['projetError'];
                                                                        unset($_SESSION['projetError']); ?></span></p>
                                    <?php } ?>
                                    <input type="hidden" name="rolesCollabo" id="rolesCollaboInput">
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </section>

    </main><!-- End #main -->


     <!-- Inclure les fichier javaScript -->
    <?php require_once("includes/fichiers-js.php"); ?>

    <script>
        // Tableau global pour stocker les liaisons entre les roles et les collaborateurs
        var rolesCollabo = [];

        function linkChoices() {
            var choixRoles = Array.from(document.getElementById("role").selectedOptions).map(option => option.value);
            var choixCollabo = document.getElementById("collabo").value;

            //Ajouter chaque choix de role  lié au choix du collaborateur dans le tableau de liaisons
            if (choixCollabo) {
                choixRoles.forEach(choice => {
                    //verifier si le role a ete selectionné
                    if (!choice) alert("Choisir un role avant de choisir un collaborateur");
                    else
                        rolesCollabo.push({
                            role: choice,
                            collabo: choixCollabo
                        });
                });
                //si le collaborateur n'a pas ete choisi
            } else alert("Choisir un collaborateur avant de valider");

            // Réinitialiser la sélection du collaborateur
            document.getElementById("collabo").selectedIndex = 0;
        }

        function finish() {
            // Mettre à jour la valeur du champ caché avec les données de rolesCollabo
            document.getElementById("rolesCollaboInput").value = JSON.stringify(rolesCollabo);

        }

        var supprimerRoleCollabo = document.querySelectorAll(".supprimer-role-collabo");
        supprimerRoleCollabo.forEach(item => 
        {
            item.addEventListener("click", function(e) {
                e.preventDefault();
                supprimer = confirm("Etes-vous sûr de vouloir supprimer ce role?");
                var lien = this.getAttribute("href");
                if (supprimer) {
                    window.location.href = lien;
                }
            });
        });

    </script>


</body>

</html>