<?php
/**
 * Formulaire de création d'un besoin
 */
?>

<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<link rel="stylesheet" href="/assets/css/besoins-form.css">

<div class="besoin-form-container">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="besoin-form-wrapper">
                    <div class="besoin-form-header">
                        <h1>📋 Ajouter un nouveau besoin</h1>
                        <p class="subtitle">Remplissez le formulaire ci-dessous pour enregistrer les besoins de votre région</p>
                    </div>

                    <div class="besoin-form-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h4 class="alert-heading">⚠️ Erreurs détectées</h4>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="info-box">
                            <strong>💡 Conseil :</strong> Vous pouvez ajouter plusieurs articles pour un même besoin. Utilisez le bouton "+ Ajouter un article" pour enrichir votre demande.
                        </div>

                        <form method="POST" action="/besoin/create" class="needs-validation" novalidate>
                            <!-- Sélection de la ville -->
                            <div class="form-group">
                                <label for="id_ville" class="form-label">
                                    🏘️ Ville <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-select" id="id_ville" name="id_ville" required>
                                    <option value="">-- Sélectionner une ville --</option>
                                    <?php foreach ($villes as $ville): ?>
                                        <option value="<?php echo $ville['id_ville']; ?>" 
                                            <?php echo (isset($selectedVille) && $selectedVille == $ville['id_ville']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($ville['nom_ville']); ?> (<?php echo htmlspecialchars($ville['nom_region']); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Section Articles -->
                            <div class="form-group">
                                <div class="articles-section">
                                    <label class="form-label">
                                        📦 Articles <span class="text-danger">*</span>
                                        <span class="articles-counter" id="articles-count">1</span>
                                    </label>
                                    
                                    <div id="articles-container">
                                        <div class="article-item card" data-article-index="0">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label class="form-label">Article</label>
                                                    <select class="form-control article-select" name="id_article[]" required>
                                                        <option value="">-- Sélectionner un article --</option>
                                                        <?php foreach ($articles as $article): ?>
                                                            <option value="<?php echo $article['id_article']; ?>" data-price="<?php echo $article['prix_unitaire'] ?? 0; ?>"
                                                                <?php echo (isset($selectedArticle) && $selectedArticle == $article['id_article']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($article['nom_article']); ?> (<?php echo htmlspecialchars($article['libelle_type']); ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Quantité</label>
                                                    <input type="number" class="form-control" name="quantite[]" 
                                                        placeholder="Quantité" step="0.01" min="0.01" required
                                                        value="<?php echo isset($old['quantite']) ? htmlspecialchars($old['quantite']) : ''; ?>">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Prix unitaire (Ar)</label>
                                                    <input type="number" class="form-control price-input" name="prix_unitaire[]" 
                                                        placeholder="Automatique" step="0.01" min="0" readonly
                                                        value="<?php echo isset($old['prix_unitaire']) ? htmlspecialchars($old['prix_unitaire']) : ''; ?>">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger btn-sm remove-article">🗑️ Supprimer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add-article-btn" class="btn btn-secondary btn-sm">➕ Ajouter un article</button>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description" class="form-label">📝 Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Détails supplémentaires sur le besoin..."><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
                            </div>

                            <!-- Urgence -->
                            <div class="form-group">
                                <label for="urgence" class="form-label">⚡ Niveau d'urgence</label>
                                <select class="form-control form-select" id="urgence" name="urgence">
                                    <option value="normale" <?php echo (isset($old['urgence']) && $old['urgence'] == 'normale') ? 'selected' : 'selected'; ?>>
                                        🟢 Normale
                                    </option>
                                    <option value="urgente" <?php echo (isset($old['urgence']) && $old['urgence'] == 'urgente') ? 'selected' : ''; ?>>
                                        🟡 Urgente
                                    </option>
                                    <option value="critique" <?php echo (isset($old['urgence']) && $old['urgence'] == 'critique') ? 'selected' : ''; ?>>
                                        🔴 Critique
                                    </option>
                                </select>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">✅ Ajouter le besoin</button>
                                <a href="/besoins" class="btn btn-secondary">❌ Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales
    const articlesContainer = document.getElementById('articles-container');
    const addArticleBtn = document.getElementById('add-article-btn');
    const articlesCountBadge = document.getElementById('articles-count');
    const articlesData = <?php echo json_encode($articles); ?>;

    // Map des articles avec leurs prix pour accès rapide
    const articlesMap = {};
    articlesData.forEach(article => {
        articlesMap[article.id_article] = {
            nom: article.nom_article,
            type: article.libelle_type,
            prix: article.prix_unitaire || 0
        };
    });

    // Créer les options des articles
    function createArticleOption() {
        let html = '<option value="">-- Sélectionner un article --</option>';
        articlesData.forEach(article => {
            html += `<option value="${article.id_article}" data-price="${article.prix_unitaire || 0}">${htmlEscape(article.nom_article)} (${htmlEscape(article.libelle_type)})</option>`;
        });
        return html;
    }

    // Échapper le HTML
    function htmlEscape(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Gérer le changement d'article
    function handleArticleChange(event) {
        const select = event.target;
        const priceInput = select.closest('.article-item').querySelector('input[name="prix_unitaire[]"]');
        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        
        if (priceInput && price) {
            priceInput.value = price;
            // Animation du remplissage
            priceInput.style.backgroundColor = '#e7f3ff';
            setTimeout(() => {
                priceInput.style.transition = 'background-color 0.3s ease';
                priceInput.style.backgroundColor = 'white';
            }, 50);
        }
    }

    // Créer un nouvel article
    function createArticleItem() {
        const index = articlesContainer.children.length;
        const div = document.createElement('div');
        div.className = 'article-item card';
        div.setAttribute('data-article-index', index);
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
                        placeholder="Quantité" step="0.01" min="0.01" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Prix unitaire (Ar)</label>
                    <input type="number" class="form-control price-input" name="prix_unitaire[]" 
                        placeholder="Automatique" step="0.01" min="0" readonly>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-article">🗑️ Supprimer</button>
                </div>
            </div>
        `;
        return div;
    }

    // Mettre à jour le compteur d'articles
    function updateArticleCount() {
        const count = articlesContainer.children.length;
        articlesCountBadge.textContent = count;
    }

    // Attacher les listeners de changement d'article
    function attachArticleChangeListeners() {
        const selects = document.querySelectorAll('.article-select');
        selects.forEach(select => {
            select.removeEventListener('change', handleArticleChange);
            select.addEventListener('change', handleArticleChange);
        });
    }

    // Attacher les listeners de suppression
    function attachRemoveListeners() {
        const removeButtons = document.querySelectorAll('.remove-article');
        removeButtons.forEach(btn => {
            btn.removeEventListener('click', removeArticle);
            btn.addEventListener('click', removeArticle);
        });
    }

    // Ajouter un nouvel article
    addArticleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const newArticle = createArticleItem();
        articlesContainer.appendChild(newArticle);
        updateArticleCount();
        attachArticleChangeListeners();
        attachRemoveListeners();
        // Animation
        setTimeout(() => {
            newArticle.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    });

    // Supprimer un article
    function removeArticle(e) {
        e.preventDefault();
        const totalArticles = articlesContainer.children.length;
        
        if (totalArticles <= 1) {
            alert('❌ Vous devez garder au moins un article!');
            return;
        }

        const articleItem = e.target.closest('.article-item');
        articleItem.classList.add('removing');
        
        setTimeout(() => {
            articleItem.remove();
            updateArticleCount();
        }, 300);
    }

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

    // Initialiser les listeners au chargement
    attachArticleChangeListeners();
    attachRemoveListeners();
    updateArticleCount();
</script>
