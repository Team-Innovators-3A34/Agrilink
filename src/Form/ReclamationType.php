<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_user', TextType::class)
            ->add('mail_user', EmailType::class)  
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('date', null, ['widget' => 'single_text'])
            ->add('type', TextType::class)
            ->add('image', FileType::class, [
                'label' => 'Image (Drag and drop)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'dropzone']
            ])
            ->add('status', TextType::class, [
                'attr' => ['class' => 'form-control', 'readonly' => true], 
            ])
            ->add('id_user', HiddenType::class, [
                'data' => 0, 
                
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
