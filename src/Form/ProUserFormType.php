<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'required' => true,
            ])

            ->add('phone', null, [
                'required' => true,

            ])
            ->add('name', null, [
                'required' => true,

            ])
            ->add('noSIRET', null, [
                'required' => true,

            ])
            ->add('imageFile',VichImageType::class,['label' => 'photo de profil'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
