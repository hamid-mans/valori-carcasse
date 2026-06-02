<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProcessusRepository;
use App\Repository\StepRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('main/home.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(
        Request $request,
        ProductRepository $productRepository,
        ProcessusRepository $processusRepository
    ): Response {
        $products = $productRepository->findAll();

        $selectedProductId = $request->query->get('product_id', $products[0]?->getId() ?? null);
        $selectedProduct = $selectedProductId ? $productRepository->find($selectedProductId) : null;

        $allProcessus = $processusRepository->findAll();
        $globalGains = 0;
        $globalCosts = 0;

        foreach ($allProcessus as $processus) {
            $globalGains += $processus->getGainsTotal();
            $globalCosts += $processus->getCostsTotal();
        }

        $stats = [
            'totalProducts'  => count($products),
            'totalProcessus' => count($allProcessus),
            'globalGains'    => $globalGains,
            'globalCosts'    => $globalCosts,
            'globalSolde'    => $globalGains - $globalCosts,
        ];

        return $this->render('main/dashboard.html.twig', [
            'products'        => $products,
            'selectedProduct' => $selectedProduct,
            'stats'           => $stats,
        ]);
    }

    #[Route('/analytics/steps', name: 'app_analytics_steps')]
    public function analyzeSteps(Request $request, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        $searchQuery = $request->query->get('search', '');
        $productIds = $request->query->all('product_ids');

        $stepsData = [];
        if (!empty($searchQuery) || !empty($productIds)) {
            foreach ($products as $product) {
                if (!empty($productIds) && !in_array($product->getId(), $productIds)) {
                    continue;
                }

                foreach ($product->getProcessuses() as $processus) {
                    foreach ($processus->getSteps() as $step) {
                        if (!empty($searchQuery) && mb_stripos($step->getName(), $searchQuery) === false) {
                            continue;
                        }

                        $stepsData[] = [
                            'product'   => $product->getName(),
                            'processus' => $processus->getName(),
                            'step_name' => $step->getName(),
                            'amount'    => $step->getAmout(),
                            'is_gain'   => $step->isGain()
                        ];
                    }
                }
            }
        }

        return $this->render('main/analytics_steps.html.twig', [
            'products'          => $products,
            'stepsData'         => $stepsData,
            'currentSearch'     => $searchQuery,
            'currentProductIds' => $productIds,
        ]);
    }

    #[Route('/compare', name: 'app_compare')]
    public function compare(
        Request $request,
        ProductRepository $productRepository,
        ProcessusRepository $processusRepository,
        StepRepository $stepRepository
    ): Response {
        // 1. Récupération de tous les produits pour les deux premiers sélecteurs
        $allProducts = $productRepository->findAll();

        // 2. Récupération des paramètres de la requête GET
        $productAId = $request->query->get('product_a');
        $productBId = $request->query->get('product_b');
        $sessionAId = $request->query->get('session_a');
        $sessionBId = $request->query->get('session_b');
        $stepAId    = $request->query->get('step_a');
        $stepBId    = $request->query->get('step_b');

        // Objects correspondants
        $productA = $productAId ? $productRepository->find($productAId) : null;
        $productB = $productBId ? $productRepository->find($productBId) : null;

        $sessionA = $sessionAId ? $processusRepository->find($sessionAId) : null;
        $sessionB = $sessionBId ? $processusRepository->find($sessionBId) : null;

        $stepA = $stepAId ? $stepRepository->find($stepAId) : null;
        $stepB = $stepBId ? $stepRepository->find($stepBId) : null;

        // 3. Listes filtrées pour les sélecteurs dépendants
        $processusListA = $productA ? $processusRepository->findBy(['product' => $productA]) : [];
        $processusListB = $productB ? $processusRepository->findBy(['product' => $productB]) : [];

        $stepsListA = $sessionA ? $sessionA->getSteps() : [];
        $stepsListB = $sessionB ? $sessionB->getSteps() : [];

        return $this->render('main/compare.html.twig', [
            'products'       => $allProducts,
            'productA'       => $productA,
            'productB'       => $productB,
            'processusListA' => $processusListA,
            'processusListB' => $processusListB,
            'sessionA'       => $sessionA,
            'sessionB'       => $sessionB,
            'stepsListA'     => $stepsListA,
            'stepsListB'     => $stepsListB,
            'stepA'          => $stepA,
            'stepB'          => $stepB,
        ]);
    }
}