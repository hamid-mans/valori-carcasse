<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SimulationController extends AbstractController
{
    #[Route('/simulation', name: 'app_simulation', methods: ['GET'])]
    public function index(
        Request $request,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ): Response {
        $categories = $categoryRepository->findAll();

        $categoryId = $request->query->get('category_id');
        $productId = $request->query->get('product_id');

        $selectedCategory = $categoryId ? $categoryRepository->find($categoryId) : null;
        $selectedProduct = $productId ? $productRepository->find($productId) : null;

        $availableProducts = $selectedCategory
            ? $productRepository->findBy(['category' => $selectedCategory])
            : $productRepository->findAll();

        // Structure JSON pour le JavaScript
        $productData = null;

        if ($selectedProduct) {
            $processusesData = [];

            foreach ($selectedProduct->getProcessuses() as $processus) {
                $stepsData = [];

                // On parcourt les étapes réelles du processus
                // Adapte 'getSteps()' selon le nom de la relation dans ton entité Processus (ex: getEtapes())
                $steps = method_exists($processus, 'getSteps') ? $processus->getSteps() : [];

                foreach ($steps as $step) {
                    // Calcul du solde orienté selon isGain (positif si gain, négatif si coût)
                    $montant = $step->getAmout() ?? 0.0;
                    $solde = $step->isGain() ? $montant : -$montant;

                    $stepsData[] = [
                        'id'          => $step->getId(),
                        'name'        => $step->getName(),
                        'soldeActuel' => (float) $solde,
                        'variation'   => 0,
                    ];
                }

                $processusesData[] = [
                    'id' => $processus->getId(),
                    'name' => $processus->getName(),
                    'soldeActuel' => (float) $processus->getSoldeFinal(),
                    'steps' => $stepsData,
                ];
            }

            $productData = [
                'id' => $selectedProduct->getId(),
                'name' => $selectedProduct->getName(),
                'processuses' => $processusesData,
            ];
        }

        return $this->render('simulation/index.html.twig', [
            'categories' => $categories,
            'availableProducts' => $availableProducts,
            'selectedCategory' => $selectedCategory,
            'selectedProduct' => $selectedProduct,
            'productDataJson' => json_encode($productData),
        ]);
    }
}
