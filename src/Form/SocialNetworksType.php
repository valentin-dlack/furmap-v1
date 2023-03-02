<?php

namespace App\Form;

use App\Entity\SocialNetworks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocialNetworksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('discord', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'TagName#1234',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 38,
                ],
                'label' => false,
            ])
            ->add('skype', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Skype username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 50,
                ],
                'label' => false,
            ])
            ->add('deviantart', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'DeviantArt username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 20,
                ],
                'label' => false,
            ])
            ->add('soundcloud', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'SoundCloud username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 25,
                ],
                'label' => false,
            ])
            ->add('reddit', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Reddit u',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 20,
                ],
                'label' => false,
            ])
            ->add('furaffinity', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'FurAffinity username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 50,
                ],
                'label' => false,
            ])
            ->add('twitter', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Twitter username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 15,
                ],
                'label' => false,
            ])
            ->add('facebook', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Facebook username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 50,
                ],
                'label' => false,
            ])
            ->add('youtube', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Youtube channel/@username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 30,
                ],
                'label' => false,
            ])
            ->add('instagram', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Instagram username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 30,
                ],
                'label' => false,
            ])
            ->add('telegram', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Telegram username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 32,
                ],
                'label' => false,
            ])
            ->add('steam', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Steam username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 32,
                ],
                'label' => false,
            ])
            ->add('twitch', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Twitch username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 25,
                ],
                'label' => false,
            ])
            ->add('vk', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'VK username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 48,
                ],
                'label' => false,
            ])
            ->add('github', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Github username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 39,
                ],
                'label' => false,
            ])
            ->add('gitlab', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'GitLab username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 50,
                ],
                'label' => false,
            ])
            ->add('tiktok', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'TikTok username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 24,
                ],
                'label' => false,
            ])
            ->add('tumblr', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Tumblr username',
                    'class' => 'w-full xl:w-96 mx-auto input input-bordered focus:input-primary',
                    'maxlength' => 32,
                ],
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialNetworks::class,
        ]);
    }
}
