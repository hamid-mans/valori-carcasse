<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Processus;
use App\Entity\Step;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Configuration des 3 catégories avec variations sur les étapes
        $categoriesConfig = [
            'Catégorie 1' => [
                'bovin' => [
                    'P01' => ['Coûts' => ["Standard administratif" => 15.00, "Logiciel gestion" => 4.00, "Assurance de base" => 5.00], 'Gains' => []],
                    'P02' => ['Coûts' => ["Planification transport" => 20.00, "Optimisation itinéraire" => 7.00], 'Gains' => []],
                    'P03' => ['Coûts' => ["Chauffeur poids lourd" => 50.00, "Carburant Diesel" => 75.00, "Péages autoroute" => 18.00, "Entretien camion" => 25.00], 'Gains' => ["Forfait enlèvement" => 240.00]],
                    'P04' => ['Coûts' => ["Agent quai" => 20.00, "Contrôle sanitaire P1" => 30.00, "Traçabilité lot" => 5.00], 'Gains' => []],
                    'P05' => ['Coûts' => ["Froid industriel" => 25.00, "Nettoyage quotidien" => 8.00], 'Gains' => []],
                    'P06' => ['Coûts' => ["Broyage mécanique" => 30.00, "Cuisson vapeur" => 60.00, "Stérilisation HP" => 40.00, "Électricité usine" => 70.00, "Main-d'œuvre ligne" => 85.00], 'Gains' => []],
                    'P07' => ['Coûts' => ["Station d'épuration" => 40.00, "Traitement vapeurs" => 20.00], 'Gains' => ["Recyclage eau" => 12.00, "Vente biogaz" => 35.00]],
                    'P08' => ['Coûts' => ["Silos de stockage" => 10.00, "Manutention" => 15.00], 'Gains' => []],
                    'P09' => ['Coûts' => ["Frais commerciaux" => 30.00, "Transport final" => 50.00], 'Gains' => ["Vente farine Cat 1" => 420.00, "Vente graisse brute" => 310.00]],
                    'P10' => ['Coûts' => ["Transestérification" => 40.00, "Réactifs chimiques" => 30.00], 'Gains' => ["Vente Biodiesel standard" => 280.00]],
                    'P11' => ['Coûts' => ["Gestion dossiers" => 5.00], 'Gains' => ["Subvention filière Cat 1" => 80.00]],
                ],
                'volaille' => [
                    'P01' => ['Coûts' => ["Accueil téléphonique" => 10.00, "Saisie ERP" => 3.00], 'Gains' => []],
                    'P02' => ['Coûts' => ["Dispatch bennes" => 15.00], 'Gains' => []],
                    'P03' => ['Coûts' => ["Chauffeur" => 40.00, "Carburant" => 50.00, "Désinfection renforcée" => 22.00], 'Gains' => ["Forfait enlèvement avicole" => 160.00]],
                    'P04' => ['Coûts' => ["Test Salmonelle" => 40.00, "Réception rapide" => 15.00], 'Gains' => []],
                    'P05' => ['Coûts' => ["Chambre froide dédiée" => 20.00, "Eau de lavage" => 12.00], 'Gains' => []],
                    'P06' => ['Coûts' => ["Hydrolyse plumes" => 55.00, "Séchage thermique" => 48.00, "Gaz industriel" => 85.00, "Personnel qualifié" => 75.00], 'Gains' => []],
                    'P07' => ['Coûts' => ["Traitement effluents lourds" => 50.00], 'Gains' => ["Compostage certifié" => 18.00]],
                    'P08' => ['Coûts' => ["Mise en Big-Bags" => 18.00], 'Gains' => []],
                    'P09' => ['Coûts' => ["Courtage" => 15.00, "Logistique grand angle" => 45.00], 'Gains' => ["Vente farine de plumes" => 380.00, "Vente huile de volaille" => 400.00]],
                    'P10' => ['Coûts' => ["Raffinage huile" => 35.00], 'Gains' => ["Vente ester volaille" => 260.00]],
                    'P11' => ['Coûts' => ["Audit sanitaire" => 8.00], 'Gains' => ["Aide soutien aviculture" => 60.00]],
                ],
            ],

            'Catégorie 2' => [
                'bovin' => [
                    'P01' => ['Coûts' => ["Secrétariat export" => 22.00, "Licences logicielles" => 8.00], 'Gains' => []],
                    'P02' => ['Coûts' => ["Optimisation flotte IA" => 12.00, "Management" => 18.00], 'Gains' => []],
                    'P03' => ['Coûts' => ["Chauffeur éco-conduite" => 52.00, "Gazole B100" => 85.00, "Taxes régionales" => 10.00], 'Gains' => ["Facturation au Km" => 310.00]],
                    'P04' => ['Coûts' => ["Labo microbiologique" => 50.00, "Tri automatique" => 25.00], 'Gains' => []],
                    'P05' => ['Coûts' => ["Brumisation désinfectante" => 14.00, "Stockage isotherme" => 30.00], 'Gains' => []],
                    'P06' => ['Coûts' => ["Broyage ultra-fin" => 42.00, "Séparation centrifugé" => 35.00, "Vapeur haute pression" => 75.00, "Maintenance préventive" => 45.00], 'Gains' => []],
                    'P07' => ['Coûts' => ["Filtration membranaire" => 60.00], 'Gains' => ["Cogénération électricité" => 45.00, "Revente vapeur local" => 30.00]],
                    'P08' => ['Coûts' => ["Silos sous azote" => 20.00, "Emballage étanche" => 15.00], 'Gains' => []],
                    'P09' => ['Coûts' => ["Marketing vert" => 40.00, "Export maritime" => 90.00], 'Gains' => ["Vente farine Premium" => 530.00, "Vente graisses filtrées" => 390.00]],
                    'P10' => ['Coûts' => ["Catalyse hétérogène" => 55.00], 'Gains' => ["Vente Biodiesel B100" => 340.00, "Vente Glycérine purifiée" => 65.00]],
                    'P11' => ['Coûts' => ["Consultant RSE" => 15.00], 'Gains' => ["Prime décarbonation" => 110.00]],
                ],
                'volaille' => [
                    'P01' => ['Coûts' => ["Gestion plateforme Web" => 14.00], 'Gains' => []],
                    'P02' => ['Coûts' => ["Planification urgente" => 25.00], 'Gains' => []],
                    'P03' => ['Coûts' => ["Chauffeur nuit" => 60.00, "Carburant" => 62.00, "Lavage benne haute pression" => 30.00], 'Gains' => ["Prestation collecte" => 210.00]],
                    'P04' => ['Coûts' => ["Analyse PCR rapide" => 65.00], 'Gains' => []],
                    'P05' => ['Coûts' => ["Ventilation renforcée" => 28.00], 'Gains' => []],
                    'P06' => ['Coûts' => ["Thermolyse poussée" => 70.00, "Séchage à bande" => 50.00, "Électricité verte" => 90.00], 'Gains' => []],
                    'P07' => ['Coûts' => ["Traitement azote/phosphore" => 68.00], 'Gains' => ["Vente digestat de méthanisation" => 25.00]],
                    'P08' => ['Coûts' => ["Conditionnement automatisé" => 22.00], 'Gains' => []],
                    'P09' => ['Coûts' => ["Export UE" => 70.00], 'Gains' => ["Vente farine Petfood Grade" => 460.00, "Vente graisses raffinées" => 450.00]],
                    'P10' => ['Coûts' => ["Purification ester" => 42.00], 'Gains' => ["Vente Bio-fioul" => 310.00]],
                    'P11' => ['Coûts' => ["Frais de dossier transition" => 10.00], 'Gains' => ["Aide FEDER Économie Circulaire" => 75.00]],
                ],
            ],

            'Catégorie 3' => [
                'bovin' => [
                    'P01' => ['Coûts' => ["Traitement d'urgence" => 25.00], 'Gains' => []],
                    'P02' => ['Coûts' => ["Sous-traitance logistique" => 35.00], 'Gains' => []],
                    'P03' => ['Coûts' => ["Location camion spécialisé" => 90.00, "Péages" => 22.00], 'Gains' => ["Forfait intervention d'urgence" => 350.00]],
                    'P04' => ['Coûts' => ["Inspection vétérinaire" => 55.00], 'Gains' => []],
                    'P05' => ['Coûts' => ["Consommation énergétique accrue" => 38.00], 'Gains' => []],
                    'P06' => ['Coûts' => ["Traitement haute température" => 80.00, "Maintenance lourde" => 60.00, "Gaz naturel" => 110.00], 'Gains' => []],
                    'P07' => ['Coûts' => ["Incinération résidus" => 75.00, "Taxe environnementale" => 30.00], 'Gains' => ["Chaleur valorisée usine" => 20.00]],
                    'P08' => ['Coûts' => ["Stockage sécurisé" => 25.00], 'Gains' => []],
                    'P09' => ['Coûts' => ["Logistique matières dangereuses" => 80.00], 'Gains' => ["Vente combustible alternatif" => 290.00, "Vente graisses industrielles" => 260.00]],
                    'P10' => ['Coûts' => ["Traitement chimique lourd" => 60.00], 'Gains' => ["Vente Bio-naphta" => 220.00]],
                    'P11' => ['Coûts' => ["Suivi conformité DREAL" => 20.00], 'Gains' => ["Compensation service public" => 120.00]],
                ],
                'volaille' => [
                    'P01' => ['Coûts' => ["Centre d'appel dédié" => 18.00], 'Gains' => []],
                    'P02' => ['Coûts' => ["Acheminement direct" => 28.00], 'Gains' => []],
                    'P03' => ['Coûts' => ["Transport étanche" => 70.00, "Carburant" => 55.00], 'Gains' => ["Redevance éleveur" => 190.00]],
                    'P04' => ['Coûts' => ["Contrôle sanitaire d'entrée" => 35.00], 'Gains' => []],
                    'P05' => ['Coûts' => ["Glace écaille / Refroidissement" => 24.00], 'Gains' => []],
                    'P06' => ['Coûts' => ["Cuisson-extrusive" => 65.00, "Pressage continu" => 40.00, "Fioul lourd" => 70.00], 'Gains' => []],
                    'P07' => ['Coûts' => ["Épuration physico-chimique" => 58.00], 'Gains' => ["Compost bio-stimulant" => 30.00]],
                    'P08' => ['Coûts' => ["Ensachage hermétique" => 16.00], 'Gains' => []],
                    'P09' => ['Coûts' => ["Transport local" => 40.00], 'Gains' => ["Vente farine d'engrais" => 270.00, "Vente huile technique" => 320.00]],
                    'P10' => ['Coûts' => ["Esterification acide" => 45.00], 'Gains' => ["Vente huile HVO" => 270.00]],
                    'P11' => ['Coûts' => ["Contrôle qualité externe" => 12.00], 'Gains' => ["Aide régionale valorisation" => 50.00]],
                ],
            ],
        ];

        // Création et persistance des objets
        foreach ($categoriesConfig as $catName => $products) {
            $category = new Category();
            $category->setName($catName);
            $manager->persist($category);

            foreach ($products as $prodType => $processusList) {
                $product = new Product();
                $product->setName(ucfirst($prodType));
                $product->setCategory($category);
                $manager->persist($product);

                foreach ($processusList as $procCode => $data) {
                    $processus = new Processus();
                    $processus->setName($procCode . ' - ' . $this->getProcessusName($procCode));
                    $processus->setProduct($product);
                    $processus->setCreatedAt(new \DateTimeImmutable());
                    $manager->persist($processus);

                    // Création des étapes de coût (isGain = false)
                    foreach ($data['Coûts'] as $stepName => $amount) {
                        $this->createStep($manager, $processus, $stepName, $amount, false);
                    }

                    // Création des étapes de gain (isGain = true)
                    foreach ($data['Gains'] as $stepName => $amount) {
                        $this->createStep($manager, $processus, $stepName, $amount, true);
                    }
                }
            }
        }

        $manager->flush();
    }

    private function createStep(ObjectManager $manager, Processus $processus, string $name, float $amount, bool $isGain): void
    {
        $step = new Step();
        $step->setName($name);

        if (method_exists($step, 'setAmout')) {
            $step->setAmout($amount);
        } else {
            $step->setAmount($amount);
        }

        $step->setIsGain($isGain);
        $step->setProcessus($processus);
        $manager->persist($step);
    }

    private function getProcessusName(string $code): string
    {
        $names = [
            'P01' => "Réception de la demande d'enlèvement",
            'P02' => "Organisation logistique",
            'P03' => "Collecte chez l'éleveur",
            'P04' => "Réception sur site",
            'P05' => "Stockage temporaire",
            'P06' => "Transformation industrielle",
            'P07' => "Gestion des déchets et effluents",
            'P08' => "Stockage des produits finis",
            'P09' => "Commercialisation",
            'P10' => "Production de biodiesel",
            'P11' => "Subventions et aides",
        ];

        return $names[$code] ?? "Processus";
    }
}
