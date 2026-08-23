<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rapport-gestion')]
class RapportController extends AbstractController
{
    #[Route('/', name: 'app_rapport_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        // Récupère toutes les catégories
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);

        // Récupère aussi les produits orphelins (sans catégorie) si besoin
        $uncategorizedProducts = $productRepository->findBy(['category' => null], ['name' => 'ASC']);

        return $this->render('rapport/index.html.twig', [
            'categories' => $categories,
            'uncategorizedProducts' => $uncategorizedProducts,
        ]);
    }

    #[Route('/generer', name: 'app_rapport_generate', methods: ['POST'])]
    public function generateReport(Request $request, ProductRepository $productRepository): Response
    {
        $productIds = $request->request->all('selected_products');

        if (empty($productIds)) {
            $this->addFlash('error', 'Veuillez sélectionner au moins un produit pour générer l’audit.');
            return $this->redirectToRoute('app_rapport_index');
        }

        $products = $productRepository->findBy(['id' => $productIds]);

        $reportData = [];
        $globalTotalSolde = 0;
        $globalCharges = 0;
        $globalGains = 0;

        $produitsFormates = [];
        $stepsComparison = [];

        foreach ($products as $product) {
            $productSolde = 0;
            $productCharges = 0;
            $productGains = 0;
            $stepDetails = [];

            foreach ($product->getProcessuses() as $processus) {
                if (method_exists($processus, 'getSoldeFinal')) {
                    $productSolde += $processus->getSoldeFinal();
                }

                foreach ($processus->getSteps() as $step) {
                    $amount = method_exists($step, 'getAmount') ? $step->getAmount() : $step->getAmout();
                    $isGain = $step->isGain();

                    if ($isGain) {
                        $productGains += $amount;
                    } else {
                        $productCharges += $amount;
                    }

                    // Agrégation fine par étape (pour l'analyse IA)
                    $stepName = $step->getName() ?? ('Étape #' . $step->getId());
                    if (!isset($stepDetails[$stepName])) {
                        $stepDetails[$stepName] = ['gains' => 0, 'charges' => 0];
                    }
                    if ($isGain) {
                        $stepDetails[$stepName]['gains'] += $amount;
                    } else {
                        $stepDetails[$stepName]['charges'] += $amount;
                    }
                }
            }

            if (!method_exists($product->getProcessuses()->first() ?: null, 'getSoldeFinal')) {
                $productSolde = $productGains - $productCharges;
            }

            $globalTotalSolde += $productSolde;
            $globalCharges += $productCharges;
            $globalGains += $productGains;

            $categoryName = $product->getCategory() ? $product->getCategory()->getName() : 'Sans catégorie';
            $displayName = $product->getName() . ' (' . $categoryName . ')';

            $reportData[] = [
                'object' => $product,
                'displayName' => $displayName,
                'category' => $categoryName,
                'solde' => $productSolde,
                'charges' => $productCharges,
                'gains' => $productGains,
                'processusCount' => count($product->getProcessuses()),
                'stepDetails' => $stepDetails,
            ];

            $produitsFormates[] = [
                'name' => $displayName,
                'rentabiliteNet' => $productSolde,
                'charges' => $productCharges,
                'gains' => $productGains,
                'tauxMarge' => $productGains > 0 ? round(($productSolde / $productGains) * 100, 1) : 0,
                'stepDetails' => $stepDetails
            ];
        }

        // Tri par rentabilité
        usort($reportData, function($a, $b) {
            return $b['solde'] <=> $a['solde'];
        });

        $topProduct = $reportData[0];
        $flopProduct = count($reportData) > 1 ? $reportData[count($reportData) - 1] : null;

        // Génération de l'analyse synthétique avancée
        $aiAnalysis = $this->generateGlobalAiAnalysis($reportData, $globalTotalSolde, $globalGains, $globalCharges, $topProduct, $flopProduct);

        return $this->render('rapport/global_report.html.twig', [
            'reportData' => $reportData,
            'globalTotalSolde' => $globalTotalSolde,
            'globalCharges' => $globalCharges,
            'globalGains' => $globalGains,
            'globalTauxMarge' => $globalGains > 0 ? round(($globalTotalSolde / $globalGains) * 100, 1) : 0,
            'topProduct' => $topProduct,
            'flopProduct' => $flopProduct,
            'aiAnalysis' => $aiAnalysis,
            'selectedCount' => count($products),
            'produits' => $produitsFormates
        ]);
    }

    private function generateGlobalAiAnalysis(array $reportData, float $globalSolde, float $globalGains, float $globalCharges, array $top, ?array $flop): array
    {
        $tauxMargeGlobal = $globalGains > 0 ? round(($globalSolde / $globalGains) * 100, 1) : 0;
        $status = $globalSolde >= 0 ? "EXCÉDENTAIRE" : "DÉFICITAIRE";

        $ecartEuros = $flop ? ($top['solde'] - $flop['solde']) : 0;
        $ratioFlopTop = ($flop && $top['solde'] > 0) ? round(($flop['solde'] / $top['solde']) * 100, 1) : 100;

        return [
            'status' => $status,
            'tauxMargeGlobal' => $tauxMargeGlobal,
            'ecartEuros' => $ecartEuros,
            'ratioFlopTop' => $ratioFlopTop,
            'topName' => $top['displayName'],
            'topSolde' => $top['solde'],
            'flopName' => $flop ? $flop['displayName'] : null,
            'flopSolde' => $flop ? $flop['solde'] : null,
            'hasMultiple' => count($reportData) > 1,
        ];
    }
}
