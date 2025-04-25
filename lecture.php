<?php
// Configuration de la base de données
$host = "localhost";
$dbname = "test"; // Remplace par le nom de ta base de données
$username = "root"; // Remplace par ton nom d'utilisateur MySQL
$password = ""; // Remplace par ton mot de passe MySQL

$audios = [];

try {
        // Connexion à la base de données avec PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        // Définir le mode d'erreur PDO sur Exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Préparation de la requête SQL pour récupérer tous les audios (ou selon tes besoins)
        $sql = "SELECT id, nom_fichier, chemin_fichier FROM audios ORDER BY date_enregistrement DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // Récupération des résultats sous forme de tableau associatif
        $audios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($audios as $audio) {
                echo "<p>" . htmlspecialchars($audio["nom_fichier"]) . "</p>";
                echo "<audio controls src=\"" . htmlspecialchars($audio["chemin_fichier"]) . "\"></audio>";
                echo "<hr>"; // Ajoute une ligne de séparation entre les audios (optionnel)
        }


} catch (PDOException $e) {
        echo "Erreur de connexion ou d'exécution de la requête : " . $e->getMessage();
} finally {
        // Fermeture de la connexion PDO
        if ($pdo) {
                $pdo = null;
        }
}

// Encodage des données au format JSON pour pouvoir les utiliser en JavaScript
echo json_encode($audios);
?>