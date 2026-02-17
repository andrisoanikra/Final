<?php
/**
 * Script de migration pour modifier la table achats
 * À exécuter une seule fois via le navigateur
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger la configuration
$config = require __DIR__ . '/../app/config/config.php';

try {
    $dbHost = $config['database']['host'] ?? 'localhost';
    $dbName = $config['database']['dbname'] ?? '';
    $dbUser = $config['database']['user'] ?? 'root';
    $dbPass = $config['database']['password'] ?? '';
    
    // Construire le DSN
    $xamppSocket = '/opt/lampp/var/mysql/mysql.sock';
    if ($dbHost === 'localhost') {
        if (file_exists($xamppSocket)) {
            $dsn = 'mysql:unix_socket=' . $xamppSocket . ';dbname=' . $dbName . ';charset=utf8mb4';
        } else {
            $dsn = 'mysql:host=127.0.0.1;dbname=' . $dbName . ';charset=utf8mb4';
        }
    } else {
        $dsn = 'mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8mb4';
    }
    
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Migration de la table achats</h2>";
    
    // Modifier la colonne id_article pour accepter NULL
    $sql = "ALTER TABLE achats MODIFY COLUMN id_article INT NULL";
    $pdo->exec($sql);
    
    echo "<div style='color: green; padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px;'>";
    echo "✅ <strong>Succès!</strong> La colonne id_article peut maintenant être NULL.<br>";
    echo "Les achats automatiques peuvent maintenant être créés sans article spécifique.";
    echo "</div>";
    
    // Vérifier la structure
    $result = $pdo->query("DESCRIBE achats");
    echo "<h3>Structure de la table achats:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $highlight = ($row['Field'] == 'id_article' && $row['Null'] == 'YES') ? 
            "style='background-color: #d4edda;'" : "";
        echo "<tr $highlight>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . ($row['Key'] ?? '') . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><br><a href='/' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Retour à l'application</a>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>";
    echo "❌ <strong>Erreur:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
