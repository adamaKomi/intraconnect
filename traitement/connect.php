<?php
session_start();
//se connecter a la base de donnees
require_once ('../includes/bdd-connect.php');


//verifier l'existence des donnees
if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
       
        // recupere la base de donnees
        $req = "SELECT * FROM collabo WHERE username=:user";
        $stmt = $bdd->prepare($req);
        $stmt->execute(["user" => $username]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        //verifier le resultat
        if($result)
        {
            // Vérifier si le mot de passe correspond
            if (password_verify($password, $result['password'])) {
                //verifier la nature du l'utilisateur(admin ou simple)
                //recuperer l'id de l'utilisateur
                $stmtCollabo = $bdd->prepare("SELECT idCollabo FROM collabo WHERE username=?");
                $stmtCollabo->execute([$username]);
                $idCollabo = $stmtCollabo->fetch(PDO::FETCH_ASSOC)['idCollabo'];
                //verifier s'il existe dans la table admin
                $stmt = $bdd->prepare("SELECT * FROM admin WHERE idCollabo=?");
                $stmt->execute([$idCollabo]);
                $idAdmin = $stmt->fetch(PDO::FETCH_ASSOC)['idAdmin'];

                if($idAdmin)
                {
                    //creer une variable de session pour l'administrateur
                    $_SESSION['admin'] = $idAdmin;
                }
                // Authentification réussie
                unset($_SESSION['error']);
                $_SESSION['auth'] = $username;
                $redirect = isset($_SESSION['from']) ? $_SESSION['from'] : "../users-profile.php";
                unset($_SESSION['from']);
                header("Location: $redirect");
                exit;
            } else {//authentification echoue
                $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect";
                header("Location: ../pages-login.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Login ou mot de passe incorrect";
            header("location:../pages-login.php");
        }
        exit; //bien s'assurer que la redirection est bien faite

    } catch (PDOException $e) {//gestion des erreurs
        echo "Erreur : " . $e->getMessage();
    }
}
?>