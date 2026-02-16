<!-- app/views/articles/form.php - nampian'i Francia -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($article) ? 'Modifier' : 'Ajouter' ?> un article</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><?= isset($article) ? 'Modifier' : 'Ajouter' ?> un article</h1>
        
        <!-- Message erreur -->
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">Veuillez remplir tous les champs obligatoires</div>
        <?php endif; ?>
        
        <form method="POST" action="<?= isset($article) ? '/articles/update/'.$article['id_article'] : '/articles/save' ?>">
            <div class="mb-3">
                <label class="form-label">Nom article <span class="text-danger">*</span></label>
                <input type="text" name="nom_article" class="form-control" value="<?= $article['nom_article'] ?? '' ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Type de besoin <span class="text-danger">*</span></label>
                <select name="id_type_besoin" class="form-control" required>
                    <option value="">-- Fidio ny type --</option>
                    <?php foreach($types as $t): ?>
                    <option value="<?= $t['id_type_besoin'] ?>" <?= (isset($article) && $article['id_type_besoin'] == $t['id_type_besoin']) ? 'selected' : '' ?>>
                        <?= $t['libelle_type'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($article['description'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-success"><?= isset($article) ? 'Modifier' : 'Ajouter' ?></button>
            <a href="/articles" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>