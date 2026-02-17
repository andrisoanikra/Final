# Ajout de l'option argent dans le formulaire de besoin

## Date: 16 février 2026

## Modifications effectuées

### 1. Frontend - Formulaire de création de besoin

**Fichier modifié:** `app/views/besoins/create.php`

#### Changements:
- Ajout de l'option "💰 Besoin en argent" dans la liste déroulante des articles
- Affichage conditionnel entre:
  - **Article physique:** Champs Quantité + Prix unitaire
  - **Argent:** Champ Montant (Ar)
- Fonction JavaScript `handleArticleChange()` mise à jour pour basculer entre les deux modes

```html
<option value="argent">💰 Besoin en argent</option>
```

### 2. Backend - Contrôleur BesoinsController

**Fichier modifié:** `app/controllers/BesoinsController.php`

#### Changements dans `storeBesoin()`:
- Validation adaptée pour accepter soit:
  - Un article avec quantité + prix_unitaire
  - Un besoin en argent avec montant
- Pour l'argent:
  - `id_article` = NULL
  - `quantite` = 1 (fictif)
  - `prix_unitaire` = montant saisi

```php
// Si c'est un besoin en argent
if ($id_article === 'argent') {
    $articlesValides[] = [
        'id_article' => null,
        'quantite' => 1,
        'prix_unitaire' => $montant,
        'is_argent' => true
    ];
}
```

### 3. Modèle BesoinsModel

**Fichier modifié:** `app/models/BesoinsModel.php`

#### Méthodes mises à jour:

1. **getArticlesDuBesoin()**: LEFT JOIN au lieu de JOIN pour gérer id_article NULL
2. **getBesoins()**: Affiche "💰 Argent" quand id_article IS NULL
3. **getBesoinsByVille()**: Idem
4. **getBesoinsNonSatisfaits()**: Idem

```sql
SELECT ba.*, 
CASE 
    WHEN ba.id_article IS NULL THEN '💰 Argent'
    ELSE a.nom_article
END as nom_article
FROM besoin_articles ba
LEFT JOIN articles a ON ba.id_article = a.id_article
```

### 4. Structure de base de données

**Fichier modifié:** `app/persistance/2026-02-16-base.sql`

#### Modification de la table besoin_articles:
```sql
id_article INT NULL,  -- NULL pour besoin en argent
```

**Fichier créé:** `app/persistance/alter-besoin-articles-argent.sql`
- Script ALTER TABLE pour modifier la base existante

## Utilisation

### Créer un besoin avec article physique:
1. Sélectionner une ville
2. Choisir un article dans la liste (ex: Riz, Tôles)
3. Saisir la quantité
4. Le prix unitaire se remplit automatiquement

### Créer un besoin en argent:
1. Sélectionner une ville
2. Choisir "💰 Besoin en argent"
3. Saisir le montant demandé en Ariary

### Besoin mixte:
Un même besoin peut contenir plusieurs lignes:
- Ligne 1: 100 sacs de riz
- Ligne 2: 50 000 Ar d'argent
- Ligne 3: 200 tôles

## Stockage en base de données

### Pour un article physique:
```
id_article: 5 (ex: Riz)
quantite: 100
prix_unitaire: 5000
```

### Pour l'argent:
```
id_article: NULL
quantite: 1
prix_unitaire: 50000 (= montant demandé)
```

## Affichage

Dans toutes les listes et détails de besoins:
- Article physique: Affiche le nom de l'article (ex: "Riz")
- Besoin en argent: Affiche "💰 Argent"

## Cohérence avec les dons

Cette implémentation est cohérente avec le système de dons où:
- Don d'article: `id_article` = ID de l'article, `quantite` renseignée
- Don d'argent: `id_article` = NULL, `montant_argent` renseigné

## Notes importantes

- La méthode `addArticleToBesoin()` accepte déjà `id_article` NULL (aucune modification nécessaire)
- Les requêtes SQL utilisent LEFT JOIN pour ne pas exclure les besoins en argent
- Les calculs de montant_total fonctionnent car: `quantite * prix_unitaire = 1 * montant`
