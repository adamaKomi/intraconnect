<?php
session_start();

// Se connecter à la base de données
require_once("../includes/bdd-connect.php");

// Charger les variables d'environnement
require '../vendor/autoload.php';
//les parametres du mail
require_once ('../includes/mail.php');



// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, 'fichier-pour-mail.env');
// $dotenv->load();


if (isset($_POST['feedback'])) {
    try {
        // Récupérer les données du formulaire
        $sujetFeed = $_POST['sujet'];
        $contenuFeed = $_POST['contenu'];

        // Récupérer les administrateurs
        $stmt = $bdd->prepare("SELECT email FROM collabo NATURAL JOIN admin");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($admins);
        // exit;

        if (isset($admins) && count($admins) > 0) {
            // Utilisation de PHPMailer pour l'envoi d'e-mails
            
           

            // Contenu de l'e-mail
            $mail->Subject = 'Vous avez un feedback sur Intraconnect: "' . $sujetFeed . '"';
            $mail->Body = '<b>Expediteur: </b> '.$collaborateur['prenom'].' '.$collaborateur['nom'].'<br><br> <b>Message:</b> <br><br>'. $contenuFeed;

            // Envoi de l'e-mail à chaque administrateur
            foreach ($admins as $admin) {
                $email = $admin['email'];
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($admin['email']);
                    $mail->send();
                    $mail->clearAddresses();
                }
            }

            echo "
                <script>
                    window.location.href = '../pages-feedback.php?action=envoieReussi';
                </script>            
            ";
            exit();
        } else {
            throw new Exception("Il n'y a aucun administrateur sur cette plateforme pour recevoir le feedback");
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'e-mail: {$mail->ErrorInfo}";
    }
}
