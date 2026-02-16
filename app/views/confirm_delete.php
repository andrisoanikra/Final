<?php
/**
 * Page de confirmation de suppression
 */
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card card-danger">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title">Confirmation de suppression</h3>
                </div>
                <div class="card-body">
                    <p class="text-danger">
                        <strong>Attention!</strong> Êtes-vous sûr de vouloir supprimer cet élément?
                    </p>
                    <div class="alert alert-warning" role="alert">
                        <strong><?php echo htmlspecialchars($label); ?></strong>
                    </div>
                    <?php if (!empty($details)): ?>
                        <div class="card card-light mb-3">
                            <div class="card-body">
                                <pre><?php echo htmlspecialchars(json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <form method="POST" action="/<?php echo $entity; ?>/<?php echo $id; ?>/delete" style="display: inline;">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Oui, supprimer
                        </button>
                    </form>
                    <a href="<?php echo htmlspecialchars($back); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
