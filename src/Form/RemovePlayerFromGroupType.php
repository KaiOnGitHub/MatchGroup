<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemovePlayerFromGroupType extends AbstractType
{
    static string $deleteFromGroupAction = '/matchgroup/remove-player';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('playerId')
            ->add('matchGroupShortId')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'action' => self::$deleteFromGroupAction,
        ]);
    }
}
