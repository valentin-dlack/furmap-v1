<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'autocomplete' => 'email',
                    'class' => 'w-full input input-bordered focus:input-primary',
                    'placeholder' => 'your@email.com',
                    'autofocus' => 'autofocus',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                ],
                'label' => false,
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'w-full input input-bordered focus:input-primary mt-2',
                    'placeholder' => 'Enter the captcha',
                ],
                'invalid_message' => 'Invalid captcha',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
