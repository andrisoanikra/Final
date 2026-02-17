<?php
/**
 * Script pour vérifier les données dans la base
 */
require_once __DIR__ . '/vendor/autoload.php';

// Charger Flight
Flight::path(__DIR__ . '/app');

// Charger la config
$ds = DIRECTORY_SEPARATOR;
require_once __DIR__ . $ds . 'app' . $ds . 'config' . $ds . 'config.php';
require_once __DIR__ . $ds . 'app' . $ds . 'config' . $ds . 'services.php';

$db = Flight::db();

echo "=== VÉRIFICATION DES DONNÉES ===\n\n";

// 1. Vérifier les besoins
$stmt = $db->runQuery("SELECT COUNT(*) as nb FROM besoins");
$result = $stmt->fetch();
echo "✓ Nombre de besoins: " . $result['nb'] . "\n";

// 2. Vérifier besoin_articles
$stmt = $db->runQuery("SELECT COUNT(*) as nb FROM besoin_articles");
$result = $stmt->fetch();
echo "✓ Nombre d'articles dans les besoins: " . $result['nb'] . "\n";

// 3. Vérifier les dons
$stmt = $db->runQuery("SELECT COUNT(*) as nb FROM dons");
$result = $stmt->fetch();
echo "✓ Nombre de dons: " . $result['nb'] . "\n";

// 4. Calculer le montant total
$stmt = $db->runQuery("
    SELECT COALESCE(SUM(ba.quantite * ba.prix_unitaire), 0) as montant_total 
    FROM besoins b 
    JOIN besoin_articles ba ON b.id_besoin = ba.id_besoin
");
$result = $stmt->fetch();
echo "✓ Montant total des besoins: " . number_format($result['montant_total'], 0, ',', ' ') . " Ar\n";

// 5. Vérifier la colonne quantite_satisfaite
$stmt = $db->runQuery("DESCRIBE besoin_articles");
$columns = $stmt->fetchAll();
$hasColumn = false;
foreach ($columns as $col) {
    if ($col['Field'] === 'quantite_satisfaite') {
        $hasColumn = true;
        break;
    }
}
echo ($hasColumn ? "✓" : "✗") . " Colonne quantite_satisfaite existe: " . ($hasColumn ? "OUI" : "NON") . "\n";

// 6. Vérifier les statuts
$stmt = $db->runQuery("
    SELECT statut, COUNT(*) as nb 
    FROM besoins 
    GROUP BY statut
");
echo "\nStatuts des besoins:\n";
while ($row = $stmt->fetch()) {
    echo "  - " . $row['statut'] . ": " . $row['nb'] . "\n";
}

echo "\n✅ Vérification terminée!\n";
echo "\nMaintenant, allez sur: http://votre-url/recapitulation\n";
