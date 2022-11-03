<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            BooleanField::new('isBlocked'),
            BooleanField::new('isVerified'),
            ChoiceField::new('roles')->allowMultipleChoices()->setChoices([
                'Admin' => 'ROLE_ADMIN',
                'Individual' => 'ROLE_INDIVIDUAL',
                'Pro' => 'ROLE_PRO',
            ]),
            TextField::new('email'),
            TextField::new('username'),
            TextField::new('imageFile', 'Image')->setFormType(VichImageType::class)->onlyOnForms(),
            ImageField::new('avatar', 'Image')->setBasePath('/uploads/images/avatars')->onlyOnIndex(),
            TextField::new('phone'),
            TextField::new('name'),
            IntegerField::new('noSIRET'),
        ];
    }
}
