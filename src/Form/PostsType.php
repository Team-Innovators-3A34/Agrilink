<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('title', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter title'
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'required' => false,
                    'class' => 'form-control',
                    'placeholder' => 'What\'s on your mind?',
                    'rows' => 5
                ]
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Active' => 'active',
                    'Draft' => 'draft'
                ],
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('images', FileType::class, [
                'label' => 'Images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
