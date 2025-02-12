<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Ressources;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformer\CallbackTransformer; 

class ResourceType extends AbstractType
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
        ->add('image', FileType::class, [
            'label' => 'Télécharger une image',
            'constraints' => [
        new \Symfony\Component\Validator\Constraints\File([
            'maxSize' => '5M',
            'mimeTypes' => ['image/jpeg', 'image/png'],
            'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG/PNG).',
        ])
            ],
        ]);
       
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ressources::class,
        ]);
    }
}
