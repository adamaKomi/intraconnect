<?php
session_start();
// Connexion à la base de données
require_once("../includes/bdd-connect.php");



// // Pour l'envoi de mail
// Charger les variables d'environnement
require '../vendor/autoload.php';
//les parametres du mail
require_once('../includes/mail.php');

// pour annuler une formation
if (isset($_GET['action'], $_GET['idFormation']) && ($_GET['action'] == 'annuler-formation')) {
    $idFormation = $_GET['idFormation'];


    //recuperer les informations de la formation a annuler
    $stmt = $bdd->prepare("SELECT * FROM formation WHERE idFormation = ?");
    $stmt->execute([$idFormation]);
    $formation = $stmt->fetch(PDO::FETCH_ASSOC);



    // Annulation de la formation
    $stmt = $bdd->prepare("UPDATE formation SET statutFormation = 'annulé' WHERE idFormation = ?");
    $stmt->execute([$idFormation]);
    $Verif_annulation = $stmt->rowCount();

    // Enregistrer la notification
    $titre = "Formation annulée: \"" . $formation['themeFmt'] . "\"";
    $contenu = "Nous sommes désolés de vous informer que la formation \"" . $formation['themeFmt'] . "\" est malheureusement annulée; nous nous excusons pour les désagrements que cela pourrait causer";
    $lien = "pages-formations.php?idFormation=" . $idFormation;
    $stmt = $bdd->prepare("INSERT INTO notification(natureElement,titre,contenu,lien,type) VALUES(?,?,?,?,?)");
    $stmt->execute(["formation", $titre, $contenu, $lien, "privé"]);

    //recuperer l'identifiant de la notification qui vient d'etre inserer
    $stmt = $bdd->prepare("SELECT id FROM notification WHERE natureElement = ? AND titre = ? AND contenu = ? AND lien = ? AND type = ?");
    $stmt->execute(["formation", $titre, $contenu, $lien, "privé"]);
    $idNotification = $stmt->fetch(PDO::FETCH_ASSOC)['id'];

    if ($Verif_annulation > 0) { //annulation reussie

        //enregistrer celui qui a publié dans la table notificationCollabo pour l'envoie de notifications
        $stmt = $bdd->prepare("INSERT INTO notificationCollabo(idNotification,idCollabo) VALUES(?,?)");
        $stmt->execute([$idNotification, $idCollabo]);

        // Informer les inscrits que la formation est annulée

        //recuperer les inscrits de la formation
        $stmt = $bdd->prepare("SELECT collabo.idCollabo AS id ,collabo.email AS email FROM collabo
                                JOIN inscritfmt ON inscritfmt.idCollabo = collabo.idCollabo
                                JOIN formation ON formation.idFormation = inscritfmt.idFormation
                                WHERE formation.idFormation = ?");
        $stmt->execute([$idFormation]);
        $inscrits = $stmt->fetchAll(PDO::FETCH_ASSOC);


        //enfoyer des emails aux inscrits pour les informer de l'annulation de la formation
        if (isset($inscrits) && count($inscrits) > 0) {

            try {
                //sujet du mail
                $mail->Subject = 'Annulation de la formation: ' . $formation['themeFmt'];
                //contenu du mail
                $mail->Body = "Bonjour, nous sommes desoles de vous informer que la formation <b>\"" . $formation['themeFmt'] . "\"</b> a ete malheureusement annulee.
                Nous nous excusons pour les desagrements que cela pourrait causé.<br><br> ";

                // Envoi de l'e-mail à chaque inscrit
                foreach ($inscrits as $inscrit) {
                    $mail->addAddress($inscrit['email']);
                    $mail->send();
                    $mail->clearAddresses(); // pour eviter que tous les addresses mails se retrouvent dans l'entete du mail
                    //enregistrer dans la table notificationCollabo pour l'envoie de notifications
                    $stmt = $bdd->prepare(" INSERT INTO notificationCollabo (idNotification, idCollabo)
                                            SELECT ?, ?
                                            WHERE NOT EXISTS (
                                                SELECT 1 FROM notificationCollabo WHERE idNotification = ? AND idCollabo = ?
                                            )
                                        ");
                    $stmt->execute([$idNotification, $inscrit['id'], $idNotification, $inscrit['id']]);
                }
            } catch (Exception $e) {
                echo "Une erreur s'est produite lors de l'envoi de l'e-mail d'annulation de formation : {$mail->ErrorInfo}";
            }
        }
    }
    header("Location:../pages-formations.php");
    exit;


    //marquer une notification comme lue
} elseif ((isset($_GET['action'], $_GET['idNotification'])) && ($_GET['action'] == 'marquer-notification-comme-lu')) {
    try {
        $idNotification = $_GET['idNotification'];
        //marquer comme lue
        $stmt = $bdd->prepare('UPDATE notificationCollabo SET statut = 0 WHERE idNotification = ? AND idCollabo = ?');
        $stmt->execute([$idNotification, $idCollabo]);
        //la redirection
        if (isset($_SESSION['page_precedente'])) {
            $page_precedente = $_SESSION['page_precedente'];
            unset($_SESSION['page_precedente']);
            header("Location:" . $page_precedente);
            exit;
        } else {
            header("Location:../pages-notifications.php");
            exit;
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }

    //marquer une notification comme non lue
} elseif ((isset($_GET['action'], $_GET['idNotification'])) && ($_GET['action'] == 'marquer-notification-comme-non-lu')) {
    $idNotification = $_GET['idNotification'];
    //marquer comme lue
    $stmt = $bdd->prepare('UPDATE notificationCollabo SET statut = 1 WHERE idNotification = ? AND idCollabo = ?');
    $stmt->execute([$idNotification, $idCollabo]);
    //la redirection
    if (isset($_SESSION['page_precedente'])) {
        $page_precedente = $_SESSION['page_precedente'];
        unset($_SESSION['page_precedente']);
        header("Location:" . $page_precedente);
        exit;
    } else {
        header("Location:../pages-notifications.php");
        exit;
    }

    //marquer une formation comme terminee
} elseif ((isset($_GET['action'], $_GET['idFormation'])) && ($_GET['action'] == 'marquer-formation-terminee')) {
    $idFormation = $_GET['idFormation'];
    //marquer comme lue
    $stmt = $bdd->prepare("UPDATE formation SET statutFormation = 'terminé' WHERE idFormation = ?");
    $stmt->execute([$idFormation]);
    //la redirection
    if (isset($_SESSION['page_precedente'])) {
        $page_precedente = $_SESSION['page_precedente'];
        unset($_SESSION['page_precedente']);
        header("Location:" . $page_precedente);
        exit;
    } else {
        header("Location:../pages-notifications.php");
        exit;
    }
}
