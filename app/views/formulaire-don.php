<?php
/**
 * Formulaire de création d'un don
 */
?>

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
                            <!-- Type de don -->
                            <div class="form-group">
                                <label for="type_don" class="form-label">
                                    📋 Type de don <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-select" id="type_don" name="type_don" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    <?php foreach ($typeDons as $typeDon): ?>
                                        <option value="<?= htmlspecialchars($typeDon['id_type_don']) ?>">
                                            <?= htmlspecialchars($typeDon['libelle_type']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Article -->
                            <div class="form-group" id="article-group">
                                <label for="article" class="form-label">
                                    📦 Article <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-select" id="article" name="article" required>
                                    <option value="">-- Sélectionner un article --</option>
                                    <?php foreach ($articles as $article): ?>
                                        <option value="<?= htmlspecialchars($article['id_article']) ?>">
                                            <?= htmlspecialchars($article['nom_article']) ?> (<?= htmlspecialchars($article['libelle_type']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="argent">💰 Don en argent</option>
                                </select>
                            </div>

                            <!-- Quantité -->
                            <div class="form-group" id="quantite-group">
                                <label for="quantite" class="form-label">
                                    🔢 Quantité <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="quantite" name="quantite" 
                                    placeholder="Entrez la quantité" step="0.01" min="0.01"
                                    value="<?php echo isset($old['quantite']) ? htmlspecialchars($old['quantite']) : ''; ?>">
                            </div>

                            <!-- Montant (pour don en argent) -->
                            <div class="form-group" id="montant-group" style="display: none;">
                                <label for="montant" class="form-label">
                                    💵 Montant (Ar) <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="montant" name="montant" 
                                    placeholder="Montant en Ariary" step="0.01" min="0"
                                    value="<?php echo isset($old['montant']) ? htmlspecialchars($old['montant']) : ''; ?>">
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description" class="form-label">📝 Description du don</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                    placeholder="Détails supplémentaires..."><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
                            </div>

                            <!-- Informations du donateur -->
                            <div class="form-group">
                                <label for="donateur_nom" class="form-label">
                                    👤 Nom du donateur <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="donateur_nom" name="donateur_nom" 
                                    placeholder="Nom complet du donateur" required
                                    value="<?php echo isset($old['donateur_nom']) ? htmlspecialchars($old['donateur_nom']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="donateur_contact" class="form-label">
                                    📞 Contact du donateur <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="donateur_contact" name="donateur_contact" 
                                    placeholder="Téléphone ou email" required
                                    value="<?php echo isset($old['donateur_contact']) ? htmlspecialchars($old['donateur_contact']) : ''; ?>">
                            </div>

                            <!-- Boutons d'action -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">✅ Enregistrer le don</button>
                                <a href="/dons" class="btn btn-secondary">❌ Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Gestion de l'affichage conditionnel quantité/montant
    const articleSelect = document.getElementById('article');
    const quantiteGroup = document.getElementById('quantite-group');
    const montantGroup = document.getElementById('montant-group');
    const quantiteInput = document.getElementById('quantite');
    const montantInput = document.getElementById('montant');

    function toggleFields() {
        const selectedValue = articleSelect.value;
        
        if (selectedValue === 'argent') {
            // Don en argent : afficher montant, cacher quantité
            quantiteGroup.style.display = 'none';
            montantGroup.style.display = 'block';
            quantiteInput.removeAttribute('required');
            montantInput.setAttribute('required', 'required');
        } else if (selectedValue !== '') {
            // Don en nature/matériel : afficher quantité, cacher montant
            quantiteGroup.style.display = 'block';
            montantGroup.style.display = 'none';
            quantiteInput.setAttribute('required', 'required');
            montantInput.removeAttribute('required');
        } else {
            // Rien sélectionné : cacher les deux
            quantiteGroup.style.display = 'none';
            montantGroup.style.display = 'none';
            quantiteInput.removeAttribute('required');
            montantInput.removeAttribute('required');
        }
    }

    articleSelect.addEventListener('change', toggleFields);

    // Initialiser l'affichage au chargement
    toggleFields();

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
</script>