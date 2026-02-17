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
                            <strong>💡 Conseil :</strong> Vous pouvez ajouter plusieurs besoins pour différentes villes. Chaque besoin peut contenir plusieurs articles.
                        </div>

                        <form method="POST" action="/besoin/create" class="needs-validation" novalidate>
                            <!-- Container pour les besoins multiples -->
                            <div id="besoins-container">
                                <!-- Les besoins seront ajoutés par JavaScript -->
                            </div>

                            <!-- Bouton ajouter un besoin -->
                            <div style="margin-bottom: 2rem;">
                                <button type="button" id="add-besoin-btn" class="btn btn-success">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Ajouter un autre besoin
                                </button>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">✅ Enregistrer tous les besoins</button>
                                <a href="/besoins" class="btn btn-secondary">❌ Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../assets/inc/footer.php'; ?>

<script nonce="<?php echo Flight::get('csp_nonce'); ?>">
    // Attendre que le DOM soit complètement chargé
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé, initialisation du formulaire...');
        
        // Données depuis PHP
        const villes = <?php echo json_encode($villes); ?>;
        const articlesData = <?php echo json_encode($articles); ?>;
        
        console.log('Villes:', villes.length, 'Articles:', articlesData.length);
        
        let besoinCounter = 0;

        // Map des articles avec leurs prix
        const articlesMap = {};
        articlesData.forEach(article => {
            articlesMap[article.id_article] = {
                nom: article.nom_article,
                type: article.libelle_type,
                prix: article.prix_unitaire || 0
            };
        });

        // Créer les options de villes
        function createVilleOptions() {
            let html = '<option value="">-- Sélectionner une ville --</option>';
            villes.forEach(ville => {
                html += `<option value="${ville.id_ville}">${htmlEscape(ville.nom_ville)} (${htmlEscape(ville.nom_region)})</option>`;
            });
            return html;
        }

        // Créer les options d'articles
        function createArticleOptions() {
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

        // Créer un template d'article
        function createArticleTemplate(besoinIndex, articleIndex) {
            return `
                <div class="article-item card" data-article-index="${articleIndex}" style="margin-bottom: 0.75rem; padding: 1rem; border: 1px solid var(--gray-300);">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label" style="font-size: 0.875rem;">Article</label>
                            <select class="form-control article-select" name="besoins[${besoinIndex}][articles][${articleIndex}][id_article]" required>
                                ${createArticleOptions()}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size: 0.875rem;">Quantité</label>
                            <input type="number" class="form-control" name="besoins[${besoinIndex}][articles][${articleIndex}][quantite]" 
                                placeholder="Ex: 100" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size: 0.875rem;">
                                Prix unitaire (Ar) 
                                <small style="color: #6c757d;">✓ Auto</small>
                            </label>
                            <input type="number" class="form-control price-input" name="besoins[${besoinIndex}][articles][${articleIndex}][prix_unitaire]" 
                                placeholder="Auto" step="0.01" min="0" required style="background-color: #f0f9ff; font-weight: 500;">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm remove-article-btn" style="width: 100%;">
                                🗑️
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Créer un template de besoin
        function createBesoinTemplate(index) {
            return `
                <div class="besoin-item card" data-besoin-index="${index}" style="margin-bottom: 1.5rem; border-left: 4px solid var(--accent-blue);">
                    <div class="card-body" style="position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h6 style="color: var(--primary-dark); margin: 0;">Besoin #${index + 1}</h6>
                            ${index > 0 ? `
                            <button type="button" class="btn-remove-besoin" style="background: var(--danger); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 18px; line-height: 1;">
                                ×
                            </button>
                            ` : ''}
                        </div>
                        
                        <!-- Ville -->
                        <div class="form-group">
                            <label class="form-label">🏘️ Ville <span class="text-danger">*</span></label>
                            <select class="form-control form-select" name="besoins[${index}][id_ville]" required>
                                ${createVilleOptions()}
                            </select>
                        </div>

                        <!-- Articles -->
                        <div class="form-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <label class="form-label" style="margin: 0;">📦 Articles <span class="text-danger">*</span></label>
                                <button type="button" class="add-article-btn btn btn-sm btn-outline-primary" data-besoin-index="${index}">
                                    ➕ Ajouter un article
                                </button>
                            </div>
                            <div class="articles-container" data-besoin-index="${index}">
                                ${createArticleTemplate(index, 0)}
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label class="form-label">📝 Description</label>
                            <textarea class="form-control" name="besoins[${index}][description]" rows="2" 
                                placeholder="Détails supplémentaires..."></textarea>
                        </div>

                        <!-- Urgence -->
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">⚡ Niveau d'urgence</label>
                            <select class="form-control form-select" name="besoins[${index}][urgence]">
                                <option value="normale" selected>🟢 Normale</option>
                                <option value="urgente">🟡 Urgente</option>
                                <option value="critique">🔴 Critique</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
        }

        // Ajouter un nouveau besoin
        function addBesoin() {
            console.log('Ajout d\'un besoin #' + besoinCounter);
            const container = document.getElementById('besoins-container');
            const besoinHTML = createBesoinTemplate(besoinCounter);
            container.insertAdjacentHTML('beforeend', besoinHTML);
            
            const besoinItem = container.lastElementChild;
            setupBesoinEventListeners(besoinItem, besoinCounter);
            
            besoinCounter++;
            updateBesoinNumbers();
        }

        // Supprimer un besoin
        function removeBesoin(besoinItem) {
            if (document.querySelectorAll('.besoin-item').length > 1) {
                console.log('Suppression d\'un besoin');
                besoinItem.remove();
                updateBesoinNumbers();
            } else {
                alert('❌ Vous devez garder au moins un besoin!');
            }
        }

        // Mettre à jour les numéros
        function updateBesoinNumbers() {
            document.querySelectorAll('.besoin-item').forEach((item, index) => {
                const title = item.querySelector('h6');
                if (title) title.textContent = `Besoin #${index + 1}`;
            });
        }

        // Ajouter un article dans un besoin
        function addArticleToBesoin(besoinIndex) {
            console.log('Ajout d\'un article au besoin #' + besoinIndex);
            const container = document.querySelector(`.articles-container[data-besoin-index="${besoinIndex}"]`);
            const articleIndex = container.children.length;
            const articleHTML = createArticleTemplate(besoinIndex, articleIndex);
            container.insertAdjacentHTML('beforeend', articleHTML);
            
            // Attacher les événements au nouvel article
            const newArticle = container.lastElementChild;
            setupArticleEventListeners(newArticle);
        }

        // Supprimer un article
        function removeArticle(articleItem, besoinIndex) {
            const container = document.querySelector(`.articles-container[data-besoin-index="${besoinIndex}"]`);
            
            if (container.children.length <= 1) {
                alert('❌ Vous devez garder au moins un article par besoin!');
                return;
            }
            
            console.log('Suppression d\'un article');
            articleItem.remove();
        }

        // Gérer le changement d'article (remplir le prix)
        function handleArticleChange(select) {
            const priceInput = select.closest('.article-item').querySelector('.price-input');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price') || 0;
            
            if (priceInput) {
                if (price && parseFloat(price) > 0) {
                    priceInput.value = price;
                    priceInput.style.backgroundColor = '#d4edda';
                    priceInput.style.borderColor = '#28a745';
                    priceInput.style.fontWeight = '600';
                    
                    // Afficher un indicateur visuel temporaire
                    const indicator = document.createElement('span');
                    indicator.textContent = '✓ Prix chargé';
                    indicator.style.cssText = 'position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #28a745; font-size: 0.75rem; font-weight: 600; animation: fadeOut 2s forwards;';
                    priceInput.parentElement.style.position = 'relative';
                    priceInput.parentElement.appendChild(indicator);
                    
                    setTimeout(() => {
                        priceInput.style.transition = 'all 0.3s ease';
                        priceInput.style.backgroundColor = '#f0f9ff';
                        priceInput.style.borderColor = '#ced4da';
                        if (indicator && indicator.parentElement) {
                            indicator.remove();
                        }
                    }, 1500);
                } else {
                    priceInput.value = '';
                    priceInput.placeholder = 'Prix manquant';
                    priceInput.style.backgroundColor = '#fff3cd';
                    priceInput.style.borderColor = '#ffc107';
                }
            }
        }

        // Configurer les événements pour un article
        function setupArticleEventListeners(articleItem) {
            const articleSelect = articleItem.querySelector('.article-select');
            const removeBtn = articleItem.querySelector('.remove-article-btn');
            
            if (articleSelect) {
                articleSelect.addEventListener('change', function() {
                    handleArticleChange(this);
                });
            }
            
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const besoinIndex = articleItem.closest('.besoin-item').getAttribute('data-besoin-index');
                    removeArticle(articleItem, besoinIndex);
                });
            }
        }

        // Configurer les événements pour un besoin
        function setupBesoinEventListeners(besoinItem, index) {
            const removeBtn = besoinItem.querySelector('.btn-remove-besoin');
            const addArticleBtn = besoinItem.querySelector('.add-article-btn');
            
            // Événement supprimer besoin
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    removeBesoin(besoinItem);
                });
            }
            
            // Événement ajouter article
            if (addArticleBtn) {
                addArticleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const besoinIndex = this.getAttribute('data-besoin-index');
                    addArticleToBesoin(besoinIndex);
                });
            }
            
            // Configurer les articles existants
            const articles = besoinItem.querySelectorAll('.article-item');
            articles.forEach(article => setupArticleEventListeners(article));
        }

        // Bouton ajouter besoin
        const addBesoinBtn = document.getElementById('add-besoin-btn');
        if (addBesoinBtn) {
            console.log('Bouton "Ajouter un besoin" trouvé, ajout de l\'événement');
            addBesoinBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Clic sur le bouton "Ajouter un besoin"');
                addBesoin();
            });
        } else {
            console.error('Bouton "Ajouter un besoin" introuvable!');
        }
        
        // Ajouter le premier besoin au chargement
        console.log('Ajout du premier besoin...');
        addBesoin();
        
        // Validation du formulaire
        const forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                const besoinsCount = document.querySelectorAll('.besoin-item').length;
                if (besoinsCount === 0) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('⚠️ Veuillez ajouter au moins un besoin');
                    return false;
                }
                
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
        
        console.log('Initialisation terminée!');
    });
</script>

<style>
    .besoin-item {
        animation: slideIn 0.3s ease-in-out;
    }
    
    .article-item {
        animation: slideIn 0.3s ease-in-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeOut {
        0% {
            opacity: 1;
        }
        70% {
            opacity: 1;
        }
        100% {
            opacity: 0;
        }
    }
    
    .btn-remove-besoin:hover,
    .remove-article-btn:hover {
        background: var(--secondary-red-dark) !important;
        transform: scale(1.1);
        transition: all 0.2s;
    }
    
    .add-article-btn {
        font-size: 0.875rem;
    }
    
    .price-input {
        transition: all 0.3s ease;
    }
    
    .price-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>
