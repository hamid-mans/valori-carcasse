<?php

namespace App\Form;

use App\Entity\Step;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'étape',
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'id' => 'step_name' // <--- ID FORCÉ
                ]
            ])
            ->add('amout', NumberType::class, [
                'label' => 'Montant (€)',
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'id' => 'step_amount', // <--- ID FORCÉ
                    'step' => '0.01'
                ]
            ])
            ->add('isGain', ChoiceType::class, [
                'label' => 'Type de flux',
                'choices' => [
                    'Coût / Charge (-)' => false,
                    'Gain / Valorisation (+)' => true,
                ],
                'attr' => ['class' => 'select select-bordered w-full']
            ])
            ->add('typeFrais', ChoiceType::class, [
                'label' => 'Catégorie de frais',
                'mapped' => false,
                'required' => false,
                'choices' => [
                    'Frais standard / Autre' => 'standard',
                    'Frais de Carburant (Logistique / Collecte)' => 'carburant',
                ],
                'attr' => [
                    'class' => 'select select-bordered w-full',
                    'id' => 'select_type_frais' // <--- ID FORCÉ
                ]
            ])
            ->add('carburantType', ChoiceType::class, [
                'label' => 'Carburant (Prix temps réel Paris)',
                'mapped' => false,
                'required' => false,
                'choices' => [
                    '-- Sélectionner un carburant --' => '',
                    'Gazole (Diesel)' => 'Gazole',
                    'Sans Plomb 95 (E10)' => 'SP95',
                    'Sans Plomb 98' => 'SP98',
                ],
                'attr' => [
                    'class' => 'select select-bordered w-full',
                    'id' => 'select_carburant' // <--- ID FORCÉ
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Step::class,
        ]);
    }
}