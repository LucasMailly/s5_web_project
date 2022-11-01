<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('price')
            ->add('dateParution')
            ->add('category', ChoiceType::class, [
              'choices'=>['Vêtement'=>'Vêtement','Voiture'=>'Voiture','Jardinage'=>'Jardinage','Matériaux'=>'Matériaux','Mobilier'=>'Mobilier'],])
            ->add('negotiation')
            ->add('used')
            ->add('quantity')
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
