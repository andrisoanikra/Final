<?php

namespace app\controllers;

use Flight;
use app\models\DonsModel;
use app\models\TypeDonModel;
use app\models\ArticlesModel;
use app\models\BesoinsModel;

class DonsController
{
    protected $db;
    protected $donsModel;
    protected $typeDonModel;
    protected $articlesModel;

    public function __construct()
    {
        $this->db = Flight::db();
        $this->donsModel = new DonsModel($this->db);
        $this->typeDonModel = new TypeDonModel($this->db);
        $this->articlesModel = new ArticlesModel($this->db);
    }

    /**
     * Affiche le formulaire d'ajout de don
     */
    public function newDonForm()
    {
        $typeDons = $this->typeDonModel->getAllTypes();
        $articles = $this->articlesModel->getAllArticles();
        
        Flight::render('formulaire-don', [
            'typeDons' => $typeDons,
            'articles' => $articles
        ]);
    }

    /**
     * Enregistre un nouveau don (ou plusieurs dons)
     */
    public function storeDon()
    {
        $donateur_nom = $_POST['donateur_nom'] ?? null;
        $donateur_contact = $_POST['donateur_contact'] ?? null;
        $description = $_POST['description'] ?? null;
        $dons = $_POST['dons'] ?? [];

        // Validation
        $errors = [];
        
        if (empty($donateur_nom)) {
            $errors[] = 'Veuillez entrer le nom du donateur';
        }

        if (empty($donateur_contact)) {
            $errors[] = 'Veuillez entrer le contact du donateur';
        }

        if (empty($dons) || !is_array($dons)) {
            $errors[] = 'Veuillez ajouter au moins un don';
        }

        // Valider chaque don
        $donsValides = [];
        foreach ($dons as $index => $don) {
            $id_type_don = $don['type_don'] ?? null;
            $article = $don['article'] ?? null;
            $quantite = $don['quantite'] ?? null;
            $montant = $don['montant'] ?? null;

            if (empty($id_type_don)) {
                $errors[] = "Don #" . ($index + 1) . " : Veuillez sélectionner un type";
                continue;
            }

            if (empty($article)) {
                $errors[] = "Don #" . ($index + 1) . " : Veuillez sélectionner un article";
                continue;
            }

            // Si c'est un don en argent
            if ($article === 'argent') {
                $id_article = null;
                $quantite_finale = null;
                
                if (empty($montant) || $montant <= 0) {
                    $errors[] = "Don #" . ($index + 1) . " : Le montant doit être supérieur à 0";
                    continue;
                }
                $montant_final = $montant;
            } else {
                // Don en nature ou matériel
                $id_article = $article;
                $montant_final = null;
                
                if (empty($quantite) || $quantite <= 0) {
                    $errors[] = "Don #" . ($index + 1) . " : La quantité doit être supérieure à 0";
                    continue;
                }
                $quantite_finale = $quantite;
            }

            $donsValides[] = [
                'id_type_don' => $id_type_don,
                'id_article' => $id_article,
                'quantite' => $quantite_finale,
                'montant' => $montant_final
            ];
        }

        if (!empty($errors)) {
            Flight::render('formulaire-don', [
                'typeDons' => $this->typeDonModel->getAllTypes(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
                    'description' => $description,
                    'donateur_nom' => $donateur_nom,
                    'donateur_contact' => $donateur_contact
                ]
            ]);
            return;
        }

        // Créer tous les dons
        $nbDonsAjoutes = 0;
        foreach ($donsValides as $don) {
            $donId = $this->donsModel->addDon(
                $don['id_type_don'],
                $don['id_article'],
                $description,
                $don['quantite'],
                $don['montant'],
                $donateur_nom,
                $donateur_contact
            );

            if ($donId) {
                $nbDonsAjoutes++;
            }
        }

        if ($nbDonsAjoutes > 0) {
            $message = $nbDonsAjoutes > 1 
                ? "$nbDonsAjoutes dons ajoutés avec succès !" 
                : "Don ajouté avec succès !";
            Flight::redirect('/dons?success=don_ajoute&message=' . urlencode($message));
        } else {
            $errors[] = 'Erreur lors de l\'ajout des dons';
            Flight::render('formulaire-don', [
                'typeDons' => $this->typeDonModel->getAllTypes(),
                'articles' => $this->articlesModel->getAllArticles(),
                'errors' => $errors,
                'old' => [
                    'description' => $description,
                    'donateur_nom' => $donateur_nom,
                    'donateur_contact' => $donateur_contact
                ]
            ]);
        }
    }

    /**
     * Affiche la liste des dons
     */
    public function getDons()
    {
        $dons = $this->donsModel->getAllDons();
        
        Flight::render('dons/index', [
            'dons' => $dons
        ]);
    }

    /**
     * Affiche les détails d'un don
     */
    public function getDonById($id)
    {
        $don = $this->donsModel->getDonById($id);
        
        if (!$don) {
            Flight::halt(404, 'Don introuvable');
        }
        
        Flight::render('dons/show', [
            'don' => $don
        ]);
    }

    /**
     * Page de confirmation de suppression de don
     */
    public function confirmDeleteDon($id)
    {
        $don = $this->donsModel->getDonById($id);
        
        if (!$don) {
            Flight::halt(404, 'Don introuvable');
        }
        
        // Construire le label
        $label = 'Don de ';
        if (!empty($don['nom_article'])) {
            $label .= $don['nom_article'] . ' (Qté: ' . $don['quantite'] . ')';
        } else {
            $label .= number_format($don['montant_argent'], 0, ',', ' ') . ' Ar';
        }
        $label .= ' - ' . $don['donateur_nom'];
        
        Flight::render('confirm_delete', [
            'entity' => 'don',
            'id' => $id,
            'label' => $label,
            'back' => '/dons',
            'details' => $don
        ]);
    }

    /**
     * Supprime un don
     */
    public function deleteDon($id)
    {
        $count = $this->donsModel->deleteDon($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Flight::redirect('/dons?success=don_supprime');
            return;
        }
        
        Flight::json(['deleted' => $count]);
    }

    /**
     * Met à jour le statut d'un don
     */
    public function updateStatut($id, $statut = null)
    {
        if ($statut === null) {
            $statut = $_POST['statut'] ?? null;
        }
        
        if ($statut === null) {
            Flight::json(['error' => 'statut manquant']);
            return;
        }
        
        $count = $this->donsModel->updateStatut($id, $statut);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Flight::redirect('/dons');
            return;
        }
        
        Flight::json(['updated' => $count]);
    }

    /**
     * Affiche le choix de méthode de distribution pour un don
     */
    public function choixDistribution($id)
    {
        $don = $this->donsModel->getDonById($id);
        
        if (!$don) {
            Flight::halt(404, 'Don introuvable');
        }
        
        if ($don['statut'] !== 'disponible') {
            Flight::redirect('/dons?error=' . urlencode('Ce don n\'est plus disponible pour distribution'));
            return;
        }
        
        Flight::render('dons/choix-distribution', [
            'don' => $don
        ]);
    }

    /**
     * Valide un don avec la méthode dispatcher (manuel)
     */
    public function validerDonDispatcher($id)
    {
        $result = $this->donsModel->dispatcherDon($id);
        
        if ($result['success']) {
            $message = 'Don validé et dispatché avec succès ! ';
            
            if (isset($result['montant_affecte'])) {
                $message .= number_format($result['montant_affecte'], 0, ',', ' ') . ' Ar affectés.';
            } elseif (isset($result['quantite_affectee'])) {
                $message .= $result['quantite_affectee'] . ' unités affectées.';
            }
            
            // Ajouter un message de félicitation pour les villes dont tous les besoins sont couverts
            if (!empty($result['villes_satisfaites'])) {
                $message .= ' 🎉 FÉLICITATIONS ! Tous les besoins de ' . 
                           (count($result['villes_satisfaites']) > 1 ? 'ces villes sont' : 'cette ville est') . 
                           ' maintenant couverts : ' . 
                           implode(', ', $result['villes_satisfaites']) . ' !';
            }
            
            Flight::redirect('/dons?success=don_valide&message=' . urlencode($message));
        } else {
            Flight::redirect('/dons?error=' . urlencode($result['message']));
        }
    }

    /**
     * Valide un don avec la méthode "plus petit montant d'abord"
     */
    public function validerDonPlusPetit($id)
    {
        $don = $this->donsModel->getDonById($id);
        
        if (!$don) {
            Flight::redirect('/dons?error=' . urlencode('Don introuvable'));
            return;
        }
        
        if ($don['statut'] !== 'disponible') {
            Flight::redirect('/dons?error=' . urlencode('Ce don n\'est plus disponible'));
            return;
        }
        
        // Utiliser le modèle BesoinsModel avec la connexion DB
        $besoinsModel = new BesoinsModel($this->db);
        
        // Récupérer les besoins non satisfaits triés par montant
        $besoins = $besoinsModel->getBesoinsNonSatisfaits();
        
        if (empty($besoins)) {
            Flight::redirect('/dons?error=' . urlencode('Aucun besoin à satisfaire actuellement'));
            return;
        }
        
        // Trier par montant total croissant (du plus petit au plus grand)
        usort($besoins, function($a, $b) {
            $montantA = floatval($a['montant_total']);
            $montantB = floatval($b['montant_total']);
            return $montantA <=> $montantB;
        });
        
        $quantite_disponible = floatval($don['quantite_restante'] ?? $don['quantite']);
        $montant_disponible = floatval($don['montant_restant'] ?? $don['montant_argent']);
        $nbDistributions = 0;
        $montantTotal = 0;
        $quantiteTotal = 0;
        
        foreach ($besoins as $besoin) {
            if ($quantite_disponible <= 0 && $montant_disponible <= 0) {
                break;
            }
            
            // Si don matériel
            if ($don['id_article']) {
                // Récupérer les détails de l'article du besoin
                $tmt = $this->db->runQuery("
                    SELECT ba.quantite, ba.quantite_satisfaite, ba.prix_unitaire, ba.id_article
                    FROM besoin_articles ba
                    WHERE ba.id_besoin = ? AND ba.id_article = ?
                ", [$besoin['id_besoin'], $don['id_article']]);
                $articleBesoin = $tmt->fetch();
                
                if ($articleBesoin) {
                    $quantiteNecessaire = floatval($articleBesoin['quantite']) - floatval($articleBesoin['quantite_satisfaite']);
                    
                    if ($quantiteNecessaire > 0) {
                        $quantiteADistribuer = min($quantite_disponible, $quantiteNecessaire);
                        
                        if ($quantiteADistribuer > 0) {
                            $this->donsModel->distribuerArticle($don['id_don'], $besoin['id_besoin'], $quantiteADistribuer);
                            $quantite_disponible -= $quantiteADistribuer;
                            $quantiteTotal += $quantiteADistribuer;
                            $nbDistributions++;
                        }
                    }
                }
            }
            // Si don en argent
            else {
                $montantNecessaire = floatval($besoin['montant_total']) - floatval($besoin['montant_recu']);
                
                if ($montantNecessaire > 0) {
                    $montantADistribuer = min($montant_disponible, $montantNecessaire);
                    
                    if ($montantADistribuer > 0) {
                        $this->donsModel->distribuerArgent($don['id_don'], $besoin['id_besoin'], $montantADistribuer);
                        $montant_disponible -= $montantADistribuer;
                        $montantTotal += $montantADistribuer;
                        $nbDistributions++;
                    }
                }
            }
        }
        
        // Mettre à jour le statut du don
        if ($don['id_article']) {
            if ($quantite_disponible <= 0) {
                $this->donsModel->updateStatut($don['id_don'], 'affecte');
            } elseif ($quantite_disponible < floatval($don['quantite'])) {
                $this->donsModel->updateStatut($don['id_don'], 'partiel');
            }
        } else {
            if ($montant_disponible <= 0) {
                $this->donsModel->updateStatut($don['id_don'], 'affecte');
            } elseif ($montant_disponible < floatval($don['montant_argent'])) {
                $this->donsModel->updateStatut($don['id_don'], 'partiel');
            }
        }
        
        if ($nbDistributions > 0) {
            $message = "Don distribué avec succès selon la méthode 'Plus petit montant d'abord' ! ";
            $message .= "$nbDistributions besoin(s) satisfait(s). ";
            
            if ($montantTotal > 0) {
                $message .= number_format($montantTotal, 0, ',', ' ') . ' Ar distribués.';
            } elseif ($quantiteTotal > 0) {
                $message .= number_format($quantiteTotal, 2, ',', ' ') . ' unités distribuées.';
            }
            
            Flight::redirect('/dons?success=don_valide&message=' . urlencode($message));
        } else {
            Flight::redirect('/dons?error=' . urlencode('Aucune distribution possible'));
        }
    }

    /**
     * Valide un don avec la méthode proportionnelle (Largest Remainder Method)
     */
    public function validerDonProportionnel($id)
    {
        $don = $this->donsModel->getDonById($id);
        
        if (!$don) {
            Flight::redirect('/dons?error=' . urlencode('Don introuvable'));
            return;
        }
        
        if ($don['statut'] !== 'disponible') {
            Flight::redirect('/dons?error=' . urlencode('Ce don n\'est plus disponible'));
            return;
        }
        
        // Utiliser le modèle BesoinsModel
        $besoinsModel = new BesoinsModel($this->db);
        
        // Récupérer les besoins non satisfaits
        $besoins = $besoinsModel->getBesoinsNonSatisfaits();
        
        if (empty($besoins)) {
            Flight::redirect('/dons?error=' . urlencode('Aucun besoin à satisfaire actuellement'));
            return;
        }
        
        $quantite_disponible = floatval($don['quantite_restante'] ?? $don['quantite']);
        $montant_disponible = floatval($don['montant_restant'] ?? $don['montant_argent']);
        
        // Filtrer les besoins compatibles et calculer les demandes
        $besoinsCompatibles = [];
        $totalDemande = 0;
        
        foreach ($besoins as $besoin) {
            if ($don['id_article']) {
                // Don matériel - vérifier compatibilité article
                $tmt = $this->db->runQuery("
                    SELECT ba.quantite, ba.quantite_satisfaite
                    FROM besoin_articles ba
                    WHERE ba.id_besoin = ? AND ba.id_article = ?
                ", [$besoin['id_besoin'], $don['id_article']]);
                $articleBesoin = $tmt->fetch();
                
                if ($articleBesoin) {
                    $demande = floatval($articleBesoin['quantite']) - floatval($articleBesoin['quantite_satisfaite']);
                    if ($demande > 0) {
                        $besoinsCompatibles[] = [
                            'id_besoin' => $besoin['id_besoin'],
                            'demande' => $demande
                        ];
                        $totalDemande += $demande;
                    }
                }
            } else {
                // Don en argent
                $demande = floatval($besoin['montant_total']) - floatval($besoin['montant_recu']);
                if ($demande > 0) {
                    $besoinsCompatibles[] = [
                        'id_besoin' => $besoin['id_besoin'],
                        'demande' => $demande
                    ];
                    $totalDemande += $demande;
                }
            }
        }
        
        if (empty($besoinsCompatibles) || $totalDemande <= 0) {
            Flight::redirect('/dons?error=' . urlencode('Aucun besoin compatible trouvé'));
            return;
        }
        
        $donDisponible = $don['id_article'] ? $quantite_disponible : $montant_disponible;
        
        // Étape 1 : Calcul proportionnel et arrondi inférieur
        $distributions = [];
        $totalDistribue = 0;
        
        foreach ($besoinsCompatibles as $bc) {
            $proportionnel = ($bc['demande'] / $totalDemande) * $donDisponible;
            $arrondiInferieur = floor($proportionnel);
            $decimal = $proportionnel - $arrondiInferieur;
            
            $distributions[] = [
                'id_besoin' => $bc['id_besoin'],
                'demande' => $bc['demande'],
                'proportionnel' => $proportionnel,
                'arrondi' => $arrondiInferieur,
                'decimal' => $decimal,
                'final' => $arrondiInferieur
            ];
            
            $totalDistribue += $arrondiInferieur;
        }
        
        // Étape 2 : Distribution du reste selon les plus grandes décimales
        $reste = $donDisponible - $totalDistribue;
        
        if ($reste > 0) {
            // Trier par décimale décroissante
            usort($distributions, function($a, $b) {
                return $b['decimal'] <=> $a['decimal'];
            });
            
            // Distribuer le reste
            for ($i = 0; $i < $reste && $i < count($distributions); $i++) {
                $distributions[$i]['final'] += 1;
            }
        }
        
        // Étape 3 : Appliquer les distributions
        $nbDistributions = 0;
        $totalFinal = 0;
        
        foreach ($distributions as $dist) {
            if ($dist['final'] > 0) {
                if ($don['id_article']) {
                    $this->donsModel->distribuerArticle($don['id_don'], $dist['id_besoin'], $dist['final']);
                } else {
                    $this->donsModel->distribuerArgent($don['id_don'], $dist['id_besoin'], $dist['final']);
                }
                $nbDistributions++;
                $totalFinal += $dist['final'];
            }
        }
        
        // Mettre à jour le statut du don
        if ($don['id_article']) {
            $quantite_restante = $quantite_disponible - $totalFinal;
            if ($quantite_restante <= 0) {
                $this->donsModel->updateStatut($don['id_don'], 'affecte');
            } elseif ($quantite_restante < floatval($don['quantite'])) {
                $this->donsModel->updateStatut($don['id_don'], 'partiel');
            }
        } else {
            $montant_restant = $montant_disponible - $totalFinal;
            if ($montant_restant <= 0) {
                $this->donsModel->updateStatut($don['id_don'], 'affecte');
            } elseif ($montant_restant < floatval($don['montant_argent'])) {
                $this->donsModel->updateStatut($don['id_don'], 'partiel');
            }
        }
        
        if ($nbDistributions > 0) {
            $message = "Don distribué avec succès selon la méthode proportionnelle ! ";
            $message .= "$nbDistributions besoin(s) ont reçu une part. ";
            
            if ($don['id_article']) {
                $message .= number_format($totalFinal, 2, ',', ' ') . ' unités distribuées.';
            } else {
                $message .= number_format($totalFinal, 0, ',', ' ') . ' Ar distribués.';
            }
            
            Flight::redirect('/dons?success=don_valide&message=' . urlencode($message));
        } else {
            Flight::redirect('/dons?error=' . urlencode('Aucune distribution possible'));
        }
    }
}
