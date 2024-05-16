<?php

namespace App\Form;

use App\Entity\Products;
use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = new Categories();


        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nom' 
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Déscription'
            ])
            ->add('price' , TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Prix'
            ])
            ->add('stock', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Stock'    
            ])
            ->add('image', FileType::class, [
                'mapped' => false, // siginfie que le champ est associé à une propriété et qu'il sera inséré en BDD
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M', // taille maximum d'upload d'image
                        'mimeTypes' => [ // format accepté
                            'image/jpeg',
                            'image/png',
                            'image/jpg'],
                        'mimeTypesMessage' => 'Formats autorisés : jpg/jpeg/png'
                    ])
                ]

            ])
            ->add('categories', EntityType::class, [
                'label' => "Choisir une catégorie",
                'class' => Categories::class, // On précise de quelle entité vient ce champ
                'choice_label' => 'name' // on définit la valeur qui apparaitra dans la liste déroulante

                ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
