<?php

namespace Herisson\Form;

use Herisson\Entity\Friend;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FriendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url')
            ->add('alias')
            //->add('name')
            //->add('email')
            //->add('public_key')
            //->add('is_active')
            //->add('is_youwant')
            //->add('is_wantsyou')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Friend::class,
        ]);
    }
}
