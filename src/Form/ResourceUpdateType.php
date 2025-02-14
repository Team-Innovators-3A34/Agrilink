<?php

namespace App\Form;

use App\Entity\Ressources;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;



class ResourceUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('owner_id_id', TextType::class, [
            'required' => true,
            'label' => 'ID du propriétaire',
            'attr' => [
                'class' => 'form-control', // Applique la classe CSS au champ
            ],
        ])
        ->add('type', ChoiceType::class, [
            'choices' => [
                'Terrain' => 'terrain',
                'Matériel' => 'materiel',
            ],
            'label' => 'Type de ressource',
            'label_attr' => [
                'class' => 'mont-font fw-600 font-xssss', // Applique la classe CSS au label
            ],
            'attr' => [
                'class' => 'form-control', // Applique la classe CSS au select
                'onchange' => 'togglePrixLocation()', // Pour la fonction JavaScript
                'required' => true, // Rendre le champ requis
            ],
        ])
        ->add('description', TextareaType::class, [
            'required' => false,
            'label' => 'Description',
            'attr' => [
                'class' => 'form-control', // Applique la classe CSS au textarea
            ],
        ])
        ->add('status', ChoiceType::class, [
            'choices' => [
                'disponible' => 'disponible',
                'indisponible' => 'indisponible',
            ],
            'expanded' => true, // Affichage sous forme de boutons radio
            'multiple' => false,
            'label' => 'Statut',
            'required' => true,
            'attr' => [
                'class' => 'form-control', // Applique la classe CSS au champ status
            ],
        ])
        ->add('adresse', TextType::class, [
            'required' => true,
            'label' => 'Adresse',
            'attr' => [
                'class' => 'form-control', 
            ],
        ])
        
        ->add('marque', TextType::class, [
            'required' => true,
            'label' => 'La marque ',
            'attr' => [
                'class' => 'form-control', 
            ],
        ])
        ->add('prix_location', TextType::class, [
            'required' => true,
            'label' => 'Prix de location par heure',
            'attr' => [
                'class' => 'form-control', 
            ],
        ])

        ->add('superficie', TextType::class, [
            'required' => true,
            'label' => 'La superficie',
            'attr' => [
                'class' => 'form-control', 
            ],
        ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressources::class,
        ]);
    }
}
