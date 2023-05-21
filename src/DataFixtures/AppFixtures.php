<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        $admin = new User();
        $admin->setName('admin');
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPhone('1112233444');
        $admin->setPassword('$2y$13$jf0jSAVZLtsHIXObaS5ba.UiagagmUWzI/g3SL3lLZxt/cKNI0ip.');
        $manager->persist($admin);
        $manager->flush();
    }
}
