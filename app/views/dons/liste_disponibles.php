<!-- app/views/dons/liste_disponibles.php - nampian'i Francia -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dons disponibles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-disponible { background-color: #28a745; color: #fff; }
        .badge-partiel { background-color: #ffc107; color: #000; }
        .badge-affecte { background-color: #6c757d; color: #fff; }
        .type-nature { background-color: #17a2b8; }
        .type-materiau { background-color: #fd7e14; }
        .type-argent { background-color: #28a745; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="mb-4">Dons disponibles</h1>
        
        <!-- Filtres -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-control" id="filtreType">
                    <option value="">-- Filtrer par type --</option>
                    <option value="nature">Nature</option>
                    <option value="materiau">Matériau</option>
                    <option value="argent">Argent</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="filtreStatut">
                    <option value="">-- Filtrer par statut --</option>
                    <option value="disponible">Disponible</option>
                    <option value="partiel">Partiel</option>
                    <option value="affecte">Affecté</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="filtreDonateur" placeholder="Filtrer par donateur">
            </div>
            <div class="col-md-3">
                <button class="btn btn-secondary" onclick="resetFiltres()">Réinitialiser</button>
            </div>
        </div>
        
        <!-- Tableau des dons -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="tableDons">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Article</th>
                        <th>Quantité</th>
                        <th>Montant (Ar)</th>
                        <th>Donateur</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Affecté</th>
                        <th>Reste</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($dons)): ?>
                    <tr>
                        <td colspan="11" class="text-center">Tsy misy dons disponibles</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($dons as $d): ?>
                    <tr data-type="<?= $d['type_don'] ?>" data-statut="<?= $d['statut'] ?>" data-donateur="<?= htmlspecialchars($d['donateur_nom'] ?? '') ?>">
                        <td><?= $d['id_don'] ?></td>
                        <td>
                            <span class="badge type-<?= $d['type_don'] ?>"><?= $d['type_don'] ?></span>
                        </td>
                        <td><?= htmlspecialchars($d['nom_article'] ?? '-') ?></td>
                        <td class="text-end"><?= $d['quantite'] ? number_format($d['quantite'], 2) : '-' ?></td>
                        <td class="text-end"><?= $d['montant_argent'] ? number_format($d['montant_argent'], 2) : '-' ?></td>
                        <td><?= htmlspecialchars($d['donateur_nom'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($d['donateur_contact'] ?? '-') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($d['date_don'])) ?></td>
                        <td class="text-end"><?= number_format($d['quantite_deja_affectee'], 2) ?></td>
                        <td class="text-end"><?= number_format($d['quantite_restante'], 2) ?></td>
                        <td>
                            <?php
                            $badgeClass = 'badge-disponible';
                            if($d['statut'] == 'partiel') $badgeClass = 'badge-partiel';
                            if($d['statut'] == 'affecte') $badgeClass = 'badge-affecte';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $d['statut'] ?></span>
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
    document.getElementById('filtreType').addEventListener('change', filterTable);
    document.getElementById('filtreStatut').addEventListener('change', filterTable);
    document.getElementById('filtreDonateur').addEventListener('keyup', filterTable);

    function filterTable() {
        const type = document.getElementById('filtreType').value;
        const statut = document.getElementById('filtreStatut').value;
        const donateur = document.getElementById('filtreDonateur').value.toLowerCase();
        const rows = document.querySelectorAll('#tableDons tbody tr');
        
        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const rowStatut = row.getAttribute('data-statut');
            const rowDonateur = (row.getAttribute('data-donateur') || '').toLowerCase();
            
            let show = true;
            if(type && rowType !== type) show = false;
            if(statut && rowStatut !== statut) show = false;
            if(donateur && !rowDonateur.includes(donateur)) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    }

    function resetFiltres() {
        document.getElementById('filtreType').value = '';
        document.getElementById('filtreStatut').value = '';
        document.getElementById('filtreDonateur').value = '';
        filterTable();
    }
    </script>
</body>
</html>