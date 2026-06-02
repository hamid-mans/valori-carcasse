<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProcessusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompareController extends AbstractController
{
    #[Route('/compare', name: 'app_compare')]
    public function index(Request $request, ProductRepository $productRepo, ProcessusRepository $processusRepo): Response
    {
        $products = $productRepo->findAll();

        $productId = $request->query->get('product_id');
        $id1 = $request->query->get('p1');
        $id2 = $request->query->get('p2');

        $selectedProduct = $productId ? $productRepo->find($productId) : null;
        $p1 = $id1 ? $processusRepo->find($id1) : null;
        $p2 = $id2 ? $processusRepo->find($id2) : null;

        $analysis = null;

        if ($p1 && $p2) {
            $analysis = [
                'diffGains' => $p2->getGainsTotal() - $p1->getGainsTotal(),
                'diffCosts' => $p2->getCostsTotal() - $p1->getCostsTotal(),
                'diffSolde' => $p2->getSoldeFinal() - $p1->getSoldeFinal(),
            ];
        }

        return $this->render('compare/index.html.twig', [
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'p1' => $p1,
            'p2' => $p2,
            'analysis' => $analysis,
        ]);
    }
}