<?php session_start();

//connexion a la base de donnees
require_once ("../includes/bdd-connect.php");



// Traitement de la réaction (like ou dislike) et mise à jour de la base de données
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idCommentaire = $_POST["id"];
    $action = $_POST["action"];

    // Vérifier si l'action a déjà été effectuée par l'utilisateur
    $stmt = $bdd->prepare("SELECT * FROM reaction_commentaire WHERE idCommentaire = ? AND idCollabo = ?");
    $stmt->execute([$idCommentaire, $idCollabo]);
    $reaction = $stmt->fetch(PDO::FETCH_ASSOC);
    if($reaction){
        // L'action a déjà été effectuée, mettre à jour la valeur
        if ($action == $reaction['action']) {
            //supprimer dans la base de donnees pour annuler l'action
            $stmt = $bdd->prepare("DELETE FROM reaction_commentaire WHERE idCommentaire = ? AND idCollabo = ? ");
            $stmt->execute([$idCommentaire, $idCollabo]);
        } else {
            $stmt = $bdd->prepare("UPDATE reaction_commentaire SET action = ? WHERE idCommentaire = ? AND idCollabo = ?");
            $stmt->execute([$action, $idCommentaire, $idCollabo]);
        }
    } else {
        // Nouvelle action, insérer une nouvelle entrée dans la table
        $stmt = $bdd->prepare("INSERT INTO reaction_commentaire (idCommentaire, idCollabo, action) VALUES (?, ?, ?)");
        $stmt->execute([$idCommentaire, $idCollabo, $action]);
    }

    // Récupérer les nouveaux compteurs de likes et dislikes
    $stmt = $bdd->prepare("SELECT COUNT(*) AS likeCount FROM reaction_commentaire WHERE idCommentaire = ? AND action = 'like'");
    $stmt->execute([$idCommentaire]);
    $likeCount = $stmt->fetchColumn();

    $stmt = $bdd->prepare("SELECT COUNT(*) AS dislikeCount FROM reaction_commentaire WHERE idCommentaire = ? AND action = 'dislike'");
    $stmt->execute([$idCommentaire]);
    $dislikeCount = $stmt->fetchColumn();

    // Exemple de réponse avec le nombre de likes/dislikes après mise à jour
    echo json_encode(["likes" => $likeCount, "dislikes" => $dislikeCount]);
}
