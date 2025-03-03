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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotNull;

class DemandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expire_date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'html5' => true,
                'empty_data' => null,
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
                'attr' => ['id' => 'message', 'class' => 'form-control']
            ])
            ->add('priorite', ChoiceType::class, [
                'label' => 'Priorité',
                'required' => true,
                'choices' => [
                    'Haute (100%)' => 100,
                    'Moyenne (70%)' => 70,
                    'Faible (30%)' => 30,
                ],
                'attr' => ['class' => 'form-control'],
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
