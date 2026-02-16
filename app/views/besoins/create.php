<?php
/**
 * Formulaire de création d'un besoin
 */
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Ajouter un nouveau besoin</h1>
            <hr>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-heading">Erreurs:</h4>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="/besoin/create" class="needs-validation">
                <div class="form-group mb-3">
                    <label for="id_ville" class="form-label">Ville <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_ville" name="id_ville" required>
                        <option value="">-- Sélectionner une ville --</option>
                        <?php foreach ($villes as $ville): ?>
                            <option value="<?php echo $ville['id_ville']; ?>" 
                                <?php echo (isset($selectedVille) && $selectedVille == $ville['id_ville']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ville['nom_ville']); ?> (<?php echo htmlspecialchars($ville['nom_region']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Articles <span class="text-danger">*</span></label>
                    <div id="articles-container">
                        <div class="article-item card p-3 mb-3">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label">Article</label>
                                    <select class="form-control article-select" name="id_article[]" required>
                                        <option value="">-- Sélectionner un article --</option>
                                        <?php foreach ($articles as $article): ?>
                                            <option value="<?php echo $article['id_article']; ?>" 
                                                <?php echo (isset($selectedArticle) && $selectedArticle == $article['id_article']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($article['nom_article']); ?> (<?php echo htmlspecialchars($article['libelle_type']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantité</label>
                                    <input type="number" class="form-control" name="quantite[]" 
                                        placeholder="Quantité" step="0.01" required
                                        value="<?php echo isset($old['quantite']) ? htmlspecialchars($old['quantite']) : ''; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Prix unitaire (Ar)</label>
                                    <input type="number" class="form-control" name="prix_unitaire[]" 
                                        placeholder="Prix unitaire" step="0.01" required
                                        value="<?php echo isset($old['prix_unitaire']) ? htmlspecialchars($old['prix_unitaire']) : ''; ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm w-100 remove-article">Supprimer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-article-btn" class="btn btn-secondary btn-sm">+ Ajouter un article</button>
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description du besoin"><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="urgence" class="form-label">Urgence</label>
                    <select class="form-control" id="urgence" name="urgence">
                        <option value="normale" <?php echo (isset($old['urgence']) && $old['urgence'] == 'normale') ? 'selected' : 'selected'; ?>>Normale</option>
                        <option value="urgente" <?php echo (isset($old['urgence']) && $old['urgence'] == 'urgente') ? 'selected' : ''; ?>>Urgente</option>
                        <option value="critique" <?php echo (isset($old['urgence']) && $old['urgence'] == 'critique') ? 'selected' : ''; ?>>Critique</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Ajouter le besoin</button>
                    <a href="/besoins" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Bootstrap form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                        form.classList.add('was-validated');
                    }
                }, false);
            });
        }, false);
    })();

    // Gestion des articles dynamiques
    const articlesContainer = document.getElementById('articles-container');
    const addArticleBtn = document.getElementById('add-article-btn');
    const articlesData = <?php echo json_encode($articles); ?>;

    function createArticleOption() {
        let html = '<option value="">-- Sélectionner un article --</option>';
        articlesData.forEach(article => {
            html += `<option value="${article.id_article}">${htmlEscape(article.nom_article)} (${htmlEscape(article.libelle_type)})</option>`;
        });
        return html;
    }

    function htmlEscape(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function createArticleItem() {
        const div = document.createElement('div');
        div.className = 'article-item card p-3 mb-3';
        div.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Article</label>
                    <select class="form-control article-select" name="id_article[]" required>
                        ${createArticleOption()}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantité</label>
                    <input type="number" class="form-control" name="quantite[]" 
                        placeholder="Quantité" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prix unitaire (Ar)</label>
                    <input type="number" class="form-control" name="prix_unitaire[]" 
                        placeholder="Prix unitaire" step="0.01" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm w-100 remove-article">Supprimer</button>
                </div>
            </div>
        `;
        return div;
    }

    addArticleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        articlesContainer.appendChild(createArticleItem());
        attachRemoveListeners();
    });

    function attachRemoveListeners() {
        const removeButtons = document.querySelectorAll('.remove-article');
        removeButtons.forEach(btn => {
            btn.removeEventListener('click', removeArticle);
            btn.addEventListener('click', removeArticle);
        });
    }

    function removeArticle(e) {
        e.preventDefault();
        if (document.querySelectorAll('.article-item').length > 1) {
            e.target.closest('.article-item').remove();
        } else {
            alert('Vous devez garder au moins un article.');
        }
    }

    // Attacher les listeners au chargement
    attachRemoveListeners();
</script>
