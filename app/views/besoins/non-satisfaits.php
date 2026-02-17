<?php
/**
 * Besoins non satisfaits
 */
$pageTitle = 'Besoins non satisfaits - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            Besoins non satisfaits
        </h1>
        <a href="<?= $base_url ?>/besoin/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Ajouter un besoin
        </a>
    </div>

            <?php if (empty($besoins)): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <h3>Tous les besoins ont été satisfaits!</h3>
                    <p>Excellent travail. Aucun besoin en attente.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-warning mb-4">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 8px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <strong><?php echo count($besoins); ?></strong> besoin(s) en attente
                </div>
                
                <div class="card-grid">
                    <?php foreach ($besoins as $besoin): ?>
                        <div class="item-card">
                            <div class="card-header-flex">
                                <div class="card-title-with-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                    </svg>
                                    <h3>Besoin #<?php echo htmlspecialchars($besoin['id_besoin']); ?></h3>
                                </div>
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
                            </div>

                            <div class="card-body">
                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    </svg>
                                    <span class="info-label">Ville:</span>
                                    <span class="info-value font-bold"><?php echo htmlspecialchars($besoin['nom_ville']); ?></span>
                                </div>

                                <?php if (!empty($besoin['articles'])): ?>
                                    <div class="info-row">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        </svg>
                                        <span class="info-label">Articles:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($besoin['articles']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($besoin['types'])): ?>
                                    <div class="info-row">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="7" height="7"></rect>
                                            <rect x="14" y="3" width="7" height="7"></rect>
                                            <rect x="14" y="14" width="7" height="7"></rect>
                                            <rect x="3" y="14" width="7" height="7"></rect>
                                        </svg>
                                        <span class="info-label">Types:</span>
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                            <?php 
                                                $types = explode(', ', $besoin['types']);
                                                foreach (array_unique($types) as $type) {
                                                    echo '<span class="badge badge-secondary">' . htmlspecialchars($type) . '</span>';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($besoin['description'])): ?>
                                    <div class="info-row">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                        </svg>
                                        <span class="info-label">Description:</span>
                                        <span class="info-value"><?php echo htmlspecialchars(substr($besoin['description'], 0, 80)); ?><?php echo strlen($besoin['description']) > 80 ? '...' : ''; ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="info-row">
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <span class="info-label">Statut:</span>
                                    <span class="badge badge-<?php echo $statutClass[$besoin['statut']]; ?>">
                                        <?php echo $statutLabel[$besoin['statut']]; ?>
                                    </span>
                                </div>

                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span class="info-label">Date:</span>
                                    <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($besoin['date_saisie'])); ?></span>
                                </div>
                            </div>
                            
                            <!-- Indicateur de progression -->
                            <?php 
                                // Calculer le montant total nécessaire (si disponible)
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

                            <div class="card-actions">
                                <a href="<?= $base_url ?>/besoin/<?php echo $besoin['id_besoin']; ?>" class="btn btn-secondary btn-sm">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Voir détails
                                </a>
                                <a href="<?= $base_url ?>/besoin/<?php echo $besoin['id_besoin']; ?>/delete" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Supprimer
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>