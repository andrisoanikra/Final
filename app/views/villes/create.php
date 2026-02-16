<?php
/**
 * Formulaire de création d'une ville
 */
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Ajouter une nouvelle ville</h1>
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

            <form method="POST" action="/ville/create" class="needs-validation">
                <div class="form-group mb-3">
                    <label for="id_region" class="form-label">Région <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_region" name="id_region" required>
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
                    <label for="nom_ville" class="form-label">Nom de la ville <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nom_ville" name="nom_ville" 
                        placeholder="Nom de la ville" required
                        value="<?php echo isset($old['nom_ville']) ? htmlspecialchars($old['nom_ville']) : ''; ?>">
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description de la ville"><?php echo isset($old['description']) ? htmlspecialchars($old['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Ajouter la ville</button>
                    <a href="/villes" class="btn btn-secondary">Annuler</a>
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
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
