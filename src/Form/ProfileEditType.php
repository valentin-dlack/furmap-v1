<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 60,
                ],
                'label' => false,
            ])
            ->add('username', TextType::class, [
                'attr' => [
                    'placeholder' => 'Username',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 30,
                ],
                'label' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Description',
                    'class' => 'w-full mx-auto input input-bordered focus:input-primary',
                    'style' => 'height: 275px;',
                ],
                'label' => false,
            ])
            ->add('avatar', FileType::class, [
                'attr' => [
                    'class' => 'file-input w-full',
                ],
                'label' => false,
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3072k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            //hidden longitute and latitude fields
            ->add('longitude', TextType::class, [
                'attr' => [
                    'class' => 'hidden',
                    'id' => 'longitude',
                    'value' => '',
                ],
                'label' => false,
                'required' => false,
            ])
            ->add('latitude', TextType::class, [
                'attr' => [
                    'class' => 'hidden',
                    'id' => 'latitude',
                    'value' => '',
                ],
                'label' => false,
                'required' => false,
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
