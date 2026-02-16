<?php
$pageTitle = 'Formulaire d\'achat - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            Acheter des articles pour le besoin
        </h1>
        <a href="/besoins/critiques-materiels" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour
        </a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Informations du besoin -->
    <div class="card mb-4" style="border-left: 4px solid #dc3545;">
        <div class="card-body">
            <h5 style="color: #dc3545; margin-bottom: 15px;">
                🚨 Besoin #<?php echo $besoin['id_besoin']; ?> - <?php echo htmlspecialchars($besoin['nom_ville']); ?>
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($besoin['description'] ?? 'N/A'); ?></p>
                    <p><strong>Urgence:</strong> 
                        <span class="badge badge-danger">🔴 Critique</span>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Montant total besoin:</strong> 
                        <span style="color: #dc3545; font-weight: bold; font-size: 18px;">
                            <?php echo number_format($besoin['montant_total'], 0, ',', ' '); ?> Ar
                        </span>
                    </p>
                    <p><strong>Montant reçu:</strong> 
                        <span style="color: #198754; font-weight: bold; font-size: 18px;">
                            <?php echo number_format($besoin['montant_recu'], 0, ',', ' '); ?> Ar
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($donsArgent)): ?>
        <div class="alert alert-warning">
            <strong>⚠️ Aucun don en argent disponible</strong><br>
            Il n'y a actuellement aucun don en argent disponible pour effectuer des achats.
            <a href="/don/create" class="btn btn-primary btn-sm mt-2">Ajouter un don</a>
        </div>
    <?php else: ?>
        <!-- Formulaire d'achat -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">💳 Effectuer un achat</h5>
                
                <div class="alert alert-info">
                    <strong>ℹ️ Information:</strong> Les achats incluent des frais de <strong><?php echo $fraisPourcentage; ?>%</strong>.
                    Par exemple, un achat de 100 000 Ar coûtera <?php echo 100 + $fraisPourcentage; ?> 000 Ar au total.
                </div>

                <form method="POST" action="/achat/create" id="achat-form">
                    <input type="hidden" name="id_besoin" value="<?php echo $besoin['id_besoin']; ?>">

                    <!-- Sélection du don en argent -->
                    <div class="form-group mb-3">
                        <label class="form-label">💰 Don en argent source <span class="text-danger">*</span></label>
                        <select class="form-control form-select" name="id_don_argent" id="don-select" required>
                            <option value="">-- Sélectionner un don --</option>
                            <?php foreach ($donsArgent as $don): ?>
                                <option value="<?php echo $don['id_don']; ?>" 
                                        data-montant="<?php echo $don['montant_disponible']; ?>">
                                    Don #<?php echo $don['id_don']; ?> - 
                                    <?php echo htmlspecialchars($don['donateur_nom']); ?> - 
                                    Disponible: <?php echo number_format($don['montant_disponible'], 0, ',', ' '); ?> Ar
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Montant disponible: <span id="montant-dispo">0</span> Ar</small>
                    </div>

                    <!-- Sélection de l'article -->
                    <div class="form-group mb-3">
                        <label class="form-label">📦 Article à acheter <span class="text-danger">*</span></label>
                        <select class="form-control form-select" name="id_article" id="article-select" required>
                            <option value="">-- Sélectionner un article --</option>
                            <?php foreach ($articles as $article): ?>
                                <option value="<?php echo $article['id_article']; ?>" 
                                        data-prix="<?php echo $article['prix_unitaire']; ?>">
                                    <?php echo htmlspecialchars($article['nom_article']); ?> - 
                                    <?php echo number_format($article['prix_unitaire'], 0, ',', ' '); ?> Ar/unité
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Prix unitaire: <span id="prix-unitaire">0</span> Ar</small>
                    </div>

                    <!-- Quantité -->
                    <div class="form-group mb-3">
                        <label class="form-label">🔢 Quantité <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="quantite" id="quantite-input" 
                               placeholder="Ex: 100" step="0.01" min="0.01" required>
                    </div>

                    <!-- Récapitulatif -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">📊 Récapitulatif de l'achat</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Montant articles:</td>
                                    <td class="text-end"><strong><span id="recap-montant-articles">0</span> Ar</strong></td>
                                </tr>
                                <tr>
                                    <td>Frais d'achat (<?php echo $fraisPourcentage; ?>%):</td>
                                    <td class="text-end"><strong><span id="recap-frais">0</span> Ar</strong></td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>TOTAL à payer:</strong></td>
                                    <td class="text-end"><strong style="color: #dc3545; font-size: 18px;"><span id="recap-total">0</span> Ar</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Message d'avertissement -->
                    <div id="warning-message" class="alert alert-warning" style="display: none;">
                        <strong>⚠️ Attention:</strong> <span id="warning-text"></span>
                    </div>

                    <!-- Boutons -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="btn-submit">
                            🛒 Créer l'achat simulé
                        </button>
                        <a href="/besoins/critiques-materiels" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script nonce="<?php echo Flight::get('csp_nonce'); ?>">
document.addEventListener('DOMContentLoaded', function() {
    const fraisPourcentage = <?php echo $fraisPourcentage; ?>;
    const donSelect = document.getElementById('don-select');
    const articleSelect = document.getElementById('article-select');
    const quantiteInput = document.getElementById('quantite-input');
    const btnSubmit = document.getElementById('btn-submit');

    function calculerRecapitulatif() {
        const prixUnitaire = parseFloat(articleSelect.options[articleSelect.selectedIndex]?.dataset.prix || 0);
        const quantite = parseFloat(quantiteInput.value || 0);
        const montantDispo = parseFloat(donSelect.options[donSelect.selectedIndex]?.dataset.montant || 0);

        const montantArticles = prixUnitaire * quantite;
        const montantFrais = montantArticles * (fraisPourcentage / 100);
        const montantTotal = montantArticles + montantFrais;

        document.getElementById('recap-montant-articles').textContent = montantArticles.toLocaleString('fr-FR');
        document.getElementById('recap-frais').textContent = montantFrais.toLocaleString('fr-FR');
        document.getElementById('recap-total').textContent = montantTotal.toLocaleString('fr-FR');

        // Vérifier si le montant est suffisant
        const warningDiv = document.getElementById('warning-message');
        const warningText = document.getElementById('warning-text');
        
        if (montantTotal > montantDispo && montantDispo > 0) {
            warningText.textContent = `Fonds insuffisants ! Il manque ${(montantTotal - montantDispo).toLocaleString('fr-FR')} Ar.`;
            warningDiv.style.display = 'block';
            btnSubmit.disabled = true;
        } else {
            warningDiv.style.display = 'none';
            btnSubmit.disabled = false;
        }
    }

    donSelect.addEventListener('change', function() {
        const montant = this.options[this.selectedIndex]?.dataset.montant || 0;
        document.getElementById('montant-dispo').textContent = parseFloat(montant).toLocaleString('fr-FR');
        calculerRecapitulatif();
    });

    articleSelect.addEventListener('change', function() {
        const prix = this.options[this.selectedIndex]?.dataset.prix || 0;
        document.getElementById('prix-unitaire').textContent = parseFloat(prix).toLocaleString('fr-FR');
        calculerRecapitulatif();
    });

    quantiteInput.addEventListener('input', calculerRecapitulatif);
});
</script>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
