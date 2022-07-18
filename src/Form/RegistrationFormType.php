<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Sodium\add;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Ник',
                'attr' => [
                  'maxlength' => 24,
                ],
                'constraints' => [
                    new NotBlank(message: 'Ник не может быть пустым!'),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Ник должен быть длинной хотя бы в 3 символа!',
                        'max' => 24,
                        'maxMessage' => 'Ник не должен быть длиннее 24 символ (рекомендовано до 16 символов)!'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'constraints' => [
                    new NotBlank(message: 'Заполните поле почты!'),
                    new Email([
                        'message' => 'Некорректная почта.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Пароль должен совпадать!',
                'mapped' => false,
                'first_options' => [
                    'label' => 'Пароль',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Пожалуйста, введите пароль.',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Пароль должен быть длиной хотя бы {{ limit }} символов.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Повтор пароля',
                ]
            ])
//            ->add('plainPassword', PasswordType::class, [
//                // instead of being set onto the object directly,
//                // this is read and encoded in the controller
//                'mapped' => false,
//                'attr' => ['autocomplete' => 'new-password'],
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
