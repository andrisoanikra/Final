<?php
/**
 * Détails d'un don
 */
$pageTitle = 'Détails du don - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            Détails du don
        </h1>
        <div>
            <a href="<?= $base_url ?>/dons" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Retour
            </a>
            <?php if ($don['statut'] == 'disponible'): ?>
                <a href="<?= $base_url ?>/don/<?php echo $don['id_don']; ?>/valider" class="btn btn-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Valider et dispatcher
                </a>
            <?php endif; ?>
            <a href="<?= $base_url ?>/don/<?php echo $don['id_don']; ?>/delete" class="btn btn-danger" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Supprimer
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations du donateur -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Informations du donateur
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <strong>Nom :</strong>
                        <span><?php echo htmlspecialchars($don['donateur_nom']); ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Contact :</strong>
                        <span><?php echo htmlspecialchars($don['donateur_contact']); ?></span>
                    </div>
                    <div class="info-row">
                        <strong>Date du don :</strong>
                        <span><?php echo date('d/m/Y H:i', strtotime($don['date_don'])); ?></span>
                    </div>
                    <?php if (!empty($don['description'])): ?>
                        <div class="info-row">
                            <strong>Description :</strong>
                            <span><?php echo nl2br(htmlspecialchars($don['description'])); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Détails du don -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        Détails du don
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <strong>Type :</strong>
                        <span>
                            <?php if ($don['nom_type_don'] == 'argent'): ?>
                                <span class="badge bg-success">💰 <?php echo htmlspecialchars($don['nom_type_don']); ?></span>
                            <?php elseif ($don['nom_type_don'] == 'nature'): ?>
                                <span class="badge bg-info">🥫 <?php echo htmlspecialchars($don['nom_type_don']); ?></span>
                            <?php else: ?>
                                <span class="badge bg-warning">📦 <?php echo htmlspecialchars($don['nom_type_don']); ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($don['nom_article'])): ?>
                        <div class="info-row">
                            <strong>Article :</strong>
                            <span><?php echo htmlspecialchars($don['nom_article']); ?></span>
                        </div>
                        <div class="info-row">
                            <strong>Quantité :</strong>
                            <span><?php echo number_format($don['quantite'], 0, ',', ' '); ?> <?php echo htmlspecialchars($don['unite'] ?? ''); ?></span>
                        </div>
                    <?php elseif (!empty($don['montant_argent'])): ?>
                        <div class="info-row">
                            <strong>Montant :</strong>
                            <span class="text-success fw-bold"><?php echo number_format($don['montant_argent'], 0, ',', ' '); ?> Ar</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-row">
                        <strong>Statut :</strong>
                        <span>
                            <?php if ($don['statut'] == 'disponible'): ?>
                                <span class="badge bg-warning">⏳ Disponible</span>
                            <?php else: ?>
                                <span class="badge bg-success">✅ Dispatché</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($don['statut'] == 'disponible'): ?>
        <div class="alert alert-info">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="min-width: 20px;">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <div>
                <strong>Don en attente de validation</strong><br>
                Ce don n'a pas encore été dispatché. Cliquez sur "Valider et dispatcher" pour l'affecter aux besoins.
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row strong {
    color: #555;
    min-width: 140px;
}

.info-row span {
    text-align: right;
    flex: 1;
}

.card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
}

.card-title {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-body {
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.page-header > div {
    display: flex;
    gap: 10px;
}

.alert {
    display: flex;
    align-items: start;
    gap: 12px;
    padding: 15px;
    border-radius: 8px;
    margin-top: 20px;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}
</style>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
