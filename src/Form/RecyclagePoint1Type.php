<?php

    namespace App\Form;

    use App\Entity\RecyclageInvestors;
    use App\Entity\Type;
    use App\Entity\RecyclagePoint;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class RecyclagePoint1Type extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('Name')
                ->add('MaxCapacite')
                ->add('adresse')
          
                ->add('Types', EntityType::class, [
                    'class' => Type::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Select a Type', // Prevents sending null
                    'required' => true, // Ensures a value is selected
                ])
                
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => RecyclagePoint::class,
            ]);
        }
    }
