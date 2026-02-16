<?php
/**
 * Détails d'un besoin
 */
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1><?php echo htmlspecialchars($besoin['nom_article']); ?></h1>
            <p class="text-muted">
                Ville: <strong><?php echo htmlspecialchars($besoin['nom_ville']); ?></strong> | 
                Type: <span class="badge bg-secondary"><?php echo htmlspecialchars($besoin['libelle_type'] ?? ''); ?></span>
            </p>
            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h3>Informations</h3>
                    <div class="card">
                        <div class="card-body">
                            <p>
                                <strong>Quantité:</strong> <?php echo number_format($besoin['quantite'], 2, ',', ' '); ?>
                            </p>
                            <p>
                                <strong>Prix unitaire:</strong> <?php echo number_format($besoin['prix_unitaire'], 0, ',', ' '); ?> Ar
                            </p>
                            <p>
                                <strong>Montant total:</strong> <?php echo number_format($besoin['quantite'] * $besoin['prix_unitaire'], 0, ',', ' '); ?> Ar
                            </p>
                            <?php if (!empty($besoin['description'])): ?>
                                <p>
                                    <strong>Description:</strong><br>
                                    <?php echo htmlspecialchars($besoin['description']); ?>
                                </p>
                            <?php endif; ?>
                            <p>
                                <strong>Urgence:</strong> 
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
                            </p>
                            <p>
                                <strong>Statut:</strong> 
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
                            </p>
                            <p>
                                <strong>Date de saisie:</strong> <?php echo date('d/m/Y H:i', strtotime($besoin['date_saisie'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h3>Actions</h3>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="/besoin/<?php echo $besoin['id_besoin']; ?>/statut" class="mb-3">
                                <div class="form-group">
                                    <label for="statut" class="form-label">Mettre à jour le statut</label>
                                    <select class="form-control" id="statut" name="statut">
                                        <option value="en_cours" <?php echo ($besoin['statut'] == 'en_cours') ? 'selected' : ''; ?>>En cours</option>
                                        <option value="satisfait" <?php echo ($besoin['statut'] == 'satisfait') ? 'selected' : ''; ?>>Satisfait</option>
                                        <option value="partiel" <?php echo ($besoin['statut'] == 'partiel') ? 'selected' : ''; ?>>Partiel</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Mettre à jour</button>
                            </form>

                            <hr>

                            <div class="mb-3">
                                <a href="/besoin/<?php echo $besoin['id_besoin']; ?>/delete" class="btn btn-danger btn-sm btn-block">Supprimer ce besoin</a>
                            </div>

                            <hr>

                            <a href="/villes/<?php echo $besoin['id_ville']; ?>" class="btn btn-secondary btn-sm btn-block">Retour à la ville</a>
                            <a href="/besoins" class="btn btn-secondary btn-sm btn-block mt-2">Tous les besoins</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
