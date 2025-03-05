<?php

namespace App\Form;

use App\Entity\Pointrecyclage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PointrecyclageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('name', TextType::class, [
                'label' => "Nom du point de recyclage",
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Entrez le nom de votre point de recyclage",
                ],
            ])



            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'adresse du point de recyclage',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Entrez une description du point de recyclage',
                ],
            ])

            ->add('image', FileType::class, [
                'label' => 'Image (Fichier)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control image-file',
                ],
                'constraints' => [

                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF, JPG).',
                    ]),
                ],
            ])
            // The longitude and latitude fields are hidden and not displayed in the form
            ->add('longitude', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'display:none;',
                ],
            ])
            ->add('latitude', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'display:none;',
                ],
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pointrecyclage::class,
        ]);
    }
}
