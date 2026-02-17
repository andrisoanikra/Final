<?php

namespace app\controllers;

use Flight;

class AdminController
{
    protected $db;

    public function __construct()
    {
        $this->db = Flight::db();
    }

    /**
     * Affiche la page de confirmation de réinitialisation
     */
    public function confirmReset()
    {
        Flight::render('admin/confirm_reset', []);
    }

    /**
     * Réinitialise la base de données avec les données du fichier 07-reset-distributions.sql
     */
    public function reset()
    {
        try {
            // Chemin vers le fichier 07-reset-distributions.sql
            $resetFile = __DIR__ . '/../../app/persistance/07-reset-distributions.sql';
            
            if (!file_exists($resetFile)) {
                Flight::redirect('/tableau-bord?error=' . urlencode('Fichier 07-reset-distributions.sql introuvable'));
                return;
            }
            
            // Lire le contenu du fichier SQL
            $sql = file_get_contents($resetFile);
            
            if ($sql === false) {
                Flight::redirect('/tableau-bord?error=' . urlencode('Impossible de lire le fichier 07-reset-distributions.sql'));
                return;
            }
            
            // Diviser les requêtes SQL (séparées par point-virgule)
            $queries = array_filter(
                array_map('trim', explode(';', $sql)),
                function($query) {
                    // Ignorer les lignes vides et les commentaires
                    return !empty($query) && 
                           !preg_match('/^\s*--/', $query) &&
                           !preg_match('/^\s*\/\*/', $query);
                }
            );
            
            $executedQueries = 0;
            $errors = [];
            
            // Désactiver temporairement les vérifications de clés étrangères
            $this->db->runQuery('SET FOREIGN_KEY_CHECKS = 0');
            
            // Exécuter chaque requête
            foreach ($queries as $query) {
                $query = trim($query);
                if (empty($query)) continue;
                
                // Ignorer les commandes USE, SET FOREIGN_KEY_CHECKS car on les gère séparément
                if (preg_match('/^\s*(USE\s+|SET\s+FOREIGN_KEY_CHECKS)/i', $query)) {
                    continue;
                }
                
                try {
                    $this->db->runQuery($query);
                    $executedQueries++;
                } catch (\PDOException $e) {
                    // Logger toutes les erreurs pour diagnostic
                    $errors[] = substr($query, 0, 80) . '... : ' . $e->getMessage();
                }
            }
            
            // Réactiver les vérifications de clés étrangères
            $this->db->runQuery('SET FOREIGN_KEY_CHECKS = 1');
            
            // Créer un fichier de log pour diagnostic
            $logFile = __DIR__ . '/../../app/persistance/reset_log.txt';
            $logContent = "=== RESET LOG - " . date('Y-m-d H:i:s') . " ===\n";
            $logContent .= "Requêtes exécutées: $executedQueries\n";
            $logContent .= "Erreurs: " . count($errors) . "\n\n";
            foreach ($errors as $error) {
                $logContent .= "❌ $error\n\n";
            }
            file_put_contents($logFile, $logContent);
            
            if (empty($errors)) {
                Flight::redirect('/tableau-bord?success=reset&message=' . 
                    urlencode("✅ Base de données réinitialisée avec succès! ($executedQueries requêtes exécutées)"));
            } else {
                $errorMsg = "⚠️ Réinitialisation avec " . count($errors) . " erreur(s). Détails dans reset_log.txt. Premières erreurs: " . implode('; ', array_slice($errors, 0, 2));
                Flight::redirect('/tableau-bord?warning=' . urlencode($errorMsg));
            }
            
        } catch (\Exception $e) {
            Flight::redirect('/tableau-bord?error=' . urlencode('Erreur lors de la réinitialisation: ' . $e->getMessage()));
        }
    }
}
