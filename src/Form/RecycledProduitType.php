<?php

namespace App\Form;

use App\Entity\RecyclageRapport;
use App\Entity\RecycledProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecycledProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('ProductName')
    ->add('Categorie')
    ->add('quantity')
    ->add('Description') // Add description field
    ->add('RecyclingMethod') // Add recycling method field
    ->add('MaterialType'); // Add material type field

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecycledProduit::class,
        ]);
    }
}
