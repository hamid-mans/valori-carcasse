<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TourneeController extends AbstractController
{
    #[Route('/tournees', name: 'app_tournee_index', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        ProductRepository $produitRepository,
        CategoryRepository $categoryRepository
    ): Response {
        // 1. Récupération des catégories et des produits non catégorisés
        $categories = $categoryRepository->findAll();
        $uncategorizedProducts = $produitRepository->findBy(['category' => null]);

        // 2. Calcul des valeurs unitaires et coûts par produit (exprimés par tonne)
        $vraisProduits = $produitRepository->findAll();
        $produitsAffiches = [];

        foreach ($vraisProduits as $product) {
            $valeurCalculee = 0;

            foreach ($product->getProcessuses() as $processus) {
                $valeurCalculee += $processus->getSoldeFinal();
            }

            $produitsAffiches[$product->getId()] = [
                'nom'        => $product->getName(),
                'valeur'     => $valeurCalculee,        // € / Tonne
                'valeur_kg'  => $valeurCalculee / 1000, // € / Kg
                'cout'       => $valeurCalculee * 0.60, // € / Tonne
            ];
        }

        // Initialisation de la saisie
        $resultats = null;
        $saisie = [];
        foreach ($produitsAffiches as $id => $info) {
            $saisie[$id] = 0;
        }

        // 3. Traitement du Formulaire (POST)
        if ($request->isMethod('POST')) {
            $totalValeur = 0;
            $totalCout = 0;

            foreach ($produitsAffiches as $id => $info) {
                // Récupération de la quantité saisie en KG (accepte les décimaux)
                $quantiteKg = (float) str_replace(',', '.', $request->request->get('prod_' . $id, 0));
                $saisie[$id] = $quantiteKg;

                if ($quantiteKg > 0) {
                    // Conversion de KG en Tonnes pour le calcul (quantiteKg / 1000)
                    $tonnage = $quantiteKg / 1000;

                    $totalValeur += $info['valeur'] * $tonnage;
                    $totalCout += $info['cout'] * $tonnage;
                }
            }

            $resultats = [
                'total_valeur' => $totalValeur,
                'total_cout'   => $totalCout,
                'marge'        => $totalValeur - $totalCout,
            ];
        }

        return $this->render('tournee/index.html.twig', [
            'categories'            => $categories,
            'uncategorizedProducts' => $uncategorizedProducts,
            'produits'              => $produitsAffiches,
            'saisie'                => $saisie,
            'resultats'             => $resultats,
        ]);
    }
}
