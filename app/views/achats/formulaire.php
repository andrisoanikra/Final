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
        <a href="<?= $base_url ?>/besoins/critiques-materiels" class="btn btn-secondary">
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
            <a href="<?= $base_url ?>/don/create" class="btn btn-primary btn-sm mt-2">Ajouter un don</a>
        </div>
    <?php else: ?>
        <!-- Formulaire d'achat automatique -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">💳 Conversion automatique d'un don en argent</h5>
                
                <div class="alert alert-info">
                    <strong>ℹ️ Fonctionnement:</strong> 
                    <ul class="mb-0">
                        <li>Le système utilise <strong>TOUT</strong> le montant disponible du don sélectionné</li>
                        <li>Les frais d'achat de <strong><?php echo $fraisPourcentage; ?>%</strong> sont automatiquement appliqués</li>
                        <li>L'argent converti sera utilisé pour couvrir les besoins critiques</li>
                    </ul>
                </div>

                <form method="POST" action="<?= $base_url ?>/achat/create" id="achat-form">
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
                    </div>

                    <!-- Récapitulatif automatique -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title">📊 Récapitulatif de la conversion</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Montant total du don:</td>
                                    <td class="text-end"><strong><span id="recap-montant-total">0</span> Ar</strong></td>
                                </tr>
                                <tr>
                                    <td>Frais d'achat (<?php echo $fraisPourcentage; ?>%):</td>
                                    <td class="text-end"><strong><span id="recap-frais">0</span> Ar</strong></td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Montant net pour le besoin:</strong></td>
                                    <td class="text-end"><strong style="color: #198754; font-size: 18px;"><span id="recap-net">0</span> Ar</strong></td>
                                </tr>
                            </table>
                            <div class="mt-2 text-muted small">
                                <em>💡 Le montant net sera utilisé pour couvrir le besoin critique</em>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                            ✅ Convertir ce don automatiquement
                        </button>
                        <a href="<?= $base_url ?>/besoins/critiques-materiels" class="btn btn-secondary">Annuler</a>
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
    const btnSubmit = document.getElementById('btn-submit');

    function calculerRecapitulatif() {
        const montantTotal = parseFloat(donSelect.options[donSelect.selectedIndex]?.dataset.montant || 0);

        if (montantTotal > 0) {
            // Montant net = montant_total / (1 + frais%)
            const montantNet = montantTotal / (1 + (fraisPourcentage / 100));
            const montantFrais = montantTotal - montantNet;

            document.getElementById('recap-montant-total').textContent = montantTotal.toLocaleString('fr-FR');
            document.getElementById('recap-frais').textContent = montantFrais.toLocaleString('fr-FR');
            document.getElementById('recap-net').textContent = montantNet.toLocaleString('fr-FR');

            btnSubmit.disabled = false;
        } else {
            document.getElementById('recap-montant-total').textContent = '0';
            document.getElementById('recap-frais').textContent = '0';
            document.getElementById('recap-net').textContent = '0';
            btnSubmit.disabled = true;
        }
    }

    donSelect.addEventListener('change', calculerRecapitulatif);
});
</script>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
