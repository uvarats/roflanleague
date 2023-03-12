<?php

namespace App\Form;

use App\Entity\Discipline;
use App\Entity\Enum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisciplineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название дисципилины',
            ])
            ->add('type', EnumType::class, [
                'class' => Enum\DisciplineType::class,
                'label' => 'Тип дисциплины',
                'choice_label' => fn (Enum\DisciplineType $choice) => match ($choice) {
                    Enum\DisciplineType::SPORTS => 'Спортивные дисциплины',
                    Enum\DisciplineType::ESPORTS => 'Киберспорт',
                    Enum\DisciplineType::MISC => 'Прочее',

                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discipline::class,
        ]);
    }
}
