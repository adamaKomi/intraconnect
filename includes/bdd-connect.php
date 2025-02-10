<?php
//se connecter a la base de donnnees
try {
    $bdd = new PDO("mysql:host=localhost;dbname=intraconnect;charset=utf8", "root", "");
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupération de l'identifiant du collaborateur
    $stmtCollabo = $bdd->prepare("SELECT * FROM collabo WHERE username=?");
    $stmtCollabo->execute([$_SESSION['auth']]);
    $collaborateur = $stmtCollabo->fetch(PDO::FETCH_ASSOC);
    $idCollabo = $collaborateur['idCollabo'];
} catch (PDOException $e) {
    echo "Erreur, impossible de se connecter à la base de données : " . $e->getMessage();
}
