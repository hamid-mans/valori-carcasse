<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Processus;
use App\Entity\Step;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Création du produit unique Bovin
        $bovin = new Product();
        $bovin->setName('Bovin');
        $manager->persist($bovin);


        // ==========================================
        // ÉTAPE 1 – Réception de la demande d'enlèvement
        // ==========================================
        $p1 = $this->createProcessus($manager, $bovin, "P01 - Réception de la demande d'enlèvement");

        $p1Coûts = [
            "Temps assistant(e) exploitation" => 14.50,
            "Téléphonie" => 1.80,
            "Logiciel métier" => 3.20,
            "Édition documents réglementaires" => 1.20,
            "Encadrement administratif" => 7.50,
            "ERP" => 2.80,
            "Informatique" => 2.00,
            "Loyer bureaux" => 5.50,
            "Assurance" => 3.90
        ];
        foreach ($p1Coûts as $name => $amount) {
            $this->createStep($manager, $p1, $name, $amount, false);
        }


        // ==========================================
        // ÉTAPE 2 – Organisation logistique
        // ==========================================
        $p2 = $this->createProcessus($manager, $bovin, "P02 - Organisation logistique");

        $p2Coûts = [
            "Temps exploitant transport" => 21.00,
            "Logiciel optimisation tournées" => 6.20,
            "Management logistique" => 11.50,
            "Infrastructure informatique" => 4.50
        ];
        foreach ($p2Coûts as $name => $amount) {
            $this->createStep($manager, $p2, $name, $amount, false);
        }


        // ==========================================
        // ÉTAPE 3 – Collecte chez l'éleveur
        // ==========================================
        $p3 = $this->createProcessus($manager, $bovin, "P03 - Collecte chez l'éleveur");

        $p3Coûts = [
            "Salaire chauffeur" => 48.00,
            "Gasoil" => 72.50,
            "Péages" => 15.00,
            "Désinfection" => 9.00,
            "Pneus" => 6.50,
            "Entretien" => 12.00,
            "Réparations" => 14.00,
            "Amortissement du camion" => 38.00,
            "Assurance véhicule" => 8.90,
            "Taxe à l'essieu" => 2.10
        ];
        foreach ($p3Coûts as $name => $amount) {
            $this->createStep($manager, $p3, $name, $amount, false);
        }
        // Recette de l'étape 3
        $this->createStep($manager, $p3, "Facturation à l'intervention", 250.00, true);


        // ==========================================
        // ÉTAPE 4 – Réception sur site
        // ==========================================
        $p4 = $this->createProcessus($manager, $bovin, "P04 - Réception sur site");

        $p4Coûts = [
            "Agents de réception" => 19.00,
            "Personnel qualité" => 13.50,
            "Analyses sanitaires" => 35.00,
            "Consommables laboratoire" => 7.20,
            "Logiciel traçabilité" => 4.00,
            "Maintenance équipements" => 10.80
        ];
        foreach ($p4Coûts as $name => $amount) {
            $this->createStep($manager, $p4, $name, $amount, false);
        }


        // ==========================================
        // ÉTAPE 5 – Stockage temporaire
        // ==========================================
        $p5 = $this->createProcessus($manager, $bovin, "P05 - Stockage temporaire");

        $p5Coûts = [
            "Main-d'œuvre" => 15.00,
            "Électricité" => 26.50,
            "Eau" => 8.00,
            "Désinfection" => 6.90,
            "Chambre froide" => 22.00,
            "Bâtiments" => 12.50
        ];
        foreach ($p5Coûts as $name => $amount) {
            $this->createStep($manager, $p5, $name, $amount, false);
        }


        // ==========================================
        // ÉTAPE 6 – Transformation industrielle
        // ==========================================
        $p6 = $this->createProcessus($manager, $bovin, "P06 - Transformation industrielle");

        $p6Coûts = [
            "Broyage" => 25.00,
            "Cuisson" => 58.00,
            "Stérilisation" => 45.00,
            "Séparation des graisses" => 19.00,
            "Séparation matières solides" => 15.50,
            "Séparation eau" => 12.00,
            "Pressage" => 24.00,
            "Séchage" => 40.00,
            "Raffinage" => 17.50,
            "Conducteurs de ligne" => 52.00,
            "Techniciens" => 38.00,
            "Maintenance" => 31.00,
            "Gaz" => 80.00,
            "Électricité" => 65.00,
            "Lubrifiants" => 6.20,
            "Produits chimiques" => 15.00,
            "Filtres" => 9.50,
            "Pièces d'usure" => 21.00,
            "Tests réglementaires et analyses" => 28.00,
            "Amortissement du broyeur" => 16.00,
            "Amortissement du cuiseur" => 30.00,
            "Amortissement Presses" => 19.50,
            "Amortissement Sécheurs" => 24.00,
            "Amortissement Chaudières" => 15.00,
            "Amortissement Réseaux vapeur" => 10.00,
            "Service qualité" => 18.00,
            "HSE" => 14.00
        ];
        foreach ($p6Coûts as $name => $amount) {
            $this->createStep($manager, $p6, $name, $amount, false);
        }


        // ==========================================
        // ÉTAPE 7 – Gestion des déchets et effluents
        // ==========================================
        $p7 = $this->createProcessus($manager, $bovin, "P07 - Gestion des déchets et effluents");

        $p7Coûts = [
            "Traitement effluents/ eaux usées" => 48.00,
            "Transport déchets" => 26.00,
            "Redevances environnementales" => 19.00,
            "Contrôles réglementaires" => 14.50
        ];
        foreach ($p7Coûts as $name => $amount) {
            $this->createStep($manager, $p7, $name, $amount, false);
        }

        $p7Recettes = [
            "Valorisation énergétique - Chaleur" => 15.00,
            "Valorisation énergétique - Vapeur" => 28.00,
            "Valorisation énergétique - Électricité" => 20.00,
            "Valorisation énergétique - Biométhane" => 42.00
        ];
        foreach ($p7Recettes as $name => $amount) {
            $this->createStep($manager, $p7, $name, $amount, true);
        }


        // ==========================================
        // ÉTAPE 8 – Stockage des produits finis
        // ==========================================
        $p8 = $this->createProcessus($manager, $bovin, "P08 - Stockage des produits finis");

        $p8Coûts = [
            "Main-d'œuvre logistique" => 18.00,
            "Contrôles qualité" => 9.50,
            "Emballages" => 8.00,
            "Big bags" => 12.00,
            "Silos" => 4.50,
            "Cuves" => 3.80,
            "Assurance stock" => 5.90
        ];
        foreach ($p8Coûts as $name => $amount) {
            $this->createStep($manager, $p8, $name, $amount, false);
        }


        // ==========================================
        // ÉTAPE 9 – Commercialisation
        // ==========================================
        $p9 = $this->createProcessus($manager, $bovin, "P09 - Commercialisation");

        $p9Coûts = [
            "Commercial" => 25.00,
            "Transport client" => 62.00,
            "Commission intermédiaire" => 15.00,
            "Administration des ventes" => 9.00,
            "ERP" => 3.50,
            "Assurance-crédit" => 7.00
        ];
        foreach ($p9Coûts as $name => $amount) {
            $this->createStep($manager, $p9, $name, $amount, false);
        }

        $p9Recettes = [
            "Vente de farines animales" => 450.00,
            "Vente de graisses animales" => 340.00,
            "Vente d'engrais organiques" => 90.00,
            "Vente de combustibles alternatifs" => 70.00
        ];
        foreach ($p9Recettes as $name => $amount) {
            $this->createStep($manager, $p9, $name, $amount, true);
        }


        // ==========================================
        // ÉTAPE 10 – Production de biodiesel
        // ==========================================
        $p10 = $this->createProcessus($manager, $bovin, "P10 - Production de biodiesel");

        $p10Coûts = [
            "Réactifs chimiques" => 36.00,
            "Énergie" => 44.00,
            "Main-d'œuvre" => 28.00,
            "Amortissement unité biodiesel" => 48.00,
            "Maintenance" => 19.00
        ];
        foreach ($p10Coûts as $name => $amount) {
            $this->createStep($manager, $p10, $name, $amount, false);
        }

        $p10Recettes = [
            "Vente biodiesel" => 310.00,
            "Vente glycérine" => 50.00
        ];
        foreach ($p10Recettes as $name => $amount) {
            $this->createStep($manager, $p10, $name, $amount, true);
        }


        // ==========================================
        // ÉTAPE 11 – Subventions et aides
        // ==========================================
        $p11 = $this->createProcessus($manager, $bovin, "P11 - Subventions et aides");

        $p11Coûts = [
            "Gestion administrative" => 5.00,
            "Audit" => 6.50
        ];
        foreach ($p11Coûts as $name => $amount) {
            $this->createStep($manager, $p11, $name, $amount, false);
        }

        $p11Recettes = [
            "Aides publiques" => 30.00,
            "Aides sanitaires" => 45.00,
            "Soutien aux filières d'équarrissage" => 60.00,
            "Subventions environnementales" => 35.00,
            "Soutien économie circulaire" => 25.00,
            "Aides énergie renouvelable" => 18.00
        ];
        foreach ($p11Recettes as $name => $amount) {
            $this->createStep($manager, $p11, $name, $amount, true);
        }

        $manager->flush();
    }

    /**
     * Helper pour créer un Processus lié au Produit
     */
    private function createProcessus(ObjectManager $manager, Product $product, string $name): Processus
    {
        $processus = new Processus();
        $processus->setName($name);
        $processus->setProduct($product);
        $processus->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($processus);

        return $processus;
    }

    /**
     * Helper pour créer une Step liée au Processus
     */
    private function createStep(ObjectManager $manager, Processus $processus, string $name, float $amount, bool $isGain): void
    {
        $step = new Step();
        $step->setName($name);
        $step->setAmout($amount);
        $step->setIsGain($isGain);
        $step->setProcessus($processus); // Liaison correcte
        $manager->persist($step);
    }
}