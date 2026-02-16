<?php
/**
 * Besoins non satisfaits
 */
?>

<div class="container mt-5">
    <div class="row">
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
                                <th>Ville</th>
                                <th>Article</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Montant total</th>
                                <th>Urgence</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($besoins as $besoin): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($besoin['nom_ville']); ?></td>
                                    <td><?php echo htmlspecialchars($besoin['nom_article']); ?></td>
                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($besoin['libelle_type'] ?? ''); ?></span></td>
                                    <td><?php echo number_format($besoin['quantite'], 2, ',', ' '); ?></td>
                                    <td><?php echo number_format($besoin['prix_unitaire'], 0, ',', ' '); ?> Ar</td>
                                    <td><?php echo number_format($besoin['quantite'] * $besoin['prix_unitaire'], 0, ',', ' '); ?> Ar</td>
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
                                        <a href="/besoin/<?php echo $besoin['id_besoin']; ?>" class="btn btn-sm btn-info">Voir</a>
                                        <a href="/besoin/<?php echo $besoin['id_besoin']; ?>/delete" class="btn btn-sm btn-danger">Supprimer</a>
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
