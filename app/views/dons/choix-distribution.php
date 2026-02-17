<?php
/**
 * Choix de la méthode de distribution pour un don
 */
$pageTitle = 'Choisir la méthode de distribution - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header mb-4">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            Choisir la méthode de distribution
        </h1>
        <a href="/dons" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Retour
        </a>
    </div>

    <!-- Informations sur le don -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                Don à distribuer
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?php if ($don['id_article']): ?>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($don['nom_article']); ?></p>
                        <p><strong>Quantité:</strong> <?php echo number_format($don['quantite'], 2, ',', ' '); ?></p>
                    <?php else: ?>
                        <p><strong>Type:</strong> Don en argent</p>
                        <p><strong>Montant:</strong> <?php echo number_format($don['montant_argent'], 0, ',', ' '); ?> Ar</p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Donateur:</strong> <?php echo htmlspecialchars($don['donateur_nom']); ?></p>
                    <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($don['date_don'])); ?></p>
                    <?php if ($don['description_don']): ?>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($don['description_don']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Choix de méthode -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h4 class="card-title mb-3">Distribution Dispatcher</h4>
                    <p class="text-muted mb-4">
                        Distribue le don selon l'ordre de priorité des besoins (urgence et date de création).
                    </p>
                    
                    <div class="alert alert-info text-start mb-4">
                        <strong>Fonctionnement:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Priorise les besoins urgents</li>
                            <li>Ensuite par ordre chronologique</li>
                            <li>Distribution équitable</li>
                        </ul>
                    </div>

                    <form method="POST" action="/don/<?php echo $don['id_don']; ?>/valider/dispatcher">
                        <button type="submit" class="btn btn-primary btn-lg w-100" 
                                onclick="return confirm('Confirmer la distribution par la méthode Dispatcher ?')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Utiliser cette méthode
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <h4 class="card-title mb-3">Plus petit montant d'abord</h4>
                    <p class="text-muted mb-4">
                        Priorise les besoins ayant le montant total le plus petit pour les satisfaire rapidement.
                    </p>
                    
                    <div class="alert alert-success text-start mb-4">
                        <strong>Fonctionnement:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Trie les besoins par montant croissant</li>
                            <li>Satisfait d'abord les petits besoins</li>
                            <li>Maximise le nombre de besoins satisfaits</li>
                        </ul>
                    </div>

                    <form method="POST" action="/don/<?php echo $don['id_don']; ?>/valider/plus-petit">
                        <button type="submit" class="btn btn-success btn-lg w-100"
                                onclick="return confirm('Confirmer la distribution par plus petit montant ?')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Utiliser cette méthode
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            <circle cx="18" cy="6" r="3" fill="#f59e0b"></circle>
                            <circle cx="6" cy="18" r="3" fill="#f59e0b"></circle>
                        </svg>
                    </div>
                    <h4 class="card-title mb-3">Distribution Proportionnelle</h4>
                    <p class="text-muted mb-4">
                        Distribue au prorata de la demande de chaque besoin avec méthode du reste le plus grand.
                    </p>
                    
                    <div class="alert alert-warning text-start mb-4">
                        <strong>Fonctionnement:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Calcul proportionnel : demande × don/total</li>
                            <li>Arrondi inférieur pour chaque besoin</li>
                            <li>Reste distribué aux plus grandes décimales</li>
                        </ul>
                    </div>

                    <form method="POST" action="/don/<?php echo $don['id_don']; ?>/valider/proportionnel">
                        <button type="submit" class="btn btn-warning btn-lg w-100"
                                onclick="return confirm('Confirmer la distribution proportionnelle ?')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Utiliser cette méthode
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    border-color: #e5e7eb;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
    font-size: 1.75rem;
    font-weight: 600;
    color: #1e293b;
}
</style>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
