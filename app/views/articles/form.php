<?php
$pageTitle = (isset($article) ? 'Modifier' : 'Ajouter') . ' un article - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<div class="container mt-5 fade-in">
    <div class="page-header">
        <h1 class="page-title">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
            </svg>
            <?= isset($article) ? 'Modifier' : 'Ajouter' ?> un article
        </h1>
    </div>
    
    <!-- Message erreur -->
    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            Veuillez remplir tous les champs obligatoires
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= isset($article) ? '/articles/update/'.$article['id_article'] : '/articles/save' ?>">
                <div class="form-group">
                    <label class="form-label">Nom article <span class="text-danger">*</span></label>
                    <input type="text" name="nom_article" class="form-control" value="<?= $article['nom_article'] ?? '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Type de besoin <span class="text-danger">*</span></label>
                    <select name="id_type_besoin" class="form-control" required>
                        <option value="">-- Sélectionner le type --</option>
                        <?php foreach($types as $t): ?>
                        <option value="<?= $t['id_type_besoin'] ?>" <?= (isset($article) && $article['id_type_besoin'] == $t['id_type_besoin']) ? 'selected' : '' ?>>
                            <?= $t['libelle_type'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($article['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Prix unitaire (Ar) <span class="text-danger">*</span></label>
                    <input type="number" name="prix_unitaire" class="form-control" step="0.01" min="0" value="<?= $article['prix_unitaire'] ?? '' ?>" required>
                    <small class="form-text text-muted">Prix unitaire en Ariary</small>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <?= isset($article) ? 'Modifier' : 'Enregistrer' ?>
                    </button>
                    <a href="/articles" class="btn btn-secondary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>