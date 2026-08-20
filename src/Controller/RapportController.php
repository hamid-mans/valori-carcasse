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
        // Récupération des IDs des produits cochés
        $productIds = $request->request->all('selected_products');

        if (empty($productIds)) {
            $this->addFlash('error', 'Veuillez sélectionner au moins un produit (ou deux pour comparer des catégories différentes) afin de générer l’audit.');
            return $this->redirectToRoute('app_rapport_index');
        }

        $products = $productRepository->findBy(['id' => $productIds]);

        $reportData = [];
        $globalTotalSolde = 0;
        $globalCharges = 0;
        $globalGains = 0;

        $produitsFormates = [];

        foreach ($products as $product) {
            $productSolde = 0;
            $productCharges = 0;
            $productGains = 0;

            foreach ($product->getProcessuses() as $processus) {
                if (method_exists($processus, 'getSoldeFinal')) {
                    $productSolde += $processus->getSoldeFinal();
                }

                foreach ($processus->getSteps() as $step) {
                    $amount = method_exists($step, 'getAmount') ? $step->getAmount() : $step->getAmout();
                    if ($step->isGain()) {
                        $productGains += $amount;
                    } else {
                        $productCharges += $amount;
                    }
                }
            }

            if (!method_exists($product->getProcessuses()->first() ?: null, 'getSoldeFinal')) {
                $productSolde = $productGains - $productCharges;
            }

            $globalTotalSolde += $productSolde;
            $globalCharges += $productCharges;
            $globalGains += $productGains;

            // Libellé clair combinant Produit + Catégorie pour la comparaison
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
            ];

            $produitsFormates[] = [
                'name' => $displayName,
                'rentabiliteNet' => $productSolde
            ];
        }

        // Tri du plus rentable au moins rentable
        usort($reportData, function($a, $b) {
            return $b['solde'] <=> $a['solde'];
        });

        $topProduct = $reportData[0];
        $flopProduct = count($reportData) > 1 ? $reportData[count($reportData) - 1] : null;

        $aiAnalysis = $this->generateGlobalAiAnalysis($reportData, $globalTotalSolde, $topProduct, $flopProduct);

        return $this->render('rapport/global_report.html.twig', [
            'reportData' => $reportData,
            'globalTotalSolde' => $globalTotalSolde,
            'globalCharges' => $globalCharges,
            'globalGains' => $globalGains,
            'topProduct' => $topProduct,
            'flopProduct' => $flopProduct,
            'aiAnalysis' => $aiAnalysis,
            'selectedCount' => count($products),
            'produits' => $produitsFormates
        ]);
    }

    private function generateGlobalAiAnalysis(array $reportData, float $globalSolde, array $top, ?array $flop): string
    {
        $status = $globalSolde >= 0 ? "excédentaire" : "déficitaire";

        $text = "### SYNTHÈSE STRATÉGIQUE COMPARATIVE MULTI-CATÉGORIES\n";
        $text .= "L'analyse comparative des portefeuilles sélectionnés met en évidence une performance globale {$status} de " . number_format($globalSolde, 2, ',', ' ') . " € de marge nette.\n\n";

        $text .= "Top Performance : \n";
        $text .= "Le produit « " . strtoupper($top['displayName']) . " » s'impose comme la référence du panel avec un solde de " . number_format($top['solde'], 2, ',', ' ') . " €.\n\n";

        if ($flop && $flop['solde'] < $top['solde']) {
            $text .= "Point d'Ajustement : \n";
            $text .= "À l'inverse, « " . strtoupper($flop['displayName']) . " » affiche un résultat de " . number_format($flop['solde'], 2, ',', ' ') . " €. ";

            $ecart = $top['solde'] - $flop['solde'];
            $text .= "L'écart de rentabilité entre ces deux périmètres est de " . number_format($ecart, 2, ',', ' ') . " €.";
        }

        return $text;
    }
}
