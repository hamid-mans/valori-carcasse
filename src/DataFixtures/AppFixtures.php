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
        // =========================================================================
        // 1. PRODUIT : BOVIN
        // =========================================================================
        $bovin = new Product();
        $bovin->setName('Bovin');
        $manager->persist($bovin);

        // P01 - Réception de la demande d'enlèvement (Bovin)
        $p1 = $this->createProcessus($manager, $bovin, "P01 - Réception de la demande d'enlèvement");
        $p1Coûts = [
            "Temps assistant(e) exploitation" => 14.50, "Téléphonie" => 1.80, "Logiciel métier" => 3.20,
            "Édition documents réglementaires" => 1.20, "Encadrement administratif" => 7.50, "ERP" => 2.80,
            "Informatique" => 2.00, "Loyer bureaux" => 5.50, "Assurance" => 3.90
        ];
        foreach ($p1Coûts as $name => $amount) { $this->createStep($manager, $p1, $name, $amount, false); }

        // P02 - Organisation logistique (Bovin)
        $p2 = $this->createProcessus($manager, $bovin, "P02 - Organisation logistique");
        $p2Coûts = ["Temps exploitant transport" => 21.00, "Logiciel optimisation tournées" => 6.20, "Management logistique" => 11.50, "Infrastructure informatique" => 4.50];
        foreach ($p2Coûts as $name => $amount) { $this->createStep($manager, $p2, $name, $amount, false); }

        // P03 - Collecte chez l'éleveur (Bovin)
        $p3 = $this->createProcessus($manager, $bovin, "P03 - Collecte chez l'éleveur");
        $p3Coûts = [
            "Salaire chauffeur" => 48.00, "Gasoil" => 72.50, "Péages" => 15.00, "Désinfection" => 9.00,
            "Pneus" => 6.50, "Entretien" => 12.00, "Réparations" => 14.00, "Amortissement du camion" => 38.00,
            "Assurance véhicule" => 8.90, "Taxe à l'essieu" => 2.10
        ];
        foreach ($p3Coûts as $name => $amount) { $this->createStep($manager, $p3, $name, $amount, false); }
        $this->createStep($manager, $p3, "Facturation à l'intervention", 250.00, true);

        // P04 - Réception sur site (Bovin)
        $p4 = $this->createProcessus($manager, $bovin, "P04 - Réception sur site");
        $p4Coûts = ["Agents de réception" => 19.00, "Personnel qualité" => 13.50, "Analyses sanitaires" => 35.00, "Consommables laboratoire" => 7.20, "Logiciel traçabilité" => 4.00, "Maintenance équipements" => 10.80];
        foreach ($p4Coûts as $name => $amount) { $this->createStep($manager, $p4, $name, $amount, false); }

        // P05 - Stockage temporaire (Bovin)
        $p5 = $this->createProcessus($manager, $bovin, "P05 - Stockage temporaire");
        $p5Coûts = ["Main-d'œuvre" => 15.00, "Électricité" => 26.50, "Eau" => 8.00, "Désinfection" => 6.90, "Chambre froide" => 22.00, "Bâtiments" => 12.50];
        foreach ($p5Coûts as $name => $amount) { $this->createStep($manager, $p5, $name, $amount, false); }

        // P06 - Transformation industrielle (Bovin)
        $p6 = $this->createProcessus($manager, $bovin, "P06 - Transformation industrielle");
        $p6Coûts = [
            "Broyage" => 25.00, "Cuisson" => 58.00, "Stérilisation" => 45.00, "Séparation des graisses" => 19.00,
            "Séparation matières solides" => 15.50, "Séparation eau" => 12.00, "Pressage" => 24.00, "Séchage" => 40.00,
            "Raffinage" => 17.50, "Conducteurs de ligne" => 52.00, "Techniciens" => 38.00, "Maintenance" => 31.00,
            "Gaz" => 80.00, "Électricité" => 65.00, "Lubrifiants" => 6.20, "Produits chimiques" => 15.00,
            "Filtres" => 9.50, "Pièces d'usure" => 21.00, "Tests réglementaires et analyses" => 28.00,
            "Amortissement du broyeur" => 16.00, "Amortissement du cuiseur" => 30.00, "Amortissement Presses" => 19.50,
            "Amortissement Sécheurs" => 24.00, "Amortissement Chaudières" => 15.00, "Amortissement Réseaux vapeur" => 10.00,
            "Service qualité" => 18.00, "HSE" => 14.00
        ];
        foreach ($p6Coûts as $name => $amount) { $this->createStep($manager, $p6, $name, $amount, false); }

        // P07 - Gestion des déchets et effluents (Bovin)
        $p7 = $this->createProcessus($manager, $bovin, "P07 - Gestion des déchets et effluents");
        $p7Coûts = ["Traitement effluents/ eaux usées" => 48.00, "Transport déchets" => 26.00, "Redevances environnementales" => 19.00, "Contrôles réglementaires" => 14.50];
        foreach ($p7Coûts as $name => $amount) { $this->createStep($manager, $p7, $name, $amount, false); }
        $p7Recettes = ["Valorisation énergétique - Chaleur" => 15.00, "Valorisation énergétique - Vapeur" => 28.00, "Valorisation énergétique - Électricité" => 20.00, "Valorisation énergétique - Biométhane" => 42.00];
        foreach ($p7Recettes as $name => $amount) { $this->createStep($manager, $p7, $name, $amount, true); }

        // P08 - Stockage des produits finis (Bovin)
        $p8 = $this->createProcessus($manager, $bovin, "P08 - Stockage des produits finis");
        $p8Coûts = ["Main-d'œuvre logistique" => 18.00, "Contrôles qualité" => 9.50, "Emballages" => 8.00, "Big bags" => 12.00, "Silos" => 4.50, "Cuves" => 3.80, "Assurance stock" => 5.90];
        foreach ($p8Coûts as $name => $amount) { $this->createStep($manager, $p8, $name, $amount, false); }

        // P09 - Commercialisation (Bovin)
        $p9 = $this->createProcessus($manager, $bovin, "P09 - Commercialisation");
        $p9Coûts = ["Commercial" => 25.00, "Transport client" => 62.00, "Commission intermédiaire" => 15.00, "Administration des ventes" => 9.00, "ERP" => 3.50, "Assurance-crédit" => 7.00];
        foreach ($p9Coûts as $name => $amount) { $this->createStep($manager, $p9, $name, $amount, false); }
        $p9Recettes = ["Vente de farines animales" => 450.00, "Vente de graisses animales" => 340.00, "Vente d'engrais organiques" => 90.00, "Vente de combustibles alternatifs" => 70.00];
        foreach ($p9Recettes as $name => $amount) { $this->createStep($manager, $p9, $name, $amount, true); }

        // P10 - Production de biodiesel (Bovin)
        $p10 = $this->createProcessus($manager, $bovin, "P10 - Production de biodiesel");
        $p10Coûts = ["Réactifs chimiques" => 36.00, "Énergie" => 44.00, "Main-d'œuvre" => 28.00, "Amortissement unité biodiesel" => 48.00, "Maintenance" => 19.00];
        foreach ($p10Coûts as $name => $amount) { $this->createStep($manager, $p10, $name, $amount, false); }
        $p10Recettes = ["Vente biodiesel" => 310.00, "Vente glycérine" => 50.00];
        foreach ($p10Recettes as $name => $amount) { $this->createStep($manager, $p10, $name, $amount, true); }

        // P11 - Subventions et aides (Bovin)
        $p11 = $this->createProcessus($manager, $bovin, "P11 - Subventions et aides");
        $p11Coûts = ["Gestion administrative" => 5.00, "Audit" => 6.50];
        foreach ($p11Coûts as $name => $amount) { $this->createStep($manager, $p11, $name, $amount, false); }
        $p11Recettes = ["Aides publiques" => 30.00, "Aides sanitaires" => 45.00, "Soutien aux filières d'équarrissage" => 60.00, "Subventions environnementales" => 35.00, "Soutien économie circulaire" => 25.00, "Aides énergie renouvelable" => 18.00];
        foreach ($p11Recettes as $name => $amount) { $this->createStep($manager, $p11, $name, $amount, true); }


        // =========================================================================
        // 2. PRODUIT : VOLAILLE (Nouveau produit avec prix adaptés et asymétriques)
        // =========================================================================
        $volaille = new Product();
        $volaille->setName('Volaille');
        $manager->persist($volaille);

        // P01 - Réception de la demande d'enlèvement (Volaille)
        $p1v = $this->createProcessus($manager, $volaille, "P01 - Réception de la demande d'enlèvement");
        $p1vCoûts = [
            "Temps assistant(e) exploitation" => 12.00, "Téléphonie" => 1.50, "Logiciel métier" => 3.20,
            "Édition documents réglementaires" => 1.00, "Encadrement administratif" => 6.00, "ERP" => 2.80,
            "Informatique" => 2.00, "Loyer bureaux" => 5.50, "Assurance" => 3.00
        ];
        foreach ($p1vCoûts as $name => $amount) { $this->createStep($manager, $p1v, $name, $amount, false); }

        // P02 - Organisation logistique (Volaille)
        $p2v = $this->createProcessus($manager, $volaille, "P02 - Organisation logistique");
        $p2vCoûts = ["Temps exploitant transport" => 18.00, "Logiciel optimisation tournées" => 6.20, "Management logistique" => 9.00, "Infrastructure informatique" => 4.50];
        foreach ($p2vCoûts as $name => $amount) { $this->createStep($manager, $p2v, $name, $amount, false); }

        // P03 - Collecte chez l'éleveur (Volaille - Moins cher que le Bovin au transport unitaire)
        $p3v = $this->createProcessus($manager, $volaille, "P03 - Collecte chez l'éleveur");
        $p3vCoûts = [
            "Salaire chauffeur" => 42.00, "Gasoil" => 58.00, "Péages" => 10.00, "Désinfection bennes" => 14.00, // Désinfection plus stricte
            "Pneus" => 5.00, "Entretien" => 10.00, "Réparations" => 9.50, "Amortissement camion benne" => 30.00,
            "Assurance véhicule" => 7.50, "Taxe à l'essieu" => 1.80
        ];
        foreach ($p3vCoûts as $name => $amount) { $this->createStep($manager, $p3v, $name, $amount, false); }
        $this->createStep($manager, $p3v, "Facturation forfaitaire groupée", 180.00, true);

        // P04 - Réception sur site (Volaille)
        $p4v = $this->createProcessus($manager, $volaille, "P04 - Réception sur site");
        $p4vCoûts = ["Agents de réception" => 16.50, "Personnel qualité" => 13.50, "Analyses salmonelle et sanitaires" => 45.00, "Consommables laboratoire" => 8.50, "Logiciel traçabilité" => 4.00, "Maintenance équipements" => 8.00];
        foreach ($p4vCoûts as $name => $amount) { $this->createStep($manager, $p4v, $name, $amount, false); }

        // P05 - Stockage temporaire (Volaille)
        $p5v = $this->createProcessus($manager, $volaille, "P05 - Stockage temporaire");
        $p5vCoûts = ["Main-d'œuvre quai" => 14.00, "Électricité" => 22.00, "Eau et lavage" => 11.00, "Désinfection continue" => 9.50, "Chambre froide" => 18.00, "Bâtiments" => 12.50];
        foreach ($p5vCoûts as $name => $amount) { $this->createStep($manager, $p5v, $name, $amount, false); }

        // P06 - Transformation industrielle (Volaille - Processus lourd et énergivore, broyage plus rapide)
        $p6v = $this->createProcessus($manager, $volaille, "P06 - Transformation industrielle");
        $p6vCoûts = [
            "Broyage fin" => 15.00, "Cuisson continue" => 64.00, "Stérilisation thermique" => 48.00, "Séparation des graisses" => 22.00,
            "Séparation plumes et solides" => 28.50, "Séparation eau" => 14.00, "Pressage des tourteaux" => 20.00, "Séchage thermique" => 45.00,
            "Raffinage graisses" => 16.00, "Conducteurs de ligne" => 52.00, "Techniciens maintenance" => 38.00, "Maintenance broyeurs" => 24.00,
            "Gaz industriel" => 95.00, "Électricité usine" => 70.00, "Lubrifiants" => 5.50, "Produits de traitement" => 12.00,
            "Filtres à odeurs" => 18.00, "Pièces d'usure et grilles" => 16.00, "Analyses qualité process" => 22.00,
            "Amortissement hydrolyseur plumes" => 35.00, "Amortissement du cuiseur" => 30.00, "Amortissement Presses" => 19.50,
            "Amortissement Sécheurs" => 24.00, "Amortissement Chaudières" => 15.00, "Amortissement Traitement vapeurs" => 14.00,
            "Service qualité" => 18.00, "HSE" => 14.50
        ];
        foreach ($p6vCoûts as $name => $amount) { $this->createStep($manager, $p6v, $name, $amount, false); }

        // P07 - Gestion des déchets et effluents (Volaille)
        $p7v = $this->createProcessus($manager, $volaille, "P07 - Gestion des déchets et effluents");
        $p7vCoûts = ["Traitement station d'épuration" => 55.00, "Transport boues" => 22.00, "Redevances agence de l'eau" => 24.00, "Contrôles rejets" => 14.50];
        foreach ($p7vCoûts as $name => $amount) { $this->createStep($manager, $p7v, $name, $amount, false); }
        $p7vRecettes = ["Valorisation biogaz - Chaleur" => 18.00, "Valorisation biogaz - Électricité" => 25.00, "Valorisation résidus - Compostage" => 14.00];
        foreach ($p7vRecettes as $name => $amount) { $this->createStep($manager, $p7v, $name, $amount, true); }

        // P08 - Stockage des produits finis (Volaille)
        $p8v = $this->createProcessus($manager, $volaille, "P08 - Stockage des produits finis");
        $p8vCoûts = ["Main-d'œuvre ensachage" => 16.00, "Analyses conformité lots" => 9.50, "Emballages et protections" => 9.00, "Big bags farine" => 14.00, "Silos stockage" => 4.50, "Cuves stockage huile" => 4.00, "Assurance vol et incendie" => 5.00];
        foreach ($p8vCoûts as $name => $amount) { $this->createStep($manager, $p8v, $name, $amount, false); }

        // P09 - Commercialisation (Volaille - Marché à forte valeur sur les graisses de volaille)
        $p9v = $this->createProcessus($manager, $volaille, "P09 - Commercialisation");
        $p9vCoûts = ["Frais commerciaux" => 20.00, "Logistique cargaison client" => 55.00, "Commissions courtiers" => 12.00, "Administration des ventes" => 9.00, "Licence ERP" => 3.50, "Assurance-crédit" => 6.00];
        foreach ($p9vCoûts as $name => $amount) { $this->createStep($manager, $p9v, $name, $amount, false); }
        $p9vRecettes = [
            "Vente farine de plumes et volaille" => 390.00,
            "Vente graisse de volaille (Petfood / Tech)" => 410.00, // Plus valorisé que le bovin sur certains segments
            "Vente d'amendements organiques" => 75.00
        ];
        foreach ($p9vRecettes as $name => $amount) { $this->createStep($manager, $p9v, $name, $amount, true); }

        // P10 - Production de biodiesel (Volaille)
        $p10v = $this->createProcessus($manager, $volaille, "P10 - Production de biodiesel");
        $p10vCoûts = ["Méthanol et réactifs ester" => 38.00, "Énergie process" => 42.00, "Main-d'œuvre dédiée" => 28.00, "Amortissement outil" => 48.00, "Maintenance préventive" => 17.00];
        foreach ($p10vCoûts as $name => $amount) { $this->createStep($manager, $p10v, $name, $amount, false); }
        $p10vRecettes = ["Vente ester méthylique (biodiesel)" => 290.00, "Vente glycérine brute" => 45.00];
        foreach ($p10vRecettes as $name => $amount) { $this->createStep($manager, $p10v, $name, $amount, true); }

        // P11 - Subventions et aides (Volaille)
        $p11v = $this->createProcessus($manager, $volaille, "P11 - Subventions et aides");
        $p11vCoûts = ["Suivi dossiers" => 4.50, "Audits indépendants" => 6.50];
        foreach ($p11vCoûts as $name => $amount) { $this->createStep($manager, $p11v, $name, $amount, false); }
        $p11vRecettes = ["Aides publiques filière" => 25.00, "Fonds de soutien sanitaire" => 35.00, "Bonus valorisation circulaire" => 40.00, "Aides décarbonation" => 20.00];
        foreach ($p11vRecettes as $name => $amount) { $this->createStep($manager, $p11v, $name, $amount, true); }


        // =========================================================================
        // EXECUTION EN BASE DE DONNÉES
        // =========================================================================
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
        // Gestion dynamique de l'erreur historique amount/amout
        if (method_exists($step, 'setAmout')) {
            $step->setAmout($amount);
        } else {
            $step->setAmount($amount);
        }
        $step->setIsGain($isGain);
        $step->setProcessus($processus);
        $manager->persist($step);
    }
}