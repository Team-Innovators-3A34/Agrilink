<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;  // Add this import
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;


class PostsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Article' => 'article',
                    'Question' => 'question',
                    'Discussion' => 'discussion'
                ],
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
          
        /*     ->add('title', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control']
            ]) */
         
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'empty_data' => '',  // This is important
                'constraints' => [
                    new NotBlank(['message' => 'Title cannot be empty']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Title must be at least {{ limit }} characters long',
                        'maxMessage' => 'Title cannot be longer than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'empty_data' => '',  // This is important
                'constraints' => [
                    new NotBlank(['message' => 'Description cannot be empty']),
                    new Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => 'Description must be at least {{ limit }} characters long',
                        'maxMessage' => 'Description cannot be longer than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 'active',
                    'Draft' => 'draft'
                ],
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
          
            ->add('fileUpload', FileType::class, [
                'label' => 'File (Image/PDF/Video)',
                'mapped' => true,
                'required' => false,
                'data_class' => null,
                  
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
            'empty_data' => new Posts(), 
        ]);
    }
}