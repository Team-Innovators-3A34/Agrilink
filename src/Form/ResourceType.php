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
            ->add('name_r', TextType::class, [
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
                'expanded' => false,
                'multiple' => false,
                'label' => 'Type',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            ->add('marque', TextType::class, [
                'label' => 'La marque ',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('prix_location', TextType::class, [
                'label' => 'Prix de location par heure',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            ->add('superficie', TextType::class, [
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
                'expanded' => false,
                'multiple' => false,
                'label' => 'Statut',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            ->add('image', FileType::class, [
                'mapped' => false,
                'label' => 'Télécharger une image',
                'attr' => [
                    'class' => 'form-control',
                ],
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
