<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Besoin en argent</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .test-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        h2 {
            color: #0056b3;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        pre {
            background: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🧪 Test du système de besoin en argent</h1>

    <div class="test-section">
        <h2>1. Test de la structure de la table besoin_articles</h2>
        <?php
        require_once __DIR__ . '/app/config/config.php';
        
        try {
            $pdo = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4",
                $config['db']['user'],
                $config['db']['password']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Vérifier la structure de la table
            $stmt = $pdo->query("DESCRIBE besoin_articles");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<pre>";
            foreach ($columns as $col) {
                echo sprintf("%-25s %-15s %-10s\n", 
                    $col['Field'], 
                    $col['Type'], 
                    $col['Null']
                );
                
                if ($col['Field'] === 'id_article' && $col['Null'] === 'YES') {
                    echo "<span class='success'>✅ id_article accepte NULL - Besoin en argent supporté!</span>\n";
                }
            }
            echo "</pre>";
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2>2. Test d'insertion d'un besoin en argent</h2>
        <?php
        try {
            // Créer un besoin de test
            $pdo->exec("INSERT INTO besoins (id_ville, description, urgence) 
                        VALUES (1, 'Test besoin en argent', 'normale')");
            $id_besoin = $pdo->lastInsertId();
            
            // Insérer un besoin en argent (id_article = NULL)
            $stmt = $pdo->prepare("INSERT INTO besoin_articles 
                                   (id_besoin, id_article, quantite, prix_unitaire) 
                                   VALUES (?, NULL, 1, 50000)");
            $stmt->execute([$id_besoin]);
            
            echo "<p class='success'>✅ Insertion réussie d'un besoin en argent!</p>";
            echo "<pre>ID Besoin: $id_besoin\nMontant: 50 000 Ar</pre>";
            
            // Vérifier la récupération
            $stmt = $pdo->prepare("SELECT ba.*, 
                CASE 
                    WHEN ba.id_article IS NULL THEN '💰 Argent'
                    ELSE a.nom_article
                END as nom_article
                FROM besoin_articles ba
                LEFT JOIN articles a ON ba.id_article = a.id_article
                WHERE ba.id_besoin = ?");
            $stmt->execute([$id_besoin]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<p class='success'>✅ Récupération avec affichage correct:</p>";
            echo "<pre>";
            echo "Nom affiché: " . htmlspecialchars($result['nom_article']) . "\n";
            echo "Montant: " . number_format($result['quantite'] * $result['prix_unitaire'], 0, ',', ' ') . " Ar\n";
            echo "</pre>";
            
            // Nettoyer
            $pdo->exec("DELETE FROM besoins WHERE id_besoin = $id_besoin");
            echo "<p>🧹 Données de test nettoyées</p>";
            
        } catch (PDOException $e) {
            echo "<p class='error'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <div class="test-section">
        <h2>3. Accès au formulaire de création</h2>
        <p>
            <a href="/besoins/create" target="_blank" style="
                display: inline-block;
                background: #007bff;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 4px;
            ">🔗 Ouvrir le formulaire de création de besoin</a>
        </p>
        <p style="color: #6c757d; font-size: 0.9em;">
            Vérifiez que l'option "💰 Besoin en argent" apparaît dans la liste déroulante des articles.
        </p>
    </div>

    <div class="test-section">
        <h2>4. Instructions de test manuel</h2>
        <ol>
            <li>Cliquez sur le lien ci-dessus pour ouvrir le formulaire</li>
            <li>Sélectionnez une ville</li>
            <li>Dans la liste des articles, sélectionnez "💰 Besoin en argent"</li>
            <li>Vérifiez que:
                <ul>
                    <li>Le champ "Quantité" disparaît</li>
                    <li>Le champ "Prix unitaire" disparaît</li>
                    <li>Un champ "Montant (Ar)" apparaît</li>
                </ul>
            </li>
            <li>Saisissez un montant (ex: 50000)</li>
            <li>Ajoutez éventuellement d'autres articles (mixte article + argent)</li>
            <li>Soumettez le formulaire</li>
            <li>Vérifiez dans la liste des besoins que "💰 Argent" s'affiche correctement</li>
        </ol>
    </div>

    <div class="test-section">
        <h2>✅ Résumé des modifications</h2>
        <ul>
            <li>✅ Table besoin_articles: id_article accepte NULL</li>
            <li>✅ Formulaire: Option "💰 Besoin en argent" ajoutée</li>
            <li>✅ JavaScript: Basculement automatique quantité/montant</li>
            <li>✅ Contrôleur: Validation adaptée pour argent vs articles</li>
            <li>✅ Modèle: LEFT JOIN pour gérer NULL, affichage "💰 Argent"</li>
            <li>✅ Documentation: DOC-BESOIN-ARGENT.md créé</li>
        </ul>
    </div>
</body>
</html>
