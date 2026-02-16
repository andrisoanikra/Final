# Système de Dispatch des Dons - BNGRC

## 📋 Vue d'ensemble

Le système de dispatch automatique distribue intelligemment les dons vers les besoins en fonction de l'urgence et de la date de saisie.

## 🎯 Principe de fonctionnement

### Exemple concret

**Besoins :**
- Ville A : 100 kg de riz (prix unitaire : 500 Ar/kg) → besoin total = 50 000 Ar
- Ville B : 50 tôles (prix unitaire : 10 000 Ar/tôle) → besoin total = 500 000 Ar

**Dons reçus (par ordre de date) :**
1. Don de 200 000 Ar (argent)
2. Don de 50 kg de riz (nature)
3. Don de 20 tôles (matériaux)

**Dispatch automatique :**
1. **200 000 Ar** → Couvre d'abord le riz (50 000 Ar) puis une partie des tôles (150 000 Ar = 15 tôles)
2. **50 kg de riz** → S'ajoute au riz déjà partiellement couvert
3. **20 tôles** → S'ajoute aux tôles déjà partiellement couvertes

## 🔄 Processus de dispatch

### 1. Pour les dons en ARGENT

```sql
Ordre de priorité :
1. Urgence (critique > urgente > normale)
2. Date de saisie (plus ancien en premier)
```

**Fonctionnement :**
- L'argent est distribué progressivement aux besoins
- Couvre d'abord complètement le premier besoin
- Passe au suivant avec le montant restant
- Continue jusqu'à épuisement du don ou des besoins

**Exclusions :**
- Les villes dont TOUS les besoins sont satisfaits ne reçoivent PLUS de dons

### 2. Pour les dons MATÉRIELS (nature/matériaux)

```sql
Ordre de priorité :
1. Même type d'article (riz → besoins de riz, tôles → besoins de tôles)
2. Urgence (critique > urgente > normale)
3. Date de saisie (plus ancien en premier)
```

**Fonctionnement :**
- Les articles sont distribués uniquement aux besoins correspondants
- S'ajoute aux quantités déjà reçues
- Continue jusqu'à épuisement de la quantité disponible

**Exclusions :**
- Les villes dont TOUS les besoins sont satisfaits ne reçoivent PLUS de dons

## 🎉 Messages de félicitation

### Quand une ville a tous ses besoins couverts :

1. **Lors du dispatch :**
   - Message de félicitation automatique dans l'alerte de succès
   - Exemple : "🎉 FÉLICITATIONS ! Tous les besoins de cette ville sont maintenant couverts : Antananarivo !"

2. **Sur la page des villes :**
   - Section spéciale en haut : alerte verte avec liste des villes satisfaites
   - Section en bas : liste dédiée avec fond vert et badge "✓ Tous les besoins couverts"

3. **Comportement :**
   - Ces villes n'apparaissent plus dans le système de dispatch
   - Elles ne reçoivent plus de nouveaux dons automatiquement
   - Permet de concentrer les ressources sur les villes encore en besoin

## 📊 Suivi de l'évolution

### Indicateurs de progression

Chaque besoin affiche :
- **Barre de progression** avec couleur :
  - 🟢 Vert : 100% (complètement couvert)
  - 🟠 Orange : 50-99% (partiellement couvert)
  - 🔴 Rouge : 1-49% (faiblement couvert)
  - ⚪ Gris : 0% (aucun don reçu)

- **Montants :**
  - Montant reçu : Somme de tous les dons dispatchés
  - Montant total besoin : Montant nécessaire
  - Reste à couvrir : Différence entre besoin et reçu

### Calcul du montant reçu

```sql
montant_recu = 
  -- Dons en argent
  SUM(dispatch_dons.montant_affecte) 
  + 
  -- Dons matériels (quantité × prix)
  SUM(dispatch_dons.quantite_affectee × article.prix_unitaire)
```

## 🚀 Comment dispatcher un don

### Étape par étape :

1. **Aller sur la page "Dons"** (`/dons`)

2. **Repérer les dons avec statut "Disponible"**
   - Badge bleu "Disponible"
   - Message d'information en haut de la page

3. **Cliquer sur "Valider"** pour chaque don
   - Le système dispatche automatiquement
   - Affiche un message de confirmation
   - Indique les montants/quantités affectés
   - Affiche les félicitations si une ville est entièrement couverte

4. **Vérifier l'évolution**
   - Les barres de progression se mettent à jour automatiquement
   - Visibles sur toutes les pages de besoins

## 🔍 Statuts des besoins

| Statut | Description | Quand ? |
|--------|-------------|---------|
| `en_cours` | Besoin actif sans don | Montant reçu = 0 Ar |
| `partiel` | Besoin partiellement couvert | 0 < Montant reçu < Montant total |
| `satisfait` | Besoin complètement couvert | Montant reçu ≥ Montant total |

## 🔍 Statuts des dons

| Statut | Description | Quand ? |
|--------|-------------|---------|
| `disponible` | Don non encore dispatché | État initial |
| `affecte` | Don partiellement utilisé | Reste encore une partie |
| `utilise` | Don complètement dispatché | Plus rien à distribuer |

## 📝 Notes importantes

1. **Les dons sont dispatchés dans l'ordre de réception** (date_don)
2. **L'urgence prime sur la date** (critique → urgente → normale)
3. **Les villes satisfaites sont exclues** pour éviter la sur-distribution
4. **Un don peut couvrir plusieurs besoins** (si montant/quantité suffisant)
5. **Un besoin peut être couvert par plusieurs dons** (accumulation progressive)

## 🛠️ Fichiers techniques

- **DonsModel.php** : Logique de dispatch (`dispatcherDon()`, `dispatcherDonArgent()`, `dispatcherDonMateriel()`)
- **DonsController.php** : Endpoint `validerDon()` qui déclenche le dispatch
- **BesoinsModel.php** : Requêtes SQL avec calculs de `montant_total` et `montant_recu`
- **Table dispatch_dons** : Historique des distributions (id_don, id_besoin, montant/quantité affectée, date)

---

**Date de mise à jour :** 16 février 2026  
**Version :** 2.0
