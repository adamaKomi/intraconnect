<?php session_start();

//connexion a la base de donnees
require_once("../includes/bdd-connect.php");



// Traitement de la réaction (like ou dislike) et mise à jour de la base de données
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (isset($_POST['note'])) {
        try {
            $idCommentaire = $_POST["id"];
            $note = $_POST['note'];
            // Vérifier si l'action a déjà été effectuée par l'utilisateur
            $stmt = $bdd->prepare("SELECT * FROM reaction_note WHERE idCommentaire = ? AND idCollabo = ?");
            $stmt->execute([$idCommentaire, $idCollabo]);
            $reaction = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$reaction) {
                // Nouvelle action, insérer une nouvelle entrée dans la table
                $stmt = $bdd->prepare("INSERT INTO reaction_note (idCommentaire, idCollabo, note) VALUES (?, ?, ?)");
                $stmt->execute([$idCommentaire, $idCollabo, $note]);
            }


            // Préparez la requête SQL pour calculer la moyenne et le nombre de votes
            $stmt = $bdd->prepare("SELECT AVG(note) AS moyenne, COUNT(note) AS nombreVotes FROM reaction_note WHERE idCommentaire = ?");
            $stmt->execute([$idCommentaire]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Récupérez les valeurs de moyenne et de nombre de votes
            $moyenne = $result['moyenne'];
            $totalVote = $result['nombreVotes'];

            // Réponse avec la moyenne et le nombre de votes
            echo json_encode(["moyenne" => round($moyenne, 1), "totalVote" => $totalVote]);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
