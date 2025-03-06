<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('adresse')
            ->add('telephone')
            ->add('nom')
            ->add('prenom')
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control mb-0 p-3 h100 bg-greylight lh-16',
                    'rows' => 5,
                    'placeholder' => 'Write your message...',
                    'spellcheck' => 'false',
                    'required' => false,
                ],
            ])
            ->add('bio')
            ->add('country')
            ->add('city')
            ->add('longitude')
            ->add('latitude')
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, or GIF).',
                    ])
                ],
                'attr' => [
                    'class' => 'input-file',
                ],
                'label' => false,
            ])->add('is2FA');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
