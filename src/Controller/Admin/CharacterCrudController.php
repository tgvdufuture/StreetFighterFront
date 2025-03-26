<?php

namespace App\Controller\Admin;

use App\Entity\Character;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class CharacterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Character::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('Name');
        yield NumberField::new('Strength');
        yield NumberField::new('Speed');
        yield NumberField::new('Durability');
        yield NumberField::new('Power');
        yield NumberField::new('Combat');
        yield AssociationField::new('user');
    }
}
