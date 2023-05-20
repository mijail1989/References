<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Zenstruck\Foundry\ModelFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method        User|Proxy create(array|callable $attributes = [])
 * @method static User|Proxy createOne(array $attributes = [])
 * @method static User|Proxy find(object|array|mixed $criteria)
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy first(string $sortedField = 'id')
 * @method static User|Proxy last(string $sortedField = 'id')
 * @method static User|Proxy random(array $attributes = [])
 * @method static User|Proxy randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static User[]|Proxy[] all()
 * @method static User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[] findBy(array $attributes)
 * @method static User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{


    const EMAILS = [
        'pasqui@schifo.com',
        'wizzard@pasqui.com',
        'mijail@mijail.com',
        'Ernestito@ciccione.com',
        'ForgotMyOwnName@name.com',
        'ClumsyClaws@claws.com',
        'FRRANK@THEWIZZARD.COM',
    ];
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct( private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->randomElement(self::EMAILS) . self::faker()->randomNumber(3),
            'name' => self::faker()->text(255),
            'password' => 'password',
            'phone' => self::faker()->text(255),
            'roles' => [],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
        ->afterInstantiate(function(User $user): void {
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            ));
        })
    ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
