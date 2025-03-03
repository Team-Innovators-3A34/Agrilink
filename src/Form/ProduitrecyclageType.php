<?php

namespace App\Form;

use App\Entity\Pointrecyclage;
use App\Entity\Produitrecyclage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitrecyclageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du produit",
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Entrez le nom du produit",
                ],
            ])

            ->add('quantite', TextType::class, [
                'label' => 'Quantite',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la quantite du produit',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Entrez une description du produit',
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
            ->add('pointderecyclage', EntityType::class, [
                'class' => Pointrecyclage::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produitrecyclage::class,
        ]);
    }
}
