<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'ajout de don</title>
    <link rel="stylesheet" href="assets/css/formulaire-don.css">
</head>
<body>
    <h1>Formulaire d'ajout de don</h1>
    <form action="traitement_don.php" method="POST">
        <label for="type_don">Type de don:</label>
        <select id="type_don" name="type_don">
            <?php foreach ($typeDons as $typeDon): ?>
                <option value="<?= htmlspecialchars($typeDon) ?>"><?= htmlspecialchars($typeDon) ?></option>
            <?php endforeach; ?>
        </select>
        

        <label for="article">Article:</label>
        <select id="article" name="article">
            <?php foreach ($articles as $article): ?>
                <option value="<?= htmlspecialchars($article) ?>"><?= htmlspecialchars($article) ?></option>
            <?php endforeach; ?>
            <option value="argent">Argent</option>
        </select>
        

        <label for="quantite">Quantité:</label>
        <input type="number" id="quantite" name="quantite" step="0.01" required>
        

        <label for="montant">Montant (en cas de don en argent):</label>
        <input type="number" id="montant" name="montant" step="0.01">
        

        <input type="text" name="donateur_nom" id="donateur_nom" placeholder="Nom du donateur" required>
        
        <input type="email" name="donateur_email" id="donateur_email" placeholder="Email du donateur" required>
        

        <input type="submit" value="Ajouter le don">
    </form>
</body>
</html>