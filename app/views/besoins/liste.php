<!-- app/views/besoins/liste.php - nampian'i Francia -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Besoins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-en-cours { background-color: #ffc107; color: #000; }
        .badge-partiel { background-color: #17a2b8; color: #fff; }
        .badge-satisfait { background-color: #28a745; color: #fff; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Liste des Besoins par Ville</h1>
        
        <!-- Filtres rapides -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-control" id="filtreVille">
                    <option value="">-- Filtrer par ville --</option>
                    <?php
                    $villes = [];
                    foreach($besoins as $b) {
                        if(!in_array($b['nom_ville'], $villes)) {
                            $villes[] = $b['nom_ville'];
                            echo '<option value="'.$b['nom_ville'].'">'.$b['nom_ville'].'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filtreStatut">
                    <option value="">-- Filtrer par statut --</option>
                    <option value="en_cours">En cours</option>
                    <option value="partiel">Partiel</option>
                    <option value="satisfait">Satisfait</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filtreType">
                    <option value="">-- Filtrer par type --</option>
                    <?php
                    $types = [];
                    foreach($besoins as $b) {
                        if(!in_array($b['libelle_type'], $types)) {
                            $types[] = $b['libelle_type'];
                            echo '<option value="'.$b['libelle_type'].'">'.$b['libelle_type'].'</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-secondary" onclick="resetFiltres()">Réinitialiser</button>
            </div>
        </div>
        
        <!-- Tableau des besoins -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="tableBesoins">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Ville</th>
                        <th>Région</th>
                        <th>Article</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Montant total</th>
                        <th>Affecté</th>
                        <th>Reste</th>
                        <th>Date saisie</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($besoins)): ?>
                    <tr>
                        <td colspan="12" class="text-center">Tsy misy besoins mbola</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($besoins as $b): ?>
                    <tr data-ville="<?= $b['nom_ville'] ?>" data-statut="<?= $b['statut'] ?>" data-type="<?= $b['libelle_type'] ?>">
                        <td><?= $b['id_besoin'] ?></td>
                        <td><?= htmlspecialchars($b['nom_ville']) ?></td>
                        <td><?= htmlspecialchars($b['nom_region']) ?></td>
                        <td><?= htmlspecialchars($b['nom_article']) ?></td>
                        <td><?= $b['libelle_type'] ?></td>
                        <td class="text-end"><?= number_format($b['quantite'], 2) ?></td>
                        <td class="text-end"><?= number_format($b['prix_unitaire'], 2) ?> Ar</td>
                        <td class="text-end"><?= number_format($b['montant_total'], 2) ?> Ar</td>
                        <td class="text-end"><?= number_format($b['quantite_deja_affectee'], 2) ?></td>
                        <td class="text-end"><?= number_format($b['quantite_restante'], 2) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($b['date_saisie'])) ?></td>
                        <td>
                            <?php
                            $badgeClass = 'badge-en-cours';
                            if($b['statut'] == 'satisfait') $badgeClass = 'badge-satisfait';
                            if($b['statut'] == 'partiel') $badgeClass = 'badge-partiel';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $b['statut'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    // Filtres JavaScript
    document.getElementById('filtreVille').addEventListener('change', filterTable);
    document.getElementById('filtreStatut').addEventListener('change', filterTable);
    document.getElementById('filtreType').addEventListener('change', filterTable);

    function filterTable() {
        const ville = document.getElementById('filtreVille').value;
        const statut = document.getElementById('filtreStatut').value;
        const type = document.getElementById('filtreType').value;
        const rows = document.querySelectorAll('#tableBesoins tbody tr');
        
        rows.forEach(row => {
            const rowVille = row.getAttribute('data-ville');
            const rowStatut = row.getAttribute('data-statut');
            const rowType = row.getAttribute('data-type');
            
            let show = true;
            if(ville && rowVille !== ville) show = false;
            if(statut && rowStatut !== statut) show = false;
            if(type && rowType !== type) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    }

    function resetFiltres() {
        document.getElementById('filtreVille').value = '';
        document.getElementById('filtreStatut').value = '';
        document.getElementById('filtreType').value = '';
        filterTable();
    }
    </script>
</body>
</html>