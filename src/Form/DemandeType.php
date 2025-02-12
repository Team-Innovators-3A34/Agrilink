<?php

namespace App\Form;

use App\Entity\Demandes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotNull;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('user_id_id',IntegerType::class, [
            'required' => true,
            'label' => 'ID de user',
            'attr' => [
                'class' => 'form-control']
                
                ])
                ->add('expire_date', DateType::class, [
                    'widget' => 'single_text',
                    'required' => true,
                    'html5' => true,
                    'constraints' => [
                        new NotNull([
                            'message' => 'La date d\'expiration est obligatoire.',
                        ]),
                    ],
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Date d\'expiration',
                ])
            
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => true,
                'attr' => ['id' => 'message']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre la demande'
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Demandes::class,
        ]);
    }
}
