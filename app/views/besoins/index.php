<?php
/**
 * Liste des besoins
 */
$pageTitle = 'Liste des besoins - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            Liste des besoins
        </h1>
        <a href="<?= $base_url ?>/besoin/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Ajouter un besoin
        </a>
    </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        if ($_GET['success'] == 'besoin_ajoute') {
                            echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Besoin ajouté avec succès!';
                        } elseif ($_GET['success'] == 'besoin_supprime') {
                            echo 'Besoin supprimé avec succès!';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

    <?php if (empty($besoins)): ?>
        <div class="empty-state">
            <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            <h3 class="empty-state-title">Aucun besoin enregistré</h3>
            <p class="empty-state-text">Commencez par créer votre premier besoin</p>
            <a href="<?= $base_url ?>/besoin/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Créer un besoin
            </a>
        </div>
    <?php else: ?>
        <div class="card-grid">
            <?php foreach ($besoins as $besoin): ?>
                <div class="item-card">
                    <div class="item-card-header">
                        <h3 class="item-card-title">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <?php echo htmlspecialchars($besoin['nom_ville']); ?>
                        </h3>
                        <span class="item-card-id">#<?php echo htmlspecialchars($besoin['id_besoin']); ?></span>
                    </div>
                    
                    <div class="item-card-body">
                        <?php if (!empty($besoin['articles'])): ?>
                        <div class="item-card-row">
                            <svg class="item-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <div style="flex: 1;">
                                <div class="item-card-label">Articles</div>
                                <div class="item-card-value"><?php echo htmlspecialchars($besoin['articles']); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($besoin['types'])): ?>
                        <div class="item-card-row">
                            <svg class="item-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                            <div style="flex: 1;">
                                <div class="item-card-label">Types</div>
                                <div class="item-card-value">
                                    <?php 
                                        $types = explode(', ', $besoin['types']);
                                        foreach (array_unique($types) as $type) {
                                            echo '<span class="badge badge-secondary">' . htmlspecialchars($type) . '</span> ';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($besoin['description'])): ?>
                        <div class="item-card-row">
                            <svg class="item-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            <div style="flex: 1;">
                                <div class="item-card-label">Description</div>
                                <div class="item-card-value">
                                    <?php 
                                        echo htmlspecialchars(substr($besoin['description'], 0, 80));
                                        if (strlen($besoin['description']) > 80) echo '...';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="item-card-row">
                            <svg class="item-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <div style="flex: 1;">
                                <div class="item-card-label">Date</div>
                                <div class="item-card-value"><?php echo date('d/m/Y H:i', strtotime($besoin['date_saisie'])); ?></div>
                            </div>
                        </div>
                        
                        <div class="item-card-row">
                            <div style="flex: 1; display: flex; gap: 0.5rem; align-items: center;">
                                <?php 
                                    $urgenceClass = [
                                        'normale' => 'success',
                                        'urgente' => 'warning',
                                        'critique' => 'danger'
                                    ];
                                    $urgenceLabel = [
                                        'normale' => 'Normale',
                                        'urgente' => 'Urgente',
                                        'critique' => 'Critique'
                                    ];
                                ?>
                                <span class="badge badge-<?php echo $urgenceClass[$besoin['urgence'] ?? 'normale']; ?>">
                                    <?php echo $urgenceLabel[$besoin['urgence'] ?? 'normale']; ?>
                                </span>
                                
                                <?php 
                                    $statutClass = [
                                        'en_cours' => 'primary',
                                        'satisfait' => 'success',
                                        'partiel' => 'info'
                                    ];
                                    $statutLabel = [
                                        'en_cours' => 'En cours',
                                        'satisfait' => 'Satisfait',
                                        'partiel' => 'Partiel'
                                    ];
                                ?>
                                <span class="badge badge-<?php echo $statutClass[$besoin['statut']]; ?>">
                                    <?php echo $statutLabel[$besoin['statut']]; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Indicateur de progression -->
                    <?php 
                        // Calculer le montant total nécessaire
                        $montantTotal = isset($besoin['montant_total']) ? floatval($besoin['montant_total']) : 0;
                        
                        // Calculer le montant reçu (via dons dispatches)
                        $montantRecu = isset($besoin['montant_recu']) ? floatval($besoin['montant_recu']) : 0;
                        
                        // Calculer le pourcentage
                        $pourcentage = $montantTotal > 0 ? min(100, round(($montantRecu / $montantTotal) * 100, 1)) : 0;
                        
                        // Déterminer la classe de la barre
                        $progressClass = '';
                        $percentageClass = 'none';
                        if ($pourcentage >= 100) {
                            $progressClass = '';
                            $percentageClass = 'complete';
                        } elseif ($pourcentage >= 50) {
                            $progressClass = 'partial';
                            $percentageClass = 'partial';
                        } elseif ($pourcentage > 0) {
                            $progressClass = 'low';
                            $percentageClass = 'partial';
                        }
                    ?>
                    <div class="progress-container">
                        <div class="progress-label">
                            <span class="progress-label-text">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 4px;">
                                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                                </svg>
                                Évolution
                            </span>
                            <span class="progress-percentage <?php echo $percentageClass; ?>">
                                <?php echo $pourcentage; ?>%
                            </span>
                        </div>
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar <?php echo $progressClass; ?>" style="width: <?php echo $pourcentage; ?>%;"></div>
                        </div>
                        <div class="progress-details">
                            <div class="progress-amount">
                                <span class="progress-amount-label">Reçu:</span>
                                <span class="progress-amount-value"><?php echo number_format($montantRecu, 0, ',', ' '); ?> Ar</span>
                            </div>
                            <div class="progress-amount">
                                <span class="progress-amount-label">Besoin:</span>
                                <span class="progress-amount-value"><?php echo number_format($montantTotal, 0, ',', ' '); ?> Ar</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="item-card-footer">
                        <a href="<?= $base_url ?>/besoin/<?php echo $besoin['id_besoin']; ?>" class="btn btn-sm btn-primary" style="flex: 1;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Détails
                        </a>
                        <a href="<?= $base_url ?>/besoin/<?php echo $besoin['id_besoin']; ?>/delete" class="btn btn-sm btn-danger" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <svg class="stat-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                </svg>
                <div class="stat-card-label">Total besoins</div>
                <div class="stat-card-value"><?php echo count($besoins); ?></div>
            </div>
            <div class="stat-card danger">
                <svg class="stat-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div class="stat-card-label">Besoins critiques</div>
                <div class="stat-card-value">
                    <?php 
                        $critiques = array_filter($besoins, function($b) { 
                            return $b['urgence'] == 'critique'; 
                        });
                        echo count($critiques);
                    ?>
                </div>
            </div>
            <div class="stat-card">
                <svg class="stat-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <div class="stat-card-label">En cours</div>
                <div class="stat-card-value">
                    <?php 
                        $enCours = array_filter($besoins, function($b) { 
                            return $b['statut'] == 'en_cours'; 
                        });
                        echo count($enCours);
                    ?>
                </div>
            </div>
            <div class="stat-card success">
                <svg class="stat-card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div class="stat-card-label">Satisfaits</div>
                <div class="stat-card-value">
                    <?php 
                        $satisfaits = array_filter($besoins, function($b) { 
                            return $b['statut'] == 'satisfait'; 
                        });
                        echo count($satisfaits);
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
