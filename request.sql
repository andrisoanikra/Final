/* =========================================================
   BASE : livraisons
   FICHIER : requete.sql
   OBJECTIF : requêtes SQL pour les Models MVC
========================================================= */


/* =========================================================
   ===================== CHAUFFEUR =========================
========================================================= */

/* Récupérer tous les chauffeurs */
SELECT * FROM chauffeur;

/* Récupérer un chauffeur par ID */
SELECT * FROM chauffeur
WHERE id = ?;

/* Récupérer le salaire d’un chauffeur */
SELECT salaire_journalier
FROM chauffeur
WHERE id = ?;

/* Nombre de livraisons par chauffeur */
SELECT 
    c.id,
    c.nom,
    COUNT(l.id) AS total_livraisons
FROM chauffeur c
LEFT JOIN livraison l ON c.id = l.chauffeur_id
GROUP BY c.id, c.nom;



/* =========================================================
   ===================== VEHICULE ==========================
========================================================= */

/* Récupérer tous les véhicules */
SELECT * FROM vehicule;

/* Récupérer un véhicule par ID */
SELECT * FROM vehicule
WHERE id = ?;

/* Récupérer le coût journalier d’un véhicule */
SELECT cout_journalier
FROM vehicule
WHERE id = ?;



/* =========================================================
   ======================= COLIS ===========================
========================================================= */

/* Récupérer tous les colis */
SELECT * FROM colis;

/* Récupérer un colis par ID */
SELECT * FROM colis
WHERE id = ?;

/* Montant gagné par kg (poids × prix/kg) */
SELECT 
    id,
    poids_kg,
    prix_par_kg,
    (poids_kg * prix_par_kg) AS montant_gagne
FROM colis
WHERE id = ?;



/* =========================================================
   ===================== LIVRAISON =========================
========================================================= */

/* Créer une livraison */
INSERT INTO livraison (
    colis_id,
    chauffeur_id,
    vehicule_id,
    adresse_depart,
    adresse_destination,
    statut,
    prix_facture,
    date_livraison
) VALUES (?, ?, ?, ?, ?, 'en attente', ?, ?);

/* Afficher toutes les livraisons avec détails */
SELECT 
    l.id,
    c.nom AS chauffeur,
    v.immatriculation AS vehicule,
    co.poids_kg,
    co.prix_par_kg,
    l.prix_facture,
    l.adresse_depart,
    l.adresse_destination,
    l.statut,
    l.date_livraison
FROM livraison l
JOIN chauffeur c ON l.chauffeur_id = c.id
JOIN vehicule v ON l.vehicule_id = v.id
JOIN colis co ON l.colis_id = co.id;

/* Récupérer les livraisons par statut */
SELECT *
FROM livraison
WHERE statut = ?;

/* Modifier le statut d’une livraison */
UPDATE livraison
SET statut = ?
WHERE id = ?;

/* Coût de revient par livraison */
SELECT 
    l.id,
    (c.salaire_journalier + v.cout_journalier) AS cout_revient
FROM livraison l
JOIN chauffeur c ON l.chauffeur_id = c.id
JOIN vehicule v ON l.vehicule_id = v.id
WHERE l.id = ?;

/* Chiffre d’affaire total */
SELECT 
    SUM(prix_facture) AS chiffre_affaire
FROM livraison
WHERE statut = 'livré';

/* Bénéfice par livraison */
SELECT 
    l.id,
    l.prix_facture,
    (c.salaire_journalier + v.cout_journalier) AS cout_revient,
    (l.prix_facture - (c.salaire_journalier + v.cout_journalier)) AS benefice
FROM livraison l
JOIN chauffeur c ON l.chauffeur_id = c.id
JOIN vehicule v ON l.vehicule_id = v.id
WHERE l.statut = 'livré';



/* =========================================================
   =================== STATISTIQUES ========================
========================================================= */

/* Bénéfice par jour */
SELECT 
    l.date_livraison,
    SUM(l.prix_facture - (c.salaire_journalier + v.cout_journalier)) AS benefice_jour
FROM livraison l
JOIN chauffeur c ON l.chauffeur_id = c.id
JOIN vehicule v ON l.vehicule_id = v.id
WHERE l.statut = 'livré'
GROUP BY l.date_livraison;

/* Bénéfice par mois */
SELECT 
    YEAR(l.date_livraison) AS annee,
    MONTH(l.date_livraison) AS mois,
    SUM(l.prix_facture - (c.salaire_journalier + v.cout_journalier)) AS benefice_mois
FROM livraison l
JOIN chauffeur c ON l.chauffeur_id = c.id
JOIN vehicule v ON l.vehicule_id = v.id
WHERE l.statut = 'livré'
GROUP BY YEAR(l.date_livraison), MONTH(l.date_livraison);

/* Bénéfice par année */
SELECT 
    YEAR(l.date_livraison) AS annee,
    SUM(l.prix_facture - (c.salaire_journalier + v.cout_journalier)) AS benefice_annee
FROM livraison l
JOIN chauffeur c ON l.chauffeur_id = c.id
JOIN vehicule v ON l.vehicule_id = v.id
WHERE l.statut = 'livré'
GROUP BY YEAR(l.date_livraison);

/* =========================================================
   ======================= ZONE ============================
========================================================= */

/* Créer la table des zones (pourcentage appliqué au prix) */
CREATE TABLE IF NOT EXISTS lv_zone (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    pourcentage DECIMAL(5,2) NOT NULL DEFAULT 0
);


/* Récupérer toutes les zones */
SELECT id, nom, pourcentage FROM lv_zone ORDER BY id;

/* Récupérer une zone par id */
SELECT id, nom, pourcentage FROM lv_zone WHERE id = ?;

/* Ajouter zone_id sur livraison si nécessaire (migration)
   NOTE: adaptez le nom de la table si votre schéma utilise un préfixe (ex: lv_livraison)
*/
ALTER TABLE lv_livraison ADD COLUMN IF NOT EXISTS zone_id INT NULL;
ALTER TABLE lv_livraison ADD CONSTRAINT IF NOT EXISTS fk_liv_zone FOREIGN KEY (zone_id) REFERENCES lv_zone(id) ON DELETE SET NULL;
