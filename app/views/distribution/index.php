<?php
$pageTitle = 'Distribution Automatique - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
            Distribution Automatique des Dons
        </h1>
        <a href="<?= $base_url ?>/tableau-bord" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['warning']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Explication du système -->
    <div class="card mb-4" style="border-left: 4px solid #28a745;">
        <div class="card-body">
            <h5 style="color: #28a745; margin-bottom: 15px;">
                🎯 Comment fonctionne la distribution automatique ?
            </h5>
            <p class="mb-3">
                Le système distribue intelligemment les dons disponibles en donnant la priorité aux <strong>besoins les plus petits en montant</strong>.
            </p>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>📋 Ordre de priorité :</h6>
                    <ol>
                        <li>Les besoins sont triés du montant le plus petit au plus grand</li>
                        <li>Pour chaque besoin, le système cherche les dons disponibles</li>
                        <li>Les dons sont affectés par ordre chronologique (FIFO)</li>
                        <li>Passage au besoin suivant une fois celui-ci satisfait</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6>✅ Avantages :</h6>
                    <ul>
                        <li>Satisfaction rapide des petits besoins</li>
                        <li>Équité dans la distribution</li>
                        <li>Automatisation complète du processus</li>
                        <li>Traçabilité totale des affectations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques avant distribution -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card" style="border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <h6 style="color: #ffc107;">📊 Besoins en attente</h6>
                    <p style="font-size: 32px; font-weight: bold; margin: 0;" id="nb-besoins">-</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="border-left: 4px solid #17a2b8;">
                <div class="card-body">
                    <h6 style="color: #17a2b8;">💰 Dons disponibles</h6>
                    <p style="font-size: 32px; font-weight: bold; margin: 0;" id="nb-dons">-</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="border-left: 4px solid #28a745;">
                <div class="card-body">
                    <h6 style="color: #28a745;">🎯 Montant distribuable</h6>
                    <p style="font-size: 24px; font-weight: bold; margin: 0;" id="montant-dispo">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bouton de lancement -->
    <div class="card">
        <div class="card-body text-center" style="padding: 40px;">
            <h4 class="mb-4">Lancer la distribution automatique</h4>
            <p class="text-muted mb-4">
                Cette action va distribuer automatiquement les dons disponibles aux besoins en attente,<br>
                en commençant par les plus petits montants.
            </p>
            
            <form method="POST" action="<?= $base_url ?>/distribution/executer" id="form-distribution">
                <button type="submit" class="btn btn-success btn-lg" style="padding: 15px 40px; font-size: 18px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 10px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Lancer la distribution
                </button>
            </form>
            
            <p class="text-muted mt-3" style="font-size: 14px;">
                <em>Note : Cette opération peut prendre quelques secondes selon le nombre de besoins et de dons.</em>
            </p>
        </div>
    </div>

    <!-- Liens rapides -->
    <div class="mt-4">
        <div class="row">
            <div class="col-md-6">
                <a href="<?= $base_url ?>/besoins" class="btn btn-outline-primary btn-block" style="width: 100%; padding: 15px;">
                    📋 Voir tous les besoins
                </a>
            </div>
            <div class="col-md-6">
                <a href="<?= $base_url ?>/dons" class="btn btn-outline-info btn-block" style="width: 100%; padding: 15px;">
                    🎁 Voir tous les dons
                </a>
            </div>
        </div>
    </div>
</div>

<script nonce="<?php echo Flight::get('csp_nonce'); ?>">
const baseUrl = '<?= $base_url ?>';
document.addEventListener('DOMContentLoaded', function() {
    // Charger les statistiques
    fetch(baseUrl + '/api/recapitulatif')
        .then(response => response.json())
        .then(data => {
            document.getElementById('nb-besoins').textContent = data.besoins_en_cours || 0;
            document.getElementById('nb-dons').textContent = data.dons_disponibles || 0;
            document.getElementById('montant-dispo').textContent = 
                (data.montant_dons_disponible || 0).toLocaleString('fr-FR') + ' Ar';
        })
        .catch(error => console.error('Erreur:', error));

    // Confirmation avant lancement
    document.getElementById('form-distribution').addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir lancer la distribution automatique ?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
