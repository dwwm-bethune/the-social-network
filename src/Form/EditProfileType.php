<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', null, [
                'label' => 'Prénom',
            ])
            ->add('username', null, [
                'label' => 'Pseudo',
            ])
            ->add('avatarFile', FileType::class, [
                'mapped' => false,
                'label' => 'Avatar',
            ])
            ->add('birthday', null, [
                'label' => 'Date de naissance',
                'years' => range(date('Y'), 1900),
            ])
            ->add('biography', null, [
                'label' => 'Biographie',
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
