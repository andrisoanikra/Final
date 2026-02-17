<?php
/**
 * Détails d'une ville
 */
$pageTitle = htmlspecialchars($ville['nom_ville']) . ' - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                </svg>
                <?php echo htmlspecialchars($ville['nom_ville']); ?>
            </h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($ville['nom_region']); ?></p>
        </div>
        <a href="<?= $base_url ?>/besoin/create?ville_id=<?php echo $ville['id_ville']; ?>" class="btn btn-primary">
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
                            echo 'Besoin ajouté avec succès!';
                        } elseif ($_GET['success'] == 'besoin_supprime') {
                            echo 'Besoin supprimé avec succès!';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        if ($_GET['success'] == 'besoin_ajoute') {
                            echo 'Besoin ajouté avec succès!';
                        } elseif ($_GET['success'] == 'besoin_supprime') {
                            echo 'Besoin supprimé avec succès!';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($besoins)): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    <h3>Aucun besoin pour cette ville</h3>
                    <p>Commencez par ajouter des besoins pour suivre les demandes des sinistrés.</p>
                    <a href="<?= $base_url ?>/besoin/create?ville_id=<?php echo $ville['id_ville']; ?>" class="btn btn-primary">Créer un besoin</a>
                </div>
            <?php else: ?>
                <div class="card-grid">
                    <?php foreach ($besoins as $besoin): ?>
                        <div class="item-card">
                            <div class="card-header-flex">
                                <div class="card-title-with-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                    </svg>
                                    <h3><?php echo htmlspecialchars($besoin['nom_article']); ?></h3>
                                </div>
                                <span class="badge badge-secondary"><?php echo htmlspecialchars($besoin['libelle_type'] ?? ''); ?></span>
                            </div>

                            <div class="card-body">
                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M9 11l3 3L22 4"></path>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    <span class="info-label">Quantité:</span>
                                    <span class="info-value"><?php echo number_format($besoin['quantite'], 2, ',', ' '); ?></span>
                                </div>

                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                    <span class="info-label">Prix unitaire:</span>
                                    <span class="info-value"><?php echo number_format($besoin['prix_unitaire'], 0, ',', ' '); ?> Ar</span>
                                </div>

                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    <span class="info-label">Montant total:</span>
                                    <span class="info-value font-bold"><?php echo number_format($besoin['quantite'] * $besoin['prix_unitaire'], 0, ',', ' '); ?> Ar</span>
                                </div>

                                <div class="info-row">
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
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                    <span class="info-label">Urgence:</span>
                                    <span class="badge badge-<?php echo $urgenceClass[$besoin['urgence'] ?? 'normale']; ?>">
                                        <?php echo $urgenceLabel[$besoin['urgence'] ?? 'normale']; ?>
                                    </span>
                                </div>

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
                                // Calculer le montant total nécessaire
                                $montantTotal = floatval($besoin['quantite']) * floatval($besoin['prix_unitaire']);
                                
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
                                <a href="<?= $base_url ?>/besoin/<?php echo $besoin['id_besoin']; ?>/delete" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');">
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

    <div class="mt-4">
        <a href="<?= $base_url ?>/villes" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour
        </a>
        <a href="<?= $base_url ?>/ville/<?php echo $ville['id_ville']; ?>/delete" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ville ?');">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            Supprimer cette ville
        </a>
    </div>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>