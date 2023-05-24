<?php

namespace App\Controller\Admin;

use App\Entity\Skins;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SkinsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Skins::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
