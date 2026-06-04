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
        $dateDebutStr = $request->query->get('date_debut');
        $dateFinStr = $request->query->get('date_fin');

        $dateDebut = $dateDebutStr ? new \DateTime($dateDebutStr . ' 00:00:00') : new \DateTime((new \DateTime())->format('Y') . '-01-01 00:00:00');
        $dateFin = $dateFinStr ? new \DateTime($dateFinStr . ' 23:59:59') : new \DateTime();

        $processusPeriod = $processusRepository->createQueryBuilder('p')
            ->leftJoin('p.product', 'prod')
            ->addSelect('prod')
            ->where('p.createdAt >= :debut')
            ->andWhere('p.createdAt <= :fin')
            ->setParameter('debut', $dateDebut)
            ->setParameter('fin', $dateFin)
            ->getQuery()
            ->getResult();

        // LOGIQUE DE CONSOLIDATION PAR PRODUIT
        $productStats = [];
        $globalGainsTotal = 0;
        $globalCostsTotal = 0;

        foreach ($processusPeriod as $p) {
            $productName = strtoupper($p->getProduct()->getName());

            if (!isset($productStats[$productName])) {
                $productStats[$productName] = [
                    'name' => $productName,
                    'gains' => 0,
                    'costs' => 0,
                    'solde' => 0,
                    'count' => 0
                ];
            }

            $productStats[$productName]['gains'] += $p->getGainsTotal();
            $productStats[$productName]['costs'] += $p->getCostsTotal();
            $productStats[$productName]['solde'] += $p->getSoldeFinal();
            $productStats[$productName]['count']++;

            $globalGainsTotal += $p->getGainsTotal();
            $globalCostsTotal += $p->getCostsTotal();
        }

        return $this->render('reporting/index.html.twig', [
            'productStats' => $productStats,
            'dateDebut' => $dateDebut->format('Y-m-d'),
            'dateFin' => $dateFin->format('Y-m-d'),
            'globalGainsTotal' => $globalGainsTotal,
            'globalCostsTotal' => $globalCostsTotal,
            'globalSoldeTotal' => $globalGainsTotal - $globalCostsTotal,
            'hasData' => !empty($processusPeriod)
        ]);
    }
}