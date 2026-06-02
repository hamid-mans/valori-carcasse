<?php

namespace App\Form;

use App\Entity\Processus;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la modélisation / session',
                'attr' => ['class' => 'input input-bordered w-full text-base']
            ])
            ->add('createdAt', DateType::class, [
                'label' => 'Date d\'analyse',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'attr' => ['class' => 'input input-bordered w-full text-base']
            ])
            ->add('product', EntityType::class, [
                'label' => 'Produit rattaché',
                'class' => Product::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'select select-bordered w-full text-base']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Processus::class,
        ]);
    }
}