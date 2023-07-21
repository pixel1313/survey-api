<?php

namespace App\Factory;

use App\Entity\ChoiceQuestion;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<ChoiceQuestion>
 *
 * @method        ChoiceQuestion|Proxy             create(array|callable $attributes = [])
 * @method static ChoiceQuestion|Proxy             createOne(array $attributes = [])
 * @method static ChoiceQuestion|Proxy             find(object|array|mixed $criteria)
 * @method static ChoiceQuestion|Proxy             findOrCreate(array $attributes)
 * @method static ChoiceQuestion|Proxy             first(string $sortedField = 'id')
 * @method static ChoiceQuestion|Proxy             last(string $sortedField = 'id')
 * @method static ChoiceQuestion|Proxy             random(array $attributes = [])
 * @method static ChoiceQuestion|Proxy             randomOrCreate(array $attributes = [])
 * @method static EntityRepository|RepositoryProxy repository()
 * @method static ChoiceQuestion[]|Proxy[]         all()
 * @method static ChoiceQuestion[]|Proxy[]         createMany(int $number, array|callable $attributes = [])
 * @method static ChoiceQuestion[]|Proxy[]         createSequence(iterable|callable $sequence)
 * @method static ChoiceQuestion[]|Proxy[]         findBy(array $attributes)
 * @method static ChoiceQuestion[]|Proxy[]         randomRange(int $min, int $max, array $attributes = [])
 * @method static ChoiceQuestion[]|Proxy[]         randomSet(int $number, array $attributes = [])
 */
final class ChoiceQuestionFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
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
            'choices' => self::faker()->words(5),
            'questionText' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(ChoiceQuestion $choiceQuestion): void {})
        ;
    }

    protected static function getClass(): string
    {
        return ChoiceQuestion::class;
    }
}
