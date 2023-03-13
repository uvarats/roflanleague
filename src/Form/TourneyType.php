<?php

namespace App\Form;

use App\Entity\Discipline;
use App\Entity\Enum\TournamentType;
use App\Entity\Tourney;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название турнира',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание',
                'required' => false,
                'attr' => [
                    'rows' => 10,
                ],
            ])
            ->add('type', EnumType::class, [
                'class' => TournamentType::class,
                'mapped' => false,
                'label' => 'Тип турнира',
                'choice_label' => fn (TournamentType $choice) => match ($choice) {
                    TournamentType::DOUBLE_ELIMINATION => 'Двойное выбывание',
                    TournamentType::SINGLE_ELIMINATION => 'Олимпийская система',

                },
            ])
            ->add('discipline', EntityType::class, [
                'class' => Discipline::class,
                'choice_label' => 'name',
                'label' => 'Дисциплина'
            ])
            ->add('impactCoefficient', NumberType::class, [
                'label' => 'Коэффициент важности турнира'
            ])
            ->add('is_ranked', CheckboxType::class, [
                'label' => 'Рейтинговый',
                'attr' => [
                    'checked' => true,
                ],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Сохранить'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tourney::class,
        ]);
    }
}
