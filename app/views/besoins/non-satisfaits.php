<?php
/**
 * Besoins non satisfaits
 */
?>

<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5">
    <div class="⚠️ Besoins non satisfaits</h1>
            <a href="/besoin/create" class="btn btn-primary mb-3">Ajouter un besoin</a
        <div class="col-md-12">
            <h1>Besoins non satisfaits</h1>
            <hr>

            <?php if (empty($besoins)): ?>
                <div class="alert alert-success">
                    Tous les besoins ont été satisfaits! Excellent travail.
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong><?php echo count($besoins); ?></strong> besoin(s) en attente
                </div>
                
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
            <?php endif; ?>
        </div>
    </div>
</div>
