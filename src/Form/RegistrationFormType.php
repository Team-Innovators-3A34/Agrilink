<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use ReCaptcha\ReCaptcha;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['placeholder' => 'Your Last Name'],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide your last name.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s-]+$/',
                        'message' => 'The last name should only contain letters, spaces, or hyphens.'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['placeholder' => 'Your First Name'],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide your first name.']),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s-]+$/',
                        'message' => 'The first name should only contain letters, spaces, or hyphens.'
                    ])
                ]
            ])
            ->add('adresse', TextType::class, [
                'attr' => ['placeholder' => 'Your Address'],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide your address.']),
                ]
            ])
            ->add('telephone', TextType::class, [
                'attr' => ['placeholder' => 'Phone Number'],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide your phone number.']),
                    new Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => 'The phone number must be exactly 8 digits.'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Your Email'],
                'constraints' => [
                    new NotBlank(['message' => 'Please provide your email.'])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false, // Not tied to the entity directly
                'attr' => ['autocomplete' => 'new-password'],
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
            ])->add('captcha', EWZRecaptchaType::class, [
                'mapped' => false,  // Empêche Symfony de chercher cette propriété dans l'entité User
                'constraints' => [
                    new RecaptchaTrue(),
                ],
            ])->add('user_type', ChoiceType::class, [
                'mapped' => false, // This ensures it’s not mapped to the `User` entity automatically
                'choices' => [
                    'Agricultural' => 'agriculture',
                    'Resource Investor' => 'agricultural_resource_investor',
                    'Recycling Investor' => 'recycling_investor',
                ],
                'attr' => [
                    'class' => 'style2-input ps-5 form-control text-grey-900 font-xsss fw-600',
                ],
                'placeholder' => 'Select User Type',
                'constraints' => [
                    new NotBlank(['message' => 'Please select your user type.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
