<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PredictType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Region', ChoiceType::class, [
                'choices' => [
                    'Ouest' => 'West',
                    'Sud' => 'South',
                    'Nord' => 'North',
                    'Est' => 'East'
                ],
                'placeholder' => 'Sélectionnez une région',
                'attr' => ['class' => 'form-select']
            ])
            ->add('Soil_Type', ChoiceType::class, [
                'choices' => [
                    'Sableux' => 'Sandy',
                    'Argileux' => 'Clay',
                    'Limon' => 'Loam',
                    'Silt' => 'Silt' // Ajout de "Silt"
                ],
                'placeholder' => 'Sélectionnez un type de sol',
                'attr' => ['class' => 'form-select']
            ])
            ->add('Crop', ChoiceType::class, [
                'choices' => [
                    'Coton' => 'Cotton',
                    'Riz' => 'Rice',
                    'Orge' => 'Barley',
                    'Blé' => 'Wheat',
                    'Soja' => 'Soybean'
                ],
                'placeholder' => 'Sélectionnez une culture',
                'attr' => ['class' => 'form-select']
            ])
            ->add('Rainfall_mm', NumberType::class, [
                'html5' => true,
                'required' => true,
                'attr' => ['class' => 'form-control', 'step' => '0.1', 'min' => '0']
            ])
            ->add('Temperature_Celsius', NumberType::class, [
                'html5' => true,
                'required' => true,
                'attr' => ['class' => 'form-control', 'step' => '0.1', 'min' => '-50', 'max' => '60']
            ])
            ->add('Fertilizer_Used', CheckboxType::class, [
                'required' => false,
                'label' => 'Engrais utilisé',
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('Irrigation_Used', CheckboxType::class, [
                'required' => false,
                'label' => 'Irrigation utilisée',
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('Weather_Condition', ChoiceType::class, [
                'choices' => [
                    'Ensoleillé' => 'Sunny',
                    'Nuageux' => 'Cloudy',
                    'Pluvieux' => 'Rainy'
                ],
                'placeholder' => 'Sélectionnez la météo',
                'attr' => ['class' => 'form-select']
            ])
            ->add('Days_to_Harvest', NumberType::class, [
                'html5' => true,
                'required' => true,
                'attr' => ['class' => 'form-control', 'min' => '1']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Prédire',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }
}
