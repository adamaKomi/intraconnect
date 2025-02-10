<?php
//authentification obligatoire
require("traitement/auth_connect_needed.php");
// Connexion à la base de données
require_once("includes/bdd-connect.php");
//les fonctions
include_once("includes/fonctions.php");


try {

    //chercher les interlocuteurs 
    $stmt = $bdd->prepare(" SELECT 
        c.idCollabo,
        c.nom, 
        c.prenom, 
        c.imageProfil, 
        c.imageProfilType, 
        c.job, 
        m.contenu, 
        m.statut, 
        m.dateAction,
        m.id_emetteur
    FROM 
        collabo c
    JOIN 
        (SELECT 
            m.id_emetteur, 
            m.contenu, 
            m.statut, 
            m.dateAction,
            m.id_recepteur
        FROM 
            message m
        WHERE 
            m.id_recepteur = ? OR m.id_emetteur = ?
        ORDER BY 
            m.dateAction DESC
        ) m 
    ON 
        m.id_emetteur = c.idCollabo OR m.id_recepteur = c.idCollabo
    GROUP BY 
        c.idCollabo
    ORDER BY 
        MAX(m.statut) DESC, 
        MAX(m.dateAction) DESC
");
    $stmt->execute([$idCollabo, $idCollabo]);

    $interlocuteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($interlocuteurs);
    // exit;

    //la discussion en cours 
    if (isset($_GET['id_emetteur'])) {
        $id_emetteur = $_GET['id_emetteur'];

        //interlocuteur courant
        $stmt = $bdd->prepare("SELECT idCollabo, nom, prenom, imageProfil, imageProfilType, job FROM collabo WHERE idCollabo = ?");
        $stmt->execute([$id_emetteur]);
        $interlocuteurCourant = $stmt->fetch(PDO::FETCH_ASSOC);

        //recuperer les messages de la discussion entre l'emetteur courant et le recepteur
        $stmt = $bdd->prepare("SELECT * FROM message WHERE id_recepteur = ? AND id_emetteur = ? OR id_recepteur = ? AND id_emetteur = ?");
        $stmt->execute([$idCollabo, $id_emetteur, $id_emetteur, $idCollabo]);
        $MesDiscusssionCourant = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($MesDiscusssionCourant);


        // marquer les messages de la  discution comme lus
        $stmt = $bdd->prepare("UPDATE message SET statut = 0 WHERE id_emetteur = ?");
        $stmt->execute([$id_emetteur]);
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}


?>

<!doctype html>
<html lang="En">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



    <title>Social Community</title>

    <link rel="icon" type="image/png" href="assets/vendor/post/images/favicon.png">
    <!-- Inclure les fichier css -->
    <?php require_once "includes/fichiers-css.php"; ?>



    <style>
        body,
        #section,
        .main-content-wrapper {
            background-color: white;
        }
        .pagetitle{
            margin-bottom: 50px;
        }

        .main-content-wrapper,
        .principal-bloc,
        .les-messages {
            min-width: 100%;
            min-height: 100%;
            padding: 0;
            margin: 0;
        }

        .entete {
            width: 100%;
            height: 100px;
            line-height: 100px;
            padding: 5px 50px;
            background-color: #f4f9ff;
            border-radius: 10px;
        }

        .principal-bloc {
            display: flex;
            flex-direction: row;
        }

        /* La liste des discussions */
        .discution {
            display: flex;
            flex-direction: column;
            width: 40%;
            margin-right: 50px;
            /* border-right: 1px solid black; */
            padding-top: 50px;
        }

        .discution-item {
            display: flex;
            flex-direction: row;
            border-top: 1px solid black;
            padding: 20px 0;
        }

        .discution-item:hover {
            background-color: #f4f9ff;
            cursor: pointer;
        }

        .discution-item img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .discution-item div div {
            margin-bottom: 5px;
        }


        /* la messagerie courante */
        .la-messagerie {
            width: 60%;
            background-color: #f4f9ff;
            padding: 20px;
            border-left: 2px solid black;
        }

        /* les informations du profil */
        .le-profil {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            background-color: #000;
            padding: 10px;
            text-align: center;
            border-radius: 40px;
        }

        .le-profil img {
            margin-right: 50px;
            width: 100px;
            height: 100px;
            border-radius: 100%;
            border: 5px solid white;
            background-color: white;
        }

        .le-profil .nomPrenom {
            margin-bottom: 10px;
        }

        .le-profil .nomPrenom p {
            margin: 0;
            text-align: start;
        }

        .le-profil .nomPrenom p span,
        .le-profil>div p span {
            color: gold;
        }

        .le-profil div p {
            color: white;
            font-weight: bold;
        }

        /* les messages proprement .discution-item */
        .les-messages {
            margin-top: 150px;
        }

        .les-messages div {
            margin-bottom: 100px;
        }

        .les-messages div div {
            padding: 5px;

        }

        /* dispostion des messages */
        .message-recepteur {
            margin-right: 20%;
            background-color: rgba(255, 255, 0, 0.1);
            padding: 0 10px;
            border-radius: 5px;
        }

        .message-emetteur {
            background-color: white;
            margin-left: 20%;
            padding: 0 10px;
            border-radius: 5px;
        }
        .contenu-message .message{
            color: black;
        }




        .entete {
            margin-bottom: 100px;
        }

        /* Formulaire d'envoie des messages */
        .separateur {
            height: 200px;
        }

        /* .formulaire-messagerie{
            position: fixed;
            bottom: 0vh;
            right: 50px;
            width: 40%;
        } */
        .comment-area {
            min-height: 200px !important;
        }

        .form-group {
            position: fixed;
            /* position: relative !important; */
            margin: 20px 0 5px 0 !important;
            bottom: 0;
            right: 20px;
            width: 40%;
        }

        .btn-envoie {
            position: absolute !important;
            right: 20px !important;
            bottom: 20px !important;
        }

        .btn-envoie .bi-send-fill {
            color: blue !important;
        }

        .btn-envoie .bi-send-fill:hover {
            color: red !important;

        }

        .btn-envoie .bi-send-fill:active {
            color: green !important;
        }

        .form-group button {
            background-color: transparent !important;
        }

        .bi-send-fill {
            cursor: pointer;
        }

        /* faire disparaitre l'element */
        .disparaitre {
            display: none;
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
            <h1>Messagerie</h1>
            
        </div><!-- End Page Title -->

        <section class="section " id="section">
            <div class="main-content-wrapper d-flex flex-row justify-content-between row">
                <div>
                    <?php if (isset($collaborateur['nom'], $collaborateur['prenom'])) : ?>
                        <div class="container principal-bloc">
                            <!-- liste des discutions -->
                            <div class="discution">
                                <div class="mb-5">
                                    <button class="btn btn-primary mb-3 btn-ouvrir-discussion">Ouvrir une discussion</button>
                                    <?php

                                    //recuperer la liste des utilisateurs
                                    $stmt = $bdd->prepare("SELECT idCollabo,nom,prenom,job FROM collabo ORDER BY prenom ASC,nom ASC");
                                    $stmt->execute();
                                    $utilisateurs = $stmt->fetchAll();

                                    ?>
                                    <?php if (isset($utilisateurs) && count($utilisateurs) > 0) : ?>
                                        <select name="theUser" class="form-select form-select-sm disparaitre liste-users" aria-label="Small select example">
                                            <option selected>--Selectionner un interlocuteur--</option>
                                            <?php foreach ($utilisateurs as $key => $utilisateur) : ?>
                                                <?php if ($utilisateur['idCollabo'] != $idCollabo && isset($utilisateur['prenom'], $utilisateur['nom'])) : ?>
                                                    <option value="<?php echo $utilisateur['idCollabo']; ?>"><?php echo $utilisateur['prenom'] . ' ' . $utilisateur['nom']; ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else : ?>
                                        <div>
                                            <h5>Il n'y a aucun utilisateur sur ce site</h5>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($interlocuteurs) && count($interlocuteurs) > 0) : ?>
                                    <?php foreach ($interlocuteurs as $key => $interlocuteur) : ?>
                                        <?php
                                        // Gérer la photo de profil
                                        $srcProfil_interlocuteur = null;
                                        $imageData = null;
                                        $imageType = null;
                                        if (!empty($interlocuteur['imageProfil']) && !empty($interlocuteur['imageProfilType'])) {
                                            // Afficher l'image
                                            $imageData = base64_encode($interlocuteur['imageProfil']); // Convertir les données de l'image en base64
                                            $imageType = $interlocuteur['imageProfilType']; // Récupérer le type de l'image
                                            $srcProfil_interlocuteur = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image
                                            unset($imageData);
                                            unset($imageType);
                                        }

                                        ?>
                                        <?php if ($interlocuteur['idCollabo'] != $idCollabo) : ?>
                                            <?php

                                            //recuperer le nombre de messages non lus venant de cet utilisateur
                                            $stmt = $bdd->prepare("SELECT COUNT(*) FROM message WHERE id_recepteur = ? AND id_emetteur = ? AND statut = ?");
                                            $stmt->execute([$idCollabo, $interlocuteur['idCollabo'], "1"]);
                                            $nbMesNonLu = $stmt->fetchColumn();

                                            ?>
                                            <div class="discution-item" data-lien="pages-messages.php?id_emetteur=<?php echo $interlocuteur['idCollabo']; ?>">
                                                <img src="<?php echo isset($srcProfil_interlocuteur) ? $srcProfil_interlocuteur : "assets/img/profile-inconnu.png";
                                                            unset($srcProfil_interlocuteur); ?>" alt="Photo de profile">
                                                <div>
                                                    <div><span><?php echo $interlocuteur['prenom'] . ' ' . $interlocuteur['nom'] ?></span></div>
                                                    <?php if (isset($nbMesNonLu) && $nbMesNonLu > 0) : ?>
                                                        <div>
                                                            <p>
                                                                <span class="badge bg-success badge-number"><?php echo $nbMesNonLu; ?></span>
                                                                <span><?php echo $nbMesNonLu > 1 ? 'Nouveaux messages' : 'Nouveau message';
                                                                        unset($nbMesNonLu); ?></span>
                                                            </p>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="discution">
                                        <p style="text-align:center;font-size:18px;">Vous n'avez aucune discussion en cours</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- champs de messagerie -->
                            <div class="la-messagerie">
                                <?php if (isset($interlocuteurCourant)) : ?>
                                    <?php
                                    // Gérer la photo de profil
                                    if (!empty($interlocuteurCourant['imageProfil']) && !empty($interlocuteurCourant['imageProfilType'])) {
                                        // Afficher l'image
                                        $imageData = base64_encode($interlocuteurCourant['imageProfil']); // Convertir les données de l'image en base64
                                        $imageType = $interlocuteurCourant['imageProfilType']; // Récupérer le type de l'image
                                        $srcProfil_discussionCourant = "data:{$imageType};base64,{$imageData}"; // Format de l'URL de l'image
                                        unset($imageData);
                                        unset($imageType);
                                    }



                                    ?>
                                    <div>
                                        <!-- le profil -->
                                        <div class="le-profil">
                                            <img src="<?php echo isset($srcProfil_discussionCourant) ? $srcProfil_discussionCourant : "assets/img/profile-inconnu.png";
                                                        unset($srcProfil_discussionCourant);
                                                        ?>" alt="Photo de profile">
                                            <div>
                                                <div class="nomPrenom">
                                                    <p><span>Prenom : </span> <?php echo isset($interlocuteurCourant['prenom']) ? $interlocuteurCourant['prenom'] : 'Inconnu' ?></p>
                                                    <p><span>Nom : </span><?php echo isset($interlocuteurCourant['nom']) ? $interlocuteurCourant['nom'] : 'Inconnu' ?></p>
                                                </div>
                                                <p><span>Job : </span> <?php echo isset($interlocuteurCourant['job']) ? $interlocuteurCourant['job'] : 'Non defini'; ?></p>
                                            </div>
                                        </div>
                                        <!-- les messages -->
                                        <div class="les-messages">
                                            <?php if (isset($MesDiscusssionCourant) and count($MesDiscusssionCourant) > 0) : ?>
                                                <?php foreach ($MesDiscusssionCourant as $key => $UnMessage) : ?>
                                                    <div class="contenu-message <?php echo $UnMessage['id_recepteur'] == $idCollabo ? 'message-recepteur' : 'message-emetteur'; ?>">
                                                        <div>
                                                            <p class="message" ><?php echo $UnMessage['contenu'] ?></p>
                                                            <p><?php echo dateAction($UnMessage['dateAction']) ?></p>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <div>
                                                    <p style="text-align: center;font-size:24px;">Il n'y a aucun message dans cette discussion</p>
                                                </div>
                                            <?php endif; ?>
                                            <div class="separateur"></div>
                                            <!-- le formulaire d'envoie de message -->
                                            <div class="formulaire-messagerie">
                                                <form action="traitement/send-message.php?id_recepteur=<?php echo $interlocuteurCourant['idCollabo']; ?>" method="post">
                                                    <div class="form-group">
                                                        <!-- zone de saisi du commentaire -->
                                                        <textarea name="message" class="form-control comment-area" placeholder="Saisir votre message ici..."></textarea>
                                                        <label for="send-fill" class="btn-envoie" style="color: blue !important;">
                                                            <button type="submit" class="btn btn-transparent p-0 border-0" name="envoyer-message">
                                                                <i class="bi bi-send-fill  fs-5 primary" id="send-fill"></i>
                                                            </button>
                                                        </label>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div>
                                        <p style="text-align: center;font-size:24px;">Veuillez choisir une discution...</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div>
                            <h2>Vous devez renseigner vos nom et prenom avant d'avoir acces a la messagerie</h2>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
    <!-- End Main Content Wrapper Area -->


    <!-- Inclure les fichier javaScript -->
    <?php require_once "includes/fichiers-js.php"; ?>


    <script>
        //selection de la discution 
        var discussion_items = document.querySelectorAll('.discution-item');
        discussion_items.forEach(function(discussion_item) {
            discussion_item.addEventListener('click', function(e) {
                var lien = this.getAttribute('data-lien');
                window.location.href = lien;
            });
        });

        //ouvrir une discution a travers la liste
        //bouton pour afficher la liste des utilisateurs
        var btn_ouvrir_discussion = document.querySelector('.btn-ouvrir-discussion');
        //la liste des utilisateurs
        var liste_users = document.querySelector('.liste-users');
        //afficher la liste quand on clique sur le bouton
        btn_ouvrir_discussion.addEventListener('click', function() {
            liste_users.classList.remove('disparaitre');
        });
        //evenement lorsqu'on selectionne un utilisateur
        liste_users.addEventListener('change', function() {
            var id_emetteur = this.value;
            window.location.href = "pages-messages.php?id_emetteur=" + id_emetteur;
        });
    </script>
</body>

</html>