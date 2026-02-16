<?php
/**
 * Villes avec tous les besoins satisfaits
 */
$pageTitle = 'Villes satisfaites - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            Villes avec besoins satisfaits
        </h1>
        <a href="/besoins" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour aux besoins
        </a>
    </div>

    <?php if (empty($villes)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <h3>Aucune ville n'a encore tous ses besoins couverts</h3>
            <p>Les villes apparaîtront ici une fois que tous leurs besoins seront satisfaits à 100%.</p>
        </div>
    <?php else: ?>
        <div class="alert alert-success mb-4">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 8px;">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <strong><?php echo count($villes); ?></strong> ville(s) avec tous les besoins couverts ! 🎉
        </div>
        
        <div class="card-grid">
            <?php foreach ($villes as $ville): ?>
                <div class="item-card" style="border-left: 4px solid #28a745;">
                    <div class="card-header-flex">
                        <div class="card-title-with-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            </svg>
                            <h3 style="color: #28a745;"><?php echo htmlspecialchars($ville['nom_ville']); ?></h3>
                        </div>
                        <span class="badge badge-success">
                            100% Satisfait
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            <span class="info-label">Nombre de besoins:</span>
                            <span class="info-value font-bold"><?php echo htmlspecialchars($ville['nombre_besoins']); ?></span>
                        </div>

                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <span class="info-label">Montant total des besoins:</span>
                            <span class="info-value font-bold" style="color: #28a745;">
                                <?php echo number_format($ville['montant_total_besoins'], 0, ',', ' '); ?> Ar
                            </span>
                        </div>

                        <div class="info-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            <span class="info-label">Montant reçu:</span>
                            <span class="info-value font-bold" style="color: #28a745;">
                                <?php echo number_format($ville['montant_total_recu'], 0, ',', ' '); ?> Ar
                            </span>
                        </div>

                        <div class="progress-container mt-3">
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar" style="width: 100%; background-color: #28a745;"></div>
                            </div>
                            <span class="progress-text" style="color: #28a745; font-weight: bold;">100%</span>
                        </div>

                        <div class="mt-3" style="padding: 10px; background-color: #d4edda; border-radius: 8px; border-left: 4px solid #28a745;">
                            <p style="margin: 0; color: #155724; font-size: 14px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" style="vertical-align: middle; margin-right: 5px;">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <strong>Félicitations !</strong> Tous les besoins de cette ville sont couverts.
                            </p>
                        </div>
                    </div>

                    <div class="card-actions">
                        <a href="/ville/<?php echo $ville['id_ville']; ?>" class="btn btn-secondary btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            Voir détails
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
