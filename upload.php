<?php
// Configuration de la base de données
$host = "localhost";
$dbname = "test"; // Remplace par le nom de ta base de données
$username = "root"; // Remplace par ton nom d'utilisateur MySQL
$password = ""; // Remplace par ton mot de passe MySQL

// Répertoire de stockage des fichiers audio (doit être accessible en écriture par le serveur)
$uploadDir = "uploads/";

if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Créer le répertoire s'il n'existe pas
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["audio_data"])) {
        $audioFile = $_FILES["audio_data"];

        // Vérification des erreurs de téléchargement
        if ($audioFile["error"] == 0) {
                $tempFile = $audioFile["tmp_name"];
                $fileName = basename($audioFile["name"]);
                $destination = $uploadDir . $fileName;

                // Déplacer le fichier téléchargé vers le répertoire de destination
                if (move_uploaded_file($tempFile, $destination)) {
                        $pdo = null; // Initialiser la variable PDO

                        try {
                                // Connexion à la base de données avec PDO
                                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                                // Définir le mode d'erreur PDO sur Exception
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                // Préparation de la requête SQL
                                $sql = "INSERT INTO audios (nom_fichier, chemin_fichier, date_enregistrement) VALUES (:nom, :chemin, NOW())";
                                $stmt = $pdo->prepare($sql);

                                // Liaison des paramètres
                                $stmt->bindParam(':nom', $fileName);
                                $stmt->bindParam(':chemin', $destination);

                                // Exécution de la requête
                                if ($stmt->execute()) {
                                        echo "Audio enregistré et stocké dans la base de données avec succès !";
                                        header('lecture.php');
                                } else {
                                        echo "Erreur lors de l'enregistrement dans la base de données.";
                                }
                        } catch (PDOException $e) {
                                echo "Erreur de connexion ou d'exécution de la requête : " . $e->getMessage();
                        } finally {
                                // Fermeture de la connexion PDO
                                if ($pdo) {
                                        $pdo = null;
                                }
                        }
                } else {
                        echo "Erreur lors du déplacement du fichier.";
                }
        } else {
                echo "Erreur lors du téléchargement du fichier : " . $audioFile["error"];
        }
} else {
        echo "Aucun fichier audio reçu.";
}
?>