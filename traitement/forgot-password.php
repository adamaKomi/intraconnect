<?php
session_start();

// Connexion à la base de données
require_once('../includes/bdd-connect.php');

// // Pour l'envoi de mail
// Charger les variables d'environnement
require '../vendor/autoload.php';
//les parametres du mail
require_once('../includes/mail.php');



if (isset($_POST['passwordForgot'], $_POST['email']) && !empty($_POST['email'])) {
    $email = $_POST['email'];
    try {

        //verifier si l'email existe dans la base de donnees
        $stmt = $bdd->prepare("SELECT * FROM collabo WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            //rediriger vers la page de changement du password
            $_SESSION['error'] = 'Aucun compte sous cet adresse email !!!';
            $_SESSION['theEmail'] = $email;
            header("Location:../mot-de-passe-oublie.php");
            exit;
        } else {
            $newPassword = $_POST['newPassword'];
            $renewPassword = $_POST['renewPassword'];



            if ($newPassword !== $renewPassword) {
                $_SESSION['error']  = "Les mots de pass ne sont pas similaires";
                $_SESSION['theEmail'] = $email;
                header("location:../mot-de-passe-oublie.php");
                exit;
            } else {
                $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $bdd->prepare("UPDATE collabo SET password=? WHERE email=?");
                $stmt->execute([$newPassword, $email]);

                //recuperer l'username
                $stmt = $bdd->prepare("SELECT username FROM collabo WHERE email = ?");
                $stmt->execute([$email]);
                $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

                //sujet du mail
                $mail->Subject = 'Changement de mot de pass';
                //contenu du mail
                $mail->Body = "Bonjour, votre mot de passe a été modifiée<br><br>Informations de connection : <br>Username: <b>\"$username\"</b><br>Mot de passe: <b>\"$renewPassword\"</b><br><br>";

                // Envoi de l'e-mail
                $mail->addAddress($email);
                if ($mail->send()) {
                    $mail->clearAddresses(); // Pour éviter que toutes les adresses mails se retrouvent dans l'entête du mail

                    $_SESSION['success-email'] = 'Votre mot de passe a été modifié avec succès.Vos informations de connexion ont été envoyées par mail.';
                    header("Location: ../pages-login.php");
                    exit;
                } else {
                    $_SESSION['error'] = 'Erreur lors de l\'envoi de l\'email.';
                    header("Location: ../mot-de-passe-oublie.php");
                    exit;
                }
            }
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
