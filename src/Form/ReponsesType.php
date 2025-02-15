<?php

namespace App\Form;

use App\Entity\Reponses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponsesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de la réponse',
                'required' => true, // Champ obligatoire
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez votre réponse ici']
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date de la réponse',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('isAuto', CheckboxType::class, [
                'label' => 'Réponse automatique',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ]);

        // Écouteur pour mettre à jour le champ content en fonction de isAuto
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data && $data->isAuto()) {
                $form->add('content', TextareaType::class, [
                    'label' => 'Contenu de la réponse',
                    'required' => true,
                    'attr' => ['class' => 'form-control', 'readonly' => true]
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponses::class,
        ]);
    }
}
