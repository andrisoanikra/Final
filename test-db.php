<?php
/**
 * Script de test pour vérifier les données de la base
 */

// Configuration de la base de données
$host = 'localhost';
$dbname = 'bngrc_dons';
$user = 'root';
$password = '';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== TEST CONNEXION BASE DE DONNÉES ===\n";
    echo "✓ Connexion réussie!\n\n";
    
    // Test 1: Montant total des besoins
    $stmt = $pdo->query('SELECT COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_total FROM besoins b JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Montant total besoins: " . number_format($result['montant_total'], 0, ',', ' ') . " Ar\n";
    
    // Test 2: Montant satisfait
    $stmt = $pdo->query('SELECT COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_satisfait FROM besoins b JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin WHERE b.statut = "satisfait"');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Montant satisfait: " . number_format($result['montant_satisfait'], 0, ',', ' ') . " Ar\n";
    
    // Test 3: Nombre de besoins
    $stmt = $pdo->query('SELECT COUNT(*) as nb FROM besoins');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Nombre de besoins: " . $result['nb'] . "\n";
    
    // Test 4: Nombre de dons
    $stmt = $pdo->query('SELECT COUNT(*) as nb FROM dons');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Nombre de dons: " . $result['nb'] . "\n";
    
    // Test 5: Vérifier les tables
    echo "\n=== TABLES DISPONIBLES ===\n";
    $stmt = $pdo->query('SHOW TABLES');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
