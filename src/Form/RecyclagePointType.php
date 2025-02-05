<?php

namespace App\Form;

use App\Entity\RecyclageInvestors;
use App\Entity\RecyclagePoint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecyclagePointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name')
            ->add('MaxCapacite')
            ->add('adresse')
            ->add('Longitude')
            ->add('Latitude')
            ->add('Owner', EntityType::class, [
                'class' => RecyclageInvestors::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecyclagePoint::class,
        ]);
    }
}
