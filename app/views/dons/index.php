<?php
/**
 * Liste des dons
 */
$pageTitle = 'Liste des dons - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            Liste des dons
        </h1>
        <a href="/don/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Ajouter un don
        </a>
    </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        if ($_GET['success'] == 'don_ajoute') {
                            echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Don ajouté avec succès !';
                        } elseif ($_GET['success'] == 'don_supprime') {
                            echo 'Don supprimé avec succès !';
                        } elseif ($_GET['success'] == 'don_modifie') {
                            echo 'Don modifié avec succès !';
                        } elseif ($_GET['success'] == 'don_valide') {
                            echo htmlspecialchars($_GET['message'] ?? 'Don validé avec succès !');
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php 
            $nbDisponibles = count(array_filter($dons, function($d) { return $d['statut'] == 'disponible'; }));
            if ($nbDisponibles > 0): ?>
                <div class="alert alert-info" role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="min-width: 20px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div>
                        <strong>Information importante :</strong> Vous avez <strong><?php echo $nbDisponibles; ?> don(s) disponible(s)</strong> en attente de dispatch. 
                        <br>Pour que les besoins affichent leur évolution, vous devez <strong>valider et dispatcher chaque don</strong> en cliquant sur le bouton <strong>"Valider"</strong> correspondant.
                    </div>
                </div>
            <?php endif; ?>
            
            <?php 
                // Compter les dons disponibles
                $donsDisponibles = array_filter($dons, function($d) { 
                    return $d['statut'] == 'disponible'; 
                });
                $nbDisponibles = count($donsDisponibles);
            ?>
            
            <?php if ($nbDisponibles > 0): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 8px;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <strong><?php echo $nbDisponibles; ?></strong> don(s) disponible(s) en attente de dispatch.
                    Pour que les besoins affichent leur évolution, vous devez <strong>valider et dispatcher</strong> chaque don en cliquant sur le bouton "Valider" de chaque don.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($dons)): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    <h3>Aucun don enregistré</h3>
                    <p>Commencez par ajouter des dons pour aider les sinistrés.</p>
                    <a href="/don/create" class="btn btn-primary">Créer un don</a>
                </div>
            <?php else: ?>
                <!-- Statistiques -->
                <div class="stats-grid mb-4">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total dons</div>
                            <div class="stat-value"><?php echo count($dons); ?></div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-success">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="16 12 12 8 8 12"></polyline>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Dons disponibles</div>
                            <div class="stat-value">
                                <?php 
                                    $disponibles = array_filter($dons, function($d) { 
                                        return $d['statut'] == 'disponible'; 
                                    });
                                    echo count($disponibles);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-info">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Montant total en argent</div>
                            <div class="stat-value">
                                <?php 
                                    $totalArgent = array_reduce($dons, function($carry, $d) {
                                        return $carry + ($d['montant_argent'] ?? 0);
                                    }, 0);
                                    echo number_format($totalArgent, 0, ',', ' ') . ' Ar';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des dons en cards -->
                <div class="card-grid">
                    <?php foreach ($dons as $don): ?>
                        <div class="item-card">
                            <div class="card-header-flex">
                                <div class="card-title-with-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    <h3>Don #<?php echo htmlspecialchars($don['id_don']); ?></h3>
                                </div>
                                <?php 
                                    $statutClass = [
                                        'disponible' => 'success',
                                        'affecte' => 'info',
                                        'utilise' => 'secondary'
                                    ];
                                    $statutLabel = [
                                        'disponible' => 'Disponible',
                                        'affecte' => 'Affecté',
                                        'utilise' => 'Utilisé'
                                    ];
                                ?>
                                <span class="badge badge-<?php echo $statutClass[$don['statut']] ?? 'secondary'; ?>">
                                    <?php echo $statutLabel[$don['statut']] ?? 'Inconnu'; ?>
                                </span>
                            </div>

                            <div class="card-body">
                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    <span class="info-label">Type:</span>
                                    <span class="badge badge-secondary">
                                        <?php echo htmlspecialchars($don['libelle_type']); ?>
                                    </span>
                                </div>

                                <?php if ($don['id_article']): ?>
                                    <div class="info-row">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                        </svg>
                                        <span class="info-label">Article:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($don['nom_article']); ?></span>
                                    </div>

                                    <?php if ($don['quantite']): ?>
                                        <div class="info-row">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M9 11l3 3L22 4"></path>
                                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                            </svg>
                                            <span class="info-label">Quantité:</span>
                                            <span class="info-value"><?php echo number_format($don['quantite'], 2, ',', ' '); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="info-row">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                        <span class="info-label">Don en argent</span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($don['montant_argent']): ?>
                                    <div class="info-row">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                        <span class="info-label">Montant:</span>
                                        <span class="info-value font-bold"><?php echo number_format($don['montant_argent'], 0, ',', ' '); ?> Ar</span>
                                    </div>
                                <?php endif; ?>

                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    <span class="info-label">Donateur:</span>
                                    <span class="info-value"><?php echo htmlspecialchars($don['donateur_nom']); ?></span>
                                </div>

                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    <span class="info-label">Contact:</span>
                                    <span class="info-value">
                                        <?php echo $don['donateur_contact'] ? htmlspecialchars($don['donateur_contact']) : '<em>Non renseigné</em>'; ?>
                                    </span>
                                </div>

                                <?php if ($don['description_don']): ?>
                                    <div class="info-row">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="16" y1="13" x2="8" y2="13"></line>
                                            <line x1="16" y1="17" x2="8" y2="17"></line>
                                            <polyline points="10 9 9 9 8 9"></polyline>
                                        </svg>
                                        <span class="info-label">Description:</span>
                                        <span class="info-value"><?php echo htmlspecialchars(substr($don['description_don'], 0, 80)); ?><?php echo strlen($don['description_don']) > 80 ? '...' : ''; ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="info-row">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <span class="info-label">Date:</span>
                                    <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($don['date_don'])); ?></span>
                                </div>
                            </div>

                            <div class="card-actions">
                                <a href="/don/<?php echo $don['id_don']; ?>" class="btn btn-secondary btn-sm">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Voir
                                </a>
                                <?php if ($don['statut'] == 'disponible'): ?>
                                    <a href="/don/<?php echo $don['id_don']; ?>/valider" class="btn btn-success btn-sm" 
                                       onclick="return confirm('Valider ce don et l\'affecter aux besoins ?');">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Valider
                                    </a>
                                <?php endif; ?>
                                <a href="/don/<?php echo $don['id_don']; ?>/delete" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');">
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
                </div>
            <?php endif; ?>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
