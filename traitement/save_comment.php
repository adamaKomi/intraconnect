<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test3";

try {
  // Créez une nouvelle connexion à la base de données
  $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
  // Définissez le mode d'erreur PDO sur exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Récupérez le commentaire du formulaire POST
  $comment = $_POST["comment"];

  // Préparez la requête SQL pour insérer le commentaire dans la base de données
  $stmt = $conn->prepare("INSERT INTO comment (content) VALUES (:content)");
  $stmt->bindParam(':content', $comment);

  // Exécutez la requête SQL
  $stmt->execute();

  header("Location:page3.php");
} catch(PDOException $e) {
  echo "Erreur : " . $e->getMessage();
}

// Fermez la connexion à la base de données
$conn = null;
?>

codeValue ="<script type='text/plain' class='language-" + language + "'>"+ codeValue +"<"+"/"+"script"+">"
codeValue = "<code> &lt;script type='text/plain' class='language-" + language + "'>" + codeValue + "</code>";