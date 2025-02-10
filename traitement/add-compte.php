<?php
session_start();

//se connecter a la base de donnees
require_once ("../includes/bdd-connect.php");

// Charger les variables d'environnement
require '../vendor/autoload.php';
//les parametres du mail
require_once ('../includes/mail.php');


if (isset($_POST['createAccount'])) {
    //recuperer les donnees
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);

    //fonction pour creer un mot de passe
    function genererPassword($length = 8)
    {
        // Caractères autorisés pour le mot de passe
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*_?';
        // melanger les caractères et en choisir $length
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    // Générer un mot de passe fort de 8 caractères
    $password = genererPassword();
    //sauvegarder le mot de passe en clair
    $savedPass = $password;
    //crypter le mot de passe avant enregistrement
    $password = password_hash($savedPass, PASSWORD_DEFAULT);



    try {
        // Rechercher dans la base de données si l'username existe déjà
        $req = "SELECT * FROM collabo WHERE username=:user";
        $stmt = $bdd->prepare($req);
        $stmt->execute(["user" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            // Cet username existe déjà
            $_SESSION['errorUsername'] = true;
            $_SESSION['usernameExists'] = $username;
            $_SESSION['emailExists'] = $email;
            header("Location:../creer-compte.php");
            exit;
        }

        //verifier si l'email existe deja
        $req = "SELECT * FROM collabo WHERE email=:mail";
        $stmt = $bdd->prepare($req);
        $stmt->execute(["mail" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            // Cet email existe déjà
            $_SESSION['errorEmail'] = true;
            $_SESSION['emailExists'] = $email;
            $_SESSION['usernameExists'] = $username;
            header("Location:../creer-compte.php");
            exit;
        }



        //inserer l'utilisateur dans la bdd
        $stmt = $bdd->prepare("INSERT INTO collabo(username,email,password) VALUES(?,?,?)");
        $stmt->execute([$username, $email, $password]);
        //recuperer l'id de l'utilisateur qui a ete ajoute
        $stmt = $bdd->prepare("SELECT idCollabo FROM collabo WHERE username=?");
        $stmt->execute([$username]);
        $userId = $stmt->fetch(PDO::FETCH_ASSOC)['idCollabo'];


    

        //si l'utilisateur est nommee admin on l'ajoute dans la table des administrateurs
        if (isset($_POST['isAdmin'])) {
            $stmt = $bdd->prepare("INSERT INTO admin(idcollabo) VALUES(?)");
            $stmt->execute([$userId]);
        }

        // Préparation de l'e-mail
        // Utilisation de PHPMailer pour l'envoi d'e-mails
        require '../vendor/autoload.php'; // Chemin vers autoload.php de PHPMailer


        try {

            // Destinataire
            $mail->addAddress($email);

            // Contenu de l'e-mail
            $link = "http://localhost/IntraConnect2/pages-login.php"; //lien de redirection
            $mail->Subject = 'Creation de votre compte IntraConnect';
            $mail->Body = "Bonjour, un compte a créé pour vous sur notre plateforme de collaboration <b>IntraConnect</b>.<br><br>
                        <b>Vos informations de connexion :</b><br>
                        <b>Login:</b><i> $username</i><br>
                        <b>Password:</b><i> $savedPass</i><br><br>
                         Veuillez cliquer sur le lien suivant pour vous connecter : <a href='$link'>Se connecter</a> <br>";
            // Envoi de l'e-mail
            if ($mail->send()) {
                echo "<script>
                        alert('Cet compte a été créé avec succès !!!');
                        window.location.href = '../creer-compte.php'; 
                    </script>";
            }
        } catch (Exception $e) {
            echo "Une erreur s'est produite lors de l'envoi de l'e-mail de confirmation : {$mail->ErrorInfo}";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
