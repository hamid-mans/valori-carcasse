<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportingController extends AbstractController
{
    #[Route('/reporting', name: 'app_reporting', methods: ['GET'])]
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

        // Filtrage des produits selon la catégorie sélectionnée
        $availableProducts = $selectedCategory
            ? $productRepository->findBy(['category' => $selectedCategory])
            : $productRepository->findAll();

        // Initialisation des données analytiques
        $kpis = null;

        if ($selectedProduct) {
            $chiffreAffaires = 0;
            $coutsVariables = 0;
            $coutsFixes = 0;
            $volumeProcessusKg = 0;

            // 1. Extraction des coûts, gains et volumes depuis les étapes du produit
            foreach ($selectedProduct->getProcessuses() as $proc) {
                $solde = $proc->getSoldeFinal();
                $montant = abs($solde);
                $nature = strtolower($proc->getName() ?? '');

                // Extraction de la quantité si elle existe sur l'étape (en kg)
                if (method_exists($proc, 'getQuantite') && $proc->getQuantite() !== null) {
                    $volumeProcessusKg += (float) $proc->getQuantite();
                }

                $isCost = $solde < 0;

                if (!$isCost) {
                    $chiffreAffaires += $montant;
                } else {
                    if (str_contains($nature, 'carburant') || str_contains($nature, 'energie') || str_contains($nature, 'électricité')) {
                        $coutsVariables += $montant;
                    } else {
                        $coutsFixes += $montant;
                    }
                }
            }

            // 2. Gestion prioritaire du Volume (Saisie Utilisateur > Cumul Processus > Secours 1000 kg)
            $userVolumeInput = $request->query->get('volume');
            if ($userVolumeInput !== null && $userVolumeInput !== '') {
                $volumeKg = max(1, (float) $userVolumeInput);
            } elseif ($volumeProcessusKg > 0) {
                $volumeKg = $volumeProcessusKg;
            } else {
                $volumeKg = 1000;
            }

            // Fallback sur les charges fixes si aucune n'est enregistrée (pour les démos)
            $coutsFixes = $coutsFixes > 0 ? $coutsFixes : 350;

            // 3. Calculs Financiers Unitaire (en €/kg)
            $prixVenteMoyenU = $volumeKg > 0 ? ($chiffreAffaires / $volumeKg) : 0;
            $coutVariableU   = $volumeKg > 0 ? ($coutsVariables / $volumeKg) : 0;

            // Marge sur Coût Variable Unitaire (MCVU) et Taux de MCVU
            $mcvu    = $prixVenteMoyenU - $coutVariableU;
            $tauxMcv = $chiffreAffaires > 0 ? (($chiffreAffaires - $coutsVariables) / $chiffreAffaires) : 0;

            // 4. Indicateurs de Rentabilité
            $rentabiliteUnitaire   = $volumeKg > 0 ? (($chiffreAffaires - ($coutsVariables + $coutsFixes)) / $volumeKg) : 0;
            $seuilRentabiliteKg    = $mcvu > 0 ? ($coutsFixes / $mcvu) : 0;
            $seuilRentabiliteEuros = $tauxMcv > 0 ? ($coutsFixes / $tauxMcv) : 0;
            $margeSecuriteKg       = $volumeKg - $seuilRentabiliteKg;
            $indiceSecurite        = $volumeKg > 0 ? ($margeSecuriteKg / $volumeKg) * 100 : 0;
            $prixMinimum           = $volumeKg > 0 ? (($coutsVariables + $coutsFixes) / $volumeKg) : 0;
            $ratioCoutsRevenus     = $chiffreAffaires > 0 ? (($coutsVariables + $coutsFixes) / $chiffreAffaires) * 100 : 0;

            $kpis = [
                'rentabilite_actuelle'  => $rentabiliteUnitaire,
                'seuil_rentabilite_kg'  => $seuilRentabiliteKg,
                'seuil_rentabilite_t'   => $seuilRentabiliteKg / 1000, // Clé attendue par la vue Twig
                'seuil_rentabilite_eur' => $seuilRentabiliteEuros,
                'volume_actuel'         => $volumeKg,
                'volume_actuel_t'       => $volumeKg / 1000,
                'marge_securite_kg'     => $margeSecuriteKg,
                'marge_securite_t'      => $margeSecuriteKg / 1000,
                'indice_securite'       => $indiceSecurite,
                'prix_minimum'          => $prixMinimum,
                'ratio_couts_revenus'   => $ratioCoutsRevenus,
                'chiffre_affaires'      => $chiffreAffaires,
                'couts_fixes'           => $coutsFixes,
                'couts_variables'       => $coutsVariables,
            ];
        }

        return $this->render('reporting/index.html.twig', [
            'categories'        => $categories,
            'availableProducts' => $availableProducts,
            'selectedCategory'  => $selectedCategory,
            'selectedProduct'   => $selectedProduct,
            'kpis'              => $kpis,
        ]);
    }
}
