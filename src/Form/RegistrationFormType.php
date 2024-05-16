<?php

namespace App\Form;


use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre email'
                    ])
                ]
            ]) 
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                    ]),
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre nom'
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prénom',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                    ]),
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre prénom'
                    ])
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'class' =>'form-control'
                ],
                'label' => 'Adresse',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\,\.\ ]+$/u',
                        'match'=> true
                    ]),
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre adresse'
                    ])
                ]
            ])
            ->add('zipcode', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '4 chiffres'
                ],
                'label' => 'Code postale',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{4}+$/',
                        'match'=> true
                    ])
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Ville',
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                    ]),
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre ville'
                    ])
                ]
            ])
            ->add('RGPDConsent', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez donner votre accord au traitement vos données',
                    ]),
                ],
                'label' => "Je suis d'accord avec traitement mes données"
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Mot de passe ne corresponde pas",
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe', 'attr' => ['placeholder' => "Entrer 8 caractères"]],
                'second_options' => ['label' => 'Confirmer mot de passe', 'attr' => ['placeholder' => "Confirmer votre mot de passe"]],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => "Votre mot de passe doit être minimum 8 caractères",
                        'max' => 50                   
                        ])
                ]

            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
