<?php
/**
 * Test de l'API de récapitulation
 */

require_once __DIR__ . '/../app/config/bootstrap.php';

use app\controllers\TableauBordController;

echo "<h1>Test de l'API de récapitulation</h1>";
echo "<hr>";

try {
    $controller = new TableauBordController();
    
    // Appeler directement la méthode privée via réflexion
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('getStatistiquesBesoins');
    $method->setAccessible(true);
    
    $stats = $method->invoke($controller);
    
    echo "<h2>Résultat de getStatistiquesBesoins():</h2>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    
    echo "<hr>";
    echo "<h2>Format JSON:</h2>";
    echo "<pre>";
    echo json_encode($stats, JSON_PRETTY_PRINT);
    echo "</pre>";
    
    echo "<hr>";
    echo "<h2>Vérification de la base de données:</h2>";
    
    $db = Flight::db();
    
    // Compter les besoins
    $stmt = $db->runQuery("SELECT COUNT(*) as total FROM besoins");
    $result = $stmt->fetch();
    echo "<p>Nombre de besoins: <strong>" . $result['total'] . "</strong></p>";
    
    // Compter les besoin_articles
    $stmt = $db->runQuery("SELECT COUNT(*) as total FROM besoin_articles");
    $result = $stmt->fetch();
    echo "<p>Nombre de lignes besoin_articles: <strong>" . $result['total'] . "</strong></p>";
    
    // Afficher quelques besoins
    $stmt = $db->runQuery("
        SELECT b.*, 
               (SELECT SUM(ba.quantite * ba.prix_unitaire) 
                FROM besoin_articles ba 
                WHERE ba.id_besoin = b.id_besoin) as montant_total
        FROM besoins b 
        LIMIT 5
    ");
    $besoins = $stmt->fetchAll();
    
    echo "<h3>Premiers besoins:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Ville</th><th>Statut</th><th>Montant</th></tr>";
    foreach ($besoins as $b) {
        echo "<tr>";
        echo "<td>" . $b['id_besoin'] . "</td>";
        echo "<td>" . $b['id_ville'] . "</td>";
        echo "<td>" . $b['statut'] . "</td>";
        echo "<td>" . number_format($b['montant_total'], 0, ',', ' ') . " Ar</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Afficher quelques besoin_articles
    $stmt = $db->runQuery("
        SELECT ba.*, 
               CASE 
                   WHEN ba.id_article IS NULL THEN '💰 Argent'
                   ELSE a.nom_article
               END as nom_article
        FROM besoin_articles ba
        LEFT JOIN articles a ON ba.id_article = a.id_article
        LIMIT 10
    ");
    $articles = $stmt->fetchAll();
    
    echo "<h3>Premiers articles de besoins:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID Besoin</th><th>Article</th><th>Quantité</th><th>Prix unitaire</th><th>Total</th></tr>";
    foreach ($articles as $art) {
        $total = $art['quantite'] * $art['prix_unitaire'];
        echo "<tr>";
        echo "<td>" . $art['id_besoin'] . "</td>";
        echo "<td>" . htmlspecialchars($art['nom_article']) . "</td>";
        echo "<td>" . $art['quantite'] . "</td>";
        echo "<td>" . number_format($art['prix_unitaire'], 0, ',', ' ') . " Ar</td>";
        echo "<td>" . number_format($total, 0, ',', ' ') . " Ar</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
