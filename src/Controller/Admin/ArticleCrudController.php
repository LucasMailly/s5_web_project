<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('libelle'),
            TextField::new('imageFile', 'Image')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('imgArticle', 'Image')->setBasePath('/uploads/images/articles')->onlyOnIndex(),
            AssociationField::new('author'),
            MoneyField::new('price')->setCurrency('EUR'),
            DateField::new('dateParution'),
            TextField::new('category'),
            BooleanField::new('negotiation'),
            BooleanField::new('opportunity'),
            IntegerField::new('quantity'),
        ];
    }
}