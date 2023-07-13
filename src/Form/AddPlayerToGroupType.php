<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPlayerToGroupType extends AbstractType
{
    static string $joinGroupAction = '/matchgroup/add-player';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('matchGroupShortId')
            ->add('email')
            ->add('wantsNotifications', null, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'action' => self::$joinGroupAction,
        ]);
    }
}
