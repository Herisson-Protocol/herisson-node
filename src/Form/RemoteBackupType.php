<?php

namespace Herisson\Form;

use Herisson\Entity\RemoteBackup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoteBackupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('size')
            ->add('nbBookmarks')
            ->add('created_at')
            ->add('friend_id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RemoteBackup::class,
        ]);
    }
}
