<?php
/**
 * Test direct de l'API de récapitulation
 */

// Configuration
$host = 'localhost';
$dbname = 'bngrc_dons';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>TEST DE L'API RÉCAPITULATION</h2>";
    echo "<pre>";
    
    // Test 1: Montant total
    echo "1. Montant total des besoins:\n";
    $stmt = $pdo->query("
        SELECT COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_total
        FROM besoins b
        JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Résultat: " . number_format($result['montant_total'], 0, ',', ' ') . " Ar\n\n";
    
    // Test 2: Montant satisfait
    echo "2. Montant satisfait:\n";
    $stmt = $pdo->query("
        SELECT COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_satisfait
        FROM besoins b
        JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        WHERE b.statut = 'satisfait'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Résultat: " . number_format($result['montant_satisfait'], 0, ',', ' ') . " Ar\n\n";
    
    // Test 3: Nombre de besoins
    echo "3. Nombre de besoins:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as nb FROM besoins");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Résultat: " . $result['nb'] . " besoins\n\n";
    
    // Test 4: Nombre de dons
    echo "4. Nombre de dons:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as nb FROM dons");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Résultat: " . $result['nb'] . " dons\n\n";
    
    // Test 5: Détails des besoins
    echo "5. Liste des besoins:\n";
    $stmt = $pdo->query("
        SELECT b.id_besoin, b.description, b.statut, 
               SUM(ba.quantite * ba.prix_unitaire) as montant
        FROM besoins b
        JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
        GROUP BY b.id_besoin
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   - Besoin #" . $row['id_besoin'] . ": " . substr($row['description'], 0, 40) . "...\n";
        echo "     Statut: " . $row['statut'] . ", Montant: " . number_format($row['montant'], 0, ',', ' ') . " Ar\n";
    }
    
    echo "\n</pre>";
    echo "<p style='color: green;'><strong>✓ Connexion réussie!</strong></p>";
    echo "<p><a href='/recapitulation'>Aller à la page de récapitulation</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ Erreur de connexion:</strong> " . $e->getMessage() . "</p>";
}
