<?php
/**
 * Liste des dons
 */
?>

<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>🎁 Liste des dons</h1>
            <a href="/don/create" class="btn btn-primary mb-3">Ajouter un don</a>
            <hr>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        if ($_GET['success'] == 'don_ajoute') {
                            echo 'Don ajouté avec succès !';
                        } elseif ($_GET['success'] == 'don_supprime') {
                            echo 'Don supprimé avec succès !';
                        } elseif ($_GET['success'] == 'don_modifie') {
                            echo 'Don modifié avec succès !';
                        } elseif ($_GET['success'] == 'don_valide') {
                            echo htmlspecialchars($_GET['message'] ?? 'Don validé avec succès !');
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($dons)): ?>
                <div class="alert alert-info">
                    Aucun don enregistré. <a href="/don/create">Créer un don</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Type de don</th>
                                <th>Article</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Donateur</th>
                                <th>Contact</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dons as $don): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($don['id_don']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($don['libelle_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                            if ($don['id_article']) {
                                                echo htmlspecialchars($don['nom_article']);
                                            } else {
                                                echo '<span class="text-muted">Don en argent</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if ($don['quantite']) {
                                                echo number_format($don['quantite'], 2, ',', ' ');
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if ($don['montant_argent']) {
                                                echo number_format($don['montant_argent'], 0, ',', ' ') . ' Ar';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($don['donateur_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($don['donateur_contact']); ?></td>
                                    <td>
                                        <?php 
                                            if ($don['description_don']) {
                                                echo htmlspecialchars(substr($don['description_don'], 0, 50));
                                                if (strlen($don['description_don']) > 50) echo '...';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($don['date_don'])); ?></td>
                                    <td>
                                        <?php 
                                            $statutClass = [
                                                'disponible' => 'success',
                                                'affecte' => 'info',
                                                'utilise' => 'secondary'
                                            ];
                                            $statutLabel = [
                                                'disponible' => 'Disponible',
                                                'affecte' => 'Affecté',
                                                'utilise' => 'Utilisé'
                                            ];
                                        ?>
                                        <span class="badge bg-<?php echo $statutClass[$don['statut']] ?? 'secondary'; ?>">
                                            <?php echo $statutLabel[$don['statut']] ?? 'Inconnu'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/don/<?php echo $don['id_don']; ?>" class="btn btn-sm btn-info" title="Voir">
                                            👁️
                                        </a>
                                        <?php if ($don['statut'] == 'disponible'): ?>
                                            <a href="/don/<?php echo $don['id_don']; ?>/valider" class="btn btn-sm btn-success" 
                                               onclick="return confirm('Valider ce don et l\'affecter aux besoins ?');" title="Valider le don">
                                                ✅
                                            </a>
                                        <?php endif; ?>
                                        <a href="/don/<?php echo $don['id_don']; ?>/delete" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');" title="Supprimer">
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
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Total dons</h5>
                                <p class="card-text display-6"><?php echo count($dons); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Dons disponibles</h5>
                                <p class="card-text display-6">
                                    <?php 
                                        $disponibles = array_filter($dons, function($d) { 
                                            return $d['statut'] == 'disponible'; 
                                        });
                                        echo count($disponibles);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="card-title">Montant total en argent</h5>
                                <p class="card-text display-6">
                                    <?php 
                                        $totalArgent = array_reduce($dons, function($carry, $d) {
                                            return $carry + ($d['montant_argent'] ?? 0);
                                        }, 0);
                                        echo number_format($totalArgent, 0, ',', ' ') . ' Ar';
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
