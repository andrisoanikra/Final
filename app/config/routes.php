<?php

use app\controllers\BesoinsController;
use app\controllers\VillesController;
use app\controllers\LivraisonsController;
use app\controllers\TypeDonController;
use app\controllers\ArticleController;
use app\controllers\DonsController;
use app\controllers\TableauBordController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$router->group('/', function (Router $router) use ($app) {

	$router->get('/formulaire-don', function() use ($app) {
	$typeDonController = new TypeDonController($app);
	$typeDons = $typeDonController->getAllTypes();
	$articleController = new ArticleController($app);
	$articles = $articleController->getAllArticles();
    $app->render('formulaire-don', [
        'typeDons' => $typeDons,
        'articles' => $articles
    ]);
});

    // Accueil / Tableau de bord
   	$router->get('', [TableauBordController::class, 'index']);
   	$router->get('tableau-bord', [TableauBordController::class, 'index']);

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

	// Validation / Dispatch
	$router->get('don/@id/valider', [DonsController::class, 'validerDon']);

	// Suppression
	$router->delete('don/@id', [DonsController::class, 'deleteDon']);
	$router->post('don/@id/delete', [DonsController::class, 'deleteDon']);

}, [new SecurityHeadersMiddleware($app)]);
