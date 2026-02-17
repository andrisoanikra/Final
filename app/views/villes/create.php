<?php
/**
 * Formulaire de création d'une ville
 */
$pageTitle = 'Ajouter une ville - BNGRC';
?>

<?php include __DIR__ . '/../assets/inc/header.php'; ?>
<?php include __DIR__ . '/../assets/inc/navbar.php'; ?>

<link rel="stylesheet" href="<?= $base_url ?>/assets/css/besoins-form.css">

<div class="besoin-form-container">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="besoin-form-wrapper">
                    <div class="besoin-form-header">
                        <h1>🏘️ Ajouter une nouvelle ville</h1>
                        <p class="subtitle">Enregistrez une nouvelle ville pour le suivi des besoins des sinistrés</p>
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

                        <form method="POST" action="<?= $base_url ?>/ville/create" class="needs-validation">
                            <div class="form-group mb-3">
                                <label for="id_region" class="form-label">
                                    📍 Région <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-select" id="id_region" name="id_region" required>
                                    <option value="">-- Sélectionner une région --</option>
                                    <?php foreach ($regions as $region): ?>
                                        <option value="<?php echo $region['id_region']; ?>" 
                                            <?php echo (isset($old['id_region']) && $old['id_region'] == $region['id_region']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($region['nom_region']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="nom_ville" class="form-label">
                                    🏘️ Nom de la ville <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nom_ville" name="nom_ville" 
                                    placeholder="Nom de la ville" required
                                    value="<?php echo isset($old['nom_ville']) ? htmlspecialchars($old['nom_ville']) : ''; ?>">
                            </div>

                            <div class="form-group mb-3">
                                <label for="description" class="form-label">📝 Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                    placeholder="Description de la ville"><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Ajouter la ville
                                </button>
                                <a href="<?= $base_url ?>/villes" class="btn btn-secondary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
        </div>
    </div>
</div>

<script nonce="<?php echo Flight::get('csp_nonce'); ?>">
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<?php include __DIR__ . '/../assets/inc/footer.php'; ?>