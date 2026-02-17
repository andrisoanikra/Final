<?php
$pageTitle = 'Simulation et validation des achats - BNGRC';
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
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            Simulation et Validation des Achats
        </h1>
        <div>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#configModal">
                ⚙️ Configurer frais
            </button>
            <a href="<?= $base_url ?>/besoins/critiques-materiels" class="btn btn-secondary">
                Retour aux besoins
            </a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            ✓ <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            ✗ <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= $base_url ?>/achats/simulation" class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Filtrer par ville</label>
                    <select class="form-control form-select" name="ville">
                        <option value="">Toutes les villes</option>
                        <?php foreach ($villes as $ville): ?>
                            <option value="<?php echo $ville['id_ville']; ?>" 
                                    <?php echo ($filtre_ville == $ville['id_ville']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ville['nom_ville']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">🔍 Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($achats)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <h3>Aucun achat enregistré</h3>
            <p>Créez un achat simulé depuis la page des besoins critiques.</p>
            <a href="<?= $base_url ?>/besoins/critiques-materiels" class="btn btn-primary">Voir les besoins critiques</a>
        </div>
    <?php else: ?>
        <?php
        $totalSimules = 0;
        $totalValides = 0;
        $montantSimules = 0;
        $montantValides = 0;
        foreach ($achats as $a) {
            if ($a['statut'] == 'simule') {
                $totalSimules++;
                $montantSimules += $a['montant_total'];
            } else {
                $totalValides++;
                $montantValides += $a['montant_total'];
            }
        }
        ?>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card" style="border-left: 4px solid #ffc107;">
                    <div class="card-body">
                        <h6 style="color: #ffc107;">🕐 Achats simulés (en attente)</h6>
                        <p style="font-size: 24px; font-weight: bold; margin: 0;">
                            <?php echo $totalSimules; ?> achat(s) - 
                            <?php echo number_format($montantSimules, 0, ',', ' '); ?> Ar
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card" style="border-left: 4px solid #28a745;">
                    <div class="card-body">
                        <h6 style="color: #28a745;">✓ Achats validés</h6>
                        <p style="font-size: 24px; font-weight: bold; margin: 0;">
                            <?php echo $totalValides; ?> achat(s) - 
                            <?php echo number_format($montantValides, 0, ',', ' '); ?> Ar
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des achats -->
        <div class="card-grid">
            <?php foreach ($achats as $achat): ?>
                <div class="item-card" style="border-left: 4px solid <?php echo $achat['statut'] == 'simule' ? '#ffc107' : '#28a745'; ?>;">
                    <div class="card-header-flex">
                        <div class="card-title-with-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <h3>Achat #<?php echo $achat['id_achat']; ?></h3>
                        </div>
                        <span class="badge badge-<?php echo $achat['statut'] == 'simule' ? 'warning' : 'success'; ?>">
                            <?php echo $achat['statut'] == 'simule' ? '🕐 Simulé' : '✓ Validé'; ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            </svg>
                            <span class="info-label">Ville:</span>
                            <span class="info-value font-bold"><?php echo htmlspecialchars($achat['nom_ville']); ?></span>
                        </div>

                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                            <span class="info-label">Article:</span>
                            <span class="info-value">
                                <?php 
                                if ($achat['id_article']) {
                                    echo htmlspecialchars($achat['nom_article']) . ' (' . htmlspecialchars($achat['libelle_type']) . ')';
                                } else {
                                    echo '💰 <strong>Conversion automatique d\'argent</strong>';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Quantité:</span>
                            <span class="info-value font-bold">
                                <?php 
                                if ($achat['id_article']) {
                                    echo number_format($achat['quantite'], 2);
                                } else {
                                    echo '<em>N/A (conversion automatique)</em>';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Prix unitaire:</span>
                            <span class="info-value"><?php echo number_format($achat['prix_unitaire'], 0, ',', ' '); ?> Ar</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Montant articles:</span>
                            <span class="info-value font-bold"><?php echo number_format($achat['montant_article'], 0, ',', ' '); ?> Ar</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Frais (<?php echo $achat['frais_pourcentage']; ?>%):</span>
                            <span class="info-value" style="color: #dc3545;"><?php echo number_format($achat['montant_frais'], 0, ',', ' '); ?> Ar</span>
                        </div>

                        <div class="info-row border-top pt-2 mt-2">
                            <span class="info-label"><strong>TOTAL:</strong></span>
                            <span class="info-value font-bold" style="color: #0d6efd; font-size: 18px;">
                                <?php echo number_format($achat['montant_total'], 0, ',', ' '); ?> Ar
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Don source:</span>
                            <span class="info-value">Don #<?php echo $achat['id_don_argent']; ?> - <?php echo htmlspecialchars($achat['donateur_source']); ?></span>
                        </div>

                        <?php if ($achat['statut'] == 'valide'): ?>
                            <div class="info-row">
                                <span class="info-label">Date validation:</span>
                                <span class="info-value">
                                    <?php 
                                    $date = new DateTime($achat['date_validation']);
                                    echo $date->format('d/m/Y à H:i');
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($achat['statut'] == 'simule'): ?>
                        <div class="card-actions">
                            <form method="POST" action="<?= $base_url ?>/achat/valider/<?php echo $achat['id_achat']; ?>" style="display: inline;" 
                                  onsubmit="return confirm('Confirmer la validation de cet achat ? Un don sera créé et dispatché automatiquement.');">
                                <button type="submit" class="btn btn-success btn-sm">
                                    ✓ Valider l'achat
                                </button>
                            </form>
                            <form method="POST" action="<?= $base_url ?>/achat/supprimer/<?php echo $achat['id_achat']; ?>" style="display: inline;"
                                  onsubmit="return confirm('Supprimer cette simulation ?');">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Configuration Frais -->
<div class="modal fade" id="configModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">⚙️ Configuration des frais d'achat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= $base_url ?>/achats/config">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Pourcentage de frais d'achat (%)</label>
                        <input type="number" class="form-control" name="frais_pourcentage" 
                               value="<?php echo $fraisPourcentage; ?>" 
                               min="0" max="100" step="0.1" required>
                        <small class="form-text text-muted">
                            Exemple: 10 pour ajouter 10% de frais sur chaque achat
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
