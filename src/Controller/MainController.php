<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Processus;
use App\Repository\CategoryRepository;
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
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('main/home.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(
        Request $request,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ): Response {
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);

        // 1. Récupération de la catégorie sélectionnée (par défaut la première)
        $selectedCategoryId = $request->query->get('category_id');
        if (!$selectedCategoryId && !empty($categories)) {
            $selectedCategoryId = $categories[0]->getId();
        }
        $selectedCategory = $selectedCategoryId ? $categoryRepository->find($selectedCategoryId) : null;

        // 2. Produits associés à la catégorie sélectionnée
        $products = [];
        if ($selectedCategory) {
            $products = $productRepository->findBy(['category' => $selectedCategory], ['name' => 'ASC']);
        }

        // 3. Produit spécifique filtré (si l'utilisateur en choisit un)
        $selectedProductId = $request->query->get('product_id');
        $selectedProduct = null;
        if ($selectedProductId) {
            $productCandidate = $productRepository->find($selectedProductId);
            if ($productCandidate && $selectedCategory && $productCandidate->getCategory()?->getId() === $selectedCategory->getId()) {
                $selectedProduct = $productCandidate;
            }
        }

        // 4. Extraction des sessions (processus)
        $processuses = [];
        if ($selectedProduct) {
            $processuses = $selectedProduct->getProcessuses()->toArray();
        } elseif ($selectedCategory) {
            foreach ($products as $product) {
                foreach ($product->getProcessuses() as $processus) {
                    $processuses[] = $processus;
                }
            }
        }

        // 5. Calcul des totaux
        $globalGains = 0;
        $globalCosts = 0;
        foreach ($processuses as $processus) {
            $globalGains += $processus->getGainsTotal();
            $globalCosts += $processus->getCostsTotal();
        }

        $stats = [
            'totalCategories' => count($categories),
            'totalProducts'   => count($products),
            'totalProcessus'  => count($processuses),
            'globalGains'     => $globalGains,
            'globalCosts'     => $globalCosts,
            'globalSolde'     => $globalGains - $globalCosts,
        ];

        return $this->render('main/dashboard.html.twig', [
            'categories'       => $categories,
            'products'         => $products,
            'selectedCategory' => $selectedCategory,
            'selectedProduct'  => $selectedProduct,
            'processuses'      => $processuses,
            'stats'            => $stats,
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
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        ProcessusRepository $processusRepository,
        StepRepository $stepRepository
    ): Response {
        $allCategories = $categoryRepository->findBy([], ['name' => 'ASC']);

        // --- CÔTÉ A ---
        $categoryAId = $request->query->get('category_a');
        $productAId  = $request->query->get('product_a');
        $sessionAId  = $request->query->get('session_a');
        $stepAId     = $request->query->get('step_a');

        $categoryA = $categoryAId ? $categoryRepository->find($categoryAId) : null;
        $productA  = $productAId ? $productRepository->find($productAId) : null;
        $sessionA  = $sessionAId ? $processusRepository->find($sessionAId) : null;
        $stepA     = $stepAId ? $stepRepository->find($stepAId) : null;

        $productsA = $categoryA ? $categoryA->getProducts() : [];
        $sessionsA = $productA ? $productA->getProcessuses() : [];
        $stepsA    = $sessionA ? $sessionA->getSteps() : [];

        // --- CÔTÉ B ---
        $categoryBId = $request->query->get('category_b');
        $productBId  = $request->query->get('product_b');
        $sessionBId  = $request->query->get('session_b');
        $stepBId     = $request->query->get('step_b');

        $categoryB = $categoryBId ? $categoryRepository->find($categoryBId) : null;
        $productB  = $productBId ? $productRepository->find($productBId) : null;
        $sessionB  = $sessionBId ? $processusRepository->find($sessionBId) : null;
        $stepB     = $stepBId ? $stepRepository->find($stepBId) : null;

        $productsB = $categoryB ? $categoryB->getProducts() : [];
        $sessionsB = $productB ? $productB->getProcessuses() : [];
        $stepsB    = $sessionB ? $sessionB->getSteps() : [];

        // Calcul des statistiques
        $statsA = ($categoryA || $productA || $sessionA || $stepA)
            ? $this->calculateStats($categoryA, $productA, $sessionA, $stepA)
            : null;

        $statsB = ($categoryB || $productB || $sessionB || $stepB)
            ? $this->calculateStats($categoryB, $productB, $sessionB, $stepB)
            : null;

        return $this->render('main/compare.html.twig', [
            'categories' => $allCategories,

            // Côté A
            'categoryA' => $categoryA,
            'productA'  => $productA,
            'sessionA'  => $sessionA,
            'stepA'     => $stepA,
            'productsA' => $productsA,
            'sessionsA' => $sessionsA,
            'stepsA'    => $stepsA,

            // Côté B
            'categoryB' => $categoryB,
            'productB'  => $productB,
            'sessionB'  => $sessionB,
            'stepB'     => $stepB,
            'productsB' => $productsB,
            'sessionsB' => $sessionsB,
            'stepsB'    => $stepsB,

            'statsA'    => $statsA,
            'statsB'    => $statsB,
        ]);
    }

    /**
     * Calcule le total des gains, coûts et le solde selon la profondeur de sélection.
     */
    private function calculateStats(?Category $category, ?Product $product, ?Processus $session, $step): ?array
    {
        if (!$category && !$product && !$session) {
            return null;
        }

        $gains = 0;
        $costs = 0;
        $label = '';
        $level = '';

        if ($step) {
            $level = 'step';
            $label = $step->getName();
            if ($step->isGain()) {
                $gains += $step->getAmout();
            } else {
                $costs += $step->getAmout();
            }
        } elseif ($session) {
            $level = 'session';
            $label = $session->getName();
            $gains = $session->getGainsTotal();
            $costs = $session->getCostsTotal();
        } elseif ($product) {
            $level = 'product';
            $label = $product->getName();
            foreach ($product->getProcessuses() as $proc) {
                $gains += $proc->getGainsTotal();
                $costs += $proc->getCostsTotal();
            }
        } elseif ($category) {
            $level = 'category';
            $label = $category->getName();
            foreach ($category->getProducts() as $prod) {
                foreach ($prod->getProcessuses() as $proc) {
                    $gains += $proc->getGainsTotal();
                    $costs += $proc->getCostsTotal();
                }
            }
        }

        return [
            'level' => $level,
            'label' => $label,
            'gains' => $gains,
            'costs' => $costs,
            'solde' => $gains - $costs,
        ];
    }
}
