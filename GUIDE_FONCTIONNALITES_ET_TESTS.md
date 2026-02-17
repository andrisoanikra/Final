# 📋 GUIDE COMPLET - Système de Gestion des Dons BNGRC

## 🎯 Vue d'ensemble du système

Le système BNGRC gère les dons et besoins pour les sinistrés à Madagascar avec 3 versions évolutives.

---

## 📊 ÉVOLUTION DES VERSIONS

### ✅ VERSION 1 (V1) - Fonctionnalités de base

#### 1. **Gestion des Besoins**
- ✅ Créer un besoin pour une ville
- ✅ Besoin = 1 article (Riz, Huile, etc.)
- ✅ Champs : Ville, Article, Quantité, Prix unitaire, Urgence
- ✅ Calcul automatique du montant total

#### 2. **Gestion des Dons**
- ✅ Créer un don (Nature ou Argent)
- ✅ Don matériel : Article + Quantité
- ✅ Don en argent : Montant
- ✅ Informations donateur

#### 3. **Distribution Dispatcher (méthode manuelle)**
- ✅ Validation d'un don = Distribution automatique
- ✅ Ordre de priorité :
  1. Urgence (critique > urgente > normale)
  2. Date de saisie (plus ancien d'abord)
- ✅ Distribution équitable entre besoins compatibles

#### 4. **Tableau de bord**
- ✅ Statistiques globales
- ✅ Besoins par urgence
- ✅ Dons par statut
- ✅ Montants totaux

#### 5. **Visualisation**
- ✅ Liste des besoins avec progression
- ✅ Liste des dons avec statut
- ✅ Détails de chaque besoin/don

---

### ✅ VERSION 2 (V2) - Améliorations majeures

#### 1. **Besoins multi-articles**
- ✅ **NOUVEAU** : 1 besoin peut avoir plusieurs articles
- ✅ Exemple : Besoin Ambanja = Riz (10kg) + Huile (5L) + Tôle (20 pcs)
- ✅ Calcul automatique du montant total cumulé

#### 2. **Besoin en argent**
- ✅ **NOUVEAU** : Option "Argent" dans le formulaire besoin
- ✅ Demander de l'argent sans article spécifique
- ✅ Compatible avec dons en argent

#### 3. **Système d'achats automatiques**
- ✅ **NOUVEAU** : Convertir don argent → besoin matériel
- ✅ Besoin critique en Riz → Utiliser don argent pour acheter
- ✅ Simulation avant validation
- ✅ Montant utilisé = 100% du don disponible

#### 4. **Récapitulation dynamique (AJAX)**
- ✅ **NOUVEAU** : Page récapitulation avec mise à jour en temps réel
- ✅ Statistiques globales
- ✅ Par région
- ✅ Par type de besoin

#### 5. **Réinitialisation intelligente**
- ✅ **NOUVEAU** : Bouton "Réinitialiser"
- ✅ Garde les données initiales (dons et besoins)
- ✅ Supprime uniquement les distributions et achats
- ✅ Restaure les quantités disponibles

---

### ✅ VERSION 3 (V3) - Nouvelles méthodes de distribution

#### 1. **Choix de méthode à la validation**
- ✅ **NOUVEAU** : Page de sélection lors du clic "Valider"
- ✅ 3 méthodes au choix

#### 2. **Méthode 1 : Dispatcher (V1)**
- Ordre de priorité : Urgence → Date
- Distribution équitable
- Idéal pour respecter les priorités

#### 3. **Méthode 2 : Plus petit montant d'abord**
- ✅ **NOUVEAU** : Tri par montant croissant
- ✅ Satisfait d'abord les petits besoins
- ✅ Maximise le nombre de besoins satisfaits
- 📈 Exemple : 
  - Don 1000 Ar
  - Besoins : 200 Ar, 500 Ar, 800 Ar
  - Résultat : 200 satisfait ✅, 500 satisfait ✅, 800 partiel (300 Ar)

#### 4. **Méthode 3 : Distribution Proportionnelle**
- ✅ **NOUVEAU** : Méthode du reste le plus grand (Hamilton)
- ✅ Chaque besoin reçoit au prorata de sa demande
- 📐 Formule : `Part = (Demande / Total demandes) × Don`
- 📊 Exemple concret :
  ```
  Don : 5 kg de Riz
  Besoins : 1 kg, 3 kg, 5 kg (total = 9 kg)
  
  Calcul proportionnel :
  - Besoin A : 1×5/9 = 0.55 → arrondi = 0, décimale = 0.55
  - Besoin B : 3×5/9 = 1.66 → arrondi = 1, décimale = 0.66
  - Besoin C : 5×5/9 = 2.77 → arrondi = 2, décimale = 0.77
  
  Total distribué = 0+1+2 = 3
  Reste = 5-3 = 2
  
  Distribution du reste (2 plus grandes décimales) :
  - 0.77 (C) → +1
  - 0.66 (B) → +1
  
  RÉSULTAT FINAL :
  - Besoin A : 0 kg
  - Besoin B : 1+1 = 2 kg
  - Besoin C : 2+1 = 3 kg
  Total = 5 kg ✅
  ```

#### 5. **Suivi amélioré**
- ✅ Affichage de la méthode utilisée dans le message
- ✅ Détails par besoin avec quantité satisfaite
- ✅ Tableau détaillé dans la page besoin

---

## 🧪 SCÉNARIO DE TEST COMPLET - ÉTAPE PAR ÉTAPE

### 📌 Préparation initiale

**Objectif** : Tester toutes les fonctionnalités dans un ordre logique

#### Étape 0 : Réinitialisation
```
1. Aller sur http://localhost:8000
2. Cliquer sur "Réinitialiser" (sidebar, bouton orange)
3. Confirmer
4. ✅ Message : "Base de données réinitialisée"
```

---

### 📦 PARTIE 1 : GESTION DES BESOINS

#### Test 1.1 : Créer un besoin simple (V1)
```
Navigation : Sidebar → Besoins → Ajouter un besoin

Données :
- Ville : Antananarivo
- Description : "Besoin urgent pour 100 familles"
- Urgence : Critique

Article 1 :
- Type : Nature
- Article : Riz
- Quantité : 50
- Prix unitaire : 5000

Cliquer : "Ajouter un besoin"

✅ Résultat attendu :
- Redirection vers /besoins
- Message vert : "Besoin ajouté avec succès"
- Carte visible avec badge "Critique" rouge
- Montant total : 250 000 Ar
- Progression : 0%
```

#### Test 1.2 : Créer un besoin multi-articles (V2)
```
Navigation : Besoins → Ajouter un besoin

Données :
- Ville : Toamasina
- Description : "Reconstruction après cyclone"
- Urgence : Urgente

Article 1 (Nature) :
- Article : Riz
- Quantité : 20
- Prix unitaire : 5000

Cliquer : "+ Ajouter un autre article"

Article 2 (Matériau) :
- Article : Tôle
- Quantité : 30
- Prix unitaire : 15000

Cliquer : "Ajouter un besoin"

✅ Résultat attendu :
- Message : "Besoin ajouté avec succès"
- Carte affiche : "Riz, Tôle"
- Montant total : 100 000 + 450 000 = 550 000 Ar
```

#### Test 1.3 : Créer un besoin en argent (V2)
```
Navigation : Besoins → Ajouter un besoin

Données :
- Ville : Mahajanga
- Description : "Frais médicaux urgents"
- Urgence : Critique

Article 1 :
- Type : Argent
- Montant : 200000
- (Pas d'article ni quantité)

Cliquer : "Ajouter un besoin"

✅ Résultat attendu :
- Message : "Besoin ajouté avec succès"
- Carte affiche : "💰 Argent"
- Montant : 200 000 Ar
```

#### Vérification 1 : Liste des besoins
```
Navigation : Besoins

✅ Affichage attendu :
┌─────────────────────────────────────────────┐
│ ANTANANARIVO                                │
│ Riz                                         │
│ 250 000 Ar | 🔴 Critique | ⚪ En cours     │
│ Progression : ████░░░░░░ 0%                │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ TOAMASINA                                   │
│ Riz, Tôle                                   │
│ 550 000 Ar | 🟡 Urgente | ⚪ En cours     │
│ Progression : ████░░░░░░ 0%                │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ MAHAJANGA                                   │
│ 💰 Argent                                   │
│ 200 000 Ar | 🔴 Critique | ⚪ En cours     │
│ Progression : ████░░░░░░ 0%                │
└─────────────────────────────────────────────┘
```

---

### 🎁 PARTIE 2 : GESTION DES DONS

#### Test 2.1 : Don matériel simple
```
Navigation : Sidebar → Dons → Ajouter un don

Données :
- Type de don : Nature
- Article : Riz
- Quantité : 100
- Description : "Don d'une ONG internationale"
- Donateur : "Croix Rouge"
- Contact : "0340000001"

Cliquer : "Enregistrer le don"

✅ Résultat attendu :
- Redirection vers /dons
- Message : "Don ajouté avec succès"
- Badge vert "Disponible"
- Quantité : 100
```

#### Test 2.2 : Don en argent
```
Navigation : Dons → Ajouter un don

Données :
- Type de don : Argent
- Montant : 500000
- Description : "Contribution d'une entreprise locale"
- Donateur : "Société ABC"
- Contact : "0340000002"

Cliquer : "Enregistrer le don"

✅ Résultat attendu :
- Message : "Don ajouté avec succès"
- Type : "Don en argent"
- Montant : 500 000 Ar
- Badge vert "Disponible"
```

#### Test 2.3 : Don matériel (Tôle)
```
Navigation : Dons → Ajouter un don

Données :
- Type : Matériau
- Article : Tôle
- Quantité : 50
- Donateur : "Quincaillerie XYZ"
- Contact : "0340000003"

Cliquer : "Enregistrer le don"

✅ Résultat attendu :
- Message : "Don ajouté avec succès"
- Article : Tôle
- Quantité : 50
```

#### Vérification 2 : Liste des dons
```
Navigation : Dons

✅ Affichage attendu :
┌─────────────────────────────────────────────┐
│ 🟢 Disponible                               │
│ Riz | 100 unités                            │
│ Donateur : Croix Rouge                      │
│ [Voir] [Valider] [Supprimer]               │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 🟢 Disponible                               │
│ Don en argent | 500 000 Ar                  │
│ Donateur : Société ABC                      │
│ [Voir] [Valider] [Supprimer]               │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 🟢 Disponible                               │
│ Tôle | 50 unités                            │
│ Donateur : Quincaillerie XYZ                │
│ [Voir] [Valider] [Supprimer]               │
└─────────────────────────────────────────────┘
```

---

### 🎯 PARTIE 3 : DISTRIBUTION MÉTHODE 1 (DISPATCHER)

#### Test 3.1 : Distribution Dispatcher du don Riz
```
Navigation : Dons

1. Trouver le don "Riz - 100 unités"
2. Cliquer sur [Valider]
3. Page de choix s'affiche avec 3 cartes

✅ Affichage attendu :
┌───────────────────────────────────┐
│ 🔵 Distribution Dispatcher        │
│ - Priorise besoins urgents        │
│ - Par ordre chronologique         │
│ [Utiliser cette méthode]          │
└───────────────────────────────────┘

┌───────────────────────────────────┐
│ 🟢 Plus petit montant d'abord     │
│ - Trie par montant croissant     │
│ - Satisfait petits besoins        │
│ [Utiliser cette méthode]          │
└───────────────────────────────────┘

┌───────────────────────────────────┐
│ 🟡 Distribution Proportionnelle   │
│ - Calcul proportionnel            │
│ - Méthode du reste plus grand     │
│ [Utiliser cette méthode]          │
└───────────────────────────────────┘

4. Cliquer sur "Utiliser cette méthode" (carte bleue Dispatcher)
5. Confirmer

✅ Résultat attendu :
- Message : "Don validé et dispatché avec succès ! X unités affectées"
- Don passe en statut "Affecté" ou "Partiel"
- Besoins en Riz passent en "Partiel" ou "Satisfait"
```

#### Vérification 3.1 : Besoins après distribution Dispatcher
```
Navigation : Besoins

✅ Progression attendue (ordre critique → urgent) :

ANTANANARIVO (Critique, Riz 50kg)
- Avant : 0/250 000 (0%)
- Après : 50kg distribués = 250 000/250 000 (100%)
- Statut : 🟢 Satisfait

TOAMASINA (Urgent, Riz 20kg + Tôle 30)
- Avant : 0/550 000 (0%)
- Après : 20kg Riz distribués = 100 000/550 000 (18%)
- Statut : 🟡 Partiel

RESTE DU DON :
- Don Riz : 100 - 50 - 20 = 30 kg restants
- Statut don : 🟡 Partiel
```

---

### 💰 PARTIE 4 : ACHATS AUTOMATIQUES (V2)

#### Test 4.1 : Achat automatique pour besoin critique
```
Navigation : Sidebar → Besoins → Besoins critiques (Matériel/Nature)

1. Identifier le besoin critique
2. Cliquer sur [Créer un achat]

✅ Page achat s'affiche :
- Don disponible : 500 000 Ar (Société ABC)
- Montant utilisé : 500 000 Ar (100% du don)
- AUCUN champ article/quantité (automatique)

3. Cliquer sur "Simuler l'achat"

✅ Résultat attendu :
- Redirection vers /achats/simulation
- Ligne visible avec statut "Simulé"
- Montant : 500 000 Ar
```

#### Test 4.2 : Validation achat
```
Navigation : Achats et simulation

1. Trouver l'achat simulé
2. Cliquer sur [Valider]

✅ Résultat attendu :
- Statut passe de "Simulé" à "Validé"
- Don argent passe en "Affecté"
- Besoin reçoit 500 000 Ar
- Progression mise à jour
```

---

### 📊 PARTIE 5 : DISTRIBUTION PLUS PETIT MONTANT

#### Test 5.1 : Créer 3 nouveaux besoins de tailles différentes
```
BESOIN A (Petit) :
- Ville : Antsirabe
- Urgence : Normale
- Article : Riz, Qté : 5, Prix : 5000
- Montant total : 25 000 Ar

BESOIN B (Moyen) :
- Ville : Fianarantsoa
- Urgence : Normale
- Article : Riz, Qté : 15, Prix : 5000
- Montant total : 75 000 Ar

BESOIN C (Grand) :
- Ville : Tuléar
- Urgence : Normale
- Article : Riz, Qté : 30, Prix : 5000
- Montant total : 150 000 Ar
```

#### Test 5.2 : Don Riz pour test proportionnel
```
Créer un don :
- Type : Nature
- Article : Riz
- Quantité : 40
- Donateur : "Test Plus Petit"
```

#### Test 5.3 : Distribution Plus Petit Montant
```
1. Aller sur /dons
2. Cliquer [Valider] sur le don "Test Plus Petit"
3. Choisir la carte 🟢 "Plus petit montant d'abord"
4. Confirmer

✅ Résultat attendu :
ORDRE DE DISTRIBUTION (du plus petit au plus grand) :

1. BESOIN A (25 000 Ar, 5kg)
   - Reçoit : 5 kg
   - Statut : 🟢 Satisfait ✅
   - Reste don : 40 - 5 = 35 kg

2. BESOIN B (75 000 Ar, 15kg)
   - Reçoit : 15 kg
   - Statut : 🟢 Satisfait ✅
   - Reste don : 35 - 15 = 20 kg

3. BESOIN C (150 000 Ar, 30kg)
   - Reçoit : 20 kg (tout le reste)
   - Statut : 🟡 Partiel (20/30)
   - Reste don : 0 kg

Message : "3 besoin(s) satisfait(s). 40 unités distribuées"
```

---

### 🎲 PARTIE 6 : DISTRIBUTION PROPORTIONNELLE

#### Test 6.1 : Scénario exact de l'exemple
```
Créer 3 besoins identiques (même urgence) :

BESOIN 1 :
- Ville : Ville A
- Urgence : Normale
- Article : Riz, Qté : 1, Prix : 5000
- Montant : 5 000 Ar

BESOIN 2 :
- Ville : Ville B
- Urgence : Normale
- Article : Riz, Qté : 3, Prix : 5000
- Montant : 15 000 Ar

BESOIN 3 :
- Ville : Ville C
- Urgence : Normale
- Article : Riz, Qté : 5, Prix : 5000
- Montant : 25 000 Ar

TOTAL DEMANDES : 1 + 3 + 5 = 9 kg
```

#### Test 6.2 : Don pour distribution proportionnelle
```
Créer un don :
- Type : Nature
- Article : Riz
- Quantité : 5
- Donateur : "Test Proportionnel"
```

#### Test 6.3 : Appliquer distribution proportionnelle
```
1. Dons → [Valider] don "Test Proportionnel"
2. Choisir 🟡 "Distribution Proportionnelle"
3. Confirmer

✅ CALCUL AUTOMATIQUE :

BESOIN 1 (1 kg demandé) :
- Proportionnel : 1×5/9 = 0.555...
- Arrondi inférieur : 0
- Décimale : 0.555

BESOIN 2 (3 kg demandés) :
- Proportionnel : 3×5/9 = 1.666...
- Arrondi inférieur : 1
- Décimale : 0.666

BESOIN 3 (5 kg demandés) :
- Proportionnel : 5×5/9 = 2.777...
- Arrondi inférieur : 2
- Décimale : 0.777 ← Plus grande

Total distribué : 0 + 1 + 2 = 3 kg
Reste : 5 - 3 = 2 kg

DISTRIBUTION DU RESTE (2 plus grandes décimales) :
- 0.777 (BESOIN 3) → +1
- 0.666 (BESOIN 2) → +1

RÉSULTAT FINAL :
- BESOIN 1 : 0 + 0 = 0 kg
- BESOIN 2 : 1 + 1 = 2 kg ✅
- BESOIN 3 : 2 + 1 = 3 kg ✅
TOTAL = 5 kg ✅

Message : "3 besoin(s) ont reçu une part. 5 unités distribuées"
```

---

### 📈 PARTIE 7 : VÉRIFICATION TABLEAU DE BORD

#### Test 7.1 : Tableau de bord global
```
Navigation : Sidebar → Tableau de bord

✅ Affichage attendu :
┌──────────────────────────────────────┐
│ STATISTIQUES GLOBALES                │
├──────────────────────────────────────┤
│ Total besoins : X                    │
│ Besoins satisfaits : Y               │
│ Besoins en cours : Z                 │
│                                      │
│ Total dons : N                       │
│ Dons disponibles : M                 │
│ Dons affectés : L                    │
│                                      │
│ Montant total besoins : XXX XXX Ar   │
│ Montant total reçu : YYY YYY Ar      │
└──────────────────────────────────────┘

Graphiques :
- 📊 Besoins par urgence (camembert)
- 📊 Dons par type (barres)
- 📊 Progression par ville (barres)
```

#### Test 7.2 : Récapitulation dynamique
```
Navigation : Sidebar → Récapitulation

✅ Affichage temps réel (AJAX) :
- Statistiques globales
- Par région (accordéon)
- Par type de besoin (accordéon)
- Mise à jour automatique sans recharger
```

---

### 🔄 PARTIE 8 : RÉINITIALISATION

#### Test 8.1 : Réinitialiser les distributions
```
1. Sidebar → Réinitialiser (bouton orange)
2. Page de confirmation
3. Cliquer "Confirmer la réinitialisation"

✅ Résultat attendu :
- Message : "Base réinitialisée avec succès"
- TOUS les dons repassent en "Disponible"
- TOUS les besoins repassent en "En cours"
- Progressions remises à 0%
- Achats supprimés
- dispatch_dons vidé
- MAIS données de base conservées ✅
```

---

## 📋 RÉSUMÉ DES POINTS DE VÉRIFICATION

### ✅ Checklist complète de test

**Besoins :**
- [ ] Besoin simple 1 article (V1)
- [ ] Besoin multi-articles (V2)
- [ ] Besoin en argent (V2)
- [ ] Affichage progression
- [ ] Page détails besoin
- [ ] Tableau articles dans détails

**Dons :**
- [ ] Don matériel
- [ ] Don en argent
- [ ] Affichage badges statut
- [ ] Page détails don

**Distribution Dispatcher :**
- [ ] Respect ordre urgence
- [ ] Distribution équitable
- [ ] Message de confirmation
- [ ] Mise à jour statuts

**Distribution Plus Petit :**
- [ ] Tri par montant croissant
- [ ] Satisfaction besoins petits d'abord
- [ ] Statuts mis à jour

**Distribution Proportionnelle :**
- [ ] Calcul proportionnel correct
- [ ] Arrondi inférieur
- [ ] Distribution reste (décimales)
- [ ] Total = don exact

**Achats :**
- [ ] Simulation automatique
- [ ] Validation
- [ ] Montant 100% du don
- [ ] Statuts mis à jour

**Tableau de bord :**
- [ ] Statistiques correctes
- [ ] Graphiques visibles
- [ ] Récapitulation AJAX

**Réinitialisation :**
- [ ] Dons → Disponible
- [ ] Besoins → En cours
- [ ] Distributions effacées
- [ ] Données conservées

---

## 🎓 CONCLUSION

### Évolution du système

**V1** : Base solide
- Gestion dons/besoins simple
- 1 méthode distribution

**V2** : Flexibilité
- Multi-articles
- Argent
- Achats automatiques
- Réinitialisation intelligente

**V3** : Intelligence
- 3 méthodes de distribution
- Choix utilisateur
- Algorithmes optimisés

### Points forts du système

1. **Flexibilité** : Gère tous types de situations
2. **Traçabilité** : Historique complet
3. **Équité** : 3 méthodes selon contexte
4. **Automatisation** : Calculs et distributions
5. **Réversibilité** : Réinitialisation propre

### Cas d'usage recommandés

**Dispatcher** → Urgences avec priorités claires
**Plus petit** → Maximiser satisfaction rapide
**Proportionnel** → Distribution équitable mathématique

---

📞 **Support** : Consultez ce guide pour tout test
📊 **Statistiques** : Tableau de bord en temps réel
🔄 **Reset** : Réinitialisez à tout moment sans perte de données
