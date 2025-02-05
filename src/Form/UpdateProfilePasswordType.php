<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UpdateProfilePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'constraints' => new NotBlank(),
                'constraints' => new Assert\NotBlank(),
                'mapped' => false, // Not tied to the User entity
                'attr' => [
                    'class' => 'form-control',
                ]
            ])->add('newPassword', PasswordType::class, [
                'mapped' => false, // Not tied to the entity directly
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8, // Matches the entity validation
                        'minMessage' => 'The password must be at least {{ limit }} characters long.',
                        'max' => 4096, // Symfony's default password length constraint
                    ]),
                    new Regex([
                        'pattern' => '/\d/',
                        'message' => 'The password must contain at least one digit.',
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'The password must contain at least one uppercase letter.',
                    ]),
                ],
            ])->add('confirmNewPassword', PasswordType::class, [
                'mapped' => false, // Not tied to the entity directly
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8, // Matches the entity validation
                        'minMessage' => 'The password must be at least {{ limit }} characters long.',
                        'max' => 4096, // Symfony's default password length constraint
                    ]),
                    new Regex([
                        'pattern' => '/\d/',
                        'message' => 'The password must contain at least one digit.',
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'The password must contain at least one uppercase letter.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
