<?php
/**
 * Liste des besoins
 */
?>

<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>📋 Liste des besoins</h1>
            <a href="/besoin/create" class="btn btn-primary mb-3">Ajouter un besoin</a>
            <hr>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        if ($_GET['success'] == 'besoin_ajoute') {
                            echo 'Besoin ajouté avec succès!';
                        } elseif ($_GET['success'] == 'besoin_supprime') {
                            echo 'Besoin supprimé avec succès!';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($besoins)): ?>
                <div class="alert alert-info">
                    Aucun besoin enregistré. <a href="/besoin/create">Créer un besoin</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Ville</th>
                                <th>Articles</th>
                                <th>Types</th>
                                <th>Description</th>
                                <th>Urgence</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($besoins as $besoin): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($besoin['id_besoin']); ?></td>
                                    <td><?php echo htmlspecialchars($besoin['nom_ville']); ?></td>
                                    <td>
                                        <?php 
                                            if (!empty($besoin['articles'])) {
                                                echo htmlspecialchars($besoin['articles']);
                                            } else {
                                                echo '<span class="text-muted">Aucun article</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if (!empty($besoin['types'])) {
                                                $types = explode(', ', $besoin['types']);
                                                foreach (array_unique($types) as $type) {
                                                    echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($type) . '</span>';
                                                }
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if (!empty($besoin['description'])) {
                                                echo htmlspecialchars(substr($besoin['description'], 0, 50));
                                                if (strlen($besoin['description']) > 50) echo '...';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $urgenceClass = [
                                                'normale' => 'success',
                                                'urgente' => 'warning',
                                                'critique' => 'danger'
                                            ];
                                            $urgenceLabel = [
                                                'normale' => 'Normale',
                                                'urgente' => 'Urgente',
                                                'critique' => 'Critique'
                                            ];
                                        ?>
                                        <span class="badge bg-<?php echo $urgenceClass[$besoin['urgence'] ?? 'normale']; ?>">
                                            <?php echo $urgenceLabel[$besoin['urgence'] ?? 'normale']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            $statutClass = [
                                                'en_cours' => 'primary',
                                                'satisfait' => 'success',
                                                'partiel' => 'info'
                                            ];
                                            $statutLabel = [
                                                'en_cours' => 'En cours',
                                                'satisfait' => 'Satisfait',
                                                'partiel' => 'Partiel'
                                            ];
                                        ?>
                                        <span class="badge bg-<?php echo $statutClass[$besoin['statut']]; ?>">
                                            <?php echo $statutLabel[$besoin['statut']]; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($besoin['date_saisie'])); ?></td>
                                    <td>
                                        <a href="/besoin/<?php echo $besoin['id_besoin']; ?>" class="btn btn-sm btn-info" title="Voir">
                                            👁️
                                        </a>
                                        <a href="/besoin/<?php echo $besoin['id_besoin']; ?>/delete" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce besoin ?');" title="Supprimer">
                                            🗑️
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Statistiques -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Total besoins</h5>
                                <p class="card-text display-6"><?php echo count($besoins); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Besoins critiques</h5>
                                <p class="card-text display-6">
                                    <?php 
                                        $critiques = array_filter($besoins, function($b) { 
                                            return $b['urgence'] == 'critique'; 
                                        });
                                        echo count($critiques);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Besoins en cours</h5>
                                <p class="card-text display-6">
                                    <?php 
                                        $enCours = array_filter($besoins, function($b) { 
                                            return $b['statut'] == 'en_cours'; 
                                        });
                                        echo count($enCours);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total besoins</h5>
                                <p class="card-text display-6"><?php echo count($besoins); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
