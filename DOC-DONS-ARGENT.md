# 💰 Gestion des Dons en Argent

## ✅ Configuration Actuelle

Le système est déjà bien configuré pour gérer correctement l'argent :

### 1. **L'argent N'EST PAS un article**
- ✅ L'argent est un **TYPE de don** (dans `type_don`)
- ✅ Il ne devrait PAS apparaître dans la table `articles`

### 2. **Fonctionnement du Formulaire**

Quand un utilisateur crée un don :

#### Pour un don en **NATURE ou MATÉRIEL** :
1. Sélectionne le type : "nature" ou "materiau"
2. Sélectionne un article (riz, tôles, etc.)
3. Saisit une **quantité**
4. Article stocké dans `dons` avec `id_article` et `quantite`

#### Pour un don en **ARGENT** :
1. Sélectionne le type : "argent"
2. Le système affiche automatiquement "💰 Don en argent"
3. Saisit un **montant** (en Ariary)
4. Stocké dans `dons` avec `id_article = NULL` et `montant_argent`

### 3. **Code JavaScript Actuel**

```javascript
if (selectedType.toLowerCase() === 'argent') {
    // Pas de liste d'articles, juste "Don en argent"
    options += '<option value="argent" data-type="Argent">💰 Don en argent</option>';
} else {
    // Affiche les articles filtrés par type
    articles.forEach(art => {
        if (art.libelle_type.toLowerCase() === selectedType.toLowerCase()) {
            options += `<option value="${art.id_article}">${art.nom_article}</option>`;
        }
    });
}
```

## 🧹 Nettoyage à Faire

Si vous avez accidentellement créé un article "Argent", supprimez-le :

```bash
# Exécuter le script de nettoyage
mysql -u root bngrc_dons < app/persistance/cleanup-argent.sql
```

Ou manuellement dans phpMyAdmin :
```sql
DELETE FROM articles WHERE LOWER(nom_article) LIKE '%argent%';
DELETE FROM articles WHERE id_type_besoin = 3;
```

## 📊 Structure de la Base de Données

### Table `type_don` (3 types)
```
id_type_don | libelle_type
------------|-------------
1           | nature
2           | materiau
3           | argent
```

### Table `articles` (PAS d'argent !)
```
id_article | nom_article    | id_type_besoin | prix_unitaire
-----------|----------------|----------------|---------------
1          | Riz            | 1 (nature)     | 2500
2          | Haricots       | 1 (nature)     | 4000
6          | Tôles ondulées | 2 (materiau)   | 55000
7          | Ciment         | 2 (materiau)   | 45000
```

### Table `dons`
```
-- Don en nature
id_don | id_type_don | id_article | quantite | montant_argent
-------|-------------|------------|----------|----------------
1      | 1 (nature)  | 1 (Riz)    | 100      | NULL

-- Don en argent
id_don | id_type_don | id_article | quantite | montant_argent
-------|-------------|------------|----------|----------------
2      | 3 (argent)  | NULL       | NULL     | 500000
```

## ✅ Résumé

✅ **Le formulaire fonctionne déjà correctement**
✅ **L'argent n'est pas un article mais un type de don**
✅ **Trois modes de dons supportés :**
   - 🌾 Dons en nature (riz, huile...) → Article + Quantité
   - 🏗️ Dons en matériel (tôles, ciment...) → Article + Quantité
   - 💰 Dons en argent → Montant uniquement

## 🧪 Test

1. Allez sur `/don/create`
2. Sélectionnez "argent" comme type
3. Vous devriez voir "💰 Don en argent" (pas de liste d'articles)
4. Saisissez un montant
5. Enregistrez

C'est tout ! Le système est déjà bien configuré. 🎉
