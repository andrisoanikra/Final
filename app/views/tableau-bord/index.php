<?php
/**
 * Tableau de bord - Vue d'ensemble des villes, besoins et dons
 */
?>

<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container-fluid mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>📊 Tableau de Bord</h1>
            <p class="lead">Vue d'ensemble des villes, besoins et dons attribués</p>
            <hr>

            <?php if (empty($villes)): ?>
                <div class="alert alert-info">
                    Aucune ville enregistrée. <a href="/ville/create">Créer une ville</a>
                </div>
            <?php else: ?>
                <!-- Statistiques globales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Villes</h5>
                                <p class="display-4"><?php echo count($villes); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <h5 class="card-title">Besoins en cours</h5>
                                <p class="display-4">
                                    <?php 
                                        $totalBesoinsEnCours = array_sum(array_column($villes, 'nb_besoins_en_cours'));
                                        echo $totalBesoinsEnCours;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h5 class="card-title">Besoins satisfaits</h5>
                                <p class="display-4">
                                    <?php 
                                        $totalBesoinsSatisfaits = array_sum(array_column($villes, 'nb_besoins_satisfaits'));
                                        echo $totalBesoinsSatisfaits;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h5 class="card-title">Dons distribués</h5>
                                <p class="display-4">
                                    <?php 
                                        $totalDons = array_sum(array_column($villes, 'nb_dons_recus'));
                                        echo $totalDons;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des villes avec détails -->
                <?php foreach ($villes as $ville): ?>
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                🏙️ <?php echo htmlspecialchars($ville['nom_ville']); ?>
                                <small class="float-end">
                                    <span class="badge bg-light text-dark">
                                        <?php echo htmlspecialchars($ville['nom_region']); ?>
                                    </span>
                                    <?php if ($ville['population']): ?>
                                        <span class="badge bg-light text-dark ms-2">
                                            👥 <?php echo number_format($ville['population'], 0, ',', ' '); ?>
                                        </span>
                                    <?php endif; ?>
                                </small>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Statistiques de la ville -->
                                <div class="col-md-12 mb-3">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border rounded p-2">
                                                <h6 class="text-muted">Total Besoins</h6>
                                                <h3><?php echo $ville['nb_besoins']; ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-2 bg-warning bg-opacity-10">
                                                <h6 class="text-muted">En cours</h6>
                                                <h3 class="text-warning"><?php echo $ville['nb_besoins_en_cours']; ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-2 bg-success bg-opacity-10">
                                                <h6 class="text-muted">Satisfaits</h6>
                                                <h3 class="text-success"><?php echo $ville['nb_besoins_satisfaits']; ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border rounded p-2 bg-info bg-opacity-10">
                                                <h6 class="text-muted">Dons reçus</h6>
                                                <h3 class="text-info"><?php echo $ville['nb_dons_recus']; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Besoins de la ville -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">📋 Besoins récents</h5>
                                    <?php if (empty($ville['besoins'])): ?>
                                        <p class="text-muted">Aucun besoin enregistré</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Articles</th>
                                                        <th>Montant</th>
                                                        <th>Urgence</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($ville['besoins'] as $besoin): ?>
                                                        <tr>
                                                            <td>
                                                                <small><?php echo htmlspecialchars($besoin['articles'] ?? 'N/A'); ?></small>
                                                            </td>
                                                            <td>
                                                                <small><?php echo number_format($besoin['montant_total'] ?? 0, 0, ',', ' '); ?> Ar</small>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                    $urgenceClass = [
                                                                        'normale' => 'success',
                                                                        'urgente' => 'warning',
                                                                        'critique' => 'danger'
                                                                    ];
                                                                ?>
                                                                <span class="badge bg-<?php echo $urgenceClass[$besoin['urgence']] ?? 'secondary'; ?>">
                                                                    <?php echo ucfirst($besoin['urgence']); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php 
                                                                    $statutClass = [
                                                                        'en_cours' => 'primary',
                                                                        'satisfait' => 'success',
                                                                        'partiel' => 'info'
                                                                    ];
                                                                ?>
                                                                <span class="badge bg-<?php echo $statutClass[$besoin['statut']] ?? 'secondary'; ?>">
                                                                    <?php echo ucfirst(str_replace('_', ' ', $besoin['statut'])); ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if (count($ville['besoins']) >= 5): ?>
                                            <a href="/ville/<?php echo $ville['id_ville']; ?>" class="btn btn-sm btn-outline-primary">
                                                Voir tous les besoins →
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Dons attribués à la ville -->
                                <div class="col-md-6">
                                    <h5 class="mb-3">🎁 Dons attribués</h5>
                                    <?php if (empty($ville['dons_recus'])): ?>
                                        <p class="text-muted">Aucun don attribué</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Article</th>
                                                        <th>Quantité/Montant</th>
                                                        <th>Donateur</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($ville['dons_recus'] as $don): ?>
                                                        <tr>
                                                            <td>
                                                                <small>
                                                                    <?php 
                                                                        if (!empty($don['nom_article'])) {
                                                                            echo htmlspecialchars($don['nom_article']);
                                                                        } else {
                                                                            echo '💰 Argent';
                                                                        }
                                                                    ?>
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <small>
                                                                    <?php 
                                                                        if (($don['montant_affecte'] ?? 0) > 0) {
                                                                            echo number_format($don['montant_affecte'], 0, ',', ' ') . ' Ar';
                                                                        } else {
                                                                            echo number_format($don['quantite_affectee'] ?? 0, 2, ',', ' ');
                                                                        }
                                                                    ?>
                                                                </small>
                                                            </td>
                                                            <td>
                                                                <small><?php echo htmlspecialchars($don['donateur_nom']); ?></small>
                                                            </td>
                                                            <td>
                                                                <small><?php echo date('d/m/Y', strtotime($don['date_dispatch'])); ?></small>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-3">
                                <a href="/ville/<?php echo $ville['id_ville']; ?>" class="btn btn-sm btn-primary">
                                    👁️ Voir détails
                                </a>
                                <a href="/besoin/create?ville_id=<?php echo $ville['id_ville']; ?>" class="btn btn-sm btn-success">
                                    ➕ Ajouter besoin
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
}

.border {
    border-color: #dee2e6 !important;
}
</style>
