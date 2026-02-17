<?php

use app\controllers\BesoinsController;
use app\controllers\VillesController;
use app\controllers\LivraisonsController;
use app\controllers\TypeDonController;
use app\controllers\ArticleController;
use app\controllers\DonsController;
use app\controllers\TableauBordController;
use app\controllers\AchatsController;
use app\controllers\AdminController;
use app\controllers\DistributionController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\controllers\ArticlesController;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$router->group('/', function (Router $router) use ($app) {

	$router->get('/formulaire-don', function() use ($app) {
	$typeDonController = new TypeDonController();
	$typeDons = $typeDonController->getAllTypes();
	$articleController = new ArticleController();
	$articles = $articleController->getAllArticles();
    $app->render('formulaire-don', [
        'typeDons' => $typeDons,
        'articles' => $articles
    ]);
});

    // Accueil / Tableau de bord
   	$router->get('', [TableauBordController::class, 'index']);
   	$router->get('tableau-bord', [TableauBordController::class, 'index']);
   	$router->get('recapitulation', [TableauBordController::class, 'recapitulation']);
   	$router->get('api/recapitulatif', [TableauBordController::class, 'getRecapitulatifAjax']);

	// Administration - Réinitialisation
	$router->get('reset', [AdminController::class, 'confirmReset']);
	$router->post('reset/execute', [AdminController::class, 'reset']);

	// Distribution automatique
	$router->get('distribution', [DistributionController::class, 'index']);
	$router->post('distribution/executer', [DistributionController::class, 'distribuerParMontant']);

	/* =======================
	   BESOINS (routes fixes)
	   ======================= */

	// Liste des besoins
	$router->get('besoins', [BesoinsController::class, 'getBesoins']);

	// Création besoin
	$router->get('besoin/create', [BesoinsController::class, 'newBesoinForm']);
	$router->post('besoin/create', [BesoinsController::class, 'storeBesoin']);

	// Filtres / statistiques
	$router->get('besoins/non-satisfaits', [BesoinsController::class, 'getBesoinsNonSatisfaits']);
	$router->get('besoins/villes-satisfaites', [BesoinsController::class, 'getVillesSatisfaites']);
	$router->get('besoins/critiques-materiels', [BesoinsController::class, 'getBesoinsCritiquesMateriels']);
	$router->get('besoins/montant-total', [BesoinsController::class, 'getMontantTotal']);

	/* =======================
	   BESOINS (routes avec ID)
	   ======================= */

	$router->get('besoin/@id/montant', [BesoinsController::class, 'getBesoinMontant']);
	$router->post('besoin/@id/statut', [BesoinsController::class, 'updateBesoinStatut']);
	$router->delete('besoin/@id', [BesoinsController::class, 'deleteBesoin']);
	$router->post('besoin/@id/delete', [BesoinsController::class, 'deleteBesoin']);
	$router->get('besoin/@id/delete', [BesoinsController::class, 'confirmDeleteBesoin']);
	$router->get('besoin/@id', [BesoinsController::class, 'getBesoinById']);

	/* =======================
	   VILLES (routes fixes)
	   ======================= */

	// Création ville
	$router->get('ville/create', [VillesController::class, 'newVilleForm']);
	$router->post('ville/create', [VillesController::class, 'storeVille']);

	// Filtres / statistiques
	$router->get('villes/nombre', [VillesController::class, 'getNombreVilles']);

	/* =======================
	   VILLES (routes avec ID)
	   ======================= */

	$router->get('villes', [VillesController::class, 'getVilles']);
	$router->get('ville/@id/besoins', [VillesController::class, 'getVilleBesoins']);
	$router->delete('ville/@id', [VillesController::class, 'deleteVille']);
	$router->post('ville/@id/delete', [VillesController::class, 'deleteVille']);
	$router->get('ville/@id/delete', [VillesController::class, 'confirmDeleteVille']);
	$router->get('ville/@id', [VillesController::class, 'getVilleById']);

	/* =======================
	   DONS (donations)
	   ======================= */

	// Création don
	$router->get('don/create', [DonsController::class, 'newDonForm']);
	$router->post('don/create', [DonsController::class, 'storeDon']);

	// Liste et consultation
	$router->get('dons', [DonsController::class, 'getDons']);
	$router->get('don/@id', [DonsController::class, 'getDonById']);

	// Validation / Dispatch - Choix de méthode
	$router->get('don/@id/valider', [DonsController::class, 'choixDistribution']);
	$router->post('don/@id/valider/dispatcher', [DonsController::class, 'validerDonDispatcher']);
	$router->post('don/@id/valider/plus-petit', [DonsController::class, 'validerDonPlusPetit']);
	$router->post('don/@id/valider/proportionnel', [DonsController::class, 'validerDonProportionnel']);

	// Suppression
	$router->get('don/@id/delete', [DonsController::class, 'confirmDeleteDon']);
	$router->delete('don/@id', [DonsController::class, 'deleteDon']);
	$router->post('don/@id/delete', [DonsController::class, 'deleteDon']);

	/* =======================
	   ARTICLES
	   ======================= */

	$router->get('articles', [ArticlesController::class, 'index']);
	$router->get('articles/create', [ArticlesController::class, 'ajouter']);
	$router->get('articles/ajouter', [ArticlesController::class, 'ajouter']);
	$router->post('articles/save', [ArticlesController::class, 'save']);
	$router->get('articles/modifier/@id', [ArticlesController::class, 'modifier']);
	$router->post('articles/update/@id', [ArticlesController::class, 'update']);
	$router->get('articles/supprimer/@id', [ArticlesController::class, 'supprimer']);

	/* =======================
	   ACHATS
	   ======================= */

	// Formulaire et création d'achat
	$router->get('achat/formulaire/@id_besoin', [AchatsController::class, 'formulaireAchat']);
	$router->post('achat/create', [AchatsController::class, 'createAchatSimule']);

	// Simulation et validation
	$router->get('achats/simulation', [AchatsController::class, 'pageSimulation']);
	$router->post('achat/valider/@id_achat', [AchatsController::class, 'validerAchat']);
	$router->post('achat/supprimer/@id_achat', [AchatsController::class, 'supprimerAchatSimule']);

	// Configuration
	$router->post('achats/config', [AchatsController::class, 'configurerFrais']);

}, [new SecurityHeadersMiddleware($app)]);
