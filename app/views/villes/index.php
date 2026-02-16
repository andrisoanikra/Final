<?php
/**
 * Liste des villes
 */
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Liste des villes</h1>
            <a href="/ville/create" class="btn btn-primary mb-3">Ajouter une ville</a>
            <hr>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                        if ($_GET['success'] == 'ville_ajoutee') {
                            echo 'Ville ajoutée avec succès!';
                        } elseif ($_GET['success'] == 'ville_supprimee') {
                            echo 'Ville supprimée avec succès!';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($villes)): ?>
                <div class="alert alert-info">
                    Aucune ville enregistrée. <a href="/ville/create">Créer une ville</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Région</th>
                                <th>Ville</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($villes as $ville): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ville['nom_region'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($ville['nom_ville']); ?></td>
                                    <td>
                                        <a href="/ville/<?php echo $ville['id_ville']; ?>" class="btn btn-sm btn-info">Voir</a>
                                        <a href="/ville/<?php echo $ville['id_ville']; ?>/delete" class="btn btn-sm btn-danger">Supprimer</a>
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
