<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ProcessusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportingController extends AbstractController
{
    #[Route('/reporting', name: 'app_reporting')]
    public function index(Request $request, ProductRepository $productRepository, ProcessusRepository $processusRepository): Response
    {
        $products = $productRepository->findAll();

        // Récupération des filtres
        $productId = $request->query->get('product_id');
        $dateDebutStr = $request->query->get('date_debut');
        $dateFinStr = $request->query->get('date_fin');

        // Définition de dates par défaut si non renseignées (Ex: Année en cours)
        $dateDebut = $dateDebutStr ? new \DateTime($dateDebutStr . ' 00:00:00') : new \DateTime((new \DateTime())->format('Y') . '-01-01 00:00:00');
        $dateFin = $dateFinStr ? new \DateTime($dateFinStr . ' 23:59:59') : new \DateTime();

        $selectedProduct = $productId ? $productRepository->find($productId) : ($products ? $products[0] : null);

        $processusPeriod = [];
        if ($selectedProduct) {
            // Récupération des processus du produit filtrés par la période choisie
            $processusPeriod = $processusRepository->createQueryBuilder('p')
                ->where('p.product = :product')
                ->andWhere('p.createdAt >= :debut')
                ->andWhere('p.createdAt <= :fin')
                ->orderBy('p.createdAt', 'ASC')
                ->setParameter('product', $selectedProduct)
                ->setParameter('debut', $dateDebut)
                ->setParameter('fin', $dateFin)
                ->getQuery()
                ->getResult();
        }

        return $this->render('reporting/index.html.twig', [
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'processusPeriod' => $processusPeriod,
            'dateDebut' => $dateDebut->format('Y-m-d'),
            'dateFin' => $dateFin->format('Y-m-d'),
        ]);
    }
}