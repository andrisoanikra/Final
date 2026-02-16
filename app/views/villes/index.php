<?php
/**
 * Liste des villes
 */
$pageTitle = 'Liste des villes - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Liste des villes
        </h1>
        <a href="/ville/create" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Ajouter une ville
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <?php 
                if ($_GET['success'] == 'ville_ajoutee') {
                    echo 'Ville ajoutée avec succès!';
                } elseif ($_GET['success'] == 'ville_supprimee') {
                    echo 'Ville supprimée avec succès!';
                }
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($villes)): ?>
        <div class="empty-state">
            <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <h3 class="empty-state-title">Aucune ville enregistrée</h3>
            <p class="empty-state-text">Commencez par créer votre première ville</p>
            <a href="/ville/create" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Créer une ville
            </a>
        </div>
    <?php else: ?>
        <?php
        // Séparer les villes en deux catégories : satisfaites et en cours
        $villesSatisfaites = [];
        $villesEnCours = [];
        
        foreach ($villes as $ville) {
            // Récupérer le statut des besoins de chaque ville
            $stmt = Flight::db()->runQuery("
                SELECT COUNT(*) as total, 
                       SUM(CASE WHEN statut = 'satisfait' THEN 1 ELSE 0 END) as satisfaits
                FROM besoins 
                WHERE id_ville = ?
            ", [$ville['id_ville']]);
            $stats = $stmt->fetch();
            
            if ($stats['total'] > 0 && $stats['total'] == $stats['satisfaits']) {
                $villesSatisfaites[] = $ville;
            } else {
                $villesEnCours[] = $ville;
            }
        }
        ?>
        
        <?php if (!empty($villesSatisfaites)): ?>
        <div class="alert alert-success" style="margin-bottom: 2rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="min-width: 24px;">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div>
                <strong>🎉 Félicitations !</strong> 
                <?php if (count($villesSatisfaites) == 1): ?>
                    La ville de <strong><?php echo htmlspecialchars($villesSatisfaites[0]['nom_ville']); ?></strong> a tous ses besoins couverts !
                <?php else: ?>
                    <strong><?php echo count($villesSatisfaites); ?> villes</strong> ont tous leurs besoins couverts :
                    <strong><?php echo implode(', ', array_map(function($v) { return htmlspecialchars($v['nom_ville']); }, $villesSatisfaites)); ?></strong>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="list-view">
            <?php foreach ($villesEnCours as $ville): ?>
                <div class="list-item">
                    <div class="list-item-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="list-item-content">
                        <div class="list-item-title"><?php echo htmlspecialchars($ville['nom_ville']); ?></div>
                        <div class="list-item-subtitle">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                            </svg>
                            <?php echo htmlspecialchars($ville['nom_region'] ?? 'Non définie'); ?>
                        </div>
                    </div>
                    <div class="list-item-actions">
                        <a href="/ville/<?php echo $ville['id_ville']; ?>" class="btn btn-sm btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Voir
                        </a>
                        <a href="/ville/<?php echo $ville['id_ville']; ?>/delete" class="btn btn-sm btn-danger"
                           onclick="return confirm('Voulez-vous vraiment supprimer cette ville ?');">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (!empty($villesSatisfaites)): ?>
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--success);">
                <h3 style="color: var(--success); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Villes avec besoins entièrement couverts
                </h3>
                <?php foreach ($villesSatisfaites as $ville): ?>
                <div class="list-item" style="background-color: #d1fae5; border-left: 4px solid var(--success);">
                    <div class="list-item-icon" style="color: var(--success);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div class="list-item-content">
                        <div class="list-item-title"><?php echo htmlspecialchars($ville['nom_ville']); ?></div>
                        <div class="list-item-subtitle">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                            </svg>
                            <?php echo htmlspecialchars($ville['nom_region'] ?? 'Non définie'); ?>
                            <span style="margin-left: 1rem; color: var(--success); font-weight: 600;">✓ Tous les besoins couverts</span>
                        </div>
                    </div>
                    <div class="list-item-actions">
                        <a href="/ville/<?php echo $ville['id_ville']; ?>" class="btn btn-sm btn-success">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Voir
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
