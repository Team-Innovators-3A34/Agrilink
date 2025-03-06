<?php
// src/Form/ReponsesType.php

namespace App\Form;

use App\Entity\Reponses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponsesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de la réponse',
                'required' => false, // La contrainte est gérée via l'entité
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre réponse ici'
                ]
            ])
            ->add('solution', TextareaType::class, [
                'label' => 'Solution',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la solution'
                ]
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date de la réponse',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('isAuto', CheckboxType::class, [
                'label' => 'Réponse automatique',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                    'onchange' => 'toggleContentField()'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponses::class,
            // Si la réponse n'est pas automatique, on active aussi le groupe "manual"
            'validation_groups' => function(FormInterface $form) {
                $data = $form->getData();
                if ($data && $data->isAuto()) {
                    return ['Default'];
                }
                return ['Default', 'manual'];
            },
        ]);
    }
}
