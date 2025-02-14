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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
class ResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('owner_id_id', TextType::class, [
            'required' => true,
            'label' => 'ID du propriétaire',
            'attr' => [
                'class' => 'form-control', 
            ],
        ])
        ->add('name_r', TextType::class, [
            'required' => true,
            'label' => 'Nom de ressource',
            'attr' => [
                'class' => 'form-control', 
            ],
        ])
        
        ->add('type', ChoiceType::class, [
            'choices' => [
                'materiel' => 'materiel',
                'terrain' => 'terrain',
            ],
            'expanded' => true, 
            'multiple' => false,
            'label' => 'Type',
            'required' => true,
            'attr' => [
                'class' => 'form-control', 
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
            'expanded' => true, 
            'multiple' => false,
            'label' => 'Statut',
            'required' => true,
            'attr' => [
                'class' => 'form-control', 
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
