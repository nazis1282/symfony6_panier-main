<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civilite', ChoiceType::class, [
                'choices' => [
                    'Madame' => 'madame',
                    'Monsieur' => 'monsieur'
                ],
                'label' => 'Civilité'
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre nom',
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre prénom',
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                    ])
                ]    
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank([
                        'message' =>'Veuiller renseigner votre email',
                    ])
                ]

            ])
            ->add('object', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Sujet',
                'constraints' => [
                    new NotBlank([
                        'message' =>'Veuiller renseigner un sujet',
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\ ]+$/u',
                        'match'=> true
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Votre message',
                'constraints' => [
                    new NotBlank([
                        'message' =>'Veuiller renseigner un message',
                    ]),
                    new Regex([
                        'pattern' => '/^[\p{Latin}\p{N}\s*\'\-\,\.\?\!\ ]+$/u',
                        'match'=> true
                    ])
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
