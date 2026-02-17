<?php
/**
 * Besoins critiques - Matériels et Nature uniquement
 */
$pageTitle = 'Besoins critiques (Matériel/Nature) - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            Besoins critiques - Matériel & Nature
        </h1>
        <a href="/besoins" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour aux besoins
        </a>
    </div>

    <?php if (empty($besoins)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <h3>Aucun besoin critique matériel</h3>
            <p>Excellent ! Aucun besoin critique de type matériel ou nature n'est en attente.</p>
        </div>
    <?php else: ?>
        <div class="alert alert-danger mb-4" style="border-left: 4px solid #dc3545;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 8px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <strong><?php echo count($besoins); ?></strong> besoin(s) critique(s) de type matériel/nature nécessitant une attention urgente
        </div>
        
        <div class="card-grid">
            <?php foreach ($besoins as $besoin): ?>
                <div class="item-card" style="border-left: 4px solid #dc3545;">
                    <div class="card-header-flex">
                        <div class="card-title-with-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            <h3 style="color: #dc3545;">Besoin #<?php echo htmlspecialchars($besoin['id_besoin']); ?></h3>
                        </div>
                        <span class="badge badge-danger">
                            🚨 CRITIQUE
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
                                <span class="info-value">
                                    <?php 
                                    $types = explode(', ', $besoin['types']);
                                    foreach ($types as $index => $type) {
                                        $color = ($type === 'Matériel') ? '#0d6efd' : '#198754';
                                        echo '<span style="display: inline-block; padding: 2px 8px; background: ' . $color . '22; color: ' . $color . '; border-radius: 4px; font-size: 12px; margin-right: 5px;">' . htmlspecialchars($type) . '</span>';
                                    }
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($besoin['description'])): ?>
                            <div class="info-row">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <span class="info-label">Description:</span>
                                <span class="info-value"><?php echo htmlspecialchars($besoin['description']); ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <span class="info-label">Montant total:</span>
                            <span class="info-value font-bold">
                                <?php echo number_format($besoin['montant_total'], 0, ',', ' '); ?> Ar
                            </span>
                        </div>

                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                            </svg>
                            <span class="info-label">Reçu:</span>
                            <span class="info-value font-bold" style="color: <?php echo $besoin['montant_recu'] > 0 ? '#198754' : '#6c757d'; ?>">
                                <?php echo number_format($besoin['montant_recu'], 0, ',', ' '); ?> Ar
                            </span>
                        </div>

                        <?php if (!empty($besoin['date_saisie'])): ?>
                            <div class="info-row">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <span class="info-label">Date de saisie:</span>
                                <span class="info-value">
                                    <?php 
                                    $date = new DateTime($besoin['date_saisie']);
                                    echo $date->format('d/m/Y à H:i');
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <!-- Barre de progression -->
                        <?php 
                        $pourcentage = 0;
                        if ($besoin['montant_total'] > 0) {
                            $pourcentage = min(100, ($besoin['montant_recu'] / $besoin['montant_total']) * 100);
                        }
                        $couleur_barre = '#dc3545'; // Rouge pour critique
                        if ($pourcentage >= 75) {
                            $couleur_barre = '#ffc107'; // Jaune pour presque satisfait
                        } elseif ($pourcentage >= 50) {
                            $couleur_barre = '#fd7e14'; // Orange
                        }
                        ?>
                        <div class="progress-container">
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar" style="width: <?php echo $pourcentage; ?>%; background-color: <?php echo $couleur_barre; ?>;"></div>
                            </div>
                            <span class="progress-text" style="color: <?php echo $couleur_barre; ?>; font-weight: bold;">
                                <?php echo number_format($pourcentage, 1); ?>%
                            </span>
                        </div>

                        <!-- Alerte urgence -->
                        <div class="mt-3" style="padding: 10px; background-color: #f8d7da; border-radius: 8px; border-left: 4px solid #dc3545;">
                            <p style="margin: 0; color: #721c24; font-size: 14px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" style="vertical-align: middle; margin-right: 5px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                                <strong>URGENT :</strong> Ce besoin nécessite une attention immédiate. 
                                <?php if ($pourcentage < 25): ?>
                                    Moins de 25% du besoin est couvert.
                                <?php elseif ($pourcentage < 50): ?>
                                    Moins de 50% du besoin est couvert.
                                <?php elseif ($pourcentage < 75): ?>
                                    Moins de 75% du besoin est couvert.
                                <?php else: ?>
                                    Presque satisfait, encore <?php echo number_format(100 - $pourcentage, 1); ?>% requis.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="card-actions">
                        <a href="/besoin/<?php echo $besoin['id_besoin']; ?>" class="btn btn-secondary btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Voir détails
                        </a>
                        <a href="/achat/formulaire/<?php echo $besoin['id_besoin']; ?>" class="btn btn-success btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            💳 Acheter
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Statistiques globales -->
        <div class="mt-4 p-4" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%); border-radius: 12px; border: 1px solid #ffcccb;">
            <h4 style="color: #dc3545; margin-bottom: 15px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" style="vertical-align: middle; margin-right: 8px;">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                Récapitulatif des besoins critiques
            </h4>
            <?php 
            $total_montant = 0;
            $total_recu = 0;
            foreach ($besoins as $b) {
                $total_montant += $b['montant_total'];
                $total_recu += $b['montant_recu'];
            }
            $pourcentage_global = $total_montant > 0 ? ($total_recu / $total_montant) * 100 : 0;
            ?>
            <div class="row">
                <div class="col-md-4">
                    <p style="margin: 5px 0; color: #666;">
                        <strong>Total nécessaire:</strong> 
                        <span style="color: #dc3545; font-weight: bold; font-size: 18px;">
                            <?php echo number_format($total_montant, 0, ',', ' '); ?> Ar
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p style="margin: 5px 0; color: #666;">
                        <strong>Total reçu:</strong> 
                        <span style="color: #198754; font-weight: bold; font-size: 18px;">
                            <?php echo number_format($total_recu, 0, ',', ' '); ?> Ar
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p style="margin: 5px 0; color: #666;">
                        <strong>Manque:</strong> 
                        <span style="color: #dc3545; font-weight: bold; font-size: 18px;">
                            <?php echo number_format($total_montant - $total_recu, 0, ',', ' '); ?> Ar
                        </span>
                    </p>
                </div>
            </div>
            <div class="progress-container mt-3">
                <div class="progress-bar-wrapper">
                    <div class="progress-bar" style="width: <?php echo $pourcentage_global; ?>%; background-color: #dc3545;"></div>
                </div>
                <span class="progress-text" style="color: #dc3545; font-weight: bold;">
                    <?php echo number_format($pourcentage_global, 1); ?>% satisfait
                </span>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../assets/inc/footer.php'; ?>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
