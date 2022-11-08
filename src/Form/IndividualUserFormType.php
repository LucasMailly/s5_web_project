<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class IndividualUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'required' => true,
            ])
            ->add('username', null, [
                'required' => true,
                'label' => 'Nom d\'utilisateur',
            ])
            ->add('phone', null, [
                'required' => true,
                'label' => 'numéro de téléphone',

            ])
            ->add('imageFile',VichImageType::class,['required' => false, 'download_label' => false, 'download_uri' => false, 'image_uri' => false, 'delete_label'=> 'Supprimer l\'image ? ','label'=> false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
