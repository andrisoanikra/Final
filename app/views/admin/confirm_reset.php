<?php
/**
 * Page de confirmation de réinitialisation
 */
$pageTitle = 'Réinitialiser les données - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        ⚠️ Réinitialisation des données
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            Attention !
                        </h5>
                        <p class="mb-0">Cette action va <strong>supprimer toutes les données actuelles</strong> et les remplacer par les données de départ.</p>
                    </div>

                    <h5 class="mt-4">Ce qui sera supprimé :</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item">
                            <strong>❌ Tous les besoins</strong> créés après l'initialisation
                        </li>
                        <li class="list-group-item">
                            <strong>❌ Tous les dons</strong> reçus
                        </li>
                        <li class="list-group-item">
                            <strong>❌ Toutes les affectations</strong> (dispatch_dons)
                        </li>
                        <li class="list-group-item">
                            <strong>❌ Tous les articles</strong> personnalisés
                        </li>
                    </ul>

                    <h5>Ce qui sera restauré :</h5>
                    <ul class="list-group mb-4">
                        <li class="list-group-item list-group-item-success">
                            <strong>✅ 5 besoins</strong> de test (variés: critique, urgent, normal, satisfait, partiel)
                        </li>
                        <li class="list-group-item list-group-item-success">
                            <strong>✅ 10 dons</strong> de test (articles et argent, statuts variés)
                        </li>
                        <li class="list-group-item list-group-item-success">
                            <strong>✅ 5 affectations</strong> de test
                        </li>
                        <li class="list-group-item list-group-item-success">
                            <strong>✅ 10 articles</strong> standards (riz, tôles, etc.)
                        </li>
                        <li class="list-group-item list-group-item-success">
                            <strong>✅ 5 villes</strong> dans 3 régions
                        </li>
                    </ul>

                    <div class="alert alert-info">
                        <strong>💡 Info :</strong> Cette opération est utile pour remettre l'application dans son état initial après des tests.
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="/tableau-bord" class="btn btn-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Annuler
                        </a>
                        
                        <form method="POST" action="/reset/execute" onsubmit="return confirm('⚠️ ÊTES-VOUS ABSOLUMENT SÛR(E) ?\n\nToutes les données actuelles seront perdues!\n\nCette action est IRRÉVERSIBLE.');">
                            <button type="submit" class="btn btn-danger btn-lg">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="1 4 1 10 7 10"></polyline>
                                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                                </svg>
                                Confirmer la réinitialisation
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">📋 Détails techniques</h5>
                </div>
                <div class="card-body">
                    <p><strong>Fichier exécuté :</strong> <code>app/persistance/reset.sql</code></p>
                    <p><strong>Opérations effectuées :</strong></p>
                    <ol>
                        <li>Désactivation temporaire des contraintes de clés étrangères</li>
                        <li>TRUNCATE de toutes les tables de données</li>
                        <li>Réactivation des contraintes</li>
                        <li>Insertion des données de test</li>
                    </ol>
                    <p class="mb-0"><strong>Durée estimée :</strong> 2-3 secondes</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>
