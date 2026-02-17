<?php
/**
 * Page de récapitulation des besoins avec actualisation Ajax
 */
$pageTitle = 'Récapitulation des Besoins - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 17H7A5 5 0 0 1 7 7h2"></path>
                <path d="M15 7h2a5 5 0 1 1 0 10h-2"></path>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Récapitulation des Besoins
        </h1>
        <div>
            <button id="btnActualiser" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Actualiser
            </button>
            <a href="<?= $base_url ?>/tableau-bord" class="btn btn-secondary">
                Retour au tableau de bord
            </a>
        </div>
    </div>

    <!-- Message de dernière actualisation -->
    <div class="alert alert-info">
        <strong>Dernière actualisation :</strong> <span id="dateActualisation">Chargement...</span>
    </div>

    <!-- Indicateur de chargement -->
    <div id="loadingIndicator" class="text-center my-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
        <p class="mt-2">Actualisation en cours...</p>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card-primary" style="border-left: 4px solid #007bff;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3" style="background: rgba(0,123,255,0.1); padding: 15px; border-radius: 10px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#007bff" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Besoins Totaux</h6>
                            <h2 class="mb-0" id="montantTotal">0 Ar</h2>
                            <small class="text-muted" id="nbBesoinsTotal">0 besoins</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card-success" style="border-left: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3" style="background: rgba(40,167,69,0.1); padding: 15px; border-radius: 10px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Besoins Satisfaits</h6>
                            <h2 class="mb-0 text-success" id="montantSatisfait">0 Ar</h2>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted" id="nbBesoinsSatisfaits">0 besoins</small>
                                <span class="badge bg-success" id="pourcentageSatisfait">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card-danger" style="border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon me-3" style="background: rgba(220,53,69,0.1); padding: 15px; border-radius: 10px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Besoins Restants</h6>
                            <h2 class="mb-0 text-danger" id="montantRestant">0 Ar</h2>
                            <small class="text-muted" id="nbBesoinsEnCours">0 en cours</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de progression -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Progression de satisfaction des besoins</h5>
            <div class="progress" style="height: 30px;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                     role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    <span id="progressText">0%</span>
                </div>
            </div>
            <div class="mt-2 text-center">
                <small class="text-muted">
                    <span id="montantSatisfaitProgress">0 Ar</span> satisfait sur 
                    <span id="montantTotalProgress">0 Ar</span>
                </small>
            </div>
        </div>
    </div>

    <!-- Détails par statut -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        Répartition des besoins
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-primary me-2">●</span>
                                Total des besoins
                            </span>
                            <strong id="nbBesoinsTotal2">0</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-success me-2">●</span>
                                Besoins satisfaits
                            </span>
                            <strong id="nbBesoinsSatisfaits2" class="text-success">0</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-info me-2">●</span>
                                Besoins partiels
                            </span>
                            <strong id="nbBesoinsPartiels" class="text-info">0</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-warning me-2">●</span>
                                Besoins en cours
                            </span>
                            <strong id="nbBesoinsEnCours2" class="text-warning">0</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        Statistiques des dons
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-primary me-2">●</span>
                                Total des dons
                            </span>
                            <strong id="nbDonsTotal">0</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-success me-2">●</span>
                                Dons disponibles
                            </span>
                            <strong id="nbDonsDisponibles" class="text-success">0</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-info me-2">●</span>
                                Dons dispatchés
                            </span>
                            <strong id="nbDonsDispatches" class="text-info">0</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge bg-warning me-2">💰</span>
                                Argent disponible
                            </span>
                            <strong id="montantArgentDisponible" class="text-warning">0 Ar</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Montants partiels -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M8 12h8"></path>
                </svg>
                Besoins partiellement satisfaits
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-2">Montant des besoins restants pour les besoins partiellement satisfaits :</p>
            <h3 class="text-info mb-0" id="montantPartiel">0 Ar</h3>
        </div>
    </div>
</div>

<script nonce="<?php echo Flight::get('csp_nonce'); ?>">
const baseUrl = '<?= $base_url ?>';

/**
 * Fonction pour actualiser les données via Ajax
 */
function actualiserRecapitulatif() {
    const btnActualiser = document.getElementById('btnActualiser');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    // Désactiver le bouton et afficher le loader
    btnActualiser.disabled = true;
    btnActualiser.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualisation...';
    loadingIndicator.style.display = 'block';
    
    // Appel Ajax
    fetch(baseUrl + '/api/recapitulatif')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            // Mise à jour des montants principaux
            document.getElementById('montantTotal').textContent = formatMontant(data.montant_total);
            document.getElementById('montantSatisfait').textContent = formatMontant(data.montant_satisfait);
            document.getElementById('montantRestant').textContent = formatMontant(data.montant_restant);
            document.getElementById('montantPartiel').textContent = formatMontant(data.montant_partiel);
            
            // Mise à jour des nombres
            document.getElementById('nbBesoinsTotal').textContent = data.nb_besoins_total + ' besoin(s)';
            document.getElementById('nbBesoinsSatisfaits').textContent = data.nb_besoins_satisfaits + ' besoin(s)';
            document.getElementById('nbBesoinsEnCours').textContent = data.nb_besoins_en_cours + ' en cours';
            
            // Mise à jour de la progression
            const pourcentage = data.pourcentage_satisfait;
            document.getElementById('pourcentageSatisfait').textContent = pourcentage + '%';
            document.getElementById('progressBar').style.width = pourcentage + '%';
            document.getElementById('progressBar').setAttribute('aria-valuenow', pourcentage);
            document.getElementById('progressText').textContent = pourcentage + '%';
            document.getElementById('montantSatisfaitProgress').textContent = formatMontant(data.montant_satisfait);
            document.getElementById('montantTotalProgress').textContent = formatMontant(data.montant_total);
            
            // Mise à jour des détails
            document.getElementById('nbBesoinsTotal2').textContent = data.nb_besoins_total;
            document.getElementById('nbBesoinsSatisfaits2').textContent = data.nb_besoins_satisfaits;
            document.getElementById('nbBesoinsPartiels').textContent = data.nb_besoins_partiels;
            document.getElementById('nbBesoinsEnCours2').textContent = data.nb_besoins_en_cours;
            
            // Mise à jour des statistiques des dons
            document.getElementById('nbDonsTotal').textContent = data.nb_dons_total;
            document.getElementById('nbDonsDisponibles').textContent = data.nb_dons_disponibles;
            document.getElementById('nbDonsDispatches').textContent = data.nb_dons_dispatches;
            document.getElementById('montantArgentDisponible').textContent = formatMontant(data.montant_argent_disponible);
            
            // Mise à jour de la date
            document.getElementById('dateActualisation').textContent = data.date_actualisation;
            
            // Animation de succès
            showSuccessMessage('Données actualisées avec succès !');
        })
        .catch(error => {
            console.error('Erreur:', error);
            showErrorMessage('Erreur lors de l\'actualisation des données');
        })
        .finally(() => {
            // Réactiver le bouton et masquer le loader
            btnActualiser.disabled = false;
            btnActualiser.innerHTML = `
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Actualiser
            `;
            loadingIndicator.style.display = 'none';
        });
}

/**
 * Formate un montant en Ariary
 */
function formatMontant(montant) {
    return new Intl.NumberFormat('fr-FR').format(montant) + ' Ar';
}

/**
 * Affiche un message de succès
 */
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ✓ ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

/**
 * Affiche un message d'erreur
 */
function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ✗ ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Événement sur le bouton actualiser
document.getElementById('btnActualiser').addEventListener('click', actualiserRecapitulatif);

// Actualisation automatique au chargement de la page
document.addEventListener('DOMContentLoaded', actualiserRecapitulatif);

// Actualisation automatique toutes les 30 secondes (optionnel)
// setInterval(actualiserRecapitulatif, 30000);
</script>

<style>
.stat-card-primary, .stat-card-success, .stat-card-danger {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card-primary:hover, .stat-card-success:hover, .stat-card-danger:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    font-size: 16px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: width 0.6s ease;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
