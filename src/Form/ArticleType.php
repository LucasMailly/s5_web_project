<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'required' => true,
            ])
            ->add('price', null, [
                'attr' => ['min' => 0, 'step' => 0.01],
                'required' => true,
            ])
            ->add('dateParution')
            ->add('negotiation')
            ->add('used')
            ->add('quantity', null, [
                'attr' => [
                    'min' => 0,
                ],
                'required' => true,
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Ajouter un article',
                'required' => false,
            ])
            ->add('category', null, [
                'class' => Category::class,
                'choice_label' => 'name',
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
