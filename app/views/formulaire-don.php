<?php
/**
 * Formulaire de création d'un don
 */
$pageTitle = 'Ajouter un don - BNGRC';
?>

<?php include __DIR__ . '/assets/inc/header.php'; ?>
<?php include __DIR__ . '/assets/inc/navbar.php'; ?>

<link rel="stylesheet" href="/assets/css/besoins-form.css">

<div class="besoin-form-container">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="besoin-form-wrapper">
                    <div class="besoin-form-header">
                        <h1>🎁 Ajouter un nouveau don</h1>
                        <p class="subtitle">Remplissez le formulaire ci-dessous pour enregistrer votre contribution</p>
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
                            <strong>💡 Information :</strong> Votre don aidera les communautés en difficulté. Merci pour votre générosité !
                        </div>

                        <form action="/don/create" method="POST" class="needs-validation" novalidate>
                            <!-- Informations du donateur (en haut) -->
                            <div class="card" style="background: var(--gray-50); border: 2px solid var(--accent-blue); margin-bottom: 2rem;">
                                <div class="card-body">
                                    <h5 style="color: var(--accent-blue); margin-bottom: 1rem;">👤 Informations du donateur</h5>
                                    
                                    <div class="form-group">
                                        <label for="donateur_nom" class="form-label">
                                            Nom complet <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="donateur_nom" name="donateur_nom" 
                                            placeholder="Nom complet du donateur" required
                                            value="<?php echo isset($old['donateur_nom']) ? htmlspecialchars($old['donateur_nom']) : ''; ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="donateur_contact" class="form-label">
                                            Contact <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="donateur_contact" name="donateur_contact" 
                                            placeholder="Téléphone ou email" required
                                            value="<?php echo isset($old['donateur_contact']) ? htmlspecialchars($old['donateur_contact']) : ''; ?>">
                                    </div>

                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label for="description" class="form-label">📝 Description générale</label>
                                        <textarea class="form-control" id="description" name="description" rows="2" 
                                            placeholder="Détails supplémentaires sur votre contribution..."><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section des dons multiples -->
                            <div style="margin-bottom: 1.5rem;">
                                <h5 style="color: var(--primary-dark); margin-bottom: 1rem;">🎁 Dons à enregistrer</h5>
                                <p class="text-muted" style="font-size: 0.9rem; margin-bottom: 1rem;">
                                    💡 Vous pouvez ajouter plusieurs dons en cliquant sur "Ajouter un autre don"
                                </p>
                            </div>

                            <!-- Container pour les dons multiples -->
                            <div id="dons-container">
                                <!-- Le premier don sera ajouté par JavaScript -->
                            </div>

                            <!-- Bouton ajouter un don -->
                            <div style="margin-bottom: 2rem;">
                                <button type="button" id="add-don-btn" class="btn btn-success">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Ajouter un autre don
                                </button>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">✅ Enregistrer tous les dons</button>
                                <a href="/dons" class="btn btn-secondary">❌ Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="<?php echo Flight::get('csp_nonce'); ?>">
    // Attendre que le DOM soit complètement chargé
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé, initialisation du formulaire de dons...');
        
        // Données des types et articles (depuis PHP)
        const typeDons = <?= json_encode($typeDons) ?>;
        const articles = <?= json_encode($articles) ?>;
        
        console.log('Types de dons:', typeDons.length, 'Articles:', articles.length);
        
        let donCounter = 0;
        
        // Créer un template de don
        function createDonTemplate(index) {
            return `
                <div class="don-item card" data-don-index="${index}" style="margin-bottom: 1.5rem; border-left: 4px solid var(--accent-blue);">
                    <div class="card-body" style="position: relative;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h6 style="color: var(--primary-dark); margin: 0;">Don #${index + 1}</h6>
                            ${index > 0 ? `
                            <button type="button" class="btn-remove-don" style="background: var(--danger); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 18px; line-height: 1;">
                                ×
                            </button>
                            ` : ''}
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">📋 Type de don <span class="text-danger">*</span></label>
                                    <select class="form-control form-select type-don-select" name="dons[${index}][type_don]" required>
                                        <option value="">-- Sélectionner --</option>
                                        ${typeDons.map(t => `<option value="${t.id_type_don}" data-libelle="${t.libelle_type}">${t.libelle_type}</option>`).join('')}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group article-group">
                                    <label class="form-label">📦 Article <span class="text-danger">*</span></label>
                                    <select class="form-control form-select article-select" name="dons[${index}][article]" required disabled>
                                        <option value="">-- Sélectionner un type d'abord --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 quantite-group" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">🔢 Quantité <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control quantite-input" name="dons[${index}][quantite]" 
                                        placeholder="Ex: 100" step="0.01" min="0.01">
                                </div>
                            </div>
                            <div class="col-md-6 montant-group" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">💵 Montant (Ar) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control montant-input" name="dons[${index}][montant]" 
                                        placeholder="Ex: 500000" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Ajouter un nouveau don
        function addDon() {
            console.log('Ajout d\'un don #' + donCounter);
            const container = document.getElementById('dons-container');
            const donHTML = createDonTemplate(donCounter);
            container.insertAdjacentHTML('beforeend', donHTML);
            
            const donItem = container.lastElementChild;
            setupDonEventListeners(donItem, donCounter);
            
            donCounter++;
            updateDonNumbers();
        }
        
        // Supprimer un don
        function removeDon(donItem) {
            if (document.querySelectorAll('.don-item').length > 1) {
                console.log('Suppression d\'un don');
                donItem.remove();
                updateDonNumbers();
            } else {
                alert('❌ Vous devez garder au moins un don!');
            }
        }
        
        // Mettre à jour les numéros
        function updateDonNumbers() {
            document.querySelectorAll('.don-item').forEach((item, index) => {
                const title = item.querySelector('h6');
                if (title) title.textContent = `Don #${index + 1}`;
            });
        }
        
        // Configurer les événements pour un don
    function setupDonEventListeners(donItem, index) {
        const typeDonSelect = donItem.querySelector('.type-don-select');
        const articleSelect = donItem.querySelector('.article-select');
        const quantiteGroup = donItem.querySelector('.quantite-group');
        const montantGroup = donItem.querySelector('.montant-group');
        const quantiteInput = donItem.querySelector('.quantite-input');
        const montantInput = donItem.querySelector('.montant-input');
        const removeBtn = donItem.querySelector('.btn-remove-don');
        
        // Événement changement de type
        typeDonSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const selectedType = selectedOption.getAttribute('data-libelle');
            
            if (!selectedType) {
                articleSelect.disabled = true;
                articleSelect.innerHTML = '<option value="">-- Sélectionner un type d\'abord --</option>';
                quantiteGroup.style.display = 'none';
                montantGroup.style.display = 'none';
                return;
            }
            
            // Filtrer les articles
            articleSelect.disabled = false;
            let options = '<option value="">-- Sélectionner --</option>';
            
            if (selectedType.toLowerCase() === 'argent') {
                options += '<option value="argent" data-type="Argent">💰 Don en argent</option>';
            } else {
                articles.forEach(art => {
                    if (art.libelle_type.toLowerCase() === selectedType.toLowerCase()) {
                        options += `<option value="${art.id_article}" data-type="${art.libelle_type}">${art.nom_article}</option>`;
                    }
                });
            }
            
            articleSelect.innerHTML = options;
            
            // Auto-select si une seule option
            if (articleSelect.options.length === 2) {
                articleSelect.selectedIndex = 1;
                articleSelect.dispatchEvent(new Event('change'));
            }
        });
        
        // Événement changement d'article
        articleSelect.addEventListener('change', function() {
            const selectedValue = this.value;
            
                if (selectedValue === 'argent') {
                    quantiteGroup.style.display = 'none';
                    montantGroup.style.display = 'block';
                    quantiteInput.removeAttribute('required');
                    quantiteInput.value = '';
                    montantInput.setAttribute('required', 'required');
                } else if (selectedValue !== '') {
                    quantiteGroup.style.display = 'block';
                    montantGroup.style.display = 'none';
                    quantiteInput.setAttribute('required', 'required');
                    montantInput.removeAttribute('required');
                    montantInput.value = '';
                } else {
                    quantiteGroup.style.display = 'none';
                    montantGroup.style.display = 'none';
                    quantiteInput.removeAttribute('required');
                    montantInput.removeAttribute('required');
                }
            });
            
            // Événement supprimer
            if (removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    removeDon(donItem);
                });
            }
        }
        
        // Bouton ajouter
        const addDonBtn = document.getElementById('add-don-btn');
        if (addDonBtn) {
            console.log('Bouton "Ajouter un don" trouvé, ajout de l\'événement');
            addDonBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Clic sur le bouton "Ajouter un don"');
                addDon();
            });
        } else {
            console.error('Bouton "Ajouter un don" introuvable!');
        }
        
        // Ajouter le premier don au chargement
        console.log('Ajout du premier don...');
        addDon();
        
        // Validation du formulaire
        const forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                // Vérifier qu'au moins un don est ajouté
                const donsCount = document.querySelectorAll('.don-item').length;
                if (donsCount === 0) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('⚠️ Veuillez ajouter au moins un don');
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
    .don-item {
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
    
    .btn-remove-don:hover {
        background: var(--secondary-red-dark) !important;
        transform: scale(1.1);
        transition: all 0.2s;
    }
</style>

<?php include __DIR__ . '/assets/inc/footer.php'; ?>