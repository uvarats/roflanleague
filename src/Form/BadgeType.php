<?php

namespace App\Form;

use App\Entity\Badge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class BadgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название бейджа',
                'constraints' => [
                    new NotBlank(message: 'Название бейджа не должно быть пустым'),
                    new Length(
                        min: 3,
                        max: 25,
                        minMessage: 'Название бейджа не должно быть короче 3 символов',
                        maxMessage: 'Название бейджа не должно быть длиннее 25 символов',
                    ),
                ]
            ])
            ->add('text', TextType::class, [
                'label' => 'Текст',
                'constraints' => [
                    new NotBlank(message: 'Описание не должно быть пустым'),
                    new Length(
                        min: 3,
                        max: 150,
                        minMessage: 'Описание не должно быть короче 3 символов',
                        maxMessage: 'Описание не должно быть длиннее 150 символов',
                    ),
                ]
            ])
            ->add('priority', NumberType::class, [
                'label' => 'Приоритет',
                'constraints' => [
                    new NotBlank(),
                    new Range(
                        notInRangeMessage: 'Значение приоритета должно быть в диапазоне {{ min }} - {{ max }}',
                        min: 0,
                        max: 32000
                    ),
                ],
            ])
            ->add('hexCode', ColorType::class, [
                'label' => 'Hex-код',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Badge::class,
        ]);
    }
}
