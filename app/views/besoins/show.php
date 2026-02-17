<?php
/**
 * Détails d'un besoin
 */
$pageTitle = 'Détails du besoin - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
            </svg>
            Détails du besoin #<?php echo $besoin['id_besoin']; ?>
        </h1>
        <a href="/besoins" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="item-card">
                <div class="card-header-flex">
                    <h3>Informations du besoin</h3>
                    <div>
                        <?php
                        $badgeClass = match($besoin['urgence'] ?? 'normale') {
                            'critique' => 'badge-danger',
                            'urgente' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars(ucfirst($besoin['urgence'] ?? 'normale')); ?>
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="info-row">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        </svg>
                        <span class="info-label">Ville:</span>
                        <span class="info-value font-bold"><?php echo htmlspecialchars($besoin['nom_ville']); ?></span>
                    </div>

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

                    <!-- Liste des articles du besoin -->
                    <?php if (!empty($besoin['articles'])): ?>
                    <div class="mt-4">
                        <h5 class="mb-3">Articles demandés :</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th>Type</th>
                                        <th class="text-end">Quantité</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Satisfait</th>
                                        <th class="text-end">Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($besoin['articles'] as $article): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($article['nom_article']); ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($article['libelle_type'] ?? 'N/A'); ?></span></td>
                                        <td class="text-end"><?php echo number_format($article['quantite'], 2, ',', ' '); ?></td>
                                        <td class="text-end"><?php echo number_format($article['prix_unitaire'], 0, ',', ' '); ?> Ar</td>
                                        <td class="text-end">
                                            <span class="text-success">
                                                <?php echo number_format($article['quantite_satisfaite'], 2, ',', ' '); ?>
                                            </span>
                                        </td>
                                        <td class="text-end font-bold">
                                            <?php echo number_format($article['quantite'] * $article['prix_unitaire'], 0, ',', ' '); ?> Ar
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="info-row mt-3">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span class="info-label">Montant total besoin:</span>
                        <span class="info-value font-bold"><?php echo number_format($besoin['montant_total'], 0, ',', ' '); ?> Ar</span>
                    </div>

                    <div class="info-row">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span class="info-label">Statut:</span>
                        <?php
                        $badgeClass = match($besoin['statut'] ?? 'en_cours') {
                            'satisfait' => 'badge-success',
                            'partiel' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>">
                            <?php echo htmlspecialchars(ucfirst($besoin['statut'] ?? 'en_cours')); ?>
                        </span>
                    </div>

                    <?php if (!empty($besoin['description_besoin'])): ?>
                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            <span class="info-label">Description:</span>
                            <span class="info-value"><?php echo htmlspecialchars($besoin['description_besoin']); ?></span>
                        </div>
                    <?php endif; ?>

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
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span class="info-label">Date de saisie:</span>
                        <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($besoin['date_saisie'])); ?></span>
                    </div>
                    
                    <!-- Indicateur de progression -->
                    <?php 
                        // Calculer le montant total nécessaire
                        $montantTotal = floatval($besoin['montant_total']);
                        
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
                                Évolution des dons reçus
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
                                <span class="progress-amount-label">Montant reçu:</span>
                                <span class="progress-amount-value"><?php echo number_format($montantRecu, 0, ',', ' '); ?> Ar</span>
                            </div>
                            <div class="progress-amount">
                                <span class="progress-amount-label">Montant besoin:</span>
                                <span class="progress-amount-value"><?php echo number_format($montantTotal, 0, ',', ' '); ?> Ar</span>
                            </div>
                        </div>
                        <?php if ($montantTotal > $montantRecu && $montantRecu > 0): ?>
                            <div class="progress-details" style="margin-top: 0.25rem;">
                                <div class="progress-amount">
                                    <span class="progress-amount-label">Reste à couvrir:</span>
                                    <span class="progress-amount-value" style="color: var(--danger);">
                                        <?php echo number_format($montantTotal - $montantRecu, 0, ',', ' '); ?> Ar
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="item-card">
                <h3>Actions</h3>
                <div class="card-body">
                    <form method="POST" action="/besoin/<?php echo $besoin['id_besoin']; ?>/statut" class="mb-3">
                        <div class="form-group mb-3">
                            <label for="statut" class="form-label">Mettre à jour le statut</label>
                            <select class="form-control" id="statut" name="statut">
                                <option value="en_cours" <?php echo ($besoin['statut'] == 'en_cours') ? 'selected' : ''; ?>>En cours</option>
                                <option value="satisfait" <?php echo ($besoin['statut'] == 'satisfait') ? 'selected' : ''; ?>>Satisfait</option>
                                <option value="partiel" <?php echo ($besoin['statut'] == 'partiel') ? 'selected' : ''; ?>>Partiel</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Mettre à jour
                        </button>
                    </form>

                    <a href="/besoin/<?php echo $besoin['id_besoin']; ?>/delete" class="btn btn-danger w-100" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Supprimer ce besoin
                    </a>

                    <div class="mt-3">
                        <a href="/ville/<?php echo $besoin['id_ville']; ?>" class="btn btn-secondary w-100 mb-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Retour à la ville
                        </a>
                        <a href="/besoins" class="btn btn-secondary w-100">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            </svg>
                            Tous les besoins
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>