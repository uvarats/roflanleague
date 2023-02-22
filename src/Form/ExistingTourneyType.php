<?php

declare(strict_types=1);

namespace App\Form;

use App\Dto\ExistingTourney;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExistingTourneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('challongeTourneyUrl', TextType::class, [
            'label' => 'ID турнира на платформе Challonge',
            'required' => true,
        ])->add('submit', SubmitType::class, [
            'label' => 'Добавить'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExistingTourney::class,
        ]);
    }
}