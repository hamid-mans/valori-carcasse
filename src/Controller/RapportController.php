<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rapport-gestion')]
class RapportController extends AbstractController
{
    #[Route('/', name: 'app_rapport_index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('rapport/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/generer', name: 'app_rapport_generate', methods: ['POST'])]
    public function generateReport(Request $request, ProductRepository $productRepository): Response
    {
        // Récupération des IDs des produits cochés
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

        // Tableau destiné à la matrice SWOT du template (clé 'produits')
        $produitsFormates = [];

        foreach ($products as $product) {
            $productSolde = 0;
            $productCharges = 0;
            $productGains = 0;

            foreach ($product->getProcessuses() as $processus) {
                // Gestion de la méthode selon ton entité historique
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

            // Si getSoldeFinal() n'existe pas ou n'est pas utilisé pour le solde, recalculer au besoin :
            // $productSolde = $productGains - $productCharges;

            $globalTotalSolde += $productSolde;
            $globalCharges += $productCharges;
            $globalGains += $productGains;

            // On stocke les stats par produit pour le tableau comparatif
            $reportData[] = [
                'object' => $product,
                'solde' => $productSolde,
                'charges' => $productCharges,
                'gains' => $productGains,
                'processusCount' => count($product->getProcessuses()),
            ];

            // On formate les données essentielles attendues par la boucle `{% for produit in produits %}` du SWOT
            $produitsFormates[] = [
                'name' => $product->getName(),
                'rentabiliteNet' => $productSolde
            ];
        }

        // Tri des produits du plus rentable au moins rentable pour isoler le Top/Flop
        usort($reportData, function($a, $b) {
            return $b['solde'] <=> $a['solde'];
        });

        $topProduct = $reportData[0];
        $flopProduct = count($reportData) > 1 ? $reportData[count($reportData) - 1] : null;

        // Génération de la note de synthèse comparative (IA Style)
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
            'produits' => $produitsFormates // <--- On passe ici le tableau pour corriger l'erreur de Twig !
        ]);
    }

    private function generateGlobalAiAnalysis(array $reportData, float $globalSolde, array $top, ?array $flop): string
    {
        $status = $globalSolde >= 0 ? "excédentaire" : "déficitaire";

        $text = "### SYNTHÈSE STRATÉGIQUE MULTI-PRODUITS\n";
        $text .= "L'analyse consolidée des portefeuilles matières sélectionnés met en évidence une performance globale {$status} s'élevant à " . number_format($globalSolde, 2, ',', ' ') . " € de marge brute nette.\n\n";

        $text .= "Le Levier de Performance (Top Produit) : \n";
        $text .= "La matière brute « " . strtoupper($top['object']->getName()) . " » s'impose comme le moteur financier principal du panel, dégageant un solde de " . number_format($top['solde'], 2, ',', ' ') . " €. C'est sur ce segment que les processus de valorisation atteignent leur meilleur taux de conversion. Recommandation : Prioriser les volumes d'achats et le sourcing sur cette catégorie.\n\n";

        if ($flop && $flop['solde'] < $top['solde']) {
            $text .= "2️⃣ **Le Point de Vigilance (Alerte Rentabilité) :** \n";
            $text .= "À l'inverse, le produit « " . strtoupper($flop['object']->getName()) . " » affiche la performance la plus dégradée du lot avec un résultat de " . number_format($flop['solde'], 2, ',', ' ') . " €. ";

            if ($flop['solde'] < 0) {
                $text .= "Ce produit subit une structure de charges intermédiaires (traitement, logistique, destruction) supérieure aux gains de revalorisation. Une suspension temporaire ou un reparamétrage strict des barèmes d'exploitation est requis pour stopper l'attrition de marge.";
            } else {
                $text .= "Bien que positif, ce produit presents un coût de revient trop élevé par rapport à sa valeur finale sur le marché. Ses lignes de traitement doivent être optimisées pour s'aligner sur les standards du leader.";
            }
        }

        return $text;
    }
}