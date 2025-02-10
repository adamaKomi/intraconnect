<?php
session_start();
//connexion a la base de donnees
require_once("../includes/bdd-connect.php");

// Charger les variables d'environnement
require '../vendor/autoload.php';
//les parametres du mail
require_once ('../includes/mail.php');

if (isset($_POST['action']) && isset($_POST['idFormation']) && $_POST['action'] == 'inscriptionFormation') {
    $idFormation = $_POST['idFormation'];

    //recuperer les informations de la formation
    $stmt = $bdd->prepare("SELECT * FROM formation WHERE idFormation = ?");
    $stmt->execute([$idFormation]);
    $formation = $stmt->fetch(PDO::FETCH_ASSOC);

    //verifier si l'utilisateur n'est pas inscrit a cette formation
    $stmt = $bdd->prepare("SELECT * FROM inscritfmt WHERE idFormation = ? AND idCollabo = ? ");
    $stmt->execute([$idFormation, $idCollabo]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        //faire l'inscription
        $stmt = $bdd->prepare('INSERT INTO inscritfmt (idFormation, idCollabo) VALUES (?, ?)');
        $stmt->execute([$idFormation, $idCollabo]);

        // Récupérer le nombre d'inscrits et faire une notification pour celui qui a publié la formation
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM inscritfmt WHERE idFormation = ?");
        $stmt->execute([$idFormation]);
        $nb_inscrits = $stmt->fetchColumn();

        //recuperer celui qui a publie la formation
        $stmt = $bdd->prepare("SELECT fp.idCollabo FROM fmtpubliee fp
                                JOIN formation f ON f.idFormation = fp.idFormation 
                                WHERE f.idFormation = ?
                            ");
        $stmt->execute([$idFormation]);
        $publicateur = $stmt->fetch(PDO::FETCH_ASSOC)['idCollabo'];

        //faire la notification
        $natureElement = "formation";
        $titre = "Inscription : \"" . $formation['themeFmt'] . "\"";
        $lien = "pages-formations.php?idFormation=" . $idFormation;
        $type = "privé";

        if ($nb_inscrits == 1) { //si la notification n'existe pas encore
            $contenu = "Il y a <b>1 inscrit</b> à votre formation \"" . $formation['themeFmt'] . "\"";
            $stmt = $bdd->prepare("INSERT INTO notification(natureElement,titre,contenu,lien,type) VALUES(?,?,?,?,?)");
            $stmt->execute([$natureElement, $titre, $contenu, $lien, $type]);

            //recuperer l'identifiant de la notification qui vient d'etre inserer
            $stmt = $bdd->prepare("SELECT id FROM notification WHERE natureElement = ? AND titre = ? AND contenu = ? AND lien = ? AND type = ?");
            $stmt->execute([$natureElement, $titre, $contenu, $lien, $type]);
            $idNotification = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            //faire une insertion dans la table notificationCollabo pour une notification privee
            $stmt = $bdd->prepare("INSERT INTO notificationCollabo(idNotification,idCollabo) VALUES(?,?)");
            $stmt->execute([$idNotification, $publicateur]);
        } else { //si la notification existe deja
            $contenu = "Il y a <b>$nb_inscrits inscrits</b> à votre formation \"" . $formation['themeFmt'] . "\"";
            //recuperer l'identifiant de la notification existante
            $stmt = $bdd->prepare("SELECT id FROM notification n 
                                 JOIN notificationCollabo nc ON nc.idNotification = n.id
                                 WHERE n.natureElement = ? AND n.titre = ? AND n.lien = ? AND n.type = ? AND nc.idCollabo = ?");
            $stmt->execute([$natureElement, $titre, $lien, $type, $publicateur]);
            $idNotification = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
            //mise à jour de la notification 
            $stmt = $bdd->prepare("UPDATE notification SET contenu = ?, dateAction = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$contenu, $idNotification]);
            //faire une insertion dans la table notificationCollabo pour une notification privee
            $stmt = $bdd->prepare("UPDATE notificationCollabo SET statut = 1, dateAction = CURRENT_TIMESTAMP WHERE idNotification = ? AND idCollabo = ?");
            $stmt->execute([$idNotification, $publicateur]);
        }
    }


    // Préparation de l'e-mail
    try {

        // Destinataire
        $mail->addAddress($collaborateur['email']);

        // Contenu de l'e-mail
        $mail->Subject = 'Inscription a la formation: <<' . $formation['themeFmt'] . '>>';
        $mail->Body = "Bonjour, vous etes inscrit a la formation <b>\"" . $formation['themeFmt'] . "\"</b>.<br><br>
                        <b>Informations complementaires :</b><br>
                        <b>Ouverture des inscriptions:</b> <i>" . $formation['DateDebut'] . "</i><br>
                        <b>Fermeture des inscriptions:</b> <i>" . $formation['DateFin'] . "</i><br><br>
                        <a href='" . $formation['lienFmt'] . "'>Acceder a la formation</a> <br>";
        // Envoi de l'e-mail
        $mail->send();
        $mail->clearAddresses(); // pour eviter que tous les addresses mails se retrouvent dans l'entete du mail

    } catch (Exception $e) {
        echo "Une erreur s'est produite lors de l'envoi de l'e-mail de confirmation : {$mail->ErrorInfo}";
    }
    //renvoyer un message de success
    echo json_encode(["success" => true]);
}
