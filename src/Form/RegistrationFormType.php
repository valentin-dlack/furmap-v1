<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', TypeTextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter an email',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your email should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 180,
                ]),
            ],
            'attr' => [
                'placeholder' => 'you@site.com',
                'class' => 'w-full input input-bordered focus:input-primary',
            ],
            'label' => false,
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
            'attr' => [
                'class' => 'checkbox checkbox-primary',
            ],
            "label" => false,
        ])
        ->add('username', TypeTextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a username',
                ]),
                new Length([
                    'min' => 4,
                    'minMessage' => 'Your username should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 25,
                ]),
                new Regex([
                    'pattern' => '/^(?=[a-zA-Z0-9._]{3,25}$)(?!.*[_.]{2})[^_.].*[^.]$/',
                    'message' => 'Username can only contain letters, numbers, underscores and dots and cannot start or end with a dot',
                ]),
            ],
            'attr' => [
                'placeholder' => 'Username',
                'class' => 'w-full input input-bordered focus:input-primary',
            ],
            'label' => false,
        ])
        ->add('captcha', CaptchaType::class, [
            'label' => false,
            'attr' => [
                'class' => 'w-20 input input-bordered focus:input-primary ',
                'placeholder' => 'Enter the captcha',
            ],
            'invalid_message' => 'Invalid captcha',
        ])
        ->add('plainPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'attr' => [
                'autocomplete' => 'new-password',
                'class' => 'w-full input input-bordered focus:input-primary',
                'placeholder' => 'Password',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
            'label' => false,
        ])
        ->add('confirmPassword', PasswordType::class, [
            'mapped' => false,
            'attr' => [
                'autocomplete' => 'new-password',
                'class' => 'w-full input input-bordered focus:input-primary',
                'placeholder' => 'Confirm Password',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
            'label' => false,
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
