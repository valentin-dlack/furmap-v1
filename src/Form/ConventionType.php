<?php

namespace App\Form;

use App\Entity\Convention;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ConventionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('slug', TextType::class, [
                'attr' => [
                    'placeholder' => 'Slug',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 60,
                ],
                'label' => false,
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Name',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 60,
                ],
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'style' => 'height: 275px;',
                    'maxlength' => 750,
                ],
                'label' => false,
            ])
            ->add('last_year_made', TextType::class, [
                'attr' => [
                    'class' => 'js-flatpickr input input-bordered w-full',
                    'data-enable-time' => 'false',
                    'data-date-format' => 'd/m/Y',
                ],
                'label' => false,
                'mapped' => false,
            ])
            ->add('latitude', TextType::class, [
                'attr' => [
                    'class' => 'hidden',
                    'id' => 'longitude',
                    'value' => '',
                ],
                'label' => false,
                'required' => false,
            ])
            ->add('longitude', TextType::class, [
                'attr' => [
                    'class' => 'hidden',
                    'id' => 'longitude',
                    'value' => '',
                ],
                'label' => false,
                'required' => false,
            ])
            ->add('location_details', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Location Details',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'style' => 'height: 275px;',
                    'maxlength' => 750,
                ],
                'label' => false,
            ])
            ->add('attendance', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Attendance',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 7,
                ],
                'label' => false,
            ])
            ->add('logo', FileType::class, [
                'attr' => [
                    'class' => 'file-input w-full',
                ],
                'label' => false,
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (No GIFs)',
                    ])
                ],
            ])
            ->add('website', TextType::class, [
                'attr' => [
                    'placeholder' => 'Website',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 60,
                ],
                'label' => false,
            ])
            ->add('first_edition', TextType::class, [
                'attr' => [
                    'class' => 'js-flatpickr input input-bordered w-full',
                    'data-enable-time' => 'false',
                    'data-date-format' => 'd/m/Y',
                ],
                'label' => false,
                'mapped' => false,
            ])
            ->add('start_date', TextType::class, [
                'attr' => [
                    'class' => 'js-flatpickr input input-bordered w-full',
                    'data-enable-time' => 'false',
                    'data-date-format' => 'd/m/Y',
                ],
                'label' => false,
                'mapped' => false,
            ])
            ->add('end_date', TextType::class, [
                'attr' => [
                    'class' => 'js-flatpickr input input-bordered w-full',
                    'data-enable-time' => 'false',
                    'data-date-format' => 'd/m/Y',
                ],
                'label' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Convention::class,
        ]);
    }
}
