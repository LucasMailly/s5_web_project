<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('price')
            ->add('dateParution')
            ->add('category', ChoiceType::class, [
              'choices'=>['Vêtement'=>'Vêtement','Voiture'=>'Voiture','Jardinage'=>'Jardinage','Matériaux'=>'Matériaux','Mobilier'=>'Mobilier'],])
            ->add('negotiation')
            ->add('opportunity')
            ->add('quantity')
            ->add('author')
            ->add('favoriteUsers')
            ->add('imageFile', VichImageType::class, [
                'label' => 'Ajouter un article',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
