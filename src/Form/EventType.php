<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom de l'événement",
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Entrez le nom de l'événement",
                ],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime', // Ensures Symfony returns a DateTime object
                'label' => "Date de l'événement",
                'required' => true, // Ensures client-side validation
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date'
                ],
                'empty_data' => (new \DateTime())->format('Y-m-d'), // Prevents empty submission
            ])

            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'adresse de l\'événement',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Entrez une description de l\'événement',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (Fichier)',
                'mapped' => false,  // Important: prevents Symfony from trying to store the File object in the entity
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
            ->add('nbr_places', IntegerType::class, [
                'label' => 'Nombre de places disponibles',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'placeholder' => 'Entrez le nombre des participants',
                ],
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la longitude',
                ],
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la latitude',
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom', // Affiche le nom de la catégorie au lieu de l’ID
                'label' => 'Catégorie',
                'required' => true,
                'placeholder' => 'Sélectionnez une catégorie',

            ])
            ->add('type', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Type d\'événement',
                'required' => true,
                'choices' => [
                    'En ligne' => 'en_ligne',
                    'Présentiel' => 'presentiel',
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ]);


        // Adjust dynamic validation for the image field
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $eventData = $event->getData();
            $form = $event->getForm();

            if (!$eventData || null === $eventData->getId()) {
                // If creating a new event, make the image field required
                $form->add('image', FileType::class, [
                    'label' => 'Image (Fichier)',
                    'mapped' => false,
                    'required' => true,
                    'attr' => [
                        'class' => 'form-control image-file',
                    ],
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'L\'image est obligatoire.',
                        ]),
                        new Assert\File([
                            'maxSize' => '2M',
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'],
                            'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF, JPG).',
                        ]),
                    ],
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
