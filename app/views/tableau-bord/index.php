<?php
/**
 * Tableau de bord - Vue d'ensemble des villes, besoins et dons
 */
$pageTitle = 'Tableau de Bord - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="9" y1="3" x2="9" y2="21"></line>
            </svg>
            Tableau de Bord
        </h1>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                if ($_GET['success'] == 'reset') {
                    echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '✅ Réinitialisation effectuée avec succès!';
                } else {
                    echo htmlspecialchars($_GET['message'] ?? 'Opération réussie!');
                }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['warning']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ❌ <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

            <?php if (empty($villes)): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <h3>Aucune ville enregistrée</h3>
                    <p>Commencez par ajouter des villes pour suivre les besoins et les dons.</p>
                    <a href="<?= $base_url ?>/ville/create" class="btn btn-primary">Créer une ville</a>
                </div>
            <?php else: ?>
                <!-- Statistiques globales -->
                <div class="stats-grid">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Villes</div>
                            <div class="stat-value"><?php echo count($villes); ?></div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-warning">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Besoins en cours</div>
                            <div class="stat-value">
                                <?php 
                                    $totalBesoinsEnCours = array_sum(array_column($villes, 'nb_besoins_en_cours'));
                                    echo $totalBesoinsEnCours;
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-success">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Besoins satisfaits</div>
                            <div class="stat-value">
                                <?php 
                                    $totalBesoinsSatisfaits = array_sum(array_column($villes, 'nb_besoins_satisfaits'));
                                    echo $totalBesoinsSatisfaits;
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-info">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Dons distribués</div>
                            <div class="stat-value">
                                <?php 
                                    $totalDons = array_sum(array_column($villes, 'nb_dons_recus'));
                                    echo $totalDons;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des villes avec détails -->
                <div class="card-grid">
                    <?php foreach ($villes as $ville): ?>
                        <div class="item-card ville-detail-card">
                            <div class="card-header-custom">
                                <div class="ville-title">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    </svg>
                                    <h3><?php echo htmlspecialchars($ville['nom_ville']); ?></h3>
                                </div>
                                <div class="ville-badges">
                                    <?php if ($ville['nom_region']): ?>
                                        <span class="badge badge-secondary">
                                            <?php echo htmlspecialchars($ville['nom_region']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($ville['population']): ?>
                                        <span class="badge badge-info">
                                            <?php echo number_format($ville['population'], 0, ',', ' '); ?> hab.
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Statistiques de la ville -->
                            <div class="mini-stats-grid">
                                <div class="mini-stat">
                                    <div class="mini-stat-label">Total Besoins</div>
                                    <div class="mini-stat-value"><?php echo $ville['nb_besoins']; ?></div>
                                </div>
                                <div class="mini-stat mini-stat-warning">
                                    <div class="mini-stat-label">En cours</div>
                                    <div class="mini-stat-value"><?php echo $ville['nb_besoins_en_cours']; ?></div>
                                </div>
                                <div class="mini-stat mini-stat-success">
                                    <div class="mini-stat-label">Satisfaits</div>
                                    <div class="mini-stat-value"><?php echo $ville['nb_besoins_satisfaits']; ?></div>
                                </div>
                                <div class="mini-stat mini-stat-info">
                                    <div class="mini-stat-label">Dons reçus</div>
                                    <div class="mini-stat-value"><?php echo $ville['nb_dons_recus']; ?></div>
                                </div>
                            </div>

                            <!-- Besoins et Dons -->
                            <div class="ville-details-section">
                                <div class="detail-column">
                                    <h4>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        Besoins récents
                                    </h4>
                                    <?php if (empty($ville['besoins'])): ?>
                                        <p class="text-muted">Aucun besoin enregistré</p>
                                    <?php else: ?>
                                        <div class="besoin-list">
                                            <?php foreach (array_slice($ville['besoins'], 0, 3) as $besoin): ?>
                                                <div class="besoin-item">
                                                    <div class="besoin-info">
                                                        <div class="besoin-articles"><?php echo htmlspecialchars(substr($besoin['articles'] ?? 'N/A', 0, 30)); ?><?php echo strlen($besoin['articles'] ?? '') > 30 ? '...' : ''; ?></div>
                                                        <div class="besoin-montant"><?php echo number_format($besoin['montant_total'] ?? 0, 0, ',', ' '); ?> Ar</div>
                                                    </div>
                                                    <div class="besoin-badges">
                                                        <?php 
                                                            $urgenceClass = [
                                                                'normale' => 'success',
                                                                'urgente' => 'warning',
                                                                'critique' => 'danger'
                                                            ];
                                                        ?>
                                                        <span class="badge badge-<?php echo $urgenceClass[$besoin['urgence']] ?? 'secondary'; ?>">
                                                            <?php echo ucfirst($besoin['urgence']); ?>
                                                        </span>
                                                        <?php 
                                                            $statutClass = [
                                                                'en_cours' => 'primary',
                                                                'satisfait' => 'success',
                                                                'partiel' => 'info'
                                                            ];
                                                        ?>
                                                        <span class="badge badge-<?php echo $statutClass[$besoin['statut']] ?? 'secondary'; ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $besoin['statut'])); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php if (count($ville['besoins']) > 3): ?>
                                            <a href="<?= $base_url ?>/ville/<?php echo $ville['id_ville']; ?>" class="btn-link">
                                                Voir tous les besoins →
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <div class="detail-column">
                                    <h4>
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                        Dons attribués
                                    </h4>
                                    <?php if (empty($ville['dons_recus'])): ?>
                                        <p class="text-muted">Aucun don attribué</p>
                                    <?php else: ?>
                                        <div class="don-list">
                                            <?php foreach (array_slice($ville['dons_recus'], 0, 3) as $don): ?>
                                                <div class="don-item">
                                                    <div class="don-info">
                                                        <div class="don-article">
                                                            <?php 
                                                                if (!empty($don['nom_article'])) {
                                                                    echo htmlspecialchars($don['nom_article']);
                                                                } else {
                                                                    echo '💰 Argent';
                                                                }
                                                            ?>
                                                        </div>
                                                        <div class="don-quantite">
                                                            <?php 
                                                                if (($don['montant_affecte'] ?? 0) > 0) {
                                                                    echo number_format($don['montant_affecte'], 0, ',', ' ') . ' Ar';
                                                                } else {
                                                                    echo number_format($don['quantite_affectee'] ?? 0, 2, ',', ' ');
                                                                }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="don-donateur"><?php echo htmlspecialchars(substr($don['donateur_nom'], 0, 20)); ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card-actions">
                                <a href="<?= $base_url ?>/ville/<?php echo $ville['id_ville']; ?>" class="btn btn-secondary btn-sm">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Voir détails
                                </a>
                                <a href="<?= $base_url ?>/besoin/create?ville_id=<?php echo $ville['id_ville']; ?>" class="btn btn-success btn-sm">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Ajouter besoin
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                </div>
            <?php endif; ?>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>