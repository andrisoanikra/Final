<!-- app/views/articles/liste.php - nampian'i Francia -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Liste des Articles</h1>
        
        <!-- Messages flash -->
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Article ajouté avec succès!</div>
        <?php endif; ?>
        <?php if(isset($_GET['updated'])): ?>
            <div class="alert alert-success">Article modifié avec succès!</div>
        <?php endif; ?>
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Article supprimé!</div>
        <?php endif; ?>
        <?php if(isset($_GET['error_delete'])): ?>
            <div class="alert alert-danger">Impossible de supprimer: article utilisé dans besoins ou dons</div>
        <?php endif; ?>
        
        <a href="/articles/ajouter" class="btn btn-primary mb-3">+ Nouvel article</a>
        
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom article</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($articles)): ?>
                <tr>
                    <td colspan="5" class="text-center">Tsy misy article mbola</td>
                </tr>
                <?php else: ?>
                <?php foreach($articles as $a): ?>
                <tr>
                    <td><?= $a['id_article'] ?></td>
                    <td><?= htmlspecialchars($a['nom_article']) ?></td>
                    <td><?= $a['libelle_type'] ?></td>
                    <td><?= htmlspecialchars($a['description'] ?? '') ?></td>
                    <td>
                        <a href="/articles/modifier/<?= $a['id_article'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="/articles/supprimer/<?= $a['id_article'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tena ho vonoina ve?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>